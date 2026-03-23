<?php

namespace App\Http\Controllers\Customer;

use App\Domain\Models\Customer;
use App\Domain\Models\AuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['vehicles', 'inspections']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20);
        $lang = app()->getLocale();

        return view('customers.index', compact('customers', 'lang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $data['created_by'] = auth()->id();
            $customer = Customer::create($data);

            AuditLog::log('customer_created', Customer::class, $customer->id, null, $data);

            $lang = app()->getLocale();
            return redirect()->route('customers.index')
                ->with('success', $lang === 'ar' ? 'تم إضافة العميل بنجاح' : 'Customer created successfully');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إضافة العميل.' : 'Error creating customer.');
        }
    }

    public function show(string $id)
    {
        $customer = Customer::with([
            'vehicles.inspections' => fn($q) => $q->latest()->limit(5),
            'vehicles.latestInspection',
        ])->withCount(['vehicles', 'inspections'])->findOrFail($id);

        $inspections = $customer->inspections()
            ->with(['vehicle', 'inspector', 'template'])
            ->latest()
            ->paginate(10);

        $lang = app()->getLocale();

        return view('customers.show', compact('customer', 'inspections', 'lang'));
    }

    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $customer->update($data);

            $lang = app()->getLocale();
            return redirect()->route('customers.show', $customer)
                ->with('success', $lang === 'ar' ? 'تم تحديث بيانات العميل' : 'Customer updated');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء التحديث.' : 'Error updating customer.');
        }
    }

    public function linkVehicle(Request $request, string $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $request->validate(['vehicle_id' => 'required|exists:vehicles,id']);

        try {
            $vehicle = \App\Domain\Models\Vehicle::findOrFail($request->vehicle_id);
            $vehicle->update([
                'customer_id' => $customer->id,
                'owner_name' => $vehicle->owner_name ?: $customer->name,
                'owner_phone' => $vehicle->owner_phone ?: $customer->phone,
                'owner_email' => $vehicle->owner_email ?: $customer->email,
            ]);

            $lang = app()->getLocale();
            return redirect()->route('customers.show', $customer)
                ->with('success', $lang === 'ar' ? 'تم ربط المركبة بالعميل' : 'Vehicle linked to customer');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء ربط المركبة.' : 'Error linking vehicle.');
        }
    }

    public function unlinkVehicle(string $customerId, string $vehicleId)
    {
        $vehicle = \App\Domain\Models\Vehicle::where('customer_id', $customerId)->findOrFail($vehicleId);
        $vehicle->update(['customer_id' => null]);

        $lang = app()->getLocale();
        return redirect()->route('customers.show', $customerId)
            ->with('success', $lang === 'ar' ? 'تم فك ربط المركبة' : 'Vehicle unlinked');
    }

    public function destroy(string $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $customer = Customer::findOrFail($id);
                $customer->vehicles()->update(['customer_id' => null]);
                $customer->delete();

                $lang = app()->getLocale();
                return redirect()->route('customers.index')
                    ->with('success', $lang === 'ar' ? 'تم حذف العميل' : 'Customer deleted');
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف العميل.' : 'Error deleting customer.');
        }
    }
}