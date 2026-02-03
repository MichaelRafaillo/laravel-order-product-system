<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Policies\ProductPolicy;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', ProductPolicy::class);
        
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($this->productService->getAllProducts())
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $this->authorize('view', $product);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product)
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->authorize('create', ProductPolicy::class);

        $validated = $request->validated();
        $product = $this->productService->createProduct($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ], 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $this->authorize('update', $product);

        $validated = $request->validated();
        $this->productService->updateProduct($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($this->productService->getProductById($id))
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $this->authorize('delete', $product);

        $this->productService->deleteProduct($id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductPolicy::class);

        $keyword = $request->get('q', '');
        
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($this->productService->searchProducts($keyword))
        ]);
    }
}
