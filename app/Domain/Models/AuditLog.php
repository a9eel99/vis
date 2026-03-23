<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class AuditLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    public static function log(
        string $action,
        ?string $modelType = null,
        ?string $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        // Resolve morph map alias (e.g. App\Domain\Models\Inspection → 'inspection')
        if ($modelType) {
            $alias = array_search($modelType, Relation::morphMap());
            if ($alias !== false) {
                $modelType = $alias;
            }
        }

        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}