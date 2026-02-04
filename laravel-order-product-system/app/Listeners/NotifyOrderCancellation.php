<?php

namespace App\Listeners;

use App\Domain\Events\OrderCancelled;

class NotifyOrderCancellation
{
    public function handle(OrderCancelled $event): void
    {
        logger()->info("Cancellation notification sent for order {$event->order->order_number}");
    }
}
