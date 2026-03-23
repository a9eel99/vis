<?php

namespace App\Domain\Services;

use App\Domain\DTOs\ScoringResultDTO;
use App\Domain\Enums\InspectionGrade;
use App\Domain\Models\Inspection;
use App\Domain\Models\InspectionResult;

class ScoringService
{
    private float $excellentThreshold;
    private float $goodThreshold;
    private float $needsAttentionThreshold;

    public function __construct()
    {
        $this->excellentThreshold = (float) \App\Domain\Models\Setting::get('score_excellent', config('vis.scoring.excellent', 90));
        $this->goodThreshold = (float) \App\Domain\Models\Setting::get('score_good', config('vis.scoring.good', 75));
        $this->needsAttentionThreshold = (float) \App\Domain\Models\Setting::get('score_needs_attention', config('vis.scoring.needs_attention', 50));
    }

    public function calculate(Inspection $inspection): ScoringResultDTO
    {
        $inspection->load(['results.question', 'template.sections.questions']);

        $totalWeightedScore = 0;
        $maxPossibleScore = 0;
        $hasCriticalFailure = false;
        $criticalItems = [];

        foreach ($inspection->results as $result) {
            $question = $result->question;
            if (!$question) { continue; }

            // Skip non-scorable types (text, photo are documentation only)
            if (in_array($question->type->value, ['text', 'photo'])) { continue; }
            if ($question->weight <= 0 || $question->max_score <= 0) { continue; }

            $weightedScore = ($result->score ?? 0) * $question->weight;
            $weightedMax = $question->max_score * $question->weight;
            $totalWeightedScore += $weightedScore;
            $maxPossibleScore += $weightedMax;

            if ($question->is_critical && $result->is_critical_fail) {
                $hasCriticalFailure = true;
                $criticalItems[] = [
                    'question' => $question->label,
                    'section' => $question->section?->name,
                    'score' => $result->score,
                    'max_score' => $question->max_score,
                ];
            }
        }

        $percentage = $maxPossibleScore > 0 ? round(($totalWeightedScore / $maxPossibleScore) * 100, 2) : 0;
        $grade = $this->determineGrade($percentage, $hasCriticalFailure);

        return new ScoringResultDTO(
            totalScore: round($totalWeightedScore, 2),
            maxPossibleScore: round($maxPossibleScore, 2),
            percentage: $percentage,
            grade: $grade,
            hasCriticalFailure: $hasCriticalFailure,
            criticalItems: $criticalItems,
        );
    }

    public function scoreAnswer(InspectionResult $result): float
    {
        $question = $result->question;
        if (!$question) { return 0; }

        return match ($question->type->value) {
            'number' => $this->scoreNumeric($result, $question),
            'checkbox' => $this->scoreCheckbox($result, $question),
            'dropdown' => $this->scoreDropdown($result, $question),
            default => 0,
        };
    }

    private function scoreNumeric($result, $question): float
    {
        $answer = (float) ($result->answer ?? 0);
        return min($answer, $question->max_score);
    }

    private function scoreCheckbox($result, $question): float
    {
        $answer = strtolower($result->answer ?? '');
        return in_array($answer, ['yes', '1', 'true', 'pass', 'on']) ? $question->max_score : 0;
    }

    private function scoreDropdown($result, $question): float
    {
        $answer = $result->answer ?? '';
        foreach ($question->options ?? [] as $option) {
            if (is_array($option) && ($option['label'] ?? '') === $answer) {
                return min((float) ($option['score'] ?? 0), $question->max_score);
            }
        }
        return 0;
    }

    private function determineGrade(float $percentage, bool $hasCriticalFailure): InspectionGrade
    {
        if ($hasCriticalFailure) { return InspectionGrade::CRITICAL; }
        if ($percentage >= $this->excellentThreshold) { return InspectionGrade::EXCELLENT; }
        if ($percentage >= $this->goodThreshold) { return InspectionGrade::GOOD; }
        if ($percentage >= $this->needsAttentionThreshold) { return InspectionGrade::NEEDS_ATTENTION; }
        return InspectionGrade::CRITICAL;
    }

    public function getThresholds(): array
    {
        return [
            'excellent' => $this->excellentThreshold,
            'good' => $this->goodThreshold,
            'needs_attention' => $this->needsAttentionThreshold,
        ];
    }
}