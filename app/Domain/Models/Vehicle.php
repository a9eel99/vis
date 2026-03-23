<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'make',
        'model',
        'year',
        'vin',
        'license_plate',
        'color',
        'mileage',
        'fuel_type',
        'transmission',
        'owner_name',
        'owner_phone',
        'owner_email',
        'notes',
        'image',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage' => 'integer',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function latestInspection()
    {
        return $this->hasOne(Inspection::class)->latestOfMany();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    public function getInspectionCountAttribute(): int
    {
        return $this->inspections()->count();
    }
}
