<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAll();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function createProduct(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function searchProducts(string $keyword): Collection
    {
        return $this->productRepository->search($keyword);
    }

    public function reduceStock(int $productId, int $quantity): bool
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product || $product->stock_quantity < $quantity) {
            return false;
        }

        return $this->productRepository->update($productId, [
            'stock_quantity' => $product->stock_quantity - $quantity
        ]);
    }
}
