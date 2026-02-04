<?php

namespace App\Domain\Exceptions;

class OrderCannotBeCancelledException extends DomainException
{
    public function __construct(int $orderId, string $status)
    {
        $message = "Order {$orderId} cannot be cancelled because it is already {$status}";
        parent::__construct($message, 'ORDER_CANNOT_BE_CANCELLED', 400);
    }
}
