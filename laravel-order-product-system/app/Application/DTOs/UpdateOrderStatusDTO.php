<?php

namespace App\Application\DTOs;

use App\Domain\ValueObjects\OrderStatus;

class UpdateOrderStatusDTO
{
    public function __construct(
        public readonly OrderStatus $status
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: new OrderStatus($data['status'])
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status->value(),
        ];
    }
}
