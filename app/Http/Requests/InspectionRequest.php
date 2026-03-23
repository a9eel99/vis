<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isNew = $this->input('vehicle_mode') === 'new';

        $rules = [
            'template_id' => 'required|uuid|exists:inspection_templates,id',
            'inspector_id' => 'nullable|uuid|exists:users,id',
            'notes' => 'nullable|string|max:2000',
            'vehicle_mode' => 'nullable|in:existing,new',
        ];

        if ($isNew) {
            $rules['make'] = 'required|string|max:100';
            $rules['model'] = 'required|string|max:100';
            $rules['year'] = 'required|integer|min:1900|max:' . (date('Y') + 1);
            $rules['color'] = 'nullable|string|max:50';
            $rules['vin'] = 'nullable|string|max:17|unique:vehicles,vin';
            $rules['license_plate'] = 'nullable|string|max:20';
            $rules['mileage'] = 'nullable|integer|min:0';
            $rules['fuel_type'] = 'nullable|in:gasoline,diesel,electric,hybrid,lpg';
            $rules['customer_id'] = 'nullable|uuid|exists:customers,id';
            $rules['owner_name'] = 'nullable|string|max:255';
            $rules['owner_phone'] = 'nullable|string|max:20';
            $rules['owner_email'] = 'nullable|email|max:255';
        } else {
            $rules['vehicle_id'] = 'required|uuid|exists:vehicles,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'template_id.required' => $isAr ? 'يرجى اختيار قالب الفحص.' : 'Please select an inspection template.',
            'template_id.exists' => $isAr ? 'القالب المختار غير موجود.' : 'Selected template not found.',
            'vehicle_id.required' => $isAr ? 'يرجى اختيار المركبة.' : 'Please select a vehicle.',
            'vehicle_id.exists' => $isAr ? 'المركبة المختارة غير موجودة.' : 'Selected vehicle not found.',
            'make.required' => $isAr ? 'الشركة المصنّعة مطلوبة.' : 'Make is required.',
            'model.required' => $isAr ? 'الموديل مطلوب.' : 'Model is required.',
            'year.required' => $isAr ? 'سنة الصنع مطلوبة.' : 'Year is required.',
            'year.min' => $isAr ? 'سنة الصنع غير صالحة.' : 'Year is invalid.',
            'vin.unique' => $isAr ? 'رقم الشاسيه مسجّل مسبقاً لمركبة أخرى.' : 'This VIN is already registered.',
            'vin.max' => $isAr ? 'رقم الشاسيه لا يتجاوز 17 حرف.' : 'VIN must not exceed 17 characters.',
            'owner_email.email' => $isAr ? 'البريد الإلكتروني غير صالح.' : 'Invalid email address.',
        ];
    }
}