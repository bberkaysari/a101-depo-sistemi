<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function createCategory(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $category = $this->categoryRepository->create($data);
            return [
                'success' => true,
                'data' => $category
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kategori oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateCategory(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Kendisini parent olarak seçemez
        if (isset($data['parent_id']) && $data['parent_id'] == $id) {
            return [
                'success' => false,
                'message' => 'Kategori kendisini üst kategori olarak seçemez.'
            ];
        }

        try {
            $updated = $this->categoryRepository->update($id, $data);
            if ($updated) {
                $category = $this->categoryRepository->find($id);
                return [
                    'success' => true,
                    'data' => $category
                ];
            }
            return [
                'success' => false,
                'message' => 'Kategori bulunamadı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kategori güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function deleteCategory(int $id): array
    {
        try {
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Kategori bulunamadı'
                ];
            }

            // Alt kategorileri var mı kontrol et
            if ($this->categoryRepository->checkCategoryHasChildren($id)) {
                return [
                    'success' => false,
                    'message' => 'Bu kategorinin alt kategorileri bulunmaktadır. Önce onları silmelisiniz.'
                ];
            }

            // Ürünleri var mı kontrol et
            if ($this->categoryRepository->checkCategoryHasProducts($id)) {
                return [
                    'success' => false,
                    'message' => 'Bu kategoride ürünler bulunmaktadır. Önce ürünleri silmelisiniz.'
                ];
            }

            $deleted = $this->categoryRepository->delete($id);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Kategori başarıyla silindi' : 'Kategori silinemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kategori silinirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function getActiveCategories(): Collection
    {
        return $this->categoryRepository->getActiveCategories();
    }

    public function getParentCategories(): Collection
    {
        return $this->categoryRepository->getParentCategories();
    }

    public function getChildCategories(int $parentId): Collection
    {
        return $this->categoryRepository->getChildCategories($parentId);
    }

    public function getCategoriesWithProducts(): Collection
    {
        return $this->categoryRepository->getCategoriesWithProducts();
    }

    public function getCategoryHierarchy(): Collection
    {
        return $this->categoryRepository->getCategoryHierarchy();
    }

    public function getCategoriesByLevel(int $level): Collection
    {
        return $this->categoryRepository->getCategoriesByLevel($level);
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        // Bu metod ProductService'de implement edilmeli
        // Şimdilik boş collection döndürüyoruz
        return collect();
    }
}
