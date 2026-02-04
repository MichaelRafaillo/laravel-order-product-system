<?php

namespace App\Application\DTOs;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\SKU;

class CreateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly Money $price,
        public readonly int $stockQuantity,
        public readonly SKU $sku,
        public readonly bool $isActive = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            price: new Money((float) $data['price']),
            stockQuantity: (int) $data['stock_quantity'],
            sku: new SKU($data['sku']),
            isActive: $data['is_active'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price->amount(),
            'stock_quantity' => $this->stockQuantity,
            'sku' => $this->sku->value(),
            'is_active' => $this->isActive,
        ];
    }
}
