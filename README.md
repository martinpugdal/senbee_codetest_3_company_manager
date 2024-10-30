# senbee_codetest_3_company_manager

## Objective

Create a simple Company Manager web application using PHP for the backend, with HTML, CSS, and JavaScript for the frontend. This application will enable users to manage a list of companies by their CVR (Danish business registration number) and synchronize company details via an external API.

## Requirements

### 1. Company Management Backend

- Allow users to create, view, and delete company entries.
- Store company data using SQLite, MySQL, or another database or data storage solution of your choice.

### 2. Frontend Interface

- Implement a form for adding companies by CVR number, a list to display companies, and buttons for interacting with company records, including a “Synchronize” function to update company details from CVRAPI.dk.

### 3. Synchronization with CVRAPI

- Implement a “Synchronize” function to update all stored companies with the latest information from the CVR API (https://cvrapi.dk).

## Technical Constraints

- Use vanilla PHP - No frameworks.
- Write clean, readable code and implement basic security practices, especially SQL prepared statements to prevent injection attacks.

## Features

### 1. Frontend

- A basic webpage where users can:
  - **Add a new company** by entering its CVR number.
  - **View a list** of all companies stored in the database.
  - **Delete a company** from the database.
  - **Synchronize company data** with CVRAPI.dk to update company information.

### 2. Backend Logic

- **Create Company**: Insert a new company entry into the database using a CVR number provided by the user.
- **View Companies**: Display a list of all companies stored in the database.
- **Delete Company**: Allow deletion of specific company entries by ID.
- **Synchronize Data**: Retrieve updated information for each company in the database by making an API call to CVR API (https://cvrapi.dk) using the CVR number. For each company, update the fields: `name`, `phone`, `email`, and `address` based on the API response.

### 3. Database Specifications

- Use an SQLite or MySQL database with the following table structure:
  - **Table name**: `companies`
  - **Columns**:
    - `id` (Primary Key, Auto Increment)
    - `cvr_number` (Text, Unique, Not Null)
    - `name` (Text)
    - `phone` (Text)
    - `email` (Text)
    - `address` (Text)

A sample SQLite database is provided in `data/companies.db`, but you may store the data however you prefer.

## Sample Data

Sample CVR numbers to help in testing CRUD and synchronization functions:

| CVR Number | Company Name           |
| ---------- | ---------------------- |
| 37609110   | Mercura ApS            |
| 26616409   | ServicePoint A/S       |
| 36903341   | girafpingvin ApS       |
| 36598301   | Den Italienske Isbutik |
| 28856636   | ÅRHUS ApS              |
| 41461098   | Ost ApS                |
| 32365469   | Mosevang Mælk ApS      |

Example API call for sample data:

- To fetch data for ServicePoint A/S, use:
  `https://cvrapi.dk/api?search=26616409&country=dk`

## Getting Started

1. Clone this repository.
2. Set up your local environment or upload the code to the provide webserver.
3. Use the provided SQLite database `data/companies.db` or set up your preferred storage solution.
4. Build the application according to the requirements.

## Bonus Points

- Styling with responsive CSS.
- Using AJAX to improve user experience.
- Adding company search and filtering options.

## Submission

- Push your code to a GitHub repository and share the link.

**Time Estimate:** 4-7 hours (Candidates may take more or less time as they see fit, with a focus on quality over speed).
