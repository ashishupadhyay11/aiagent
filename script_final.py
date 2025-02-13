import streamlit as st
import openai
import anthropic
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
from openai import OpenAI

FAISS_INDEX_PATH = "faiss_index.bin"
EMBEDDINGS_PATH = "embeddings.pkl"

# Load external styles
def load_css(file_name="styles.css"):
    with open(file_name, "r") as f:
        css = f.read()
    st.markdown(f"<style>{css}</style>", unsafe_allow_html=True)

load_css()

# Initialize Streamlit UI
st.title("🌱 Lufa Farms Data Extractor")

with st.spinner("🌱 Initializing... Please wait..."):
    time.sleep(2)

load_dotenv()
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")
ANTHROPIC_API_KEY = os.getenv("ANTHROPIC_API_KEY")
openai_client = OpenAI(api_key=OPENAI_API_KEY)
claude_client = anthropic.Anthropic(api_key=ANTHROPIC_API_KEY)

DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "port": os.getenv("DB_PORT"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASSWORD"),
    "database": os.getenv("DB_NAME")
}

# Load database schema
df_schema = pd.read_csv("database_schema_with_context.csv")
df_tables = pd.read_csv("database_tables.csv")
df_relationships = pd.read_csv("database_relationships.csv")

valid_tables = df_tables["Table Name"].unique().tolist()
table_column_mapping = df_schema.groupby("Table Name")["Column Name"].apply(list).to_dict()

# Store previous clarifications
if "clarifications" not in st.session_state:
    st.session_state["clarifications"] = {}

if "clarification_question" not in st.session_state:
    st.session_state["clarification_question"] = None

if "awaiting_clarification" not in st.session_state:
    st.session_state["awaiting_clarification"] = False

if "sql_generated" not in st.session_state:
    st.session_state["sql_generated"] = False

if "generated_context" not in st.session_state:
    st.session_state["generated_context"] = ""

if "sql_query" not in st.session_state:
    st.session_state["sql_query"] = ""

if "explanation_mode" not in st.session_state:
    st.session_state["explanation_mode"] = False

if "selected_model" not in st.session_state:
    st.session_state["selected_model"] = "Claude"

# Sidebar Settings
st.sidebar.subheader("Settings")
st.session_state["explanation_mode"] = st.sidebar.checkbox("Enable Explanation Mode", value=False)
st.session_state["selected_model"] = st.sidebar.radio("Select AI Model", ["Claude", "GPT-4"], index=0)

# Load or create FAISS index
def load_or_create_faiss_index():
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

# Initialize FAISS
model, index = load_or_create_faiss_index()

def get_relevant_context(query, top_k=5):
    query_embedding = model.encode([query])
    _, idxs = index.search(query_embedding, top_k)
    return "\n".join(df_schema.iloc[idxs[0]]['context_for_ai'].tolist())

def get_ai_response(messages, model="Claude"):
    try:
        if model == "Claude":
            response = claude_client.messages.create(
                model="claude-3-opus-20240229",
                max_tokens=1024,
                messages=[{"role": m["role"], "content": m["content"]} for m in messages]
            )
            return response.content[0].text
        else:  # GPT-4
            response = openai_client.chat.completions.create(
                model="gpt-4-turbo-preview",
                messages=messages
            )
            return response.choices[0].message.content
    except Exception as e:
        st.error(f"Error with {model}: {str(e)}")
        return None

def ask_for_clarification(nl_query):
    """Checks if clarification is needed and stores the response."""
    if nl_query in st.session_state["clarifications"]:
        return None  # Skip asking if we already have an answer

    if not st.session_state["explanation_mode"]:
        return None  # If Explanation Mode is OFF, skip clarifications

    prompt = f"""Analyze the following natural language query and determine if more information is needed to generate an accurate SQL query.
    
    **IMPORTANT:** Before asking for clarification, check the provided schema and relationships below.  
    - **Valid Tables:** {', '.join(valid_tables)}  
    - **Table Columns:** {json.dumps(table_column_mapping, indent=2)}  
    - **Table Relationships:** {json.dumps(df_relationships.to_dict(orient="records"), indent=2)}

    If the query is **clear based on the schema**, return: "NO_CLARIFICATION_NEEDED".  
    If you **need** more details, return a **single follow-up question**.  

    Query: {nl_query}
    """

    messages = [
        {"role": "system", "content": "You are an expert database assistant. Always use the provided schema and relationships before asking for clarification. Only ask for details if absolutely necessary."},
        {"role": "user", "content": prompt}
    ]

    clarification_question = get_ai_response(messages, st.session_state["selected_model"])
    return clarification_question if clarification_question and clarification_question != "NO_CLARIFICATION_NEEDED" else None

def generate_sql(nl_query):
    with st.spinner("🔍 Retrieving relevant database context..."):
        context = get_relevant_context(nl_query)

    clarification_text = f"\nClarification provided by user: {st.session_state['clarifications'].get(nl_query, '')}"

    prompt = f"""Convert the following natural language query into a MySQL query using only the provided schema.

    📂 **Valid Tables:** {', '.join(valid_tables)}
    📊 **Table Columns:** {json.dumps(table_column_mapping, indent=2)}
    🔗 **Table Relationships:** {json.dumps(df_relationships.to_dict(orient="records"), indent=2)}

    📝 **User Query:** {nl_query}
    {clarification_text}

    🚀 **Generate a MySQL query.**
    """

    with st.spinner(f"🤖 Generating SQL query using {st.session_state['selected_model']}..."):
        messages = [
            {"role": "system", "content": "You are an expert SQL assistant. Always return only the SQL query."},
            {"role": "user", "content": prompt}
        ]
        sql_query = get_ai_response(messages, st.session_state["selected_model"])

    if sql_query:
        sql_query = re.sub(r'```sql|```', '', sql_query.strip())
        st.session_state["generated_context"] = context
        return sql_query
    return None

def execute_sql(query):
    try:
        with st.spinner("🗄️ Connecting to the database..."):
            conn = mysql.connector.connect(**DB_CONFIG)
            cursor = conn.cursor()

        with st.spinner("⚡ Executing SQL query..."):
            cursor.execute(query.strip())

            if cursor.with_rows:
                results = cursor.fetchall()
                columns = [desc[0] for desc in cursor.description]
                cursor.close()
                conn.close()
                return pd.DataFrame(results, columns=columns), None

            conn.commit()
            cursor.close()
            conn.close()
            return "Query executed successfully.", None

    except mysql.connector.Error as e:
        return None, str(e)

nl_query = st.text_area("Enter your natural language query:", placeholder="Give English names and IDs of 10 products with ID > 10000")

if st.button("Generate SQL"):
    clarification_question = ask_for_clarification(nl_query)
    if clarification_question:
        st.session_state["clarification_question"] = clarification_question
        st.session_state["awaiting_clarification"] = True
    else:
        st.session_state["sql_query"] = generate_sql(nl_query)
        st.session_state["sql_generated"] = True

if st.session_state.get("awaiting_clarification"):
    st.subheader("Clarification Needed")
    user_clarification = st.text_input(st.session_state["clarification_question"], placeholder="Type your answer here...")
    if st.button("Submit Clarification"):
        st.session_state["clarifications"][nl_query] = user_clarification
        st.session_state["awaiting_clarification"] = False
        st.session_state["sql_query"] = generate_sql(nl_query)
        st.session_state["sql_generated"] = True
        st.rerun()

if st.session_state.get("sql_generated"):
    if st.session_state["explanation_mode"]:
        st.text_area("Generated Context (Scrollable):", st.session_state["generated_context"], height=200)
    st.text_area("Generated SQL Query (Editable):", st.session_state["sql_query"], height=150)

if st.button("Execute SQL Query"):
    result, execution_error = execute_sql(st.session_state.get("sql_query", ""))
    if execution_error is None:
        st.subheader("Query Results")
        st.dataframe(result)
    else:
        st.error(f"Execution failed: {execution_error}")