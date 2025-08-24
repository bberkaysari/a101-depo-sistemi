<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['parent', 'children', 'products'])->get();
    }

    public function find(int $id): ?Category
    {
        return $this->model->with(['parent', 'children', 'products'])->find($id);
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->model->find($id);
        if (!$category) {
            return false;
        }
        return $category->update($data);
    }

    public function delete(int $id): bool
    {
        $category = $this->model->find($id);
        if (!$category) {
            return false;
        }
        return $category->delete();
    }

    public function getActiveCategories(): Collection
    {
        return $this->model->active()->with(['parent', 'children', 'products'])->orderBy('name')->get();
    }

    public function getParentCategories(): Collection
    {
        return $this->model->active()->whereNull('parent_id')->get();
    }

    public function getChildCategories(int $parentId): Collection
    {
        return $this->model->active()->where('parent_id', $parentId)->get();
    }

    public function getCategoriesWithProducts(): Collection
    {
        return $this->model->active()->with(['parent', 'children', 'products'])->get();
    }

    public function getCategoryHierarchy(): Collection
    {
        return $this->model->whereNull('parent_id')
            ->with('allChildren')
            ->active()
            ->get();
    }

    public function getCategoriesByLevel(int $level): Collection
    {
        return $this->model->where('level', $level)->active()->get();
    }

    public function checkCategoryHasChildren(int $id): bool
    {
        $category = $this->model->find($id);
        return $category ? $category->children()->count() > 0 : false;
    }

    public function checkCategoryHasProducts(int $id): bool
    {
        $category = $this->model->find($id);
        return $category ? $category->products()->count() > 0 : false;
    }
}
