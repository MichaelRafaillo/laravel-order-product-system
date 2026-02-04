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

    public function addItemToOrder(int $orderId, int $productId, int $quantity): Order
    {
        $order = $this->getOrderById($orderId);
        
        if ($order->status !== OrderStatus::PENDING && $order->status !== OrderStatus::PROCESSING) {
            throw new \Exception("Cannot add items to order with status: {$order->status}");
        }

        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \Exception("Product not found");
        }

        if ($product->stock_quantity < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$product->stock_quantity}");
        }

        DB::transaction(function () use ($order, $product, $quantity) {
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $product->price * $quantity
            ]);

            // Reduce stock
            $this->productRepository->update($product->id, [
                'stock_quantity' => $product->stock_quantity - $quantity
            ]);

            // Recalculate total
            $this->recalculateOrderTotal($order->id);
        });

        return $this->getOrderById($orderId);
    }

    public function updateOrderItemQuantity(int $orderId, int $itemId, int $quantity): Order
    {
        $order = $this->getOrderById($orderId);
        
        if ($order->status !== OrderStatus::PENDING && $order->status !== OrderStatus::PROCESSING) {
            throw new \Exception("Cannot modify items in order with status: {$order->status}");
        }

        $item = $order->items()->find($itemId);
        
        if (!$item) {
            throw new \Exception("Order item not found");
        }

        $product = $this->productRepository->findById($item->product_id);
        
        DB::transaction(function () use ($item, $quantity, $product) {
            $quantityDiff = $quantity - $item->quantity;
            
            if ($quantityDiff > 0) {
                // Increasing quantity - reduce more stock
                if ($product->stock_quantity < $quantityDiff) {
                    throw new \Exception("Insufficient stock. Need: {$quantityDiff}, Available: {$product->stock_quantity}");
                }
                
                $this->productRepository->update($product->id, [
                    'stock_quantity' => $product->stock_quantity - $quantityDiff
                ]);
            } elseif ($quantityDiff < 0) {
                // Decreasing quantity - restore stock
                $this->productRepository->update($product->id, [
                    'stock_quantity' => $product->stock_quantity + abs($quantityDiff)
                ]);
            }

            // Update item
            $item->update([
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity
            ]);

            $this->recalculateOrderTotal($item->order_id);
        });

        return $this->getOrderById($orderId);
    }

    public function removeOrderItem(int $orderId, int $itemId): Order
    {
        $order = $this->getOrderById($orderId);
        
        if ($order->status !== OrderStatus::PENDING && $order->status !== OrderStatus::PROCESSING) {
            throw new \Exception("Cannot remove items from order with status: {$order->status}");
        }

        $item = $order->items()->find($itemId);
        
        if (!$item) {
            throw new \Exception("Order item not found");
        }

        $product = $this->productRepository->findById($item->product_id);

        DB::transaction(function () use ($item, $product) {
            // Restore stock
            $this->productRepository->update($product->id, [
                'stock_quantity' => $product->stock_quantity + $item->quantity
            ]);

            // Delete item
            $item->delete();

            // Recalculate total
            $this->recalculateOrderTotal($item->order_id);
        });

        return $this->getOrderById($orderId);
    }

    public function recalculateOrderTotal(int $orderId): Order
    {
        $order = $this->getOrderById($orderId);
        
        $total = $order->items->sum('subtotal');
        
        $this->orderRepository->update($orderId, [
            'total_amount' => $total
        ]);

        return $this->getOrderById($orderId);
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
