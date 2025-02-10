import streamlit as st
import pandas as pd
import plotly.express as px
import plotly.graph_objects as go
import mysql.connector
from dotenv import load_dotenv
import os
from openai import OpenAI
import json
import numpy as np
import faiss
import pickle
from typing import List, Dict, Tuple
import warnings
from pathlib import Path
warnings.filterwarnings('ignore')

# Initialize session state
if 'initialized' not in st.session_state:
    st.session_state.initialized = False
    st.session_state.vectorizer = None
    st.session_state.schema_context = None

# Load environment variables
load_dotenv()

# Initialize OpenAI client
client = OpenAI(api_key=os.getenv('OPENAI_API_KEY'))

# Database configuration with port
DB_CONFIG = {
    'host': os.getenv('DB_HOST'),
    'port': int(os.getenv('DB_PORT', '3306')),
    'user': os.getenv('DB_USER'),
    'password': os.getenv('DB_PASSWORD'),
    'database': os.getenv('DB_NAME')
}

class SchemaVectorizer:
    def __init__(self, embedding_dimension: int = 1536):
        self.index = faiss.IndexFlatL2(embedding_dimension)
        self.schema_texts = []
        self.schema_details = []

    def get_embedding(self, text: str) -> np.ndarray:
        try:
            response = client.embeddings.create(
                model="text-embedding-3-small",
                input=text
            )
            return np.array(response.data[0].embedding, dtype=np.float32)
        except Exception as e:
            st.error(f"Error generating embedding: {str(e)}")
            return None

    def add_schema_item(self, text: str, details: Dict):
        embedding = self.get_embedding(text)
        if embedding is not None:
            self.index.add(embedding.reshape(1, -1))
            self.schema_texts.append(text)
            self.schema_details.append(details)

    def find_similar_contexts(self, query: str, k: int = 5) -> List[Tuple[str, Dict, float]]:
        query_embedding = self.get_embedding(query)
        if query_embedding is None:
            return []
        distances, indices = self.index.search(query_embedding.reshape(1, -1), k)
        results = []
        for idx, distance in zip(indices[0], distances[0]):
            if idx != -1:
                results.append((
                    self.schema_texts[idx],
                    self.schema_details[idx],
                    float(distance)
                ))
        return results

    def save_index(self, filepath: str):
        Path(filepath).parent.mkdir(parents=True, exist_ok=True)
        faiss.write_index(self.index, f"{filepath}.index")
        with open(f"{filepath}.pkl", 'wb') as f:
            pickle.dump((self.schema_texts, self.schema_details), f)

    def load_index(self, filepath: str) -> bool:
        try:
            self.index = faiss.read_index(f"{filepath}.index")
            with open(f"{filepath}.pkl", 'rb') as f:
                self.schema_texts, self.schema_details = pickle.load(f)
            return True
        except Exception as e:
            st.error(f"Error loading index: {str(e)}")
            return False

@st.cache_data
def load_schema_csv() -> pd.DataFrame:
    """Load the schema CSV file with caching."""
    try:
        return pd.read_csv('database_schema_with_context.csv')
    except Exception as e:
        st.error(f"Error loading schema CSV: {str(e)}")
        return pd.DataFrame()

def initialize_vectorizer(force_rebuild: bool = False) -> Tuple[Dict, SchemaVectorizer]:
    """Initialize the vectorizer with proper error handling and progress tracking."""
    schema_context = {}
    vectorizer = SchemaVectorizer()
    
    # Check for existing index
    if not force_rebuild and os.path.exists('schema_vectors.index'):
        with st.spinner("Loading existing schema vectors..."):
            if vectorizer.load_index('schema_vectors'):
                schema_df = load_schema_csv()
                # Build schema context without embeddings
                for _, row in schema_df.iterrows():
                    table_name = row['Table Name']
                    if table_name not in schema_context:
                        schema_context[table_name] = {
                            'columns': [],
                            'column_types': {},
                            'nullable': {},
                            'keys': {},
                            'context': {}
                        }
                    schema_context[table_name]['columns'].append(row['Column Name'])
                    schema_context[table_name]['column_types'][row['Column Name']] = row['Column Type']
                    schema_context[table_name]['nullable'][row['Column Name']] = row['Is Nullable']
                    schema_context[table_name]['keys'][row['Column Name']] = row['Column Key']
                    schema_context[table_name]['context'][row['Column Name']] = row['context_for_ai']
                return schema_context, vectorizer
    
    # If no existing index or force rebuild, create new one
    schema_df = load_schema_csv()
    if schema_df.empty:
        st.error("Failed to load schema CSV file.")
        return schema_context, vectorizer

    with st.spinner("Building schema vectors... This may take a few minutes..."):
        progress_bar = st.progress(0)
        total_rows = len(schema_df)
        
        for idx, row in schema_df.iterrows():
            table_name = row['Table Name']
            if table_name not in schema_context:
                schema_context[table_name] = {
                    'columns': [],
                    'column_types': {},
                    'nullable': {},
                    'keys': {},
                    'context': {}
                }
            
            # Update schema context
            schema_context[table_name]['columns'].append(row['Column Name'])
            schema_context[table_name]['column_types'][row['Column Name']] = row['Column Type']
            schema_context[table_name]['nullable'][row['Column Name']] = row['Is Nullable']
            schema_context[table_name]['keys'][row['Column Name']] = row['Column Key']
            schema_context[table_name]['context'][row['Column Name']] = row['context_for_ai']

            # Create and add vector
            context_text = f"Table: {table_name}, Column: {row['Column Name']}, Type: {row['Column Type']}, "
            context_text += f"Context: {row['context_for_ai']}" if row['context_for_ai'] else ""
            
            vectorizer.add_schema_item(context_text, {
                'table': table_name,
                'column': row['Column Name'],
                'type': row['Column Type'],
                'context': row['context_for_ai']
            })
            
            # Update progress
            progress_bar.progress((idx + 1) / total_rows)

        # Save the index
        vectorizer.save_index('schema_vectors')
        
    return schema_context, vectorizer

def generate_schema_prompt(query: str, vectorizer: SchemaVectorizer) -> str:
    """
    Generate a focused schema prompt using vector similarity search.
    Ensures consistent handling of schema metadata.
    """
    similar_contexts = vectorizer.find_similar_contexts(query)
    
    # Create a structured schema representation
    schema_info = {}
    
    # First pass: Collect all relevant tables and their columns
    for context_text, details, distance in similar_contexts:
        relevance_score = 1 / (1 + distance)
        if relevance_score > 0.3:  # Lowered threshold to capture more context
            table_name = details['table']
            if table_name not in schema_info:
                schema_info[table_name] = {
                    'columns': [],
                    'key_columns': [],
                    'contexts': {}
                }
            
            column_name = details['column']
            if column_name not in schema_info[table_name]['columns']:
                schema_info[table_name]['columns'].append(column_name)
                if details.get('is_key', False):
                    schema_info[table_name]['key_columns'].append(column_name)
                schema_info[table_name]['contexts'][column_name] = {
                    'type': details['type'],
                    'context': details['context'],
                    'relevance': relevance_score,
                    'is_key': details.get('is_key', False)
                }

    # Build the prompt with complete table information
    prompt = "Available Database Schema:\n\n"
    
    # List tables and their columns
    for table_name, info in schema_info.items():
        prompt += f"Table: {table_name}\n"
        
        # First list key columns if any
        if info['key_columns']:
            prompt += "Key Columns:\n"
            for col in info['key_columns']:
                prompt += f"- {col} (Type: {info['contexts'][col]['type']})\n"
                if info['contexts'][col]['context']:
                    prompt += f"  Purpose: {info['contexts'][col]['context']}\n"
            prompt += "\n"
        
        # Then list other columns
        prompt += "Other Columns:\n"
        for col in info['columns']:
            if col not in info['key_columns']:
                prompt += f"- {col} (Type: {info['contexts'][col]['type']})\n"
                if info['contexts'][col]['context']:
                    prompt += f"  Purpose: {info['contexts'][col]['context']}\n"
        prompt += "\n"
    
    # Add important notes
    prompt += "\nIMPORTANT NOTES:\n"
    prompt += "1. Use ONLY the tables and columns listed above\n"
    prompt += "2. Use the provided context to understand column purposes and relationships\n"
    prompt += "3. Pay attention to key columns for proper join conditions\n"
    prompt += "4. Ensure all referenced columns exist in the schema\n"
    
    return prompt

def generate_sql_query(natural_query: str, vectorizer: SchemaVectorizer) -> str:
    """
    Generate SQL query from natural language using GPT-4 with strict schema adherence.
    Uses context_for_ai to understand relationships and generate accurate queries.
    """
    schema_prompt = generate_schema_prompt(natural_query, vectorizer)
    
    system_message = """You are an expert SQL query generator with strict schema validation. Your task is to:

    1. Generate SQL queries using ONLY the tables and columns explicitly listed in the schema
    2. Use the provided context_for_ai information to understand:
       - The meaning and purpose of each column
       - Relationships between tables
       - Proper join conditions
       - Business rules and constraints
    3. Do not make assumptions about:
       - The existence of tables or columns not listed
       - Relationships not mentioned in the context
       - Data types or constraints not specified
    4. Include clear comments explaining:
       - Why specific tables were chosen
       - How relationships are being used
       - Any important context from the schema
    5. If the query cannot be generated using only the provided schema:
       - Return an error message explaining what's missing
       - Do not create a query with invalid tables or columns
    
    Respond with only the SQL query or an error message if the schema is insufficient."""

    try:
        response = client.chat.completions.create(
            model="gpt-4-turbo-preview",
            messages=[
                {"role": "system", "content": system_message},
                {"role": "user", "content": f"Schema:\n{schema_prompt}\n\nNatural Query: {natural_query}"}
            ],
            temperature=0.1
        )
        return response.choices[0].message.content.strip()
    except Exception as e:
        st.error(f"Error generating SQL query: {str(e)}")
        return None

def clean_sql_query(query: str) -> str:
    """
    Clean and validate a SQL query by removing markdown formatting, comments, and other non-SQL elements.
    Returns a clean, executable SQL query.
    
    Parameters:
        query (str): The raw query string that might contain markdown or comments
        
    Returns:
        str: A cleaned SQL query ready for execution
    """
    if not query:
        return ""
        
    # Remove markdown SQL code block indicators
    query = query.replace('```sql', '').replace('```', '')
    
    # Split into lines and process each line
    lines = query.split('\n')
    cleaned_lines = []
    
    for line in lines:
        # Remove leading/trailing whitespace
        line = line.strip()
        
        # Skip empty lines
        if not line:
            continue
            
        # Remove SQL line comments
        if line.startswith('--'):
            continue
            
        # Remove markdown dashes used for comments
        if line.startswith('—'):
            continue
            
        # For lines containing comments, keep only the SQL part
        if '--' in line:
            line = line.split('--')[0].strip()
            
        if '/*' in line and '*/' in line:
            # Remove inline block comments
            start = line.find('/*')
            end = line.find('*/') + 2
            line = line[:start] + line[end:]
            
        cleaned_lines.append(line)
    
    # Join the cleaned lines back together
    cleaned_query = ' '.join(cleaned_lines).strip()
    
    # Additional cleanup for common issues
    cleaned_query = cleaned_query.replace('\u2014', '--')  # Replace em dash with proper comment marker
    cleaned_query = cleaned_query.replace('\u2018', "'")   # Replace smart quotes
    cleaned_query = cleaned_query.replace('\u2019', "'")
    cleaned_query = cleaned_query.replace('\u201C', '"')
    cleaned_query = cleaned_query.replace('\u201D', '"')
    
    return cleaned_query

def execute_query(query: str) -> pd.DataFrame:
    """
    Execute a SQL query and return the results as a pandas DataFrame.
    Includes proper query cleaning and error handling.
    """
    try:
        # Clean the query first
        cleaned_query = clean_sql_query(query)
        
        if not cleaned_query:
            st.error("No valid SQL query found after cleaning.")
            return None
            
        # Log the cleaned query for debugging (optional)
        st.code(cleaned_query, language="sql")
        
        # Establish database connection
        conn = mysql.connector.connect(**DB_CONFIG)
        
        # Create a cursor and execute the query
        with conn.cursor(dictionary=True) as cursor:
            cursor.execute(cleaned_query)
            
            # Fetch results and convert to DataFrame
            results = cursor.fetchall()
            df = pd.DataFrame(results) if results else pd.DataFrame()
            
            # If it's an INSERT, UPDATE, or DELETE query, commit the changes
            if cleaned_query.strip().upper().startswith(('INSERT', 'UPDATE', 'DELETE')):
                conn.commit()
                
            return df
            
    except mysql.connector.Error as e:
        st.error(f"Database error: {str(e)}")
        return None
    except Exception as e:
        st.error(f"Error executing query: {str(e)}")
        return None
    finally:
        if 'conn' in locals() and conn.is_connected():
            conn.close()

def analyze_query_results(df: pd.DataFrame) -> str:
    """
    Generate a detailed analysis of query results using GPT-4.
    Includes statistical analysis and data insights.
    """
    if df is None or df.empty:
        return "No data available for analysis."

    # Prepare comprehensive data summary
    summary = {
        'row_count': len(df),
        'column_count': len(df.columns),
        'columns': list(df.columns),
        'numeric_summaries': {},
        'categorical_summaries': {},
        'null_counts': df.isnull().sum().to_dict()
    }

    # Analyze numeric columns
    numeric_cols = df.select_dtypes(include=['int64', 'float64']).columns
    for col in numeric_cols:
        summary['numeric_summaries'][col] = {
            'mean': float(df[col].mean()) if not df[col].isnull().all() else None,
            'median': float(df[col].median()) if not df[col].isnull().all() else None,
            'std': float(df[col].std()) if not df[col].isnull().all() else None,
            'min': float(df[col].min()) if not df[col].isnull().all() else None,
            'max': float(df[col].max()) if not df[col].isnull().all() else None,
            'quartiles': df[col].quantile([0.25, 0.75]).to_dict() if not df[col].isnull().all() else None
        }

    # Analyze categorical columns
    categorical_cols = df.select_dtypes(include=['object', 'category']).columns
    for col in categorical_cols:
        value_counts = df[col].value_counts().head(10).to_dict()  # Top 10 values
        summary['categorical_summaries'][col] = {
            'unique_values': df[col].nunique(),
            'top_values': value_counts,
            'mode': df[col].mode().iloc[0] if not df[col].empty else None
        }

    try:
        response = client.chat.completions.create(
            model="gpt-4-turbo-preview",
            messages=[
                {"role": "system", "content": """You are a data analyst providing insights from query results. 
                Focus on:
                1. Key statistics and their interpretation
                2. Notable patterns or trends
                3. Potential business implications
                4. Data quality observations
                5. Suggestions for further analysis
                
                Provide a clear, structured analysis in natural language paragraphs."""},
                {"role": "user", "content": f"Analyze this data summary and provide insights:\n{json.dumps(summary, indent=2)}"}
            ],
            temperature=0.7
        )
        return response.choices[0].message.content
    except Exception as e:
        return f"Error generating analysis: {str(e)}"

def create_visualizations(df: pd.DataFrame) -> List[go.Figure]:
    """
    Create appropriate visualizations based on the query results.
    Automatically determines the best visualization types based on data characteristics.
    """
    if df is None or df.empty:
        return []

    figures = []
    
    # Handle numeric data visualizations
    numeric_cols = df.select_dtypes(include=['int64', 'float64']).columns
    if len(numeric_cols) > 0:
        # Distribution plots for numeric columns
        for col in numeric_cols:
            # Create histogram with kernel density estimate
            fig = go.Figure()
            fig.add_trace(go.Histogram(
                x=df[col],
                name='Histogram',
                nbinsx=30,
                histnorm='probability density'
            ))
            
            # Add kernel density estimate
            if len(df[col].dropna()) > 1:  # Need at least 2 points for KDE
                kde_x = np.linspace(df[col].min(), df[col].max(), 100)
                kde = stats.gaussian_kde(df[col].dropna())
                fig.add_trace(go.Scatter(
                    x=kde_x,
                    y=kde(kde_x),
                    name='KDE',
                    line=dict(color='red')
                ))
            
            fig.update_layout(
                title=f'Distribution of {col}',
                xaxis_title=col,
                yaxis_title='Density',
                showlegend=True
            )
            figures.append(fig)
            
        # Correlation heatmap for multiple numeric columns
        if len(numeric_cols) > 1:
            correlation = df[numeric_cols].corr()
            fig = go.Figure(data=go.Heatmap(
                z=correlation,
                x=correlation.columns,
                y=correlation.columns,
                colorscale='RdBu',
                zmin=-1,
                zmax=1
            ))
            fig.update_layout(
                title='Correlation Heatmap',
                width=800,
                height=800
            )
            figures.append(fig)

    # Handle categorical data visualizations
    categorical_cols = df.select_dtypes(include=['object', 'category']).columns
    for col in categorical_cols:
        if df[col].nunique() <= 15:  # Only for columns with reasonable number of categories
            value_counts = df[col].value_counts()
            fig = go.Figure(data=go.Bar(
                x=value_counts.index,
                y=value_counts.values,
                text=value_counts.values,
                textposition='auto'
            ))
            fig.update_layout(
                title=f'Distribution of {col}',
                xaxis_title=col,
                yaxis_title='Count',
                showlegend=False
            )
            figures.append(fig)

    # Time series visualization if date columns are present
    date_cols = df.select_dtypes(include=['datetime64']).columns
    if len(date_cols) > 0 and len(numeric_cols) > 0:
        for date_col in date_cols:
            for numeric_col in numeric_cols:
                fig = go.Figure()
                fig.add_trace(go.Scatter(
                    x=df[date_col],
                    y=df[numeric_col],
                    mode='lines+markers',
                    name=numeric_col
                ))
                fig.update_layout(
                    title=f'{numeric_col} over Time',
                    xaxis_title=date_col,
                    yaxis_title=numeric_col
                )
                figures.append(fig)

    return figures

def main():
    st.title("Natural Language SQL Query Generator with FAISS")
    
    # Initialize app state
    if not st.session_state.initialized:
        st.info("Initializing application... Please wait.")
        try:
            schema_context, vectorizer = initialize_vectorizer()
            st.session_state.schema_context = schema_context
            st.session_state.vectorizer = vectorizer
            st.session_state.initialized = True
            st.rerun()
        except Exception as e:
            st.error(f"Error initializing application: {str(e)}")
            return
    
    # Add a force rebuild button in sidebar
    with st.sidebar:
        if st.button("Force Rebuild Vector Index"):
            st.session_state.initialized = False
            st.rerun()
    
    # Rest of the application
    if st.session_state.initialized and st.session_state.vectorizer:
        # Natural language query input
        natural_query = st.text_area(
            "Enter your query in natural language:", 
            height=100,
            placeholder="Example: Show me the total sales by product category for the last month"
        )
        
        # Generate SQL query button
        if st.button("Generate SQL Query"):
            if natural_query:
                with st.spinner("Generating SQL query using semantic search..."):
                    sql_query = generate_sql_query(natural_query, st.session_state.vectorizer)
                    if sql_query:
                        st.session_state['sql_query'] = sql_query
                        st.session_state['show_editor'] = True

        # SQL query editor
        if 'show_editor' in st.session_state and st.session_state['show_editor']:
            st.subheader("Generated SQL Query")
            sql_query = st.text_area(
                "Edit SQL Query:", 
                value=st.session_state['sql_query'],
                height=150
            )
            
            # Execute query button
            if st.button("Execute Query"):
                if sql_query:
                    results_df = execute_query(sql_query)
                    if results_df is not None:
                        st.session_state['results_df'] = results_df
                        st.subheader("Query Results")
                        st.dataframe(results_df)
                        
                        col1, col2 = st.columns(2)
                        with col1:
                            if st.button("Generate Analysis"):
                                analysis = analyze_query_results(results_df)
                                st.markdown("### Analysis")
                                st.write(analysis)
                        
                        with col2:
                            if st.button("Create Visualizations"):
                                figures = create_visualizations(results_df)
                                if figures:
                                    st.markdown("### Visualizations")
                                    for fig in figures:
                                        st.plotly_chart(fig)
                                else:
                                    st.info("No suitable visualizations could be generated for this data.")

if __name__ == "__main__":
    main()