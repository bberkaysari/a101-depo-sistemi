<?php

namespace App\Services;

use App\Repositories\Interfaces\StockRequestRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\Interfaces\StockRepositoryInterface;
use App\Models\StockRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class StockRequestService
{
    protected $stockRequestRepository;
    protected $productRepository;
    protected $locationRepository;
    protected $stockRepository;

    public function __construct(
        StockRequestRepositoryInterface $stockRequestRepository,
        ProductRepositoryInterface $productRepository,
        LocationRepositoryInterface $locationRepository,
        StockRepositoryInterface $stockRepository
    ) {
        $this->stockRequestRepository = $stockRequestRepository;
        $this->productRepository = $productRepository;
        $this->locationRepository = $locationRepository;
        $this->stockRepository = $stockRepository;
    }

    public function getAllStockRequests(): Collection
    {
        return $this->stockRequestRepository->all();
    }

    public function getStockRequestById(int $id): ?StockRequest
    {
        return $this->stockRequestRepository->find($id);
    }

    public function createStockRequest(array $data): array
    {
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id',
            'requested_quantity' => 'required|integer|min:1',
            'request_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Kullanıcının kendi lokasyonundan başka bir lokasyona istek yapabilmesi için
        $currentUserLocation = Auth::user()->location_id;
        
        // Eğer kullanıcı kendi lokasyonundan istek yapmaya çalışıyorsa, hata ver
        if ($data['from_location_id'] == $currentUserLocation) {
            return [
                'success' => false,
                'message' => 'Kendi lokasyonunuzdan stok isteği yapamazsınız.'
            ];
        }
        
        // Alıcı lokasyon olarak kullanıcının kendi lokasyonu otomatik seçilir
        $data['to_location_id'] = $currentUserLocation;

        // Aynı lokasyondan aynı lokasyona istek yapılamaz
        if ($data['from_location_id'] == $data['to_location_id']) {
            return [
                'success' => false,
                'message' => 'Aynı lokasyondan aynı lokasyona stok isteği yapılamaz.'
            ];
        }

        // Gönderen lokasyonda yeterli stok var mı kontrol et
        $fromStock = $this->stockRepository->checkStockExists(
            $data['product_id'], 
            $data['from_location_id']
        );

        if (!$fromStock || $fromStock->quantity < $data['requested_quantity']) {
            return [
                'success' => false,
                'message' => 'Gönderen lokasyonda yeterli stok bulunmamaktadır.'
            ];
        }

        // Giriş yapan kullanıcının ID'sini kullan
        $data['requested_by'] = Auth::id();

        try {
            $stockRequest = $this->stockRequestRepository->create($data);
            return [
                'success' => true,
                'data' => $stockRequest
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok isteği oluşturulurken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function updateStockRequest(int $id, array $data): array
    {
        $stockRequest = $this->stockRequestRepository->find($id);
        if (!$stockRequest) {
            return [
                'success' => false,
                'message' => 'Stok isteği bulunamadı'
            ];
        }

        // Sadece bekleyen istekler güncellenebilir
        if (!$stockRequest->isPending()) {
            return [
                'success' => false,
                'message' => 'Sadece bekleyen istekler güncellenebilir.'
            ];
        }

        $validator = Validator::make($data, [
            'product_id' => 'sometimes|required|exists:products,id',
            'from_location_id' => 'sometimes|required|exists:locations,id',
            'to_location_id' => 'sometimes|required|exists:locations,id',
            'requested_quantity' => 'sometimes|required|integer|min:1',
            'request_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Aynı lokasyondan aynı lokasyona istek yapılamaz
        if (isset($data['from_location_id']) && isset($data['to_location_id']) && 
            $data['from_location_id'] == $data['to_location_id']) {
            return [
                'success' => false,
                'message' => 'Aynı lokasyondan aynı lokasyona stok isteği yapılamaz.'
            ];
        }

        try {
            $updated = $this->stockRequestRepository->update($id, $data);
            if ($updated) {
                $stockRequest = $this->stockRequestRepository->find($id);
                return [
                    'success' => true,
                    'data' => $stockRequest
                ];
            }
            return [
                'success' => false,
                'message' => 'Stok isteği güncellenemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok isteği güncellenirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function deleteStockRequest(int $id): array
    {
        $stockRequest = $this->stockRequestRepository->find($id);
        if (!$stockRequest) {
            return [
                'success' => false,
                'message' => 'Stok isteği bulunamadı'
            ];
        }

        // Sadece bekleyen istekler silinebilir
        if (!$stockRequest->isPending()) {
            return [
                'success' => false,
                'message' => 'Sadece bekleyen istekler silinebilir.'
            ];
        }

        // Sadece isteği oluşturan mağaza silebilir
        if ($stockRequest->from_location_id != 4) { // Hürriyet Şubesi (ID: 4) - mülakat için sabit
            return [
                'success' => false,
                'message' => 'Bu isteği silme yetkiniz bulunmamaktadır.'
            ];
        }

        try {
            $deleted = $this->stockRequestRepository->delete($id);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Stok isteği başarıyla silindi' : 'Stok isteği silinemedi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok isteği silinirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function approveStockRequest(int $id, array $data): array
    {
        $stockRequest = $this->stockRequestRepository->find($id);
        if (!$stockRequest) {
            return [
                'success' => false,
                'message' => 'Stok isteği bulunamadı'
            ];
        }

        // Sadece bekleyen istekler onaylanabilir
        if (!$stockRequest->isPending()) {
            return [
                'success' => false,
                'message' => 'Bu istek zaten onaylanmış veya reddedilmiş.'
            ];
        }

        // Sadece isteği alan mağaza onaylayabilir
        $currentUserLocation = Auth::user()->location_id;
        
        if ($stockRequest->to_location_id != $currentUserLocation) {
            return [
                'success' => false,
                'message' => 'Bu isteği onaylama yetkiniz bulunmamaktadır. Sadece kendi lokasyonunuza gelen istekleri onaylayabilirsiniz.'
            ];
        }
        
        if ($stockRequest->from_location_id == $currentUserLocation) {
            return [
                'success' => false,
                'message' => 'Kendi gönderdiğiniz isteği onaylayamazsınız. Sadece size gelen istekleri onaylayabilirsiniz.'
            ];
        }

        $validator = Validator::make($data, [
            'approved_quantity' => 'required|integer|min:1|max:' . $stockRequest->requested_quantity,
            'response_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        // Gönderen lokasyonda yeterli stok var mı kontrol et
        $fromStock = $this->stockRepository->checkStockExists(
            $stockRequest->product_id, 
            $stockRequest->from_location_id
        );

        if (!$fromStock || $fromStock->quantity < $data['approved_quantity']) {
            return [
                'success' => false,
                'message' => 'Gönderen lokasyonda yeterli stok bulunmamaktadır.'
            ];
        }

        try {
            $stockRequest->approve(
                $data['approved_quantity'],
                $data['response_notes'],
                Auth::id()
            );

            return [
                'success' => true,
                'message' => 'Stok isteği başarıyla onaylandı ve transfer gerçekleştirildi.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok isteği onaylanırken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function rejectStockRequest(int $id, array $data): array
    {
        $stockRequest = $this->stockRequestRepository->find($id);
        if (!$stockRequest) {
            return [
                'success' => false,
                'message' => 'Stok isteği bulunamadı'
            ];
        }

        // Sadece bekleyen istekler reddedilebilir
        if (!$stockRequest->isPending()) {
            return [
                'success' => false,
                'message' => 'Bu istek zaten onaylanmış veya reddedilmiş.'
            ];
        }

        // Sadece isteği alan mağaza reddedebilir
        $currentUserLocation = Auth::user()->location_id;
        
        if ($stockRequest->to_location_id != $currentUserLocation) {
            return [
                'success' => false,
                'message' => 'Bu isteği reddetme yetkiniz bulunmamaktadır. Sadece kendi lokasyonunuza gelen istekleri reddedebilirsiniz.'
            ];
        }
        
        if ($stockRequest->from_location_id == $currentUserLocation) {
            return [
                'success' => false,
                'message' => 'Kendi gönderdiğiniz isteği reddedemezsiniz. Sadece size gelen istekleri reddedebilirsiniz.'
            ];
        }

        $validator = Validator::make($data, [
            'response_notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        try {
            $stockRequest->reject(
                $data['response_notes'],
                Auth::id()
            );

            return [
                'success' => true,
                'message' => 'Stok isteği reddedildi.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stok isteği reddedilirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function getRequestsByStatus(string $status): Collection
    {
        return $this->stockRequestRepository->getRequestsByStatus($status);
    }

    public function getRequestsByLocation(int $locationId, string $type = 'to'): Collection
    {
        return $this->stockRequestRepository->getRequestsByLocation($locationId, $type);
    }

    public function getRequestsByUser(int $userId): Collection
    {
        return $this->stockRequestRepository->getRequestsByUser($userId);
    }

    public function getRequestsWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->stockRequestRepository->getRequestsWithFilters($filters);
    }

    public function getPendingRequests(): Collection
    {
        return $this->stockRequestRepository->getPendingRequests();
    }

    public function getApprovedRequests(): Collection
    {
        return $this->stockRequestRepository->getApprovedRequests();
    }

    public function getRejectedRequests(): Collection
    {
        return $this->stockRequestRepository->getRejectedRequests();
    }

    public function getRequestsByProduct(int $productId): Collection
    {
        return $this->stockRequestRepository->getRequestsByProduct($productId);
    }

    public function getIncomingRequests(int $locationId): Collection
    {
        return $this->stockRequestRepository->getIncomingRequests($locationId);
    }

    public function getOutgoingRequests(int $locationId): Collection
    {
        return $this->stockRequestRepository->getOutgoingRequests($locationId);
    }
}
