<?php

namespace App\Providers;

use App\Application\Interfaces\Services\OrderServiceInterface;
use App\Application\Interfaces\Services\ProductServiceInterface;
use App\Application\Services\OrderService;
use App\Application\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
