<?php

namespace App\Listeners;

use App\Domain\Events\OrderCreated;

class LogOrderActivity
{
    public function handle(OrderCreated $event): void
    {
        logger()->info("Order {$event->order->order_number} created", [
            'order_id' => $event->order->id,
            'order_number' => $event->order->order_number,
            'customer_id' => $event->order->customer_id,
            'total_amount' => $event->order->total_amount,
        ]);
    }
}
