<?php

namespace App\Application\DTOs;

class AddOrderItemDTO
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $productId,
        public readonly int $quantity
    ) {}

    public static function fromArray(array $data, int $orderId): self
    {
        return new self(
            orderId: $orderId,
            productId: (int) $data['product_id'],
            quantity: (int) $data['quantity']
        );
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
