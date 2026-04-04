<?php

namespace App\Application\Services;

use App\Domain\Models\Inspection;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class ReportService
{
    public function generateInspectionPdf(Inspection $inspection)
    {
        // Fix for large HTML templates exceeding pcre limit
        @ini_set('pcre.backtrack_limit', '5000000');

        $inspection->load([
            'vehicle',
            'template.sections.questions',
            'results.question.section',
            'results.media',
            'inspector',
            'creator',
            'media',
        ]);

        $lang = app()->getLocale();

        // Organize results by section
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
                    'media' => [], // Photos removed from PDF — shown on share page instead
                ];
            }
        }

        // Company info - read from DB settings first, fallback to config
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
            'tax'     => \App\Domain\Models\Setting::get('company_tax_number', config('vis.company.tax_number')),
        ];

        // Logo as base64
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

        // Generate share URL for QR code (auto-create token if missing)
        if (!$inspection->share_token) {
            $inspection->update([
                'share_token' => hash('sha256', $inspection->id . $inspection->reference_number . config('app.key')),
            ]);
            $inspection->refresh();
        }

        $shareUrl = url('/share/' . $inspection->share_token);

        // Render the blade view to HTML
        $html = view('reports.inspection-pdf', [
            'inspection' => $inspection,
            'sectionResults' => $sectionResults,
            'company' => $company,
            'logoBase64' => $logoBase64,
            'shareUrl' => $shareUrl,
            'lang' => $lang,
        ])->render();

        // Increase regex limits for large HTML (mPDF uses preg heavily)
        @ini_set('pcre.backtrack_limit', '5000000');
        @ini_set('pcre.recursion_limit', '500000');

        // Create mPDF instance with Arabic support
        // Use Cairo font if installed, otherwise fallback to XBRiyaz
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $cairoPath = storage_path('fonts/Cairo-Regular.ttf');
        $hasCairo = file_exists($cairoPath);

        $customFontDirs = $hasCairo ? [storage_path('fonts')] : [];
        $customFontData = $hasCairo ? [
            'cairo' => [
                'R'  => 'Cairo-Regular.ttf',
                'B'  => 'Cairo-Bold.ttf',
            ],
        ] : [];

        // تأكد من وجود المجلد المؤقت لـ mPDF
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'default_font'     => $hasCairo ? 'cairo' : 'XBRiyaz',
            'default_font_size'=> 10,
            'margin_left'      => 10,
            'margin_right'     => 10,
            'margin_top'       => 10,
            'margin_bottom'    => 10,
            'autoArabic'       => true,
            'autoLangToFont'   => true,
            'tempDir'          => $tempDir,
            'fontDir'          => array_merge($fontDirs, $customFontDirs),
            'fontdata'         => $fontData + $customFontData,
        ]);

        // Set direction based on language
        if ($lang === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    public function downloadInspectionPdf(Inspection $inspection)
    {
        $mpdf = $this->generateInspectionPdf($inspection);
        $filename = "inspection-{$inspection->reference_number}.pdf";

        return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function streamInspectionPdf(Inspection $inspection)
    {
        $mpdf = $this->generateInspectionPdf($inspection);
        $filename = "inspection-{$inspection->reference_number}.pdf";

        return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}