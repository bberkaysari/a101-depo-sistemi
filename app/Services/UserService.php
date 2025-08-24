<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'location_id' => 'required|exists:locations,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $data['password'] = Hash::make($data['password']);
            $user = $this->userRepository->create($data);
            return [
                'success' => true,
                'data' => $user
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kullanıcı oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateUser(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'location_id' => 'sometimes|required|exists:locations,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $updated = $this->userRepository->update($id, $data);
            if ($updated) {
                $user = $this->userRepository->find($id);
                return [
                    'success' => true,
                    'data' => $user
                ];
            }
            return [
                'success' => false,
                'message' => 'Kullanıcı bulunamadı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kullanıcı güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function deleteUser(int $id): array
    {
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ];
            }

            $deleted = $this->userRepository->delete($id);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Kullanıcı başarıyla silindi' : 'Kullanıcı silinemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kullanıcı silinirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getUsersByLocation(int $locationId): Collection
    {
        return $this->userRepository->getUsersByLocation($locationId);
    }

    public function getActiveUsers(): Collection
    {
        return $this->userRepository->getActiveUsers();
    }

    public function updateLocation(int $userId, int $locationId): array
    {
        try {
            $updated = $this->userRepository->updateLocation($userId, $locationId);
            if ($updated) {
                return [
                    'success' => true,
                    'message' => 'Kullanıcı lokasyonu başarıyla güncellendi'
                ];
            }
            return [
                'success' => false,
                'message' => 'Kullanıcı bulunamadı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kullanıcı lokasyonu güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }
}
