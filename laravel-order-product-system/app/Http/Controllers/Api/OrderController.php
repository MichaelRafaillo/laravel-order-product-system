<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\CreateOrderDTO;
use App\Application\DTOs\UpdateOrderStatusDTO;
use App\Application\Interfaces\Services\OrderServiceInterface;
use App\Http\Requests\AddOrderItemRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderItemQuantityRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;

class OrderController extends \App\Http\Controllers\Controller
{
    public function __construct(
        protected OrderServiceInterface $orderService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($this->orderService->getAllOrders())
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
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
            $dto = UpdateOrderStatusDTO::fromArray($request->validated());
            $updatedOrder = $this->orderService->updateOrderStatus($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => new OrderResource($updatedOrder)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $cancelledOrder = $this->orderService->cancelOrder($id);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => new OrderResource($cancelledOrder)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function byStatus(string $status): JsonResponse
    {
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

    // Order Item Management Endpoints

    public function addItem(AddOrderItemRequest $request, int $orderId): JsonResponse
    {
        try {
            $validated = $request->validated();
            $order = $this->orderService->addItemToOrder(
                $orderId,
                $validated['product_id'],
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added to order successfully',
                'data' => new OrderResource($order)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function updateItemQuantity(UpdateOrderItemQuantityRequest $request, int $orderId, int $itemId): JsonResponse
    {
        try {
            $validated = $request->validated();
            $order = $this->orderService->updateOrderItemQuantity(
                $orderId,
                $itemId,
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Item quantity updated successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function removeItem(int $orderId, int $itemId): JsonResponse
    {
        try {
            $order = $this->orderService->removeOrderItem($orderId, $itemId);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from order successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function recalculateTotal(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderService->recalculateOrderTotal($orderId);

            return response()->json([
                'success' => true,
                'message' => 'Order total recalculated successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }
}
