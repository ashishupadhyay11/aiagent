import csv
import openai
import os
import faiss
import numpy as np
import logging
from dotenv import load_dotenv
from typing import List, Dict, Tuple
from sentence_transformers import SentenceTransformer

# Configure logging
logging.basicConfig(
    filename='script.log',
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

# Load Sentence Transformer model for FAISS embeddings
model = SentenceTransformer('all-MiniLM-L6-v2')

def load_table_context(file_path: str) -> Tuple[Dict[str, str], faiss.IndexFlatL2, np.ndarray, List[str]]:
    """Load table-level AI-generated context from updated_database_tables.csv and index it using FAISS."""
    logging.info("Loading table context from %s", file_path)
    table_context = {}
    table_names = []
    embeddings = []

    if os.path.exists(file_path):
        with open(file_path, 'r', newline='', encoding='utf-8') as csvfile:
            reader = csv.DictReader(csvfile)
            for row in reader:
                table_name = row.get("Table Name", "").strip()
                context = row.get("context_for_ai", "").strip()
                if table_name:
                    table_context[table_name] = context
                    table_names.append(table_name)
                    embeddings.append(model.encode(context))  # Convert context to vector
    
    if embeddings:
        index = faiss.IndexFlatL2(len(embeddings[0]))  # Create FAISS index
        index.add(np.array(embeddings).astype('float32'))  # Add embeddings
        logging.info("FAISS index created with %d tables", len(table_names))
    else:
        index = None

    return table_context, index, np.array(embeddings).astype('float32') if embeddings else None, table_names

def get_closest_table_context(table_name: str, table_contexts: Dict[str, str], index: faiss.IndexFlatL2, embeddings: np.ndarray, table_names: List[str]) -> str:
    """Retrieve the closest matching table context using FAISS."""
    if table_name in table_contexts:
        logging.info("Exact match found for table: %s", table_name)
        return table_contexts[table_name]  # Exact match
    
    if index is None or embeddings is None:
        logging.warning("No FAISS index available. Returning default context.")
        return "No additional context available."

    query_vector = model.encode([table_name]).astype('float32')
    _, closest_idx = index.search(query_vector, 1)  # Find closest match
    closest_table = table_names[closest_idx[0][0]]
    logging.info("Closest match for table %s is %s", table_name, closest_table)
    return table_contexts.get(closest_table, "No additional context available.")

def generate_column_context_gpt4turbo(table_name: str, column_info: Dict[str, str], table_context: str) -> str:
    """Generate AI context for a database column using OpenAI GPT-4 Turbo."""
    logging.info("Generating AI context for column: %s.%s using GPT-4 Turbo", table_name, column_info['Column Name'])

    prompt = f"""Based on the following database column and table information, generate a clear, concise explanation (2-3 sentences) 
    that helps AI systems understand how to use this column in SQL queries.

    **Table-Level Context**: {table_context if table_context else "No additional context available."}

    **Column Details**:
    - Table Name: {table_name}
    - Column Name: {column_info['Column Name']}
    - Data Type: {column_info['Column Type']}
    - Nullable: {column_info['Is Nullable']}
    - Key Type: {column_info['Column Key']}
    - Extra Info: {column_info['Extra']}
    """

    try:
        client = openai.OpenAI()  # Initialize OpenAI client

        response = client.chat.completions.create(
            model="gpt-4-turbo",
            messages=[{"role": "user", "content": prompt}],
            temperature=0.5,
            max_tokens=150
        )

        content = response.choices[0].message.content.strip()
        logging.info("Successfully generated AI context for column: %s.%s", table_name, column_info['Column Name'])
        return content
    except Exception as e:
        logging.error("Error in generate_column_context_gpt4turbo: %s", str(e))
        return f"Error generating context: {str(e)}"

def process_remaining_columns(input_file: str, output_file: str, api_key: str, table_context_file: str):
    """Process only the columns that failed with Claude and update with GPT-4 Turbo, writing to a new output file every 50 rows."""
    openai.api_key = api_key  # Set OpenAI API Key
    logging.info("Processing remaining columns using GPT-4 Turbo")

    # Load table context into FAISS
    table_contexts, index, embeddings, table_names = load_table_context(table_context_file)

    # Read input file and process only failed rows
    batch_size = 50  # Number of rows before writing to file
    batch_buffer = []

    with open(input_file, 'r', newline='', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        fieldnames = reader.fieldnames

        # Open the output file for appending
        with open(output_file, 'w', newline='', encoding='utf-8') as csvfile_out:
            writer = csv.DictWriter(csvfile_out, fieldnames=fieldnames)
            writer.writeheader()  # Write headers only once

            for row in reader:
                column_id = f"{row['Table Name']}.{row['Column Name']}"

                # Only process rows with errors
                if "Error generating context" in row['context_for_ai']:
                    table_context = get_closest_table_context(row['Table Name'], table_contexts, index, embeddings, table_names)
                    row['context_for_ai'] = generate_column_context_gpt4turbo(row['Table Name'], row, table_context)

                batch_buffer.append(row)

                # Write to file after processing 50 rows
                if len(batch_buffer) >= batch_size:
                    writer.writerows(batch_buffer)
                    logging.info("Wrote %d rows to file %s", len(batch_buffer), output_file)
                    batch_buffer = []  # Clear buffer

            # Write any remaining rows
            if batch_buffer:
                writer.writerows(batch_buffer)
                logging.info("Wrote final %d rows to file %s", len(batch_buffer), output_file)

    logging.info("Successfully saved updated columns using GPT-4 Turbo to %s", output_file)

def main():
    """Main function to run the script."""
    load_dotenv()
    api_key = os.getenv('OPENAI_API_KEY')

    if not api_key:
        logging.critical("OPENAI_API_KEY not found in environment variables!")
        raise ValueError("Please set the OPENAI_API_KEY in your .env file")

    logging.info("Starting script to process remaining failed columns")
    
    try:
        process_remaining_columns(
            input_file='database_schema_with_context.csv',
            output_file='database_schema_with_gpt4.csv',  # NEW FILE
            api_key=api_key,
            table_context_file='updated_database_tables.csv'
        )
        logging.info("Successfully updated remaining columns with GPT-4 Turbo!")
    except Exception as e:
        logging.critical("Script execution failed: %s", str(e))

if __name__ == "__main__":
    main()
