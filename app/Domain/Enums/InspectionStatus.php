<?php

namespace App\Domain\Enums;

enum InspectionStatus: string
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        $ar = app()->getLocale() === 'ar';
        return match ($this) {
            self::DRAFT => $ar ? 'مسودة' : 'Draft',
            self::IN_PROGRESS => $ar ? 'قيد الإنجاز' : 'In Progress',
            self::COMPLETED => $ar ? 'مكتمل' : 'Completed',
            self::CANCELLED => $ar ? 'ملغي' : 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::IN_PROGRESS => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
