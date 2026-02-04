<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Product Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});

// Order Routes
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('/', [OrderController::class, 'store']);
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
    Route::get('/status/{status}', [OrderController::class, 'byStatus']);
    Route::get('/customer/{customerId}', [OrderController::class, 'customerOrders']);
    
    // Order Item Management Routes
    Route::post('/{orderId}/items', [OrderController::class, 'addItem']);
    Route::put('/{orderId}/items/{itemId}', [OrderController::class, 'updateItemQuantity']);
    Route::delete('/{orderId}/items/{itemId}', [OrderController::class, 'removeItem']);
    Route::post('/{orderId}/recalculate', [OrderController::class, 'recalculateTotal']);
});
