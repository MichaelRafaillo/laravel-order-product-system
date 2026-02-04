# Postman Setup Guide

## Step 1: Import Files

1. Open **Postman**
2. Click **Import** button
3. Import these files:
   - `postman-collection.json`
   - `postman-environment.json`

## Step 2: Setup Environment

1. Click the **Environment** dropdown (top right)
2. Select **"Laravel Order & Product API"**
3. The `baseUrl` variable is already set to `http://localhost:8000/api`

## Step 3: Start Laravel Server

```bash
cd laravel-order-product-system
php artisan serve
```

## Step 4: Run Migrations

```bash
php artisan migrate
```

## Step 5: Test the API

### 1. Create a Product
- Open **Products > Create Product**
- Click **Send**
- Copy the `id` from response

### 2. Create Another Product
- Repeat with different SKU

### 3. Create an Order
- Open **Orders > Create Order**
- Replace `product_id` values with your product IDs
- Click **Send**

### 4. Update Order Status
- Open **Orders > Update Order Status**
- Replace `1` with your order ID
- Change status: `pending` → `processing` → `completed`
- Click **Send**

## Example Responses

### Success Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "type": "product",
        "attributes": {
            "name": "Laptop Pro",
            "price": {
                "amount": 1299.99,
                "currency": "USD",
                "formatted": "1299.99 USD"
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

## Available Status Values

### Order Status
- `pending` - Order placed
- `processing` - Being prepared
- `completed` - Delivered/finished
- `cancelled` - Cancelled (restores stock)
- `refunded` - Refunded

## Troubleshooting

### 404 Not Found
- Make sure Laravel server is running
- Check the `baseUrl` environment variable

### 500 Server Error
- Run migrations: `php artisan migrate`
- Check `.env` database configuration

### Connection Refused
- Laravel server not running
- Run: `php artisan serve`
