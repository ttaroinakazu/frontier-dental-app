Frontier Dental E-Commerce App

A robust authentication API implementation using Laravel 12 with Sanctum authentication.

## Features

- User Registration
- User Login with Token Generation
- Protected Routes with Sanctum Authentication
- User Profile Access
- Secure Logout with Token Invalidation
- Rate Limiting on Login Attempts

## API Endpoints

### Public Routes

http POST /api/register POST /api/login


### Protected Routes (Requires Authentication)

http POST /api/v1/logout GET /api/v1/user


## Authentication Flow

1. **Registration**
   - Endpoint: `POST /api/register`
   - Creates new user account
   - Returns user data and authentication token

2. **Login**
   - Endpoint: `POST /api/login`
   - Authenticates user credentials
   - Returns authentication token for API access

3. **User Profile**
   - Endpoint: `GET /api/v1/user Authorization: Bearer {token}`
   - Returns authenticated user profile data

4. **Logout**
    - Endpoint: `POST /api/v1/logout Authorization: Bearer {token}`

5. **Protected Routes**
   - Requires valid Bearer token in Authorization header
   - Format: `Authorization: Bearer {token}`


## Products API

All endpoints require authentication (`Bearer {token}` in Authorization header)

### List Products

http GET /api/v1/products Authorization: Bearer {token}


## Wishlist API

### List Wishlist Items

http GET /api/v1/wishlist Authorization: Bearer {token}


### Add to Wishlist

http POST /api/v1/wishlist Authorization: Bearer {token} Content-Type: application/json


### Remove Product from Wishlist

http DELETE /api/v1/wishlist/{product_id} Authorization: Bearer {token}
 

Note: All endpoints return JSON responses and require the `Content-Type: application/json` header. Any error responses will include appropriate HTTP status codes and error messages.





## Request & Response Examples

### Register User

http POST /api/register Content-Type: application/json
{ "name": "John Doe", "email": "john@example.com", "password": "password123", "password_confirmation": "password123" }


Response:

json { "message": "Registration successful", "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2024-03-XX" }, "token": "1|abcdef..." }


### Login

http POST /api/login Content-Type: application/json
{ "email": "john@example.com", "password": "password123" }


Response:

json { "status": "success", "message": "Successfully logged in", "data": { "user": { "id": 1, "name": "John Doe", "email": "john@example.com" }, "token": "2|xyz123..." } }


### Get User Profile

http GET /api/v1/user Authorization: Bearer {token} Content-Type: application/json


Response:

json { "data": { "id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2024-03-XX", "updated_at": "2024-03-XX" } }


Note: This endpoint requires authentication. Include the Bearer token in the Authorization header that was received during login.


### Logout

http POST /api/v1/logout Authorization: Bearer {token}


Response:

json { "status": "success", "message": "Successfully logged out" }


Note: This endpoint requires a valid authentication token in the Authorization header. Upon successful logout, the current access token will be invalidated.


## Products API

### List Products

http GET /api/v1/products Authorization: Bearer {token}


Optional Query Parameters:
- `per_page`: Number of items per page (default: 15)

Success Response:

json { "message": "List of products", "products": { "data": [ {
                "id": 1,
                "name": "Product ut",
                "price": "108.53",
                "description": "Voluptatem unde voluptatem quas. Sequi quaerat rerum facilis vel deserunt minus qui magnam. Laborum culpa quo consequatur voluptatem. Qui occaecati quas provident fugiat ad neque.",
                "created_at": "2025-07-11T01:21:27.000000Z",
                "updated_at": "2025-07-11T01:21:27.000000Z"
            } ], "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 75 } }



## Wishlist API

### List Wishlist Items

http GET /api/v1/wishlist Authorization: Bearer {token}


Optional Query Parameters:
- `per_page`: Number of items per page (default: 15)

Success Response:

json { "wishlist": { "id": 1, "name": "John's Wishlist", "user_id": 1, "created_at": "2024-03-XX", "updated_at": "2024-03-XX", "products": { "data": [ {
                "id": 1,
                "name": "Product ut",
                "price": "108.53",
                "description": "Voluptatem unde voluptatem quas. Sequi quaerat rerum facilis vel deserunt minus qui magnam. Laborum culpa quo consequatur voluptatem. Qui occaecati quas provident fugiat ad neque.",
                "created_at": "2025-07-11T01:21:27.000000Z",
                "updated_at": "2025-07-11T01:21:27.000000Z"
            } ]} }



Error Response (404):

json { "status": "error", "message": "No wishlist found" }


### Add to Wishlist

http POST /api/v1/wishlist Authorization: Bearer {token} Content-Type: application/json
{ "name": "My Custom Wishlist", // optional "products": [ { "id": 1, id: 2 }]}

 

Success Response (201):

json { "message": "Wishlist updated successfully" }


### Remove from Wishlist

http DELETE /api/v1/wishlist/{product_id} Authorization: Bearer {token}


Success Response (200):

json { "message": "Product removed from wishlist" }
 

Error Response (404):

json { "message": "Product not found in wishlist" }


Note: All endpoints require authentication via Bearer token and return JSON responses. Error responses will include appropriate HTTP status codes.


## Testing

Run the test suite using:

php artisan test


Key Test Scenarios:
- User Registration Validation
- Login Authentication
- Token Generation and Validation
- Protected Route Access
- Rate Limiting
- Token Invalidation on Logout

Example Test Command for Specific Feature:

php artisan test --filter=AuthenticationTest

## Setup and Installation

1. Clone the repository

2. Install dependencies:
   composer install
   npm install
   npm run dev

3. Configure environment:
    - Copy `.env.example` to `.env`
    - Set up database credentials

4. Set up the database:   
   php artisan migrate

5. Generate application key:
   php artisan key:generate

6. Seed the database with initial data:
   php artisan db:seed

## Requirements

- PHP >= 8.3
- Laravel 12.x
- MySQL 8.0+
- Composer
- Node.js & NPM

