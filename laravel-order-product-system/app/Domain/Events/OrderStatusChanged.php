<?php

namespace App\Domain\Events;

use App\Models\Order;
use App\Domain\ValueObjects\OrderStatus;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $previousStatus,
        public readonly OrderStatus $newStatus
    ) {}
}
