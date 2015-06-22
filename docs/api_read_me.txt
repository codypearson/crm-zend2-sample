CRM API:

Customer object - JSON format:

{
    id: '',
    last_name: '',
    first_name: '',
    email: '',
    street_address: '',
    city: '',
    state: '',
    postal_code: '',
    company_name: '',
    phone: ''
}

To authenticate:

POST to /crm/authenticate
Params:
    username="username"
    password="password"
Response JSON:
{
    token: 'token_string'
}

To create a new customer or update an existing customer:
Build a customer object in JSON format.
POST to /crm/customers
Params:
    token="token_string"
    customer=JSON object
Response JSON:
{
    customers: Array of customers
}

To delete a customer:
POST to /crm/customers/delete
Params:
    token="token_string"
    id="id"
Response JSON:
{
    success: true
}

To view customers:
If no params other than "token" are provided, show all customers.
GET from /crm/customers
Params:
    token="token"
    id="id"
    email="email"
    lastname="lastname"
Response JSON:
{
    customers: Array of customers
}