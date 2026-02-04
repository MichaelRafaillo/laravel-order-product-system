<?php

namespace App\Providers;

use App\Application\Interfaces\Repositories\OrderRepositoryInterface;
use App\Application\Interfaces\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
