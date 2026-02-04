<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Domain\Events\ProductCreated::class => [
            \App\Listeners\LogProductActivity::class,
        ],
        \App\Domain\Events\ProductUpdated::class => [
            \App\Listeners\LogProductActivity::class,
        ],
        \App\Domain\Events\OrderCreated::class => [
            \App\Listeners\LogOrderActivity::class,
            \App\Listeners\SendOrderConfirmation::class,
        ],
        \App\Domain\Events\OrderStatusChanged::class => [
            \App\Listeners\LogOrderStatusChange::class,
            \App\Listeners\NotifyOrderStatusChange::class,
        ],
        \App\Domain\Events\OrderCancelled::class => [
            \App\Listeners\LogOrderCancellation::class,
            \App\Listeners\NotifyOrderCancellation::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
