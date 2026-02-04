<?php

namespace App\Listeners;

use App\Domain\Events\ProductCreated;
use App\Domain\Events\ProductUpdated;

class LogProductActivity
{
    public function handle(ProductCreated|ProductUpdated $event): void
    {
        $action = $event instanceof ProductCreated ? 'created' : 'updated';
        
        logger()->info("Product {$event->product->id} {$action}", [
            'product_id' => $event->product->id,
            'name' => $event->product->name,
            'changes' => $event instanceof ProductUpdated ? $event->changes : null,
        ]);
    }
}
