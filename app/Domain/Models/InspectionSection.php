<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InspectionSection extends Model
{
    use HasUuids;

    protected $fillable = [
        'template_id',
        'name',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function template()
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id');
    }

    public function questions()
    {
        return $this->hasMany(InspectionQuestion::class, 'section_id')->orderBy('sort_order');
    }
}
