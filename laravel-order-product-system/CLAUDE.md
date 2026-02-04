# CLAUDE.md

This file provides guidelines for Claude when working on this codebase.

## Project Overview

Laravel DDD-compliant system for managing Products and Orders.

## Key Rules

### Architecture
- **Domain Layer** = Pure business logic (Entities, ValueObjects, Events, Exceptions)
- **Application Layer** = DTOs, Service Interfaces, Services (use DTOs!)
- **Infrastructure Layer** = Repository implementations
- **Http Layer** = Controllers, Resources, Requests

### Code Patterns

**Always use DTOs:**
```php
// Controller receives DTO
$dto = CreateProductDTO::fromArray($request->validated());
$product = $this->productService->createProduct($dto);
```

**Throw Domain Exceptions (not return null):**
```php
public function getProductById(int $id): Product
{
    $product = $this->repository->findById($id);
    if (!$product) {
        throw new ProductNotFoundException($id);
    }
    return $product;
}
```

**ValueObjects are immutable:**
```php
public function add(Money $other): Money
{
    return new Money($this->amount + $other->amount);
}
```

**Controllers stay thin:**
- Receive HTTP requests
- Validate with FormRequests
- Convert to DTOs
- Call service methods
- Return Resources

### Folder Structure
```
app/
├── Domain/{Entities,ValueObjects,Events,Exceptions}
├── Application/{DTOs,Interfaces/{Repositories,Services},Services}
├── Infrastructure/Repositories
└── Http/{Controllers/Api,Resources,Requests}
```

### Naming
- Events: Past tense (`ProductCreated`)
- Exceptions: Suffix (`ProductNotFoundException`)
- DTOs: Suffix (`CreateProductDTO`)
- Resources: Suffix (`ProductResource`)

## Commands

```bash
# Code style
./vendor/bin/pint

# Tests
php vendor/bin/phpunit
```

## See Also

- `DEVELOPMENT.md` - Full guidelines
- `README.md` - Project overview
