<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class SKU
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new InvalidArgumentException('SKU cannot be empty');
        }
        
        if (strlen($value) > 50) {
            throw new InvalidArgumentException('SKU cannot exceed 50 characters');
        }
        
        if (!preg_match('/^[A-Za-z0-9-_]+$/', $value)) {
            throw new InvalidArgumentException('SKU can only contain alphanumeric characters, hyphens, and underscores');
        }
        
        $this->value = strtoupper($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(SKU $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
