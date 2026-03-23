<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'scoring_mode' => 'nullable|in:scored,descriptive',
            'fuel_type' => 'nullable|string|in:gasoline,diesel,electric,hybrid,lpg',
            'price' => 'nullable|numeric|min:0|max:9999',
            'sections' => 'nullable|array',
            'sections.*.name' => 'required_with:sections|string|max:255',
            'sections.*.description' => 'nullable|string|max:1000',
            'sections.*.questions' => 'nullable|array',
            'sections.*.questions.*.label' => 'required_with:sections.*.questions|string|max:255',
            'sections.*.questions.*.type' => 'required_with:sections.*.questions|in:text,number,checkbox,dropdown,photo',
            'sections.*.questions.*.weight' => 'nullable|numeric|min:0|max:100',
            'sections.*.questions.*.max_score' => 'nullable|numeric|min:0|max:100',
            'sections.*.questions.*.is_critical' => 'nullable|boolean',
            'sections.*.questions.*.is_required' => 'nullable|boolean',
            'sections.*.questions.*.options' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'name.required' => $isAr ? 'اسم القالب مطلوب.' : 'Template name is required.',
            'name.max' => $isAr ? 'اسم القالب طويل جداً.' : 'Template name is too long.',
            'sections.*.name.required_with' => $isAr ? 'اسم القسم مطلوب.' : 'Section name is required.',
            'sections.*.questions.*.label.required_with' => $isAr ? 'عنوان السؤال مطلوب.' : 'Question label is required.',
            'sections.*.questions.*.type.in' => $isAr ? 'نوع السؤال غير صالح.' : 'Invalid question type.',
        ];
    }
}