<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Public access
    }

    public function view(User $user, Order $order): bool
    {
        // Allow if user owns the order or is admin
        return $user->id === $order->customer_id || ($user->is_admin ?? false);
    }

    public function create(User $user): bool
    {
        return true; // Authenticated users can create
    }

    public function update(User $user, Order $order): bool
    {
        // Only admins can update orders
        return $user->is_admin ?? false;
    }

    public function cancel(User $user, Order $order): bool
    {
        // Allow owner or admin to cancel if cancellable
        if ($user->id === $order->customer_id || ($user->is_admin ?? false)) {
            return $order->is_cancellable;
        }
        return false;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->is_admin ?? false;
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->is_admin ?? false;
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->is_admin ?? false;
    }
}
