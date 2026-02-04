<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class OrderStatus
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const REFUNDED = 'refunded';

    private const ALLOWED_STATUSES = [
        self::PENDING,
        self::PROCESSING,
        self::COMPLETED,
        self::CANCELLED,
        self::REFUNDED,
    ];

    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower($value);
        
        if (!in_array($value, self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException("Invalid order status: {$value}");
        }
        
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return match($this->value) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->value === self::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->value === self::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }

    public function isCancellable(): bool
    {
        return in_array($this->value, [self::PENDING, self::PROCESSING]);
    }

    public function equals(OrderStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }
}
