<?php

namespace App\Domain\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InspectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getByVehicle(string $vehicleId): Collection;

    public function getByInspector(string $inspectorId, int $perPage = 15): LengthAwarePaginator;

    public function getCompleted(int $perPage = 15): LengthAwarePaginator;

    public function getRecentCompleted(int $limit = 10): Collection;

    public function getMonthlyStats(int $months = 12): Collection;

    public function getDashboardStats(): array;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    public function getWithFullDetails(string $id);
}
