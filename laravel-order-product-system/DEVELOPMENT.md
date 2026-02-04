# Development Guidelines

This document outlines the rules and conventions for the Laravel Order & Product System.

---

## 🏗️ DDD Architecture Rules

### Folder Structure
```
app/
├── Domain/           # Business logic (never changes)
│   ├── Entities/
│   ├── ValueObjects/  # Type-safe values (Money, SKU, OrderStatus)
│   ├── Events/        # Business events (immutable)
│   └── Exceptions/    # Domain-specific errors
├── Application/       # Use cases (orchestration)
│   ├── DTOs/          # Data transfer (no business logic)
│   ├── Interfaces/    # Contracts (what, not how)
│   └── Services/      # Business operations
├── Infrastructure/    # Technical details (can change)
│   └── Repositories/  # Database operations
└── Http/              # Presentation layer
    ├── Controllers/  # HTTP handling only
    ├── Resources/     # Response formatting
    └── Requests/      # Input validation
```

### Rules
1. **Domain Layer** - No dependencies on other layers
2. **Application Layer** - Depends only on Domain
3. **Infrastructure Layer** - Implements Application interfaces
4. **Http Layer** - Depends on Application

---

## 📝 Naming Conventions

### Classes
| Layer | Suffix | Example |
|-------|--------|---------|
| Entity | None | `Product`, `Order` |
| ValueObject | None | `Money`, `SKU` |
| Event | Past tense | `ProductCreated`, `OrderShipped` |
| Exception | Exception | `ProductNotFoundException` |
| DTO | DTO | `CreateProductDTO` |
| Interface | Interface | `ProductRepositoryInterface` |
| Service | None | `ProductService` |
| Controller | Controller | `ProductController` |
| Resource | Resource | `ProductResource` |
| Listener | Listener | `LogProductActivity` |
| Policy | Policy | `ProductPolicy` |

### Methods
- **Services**: Verb-based (`createProduct`, `updateOrderStatus`)
- **ValueObjects**: Fluent (`add()`, `multiply()`)
- **Repositories**: CRUD-based (`findById`, `search`)

### Variables
- Use **camelCase**: `$productName`, `$orderItems`
- Value Objects: `$money`, `$sku` (not `$moneyValue`)
- Collections: `$products`, `$orders` (plural or suffix `Collection`)

---

## 🔧 Code Style Rules

### PHP
```php
// ✅ Correct
class ProductService implements ProductServiceInterface
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function createProduct(CreateProductDTO $dto): Product
    {
        // ...
    }
}

// ❌ Wrong
class product_service {
    function createProduct($data) {
        // ...
    }
}
```

### Controllers (Keep Thin!)
```php
// ✅ Correct - Delegate to service
public function store(StoreProductRequest $request): JsonResponse
{
    $dto = CreateProductDTO::fromArray($request->validated());
    $product = $this->productService->createProduct($dto);

    return response()->json([
        'success' => true,
        'data' => new ProductResource($product)
    ], 201);
}

// ❌ Wrong - Business logic in controller
public function store(Request $request)
{
    $product = new Product();
    $product->name = $request->name;
    $product->price = $request->price * 1.1; // Business logic!
    $product->save();
}
```

### Value Objects (Immutable!)
```php
// ✅ Correct - Returns new instance
public function add(Money $other): Money
{
    return new Money($this->amount + $other->amount);
}

// ❌ Wrong - Mutates state
public function add(Money $other): void
{
    $this->amount += $other->amount;
}
```

---

## 🎯 DDD Patterns

### Always Use DTOs at Boundaries
```php
// Controller receives DTO
public function store(CreateProductRequest $request): JsonResponse
{
    $dto = CreateProductDTO::fromArray($request->validated());
    $product = $this->productService->createProduct($dto);
}
```

### Throw Domain Exceptions
```php
// ✅ Correct
public function getProductById(int $id): Product
{
    $product = $this->repository->findById($id);
    
    if (!$product) {
        throw new ProductNotFoundException($id);
    }
    
    return $product;
}

// ❌ Wrong - Returns null, forces null checks
public function getProductById(int $id): ?Product
{
    return $this->repository->findById($id);
}
```

### Use Domain Events
```php
// When something important happens
Event::dispatch(new OrderCreated($order));

// Elsewhere - react to event
class SendOrderConfirmation
{
    public function handle(OrderCreated $event): void
    {
        // Send email, notification, etc.
    }
}
```

---

## 🧪 Testing Rules

### Unit Tests Required For
- Value Objects (100% coverage)
- DTOs (100% coverage)
- Service logic (critical paths)

### Test Naming
```php
class MoneyTest extends TestCase
{
    public function test_can_add_money(): void { }
    public function test_cannot_create_negative_money(): void { }
}
```

---

## 📝 Git Commit Messages

### Format
```
type(scope): subject

body (optional)

footer (optional)
```

### Types
- `feat` - New feature
- `fix` - Bug fix
- `docs` - Documentation
- `refactor` - Code restructuring
- `test` - Tests
- `chore` - Maintenance

### Examples
```
feat(domain): add Money value object

- Add currency validation
- Implement add() and multiply() methods
- Add formatted() for display

closes #123
```

```
fix(services): handle insufficient stock in OrderService

- Throw InsufficientStockException
- Return proper error response
```

---

## 🔄 Git Workflow

### Branch Naming
```
feature/[ticket-number]-short-description
bugfix/[ticket-number]-short-description
hotfix/[ticket-number]-short-description
```

### Process
1. Create feature branch
2. Make changes following these rules
3. Write/update tests
4. Run linting: `./vendor/bin/pint`
5. Run tests: `php vendor/bin/phpunit`
6. Commit with proper message
7. Push and create PR

---

## ⚙️ Commands

### Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Development
```bash
# Code style
./vendor/bin/pint

# Run tests
php vendor/bin/phpunit

# Run specific test
php vendor/bin/phpunit tests/Unit/Domain/ValueObjects/
```

---

## 📚 References

- [Laravel Documentation](https://laravel.com/docs)
- [PHP DDD](https://domain-driven-design.org/)
- [Laravel Pint](https://github.com/laravel/pint)

---

**Following these rules ensures maintainable, scalable code!**
