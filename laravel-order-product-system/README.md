# Laravel Order & Product Management System

A clean, **Domain-Driven Design (DDD)** compliant Laravel application for managing products and orders.

## 🏗️ Architecture

### DDD Layers Applied

```
app/
├── Domain/                    # Core business logic
│   ├── Entities/              # Business models (Product, Order, OrderItem)
│   ├── ValueObjects/         # Money, OrderStatus, SKU
│   ├── Events/               # Domain events (ProductCreated, OrderStatusChanged, etc.)
│   └── Exceptions/            # Domain-specific exceptions
├── Application/               # Use cases & orchestration
│   ├── DTOs/                 # Data Transfer Objects
│   ├── Interfaces/            # Contracts (Repositories, Services)
│   └── Services/             # Business logic implementations
├── Infrastructure/           # Technical implementations
│   └── Repositories/         # Database operations
├── Http/                     # Presentation layer
│   ├── Controllers/          # API Controllers
│   ├── Resources/            # JSON API formatters
│   └── Requests/             # Form validation
├── Policies/                 # Authorization rules
├── Listeners/                # Event handlers
└── Providers/                 # Service providers
```

### SOLID Principles Applied

- **Single Responsibility**: Each class has one reason to change
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Interfaces define contracts
- **Interface Segregation**: Small, specific interfaces
- **Dependency Inversion**: Depend on abstractions, not concretions

## 🚀 Features

### Products CRUD
- Create, read, update, delete products
- Search by name or SKU
- Stock management
- Active/inactive status

### Orders CRUD
- Create orders with multiple items
- Automatic stock reduction
- Order status management (pending → processing → completed)
- Cancel orders (restores stock)
- Filter by status or customer

### Domain Events
- **ProductCreated** / **ProductUpdated**
- **OrderCreated**
- **OrderStatusChanged**
- **OrderCancelled**

### Error Handling
- Custom domain exceptions
- Consistent error response format
- HTTP status codes with business error codes

## 📡 API Endpoints

### Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | List all products |
| GET | `/api/products/{id}` | Get single product |
| POST | `/api/products` | Create product |
| PUT | `/api/products/{id}` | Update product |
| DELETE | `/api/products/{id}` | Delete product |
| GET | `/api/products/search?q=` | Search products |

### Orders
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | List all orders |
| GET | `/api/orders/{id}` | Get single order |
| POST | `/api/orders` | Create order |
| PUT | `/api/orders/{id}/status` | Update status |
| POST | `/api/orders/{id}/cancel` | Cancel order |
| DELETE | `/api/orders/{id}` | Delete order |
| GET | `/api/orders/status/{status}` | Filter by status |
| GET | `/api/orders/customer/{id}` | Customer orders |

## 📝 Example Responses

### Success Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "type": "product",
    "attributes": {
      "name": "Laptop",
      "price": {
        "amount": 999.99,
        "currency": "USD",
        "formatted": "999.99 USD"
      },
      "stock_quantity": 50,
      "is_active": true
    },
    "meta": {
      "created_at": "2026-02-04T10:00:00+00:00"
    }
  }
}
```

### Error Response
```json
{
  "success": false,
  "error": {
    "code": "PRODUCT_NOT_FOUND",
    "message": "Product with ID 999 not found"
  }
}
```

## 🛠️ Setup

### Requirements
- PHP 8.3+
- Composer
- MySQL/PostgreSQL/SQLite

### Installation

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

## 🧪 Testing

```bash
# Run all tests
php vendor/bin/phpunit

# Run specific test suite
php vendor/bin/phpunit tests/Unit/Domain/ValueObjects/
php vendor/bin/phpunit tests/Unit/Application/DTOs/
```

## 🔒 API Authentication

All endpoints are public in this version. Add JWT/Sanctum for production.

## 📝 License

MIT
