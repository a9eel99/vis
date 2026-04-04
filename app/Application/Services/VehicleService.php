<?php

namespace App\Application\Services;

use App\Domain\DTOs\VehicleDTO;
use App\Domain\Models\AuditLog;
use App\Domain\Models\Vehicle;
use App\Domain\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class VehicleService
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository
    ) {}

    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        if ($search) {
            return $this->vehicleRepository->search($search, $perPage);
        }

        return $this->vehicleRepository->paginate($perPage);
    }

    public function filter(?string $search = null, ?string $fuelType = null, string $sort = 'latest', int $perPage = 20): LengthAwarePaginator
    {
        $query = Vehicle::withCount('inspections');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($fuelType) {
            $query->where('fuel_type', $fuelType);
        }

        match ($sort) {
            'name' => $query->orderBy('make')->orderBy('model'),
            'mileage' => $query->orderByDesc('mileage'),
            'inspections' => $query->orderByDesc('inspections_count'),
            default => $query->orderByDesc('created_at'),
        };

        return $query->paginate($perPage);
    }

    public function find(string $id): Vehicle
    {
        return $this->vehicleRepository->findOrFail($id, ['customer', 'inspections.inspector', 'inspections.template']);
    }

    public function create(VehicleDTO $dto): Vehicle
    {
        $data = $dto->toArray();
        $data['created_by'] = auth()->id();

        $vehicle = $this->vehicleRepository->create($data);

        AuditLog::log('vehicle_created', Vehicle::class, $vehicle->id, null, $data);

        return $vehicle;
    }

    public function update(string $id, VehicleDTO $dto): Vehicle
    {
        $old = $this->vehicleRepository->find($id);
        $vehicle = $this->vehicleRepository->update($id, $dto->toArray());

        AuditLog::log('vehicle_updated', Vehicle::class, $id, $old?->toArray(), $dto->toArray());

        return $vehicle;
    }

    public function delete(string $id): bool
    {
        $vehicle = $this->vehicleRepository->findOrFail($id);

        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        AuditLog::log('vehicle_deleted', Vehicle::class, $id);
        return $this->vehicleRepository->delete($id);
    }

    public function uploadImage(string $id, $image): Vehicle
    {
        $vehicle = $this->vehicleRepository->findOrFail($id);

        // Delete old image
        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $path = $image->store('vehicles', 'public');

        return $this->vehicleRepository->update($id, ['image' => $path]);
    }

    public function getHistory(string $id)
    {
        return $this->vehicleRepository->getWithInspections($id);
    }
}