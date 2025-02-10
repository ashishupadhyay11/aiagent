import streamlit as st
import openai
import faiss
import pandas as pd
import mysql.connector
import os
import json
from dotenv import load_dotenv
from sentence_transformers import SentenceTransformer
import plotly.express as px
import time
import re

# Apply custom styles for UI
def set_page_style():
    st.markdown(
        """
        <style>
            html, body, [class*="stApp"] {
                background-color: #e8f5e9 !important;  /* Very light green background */
                color: #1b5e20 !important;  /* Dark green text */
            }
            label, h1, h2, h3, h4, h5, h6, p {
                color: #1b5e20 !important;  /* Dark green text for labels and headings */
            }
            .stButton > button {
                background-color: #1b5e20 !important;
                color: #ffffff !important;
                font-weight: 600 !important;
                border-radius: 8px !important;
                padding: 10px 20px !important;
                text-shadow: none !important;  /* Remove text shadow */
                border: none !important;  /* Remove border */
                box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
            }
            .stButton > button:hover {
                background-color: #2e7d32 !important;
                color: #ffffff !important;
                border: none !important;
            }
            .stButton > button * {
                color: #ffffff !important;
            }
            .stButton > button:active, .stButton > button:focus {
                color: #ffffff !important;
            }
            textarea, .stTextArea textarea, .stTextInput input {
                background-color: #f9fff0 !important;
                color: #1b5e20 !important;
                border: 2px solid #2e7d32 !important;
                border-radius: 5px !important;
                font-weight: bold !important;
                caret-color: black !important;
            }
        </style>
        """,
        unsafe_allow_html=True
    )

set_page_style()

with st.spinner("🌱 Initializing Lufa Farms Data Extractor... Please wait..."):
    time.sleep(3)

load_dotenv()
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")
DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "port": os.getenv("DB_PORT"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASSWORD"),
    "database": os.getenv("DB_NAME")
}

with st.spinner("🌿 Loading database schema..."):
    time.sleep(2)
    df_schema = pd.read_csv("database_schema_with_context.csv")
    df_tables = pd.read_csv("database_tables.csv")
    df_relationships = pd.read_csv("database_relationships.csv")

valid_tables = df_tables["Table Name"].unique().tolist()
table_column_mapping = df_schema.groupby("Table Name")["Column Name"].apply(list).to_dict()

with st.spinner("🌳 Initializing FAISS for optimized context matching..."):
    time.sleep(2)
    model = SentenceTransformer("all-MiniLM-L6-v2")
    embeddings = model.encode(df_schema["context_for_ai"].fillna(""))
    index = faiss.IndexFlatL2(embeddings.shape[1])
    index.add(embeddings)

success_message = st.success("✅ Initialization complete!")
time.sleep(2)
success_message.empty()

def get_relevant_context(query, top_k=5):
    query_embedding = model.encode([query])
    _, idxs = index.search(query_embedding, top_k)
    return "\n".join(df_schema.iloc[idxs[0]]['context_for_ai'].tolist())

openai.api_key = OPENAI_API_KEY

def generate_sql(nl_query):
    with st.spinner("🔍 Retrieving relevant database context..."):
        context = get_relevant_context(nl_query)
    
    prompt = f"""Convert the following natural language query into a MySQL query using only the provided schema.
    Ensure that the query only references valid tables and columns explicitly provided in the schema.
    
    Valid Tables:
    {', '.join(valid_tables)}
    
    Table Columns:
    {json.dumps(table_column_mapping, indent=2)}
    
    Query: {nl_query}
    
    Context:
    {context}
    
    SQL Query:"""
    
    with st.spinner("🤖 Asking ChatGPT to generate SQL query..."):
        response = openai.ChatCompletion.create(
            model="gpt-4-turbo",
            messages=[{"role": "system", "content": "You are an expert SQL generator. Use only valid tables and columns provided. Do not make up table or column names."},
                      {"role": "user", "content": prompt}]
        )
    sql_query = response.choices[0].message.content.strip()
    sql_query = re.sub(r'```sql|```', '', sql_query).strip()
    return context, sql_query

def execute_sql(query):
    try:
        with st.spinner("🗄️ Connecting to the database..."):
            conn = mysql.connector.connect(**DB_CONFIG)
            cursor = conn.cursor()
        
        with st.spinner("⚡ Executing SQL query..."):
            cursor.execute(query)
            if query.lower().startswith("select"):
                results = cursor.fetchall()
                columns = [desc[0] for desc in cursor.description]
                return pd.DataFrame(results, columns=columns)
            conn.commit()
            return "Query executed successfully."
    except Exception as e:
        return f"Error: {e}"
    finally:
        cursor.close()
        conn.close()

st.title("🌱 Lufa Farms Data Extractor")
nl_query = st.text_area("Enter your natural language query:")

if st.button("Generate SQL"):
    context, sql_query = generate_sql(nl_query)
    st.session_state["sql_query"] = sql_query
    st.session_state["context"] = context

if "context" in st.session_state:
    st.subheader("Relevant Context")
    st.text_area("Generated Context:", st.session_state["context"], height=150)

sql_query = st.text_area("Generated SQL Query (Editable):", st.session_state.get("sql_query", ""), height=150)

if st.button("Execute SQL Query"):
    result = execute_sql(sql_query)
    if isinstance(result, pd.DataFrame):
        st.subheader("Query Results")
        st.dataframe(result)
    else:
        st.write(result)

if "result" in locals() and isinstance(result, pd.DataFrame) and not result.empty:
    if st.button("Generate Analysis & Visualization"):
        with st.spinner("📊 Generating analysis and visualization..."):
            time.sleep(2)
            st.subheader("Basic Analysis")
            st.write(result.describe())

            chart_type = st.selectbox("Select chart type:", ["Bar", "Line", "Pie", "Scatter"])
            x_axis = st.selectbox("Select X-axis:", result.columns)
            y_axis = st.selectbox("Select Y-axis:", result.columns)

            if chart_type == "Bar":
                fig = px.bar(result, x=x_axis, y=y_axis)
            elif chart_type == "Line":
                fig = px.line(result, x=x_axis, y=y_axis)
            elif chart_type == "Pie":
                fig = px.pie(result, names=x_axis, values=y_axis)
            elif chart_type == "Scatter":
                fig = px.scatter(result, x=x_axis, y=y_axis)

            st.plotly_chart(fig)
