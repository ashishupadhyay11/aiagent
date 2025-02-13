import pandas as pd

def map_context():
    # Read both files
    print("Reading files...")
    current_file = 'database_schema_with_context.csv'
    backup_file = 'database_schema_with_context.csv.backup_20250210_181151'
    
    current_df = pd.read_csv(current_file)
    backup_df = pd.read_csv(backup_file)
    
    print(f"\nFile Statistics:")
    print(f"Current file rows: {len(current_df)}")
    print(f"Backup file rows: {len(backup_df)}")
    
    # Create mapping keys
    backup_df['mapping_key'] = backup_df.apply(
        lambda x: f"{x['Table Name'].lower().strip()}.{x['Column Name'].lower().strip()}", 
        axis=1
    )
    
    current_df['mapping_key'] = current_df.apply(
        lambda x: f"{x['Table Name'].lower().strip()}.{x['Column Name'].lower().strip()}", 
        axis=1
    )
    
    # Find new and missing columns
    current_keys = set(current_df['mapping_key'])
    backup_keys = set(backup_df['mapping_key'])
    
    new_columns = current_keys - backup_keys
    
    print(f"\nNew Columns ({len(new_columns)}):")
    for key in sorted(new_columns):
        table, column = key.split('.')
        print(f"- {table}: {column}")
    
    # Create context mapping from backup
    context_mapping = dict(zip(backup_df['mapping_key'], backup_df['context_for_ai']))
    
    # Map context
    current_df['context_for_ai'] = current_df['mapping_key'].map(context_mapping)
    
    # Find columns without context
    missing_context = current_df[current_df['context_for_ai'].isna()]
    
    print(f"\nColumns Missing Context ({len(missing_context)}):")
    for _, row in missing_context.sort_values(['Table Name', 'Column Name']).iterrows():
        print(f"- {row['Table Name']}: {row['Column Name']}")
    
    # Remove mapping key
    current_df = current_df.drop('mapping_key', axis=1)
    
    # Save updated file
    current_df.to_csv(current_file, index=False)
    
    print("\nSummary:")
    print(f"Total columns: {len(current_df)}")
    print(f"Columns with context: {len(current_df[current_df['context_for_ai'].notna()])}")
    print(f"New columns added: {len(new_columns)}")
    print(f"Columns missing context: {len(missing_context)}")

if __name__ == "__main__":
    map_context()