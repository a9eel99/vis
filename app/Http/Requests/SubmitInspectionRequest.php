<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array',
            'answers.*' => 'nullable',
            'answers.*.answer' => 'nullable|string|max:5000',
            'answers.*.remarks' => 'nullable|string|max:1000',
            'answers.*.remarks_score' => 'nullable|integer|min:0|max:100',
            'answers.*.score' => 'nullable|numeric|min:0|max:100',
            'media' => 'nullable|array',
            'media.*' => 'nullable|array',
            'media.*.*' => 'file|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'answers.required' => $isAr ? 'يرجى الإجابة على الأسئلة.' : 'Please answer the questions.',
            'answers.*.remarks.max' => $isAr ? 'الملاحظات طويلة جداً (حد أقصى 1000 حرف).' : 'Remarks too long (max 1000).',
            'media.*.*.max' => $isAr ? 'حجم الصورة لا يتجاوز 10MB.' : 'Image must not exceed 10MB.',
            'media.*.*.mimes' => $isAr ? 'الملف يجب أن يكون صورة (JPG, PNG, WebP).' : 'File must be an image (JPG, PNG, WebP).',
            'media.*.max' => $isAr ? 'الحد الأقصى 10 صور لكل سؤال.' : 'Max 10 images per question.',
        ];
    }
}