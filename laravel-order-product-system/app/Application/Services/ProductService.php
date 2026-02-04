<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateProductDTO;
use App\Application\DTOs\UpdateProductDTO;
use App\Application\Interfaces\Repositories\ProductRepositoryInterface;
use App\Application\Interfaces\Services\ProductServiceInterface;
use App\Domain\Events\ProductCreated;
use App\Domain\Events\ProductUpdated;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAll();
    }

    public function getProductById(int $id): Product
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            throw new ProductNotFoundException($id);
        }
        
        return $product;
    }

    public function createProduct(CreateProductDTO $dto): Product
    {
        $product = $this->productRepository->create($dto->toArray());
        
        Event::dispatch(new ProductCreated($product));
        
        return $product;
    }

    public function updateProduct(int $id, UpdateProductDTO $dto): Product
    {
        $product = $this->getProductById($id);

        $changes = $dto->toArray();
        $this->productRepository->update($id, $changes);
        
        $updatedProduct = $product->fresh();
        Event::dispatch(new ProductUpdated($updatedProduct, $changes));
        
        return $updatedProduct;
    }

    public function deleteProduct(int $id): void
    {
        $this->getProductById($id);
        $this->productRepository->delete($id);
    }

    public function searchProducts(string $keyword): Collection
    {
        return $this->productRepository->search($keyword);
    }

    public function reduceStock(int $productId, int $quantity): void
    {
        $product = $this->getProductById($productId);
        
        if ($product->stock_quantity < $quantity) {
            throw new InsufficientStockException(
                $productId,
                $quantity,
                $product->stock_quantity
            );
        }

        $this->productRepository->update($productId, [
            'stock_quantity' => $product->stock_quantity - $quantity
        ]);
    }
}
