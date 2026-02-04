<?php

namespace Tests\Unit\Application\DTOs;

use App\Application\DTOs\CreateOrderDTO;
use App\Application\DTOs\OrderItemDTO;
use App\Application\DTOs\UpdateOrderStatusDTO;
use App\Domain\ValueObjects\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderDTOTest extends TestCase
{
    public function test_create_order_dto_from_array(): void
    {
        $data = [
            'customer_id' => 1,
            'status' => 'pending',
            'notes' => 'Test order',
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 1],
            ],
        ];

        $dto = CreateOrderDTO::fromArray($data);

        $this->assertEquals(1, $dto->customerId);
        $this->assertEquals('pending', $dto->status->value());
        $this->assertEquals('Test order', $dto->notes);
        $this->assertCount(2, $dto->items);
    }

    public function test_create_order_dto_default_status(): void
    {
        $dto = CreateOrderDTO::fromArray([
            'customer_id' => 1,
            'items' => [['product_id' => 1, 'quantity' => 1]],
        ]);

        $this->assertEquals('pending', $dto->status->value());
    }

    public function test_create_order_dto_to_array(): void
    {
        $dto = new CreateOrderDTO(
            customerId: 1,
            items: [
                new OrderItemDTO(1, 2),
            ],
            status: OrderStatus::processing(),
            notes: 'VIP customer'
        );

        $array = $dto->toArray();

        $this->assertEquals(1, $array['customer_id']);
        $this->assertEquals('processing', $array['status']);
        $this->assertEquals('VIP customer', $array['notes']);
        $this->assertCount(1, $array['items']);
    }

    public function test_order_item_dto(): void
    {
        $dto = new OrderItemDTO(productId: 5, quantity: 3);

        $this->assertEquals(5, $dto->productId);
        $this->assertEquals(3, $dto->quantity);
    }

    public function test_order_item_dto_from_array(): void
    {
        $data = ['product_id' => 10, 'quantity' => 5];
        $dto = OrderItemDTO::fromArray($data);

        $this->assertEquals(10, $dto->productId);
        $this->assertEquals(5, $dto->quantity);
    }

    public function test_update_order_status_dto(): void
    {
        $dto = UpdateOrderStatusDTO::fromArray(['status' => 'completed']);

        $this->assertEquals('completed', $dto->status->value());
        $this->assertTrue($dto->status->isCompleted());
    }

    public function test_update_order_status_dto_to_array(): void
    {
        $dto = new UpdateOrderStatusDTO(status: OrderStatus::cancelled());
        $array = $dto->toArray();

        $this->assertEquals('cancelled', $array['status']);
    }
}
