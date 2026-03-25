<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class CarColor extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'hex', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
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