<?php

namespace App\Http\Controllers\Template;

use App\Application\Services\TemplateService;
use App\Domain\DTOs\TemplateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemplateRequest;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct(
        private TemplateService $templateService
    ) {}

    public function index()
    {
        $templates = $this->templateService->list();
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(TemplateRequest $request)
    {
        try {
            $dto = TemplateDTO::fromArray($request->validated());
            $template = $this->templateService->create($dto);

            return redirect()->route('templates.edit', $template)
                ->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء القالب. أضف الأقسام والأسئلة الآن.' : 'Template created. Now add sections and questions.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إنشاء القالب.' : 'Error creating template.');
        }
    }

    public function show(string $id)
    {
        $template = $this->templateService->find($id);
        return view('templates.show', compact('template'));
    }

    public function edit(string $id)
    {
        $template = $this->templateService->find($id);
        return view('templates.edit', compact('template'));
    }

    public function update(TemplateRequest $request, string $id)
    {
        try {
            $dto = TemplateDTO::fromArray($request->validated());
            $this->templateService->update($id, $dto);

            return redirect()->route('templates.show', $id)
                ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث القالب بنجاح.' : 'Template updated successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء تحديث القالب.' : 'Error updating template.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $template = \App\Domain\Models\InspectionTemplate::findOrFail($id);

            // Prevent deleting templates with inspections
            if ($template->inspections()->exists()) {
                return back()->with('error',
                    app()->getLocale() === 'ar'
                        ? 'لا يمكن حذف هذا القالب لأنه مرتبط بفحوصات. يمكنك تعطيله بدلاً من حذفه.'
                        : 'Cannot delete — this template has inspections. Deactivate it instead.');
            }

            $this->templateService->delete($id);

            return redirect()->route('templates.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف القالب بنجاح.' : 'Template deleted successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف القالب.' : 'Error deleting template.');
        }
    }

    public function duplicate(string $id)
    {
        try {
            $template = $this->templateService->duplicate($id);

            return redirect()->route('templates.edit', $template)
                ->with('success', app()->getLocale() === 'ar' ? 'تم نسخ القالب بنجاح.' : 'Template duplicated.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء نسخ القالب.' : 'Error duplicating template.');
        }
    }

    // ── Section Management ──

    public function addSection(Request $request, string $templateId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $this->templateService->addSection($templateId, $request->all());

            return redirect()->route('templates.edit', $templateId)
                ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة القسم.' : 'Section added.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إضافة القسم.' : 'Error adding section.');
        }
    }

    public function updateSection(Request $request, string $sectionId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $section = $this->templateService->updateSection($sectionId, $request->all());

            return redirect()->route('templates.edit', $section->template_id)
                ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث القسم.' : 'Section updated.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء تحديث القسم.' : 'Error updating section.');
        }
    }

    public function deleteSection(string $sectionId)
    {
        try {
            $section = \App\Domain\Models\InspectionSection::findOrFail($sectionId);
            $templateId = $section->template_id;
            $this->templateService->deleteSection($sectionId);

            return redirect()->route('templates.edit', $templateId)
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف القسم.' : 'Section deleted.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف القسم.' : 'Error deleting section.');
        }
    }

    // ── Question Management ──

    public function addQuestion(Request $request, string $sectionId)
    {
        $validated = $request->validate([
            'label'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => 'required|in:text,number,checkbox,dropdown,photo',
            'weight'      => 'nullable|numeric|min:0|max:100',
            'max_score'   => 'nullable|numeric|min:0|max:100',
            'is_critical' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'options_json'=> 'nullable|string',
        ]);

        try {
            $data = $validated;

            if (!empty($data['options_json'])) {
                $decoded = json_decode($data['options_json'], true);
                $data['options'] = is_array($decoded) ? $decoded : null;
            } elseif ($data['type'] !== 'dropdown') {
                $data['options'] = null;
            }

            unset($data['options_json']);

            $section = \App\Domain\Models\InspectionSection::findOrFail($sectionId);
            $this->templateService->addQuestion($sectionId, $data);

            return redirect()->route('templates.edit', $section->template_id)
                ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة السؤال.' : 'Question added.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إضافة السؤال.' : 'Error adding question.');
        }
    }

    public function updateQuestion(Request $request, string $questionId)
    {
        try {
            $question = \App\Domain\Models\InspectionQuestion::findOrFail($questionId);
            $data = $request->all();

            if ($request->filled('options_json')) {
                $data['options'] = json_decode($request->input('options_json'), true);
            } elseif (($data['type'] ?? '') !== 'dropdown') {
                $data['options'] = null;
            }

            unset($data['options_json'], $data['_token'], $data['_method']);
            $this->templateService->updateQuestion($questionId, $data);

            return redirect()->route('templates.edit', $question->section->template_id)
                ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث السؤال.' : 'Question updated.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء تحديث السؤال.' : 'Error updating question.');
        }
    }

    public function deleteQuestion(string $questionId)
    {
        try {
            $question = \App\Domain\Models\InspectionQuestion::findOrFail($questionId);
            $templateId = $question->section->template_id;
            $this->templateService->deleteQuestion($questionId);

            return redirect()->route('templates.edit', $templateId)
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف السؤال.' : 'Question deleted.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حذف السؤال.' : 'Error deleting question.');
        }
    }
}