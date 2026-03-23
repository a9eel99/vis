<?php

namespace App\Application\Services;

use App\Domain\DTOs\TemplateDTO;
use App\Domain\Models\AuditLog;
use App\Domain\Models\InspectionQuestion;
use App\Domain\Models\InspectionSection;
use App\Domain\Models\InspectionTemplate;
use App\Domain\Repositories\Contracts\TemplateRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->templateRepository->paginate($perPage);
    }

    public function getActive(): Collection
    {
        return $this->templateRepository->getActive();
    }

    public function find(string $id): InspectionTemplate
    {
        return $this->templateRepository->getWithFullStructure($id);
    }

    public function create(TemplateDTO $dto): InspectionTemplate
    {
        return DB::transaction(function () use ($dto) {
            $template = $this->templateRepository->create(
                array_merge($dto->toArray(), ['created_by' => auth()->id()])
            );

            if (!empty($dto->sections)) {
                $this->syncSections($template, $dto->sections);
            }

            AuditLog::log('template_created', InspectionTemplate::class, $template->id);

            return $template->load('sections.questions');
        });
    }

    public function update(string $id, TemplateDTO $dto): InspectionTemplate
    {
        return DB::transaction(function () use ($id, $dto) {
            $template = $this->templateRepository->update($id, $dto->toArray());

            if (!empty($dto->sections)) {
                $this->syncSections($template, $dto->sections);
            }

            AuditLog::log('template_updated', InspectionTemplate::class, $id);

            return $template->load('sections.questions');
        });
    }

    public function delete(string $id): bool
    {
        AuditLog::log('template_deleted', InspectionTemplate::class, $id);
        return $this->templateRepository->delete($id);
    }

    public function addSection(string $templateId, array $data): InspectionSection
    {
        $template = $this->templateRepository->findOrFail($templateId);
        $maxOrder = $template->sections()->max('sort_order') ?? 0;

        return InspectionSection::create([
            'template_id' => $templateId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? $maxOrder + 1,
        ]);
    }

    public function updateSection(string $sectionId, array $data): InspectionSection
    {
        $section = InspectionSection::findOrFail($sectionId);
        $section->update($data);
        return $section;
    }

    public function deleteSection(string $sectionId): bool
    {
        return InspectionSection::findOrFail($sectionId)->delete();
    }

    public function addQuestion(string $sectionId, array $data): InspectionQuestion
    {
        $section = InspectionSection::findOrFail($sectionId);
        $maxOrder = $section->questions()->max('sort_order') ?? 0;

        return InspectionQuestion::create([
            'section_id' => $sectionId,
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'options' => $data['options'] ?? null,
            'weight' => $data['weight'] ?? 1.00,
            'max_score' => $data['max_score'] ?? 10.00,
            'is_critical' => $data['is_critical'] ?? false,
            'is_required' => $data['is_required'] ?? true,
            'sort_order' => $data['sort_order'] ?? $maxOrder + 1,
        ]);
    }

    public function updateQuestion(string $questionId, array $data): InspectionQuestion
    {
        $question = InspectionQuestion::findOrFail($questionId);
        $question->update($data);
        return $question;
    }

    public function deleteQuestion(string $questionId): bool
    {
        return InspectionQuestion::findOrFail($questionId)->delete();
    }

    public function duplicate(string $id): InspectionTemplate
    {
        return DB::transaction(function () use ($id) {
            return $this->templateRepository->duplicate($id);
        });
    }

    private function syncSections(InspectionTemplate $template, array $sections): void
    {
        $existingIds = [];

        foreach ($sections as $index => $sectionData) {
            $sectionAttrs = [
                'template_id' => $template->id,
                'name' => $sectionData['name'],
                'description' => $sectionData['description'] ?? null,
                'sort_order' => $index,
            ];

            if (!empty($sectionData['id'])) {
                $section = InspectionSection::find($sectionData['id']);
                if ($section) {
                    $section->update($sectionAttrs);
                } else {
                    $section = InspectionSection::create($sectionAttrs);
                }
            } else {
                $section = InspectionSection::create($sectionAttrs);
            }

            $existingIds[] = $section->id;

            if (!empty($sectionData['questions'])) {
                $this->syncQuestions($section, $sectionData['questions']);
            }
        }

        // Delete removed sections
        $template->sections()->whereNotIn('id', $existingIds)->delete();
    }

    private function syncQuestions(InspectionSection $section, array $questions): void
    {
        $existingIds = [];

        foreach ($questions as $index => $qData) {
            $qAttrs = [
                'section_id' => $section->id,
                'label' => $qData['label'],
                'description' => $qData['description'] ?? null,
                'type' => $qData['type'],
                'options' => $qData['options'] ?? null,
                'weight' => $qData['weight'] ?? 1.00,
                'max_score' => $qData['max_score'] ?? 10.00,
                'is_critical' => $qData['is_critical'] ?? false,
                'is_required' => $qData['is_required'] ?? true,
                'sort_order' => $index,
            ];

            if (!empty($qData['id'])) {
                $question = InspectionQuestion::find($qData['id']);
                if ($question) {
                    $question->update($qAttrs);
                } else {
                    $question = InspectionQuestion::create($qAttrs);
                }
            } else {
                $question = InspectionQuestion::create($qAttrs);
            }

            $existingIds[] = $question->id;
        }

        $section->questions()->whereNotIn('id', $existingIds)->delete();
    }
}
