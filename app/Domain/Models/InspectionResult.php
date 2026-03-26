<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InspectionResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'inspection_id',
        'question_id',
        'answer',
        'score',
        'is_critical_fail',
        'remarks',
        'remarks_score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_critical_fail' => 'boolean',
            'remarks_score' => 'integer',
        ];
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function question()
    {
        return $this->belongsTo(InspectionQuestion::class, 'question_id');
    }

    public function media()
    {
        return $this->hasMany(InspectionMedia::class, 'result_id');
    }

    public function getWeightedScoreAttribute(): float
    {
        if (!$this->score || !$this->question) {
            return 0;
        }
        return $this->score * $this->question->weight;
    }
}