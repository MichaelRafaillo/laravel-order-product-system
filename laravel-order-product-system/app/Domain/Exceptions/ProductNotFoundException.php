<?php

namespace App\Domain\Exceptions;

class ProductNotFoundException extends DomainException
{
    public function __construct(int $productId)
    {
        $message = "Product with ID {$productId} not found";
        parent::__construct($message, 'PRODUCT_NOT_FOUND', 404);
    }
}
