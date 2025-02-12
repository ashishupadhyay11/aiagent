import mysql.connector
import mgclient
import os
from dotenv import load_dotenv

load_dotenv()

# ======== CONFIGURATION ========
# MySQL connection info from environment variables:
mysql_config = {
    'user': os.getenv('DB_USER'),
    'password': os.getenv('DB_PASSWORD'),
    'host': os.getenv('DB_HOST'),  # e.g., 'localhost'
    'database': os.getenv('DB_NAME'),
}

print("MySQL Config:", mysql_config)

# ======== FUNCTIONS TO EXTRACT SCHEMA FROM MYSQL ========
def extract_mysql_schema():
    """Extracts tables, columns, and foreign keys from MySQL."""
    conn = mysql.connector.connect(**mysql_config)
    cursor = conn.cursor(dictionary=True)
    
    # Get list of tables.
    cursor.execute("""
        SELECT TABLE_NAME
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = %s AND TABLE_TYPE = 'BASE TABLE'
    """, (mysql_config['database'],))
    tables = cursor.fetchall()
    
    # Get columns.
    cursor.execute("""
        SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = %s
    """, (mysql_config['database'],))
    columns = cursor.fetchall()
    
    # Get foreign key relationships.
    cursor.execute("""
        SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = %s AND REFERENCED_TABLE_NAME IS NOT NULL
    """, (mysql_config['database'],))
    foreign_keys = cursor.fetchall()
    
    cursor.close()
    conn.close()
    
    return tables, columns, foreign_keys

# ======== FUNCTIONS TO BUILD THE MEMGRAPH GRAPH ========
def build_memgraph_graph(tables, columns, foreign_keys):
    """
    Builds a graph in Memgraph with the following structure:
      - Each Table becomes a node with property: {name: table_name}
      - Each Column becomes a node with properties: {name: column_name, qualified_name: "table.column"}
      - A COLUMNS relationship connects a Table node to each of its Column nodes.
      - A REFERENCE relationship connects a child Table node to a parent Table node for each foreign key.
        The relationship has properties:
            ref_column: The referencing column in the child table.
            parent_column: The referenced column in the parent table.
    """
    # Connect to Memgraph.
    mg_conn = mgclient.connect(host='localhost', port=7687)
    mg_cursor = mg_conn.cursor()

    # --- Create Table nodes ---
    for table in tables:
        table_name = table["TABLE_NAME"]
        query = f"MERGE (:Table {{name: '{table_name}'}})"
        mg_cursor.execute(query)
    mg_conn.commit()
    
    # --- Create Column nodes and link them to Table nodes ---
    for col in columns:
        table_name = col["TABLE_NAME"]
        column_name = col["COLUMN_NAME"]
        qualified_name = f"{table_name}.{column_name}"
        # Create the Column node.
        query = (
            f"MERGE (:Column {{"
            f"name: '{column_name}', "
            f"qualified_name: '{qualified_name}'"
            f"}})"
        )
        mg_cursor.execute(query)
        
        # Create the COLUMNS relationship from the Table to the Column.
        rel_query = (
            f"MATCH (t:Table {{name: '{table_name}'}}), "
            f"(c:Column {{qualified_name: '{qualified_name}'}}) "
            f"MERGE (t)-[:COLUMNS]->(c)"
        )
        mg_cursor.execute(rel_query)
    mg_conn.commit()
    
    # --- Create REFERENCE relationships between Table nodes ---
    # For each foreign key, we connect the child table node to the parent table node.
    # The relationship stores:
    #   - ref_column: the child table's column (foreign key column)
    #   - parent_column: the referenced column in the parent table.
    for fk in foreign_keys:
        child_table = fk["TABLE_NAME"]
        child_column = fk["COLUMN_NAME"]
        parent_table = fk["REFERENCED_TABLE_NAME"]
        parent_column = fk["REFERENCED_COLUMN_NAME"]
        
        fk_query = (
            f"MATCH (child:Table {{name: '{child_table}'}}), "
            f"(parent:Table {{name: '{parent_table}'}}) "
            f"MERGE (child)-[r:REFERENCE {{ref_column: '{child_column}', parent_column: '{parent_column}'}}]->(parent)"
        )
        mg_cursor.execute(fk_query)
    mg_conn.commit()
    
    mg_cursor.close()
    mg_conn.close()
    print("Memgraph graph creation complete.")

# ======== MAIN EXECUTION ========
def main():
    tables, columns, foreign_keys = extract_mysql_schema()
    
    # (Optional) Print out schema info for verification:
    print("Tables found:")
    for table in tables:
        print(f" - {table['TABLE_NAME']}")
    
    print("\nSample Columns:")
    for col in columns[:5]:
        print(f" - {col['TABLE_NAME']}.{col['COLUMN_NAME']} ({col['DATA_TYPE']})")
    
    print("\nForeign Keys:")
    for fk in foreign_keys:
        print(f" - {fk['TABLE_NAME']}.{fk['COLUMN_NAME']} REFERENCES {fk['REFERENCED_TABLE_NAME']}.{fk['REFERENCED_COLUMN_NAME']}")
    
    build_memgraph_graph(tables, columns, foreign_keys)

if __name__ == "__main__":
    main()
