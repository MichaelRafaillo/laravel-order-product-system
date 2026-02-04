<?php

namespace App\Application\DTOs;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\SKU;

class UpdateProductDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?Money $price = null,
        public readonly ?int $stockQuantity = null,
        public readonly ?SKU $sku = null,
        public readonly ?bool $isActive = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            price: isset($data['price']) ? new Money((float) $data['price']) : null,
            stockQuantity: $data['stock_quantity'] ?? null,
            sku: isset($data['sku']) ? new SKU($data['sku']) : null,
            isActive: $data['is_active'] ?? null
        );
    }

    public function toArray(): array
    {
        $array = [];
        
        if ($this->name !== null) {
            $array['name'] = $this->name;
        }
        
        if ($this->description !== null) {
            $array['description'] = $this->description;
        }
        
        if ($this->price !== null) {
            $array['price'] = $this->price->amount();
        }
        
        if ($this->stockQuantity !== null) {
            $array['stock_quantity'] = $this->stockQuantity;
        }
        
        if ($this->sku !== null) {
            $array['sku'] = $this->sku->value();
        }
        
        if ($this->isActive !== null) {
            $array['is_active'] = $this->isActive;
        }
        
        return $array;
    }

    public function hasChanges(): bool
    {
        return !empty($this->toArray());
    }
}
