import os
import faiss
import pandas as pd
import numpy as np
from anthropic import Anthropic
from dotenv import load_dotenv
from sklearn.feature_extraction.text import TfidfVectorizer
import time

# Load API key from .env file
load_dotenv()
CLAUDE_API_KEY = os.getenv("CLAUDE_API_KEY")

# Initialize Anthropic Client
client = Anthropic(api_key=CLAUDE_API_KEY)

print("🔄 Loading CSV files...")

# Load CSV files
schema_df = pd.read_csv("database_tables.csv")
relationships_df = pd.read_csv("database_relationships.csv")

# Ensure column names are properly stripped
schema_df.columns = schema_df.columns.str.strip()
relationships_df.columns = relationships_df.columns.str.strip()

# Remove 'Database' column from schema_df if present
if "Database" in schema_df.columns:
    schema_df = schema_df.drop(columns=["Database"])

print(f"✅ Loaded {len(schema_df)} tables and {len(relationships_df)} relationships.")

# Dictionary to store context for each table
table_contexts = {}

# Generate relationship-based context
print("🔄 Generating relationship context...")
for _, row in relationships_df.iterrows():
    src_table, src_col, tgt_table, tgt_col = row["Source Table"], row["Source Column"], row["Target Table"], row["Target Column"]
    
    table_contexts.setdefault(src_table, []).append(f"Has relationship with {tgt_table} via {src_col} → {tgt_col}.")
    table_contexts.setdefault(tgt_table, []).append(f"Has relationship with {src_table} via {tgt_col} → {src_col}.")

# Convert relationships into text format
for table, context in table_contexts.items():
    table_contexts[table] = " ".join(context)

print("✅ Relationship context generated.")

# TF-IDF Vectorizer for embeddings
print("🔄 Creating vector embeddings...")
vectorizer = TfidfVectorizer()
texts = list(table_contexts.values()) if table_contexts else [""]
vectorizer.fit(texts)
embeddings = vectorizer.transform(texts).toarray()

# Create FAISS index
d = embeddings.shape[1]
index = faiss.IndexFlatL2(d)
index.add(np.array(embeddings, dtype=np.float32))

print("✅ FAISS index created.")

# Function to generate AI-driven context for a single table
def generate_context_for_table(table_name, retries=3):
    print(f"🔄 Generating AI context for table: {table_name}...")

    relationships = table_contexts.get(table_name, "No relationships found.")

    prompt = f"""
    You are an AI assistant generating metadata for a database table.

    **Table Name:** {table_name}
    **Relationships:** {relationships}

    **Instructions:**
    - Provide a **short and clear** description of this table.
    - Include its **purpose and how it connects to other tables**.
    - Keep it **under 3 sentences**.

    **Example Response:**
    "This table stores user information including names, emails, and registration dates. It links to orders via the user_id column."
    """

    for attempt in range(retries):
        try:
            response = client.messages.create(
                model="claude-3-5-sonnet-20241022",
                max_tokens=150,
                temperature=0.1,  # ✅ Keeping temperature low for consistency
                messages=[{"role": "user", "content": prompt}]
            )

            # Extract the correct response format
            if isinstance(response.content, list):  # If Claude returns a list
                return response.content[0].text.strip() if hasattr(response.content[0], "text") else "Context generation failed."

            elif isinstance(response.content, str):  # If Claude returns a single string
                return response.content.strip()

        except Exception as e:
            print(f"❌ Error generating context (Attempt {attempt + 1}): {str(e)}")
        
        time.sleep(2)  # Wait before retrying

    print(f"❌ Final attempt failed. Using fallback.")
    return "Context generation failed."

# Process tables one at a time and write to CSV immediately
output_file = "updated_database_tables.csv"

# Save headers only at the start
if not os.path.exists(output_file):
    schema_df.iloc[:0].assign(context_for_ai="").to_csv(output_file, index=False)

print("🔄 Processing tables and writing to CSV one by one...")

for i, table_name in enumerate(schema_df["Table Name"]):
    print(f"🚀 Processing table {i + 1} / {len(schema_df)}: {table_name}")
    context = generate_context_for_table(table_name)

    # Create single-row dataframe and append to CSV
    row_df = pd.DataFrame({"Table Name": [table_name], "context_for_ai": [context]})
    row_df.to_csv(output_file, mode='a', header=False, index=False)

    print(f"✅ Table {i + 1} saved to CSV.")

print("✅ All tables processed and written to CSV.")
print(f"🎉 Process completed successfully! Data saved in '{output_file}'.")
