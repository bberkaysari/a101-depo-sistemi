<?php

namespace App\Repositories\Interfaces;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Stock;
    public function create(array $data): Stock;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getStocksByLocation(int $locationId): Collection;
    public function getStocksByProduct(int $productId): Collection;
    public function getLowStockItems(): Collection;
    public function getOutOfStockItems(): Collection;
    public function getStocksWithFilters(array $filters = []): LengthAwarePaginator;
    public function checkStockExists(int $productId, int $locationId): ?Stock;
    public function updateStockQuantity(int $id, int $quantity, string $operation): bool;
    public function getStockLevels(int $productId): Collection;
    public function getActiveStocks(): Collection;
}
