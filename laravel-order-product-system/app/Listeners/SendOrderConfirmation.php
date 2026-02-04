<?php

namespace App\Listeners;

use App\Domain\Events\OrderCreated;

class SendOrderConfirmation
{
    public function handle(OrderCreated $event): void
    {
        // In a real app, this would send an email/SMS
        logger()->info("Order confirmation sent for {$event->order->order_number}");
    }
}
