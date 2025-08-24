<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByEmail(string $email): ?User;
    public function getUsersByLocation(int $locationId): Collection;
    public function getActiveUsers(): Collection;
    public function updateLocation(int $userId, int $locationId): bool;
}
