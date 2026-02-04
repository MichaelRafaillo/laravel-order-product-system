<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\CreateProductDTO;
use App\Application\DTOs\UpdateProductDTO;
use App\Application\Interfaces\Services\ProductServiceInterface;
use App\Domain\Exceptions\DomainException;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class ProductController extends \App\Http\Controllers\Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($this->productService->getAllProducts())
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
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

    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = CreateProductDTO::fromArray($request->validated());
        $product = $this->productService->createProduct($dto);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ], 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateProductDTO::fromArray($request->validated());
            $updatedProduct = $this->productService->updateProduct($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource($updatedProduct)
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
            $this->productService->deleteProduct($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
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

    public function search(\Illuminate\Http\Request $request): JsonResponse
    {
        $keyword = $request->get('q', '');
        
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($this->productService->searchProducts($keyword))
        ]);
    }
}
