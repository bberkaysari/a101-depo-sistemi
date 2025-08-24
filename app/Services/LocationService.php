<?php

namespace App\Services;

use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class LocationService
{
    protected $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function getAllLocations(): Collection
    {
        return $this->locationRepository->all();
    }

    public function getLocationById(int $id): ?Location
    {
        return $this->locationRepository->find($id);
    }

    public function createLocation(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'required|in:warehouse,store,branch',
            'address' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:locations,id',
            'level' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $location = $this->locationRepository->create($data);
            return [
                'success' => true,
                'data' => $location
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lokasyon oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateLocation(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:warehouse,store,branch',
            'address' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:locations,id',
            'level' => 'sometimes|required|integer|min:0',
            'is_active' => 'boolean'
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
                'message' => 'Lokasyon kendisini üst lokasyon olarak seçemez'
            ];
        }

        try {
            $updated = $this->locationRepository->update($id, $data);
            if ($updated) {
                $location = $this->locationRepository->find($id);
                return [
                    'success' => true,
                    'data' => $location
                ];
            }
            return [
                'success' => false,
                'message' => 'Lokasyon bulunamadı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lokasyon güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function deleteLocation(int $id): array
    {
        try {
            $location = $this->locationRepository->find($id);
            if (!$location) {
                return [
                    'success' => false,
                    'message' => 'Lokasyon bulunamadı'
                ];
            }

            // Alt lokasyonları var mı kontrol et
            if ($location->children()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Bu lokasyonun alt lokasyonları bulunmaktadır. Önce onları silmelisiniz.'
                ];
            }

            // Stokları var mı kontrol et
            if ($location->stocks()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Bu lokasyonda stok bulunmaktadır. Önce stokları silmelisiniz.'
                ];
            }

            $deleted = $this->locationRepository->delete($id);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Lokasyon başarıyla silindi' : 'Lokasyon silinemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lokasyon silinirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function getWarehouses(): Collection
    {
        return $this->locationRepository->getWarehouses();
    }

    public function getStores(): Collection
    {
        return $this->locationRepository->getStores();
    }

    public function getBranches(): Collection
    {
        return $this->locationRepository->getBranches();
    }

    public function getLocationHierarchy(): Collection
    {
        return $this->locationRepository->getHierarchy();
    }

    public function getChildrenLocations(int $parentId): Collection
    {
        return $this->locationRepository->getChildren($parentId);
    }

    public function getActiveLocations(): Collection
    {
        return $this->locationRepository->getActiveLocations();
    }

    public function getParentLocations(?int $excludeId = null): Collection
    {
        return $this->locationRepository->getParentLocations($excludeId);
    }
}
