<?php

namespace App\Application\Interfaces\Services;

use App\Application\DTOs\CreateOrderDTO;
use App\Application\DTOs\UpdateOrderStatusDTO;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
{
    public function getAllOrders(): Collection;
    public function getOrderById(int $id): Order;
    public function createOrder(CreateOrderDTO $dto): Order;
    public function updateOrderStatus(int $id, UpdateOrderStatusDTO $dto): Order;
    public function cancelOrder(int $id, string $reason = ''): Order;
    public function deleteOrder(int $id): void;
    public function getOrdersByStatus(string $status): Collection;
    public function getCustomerOrders(int $customerId): Collection;
    
    // Order Item Management
    public function addItemToOrder(int $orderId, int $productId, int $quantity): Order;
    public function updateOrderItemQuantity(int $orderId, int $itemId, int $quantity): Order;
    public function removeOrderItem(int $orderId, int $itemId): Order;
    public function recalculateOrderTotal(int $orderId): Order;
}
