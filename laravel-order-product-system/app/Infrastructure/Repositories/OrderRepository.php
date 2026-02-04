<?php

namespace App\Infrastructure\Repositories;

use App\Application\Interfaces\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAll(): Collection
    {
        return Order::withTrashed()->with('items')->get();
    }

    public function findById(int $id): ?Order
    {
        return Order::withTrashed()->with('items')->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $order = $this->findById($id);
        return $order ? $order->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $order = $this->findById($id);
        return $order ? $order->delete() : false;
    }

    public function getByStatus(string $status): Collection
    {
        return Order::withTrashed()->with('items')
            ->where('status', $status)
            ->get();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return Order::withTrashed()->with('items')
            ->where('customer_id', $customerId)
            ->get();
    }
}
