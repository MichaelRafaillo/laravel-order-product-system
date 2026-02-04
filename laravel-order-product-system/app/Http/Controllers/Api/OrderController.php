<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\CreateOrderDTO;
use App\Application\DTOs\UpdateOrderStatusDTO;
use App\Application\Interfaces\Services\OrderServiceInterface;
use App\Domain\Exceptions\DomainException;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Policies\OrderPolicy;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function __construct(
        protected OrderServiceInterface $orderService
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
        try {
            $order = $this->orderService->getOrderById($id);
            $this->authorize('view', $order);

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order)
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage()
                ]
            ], $e->getCode());
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', OrderPolicy::class);

        $dto = CreateOrderDTO::fromArray($request->validated());
        $order = $this->orderService->createOrder($dto);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order)
        ], 201);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            $this->authorize('update', $order);

            $dto = UpdateOrderStatusDTO::fromArray($request->validated());
            $updatedOrder = $this->orderService->updateOrderStatus($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => new OrderResource($updatedOrder)
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage()
                ]
            ], $e->getCode());
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            $this->authorize('cancel', $order);

            $cancelledOrder = $this->orderService->cancelOrder($id);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => new OrderResource($cancelledOrder)
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage()
                ]
            ], $e->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            $this->authorize('delete', $order);

            $this->orderService->deleteOrder($id);

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage()
                ]
            ], $e->getCode());
        }
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
