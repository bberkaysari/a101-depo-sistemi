<?php

namespace App\Repositories\Eloquent;

use App\Models\Stock;
use App\Repositories\Interfaces\StockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StockRepository implements StockRepositoryInterface
{
    protected $model;

    public function __construct(Stock $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['product.category', 'location'])->get();
    }

    public function find(int $id): ?Stock
    {
        return $this->model->with(['product.category', 'location'])->find($id);
    }

    public function create(array $data): Stock
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $stock = $this->model->find($id);
        if (!$stock) {
            return false;
        }
        return $stock->update($data);
    }

    public function delete(int $id): bool
    {
        $stock = $this->model->find($id);
        if (!$stock) {
            return false;
        }
        return $stock->delete();
    }

    public function getStocksByLocation(int $locationId): Collection
    {
        return $this->model->with(['product.category', 'location'])
            ->where('location_id', $locationId)
            ->get();
    }

    public function getStocksByProduct(int $productId): Collection
    {
        return $this->model->with(['product.category', 'location'])
            ->where('product_id', $productId)
            ->get();
    }

    public function getLowStockItems(): Collection
    {
        return $this->model->with(['product.category', 'location'])
            ->lowStock()
            ->get();
    }

    public function getOutOfStockItems(): Collection
    {
        return $this->model->with(['product.category', 'location'])
            ->outOfStock()
            ->get();
    }

    public function getStocksWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['product.category', 'location']);

        if (isset($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['status'])) {
            switch ($filters['status']) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->outOfStock();
                    break;
                case 'normal':
                    $query->inStock()->whereRaw('quantity > min_quantity');
                    break;
            }
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function checkStockExists(int $productId, int $locationId): ?Stock
    {
        return $this->model->where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();
    }

    public function updateStockQuantity(int $id, int $quantity, string $operation): bool
    {
        $stock = $this->model->find($id);
        if (!$stock) {
            return false;
        }

        switch ($operation) {
            case 'set':
                $stock->setStock($quantity);
                break;
            case 'add':
                $stock->addStock($quantity);
                break;
            case 'remove':
                if (!$stock->removeStock($quantity)) {
                    return false;
                }
                break;
        }

        return true;
    }

    public function getStockLevels(int $productId): Collection
    {
        return $this->model->with('location')
            ->where('product_id', $productId)
            ->get();
    }

    public function getActiveStocks(): Collection
    {
        return $this->model->with(['product.category', 'location'])
            ->whereHas('product', function($query) {
                $query->active();
            })
            ->whereHas('location', function($query) {
                $query->active();
            })
            ->get();
    }
}
