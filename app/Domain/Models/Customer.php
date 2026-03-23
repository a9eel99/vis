<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'id_number',
        'address',
        'notes',
        'created_by',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function inspections()
    {
        return $this->hasManyThrough(Inspection::class, Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->phone) return null;
        $phone = preg_replace('/[^0-9+]/', '', $this->phone);
        $phone = ltrim($phone, '+');
        // If starts with 0, replace with 962 (Jordan)
        if (str_starts_with($phone, '0')) {
            $phone = '962' . substr($phone, 1);
        }
        return "https://wa.me/{$phone}";
    }
}
