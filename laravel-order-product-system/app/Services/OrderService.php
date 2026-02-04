<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateOrderDTO;
use App\Application\DTOs\OrderItemDTO;
use App\Application\DTOs\UpdateOrderStatusDTO;
use App\Application\Interfaces\Repositories\OrderRepositoryInterface;
use App\Application\Interfaces\Repositories\ProductRepositoryInterface;
use App\Application\Interfaces\Services\OrderServiceInterface;
use App\Domain\Events\OrderCancelled;
use App\Domain\Events\OrderCreated;
use App\Domain\Events\OrderStatusChanged;
use App\Domain\Exceptions\OrderCannotBeCancelledException;
use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\ValueObjects\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAll();
    }

    public function getOrderById(int $id): Order
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order) {
            throw new OrderNotFoundException($id);
        }
        
        return $order;
    }

    public function createOrder(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            $orderData = [
                'customer_id' => $dto->customerId,
                'status' => $dto->status?->value() ?? OrderStatus::PENDING,
                'notes' => $dto->notes,
                'order_number' => $this->generateOrderNumber(),
            ];

            $order = $this->orderRepository->create($orderData);

            foreach ($dto->items as $item) {
                $this->addOrderItem($order, $item);
            }

            // Update total amount
            $order->total_amount = $order->calculateTotal();
            $order->save();

            $order = $order->fresh()->load('items');
            
            Event::dispatch(new OrderCreated($order));
            
            return $order;
        });
    }

    public function updateOrderStatus(int $id, UpdateOrderStatusDTO $dto): Order
    {
        $order = $this->getOrderById($id);

        $previousStatus = new OrderStatus($order->status);
        $newStatus = $dto->status;

        $this->orderRepository->update($id, ['status' => $newStatus->value()]);
        
        $updatedOrder = $order->fresh();
        Event::dispatch(new OrderStatusChanged(
            $updatedOrder,
            $previousStatus,
            $newStatus
        ));
        
        return $updatedOrder;
    }

    public function cancelOrder(int $id, string $reason = ''): Order
    {
        $order = $this->getOrderById($id);
        
        if ($order->status === OrderStatus::COMPLETED) {
            throw new OrderCannotBeCancelledException($id, $order->status);
        }

        // Restore stock
        foreach ($order->items as $item) {
            $product = $this->productRepository->findById($item->product_id);
            $this->productRepository->update($item->product_id, [
                'stock_quantity' => $product->stock_quantity + $item->quantity
            ]);
        }

        $this->orderRepository->update($id, ['status' => OrderStatus::CANCELLED]);
        
        $cancelledOrder = $order->fresh();
        Event::dispatch(new OrderCancelled($cancelledOrder, $reason));
        
        return $cancelledOrder;
    }

    public function deleteOrder(int $id): void
    {
        $this->getOrderById($id);
        $this->orderRepository->delete($id);
    }

    public function getOrdersByStatus(string $status): Collection
    {
        return $this->orderRepository->getByStatus($status);
    }

    public function getCustomerOrders(int $customerId): Collection
    {
        return $this->orderRepository->getByCustomer($customerId);
    }

    private function addOrderItem(Order $order, OrderItemDTO $item): void
    {
        $product = $this->productRepository->findById($item->productId);
        
        $order->items()->create([
            'product_id' => $item->productId,
            'quantity' => $item->quantity,
            'unit_price' => $product->price,
            'subtotal' => $product->price * $item->quantity
        ]);

        // Reduce stock
        $this->productRepository->update($item->productId, [
            'stock_quantity' => $product->stock_quantity - $item->quantity
        ]);
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}
