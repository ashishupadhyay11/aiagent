import streamlit as st
import pandas as pd
from langchain.document_loaders import CSVLoader
from langchain.vectorstores import FAISS
from langchain.embeddings.openai import OpenAIEmbeddings
from langchain.prompts import PromptTemplate
from langchain.chat_models import ChatOpenAI
from langchain.chains import LLMChain
from dotenv import load_dotenv
from langchain.schema import Document  # Import the correct Document class

load_dotenv()

# 1. Load and process CSV data
csv_path = "customer_support_tickets.csv"

# Load CSV using Pandas
df = pd.read_csv(csv_path)
df.columns = df.columns.str.strip()  # Remove extra spaces in column names

# Define correct column names
QUERY_COLUMN = "Ticket Description"
RESOLUTION_COLUMN = "Resolution"

# Check if the required columns exist
if QUERY_COLUMN not in df.columns or RESOLUTION_COLUMN not in df.columns:
    raise ValueError(f"Columns '{QUERY_COLUMN}' or '{RESOLUTION_COLUMN}' not found in the CSV file. Available columns: {df.columns}")

# Create documents for FAISS storage
documents = [
    Document(
        page_content=row[QUERY_COLUMN], 
        metadata={"resolution": row[RESOLUTION_COLUMN]}
    ) 
    for _, row in df.iterrows()
]

# Vectorize using FAISS
embeddings = OpenAIEmbeddings()
db = FAISS.from_documents(documents, embeddings)

# 2. Function for similarity search (retrieving best past resolutions)
def retrieve_info(query):
    similar_responses = db.similarity_search(query, k=3)
    resolutions = [doc.metadata["resolution"] for doc in similar_responses]  # Fetch the resolution column
    return resolutions

# 3. Setup LLMChain & Prompt
llm = ChatOpenAI(temperature=0, model="gpt-4-turbo")  # Using GPT-4 Turbo

template = """
You are a world-class customer support representative.
I will share a customer's message with you, and you will provide the best response based on past resolutions.

1/ The response should be similar to past resolutions in terms of tone, length, and style.
2/ If no past resolution is relevant, try to mimic the response style while addressing the customer's issue.

Below is a message from a customer:
{message}

Here are previous resolutions for similar issues:
{resolution}

Please generate the best response to send to the customer, end the response with "Lufa Customer Support"
"""

prompt = PromptTemplate(
    input_variables=["message", "resolution"],
    template=template
)

chain = LLMChain(llm=llm, prompt=prompt)

# 4. Retrieval augmented generation function
def generate_response(message):
    resolutions = retrieve_info(message)  # Get similar resolutions
    response = chain.run(message=message, resolution=resolutions)
    return response

# 5. Streamlit UI for the chatbot
def main():
    st.set_page_config(page_title="Lufa Customer Support", page_icon="💬")

    st.header("Lufa Customer Support")
    message = st.text_area("Enter customer query:")

    if message:
        st.write("Generating the best response...")

        result = generate_response(message)

        st.info(result)


if __name__ == '__main__':
    main()
