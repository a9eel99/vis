<?php

namespace App\Http\Controllers\Vehicle;

use App\Application\Services\VehicleService;
use App\Domain\DTOs\VehicleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService
    ) {}

    public function index(Request $request)
    {
        $query = \App\Domain\Models\Vehicle::withCount('inspections');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($fuel = $request->get('fuel_type')) {
            $query->where('fuel_type', $fuel);
        }

        switch ($request->get('sort', 'latest')) {
            case 'name': $query->orderBy('make')->orderBy('model'); break;
            case 'mileage': $query->orderByDesc('mileage'); break;
            case 'inspections': $query->orderByDesc('inspections_count'); break;
            default: $query->orderByDesc('created_at');
        }

        $vehicles = $query->paginate(20);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(VehicleRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $dto = VehicleDTO::fromArray($request->validated());
                $vehicle = $this->vehicleService->create($dto);

                // Link or create customer
                if ($request->filled('customer_id')) {
                    $vehicle->update(['customer_id' => $request->input('customer_id')]);
                } elseif ($request->filled('owner_name')) {
                    $customer = \App\Domain\Models\Customer::create([
                        'name' => $request->input('owner_name'),
                        'phone' => $request->input('owner_phone'),
                        'email' => $request->input('owner_email'),
                        'created_by' => auth()->id(),
                    ]);
                    $vehicle->update(['customer_id' => $customer->id]);
                }

                if ($request->hasFile('image')) {
                    $this->vehicleService->uploadImage($vehicle->id, $request->file('image'));
                }

                Cache::forget('dashboard_stats');

                return redirect()->route('vehicles.show', $vehicle)
                    ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة المركبة بنجاح.' : 'Vehicle created successfully.');
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إضافة المركبة.' : 'Error creating vehicle.');
        }
    }

    public function show(string $id)
    {
        $vehicle = $this->vehicleService->find($id);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(string $id)
    {
        $vehicle = $this->vehicleService->find($id);
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(VehicleRequest $request, string $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $dto = VehicleDTO::fromArray($request->validated());
                $this->vehicleService->update($id, $dto);
                $vehicle = $this->vehicleService->find($id);

                if ($request->filled('customer_id')) {
                    $vehicle->update(['customer_id' => $request->input('customer_id')]);
                } elseif ($request->filled('owner_name') && !$vehicle->customer_id) {
                    $customer = \App\Domain\Models\Customer::create([
                        'name' => $request->input('owner_name'),
                        'phone' => $request->input('owner_phone'),
                        'email' => $request->input('owner_email'),
                        'created_by' => auth()->id(),
                    ]);
                    $vehicle->update(['customer_id' => $customer->id]);
                } else {
                    $vehicle->update(['customer_id' => $request->input('customer_id') ?: null]);
                }

                if ($request->hasFile('image')) {
                    $this->vehicleService->uploadImage($id, $request->file('image'));
                }

                return redirect()->route('vehicles.show', $id)
                    ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث المركبة بنجاح.' : 'Vehicle updated successfully.');
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء تحديث المركبة.' : 'Error updating vehicle.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->vehicleService->delete($id);
            Cache::forget('dashboard_stats');

            return redirect()->route('vehicles.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف المركبة بنجاح.' : 'Vehicle deleted successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'لا يمكن حذف مركبة لها فحوصات.' : 'Cannot delete vehicle with inspections.');
        }
    }
}