<?php

namespace App\Repositories\Eloquent;

use App\Models\Location;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository implements LocationRepositoryInterface
{
    protected $model;

    public function __construct(Location $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['parent', 'children'])->get();
    }

    public function find(int $id): ?Location
    {
        return $this->model->with(['parent', 'children', 'stocks'])->find($id);
    }

    public function create(array $data): Location
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $location = $this->model->find($id);
        if (!$location) {
            return false;
        }
        return $location->update($data);
    }

    public function delete(int $id): bool
    {
        $location = $this->model->find($id);
        if (!$location) {
            return false;
        }
        return $location->delete();
    }

    public function getWarehouses(): Collection
    {
        return $this->model->warehouses()->active()->with('children')->get();
    }

    public function getStores(): Collection
    {
        return $this->model->stores()->active()->with('children')->get();
    }

    public function getBranches(): Collection
    {
        return $this->model->branches()->active()->with('children')->get();
    }

    public function getByLevel(string $level): Collection
    {
        return $this->model->where('level', $level)->active()->get();
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)->active()->get();
    }

    public function getParent(int $id): ?Location
    {
        $location = $this->model->find($id);
        return $location ? $location->parent : null;
    }

    public function getHierarchy(): Collection
    {
        return $this->model->whereNull('parent_id')
            ->with('allChildren')
            ->active()
            ->get();
    }

    public function getActiveLocations(): Collection
    {
        return $this->model->active()->with(['parent', 'children'])->get();
    }

    public function getParentLocations(?int $excludeId = null): Collection
    {
        $query = $this->model->active();
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->get();
    }
}
