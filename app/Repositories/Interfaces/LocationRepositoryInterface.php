<?php

namespace App\Repositories\Interfaces;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

interface LocationRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Location;
    public function create(array $data): Location;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getWarehouses(): Collection;
    public function getStores(): Collection;
    public function getBranches(): Collection;
    public function getByLevel(string $level): Collection;
    public function getChildren(int $parentId): Collection;
    public function getParent(int $id): ?Location;
    public function getHierarchy(): Collection;
    public function getActiveLocations(): Collection;
}
