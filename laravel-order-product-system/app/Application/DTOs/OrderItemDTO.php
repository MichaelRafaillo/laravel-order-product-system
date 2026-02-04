<?php

namespace App\Application\DTOs;

class OrderItemDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            quantity: (int) $data['quantity']
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
