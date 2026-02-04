<?php

namespace App\Listeners;

use App\Domain\Events\OrderStatusChanged;

class LogOrderStatusChange
{
    public function handle(OrderStatusChanged $event): void
    {
        logger()->info("Order {$event->order->order_number} status changed", [
            'order_id' => $event->order->id,
            'previous_status' => $event->previousStatus->value(),
            'new_status' => $event->newStatus->value(),
        ]);
    }
}
