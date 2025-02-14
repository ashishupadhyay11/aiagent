import pandas as pd

def main():
    # Read the CSV files
    metadata_df = pd.read_csv('mysql_metadata.csv')
    relationships_df = pd.read_csv('database_relationships.csv')
    
    # Create sets of table.column combinations from metadata
    metadata_combinations = set()
    for _, row in metadata_df.iterrows():
        metadata_combinations.add(f"{row['Table Name']}.{row['Column Name']}")
    
    # Filter relationships where both source and target exist in metadata
    filtered_relationships = []
    for _, rel in relationships_df.iterrows():
        source_combo = f"{rel['Source Table']}.{rel['Source Column']}"
        target_combo = f"{rel['Target Table']}.{rel['Target Column']}"
        
        if source_combo in metadata_combinations or target_combo in metadata_combinations:
            filtered_relationships.append(rel)
    
    # Create new DataFrame with filtered relationships
    filtered_df = pd.DataFrame(filtered_relationships)
    
    # Save to new CSV file
    output_file = 'filtered_relationships.csv'
    filtered_df.to_csv(output_file, index=False)
    print(f"Created {output_file} with {len(filtered_df)} relationships")
    print(f"Original relationships: {len(relationships_df)}")
    print(f"Filtered to relationships where at least one end has a comment")

if __name__ == "__main__":
    main() 