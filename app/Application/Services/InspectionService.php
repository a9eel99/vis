<?php

namespace App\Application\Services;

use App\Domain\DTOs\InspectionDTO;
use App\Domain\DTOs\ScoringResultDTO;
use App\Domain\Enums\InspectionStatus;
use App\Domain\Models\AuditLog;
use App\Domain\Models\Inspection;
use App\Domain\Models\InspectionResult;
use App\Domain\Models\InspectionTemplate;
use App\Domain\Repositories\Contracts\InspectionRepositoryInterface;
use App\Domain\Services\ScoringService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InspectionService
{
    public function __construct(
        private InspectionRepositoryInterface $inspectionRepository,
        private ScoringService $scoringService,
        private MediaService $mediaService,
    ) {}

    public function list(?string $search = null, ?string $status = null, ?string $grade = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->inspectionRepository->filter($search, $status, $grade, $perPage);
    }

    public function find(string $id): Inspection
    {
        return $this->inspectionRepository->getWithFullDetails($id);
    }

    public function create(InspectionDTO $dto): Inspection
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            $data['reference_number'] = Inspection::generateReferenceNumber();
            $data['status'] = InspectionStatus::DRAFT->value;
            $data['created_by'] = auth()->id();

            // Auto-set price from template
            $template = InspectionTemplate::find($dto->templateId);
            if ($template && $template->price > 0) {
                $data['price'] = $template->price;
            }

            $inspection = $this->inspectionRepository->create($data);

            AuditLog::log('inspection_created', Inspection::class, $inspection->id);

            return $inspection;
        });
    }

    public function startInspection(string $id): Inspection
    {
        return DB::transaction(function () use ($id) {
            $inspection = $this->inspectionRepository->findOrFail($id);

            $inspection->update([
                'status' => InspectionStatus::IN_PROGRESS->value,
                'started_at' => now(),
            ]);

            AuditLog::log('inspection_started', Inspection::class, $id);

            return $inspection->fresh();
        });
    }

    public function submitResults(string $id, array $answers, array $files = []): Inspection
    {
        return DB::transaction(function () use ($id, $answers, $files) {
            $inspection = $this->inspectionRepository->findOrFail($id, ['template.sections.questions']);
            $isScored = $inspection->template->isScored();

            // Save answers
            foreach ($answers as $questionId => $answerData) {
                $answer = is_array($answerData) ? ($answerData['answer'] ?? '') : $answerData;
                $remarks = is_array($answerData) ? ($answerData['remarks'] ?? null) : null;
                $remarksScore = is_array($answerData) ? ($answerData['remarks_score'] ?? null) : null;

                $result = InspectionResult::updateOrCreate(
                    [
                        'inspection_id' => $id,
                        'question_id' => $questionId,
                    ],
                    [
                        'answer' => $answer,
                        'remarks' => $remarks,
                        'remarks_score' => $remarksScore ? (int) $remarksScore : null,
                    ]
                );

                if ($isScored) {
                    $qType = $result->question?->type?->value ?? '';
                    $isScorable = !in_array($qType, ['text', 'photo', 'video']);

                    // If custom option used (remarks_score set), use it as the score
                    if ($remarksScore !== null && $remarksScore !== '') {
                        $score = (float) $remarksScore;
                    } else {
                        $score = $this->scoringService->scoreAnswer($result->load('question'));
                    }

                    $isCriticalFail = $isScorable && $result->question?->is_critical && $score < ($result->question->max_score * 0.5);

                    $result->update([
                        'score' => $score,
                        'is_critical_fail' => $isCriticalFail,
                    ]);
                } else {
                    // Descriptive mode — no scoring
                    $result->update([
                        'score' => null,
                        'is_critical_fail' => false,
                    ]);
                }

                // Handle file uploads for this question
                if (isset($files[$questionId])) {
                    $this->mediaService->uploadForResult($inspection, $result, $files[$questionId]);
                }
            }

            if ($isScored) {
                // Calculate total score
                $scoringResult = $this->scoringService->calculate($inspection->fresh());

                $inspection->update([
                    'status' => InspectionStatus::COMPLETED->value,
                    'total_score' => $scoringResult->totalScore,
                    'percentage' => $scoringResult->percentage,
                    'grade' => $scoringResult->grade->value,
                    'has_critical_failure' => $scoringResult->hasCriticalFailure,
                    'share_token' => Str::random(32),
                    'completed_at' => now(),
                ]);

                AuditLog::log('inspection_completed', Inspection::class, $id, null, $scoringResult->toArray());
            } else {
                // Descriptive — no scores, no grade
                $inspection->update([
                    'status' => InspectionStatus::COMPLETED->value,
                    'total_score' => null,
                    'percentage' => null,
                    'grade' => null,
                    'has_critical_failure' => false,
                    'share_token' => Str::random(32),
                    'completed_at' => now(),
                ]);

                AuditLog::log('inspection_completed', Inspection::class, $id, null, [
                    'mode' => 'descriptive',
                ]);
            }

            return $inspection->fresh(['results.question', 'vehicle', 'template', 'inspector', 'media']);
        });
    }

    public function cancel(string $id): Inspection
    {
        $inspection = $this->inspectionRepository->findOrFail($id);

        $inspection->update([
            'status' => InspectionStatus::CANCELLED->value,
        ]);

        AuditLog::log('inspection_cancelled', Inspection::class, $id);

        return $inspection->fresh();
    }

    public function delete(string $id): void
    {
        $inspection = $this->inspectionRepository->findOrFail($id, ['media']);

        AuditLog::log('inspection_deleted', Inspection::class, $id);

        foreach ($inspection->media as $media) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->path);
        }

        $inspection->media()->delete();
        $inspection->results()->delete();
        $inspection->delete();
    }

    public function getByVehicle(string $vehicleId): Collection
    {
        return $this->inspectionRepository->getByVehicle($vehicleId);
    }

    public function getDashboardStats(): array
    {
        return $this->inspectionRepository->getDashboardStats();
    }

    public function getMonthlyStats(int $months = 12): Collection
    {
        return $this->inspectionRepository->getMonthlyStats($months);
    }

    public function recalculateScore(string $id): ScoringResultDTO
    {
        $inspection = $this->inspectionRepository->findOrFail($id);
        $result = $this->scoringService->calculate($inspection);

        $inspection->update([
            'total_score' => $result->totalScore,
            'percentage' => $result->percentage,
            'grade' => $result->grade->value,
            'has_critical_failure' => $result->hasCriticalFailure,
        ]);

        return $result;
    }
}