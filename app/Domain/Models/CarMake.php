<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class CarMake extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'models', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'models' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}