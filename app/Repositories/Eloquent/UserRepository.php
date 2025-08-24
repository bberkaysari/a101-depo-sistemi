<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('location')->get();
    }

    public function find(int $id): ?User
    {
        return $this->model->with('location')->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->model->find($id);
        if (!$user) {
            return false;
        }
        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->model->find($id);
        if (!$user) {
            return false;
        }
        return $user->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function getUsersByLocation(int $locationId): Collection
    {
        return $this->model->with('location')
            ->where('location_id', $locationId)
            ->get();
    }

    public function getActiveUsers(): Collection
    {
        return $this->model->with('location')
            ->where('is_active', true)
            ->get();
    }

    public function updateLocation(int $userId, int $locationId): bool
    {
        $user = $this->model->find($userId);
        if (!$user) {
            return false;
        }
        return $user->update(['location_id' => $locationId]);
    }
}
