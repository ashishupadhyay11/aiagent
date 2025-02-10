import csv
import anthropic
import os
import faiss
import numpy as np
import logging
from dotenv import load_dotenv
from typing import List, Dict, Set, Tuple
from time import sleep
from itertools import islice
from sentence_transformers import SentenceTransformer

# Configure logging
logging.basicConfig(
    filename='script.log',  # Logs are written to script.log
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

# Load Sentence Transformer model for embeddings
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

def generate_column_context(client: anthropic.Client, table_name: str, column_info: Dict[str, str], table_context: str) -> str:
    """Generate AI context for a database column using Claude, incorporating table-level context."""
    logging.info("Generating AI context for column: %s.%s", table_name, column_info['Column Name'])
    
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
        message = client.messages.create(
            model="claude-3-5-sonnet-20241022",
            max_tokens=150,
            temperature=0,
            messages=[{"role": "user", "content": prompt}]
        )
        content = message.content[0].text.strip() if isinstance(message.content, list) else message.content.strip()
        logging.info("Successfully generated AI context for column: %s.%s", table_name, column_info['Column Name'])
        return content
    except Exception as e:
        logging.error("Error in generate_column_context: %s", str(e))
        return f"Error generating context: {str(e)}"

def process_batch(client: anthropic.Client, batch: List[Dict], current_table: str, processed_columns: Set[str], table_contexts: Dict[str, str], index: faiss.IndexFlatL2, embeddings: np.ndarray, table_names: List[str]) -> List[Dict]:
    """Process a batch of rows and generate AI context for each column."""
    logging.info("Processing batch of %d columns", len(batch))
    processed_batch = []

    for row in batch:
        if not row['Table Name']:  # Skip empty rows
            continue

        # Check if column has already been processed
        column_id = f"{row['Table Name']}.{row['Column Name']}"
        if column_id in processed_columns:
            logging.info("Skipping already processed column: %s", column_id)
            continue

        # Retrieve closest table context using FAISS
        table_context = get_closest_table_context(row['Table Name'], table_contexts, index, embeddings, table_names)

        # Generate AI context for the column
        try:
            context = generate_column_context(client, row['Table Name'], row, table_context)
            row['context_for_ai'] = context
            processed_batch.append(row)
        except Exception as e:
            logging.error("Error processing column %s: %s", column_id, str(e))
            row['context_for_ai'] = "Error generating context"
            processed_batch.append(row)

        # Add delay to avoid API rate limits
        sleep(0.5)

    return processed_batch, current_table

def main():
    """Main function to run the script."""
    load_dotenv()
    api_key = os.getenv('CLAUDE_API_KEY')

    if not api_key:
        logging.critical("CLAUDE_API_KEY not found in environment variables!")
        raise ValueError("Please set the CLAUDE_API_KEY in your .env file")

    logging.info("Starting script to process database schema")
    try:
        process_schema_file('database_schema.csv', 'database_schema_with_context.csv', 'updated_database_tables.csv', api_key)
        logging.info("Successfully processed schema file!")
    except Exception as e:
        logging.critical("Script execution failed: %s", str(e))
def batch_iterator(iterable, batch_size):
    """Create an iterator that yields batches of the given size."""
    iterator = iter(iterable)
    return iter(lambda: list(islice(iterator, batch_size)), [])

def process_schema_file(input_file: str, output_file: str, table_context_file: str, api_key: str, batch_size: int = 100):
    """Process the database schema CSV file and add AI-generated context."""
    logging.info("Loading database schema from %s", input_file)
    client = anthropic.Client(api_key=api_key)

    # Load table-level AI context into FAISS
    table_contexts, index, embeddings, table_names = load_table_context(table_context_file)
    logging.info("Loaded context for %d tables.", len(table_contexts))

    # Read input CSV file
    with open(input_file, 'r', newline='', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        fieldnames = reader.fieldnames or []
        if 'context_for_ai' not in fieldnames:
            fieldnames.append('context_for_ai')

        rows = [{k: v for k, v in row.items() if k in fieldnames} for row in reader]

    # Process batch-wise
    total_rows = len(rows)
    logging.info("Processing %d columns in batches of %d.", total_rows, batch_size)
    current_table = ""

    for batch_number, batch in enumerate(batch_iterator(rows, batch_size), 1):
        processed_batch, current_table = process_batch(client, batch, current_table, set(), table_contexts, index, embeddings, table_names)
        with open(output_file, 'a', newline='', encoding='utf-8') as csvfile:
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            writer.writerows(processed_batch)

    logging.info("Schema processing complete. Output saved to %s", output_file)


if __name__ == "__main__":
    main()
