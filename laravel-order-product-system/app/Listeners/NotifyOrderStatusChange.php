<?php

namespace App\Listeners;

use App\Domain\Events\OrderStatusChanged;

class NotifyOrderStatusChange
{
    public function handle(OrderStatusChanged $event): void
    {
        // In a real app, this would notify the customer
        logger()->info("Customer notified: Order {$event->order->order_number} is now {$event->newStatus->label()}");
    }
}
