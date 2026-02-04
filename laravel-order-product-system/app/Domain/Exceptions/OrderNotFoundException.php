<?php

namespace App\Domain\Exceptions;

class OrderNotFoundException extends DomainException
{
    public function __construct(int $orderId)
    {
        $message = "Order with ID {$orderId} not found";
        parent::__construct($message, 'ORDER_NOT_FOUND', 404);
    }
}
