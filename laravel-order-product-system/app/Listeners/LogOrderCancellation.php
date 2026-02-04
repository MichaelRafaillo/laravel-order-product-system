<?php

namespace App\Listeners;

use App\Domain\Events\OrderCancelled;

class LogOrderCancellation
{
    public function handle(OrderCancelled $event): void
    {
        logger()->info("Order {$event->order->order_number} cancelled", [
            'order_id' => $event->order->id,
            'reason' => $event->reason,
            'stock_restored' => true,
        ]);
    }
}
