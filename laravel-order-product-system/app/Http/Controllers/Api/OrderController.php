<?php

namespace App\Http\Controllers\Api;

use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->orderService->getAllOrders()
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'status' => 'sometimes|in:pending,processing,completed,cancelled,refunded',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $order = $this->orderService->createOrder($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded'
        ]);

        $updated = $this->orderService->updateOrderStatus($id, $validated['status']);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $this->orderService->getOrderById($id)
        ]);
    }

    public function cancel(int $id): JsonResponse
    {
        $cancelled = $this->orderService->cancelOrder($id);

        if (!$cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $this->orderService->getOrderById($id)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->orderService->deleteProduct($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    public function byStatus(string $status): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->orderService->getOrdersByStatus($status)
        ]);
    }

    public function customerOrders(int $customerId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->orderService->getCustomerOrders($customerId)
        ]);
    }
}
