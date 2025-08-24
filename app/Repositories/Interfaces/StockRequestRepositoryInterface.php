<?php

namespace App\Repositories\Interfaces;

use App\Models\StockRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockRequestRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?StockRequest;
    public function create(array $data): StockRequest;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getRequestsByStatus(string $status): Collection;
    public function getRequestsByLocation(int $locationId, string $type = 'to'): Collection;
    public function getRequestsByUser(int $userId): Collection;
    public function getRequestsWithFilters(array $filters = []): LengthAwarePaginator;
    public function getPendingRequests(): Collection;
    public function getApprovedRequests(): Collection;
    public function getRejectedRequests(): Collection;
    public function getRequestsByProduct(int $productId): Collection;
    public function getIncomingRequests(int $locationId): Collection;
    public function getOutgoingRequests(int $locationId): Collection;
}
