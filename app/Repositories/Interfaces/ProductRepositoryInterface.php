<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getActiveProducts(): Collection;
    public function getProductsByCategory(int $categoryId): Collection;
    public function searchProducts(string $search): Collection;
    public function getProductsWithStock(): Collection;
    public function getProductsWithLowStock(): Collection;
    public function getProductsOutOfStock(): Collection;
    public function getProductsByLocation(int $locationId): Collection;
    public function paginateProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getProductStockLevels(int $productId): Collection;
    public function checkSkuExists(string $sku, ?int $excludeId = null): bool;
    public function checkBarcodeExists(string $barcode, ?int $excludeId = null): bool;
}
