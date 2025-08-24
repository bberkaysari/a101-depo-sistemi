<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['category', 'stocks.location'])->get();
    }

    public function find(int $id): ?Product
    {
        return $this->model->with(['category', 'stocks.location', 'stockRequests'])->find($id);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->model->find($id);
        if (!$product) {
            return false;
        }
        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->model->find($id);
        if (!$product) {
            return false;
        }
        return $product->delete();
    }

    public function getActiveProducts(): Collection
    {
        return $this->model->active()->with(['category', 'stocks.location'])->get();
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return $this->model->active()
            ->where('category_id', $categoryId)
            ->with(['category', 'stocks.location'])
            ->get();
    }

    public function searchProducts(string $search): Collection
    {
        return $this->model->active()
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->with(['category', 'stocks.location'])
            ->get();
    }

    public function getProductsWithStock(): Collection
    {
        return $this->model->active()
            ->with(['category', 'stocks.location'])
            ->whereHas('stocks', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->get();
    }

    public function getProductsWithLowStock(): Collection
    {
        return $this->model->active()
            ->with(['category', 'stocks.location'])
            ->whereHas('stocks', function($query) {
                $query->whereRaw('quantity <= min_quantity');
            })
            ->get();
    }

    public function getProductsOutOfStock(): Collection
    {
        return $this->model->active()
            ->with(['category', 'stocks.location'])
            ->whereHas('stocks', function($query) {
                $query->where('quantity', '=', 0);
            })
            ->get();
    }

    public function getProductsByLocation(int $locationId): Collection
    {
        return $this->model->active()
            ->with(['category', 'stocks.location'])
            ->whereHas('stocks', function($query) use ($locationId) {
                $query->where('location_id', $locationId);
            })
            ->get();
    }

    public function paginateProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->active()->with(['category', 'stocks.location']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getProductStockLevels(int $productId): Collection
    {
        $product = $this->model->find($productId);
        if (!$product) {
            return collect();
        }

        $stocks = $product->stocks()->with('location')->get();
        $locations = \App\Models\Location::active()->get();
        
        return $locations->map(function($location) use ($stocks) {
            $stock = $stocks->where('location_id', $location->id)->first();
            return [
                'location' => $location,
                'quantity' => $stock ? $stock->quantity : 0,
                'min_quantity' => $stock ? $stock->min_quantity : 0,
                'max_quantity' => $stock ? $stock->max_quantity : null,
                'stock' => $stock
            ];
        });
    }

    public function checkSkuExists(string $sku, ?int $excludeId = null): bool
    {
        $query = $this->model->where('sku', $sku);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function checkBarcodeExists(string $barcode, ?int $excludeId = null): bool
    {
        $query = $this->model->where('barcode', $barcode);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }
}
