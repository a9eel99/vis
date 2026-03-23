<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InspectionMedia extends Model
{
    use HasUuids;

    protected $table = 'inspection_media';

    protected $fillable = [
        'inspection_id',
        'result_id',
        'question_id',
        'type',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function result()
    {
        return $this->belongsTo(InspectionResult::class, 'result_id');
    }

    public function question()
    {
        return $this->belongsTo(InspectionQuestion::class, 'question_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
