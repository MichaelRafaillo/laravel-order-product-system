<?php

namespace App\Application\DTOs;

use App\Domain\ValueObjects\OrderStatus;

class CreateOrderDTO
{
    public function __construct(
        public readonly int $customerId,
        public readonly OrderItemDTO $items,
        public readonly ?OrderStatus $status = null,
        public readonly ?string $notes = null
    ) {}

    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn(array $item) => OrderItemDTO::fromArray($item),
            $data['items']
        );

        return new self(
            customerId: (int) $data['customer_id'],
            items: $items,
            status: isset($data['status']) ? new OrderStatus($data['status']) : OrderStatus::pending(),
            notes: $data['notes'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
            'status' => $this->status?->value(),
            'notes' => $this->notes,
        ];
    }
}
