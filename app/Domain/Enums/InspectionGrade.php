<?php

namespace App\Domain\Enums;

enum InspectionGrade: string
{
    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case NEEDS_ATTENTION = 'needs_attention';
    case CRITICAL = 'critical';

    public function label(): string
    {
        $ar = app()->getLocale() === 'ar';
        return match ($this) {
            self::EXCELLENT => $ar ? 'ممتاز' : 'Excellent',
            self::GOOD => $ar ? 'جيد' : 'Good',
            self::NEEDS_ATTENTION => $ar ? 'يحتاج اهتمام' : 'Needs Attention',
            self::CRITICAL => $ar ? 'حرج' : 'Critical',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EXCELLENT => '#10b981',
            self::GOOD => '#3b82f6',
            self::NEEDS_ATTENTION => '#f59e0b',
            self::CRITICAL => '#ef4444',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EXCELLENT => 'check-circle',
            self::GOOD => 'thumbs-up',
            self::NEEDS_ATTENTION => 'alert-triangle',
            self::CRITICAL => 'x-circle',
        };
    }
}
