<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\InspectionTemplate;
use App\Domain\Repositories\Contracts\TemplateRepositoryInterface;
use Illuminate\Support\Collection;

class TemplateRepository extends BaseRepository implements TemplateRepositoryInterface
{
    public function __construct(InspectionTemplate $model)
    {
        parent::__construct($model);
    }

    public function getActive(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->with(['sections.questions'])
            ->withCount('sections')
            ->orderBy('name')
            ->get();
    }

    public function getWithSections(string $id)
    {
        return $this->model
            ->with('sections')
            ->findOrFail($id);
    }

    public function getWithFullStructure(string $id)
    {
        return $this->model
            ->with(['sections' => function ($q) {
                $q->orderBy('sort_order')
                  ->with(['questions' => function ($qq) {
                      $qq->orderBy('sort_order');
                  }]);
            }])
            ->findOrFail($id);
    }

    public function duplicate(string $id)
    {
        $original = $this->getWithFullStructure($id);

        $template = $original->replicate();
        $template->name = $original->name . ' (Copy)';
        $template->version = 1;
        $template->created_by = auth()->id();
        $template->save();

        foreach ($original->sections as $section) {
            $newSection = $section->replicate();
            $newSection->template_id = $template->id;
            $newSection->save();

            foreach ($section->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->section_id = $newSection->id;
                $newQuestion->save();
            }
        }

        return $this->getWithFullStructure($template->id);
    }
}
