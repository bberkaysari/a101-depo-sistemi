<?php

namespace App\Services;

use App\Repositories\Interfaces\StockRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class StockService
{
    protected $stockRepository;
    protected $productRepository;
    protected $locationRepository;

    public function __construct(
        StockRepositoryInterface $stockRepository,
        ProductRepositoryInterface $productRepository,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->stockRepository = $stockRepository;
        $this->productRepository = $productRepository;
        $this->locationRepository = $locationRepository;
    }

    public function getAllStocks(): Collection
    {
        return $this->stockRepository->all();
    }

    public function getStockById(int $id): ?Stock
    {
        return $this->stockRepository->find($id);
    }

    public function createStock(array $data): array
    {
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Aynı ürün-lokasyon kombinasyonunda stok var mı kontrol et
        $existingStock = $this->stockRepository->checkStockExists(
            $data['product_id'], 
            $data['location_id']
        );

        if ($existingStock) {
            return [
                'success' => false,
                'message' => 'Bu ürün için bu lokasyonda zaten stok tanımlanmış.'
            ];
        }

        try {
            $stock = $this->stockRepository->create($data);
            return [
                'success' => true,
                'data' => $stock
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateStock(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'product_id' => 'sometimes|required|exists:products,id',
            'location_id' => 'sometimes|required|exists:locations,id',
            'quantity' => 'sometimes|required|integer|min:0',
            'min_quantity' => 'sometimes|required|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Aynı ürün-lokasyon kombinasyonunda başka stok var mı kontrol et
        if (isset($data['product_id']) && isset($data['location_id'])) {
            $existingStock = $this->stockRepository->checkStockExists(
                $data['product_id'], 
                $data['location_id']
            );

            if ($existingStock && $existingStock->id != $id) {
                return [
                    'success' => false,
                    'message' => 'Bu ürün için bu lokasyonda zaten stok tanımlanmış.'
                ];
            }
        }

        try {
            $updated = $this->stockRepository->update($id, $data);
            if ($updated) {
                $stock = $this->stockRepository->find($id);
                return [
                    'success' => true,
                    'data' => $stock
                ];
            }
            return [
                'success' => false,
                'message' => 'Stok bulunamadı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function deleteStock(int $id): array
    {
        try {
            $stock = $this->stockRepository->find($id);
            if (!$stock) {
                return [
                    'success' => false,
                    'message' => 'Stok bulunamadı'
                ];
            }

            $deleted = $this->stockRepository->delete($id);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Stok başarıyla silindi' : 'Stok silinemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok silinirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateStockQuantity(int $id, int $quantity, string $operation): array
    {
        $validator = Validator::make([
            'quantity' => $quantity,
            'operation' => $operation
        ], [
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|in:set,add,remove',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $updated = $this->stockRepository->updateStockQuantity($id, $quantity, $operation);
            if ($updated) {
                $stock = $this->stockRepository->find($id);
                return [
                    'success' => true,
                    'data' => $stock
                ];
            }
            return [
                'success' => false,
                'message' => 'Stok miktarı güncellenemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok miktarı güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function getStocksByLocation(int $locationId): Collection
    {
        return $this->stockRepository->getStocksByLocation($locationId);
    }

    public function getStocksByProduct(int $productId): Collection
    {
        return $this->stockRepository->getStocksByProduct($productId);
    }

    public function getLowStockItems(): Collection
    {
        return $this->stockRepository->getLowStockItems();
    }

    public function getOutOfStockItems(): Collection
    {
        return $this->stockRepository->getOutOfStockItems();
    }

    public function getStocksWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->stockRepository->getStocksWithFilters($filters);
    }

    public function getStockLevels(int $productId): Collection
    {
        return $this->stockRepository->getStockLevels($productId);
    }

    public function getActiveStocks(): Collection
    {
        return $this->stockRepository->getActiveStocks();
    }
}
