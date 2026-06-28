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

| Role   | Description                         |
| ------ | ----------------------------------- |
| admin  | Full access to all modules          |
| agent  | Manage own properties, view clients |
| client | View properties, submit inquiries   |

## Folder Structure

```
app/
├── Helpers/
│   └── ApiResponse.php
├── Http/
│   ├── Controllers/Api/V1/
│   ├── Requests/
│   │   ├── Auth/
│   │   ├── Client/
│   │   ├── Employee/
│   │   ├── Inquiry/
│   │   ├── Lease/
│   │   ├── Property/
│   │   └── Sale/
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
    "name": "John Updated",
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

**Access:** Admin, Agent, Client (own only)

## Update Client

```
PUT /clients/{id}
```

**Access:** Admin, Client (own only)

**Request:**

```json
{
    "name": "John Updated",
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

# 5. Inquiries Module

## Get All Inquiries

```
GET /inquiries
```

**Access:**

- Admin → all inquiries
- Agent → inquiries on their properties
- Client → own inquiries only

**Response:**

```json
{
    "success": true,
    "message": "Inquiries retrieved successfully",
    "data": [
        {
            "id": 1,
            "property": {
                "id": 1,
                "title": "Modern Villa",
                "type": "sale",
                "status": "available",
                "price": "250000.00",
                "location": "Colombo 07",
                "agent": {
                    "id": 2,
                    "name": "Jane Agent",
                    "email": "agent@estatecore.com"
                }
            },
            "client": {
                "id": 3,
                "name": "John Client",
                "email": "client@estatecore.com"
            },
            "message": "I am interested in this property",
            "status": "pending",
            "created_at": "2026-06-01 10:00:00",
            "updated_at": "2026-06-01 10:00:00"
        }
    ],
    "errors": null
}
```

## Get Single Inquiry

```
GET /inquiries/{id}
```

**Access:** Admin, Agent (own properties), Client (own only)

## Create Inquiry

```
POST /inquiries
```

**Access:** Client only

> Note: client_id is auto set from logged in user. Only available properties can be inquired.

**Request:**

```json
{
    "property_id": 1,
    "message": "I am interested in this property"
}
```

## Respond to Inquiry

```
PUT /inquiries/{id}
```

**Access:** Admin, Agent only

**Request:**

```json
{
    "status": "responded"
}
```

## Delete Inquiry

```
DELETE /inquiries/{id}
```

**Access:** Admin only

> Note: Inquiries do NOT change property status.
> Only Leases and Sales change property status.
> Multiple clients can inquire on the same available property.

# 6. Leases Module

## Get All Leases

```
GET /leases
```

**Access:**

- Admin → all leases
- Agent → leases on their properties
- Client → own leases only

**Response:**

```json
{
    "success": true,
    "message": "Leases retrieved successfully",
    "data": [
        {
            "id": 1,
            "property": {
                "id": 1,
                "title": "City Apartment",
                "type": "rent",
                "status": "rented",
                "price": "25000.00",
                "location": "Colombo 03",
                "agent": {
                    "id": 2,
                    "name": "Jane Agent",
                    "email": "agent@estatecore.com"
                }
            },
            "client": {
                "id": 3,
                "name": "John Client",
                "email": "client@estatecore.com"
            },
            "start_date": "2026-07-01",
            "end_date": "2027-07-01",
            "monthly_rent": "25000.00",
            "status": "active",
            "created_at": "2026-06-01 10:00:00"
        }
    ],
    "errors": null
}
```

## Get Single Lease

```
GET /leases/{id}
```

**Access:** Admin, Agent (own properties), Client (own only)

## Create Lease

```
POST /leases
```

**Access:** Admin, Agent

> Note: Property must be type "rent" and status "available".
> Property status auto updates to "rented" on create.

**Request:**

```json
{
    "client_id": 1,
    "property_id": 1,
    "start_date": "2026-07-01",
    "end_date": "2027-07-01",
    "monthly_rent": 25000
}
```

## Update Lease Status

```
PUT /leases/{id}
```

**Access:** Admin only

**Request:**

```json
{
    "status": "expired"
}
```

> Note: When status → expired, property status reverts to "available".

## Delete Lease

```
DELETE /leases/{id}
```

**Access:** Admin only

> Note: Property status reverts to "available" on delete.

## Lease Status Flow

```
lease created  → status: active
                 property: available → rented
lease expired  → status: expired
                 property: rented → available
lease deleted  → property: rented → available
```

# 7. Sales Module

## Get All Sales

```
GET /sales
```

**Access:**

- Admin → all sales
- Agent → sales on their properties
- Client → own sales only

**Response:**

```json
{
    "success": true,
    "message": "Sales retrieved successfully",
    "data": [
        {
            "id": 1,
            "property": {
                "id": 2,
                "title": "Luxury Villa",
                "type": "sale",
                "status": "sold",
                "price": "500000.00",
                "location": "Colombo 07",
                "agent": {
                    "id": 2,
                    "name": "Jane Agent",
                    "email": "agent@estatecore.com"
                }
            },
            "client": {
                "id": 3,
                "name": "John Client",
                "email": "client@estatecore.com"
            },
            "sale_price": "490000.00",
            "sale_date": "2026-06-29",
            "created_at": "2026-06-29 10:00:00"
        }
    ],
    "errors": null
}
```

## Get Single Sale

```
GET /sales/{id}
```

**Access:** Admin, Agent (own properties), Client (own only)

## Create Sale

```
POST /sales
```

**Access:** Admin, Agent

> Note: Property must be type "sale" and status "available".
> Property status auto updates to "sold" on create.

**Request:**

```json
{
    "client_id": 1,
    "property_id": 2,
    "sale_price": 490000,
    "sale_date": "2026-06-29"
}
```

## Delete Sale

```
DELETE /sales/{id}
```

**Access:** Admin only

> Note: Property status reverts to "available" on delete.

## Sale Status Flow

```
sale created  → property: available → sold (permanent)
sale deleted  → property: sold → available
```

## Property Status Summary

```
available  → can be inquired, leased, or sold
rented     → active lease exists
sold       → sale completed
```

## Property Type Rules

```
type: rent → can only create lease (not sale)
type: sale → can only create sale (not lease)
```
