<?php

namespace App\Domain\Exceptions;

class InsufficientStockException extends DomainException
{
    public function __construct(int $productId, int $requested, int $available)
    {
        $message = "Insufficient stock for product {$productId}. Requested: {$requested}, Available: {$available}";
        parent::__construct($message, 'INSUFFICIENT_STOCK', 400);
    }
}
