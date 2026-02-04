<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\OrderStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_can_create_valid_status(): void
    {
        $status = new OrderStatus('pending');
        
        $this->assertEquals('pending', $status->value());
        $this->assertEquals('Pending', $status->label());
    }

    public function test_can_create_all_valid_statuses(): void
    {
        foreach (['pending', 'processing', 'completed', 'cancelled', 'refunded'] as $statusValue) {
            $status = new OrderStatus($statusValue);
            $this->assertEquals($statusValue, $status->value());
        }
    }

    public function test_case_insensitive(): void
    {
        $status = new OrderStatus('PENDING');
        
        $this->assertEquals('pending', $status->value());
    }

    public function test_cannot_create_invalid_status(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        new OrderStatus('invalid_status');
    }

    public function test_pending_is_cancellable(): void
    {
        $status = new OrderStatus('pending');
        
        $this->assertTrue($status->isPending());
        $this->assertTrue($status->isCancellable());
        $this->assertFalse($status->isCompleted());
    }

    public function test_processing_is_cancellable(): void
    {
        $status = new OrderStatus('processing');
        
        $this->assertTrue($status->isProcessing());
        $this->assertTrue($status->isCancellable());
    }

    public function test_completed_is_not_cancellable(): void
    {
        $status = new OrderStatus('completed');
        
        $this->assertFalse($status->isCancellable());
        $this->assertTrue($status->isCompleted());
    }

    public function test_static_factory_methods(): void
    {
        $pending = OrderStatus::pending();
        $processing = OrderStatus::processing();
        $completed = OrderStatus::completed();
        $cancelled = OrderStatus::cancelled();
        
        $this->assertEquals('pending', $pending->value());
        $this->assertEquals('processing', $processing->value());
        $this->assertEquals('completed', $completed->value());
        $this->assertEquals('cancelled', $cancelled->value());
    }

    public function test_status_equality(): void
    {
        $status1 = new OrderStatus('pending');
        $status2 = new OrderStatus('pending');
        $status3 = new OrderStatus('processing');
        
        $this->assertTrue($status1->equals($status2));
        $this->assertFalse($status1->equals($status3));
    }
}
