# EstateCore API Documentation

## Base URL
`http://127.0.0.1:8000/api/v1`

## Authentication
EstateCore uses Laravel Sanctum token-based authentication.

After login, include token in every request header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```


## Roles
| Role   | Description                              |
|--------|------------------------------------------|
| admin  | Full access to all modules               |
| agent  | Manage own properties, view clients      |
| client | View properties, submit inquiries        |


## Folder Structure
```
app/
├── Helpers/
│   └── ApiResponse.php
├── Http/
│   ├── Controllers/Api/V1/
│   ├── Requests/
│   │   ├── Auth/
│   │   └── Property/
│   └── Resources/
├── Models/
└── Services/
```


## Development Pattern
Follow this pattern for every module:
```
1. Request     → validate input
2. Service     → business logic
3. Resource    → shape response
4. Controller  → handle request
5. Routes      → register endpoint
```


## Response Format
All responses follow this structure:
```json
{
    "success": true,
    "message": "Success message",
    "data": {},
    "errors": null
}
```


# 1. Auth Module

## Register Client
```
POST /auth/register
```
**Access:** Public

**Request:**
```json
{
    "name": "John Client",
    "email": "john@gmail.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "0771234567",
    "address": "123 Main St"
}
```
**Response:**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "token": "1|xxx",
        "user": {
            "id": 1,
            "name": "John Client",
            "email": "john@gmail.com",
            "phone": "0771234567",
            "address": "123 Main St",
            "role": "client",
            "created_at": "2026-06-01 10:00:00"
        }
    },
    "errors": null
}
```


## Register Agent
```
POST /auth/register-agent
```
**Access:** Admin only

**Request:**
```json
{
    "name": "Jane Agent",
    "email": "jane@gmail.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "0779876543"
}
```
**Response:**
```json
{
    "success": true,
    "message": "Agent created successfully",
    "data": {
        "user": {
            "id": 2,
            "name": "Jane Agent",
            "email": "jane@gmail.com",
            "phone": "0779876543",
            "hire_date": "2026-06-01",
            "role": "agent",
            "created_at": "2026-06-01 10:00:00"
        }
    },
    "errors": null
}
```


## Login
```
POST /auth/login
```
**Access:** Public

**Request:**
```json
{
    "email": "john@gmail.com",
    "password": "password123"
}
```
**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "1|xxx",
        "user": {
            "id": 1,
            "name": "John Client",
            "email": "john@gmail.com",
            "role": "client",
            "created_at": "2026-06-01 10:00:00"
        }
    },
    "errors": null
}
```


## Me (Current User)
```
GET /auth/me
```
**Access:** Authenticated

**Headers:**
```
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Client",
        "email": "john@gmail.com",
        "role": "client",
        "created_at": "2026-06-01 10:00:00"
    },
    "errors": null
}
```


## Logout
```
POST /auth/logout
```
**Access:** Authenticated

**Headers:**
```
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully",
    "data": null,
    "errors": null
}
```


# 2. Properties Module

## Get All Properties
```
GET /properties
```
**Access:** Authenticated
- Admin/Agent → all properties
- Client → available only

**Response:**
```json
{
    "success": true,
    "message": "Properties retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Modern Villa",
            "description": "Beautiful villa",
            "type": "sale",
            "status": "available",
            "price": "250000.00",
            "location": "Colombo 07",
            "agent": {
                "id": 2,
                "name": "Jane Agent"
            },
            "created_at": "2026-06-01 10:00:00"
        }
    ],
    "errors": null
}
```


## Get Single Property
```
GET /properties/{id}
```
**Access:** Authenticated


## Create Property
```
POST /properties
```
**Access:** Admin, Agent

**Request (agent):**
```json
{
    "title": "Modern Villa",
    "description": "Beautiful villa",
    "price": 250000,
    "location": "Colombo 07",
    "type": "sale"
}
```

**Request (admin):**
```json
{
    "title": "Modern Villa",
    "description": "Beautiful villa",
    "price": 250000,
    "location": "Colombo 07",
    "type": "sale",
    "agent_id": 2
}
```


## Update Property
```
PUT /properties/{id}
```
**Access:** Admin, Agent

**Request:**
```json
{
    "price": 300000,
    "status": "available"
}
```


## Delete Property
```
DELETE /properties/{id}
```
**Access:** Admin only


# 3. Employees Module

## Get All Employees
```
GET /employees
```
**Access:** Admin only


## Get Single Employee
```
GET /employees/{id}
```
**Access:** Admin only


## Update Employee
```
PUT /employees/{id}
```
**Access:** Admin only

**Request:**
```json
{
    "phone": "0771234567"
}
```


## Delete Employee
```
DELETE /employees/{id}
```
**Access:** Admin only

> Note: Employees are created via POST /auth/register-agent



# 4. Clients Module

## Get All Clients
```
GET /clients
```
**Access:** Admin, Agent


## Get Single Client
```
GET /clients/{id}
```
**Access:** Admin, Agent


## Update Client
```
PUT /clients/{id}
```
**Access:** Admin only

**Request:**
```json
{
    "phone": "0771234567",
    "address": "456 New St"
}
```


## Delete Client
```
DELETE /clients/{id}
```
**Access:** Admin only

> Note: Clients are created via POST /auth/register

