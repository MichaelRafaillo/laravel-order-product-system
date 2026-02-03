<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Policies\OrderPolicy;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', OrderPolicy::class);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($this->orderService->getAllOrders())
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

        $this->authorize('view', $order);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', OrderPolicy::class);

        $validated = $request->validated();
        $order = $this->orderService->createOrder($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order)
        ], 201);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $this->authorize('update', $order);

        $validated = $request->validated();
        $this->orderService->updateOrderStatus($id, $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => new OrderResource($this->orderService->getOrderById($id))
        ]);
    }

    public function cancel(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $this->authorize('cancel', $order);

        $this->orderService->cancelOrder($id);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => new OrderResource($this->orderService->getOrderById($id))
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $this->authorize('delete', $order);

        $this->orderService->deleteProduct($id);

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    public function byStatus(string $status): JsonResponse
    {
        $this->authorize('viewAny', OrderPolicy::class);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($this->orderService->getOrdersByStatus($status))
        ]);
    }

    public function customerOrders(int $customerId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($this->orderService->getCustomerOrders($customerId))
        ]);
    }
}
