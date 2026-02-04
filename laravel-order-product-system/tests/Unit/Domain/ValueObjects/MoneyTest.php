<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_can_create_money(): void
    {
        $money = new Money(99.99, 'USD');
        
        $this->assertEquals(99.99, $money->amount());
        $this->assertEquals('USD', $money->currency());
    }

    public function test_cannot_create_negative_money(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Money(-10, 'USD');
    }

    public function test_money_addition(): void
    {
        $money1 = new Money(50, 'USD');
        $money2 = new Money(30, 'USD');
        
        $result = $money1->add($money2);
        
        $this->assertEquals(80, $result->amount());
        $this->assertEquals('USD', $result->currency());
    }

    public function test_cannot_add_different_currencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $money1 = new Money(50, 'USD');
        $money2 = new Money(30, 'EUR');
        
        $money1->add($money2);
    }

    public function test_money_multiplication(): void
    {
        $money = new Money(25, 'USD');
        
        $result = $money->multiply(4);
        
        $this->assertEquals(100, $result->amount());
    }

    public function test_money_equality(): void
    {
        $money1 = new Money(99.99, 'USD');
        $money2 = new Money(99.99, 'USD');
        $money3 = new Money(99.99, 'EUR');
        
        $this->assertTrue($money1->equals($money2));
        $this->assertFalse($money1->equals($money3));
    }

    public function test_formatted_money(): void
    {
        $money = new Money(1234.56, 'USD');
        
        $this->assertEquals('1234.56 USD', $money->formatted());
    }
}
