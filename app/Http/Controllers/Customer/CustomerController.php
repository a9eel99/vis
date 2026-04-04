<?php

namespace App\Http\Controllers\Customer;

use App\Application\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
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

    public function store(CustomerRequest $request)
    {
        try {
            $this->customerService->create($request->validated());
            return redirect()->route('customers.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة العميل بنجاح' : 'Customer created successfully');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إضافة العميل.' : 'Error creating customer.');
        }
    }

    public function show(string $id)
    {
        $customer    = $this->customerService->find($id);
        $inspections = $this->customerService->getInspections($id);
        $lang        = app()->getLocale();
        return view('customers.show', compact('customer', 'inspections', 'lang'));
    }

    public function update(CustomerRequest $request, string $id)
    {
        try {
            $customer = $this->customerService->update($id, $request->validated());
            return redirect()->route('customers.show', $customer)
                ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث بيانات العميل' : 'Customer updated');
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
            return redirect()->route('customers.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف العميل' : 'Customer deleted');
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
            return redirect()->route('customers.show', $customerId)
                ->with('success', app()->getLocale() === 'ar' ? 'تم ربط المركبة بالعميل' : 'Vehicle linked to customer');
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
            return redirect()->route('customers.show', $customerId)
                ->with('success', app()->getLocale() === 'ar' ? 'تم فك ربط المركبة' : 'Vehicle unlinked');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ.' : 'Error unlinking vehicle.');
        }
    }
}