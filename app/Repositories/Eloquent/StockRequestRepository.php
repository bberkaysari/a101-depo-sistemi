<?php

namespace App\Repositories\Eloquent;

use App\Models\StockRequest;
use App\Repositories\Interfaces\StockRequestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StockRequestRepository implements StockRequestRepositoryInterface
{
    protected $model;

    public function __construct(StockRequest $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])->get();
    }

    public function find(int $id): ?StockRequest
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy', 'respondedBy'])->find($id);
    }

    public function create(array $data): StockRequest
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $stockRequest = $this->model->find($id);
        if (!$stockRequest) {
            return false;
        }
        return $stockRequest->update($data);
    }

    public function delete(int $id): bool
    {
        $stockRequest = $this->model->find($id);
        if (!$stockRequest) {
            return false;
        }
        return $stockRequest->delete();
    }

    public function getRequestsByStatus(string $status): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])
            ->where('status', $status)
            ->get();
    }

    public function getRequestsByLocation(int $locationId, string $type = 'to'): Collection
    {
        $field = $type === 'from' ? 'from_location_id' : 'to_location_id';
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])
            ->where($field, $locationId)
            ->get();
    }

    public function getRequestsByUser(int $userId): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation'])
            ->where('requested_by', $userId)
            ->latest()
            ->get();
    }

    public function getRequestsWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_location_id'])) {
            $query->where('from_location_id', $filters['from_location_id']);
        }

        if (isset($filters['to_location_id'])) {
            $query->where('to_location_id', $filters['to_location_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 20);
    }

    public function getPendingRequests(): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])
            ->where('status', 'pending')
            ->get();
    }

    public function getApprovedRequests(): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])
            ->where('status', 'approved')
            ->get();
    }

    public function getRejectedRequests(): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])
            ->where('status', 'rejected')
            ->get();
    }

    public function getRequestsByProduct(int $productId): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'toLocation', 'requestedBy'])
            ->where('product_id', $productId)
            ->get();
    }

    public function getIncomingRequests(int $locationId): Collection
    {
        return $this->model->with(['product.category', 'fromLocation', 'requestedBy'])
            ->where('to_location_id', $locationId)
            ->latest()
            ->get();
    }

    public function getOutgoingRequests(int $locationId): Collection
    {
        return $this->model->with(['product.category', 'toLocation'])
            ->where('from_location_id', $locationId)
            ->latest()
            ->get();
    }
}
