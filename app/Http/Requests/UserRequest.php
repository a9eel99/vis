<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ];

        if ($isUpdate) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        } else {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'name.required' => $isAr ? 'الاسم مطلوب.' : 'Name is required.',
            'email.required' => $isAr ? 'البريد الإلكتروني مطلوب.' : 'Email is required.',
            'email.email' => $isAr ? 'البريد الإلكتروني غير صالح.' : 'Invalid email address.',
            'email.unique' => $isAr ? 'البريد الإلكتروني مستخدم مسبقاً.' : 'Email already taken.',
            'password.required' => $isAr ? 'كلمة المرور مطلوبة.' : 'Password is required.',
            'password.min' => $isAr ? 'كلمة المرور 8 أحرف على الأقل.' : 'Password must be at least 8 characters.',
            'password.confirmed' => $isAr ? 'كلمة المرور غير متطابقة.' : 'Passwords do not match.',
            'role.required' => $isAr ? 'الدور مطلوب.' : 'Role is required.',
            'role.exists' => $isAr ? 'الدور المختار غير موجود.' : 'Selected role not found.',
        ];
    }
}