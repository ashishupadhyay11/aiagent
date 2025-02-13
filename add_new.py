import mysql.connector
import pandas as pd
from dotenv import load_dotenv
import os
import json
import pickle
from datetime import datetime
import shutil
import sys

def connect_to_database():
    """Create database connection using environment variables"""
    print("Starting database connection...")
    load_dotenv()
    
    # Get connection parameters
    host = os.getenv("DB_HOST")
    port = os.getenv("DB_PORT")
    user = os.getenv("DB_USER")
    password = os.getenv("DB_PASSWORD")
    database = os.getenv("DB_NAME")
    
    # Validate connection parameters
    if not all([host, user, password, database]):
        raise ValueError("Missing required database connection parameters in .env file")
    
    print(f"Attempting to connect to database {database} on {host}")
    
    # Convert port to integer if present
    if port:
        try:
            port = int(port)
        except ValueError:
            raise ValueError("DB_PORT must be a valid integer")
    
    try:
        conn = mysql.connector.connect(
            host=host,
            port=port,
            user=user,
            password=password,
            database=database
        )
        print("Database connection successful!")
        return conn
    except Exception as e:
        print(f"Failed to connect to database: {str(e)}")
        raise

def get_enhanced_schema(conn):
    """Extract detailed schema information including foreign keys and default values"""
    print("  Executing schema query...")
    
    query = """
    SELECT 
        t.TABLE_NAME,
        c.COLUMN_NAME,
        c.COLUMN_TYPE,
        c.IS_NULLABLE,
        c.COLUMN_KEY,
        c.EXTRA,
        c.COLUMN_DEFAULT as Default_Value,
        k.REFERENCED_TABLE_NAME,
        k.REFERENCED_COLUMN_NAME,
        c.CHARACTER_MAXIMUM_LENGTH as Max_Length,
        c.NUMERIC_PRECISION,
        c.NUMERIC_SCALE
    FROM 
        information_schema.COLUMNS c
    LEFT JOIN 
        information_schema.TABLES t 
        ON c.TABLE_NAME = t.TABLE_NAME 
        AND c.TABLE_SCHEMA = t.TABLE_SCHEMA
    LEFT JOIN 
        information_schema.KEY_COLUMN_USAGE k
        ON c.TABLE_NAME = k.TABLE_NAME 
        AND c.COLUMN_NAME = k.COLUMN_NAME
        AND c.TABLE_SCHEMA = k.TABLE_SCHEMA
        AND k.REFERENCED_TABLE_NAME IS NOT NULL
    WHERE 
        c.TABLE_SCHEMA = %s
    ORDER BY 
        t.TABLE_NAME, 
        c.ORDINAL_POSITION;
    """
    
    cursor = conn.cursor(dictionary=True)
    try:
        print("  Executing schema query...")
        cursor.execute(query, (os.getenv("DB_NAME"),))
        results = cursor.fetchall()
        print(f"  Retrieved {len(results)} column definitions")
        
        df = pd.DataFrame(results)
        print("  Schema data converted to DataFrame")
        print(f"  Columns retrieved: {', '.join(df.columns)}")
        
        return df
    finally:
        cursor.close()
        print("  Schema query cursor closed")

def get_relationships(conn):
    """Extract relationship information including foreign key constraints"""
    print("Extracting relationship information...")
    query = """
    SELECT 
        k.TABLE_NAME as Source_Table,
        k.COLUMN_NAME as Source_Column,
        k.REFERENCED_TABLE_NAME as Target_Table,
        k.REFERENCED_COLUMN_NAME as Target_Column,
        r.UPDATE_RULE,
        r.DELETE_RULE,
        c.IS_NULLABLE as Is_Required
    FROM 
        information_schema.KEY_COLUMN_USAGE k
    JOIN 
        information_schema.REFERENTIAL_CONSTRAINTS r
        ON k.CONSTRAINT_NAME = r.CONSTRAINT_NAME
        AND k.TABLE_SCHEMA = r.CONSTRAINT_SCHEMA
    JOIN 
        information_schema.COLUMNS c
        ON k.TABLE_NAME = c.TABLE_NAME
        AND k.COLUMN_NAME = c.COLUMN_NAME
        AND k.TABLE_SCHEMA = c.TABLE_SCHEMA
    WHERE 
        k.TABLE_SCHEMA = %s
        AND k.REFERENCED_TABLE_NAME IS NOT NULL;
    """
    
    cursor = conn.cursor(dictionary=True)
    try:
        cursor.execute(query, (os.getenv("DB_NAME"),))
        results = cursor.fetchall()
        print(f"Found {len(results)} relationships")
        return pd.DataFrame(results)
    finally:
        cursor.close()

def get_sample_values(conn):
    """Extract sample values for each column more efficiently"""
    schema_df = get_enhanced_schema(conn)
    sample_values = {}
    
    cursor = conn.cursor()
    cursor.execute("SET SESSION MAX_EXECUTION_TIME=5000")  # 5 second timeout
    
    # List of MySQL reserved words that need backticks
    reserved_words = {'primary', 'order', 'group', 'key', 'update', 'default', 'like', 'between',
                     'values', 'references', 'check', 'index', 'status', 'mode', 'type', 'usage',
                     'role', 'start', 'end', 'limit', 'offset', 'size', 'position', 'count'}
    
    # Group columns by table for batch processing
    table_columns = schema_df.groupby('TABLE_NAME').agg(list).to_dict()['COLUMN_NAME']
    total_tables = len(table_columns)
    
    print(f"\nProcessing {total_tables} tables for sample values...")
    
    for idx, (table, columns) in enumerate(table_columns.items(), 1):
        print(f"Processing table {idx}/{total_tables}: {table}")
        
        try:
            # First check table size
            cursor.execute(f"SELECT TABLE_ROWS FROM information_schema.TABLES WHERE TABLE_NAME = %s", (table,))
            result = cursor.fetchone()
            row_count = result[0] if result and result[0] else 0
            
            if row_count and row_count > 1000000:  # Skip large tables
                print(f"Skipping large table {table} ({row_count:,} rows)")
                continue
                
            # Check if table is empty
            cursor.execute(f"SELECT 1 FROM `{table}` LIMIT 1")
            if not cursor.fetchone():
                print(f"Skipping empty table: {table}")
                continue
                
            table_schema = schema_df[schema_df['TABLE_NAME'] == table]
            safe_columns = []
            
            for col in columns:
                col_type = table_schema[table_schema['COLUMN_NAME'] == col]['COLUMN_TYPE'].iloc[0].lower()
                if not any(t in col_type for t in ['text', 'blob', 'password', 'key']):
                    safe_columns.append(col)
            
            if safe_columns:
                print(f"Collecting samples for {len(safe_columns)} columns in {table}")
                # Properly escape column names with backticks
                column_list = ', '.join(f"`{col}`" if col.lower() in reserved_words else col 
                                      for col in safe_columns)
                
                query = f"""
                SELECT DISTINCT {column_list}
                FROM (
                    SELECT {column_list}
                    FROM `{table}`
                    LIMIT 1000
                ) t
                LIMIT 5
                """
                
                try:
                    cursor.execute(query)
                    rows = cursor.fetchall()
                    
                    for i, col in enumerate(safe_columns):
                        values = [str(row[i]) for row in rows if row[i] is not None]
                        if values:
                            sample_values[f"{table}.{col}"] = ', '.join(values[:5])  # Limit string length
                except mysql.connector.Error as e:
                    print(f"Error getting samples for {table}: {str(e)}")
                    print(f"Failed query: {query}")  # Print the failed query for debugging
                    continue
                    
        except mysql.connector.Error as e:
            print(f"Error accessing table {table}: {str(e)}")
            continue
        except Exception as e:
            print(f"Unexpected error processing {table}: {str(e)}")
            continue
    
    cursor.close()
    print(f"\nCompleted sample collection for {len(sample_values)} columns")
    return sample_values

def match_and_validate_schema(existing_schema, new_schema_data):
    """Match and validate schema data between existing CSV and new database schema"""
    print("Validating schema changes...")
    discrepancies = []
    
    existing_schema['mapping_key'] = existing_schema.apply(
        lambda x: f"{x['Table Name'].lower().strip()}.{x['Column Name'].lower().strip()}", 
        axis=1
    )
    
    new_schema_data['mapping_key'] = new_schema_data.apply(
        lambda x: f"{x['TABLE_NAME'].lower().strip()}.{x['COLUMN_NAME'].lower().strip()}", 
        axis=1
    )
    
    existing_keys = set(existing_schema['mapping_key'])
    new_keys = set(new_schema_data['mapping_key'])
    
    missing_in_db = existing_keys - new_keys
    new_in_db = new_keys - existing_keys
    
    if missing_in_db:
        discrepancies.append(f"Columns in CSV but not in database: {missing_in_db}")
    if new_in_db:
        discrepancies.append(f"New columns in database: {new_in_db}")
    
    context_mapping = dict(zip(existing_schema['mapping_key'], existing_schema['context_for_ai']))
    
    return context_mapping, discrepancies

def generate_enhanced_csvs():
    """Update existing CSV files with additional metadata while ensuring correct mapping"""
    print("\n=== Starting Schema Enhancement Process ===")
    print("Time:", datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
    
    conn = connect_to_database()
    
    try:
        print("\n1. Loading Existing CSV Files...")
        try:
            print("- Reading database_schema_with_context.csv")
            existing_schema = pd.read_csv('database_schema_with_context.csv')
            print(f"  Found {len(existing_schema)} rows in schema")
            print(f"  Current columns: {', '.join(existing_schema.columns)}")
            
            print("\n- Reading database_relationships.csv")
            existing_relationships = pd.read_csv('database_relationships.csv')
            print(f"  Found {len(existing_relationships)} relationships")
            
            print("\n- Reading database_tables.csv")
            existing_tables = pd.read_csv('database_tables.csv')
            print(f"  Found {len(existing_tables)} tables")
            
            print("\n2. Normalizing Data...")
            existing_schema['Table Name'] = existing_schema['Table Name'].str.strip()
            existing_schema['Column Name'] = existing_schema['Column Name'].str.strip()
            existing_relationships['Source Table'] = existing_relationships['Source Table'].str.strip()
            existing_relationships['Target Table'] = existing_relationships['Target Table'].str.strip()
            existing_tables['Table Name'] = existing_tables['Table Name'].str.strip()
            print("- Data normalization complete")
            
        except FileNotFoundError as e:
            raise FileNotFoundError(f"Required CSV file not found: {e.filename}")
        
        print("\n3. Fetching New Schema Information from Database...")
        schema_df = get_enhanced_schema(conn)
        print(f"- Retrieved information for {len(schema_df)} columns")
        
        print("\n4. Fetching Relationship Information...")
        relationships_df = get_relationships(conn)
        print(f"- Retrieved {len(relationships_df)} relationships")
        
        print("\n5. Collecting Sample Values...")
        print("- This might take a few minutes depending on the database size")
        sample_values = get_sample_values(conn)
        print(f"- Collected samples for {len(sample_values)} columns")
        
        print("\n6. Checking for Schema Changes...")
        context_mapping, discrepancies = match_and_validate_schema(existing_schema, schema_df)
        print(f"- Found {len(discrepancies)} discrepancies")
        
        if discrepancies:
            print("\nSchema Discrepancies Found:")
            for disc in discrepancies:
                print(f"- {disc}")
            
            proceed = input("\nDo you want to proceed with the update? (yes/no): ")
            if proceed.lower() != 'yes':
                print("Update cancelled.")
                return
        
        print("\n7. Creating Backup Directory...")
        backup_dir = 'schema_backups'
        if not os.path.exists(backup_dir):
            os.makedirs(backup_dir)
            print(f"- Created backup directory: {backup_dir}")
        
        print("\n8. Creating Backups...")
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        for file in ['database_schema_with_context.csv', 'database_relationships.csv', 'database_tables.csv']:
            if os.path.exists(file):
                backup_path = os.path.join(backup_dir, f"{file}.backup_{timestamp}")
                shutil.copy2(file, backup_path)
                print(f"- Backed up {file} to {backup_path}")
        
        print("\n9. Preparing Updated Files...")
        print("- Preparing updated schema")
        updated_schema_df = schema_df.rename(columns={
            'TABLE_NAME': 'Table Name',
            'COLUMN_NAME': 'Column Name',
            'COLUMN_TYPE': 'Column Type',
            'IS_NULLABLE': 'Is Nullable',
            'COLUMN_KEY': 'Column Key',
            'EXTRA': 'Extra'
        })
        print(f"  New columns added: {', '.join(set(updated_schema_df.columns) - set(existing_schema.columns))}")
        
        print("- Preparing updated relationships")
        updated_relationships_df = relationships_df.rename(columns={
            'Source_Table': 'Source Table',
            'Source_Column': 'Source Column',
            'Target_Table': 'Target Table',
            'Target_Column': 'Target Column'
        })
        
        print("- Preparing updated tables information")
        table_context = dict(zip(existing_tables['Table Name'].str.lower(), existing_tables['context_for_ai']))
        tables_df = pd.DataFrame({'Table Name': schema_df['TABLE_NAME'].unique()})
        tables_df['context_for_ai'] = tables_df['Table Name'].str.lower().map(table_context)
        
        print("\n10. Saving Updated Files...")
        updated_schema_df.to_csv('database_schema_with_context.csv', index=False)
        print("- Saved updated schema CSV")
        updated_relationships_df.to_csv('database_relationships.csv', index=False)
        print("- Saved updated relationships CSV")
        tables_df.to_csv('database_tables.csv', index=False)
        print("- Saved updated tables CSV")
        
        print("\n=== Process Complete ===")
        print("Summary:")
        print(f"- Processed {len(updated_schema_df)} columns")
        print(f"- Updated {len(updated_relationships_df)} relationships")
        print(f"- Covered {len(tables_df)} tables")
        print(f"- Added sample values for {len(sample_values)} columns")
        print(f"- Created backups with timestamp: {timestamp}")
        print("\nNew metadata columns added:")
        print(", ".join(set(updated_schema_df.columns) - set(existing_schema.columns)))
        
    finally:
        conn.close()
        print("\nDatabase connection closed")

if __name__ == "__main__":
    try:
        generate_enhanced_csvs()
    except Exception as e:
        print(f"\nError occurred: {str(e)}")
        print("\nPlease check your database connection and CSV files.")