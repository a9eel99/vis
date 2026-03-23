<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionTemplate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'scoring_mode',
        'fuel_type',
        'price',
        'version',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'version' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    /* ── Scoring Mode Helpers ── */

    public function isScored(): bool
    {
        return ($this->scoring_mode ?? 'scored') === 'scored';
    }

    public function isDescriptive(): bool
    {
        return $this->scoring_mode === 'descriptive';
    }

    /* ── Relationships ── */

    public function sections()
    {
        return $this->hasMany(InspectionSection::class, 'template_id')->orderBy('sort_order');
    }

    public function questions()
    {
        return $this->hasManyThrough(
            InspectionQuestion::class,
            InspectionSection::class,
            'template_id',
            'section_id'
        );
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Computed ── */

    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getMaxPossibleScoreAttribute(): float
    {
        return $this->questions()->sum(\DB::raw('weight * max_score'));
    }

    public function getScoringModeLabelAttribute(): string
    {
        $ar = app()->getLocale() === 'ar';
        return $this->isScored()
            ? ($ar ? 'تقييم بالعلامات' : 'Scored')
            : ($ar ? 'فحص وصفي' : 'Descriptive');
    }
}