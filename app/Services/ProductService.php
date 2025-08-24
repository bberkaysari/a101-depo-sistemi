<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class ProductService
{
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->all();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:products',
            'barcode' => 'nullable|string|max:100|unique:products',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $product = $this->productRepository->create($data);
            return [
                'success' => true,
                'data' => $product
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ürün oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateProduct(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'sometimes|required|string|max:100|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $id,
            'category_id' => 'sometimes|required|exists:categories,id',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'unit' => 'sometimes|required|string|max:50',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $updated = $this->productRepository->update($id, $data);
            if ($updated) {
                $product = $this->productRepository->find($id);
                return [
                    'success' => true,
                    'data' => $product
                ];
            }
            return [
                'success' => false,
                'message' => 'Ürün bulunamadı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ürün güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function deleteProduct(int $id): array
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Ürün bulunamadı'
                ];
            }

            // Ürünün stokları var mı kontrol et
            if ($product->stocks()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Bu ürünün stokları bulunmaktadır. Önce stokları silmelisiniz.'
                ];
            }

            $deleted = $this->productRepository->delete($id);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Ürün başarıyla silindi' : 'Ürün silinemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ürün silinirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActiveProducts();
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return $this->productRepository->getProductsByCategory($categoryId);
    }

    public function searchProducts(string $search): Collection
    {
        return $this->productRepository->searchProducts($search);
    }

    public function getProductsWithStock(): Collection
    {
        return $this->productRepository->getProductsWithStock();
    }

    public function getProductsWithLowStock(): Collection
    {
        return $this->productRepository->getProductsWithLowStock();
    }

    public function getProductsOutOfStock(): Collection
    {
        return $this->productRepository->getProductsOutOfStock();
    }

    public function getProductsByLocation(int $locationId): Collection
    {
        return $this->productRepository->getProductsByLocation($locationId);
    }

    public function paginateProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginateProducts($filters, $perPage);
    }

    public function getProductStockLevels(int $productId): Collection
    {
        return $this->productRepository->getProductStockLevels($productId);
    }

    public function checkSkuExists(string $sku, ?int $excludeId = null): bool
    {
        return $this->productRepository->checkSkuExists($sku, $excludeId);
    }

    public function checkBarcodeExists(string $barcode, ?int $excludeId = null): bool
    {
        return $this->productRepository->checkBarcodeExists($barcode, $excludeId);
    }
}
