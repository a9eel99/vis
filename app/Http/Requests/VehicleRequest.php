<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');

        return [
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => [
                'nullable',
                'string',
                'max:17',
                \Illuminate\Validation\Rule::unique('vehicles', 'vin')
                    ->ignore($vehicleId)
                    ->whereNull('deleted_at'),
            ],
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|in:gasoline,diesel,electric,hybrid,lpg',
            'transmission' => 'nullable|string|in:automatic,manual,cvt,other',
            'owner_name' => 'nullable|string|max:255',
            'owner_phone' => 'nullable|string|max:20',
            'owner_email' => 'nullable|email|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'make.required' => $isAr ? 'الشركة المصنّعة مطلوبة.' : 'Vehicle make is required.',
            'model.required' => $isAr ? 'الموديل مطلوب.' : 'Vehicle model is required.',
            'year.required' => $isAr ? 'سنة الصنع مطلوبة.' : 'Vehicle year is required.',
            'year.min' => $isAr ? 'سنة الصنع غير صالحة.' : 'Year must be 1900 or later.',
            'year.max' => $isAr ? 'سنة الصنع غير صالحة.' : 'Year is too far in the future.',
            'vin.unique' => $isAr ? 'رقم الشاسيه مسجّل مسبقاً لمركبة أخرى.' : 'This VIN is already registered.',
            'vin.max' => $isAr ? 'رقم الشاسيه لا يتجاوز 17 حرف.' : 'VIN must not exceed 17 characters.',
            'owner_email.email' => $isAr ? 'البريد الإلكتروني غير صالح.' : 'Invalid email address.',
            'image.max' => $isAr ? 'حجم الصورة لا يتجاوز 5MB.' : 'Image must not exceed 5MB.',
            'mileage.min' => $isAr ? 'الكيلومتر لا يمكن أن يكون سالباً.' : 'Mileage cannot be negative.',
        ];
    }
}