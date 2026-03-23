<?php

namespace App\Domain\Models;

use App\Domain\Enums\InspectionGrade;
use App\Domain\Enums\InspectionStatus;
use App\Domain\Scopes\HiddenInspectionScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'vehicle_id',
        'template_id',
        'inspector_id',
        'created_by',
        'status',
        'total_score',
        'percentage',
        'grade',
        'has_critical_failure',
        'is_hidden',
        'hidden_reason',
        'hidden_at',
        'hidden_by',
        'notes',
        'share_token',
        'price',
        'payment_status',
        'paid_at',
        'discount',
        'payment_note',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InspectionStatus::class,
            'total_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'has_critical_failure' => 'boolean',
            'is_hidden' => 'boolean',
            'hidden_at' => 'datetime',
            'paid_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /* ── Global Scope: auto-filter hidden for non-super-admin ── */

    protected static function booted(): void
    {
        static::addGlobalScope(new HiddenInspectionScope());
    }

    /* ── Hide/Show Methods ── */

    public function hideInspection(?string $reason = null): self
    {
        $this->update([
            'is_hidden' => true,
            'hidden_reason' => $reason,
            'hidden_at' => now(),
            'hidden_by' => auth()->id(),
        ]);

        AuditLog::log('inspection_hidden', self::class, $this->id, null, [
            'reason' => $reason,
        ]);

        return $this;
    }

    public function showInspection(): self
    {
        $this->update([
            'is_hidden' => false,
            'hidden_reason' => null,
            'hidden_at' => null,
            'hidden_by' => null,
        ]);

        AuditLog::log('inspection_shown', self::class, $this->id);

        return $this;
    }

    /* ── Payment Helpers ── */

    public function getNetAmountAttribute(): float
    {
        return (float) $this->price - (float) $this->discount;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /* ── Relationships ── */

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function template()
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id')->withTrashed();
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hiddenByUser()
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }

    public function results()
    {
        return $this->hasMany(InspectionResult::class);
    }

    public function media()
    {
        return $this->hasMany(InspectionMedia::class);
    }

    /* ── Accessors ── */

    public function getGradeEnumAttribute(): ?InspectionGrade
    {
        return $this->grade ? InspectionGrade::tryFrom($this->grade) : null;
    }

    public function getGradeColorAttribute(): string
    {
        return $this->gradeEnum?->color() ?? '#6b7280';
    }

    public function getGradeLabelAttribute(): string
    {
        return $this->gradeEnum?->label() ?? (app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public static function generateReferenceNumber(): string
    {
        $prefix = 'INS';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$date}-{$random}";
    }

    /* ── Scopes ── */

    public function scopeCompleted($query)
    {
        return $query->where('status', InspectionStatus::COMPLETED);
    }

    public function scopeByInspector($query, $inspectorId)
    {
        return $query->where('inspector_id', $inspectorId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeWithHidden($query)
    {
        return $query->withoutGlobalScope(HiddenInspectionScope::class);
    }

    public function scopeOnlyHidden($query)
    {
        return $query->withoutGlobalScope(HiddenInspectionScope::class)
                     ->where('is_hidden', true);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }
}