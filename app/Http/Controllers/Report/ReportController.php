<?php

namespace App\Http\Controllers\Report;

use App\Application\Services\InspectionService;
use App\Application\Services\ReportService;
use App\Domain\Models\Inspection;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private InspectionService $inspectionService,
    ) {}

    public function downloadPdf(string $id)
    {
        try {
            $inspection = $this->inspectionService->find($id);
            return $this->reportService->downloadInspectionPdf($inspection);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إنشاء التقرير. حاول مرة أخرى.' : 'Error generating PDF. Please try again.');
        }
    }

    public function viewPdf(string $id)
    {
        try {
            $inspection = $this->inspectionService->find($id);
            return $this->reportService->streamInspectionPdf($inspection);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء عرض التقرير.' : 'Error viewing PDF.');
        }
    }

    // ── PUBLIC SHARE (no login) ──

    public function publicView(string $token)
    {
        $inspection = Inspection::where('share_token', $token)
            ->where('status', 'completed')
            ->with([
                'vehicle',
                'template.sections.questions',
                'results.question.section',
                'results.media',
                'inspector',
                'creator',
            ])
            ->firstOrFail();

        $lang = request()->query('lang', app()->getLocale());
        app()->setLocale($lang);

        $company = [
            'name'    => \App\Domain\Models\Setting::get($lang === 'ar' ? 'company_name_ar' : 'company_name_en',
                         $lang === 'ar' ? config('vis.company.name_ar') : config('vis.company.name_en')),
            'address' => \App\Domain\Models\Setting::get($lang === 'ar' ? 'company_address_ar' : 'company_address_en',
                         $lang === 'ar' ? config('vis.company.address_ar') : config('vis.company.address_en')),
            'notes'   => \App\Domain\Models\Setting::get($lang === 'ar' ? 'pdf_notes_ar' : 'pdf_notes_en',
                         $lang === 'ar' ? config('vis.company.notes_ar') : config('vis.company.notes_en')),
            'phone'   => \App\Domain\Models\Setting::get('company_phone', config('vis.company.phone')),
            'email'   => \App\Domain\Models\Setting::get('company_email', config('vis.company.email')),
            'website' => \App\Domain\Models\Setting::get('company_website', config('vis.company.website')),
        ];

        $logoBase64 = null;
        $logoPath = \App\Domain\Models\Setting::get('company_logo', config('vis.company.logo'));
        if ($logoPath) {
            $fullPath = storage_path('app/public/' . $logoPath);
            if (file_exists($fullPath)) {
                $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/' . $ext;
                $logoBase64 = "data:{$mime};base64," . base64_encode(file_get_contents($fullPath));
            }
        }

        $sectionResults = [];
        foreach ($inspection->template->sections as $section) {
            $sectionResults[$section->id] = [
                'section' => $section,
                'results' => [],
            ];
            foreach ($section->questions as $question) {
                $result = $inspection->results->firstWhere('question_id', $question->id);
                $sectionResults[$section->id]['results'][] = [
                    'question' => $question,
                    'result' => $result,
                ];
            }
        }

        return view('reports.public-view', compact(
            'inspection', 'sectionResults', 'company', 'logoBase64', 'lang', 'token'
        ));
    }

    public function publicPdf(string $token)
    {
        try {
            $inspection = Inspection::where('share_token', $token)
                ->where('status', 'completed')
                ->firstOrFail();

            return $this->reportService->downloadInspectionPdf($inspection);
        } catch (\Throwable $e) {
            report($e);
            abort(500, app()->getLocale() === 'ar' ? 'خطأ في إنشاء التقرير' : 'Error generating report');
        }
    }
}