<?php

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAll();
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = $this->orderRepository->create($data);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $product = $this->productRepository->findById($item['product_id']);
                    
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $product->price * $item['quantity']
                    ]);

                    // Reduce stock
                    $this->productRepository->update($item['product_id'], [
                        'stock_quantity' => $product->stock_quantity - $item['quantity']
                    ]);
                }
            }

            return $order->load('items');
        });
    }

    public function updateOrderStatus(int $id, string $status): bool
    {
        return $this->orderRepository->update($id, ['status' => $status]);
    }

    public function cancelOrder(int $id): bool
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order || $order->status === 'completed') {
            return false;
        }

        // Restore stock
        foreach ($order->items as $item) {
            $product = $this->productRepository->findById($item->product_id);
            $this->productRepository->update($item->product_id, [
                'stock_quantity' => $product->stock_quantity + $item->quantity
            ]);
        }

        return $this->orderRepository->update($id, ['status' => 'cancelled']);
    }

    public function getOrdersByStatus(string $status): Collection
    {
        return $this->orderRepository->getByStatus($status);
    }

    public function getCustomerOrders(int $customerId): Collection
    {
        return $this->orderRepository->getByCustomer($customerId);
    }
}
