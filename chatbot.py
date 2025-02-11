import os
import re
import json
import faiss
import pickle
import pandas as pd
import streamlit as st
import openai
import anthropic
from dotenv import load_dotenv
from sentence_transformers import SentenceTransformer
from sqlalchemy import create_engine, text

#############################################
# Utility Functions & Initialization
#############################################

def load_css(file_name="styles.css"):
    """Load external CSS for UI styling."""
    try:
        with open(file_name, "r") as f:
            css = f.read()
        st.markdown(f"<style>{css}</style>", unsafe_allow_html=True)
    except Exception:
        st.warning("No CSS file found.")

def initialize_app():
    """Load environment variables and initialize session state."""
    load_dotenv()
    if 'initialized' not in st.session_state:
        st.session_state.initialized = False
        st.session_state.sql_query = ""
        st.session_state.sql_generated = False
        st.session_state.explanation_mode = False
        st.session_state.generated_context = ""
        st.session_state.query_history = []
        st.session_state.selected_query = None
        st.session_state.nl_query = ""
        st.session_state.retry_count = 0  # For tracking refinement iterations
        st.session_state.context_top_k = 5  # initial top_k for similarity search
    return {
        "host": os.getenv("DB_HOST"),
        "port": os.getenv("DB_PORT"),
        "user": os.getenv("DB_USER"),
        "password": os.getenv("DB_PASSWORD"),
        "database": os.getenv("DB_NAME")
    }

#############################################
# Schema Loading & Composite Schema Construction
#############################################

def load_database_schema():
    """
    Load CSV files:
      - database_schema_with_context.csv: detailed column info.
      - database_tables.csv: table-level context.
      - database_relationships.csv: relationships.
    Returns dataframes, basic mappings, and a composite schema.
    """
    try:
        df_schema = pd.read_csv("database_schema_with_context.csv")
        df_tables = pd.read_csv("database_tables.csv")
        df_relationships = pd.read_csv("database_relationships.csv")
        valid_tables = df_tables["Table Name"].unique().tolist()
        table_columns = df_schema.groupby("Table Name")["Column Name"].apply(list).to_dict()
        composite_schema = build_composite_schema(df_schema, df_tables)
        return df_schema, df_tables, df_relationships, valid_tables, table_columns, composite_schema
    except Exception as e:
        st.error(f"Error loading schema: {str(e)}")
        return None, None, None, None, None, None

def build_composite_schema(df_schema, df_tables):
    """
    For each table, build a composite dictionary including:
      - Table-level context from database_tables.csv.
      - A list of column details (type, nullability, keys, etc.) from df_schema.
    Returns a dictionary mapping table names to their composite info.
    """
    composite_schema = {}
    table_context_map = df_tables.set_index("Table Name")["context_for_ai"].to_dict()
    for table in df_schema["Table Name"].unique():
        table_context = table_context_map.get(table, "")
        cols_df = df_schema[df_schema["Table Name"] == table]
        columns_info = []
        for _, row in cols_df.iterrows():
            col_info = {
                "Column Name": row["Column Name"],
                "Column Type": row["Column Type"],
                "Is Nullable": row["Is Nullable"],
                "Column Key": row["Column Key"],
                "Extra": row["Extra"],
                "Default_Value": row["Default_Value"],
                "REFERENCED_TABLE_NAME": row["REFERENCED_TABLE_NAME"],
                "REFERENCED_COLUMN_NAME": row["REFERENCED_COLUMN_NAME"],
                "Max_Length": row["Max_Length"],
                "NUMERIC_PRECISION": row["NUMERIC_PRECISION"],
                "NUMERIC_SCALE": row["NUMERIC_SCALE"],
                "Context": row["context_for_ai"]
            }
            columns_info.append(col_info)
        composite_schema[table] = {
            "table_context": table_context,
            "columns": columns_info
        }
    return composite_schema

def get_condensed_schema(composite_schema):
    """
    Create a condensed version of the composite schema that includes for each table:
      - Its context and list of column names.
    """
    condensed = {}
    for table, info in composite_schema.items():
        condensed[table] = {
            "table_context": info.get("table_context", ""),
            "columns": [col_info["Column Name"] for col_info in info["columns"]]
        }
    return condensed

#############################################
# FAISS Index & Context Retrieval
#############################################

def setup_faiss_index(df_schema):
    """Initialize or load FAISS index using the 'context_for_ai' from df_schema."""
    FAISS_INDEX_PATH = "faiss_index.bin"
    EMBEDDINGS_PATH = "embeddings.pkl"
    model = SentenceTransformer("all-MiniLM-L6-v2")
    if os.path.exists(FAISS_INDEX_PATH) and os.path.exists(EMBEDDINGS_PATH):
        with open(EMBEDDINGS_PATH, "rb") as f:
            embeddings = pickle.load(f)
        index = faiss.read_index(FAISS_INDEX_PATH)
    else:
        texts = df_schema["context_for_ai"].fillna("").tolist()
        embeddings = model.encode(texts)
        d = embeddings.shape[1]
        index = faiss.IndexFlatL2(d)
        index.add(embeddings)
        faiss.write_index(index, FAISS_INDEX_PATH)
        with open(EMBEDDINGS_PATH, "wb") as f:
            pickle.dump(embeddings, f)
    return model, index

def get_relevant_context(query, model, index, df_schema, top_k=5):
    """Retrieve relevant context from FAISS based on the query.
       Converts every retrieved context to a string before joining.
    """
    query_embedding = model.encode([query])
    _, idxs = index.search(query_embedding, top_k)
    contexts = df_schema.iloc[idxs[0]]["context_for_ai"].tolist()
    return "\n".join(str(context) for context in contexts if context is not None)

#############################################
# SQL Generation, Validation, and Feedback
#############################################

def extract_sql(query_text):
    """
    Remove markdown code blocks, backticks, and any leading "sql" text.
    Returns a clean SQL query string.
    """
    cleaned = re.sub(r'```(?:sql)?', '', query_text, flags=re.IGNORECASE).strip()
    cleaned = re.sub(r'```', '', cleaned).strip()
    if cleaned.lower().startswith("sql"):
        cleaned = cleaned[3:].strip()
    return cleaned

def generate_sql_query(nl_query, context, composite_schema, relationships, model_choice, max_tokens=400):
    """
    Generate a SQL query using the LLM.
    Uses a condensed schema to keep the prompt size within limits.
    """
    condensed_schema = get_condensed_schema(composite_schema)
    prompt = f"""Convert the following natural language query into a valid MySQL SQL query using only the provided schema.

Available Schema (condensed):
{json.dumps(condensed_schema, indent=2)}

Relationships:
{json.dumps(relationships, indent=2)}

Context:
{context}

Query: {nl_query}

IMPORTANT RULES:
1. Always qualify column names with their table names (e.g., table_name.column_name).
2. Only use columns that exist in the provided schema.
3. Do not invent new column names.
4. Ensure proper joins based on the relationships.
5. For subqueries, ensure column references are valid.

Return ONLY the SQL query with no markdown formatting or commentary.
"""
    try:
        if model_choice == "ChatGPT Turbo":
            response = openai.ChatCompletion.create(
                model="gpt-4-turbo",
                messages=[
                    {"role": "system", "content": "You are an expert SQL generator. Return only valid MySQL queries using the provided schema."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=max_tokens
            )
            sql_query = response.choices[0].message.content.strip()
        else:
            anthro_client = anthropic.Client(api_key=os.getenv("CLAUDE_API_KEY"))
            response = anthro_client.messages.create(
                model="claude-3-5-sonnet-20241022",
                messages=[{"role": "user", "content": prompt}],
                max_tokens=max_tokens
            )
            sql_query = response.completion.strip()
        return extract_sql(sql_query)
    except Exception as e:
        st.error(f"Error generating SQL: {str(e)}")
        return None

def refine_sql_query(previous_query, error_message, context, composite_schema, relationships, model_choice, max_tokens=400):
    """
    Refine the SQL query based on error feedback.
    """
    condensed_schema = get_condensed_schema(composite_schema)
    prompt = f"""The previously generated SQL query:
{previous_query}

returned the following error: {error_message}

Using the provided schema details below:
Available Schema (condensed):
{json.dumps(condensed_schema, indent=2)}

Relationships:
{json.dumps(relationships, indent=2)}

Context:
{context}

Please refine and correct the SQL query. Return ONLY the corrected SQL query with no additional commentary.
"""
    try:
        if model_choice == "ChatGPT Turbo":
            response = openai.ChatCompletion.create(
                model="gpt-4-turbo",
                messages=[
                    {"role": "system", "content": "You are an expert SQL generator. Correct the SQL query based on the feedback provided."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=max_tokens
            )
            refined = response.choices[0].message.content.strip()
        else:
            anthro_client = anthropic.Client(api_key=os.getenv("CLAUDE_API_KEY"))
            response = anthro_client.messages.create(
                model="claude-3-5-sonnet-20241022",
                messages=[{"role": "user", "content": prompt}],
                max_tokens=max_tokens
            )
            refined = response.completion.strip()
        return extract_sql(refined)
    except Exception as e:
        st.error(f"Error refining SQL: {str(e)}")
        return None

def validate_columns(sql_query, table_columns):
    """
    Ensure that all table.column references in the SQL query exist in the schema.
    """
    pattern = r'([a-zA-Z_][a-zA-Z0-9_]*)\.([a-zA-Z_][a-zA-Z0-9_]*)'
    refs = re.findall(pattern, sql_query)
    for table, col in refs:
        if table not in table_columns or col not in table_columns[table]:
            return False, f"Invalid column reference: {table}.{col}"
    return True, "Valid query"

#############################################
# SQL Query Execution
#############################################

def execute_query(sql_query, db_config):
    """
    Execute the SQL query using SQLAlchemy.
    """
    try:
        engine = create_engine(f"mysql+pymysql://{db_config['user']}:{db_config['password']}@{db_config['host']}:{db_config['port']}/{db_config['database']}")
        with engine.connect() as conn:
            result = conn.execute(text(sql_query))
            df_results = pd.DataFrame(result.fetchall(), columns=result.keys())
        return df_results, None
    except Exception as e:
        return None, str(e)

#############################################
# Main Application Interface
#############################################

def main():
    st.title("🌱 Natural Language to SQL Query Interface with Feedback Loop")
    load_css()
    db_config = initialize_app()
    
    # Sidebar: model selection and explanation mode
    st.sidebar.title("Settings")
    st.session_state.explanation_mode = st.sidebar.checkbox("Show Query Details", value=False)
    model_choice = st.sidebar.selectbox("Select Model", ["ChatGPT Turbo", "Claude 3.5 Sonnet"])
    
    # Load schema if not already initialized
    if not st.session_state.initialized:
        with st.spinner("Loading database schema..."):
            df_schema, df_tables, df_relationships, valid_tables, table_columns, composite_schema = load_database_schema()
            if df_schema is None:
                st.error("Failed to load database schema.")
                return
            model, index = setup_faiss_index(df_schema)
            st.session_state.df_schema = df_schema
            st.session_state.df_relationships = df_relationships.to_dict('records')
            st.session_state.valid_tables = valid_tables
            st.session_state.table_columns = table_columns
            st.session_state.composite_schema = composite_schema
            st.session_state.model = model
            st.session_state.index = index
            st.session_state.initialized = True
    
    # Query history dropdown
    if st.session_state.query_history:
        selected_history = st.selectbox("Previous queries:", [""] + st.session_state.query_history, key="history_select")
        if selected_history and selected_history != st.session_state.get("nl_query", ""):
            st.session_state.nl_query = selected_history
    
    # Main natural language query input
    nl_query = st.text_area(
        "Enter your question:", 
        value=st.session_state.get("nl_query", ""),
        placeholder="e.g., Show me the top 10 products ordered in the last month",
        key="nl_query"
    )
    
    if st.button("Generate Query"):
        if nl_query.strip() and nl_query not in st.session_state.query_history:
            st.session_state.query_history.insert(0, nl_query)
            st.session_state.query_history = st.session_state.query_history[:10]
        
        with st.spinner("Generating SQL query..."):
            context = get_relevant_context(nl_query, st.session_state.model, st.session_state.index, st.session_state.df_schema, top_k=st.session_state.context_top_k)
            sql_query = generate_sql_query(
                nl_query,
                context,
                st.session_state.composite_schema,
                st.session_state.df_relationships,
                model_choice
            )
            valid, message = validate_columns(sql_query, st.session_state.table_columns)
            retry_count = 0
            max_retries = 3
            
            while not valid and retry_count < max_retries:
                st.warning(f"Validation failed: {message}. Refining the query (Attempt {retry_count+1} of {max_retries})")
                refined_query = refine_sql_query(
                    sql_query,
                    message,
                    context,
                    st.session_state.composite_schema,
                    st.session_state.df_relationships,
                    model_choice
                )
                if not refined_query:
                    break
                sql_query = refined_query
                valid, message = validate_columns(sql_query, st.session_state.table_columns)
                retry_count += 1
            
            if not valid:
                st.error(f"SQL query could not be refined after {max_retries} attempts: {message}.")
                # Optionally, re-run similarity search with increased context (top_k)
                st.warning("Refinement attempts failed. Re-running similarity search with more context...")
                st.session_state.context_top_k = 10
                context = get_relevant_context(nl_query, st.session_state.model, st.session_state.index, st.session_state.df_schema, top_k=st.session_state.context_top_k)
                sql_query = generate_sql_query(
                    nl_query,
                    context,
                    st.session_state.composite_schema,
                    st.session_state.df_relationships,
                    model_choice
                )
                valid, message = validate_columns(sql_query, st.session_state.table_columns)
                if not valid:
                    st.error(f"SQL query could not be refined: {message}")
                else:
                    st.session_state.sql_query = sql_query
                    st.session_state.generated_context = context
                    st.session_state.sql_generated = True
            else:
                st.session_state.sql_query = sql_query
                st.session_state.generated_context = context
                st.session_state.sql_generated = True
    
    if st.session_state.sql_generated:
        if st.session_state.explanation_mode:
            st.subheader("Query Context")
            st.text_area("Relevant Schema Context:", st.session_state.generated_context, height=100, key="context_area")
        st.subheader("Generated SQL Query")
        sql_query = st.text_area("SQL Query (editable):", st.session_state.sql_query, height=150, key="sql_area")
        
        if st.button("Execute Query"):
            with st.spinner("Executing query..."):
                results, error = execute_query(sql_query, db_config)
                if error:
                    st.error(f"Query execution failed: {error}")
                elif results is not None:
                    st.subheader("Query Results")
                    st.dataframe(results)
                    if not results.empty:
                        csv = results.to_csv(index=False)
                        st.download_button("Download Results (CSV)", csv, "query_results.csv", "text/csv", key="download_btn")

if __name__ == "__main__":
    main()
