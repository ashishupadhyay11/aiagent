import streamlit as st
import openai
import faiss
import pandas as pd
import mysql.connector
import os
import json
import pickle
from dotenv import load_dotenv
from sentence_transformers import SentenceTransformer
import time
import re

# Constants
FAISS_INDEX_PATH = "faiss_index.bin"
EMBEDDINGS_PATH = "embeddings.pkl"

def load_css(file_name="styles.css"):
    """Load external CSS styles"""
    with open(file_name, "r") as f:
        css = f.read()
    st.markdown(f"<style>{css}</style>", unsafe_allow_html=True)

def initialize_app():
    """Initialize the application state and load required data"""
    # Initialize session state variables if they don't exist
    if 'initialized' not in st.session_state:
        st.session_state.initialized = False
        st.session_state.sql_query = ""
        st.session_state.sql_generated = False
        st.session_state.explanation_mode = False
        st.session_state.generated_context = ""
        st.session_state.query_history = []
        st.session_state.selected_query = None

    # Load environment variables
    load_dotenv()
    
    return {
        "host": os.getenv("DB_HOST"),
        "port": os.getenv("DB_PORT"),
        "user": os.getenv("DB_USER"),
        "password": os.getenv("DB_PASSWORD"),
        "database": os.getenv("DB_NAME")
    }

def load_database_schema():
    """Load and validate database schema from CSV files"""
    try:
        df_schema = pd.read_csv("database_schema_with_context.csv")
        df_tables = pd.read_csv("database_tables.csv")
        df_relationships = pd.read_csv("database_relationships.csv")
        
        valid_tables = df_tables["Table Name"].unique().tolist()
        table_columns = df_schema.groupby("Table Name")["Column Name"].apply(list).to_dict()
        
        return df_schema, df_tables, df_relationships, valid_tables, table_columns
    except Exception as e:
        st.error(f"Error loading database schema: {str(e)}")
        return None, None, None, None, None

def setup_faiss_index(df_schema):
    """Initialize or load FAISS index for similarity search"""
    model = SentenceTransformer("all-MiniLM-L6-v2")
    
    if os.path.exists(FAISS_INDEX_PATH) and os.path.exists(EMBEDDINGS_PATH):
        with open(EMBEDDINGS_PATH, "rb") as f:
            embeddings = pickle.load(f)
        index = faiss.read_index(FAISS_INDEX_PATH)
    else:
        embeddings = model.encode(df_schema["context_for_ai"].fillna(""))
        index = faiss.IndexFlatL2(embeddings.shape[1])
        index.add(embeddings)
        
        faiss.write_index(index, FAISS_INDEX_PATH)
        with open(EMBEDDINGS_PATH, "wb") as f:
            pickle.dump(embeddings, f)
    
    return model, index

def get_relevant_context(query, model, index, df_schema, top_k=5):
    """Get relevant context for the query using FAISS"""
    query_embedding = model.encode([query])
    _, idxs = index.search(query_embedding, top_k)
    relevant_contexts = df_schema.iloc[idxs[0]]['context_for_ai'].tolist()
    return "\n".join(filter(None, relevant_contexts))

def generate_sql_query(nl_query, context, valid_tables, table_columns, relationships):
    """Generate SQL query using OpenAI"""
    prompt = f"""Convert the following natural language query into a MySQL query using only the provided schema.
    
    Available Tables: {json.dumps(valid_tables)}
    Table Columns: {json.dumps(table_columns)}
    Table Relationships: {json.dumps(relationships)}
    
    Context: {context}
    Query: {nl_query}
    
    IMPORTANT RULES:
    1. Always qualify column names with their table names (e.g., table_name.column_name)
    2. Only use columns that exist in the provided schema
    3. Ensure proper table joins based on the relationships provided
    4. For subqueries, ensure column references are valid in both inner and outer queries
    
    Return only the SQL query without any markdown formatting."""
    
    try:
        response = openai.ChatCompletion.create(
            model="gpt-4-turbo",
            messages=[
                {"role": "system", "content": "You are an expert SQL generator. Generate only valid MySQL queries using the provided schema."},
                {"role": "user", "content": prompt}
            ]
        )
        
        sql_query = response.choices[0].message.content.strip()
        return re.sub(r'```sql|```', '', sql_query).strip()
    except Exception as e:
        st.error(f"Error generating SQL query: {str(e)}")
        return None

def validate_columns(sql_query, table_columns):
    """Validate that all column references in the query exist in the schema"""
    # Extract all potential column references
    column_pattern = r'[a-zA-Z_][a-zA-Z0-9_]*\.[a-zA-Z_][a-zA-Z0-9_]*'
    column_refs = re.findall(column_pattern, sql_query)
    
    for ref in column_refs:
        table, column = ref.split('.')
        if table not in table_columns or column not in table_columns[table]:
            return False, f"Invalid column reference: {ref}"
    
    return True, "Valid query"

def execute_query(sql_query, db_config):
    """Execute SQL query and return results"""
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        cursor.execute(sql_query)
        
        if cursor.with_rows:
            results = cursor.fetchall()
            columns = [desc[0] for desc in cursor.description]
            df_results = pd.DataFrame(results, columns=columns)
        else:
            df_results = pd.DataFrame()
            
        cursor.close()
        conn.close()
        return df_results, None
    except Exception as e:
        return None, str(e)

def main():
    st.title("🌱 Natural Language Database Query Interface")
    
    # Load CSS and initialize app
    load_css()
    db_config = initialize_app()
    
    # Initialize explanation mode in sidebar
    st.sidebar.title("Settings")
    st.session_state.explanation_mode = st.sidebar.checkbox("Show Query Details", value=False)
    
    # Load schema data only once
    if not st.session_state.initialized:
        with st.spinner("Loading database schema..."):
            df_schema, df_tables, df_relationships, valid_tables, table_columns = load_database_schema()
            if df_schema is None:
                st.error("Failed to load database schema")
                return
            
            model, index = setup_faiss_index(df_schema)
            
            # Store in session state
            st.session_state.df_schema = df_schema
            st.session_state.df_relationships = df_relationships
            st.session_state.valid_tables = valid_tables
            st.session_state.table_columns = table_columns
            st.session_state.model = model
            st.session_state.index = index
            st.session_state.initialized = True
    
    # Query history dropdown
    if st.session_state.query_history:
        selected_history = st.selectbox(
            "Previous queries:",
            [""] + st.session_state.query_history,
            key="history_select",
            help="Select a previous query"
        )
        if selected_history and selected_history != st.session_state.selected_query:
            st.session_state.selected_query = selected_history
            st.session_state.nl_query = selected_history
    
    # Main query interface
    nl_query = st.text_area(
        "Enter your question:", 
        value=st.session_state.get('nl_query', ''),
        placeholder="Example: Show me the top 10 products ordered in the last month",
        key="nl_query")
    
    if st.button("Generate Query", key="generate_btn"):
        # Add query to history if it's not empty and not already in history
        if nl_query.strip() and nl_query not in st.session_state.query_history:
            st.session_state.query_history.insert(0, nl_query)
            # Keep only the last 10 queries
            st.session_state.query_history = st.session_state.query_history[:10]
            
        with st.spinner("Generating SQL query..."):
            context = get_relevant_context(
                nl_query,
                st.session_state.model,
                st.session_state.index,
                st.session_state.df_schema
            )
            
            sql_query = generate_sql_query(
                nl_query,
                context,
                st.session_state.valid_tables,
                st.session_state.table_columns,
                st.session_state.df_relationships.to_dict('records')
            )
            
            if sql_query:
                st.session_state.sql_query = sql_query
                st.session_state.generated_context = context
                st.session_state.sql_generated = True
    
    # Show SQL query and execution interface
    if st.session_state.sql_generated:
        if st.session_state.explanation_mode:
            st.subheader("Query Context")
            st.text_area("Relevant Schema Context:", 
                        st.session_state.generated_context,
                        height=100,
                        key="context_area")
        
        st.subheader("Generated SQL Query")
        sql_query = st.text_area("SQL Query (editable):",
                                st.session_state.sql_query,
                                height=150,
                                key="sql_area")
        
        if st.button("Execute Query", key="execute_btn"):
            with st.spinner("Executing query..."):
                results, error = execute_query(sql_query, db_config)
                
                if error:
                    st.error(f"Query execution failed: {error}")
                elif results is not None:
                    st.subheader("Query Results")
                    st.dataframe(results)
                    
                    if not results.empty:
                        csv = results.to_csv(index=False)
                        st.download_button(
                            "Download Results (CSV)",
                            csv,
                            "query_results.csv",
                            "text/csv",
                            key="download_btn"
                        )

if __name__ == "__main__":
    main()