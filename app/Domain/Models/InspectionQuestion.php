<?php

namespace App\Domain\Models;

use App\Domain\Enums\QuestionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InspectionQuestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'section_id',
        'label',
        'description',
        'type',
        'options',
        'weight',
        'max_score',
        'is_critical',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'options' => 'array',
            'weight' => 'decimal:2',
            'max_score' => 'decimal:2',
            'is_critical' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function section()
    {
        return $this->belongsTo(InspectionSection::class, 'section_id');
    }

    public function results()
    {
        return $this->hasMany(InspectionResult::class, 'question_id');
    }

    public function media()
    {
        return $this->hasMany(InspectionMedia::class, 'question_id');
    }

    public function getWeightedMaxScoreAttribute(): float
    {
        return $this->weight * $this->max_score;
    }
}
