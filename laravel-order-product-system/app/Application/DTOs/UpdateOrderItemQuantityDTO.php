<?php

namespace App\Application\DTOs;

class UpdateOrderItemQuantityDTO
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $itemId,
        public readonly int $quantity
    ) {}

    public static function fromArray(array $data, int $orderId, int $itemId): self
    {
        return new self(
            orderId: $orderId,
            itemId: $itemId,
            quantity: (int) $data['quantity']
        );
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'item_id' => $this->itemId,
            'quantity' => $this->quantity,
        ];
    }
}
