<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Public access
    }

    public function view(User $user, Product $product): bool
    {
        return true; // Public access
    }

    public function create(User $user): bool
    {
        return $user->is_admin ?? false;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->is_admin ?? false;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->is_admin ?? false;
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->is_admin ?? false;
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->is_admin ?? false;
    }
}
