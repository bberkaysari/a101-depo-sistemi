<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Category;
    public function create(array $data): Category;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getActiveCategories(): Collection;
    public function getParentCategories(): Collection;
    public function getChildCategories(int $parentId): Collection;
    public function getCategoriesWithProducts(): Collection;
    public function getCategoryHierarchy(): Collection;
    public function getCategoriesByLevel(int $level): Collection;
    public function checkCategoryHasChildren(int $id): bool;
    public function checkCategoryHasProducts(int $id): bool;
}
