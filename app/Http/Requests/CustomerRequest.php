<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:500',
            'notes'     => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'name.required' => $isAr ? 'اسم العميل مطلوب.' : 'Customer name is required.',
            'name.max'      => $isAr ? 'الاسم طويل جداً.'  : 'Name is too long.',
            'email.email'   => $isAr ? 'البريد الإلكتروني غير صالح.' : 'Invalid email address.',
            'phone.max'     => $isAr ? 'رقم الهاتف طويل جداً.' : 'Phone number is too long.',
        ];
    }
}