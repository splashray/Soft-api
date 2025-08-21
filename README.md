# Soft Laravel Transaction API

A robust Laravel REST API for managing user balances and transactional operations with proper concurrency controls, authentication, and comprehensive testing.

## Features

-   **Secure Transaction Management**: Create credit/debit transactions with reference tracking
-   **Real-time Balance Calculation**: Get accurate user balances with concurrency protection
-   **Authentication**: Sanctum-based API authentication
-   **Concurrency Safety**: Proper locking mechanisms to prevent double-spending
-   **Comprehensive Testing**: Full test suite included
-   **Docker Support**: Containerized development environment

## Quick Start with Docker

### Prerequisites

-   Docker and Docker Compose installed
-   Git

### Setup Instructions

1. **Clone the repository**

```bash
git clone https://github.com/splashray/Soft-api.git
cd Soft-api
```

2. **Build and start services**

```bash
docker compose up -d --build
```

3. **Install dependencies and setup application**

```bash
# Install PHP dependencies
docker compose run --rm composer install

# Generate application key
docker compose exec app php artisan key:generate

# Run database migrations
docker compose exec app php artisan migrate

# Seed database (optional)
docker compose exec app php artisan db:seed
```

4. **Access the application**

-   API Base URL: `http://localhost:8080`
-   Application runs in Docker container with auto-reload

## API Documentation

### Authentication Endpoints

#### Register User

```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login User

```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

Returns authentication token for subsequent requests.

### Protected Endpoints

All endpoints below require authentication. Include the token in headers:

```
Authorization: Bearer {your-token}
```

#### Get User Balance

```http
GET /api/balance
```

**Response:**

```json
{
    "balance": "1500.00"
}
```

#### Create Transaction

```http
POST /api/transactions
Content-Type: application/json

{
    "reference": "TXN-12345",
    "type": "credit",
    "amount": 100.50,
    "meta": {
        "description": "Payment received",
        "source": "bank_transfer"
    }
}
```

**Parameters:**

-   `reference` (required): Unique transaction reference string (max 64 chars)
-   `type` (required): Either "credit" or "debit"
-   `amount` (required): Positive number for transaction amount
-   `meta` (optional): Additional metadata object

**Response:**

```json
{
    "transaction": {
        "id": 1,
        "user_id": 1,
        "reference": "TXN-12345",
        "type": "credit",
        "amount": "100.50",
        "meta": {
            "description": "Payment received",
            "source": "bank_transfer"
        },
        "created_at": "2025-01-15T10:30:00.000000Z",
        "updated_at": "2025-01-15T10:30:00.000000Z"
    }
}
```

#### Get Transaction Details

```http
GET /api/transactions/{transaction-id}
```

### Postman Collection

Import the complete API documentation and test collection:

**[📋 View Postman Collection](https://documenter.getpostman.com/view/38974030/2sB3BLi7Xv)**

## Testing

Run the comprehensive test suite:

```bash
# Run all tests
docker compose exec app php artisan test

# Run with coverage
docker compose exec app php artisan test --coverage

# Run specific test file
docker compose exec app php artisan test tests/Feature/TransactionTest.php
```

### Test Coverage

-   ✅ User authentication and authorization
-   ✅ Transaction creation and validation
-   ✅ Balance calculation accuracy
-   ✅ Concurrency and race condition handling
-   ✅ Double-spending prevention
-   ✅ Edge cases and error scenarios

## Architecture & Security

### Concurrency Control

-   **Database Locking**: Prevents race conditions during balance calculations
-   **Transaction Isolation**: Ensures ACID compliance for financial operations
-   **Idempotency**: Duplicate transaction prevention via reference validation

### Security Features

-   **Sanctum Authentication**: Secure API token management
-   **Input Validation**: Comprehensive request validation
-   **Authorization Policies**: User-based access control
-   **SQL Injection Protection**: Eloquent ORM with parameter binding

## Development

### Local Development (Non-Docker)

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### Database Schema

```sql
-- Transactions table structure
CREATE TABLE transactions (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    reference VARCHAR(64) UNIQUE,
    type ENUM('credit', 'debit'),
    amount DECIMAL(15,2),
    meta JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX(user_id),
    INDEX(reference),
    INDEX(created_at)
);
```

## Scaling Considerations

### Database Optimization

-   **Indexing**: Optimized indexes on user_id, reference, and created_at
-   **Partitioning**: Consider table partitioning for large transaction volumes
-   **Read Replicas**: Separate read/write database instances

### Caching Strategy

-   **Balance Caching**: Redis-based balance caching with proper invalidation
-   **Query Caching**: Optimize frequent balance queries
-   **Session Storage**: Redis for session management

### Infrastructure

-   **Horizontal Scaling**: Load balancer with multiple API instances
-   **Queue Processing**: Background job processing for heavy operations
-   **Monitoring**: Application performance monitoring and alerting

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Need help?** Check our [API documentation](https://documenter.getpostman.com/view/your-collection-id) or open an issue.
