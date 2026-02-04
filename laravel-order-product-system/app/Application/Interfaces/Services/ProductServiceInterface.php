<?php

namespace App\Application\Interfaces\Services;

use App\Application\DTOs\CreateProductDTO;
use App\Application\DTOs\UpdateProductDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getAllProducts(): Collection;
    public function getProductById(int $id): Product;
    public function createProduct(CreateProductDTO $dto): Product;
    public function updateProduct(int $id, UpdateProductDTO $dto): Product;
    public function deleteProduct(int $id): void;
    public function searchProducts(string $keyword): Collection;
    public function reduceStock(int $productId, int $quantity): void;
}
