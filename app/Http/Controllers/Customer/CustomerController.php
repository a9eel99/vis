<?php

namespace App\Http\Controllers\Customer;

use App\Application\Services\CustomerService;
use App\Domain\Models\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        $customers = $this->customerService->filter(
            search: $request->get('search'),
            perPage: 20
        );
        $lang = app()->getLocale();

        return view('customers.index', compact('customers', 'lang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:500',
            'notes'     => 'nullable|string|max:1000',
        ]);

        try {
            $this->customerService->create($data);

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
        $customer = $this->customerService->find($id);

        $inspections = $this->customerService->getInspections($id);

        $lang = app()->getLocale();

        return view('customers.show', compact('customer', 'inspections', 'lang'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:500',
            'notes'     => 'nullable|string|max:1000',
        ]);

        try {
            $customer = $this->customerService->update($id, $data);

            $lang = app()->getLocale();
            return redirect()->route('customers.show', $customer)
                ->with('success', $lang === 'ar' ? 'تم تحديث بيانات العميل' : 'Customer updated');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء التحديث.' : 'Error updating customer.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->customerService->delete($id);

            $lang = app()->getLocale();
            return redirect()->route('customers.index')
                ->with('success', $lang === 'ar' ? 'تم حذف العميل' : 'Customer deleted');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف العميل.' : 'Error deleting customer.');
        }
    }

    public function linkVehicle(Request $request, string $customerId)
    {
        $request->validate(['vehicle_id' => 'required|exists:vehicles,id']);

        try {
            $this->customerService->linkVehicle($customerId, $request->vehicle_id);

            $lang = app()->getLocale();
            return redirect()->route('customers.show', $customerId)
                ->with('success', $lang === 'ar' ? 'تم ربط المركبة بالعميل' : 'Vehicle linked to customer');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء ربط المركبة.' : 'Error linking vehicle.');
        }
    }

    public function unlinkVehicle(string $customerId, string $vehicleId)
    {
        try {
            $this->customerService->unlinkVehicle($customerId, $vehicleId);

            $lang = app()->getLocale();
            return redirect()->route('customers.show', $customerId)
                ->with('success', $lang === 'ar' ? 'تم فك ربط المركبة' : 'Vehicle unlinked');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ.' : 'Error unlinking vehicle.');
        }
    }
}