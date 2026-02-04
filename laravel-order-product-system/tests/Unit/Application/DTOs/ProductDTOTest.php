<?php

namespace Tests\Unit\Application\DTOs;

use App\Application\DTOs\CreateProductDTO;
use App\Application\DTOs\UpdateProductDTO;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\SKU;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    public function test_create_product_dto_from_array(): void
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'A test product',
            'price' => 99.99,
            'stock_quantity' => 100,
            'sku' => 'TEST-001',
            'is_active' => true,
        ];

        $dto = CreateProductDTO::fromArray($data);

        $this->assertEquals('Test Product', $dto->name);
        $this->assertEquals('A test product', $dto->description);
        $this->assertEquals(99.99, $dto->price->amount());
        $this->assertEquals(100, $dto->stockQuantity);
        $this->assertEquals('TEST-001', $dto->sku->value());
        $this->assertTrue($dto->isActive);
    }

    public function test_create_product_dto_to_array(): void
    {
        $dto = new CreateProductDTO(
            name: 'Test Product',
            description: 'A test product',
            price: new Money(99.99),
            stockQuantity: 100,
            sku: new SKU('TEST-001'),
            isActive: true
        );

        $array = $dto->toArray();

        $this->assertEquals('Test Product', $array['name']);
        $this->assertEquals(99.99, $array['price']);
        $this->assertEquals(100, $array['stock_quantity']);
        $this->assertEquals('TEST-001', $array['sku']);
    }

    public function test_update_product_dto_partial_update(): void
    {
        $dto = UpdateProductDTO::fromArray([
            'name' => 'Updated Name',
            'price' => 149.99,
        ]);

        $this->assertEquals('Updated Name', $dto->name);
        $this->assertEquals(149.99, $dto->price->amount());
        $this->assertNull($dto->description);
        $this->assertNull($dto->stockQuantity);
    }

    public function test_update_product_dto_has_changes(): void
    {
        $dtoWithChanges = UpdateProductDTO::fromArray(['name' => 'New Name']);
        $dtoWithoutChanges = UpdateProductDTO::fromArray([]);

        $this->assertTrue($dtoWithChanges->hasChanges());
        $this->assertFalse($dtoWithoutChanges->hasChanges());
    }

    public function test_update_product_dto_to_array_only_includes_changes(): void
    {
        $dto = UpdateProductDTO::fromArray([
            'name' => 'Updated Name',
            'description' => null, // Explicit null
        ]);

        $array = $dto->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayNotHasKey('price', $array);
        $this->assertArrayNotHasKey('stock_quantity', $array);
    }
}
