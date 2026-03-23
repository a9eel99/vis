<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Vehicle;
use App\Domain\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model
            ->withCount('inspections')
            ->with('latestInspection')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->withCount('inspections')
            ->where(function ($q) use ($query) {
                $q->where('make', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhere('vin', 'like', "%{$query}%")
                  ->orWhere('license_plate', 'like', "%{$query}%")
                  ->orWhere('owner_name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getWithInspections(string $id)
    {
        return $this->model
            ->with(['inspections' => function ($q) {
                $q->with(['inspector', 'template'])
                  ->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);
    }

    public function getByOwner(string $ownerEmail): Collection
    {
        return $this->model
            ->where('owner_email', $ownerEmail)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
