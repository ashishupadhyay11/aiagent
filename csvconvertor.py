import mysql.connector
import pandas as pd
from dotenv import load_dotenv
import os

def main():
    # Load environment variables
    load_dotenv()
    
    # Database connection details
    config = {
        "host": os.getenv("DB_HOST"),
        "port": os.getenv("DB_PORT"),
        "user": os.getenv("DB_USER"),
        "password": os.getenv("DB_PASSWORD"),
        "database": os.getenv("DB_NAME")
    }
    
    print("Attempting to connect to database:")
    print(f"Host: {config['host']}")
    print(f"Port: {config['port']}")
    print(f"User: {config['user']}")
    print(f"Database: {config['database']}")
    
    try:
        # Connect to the database
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor(dictionary=True)
        
        print("Successfully connected to database!")
        
        # Query to get all tables
        cursor.execute("SHOW TABLES")
        tables = [table[f'Tables_in_{config["database"]}'] for table in cursor.fetchall()]
        
        print(f"Found {len(tables)} tables in the database.")
        metadata = []
        
        for table in tables:
            print(f"Processing table: {table}")
            # Query to get column metadata for each table
            cursor.execute(f"SHOW FULL COLUMNS FROM {table}")
            columns = cursor.fetchall()
            
            for column in columns:
                # Only add rows where Comment is not empty
                if column["Comment"] and column["Comment"].strip():
                    metadata.append({
                        "Table Name": table,
                        "Column Name": column["Field"],
                        "Data Type": column["Type"],
                        "Null Allowed": column["Null"],
                        "Key": column["Key"],
                        "Default Value": column["Default"],
                        "Extra": column["Extra"],
                        "Comment": column["Comment"]
                    })
        
        # Convert to DataFrame
        df = pd.DataFrame(metadata)
        
        if df.empty:
            print("No columns with comments found in the database.")
            return
        
        # Save to CSV file
        csv_filename = "mysql_metadata.csv"
        df.to_csv(csv_filename, index=False)
        print(f"Metadata with comments successfully saved to {csv_filename}")
        print(f"Total number of columns with comments: {len(df)}")
        
    except mysql.connector.Error as err:
        print(f"Error connecting to database: {err}")
        print("Please check your .env file contains the correct database credentials.")
        
    finally:
        if 'conn' in locals() and conn and conn.is_connected():
            cursor.close()
            conn.close()
            print("Database connection closed.")

if __name__ == "__main__":
    main()
