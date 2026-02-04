<?php

namespace App\Domain\Exceptions;

use Exception;

class DomainException extends Exception
{
    protected string $errorCode;

    public function __construct(string $message, string $errorCode = 'DOMAIN_ERROR', int $code = 400)
    {
        parent::__construct($message, $code);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
