<?php

namespace App\Domain\DTOs;

use App\Domain\Enums\InspectionGrade;

class ScoringResultDTO
{
    public function __construct(
        public readonly float $totalScore,
        public readonly float $maxPossibleScore,
        public readonly float $percentage,
        public readonly InspectionGrade $grade,
        public readonly bool $hasCriticalFailure,
        public readonly array $criticalItems = [],
    ) {}

    public function toArray(): array
    {
        return [
            'total_score' => $this->totalScore,
            'max_possible_score' => $this->maxPossibleScore,
            'percentage' => $this->percentage,
            'grade' => $this->grade->value,
            'grade_label' => $this->grade->label(),
            'has_critical_failure' => $this->hasCriticalFailure,
            'critical_items' => $this->criticalItems,
        ];
    }
}
