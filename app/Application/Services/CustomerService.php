<?php

namespace App\Application\Services;

use App\Domain\Models\AuditLog;
use App\Domain\Models\Customer;
use App\Domain\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function filter(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Customer::withCount(['vehicles', 'inspections']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(string $id): Customer
    {
        return Customer::with(['vehicles.inspections', 'vehicles.latestInspection'])
            ->withCount(['vehicles', 'inspections'])
            ->findOrFail($id);
    }

    public function getInspections(string $customerId, int $perPage = 10)
    {
        $customer = Customer::findOrFail($customerId);
        return $customer->inspections()
            ->with(['vehicle', 'inspector', 'template'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Customer
    {
        $data['created_by'] = auth()->id();
        $customer = Customer::create($data);

        AuditLog::log('customer_created', Customer::class, $customer->id, null, $data);

        return $customer;
    }

    public function update(string $id, array $data): Customer
    {
        $customer = Customer::findOrFail($id);
        $old = $customer->toArray();
        $customer->update($data);

        AuditLog::log('customer_updated', Customer::class, $id, $old, $data);

        return $customer->fresh();
    }

    public function delete(string $id): bool
    {
        $customer = Customer::findOrFail($id);
        // Unlink vehicles first
        $customer->vehicles()->update(['customer_id' => null]);

        AuditLog::log('customer_deleted', Customer::class, $id);

        return $customer->delete();
    }

    public function linkVehicle(string $customerId, string $vehicleId): Vehicle
    {
        $customer = Customer::findOrFail($customerId);
        $vehicle = Vehicle::findOrFail($vehicleId);
        $vehicle->update(['customer_id' => $customer->id]);

        AuditLog::log('vehicle_linked_to_customer', Customer::class, $customerId, null, [
            'vehicle_id' => $vehicleId,
        ]);

        return $vehicle->fresh();
    }

    public function unlinkVehicle(string $customerId, string $vehicleId): Vehicle
    {
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $vehicle->update(['customer_id' => null]);

        AuditLog::log('vehicle_unlinked_from_customer', Customer::class, $customerId, null, [
            'vehicle_id' => $vehicleId,
        ]);

        return $vehicle->fresh();
    }
}