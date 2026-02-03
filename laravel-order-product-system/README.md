# Laravel Order & Product Management System

A clean, SOLID-compliant Laravel application for managing products and orders.

## 🏗️ Architecture

### SOLID Principles Applied

- **Single Responsibility**: Each class has one reason to change
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Interfaces define contracts
- **Interface Segregation**: Small, specific interfaces
- **Dependency Inversion**: Depend on abstractions, not concretions

### Project Structure

```
app/
├── Contracts/
│   └── Repositories/
│       ├── ProductRepositoryInterface.php
│       └── OrderRepositoryInterface.php
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── ProductController.php
│           └── OrderController.php
├── Models/
│   ├── Product.php
│   ├── Order.php
│   └── OrderItem.php
├── Providers/
│   └── RepositoryServiceProvider.php
├── Repositories/
│   ├── ProductRepository.php
│   └── OrderRepository.php
└── Services/
    ├── ProductService.php
    └── OrderService.php
```

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

## 🔒 API Authentication

All endpoints are public in this version. Add JWT/Sanctum for production.

## 📝 License

MIT
