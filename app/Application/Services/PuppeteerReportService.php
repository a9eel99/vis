<?php

namespace App\Application\Services;

use App\Domain\Models\Inspection;
use App\Domain\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PuppeteerReportService
{
    private string $nodePath;
    private string $scriptPath;
    private string $templatePath;

    public function __construct()
    {
        $this->nodePath     = config('vis.puppeteer.node_path', 'node');
        $this->scriptPath   = base_path('resources/js/reports/generate-report.js');
        $this->templatePath = base_path('resources/js/reports/report-template.html');
    }

    /**
     * Generate a PDF from an Inspection model.
     * Returns the path to the generated PDF.
     */
    public function generatePdf(Inspection $inspection, string $lang = 'ar'): string
    {
        // 1. Build the JSON data payload
        $data = $this->buildReportData($inspection, $lang);

        // 2. Write JSON to a temp file
        $jsonPath = storage_path('app/private/report-data-' . $inspection->id . '.json');
        file_put_contents($jsonPath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // 3. Define output PDF path
        $pdfDir  = storage_path('app/public/reports');
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0775, true);
        }
        $pdfPath = $pdfDir . '/' . $inspection->reference_number . '.pdf';

        // 4. Build the Node.js command
        $command = sprintf(
            '%s %s --data=%s --output=%s --template=%s 2>&1',
            escapeshellcmd($this->nodePath),
            escapeshellarg($this->scriptPath),
            escapeshellarg($jsonPath),
            escapeshellarg($pdfPath),
            escapeshellarg($this->templatePath)
        );

        // 5. Execute using proc_open (exec/shell_exec disabled on server)
        $outputStr = '';
        $exitCode  = -1;
        if (function_exists('proc_open')) {
            $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $process = proc_open($command, $descriptors, $pipes);
            if (is_resource($process)) {
                fclose($pipes[0]);
                $outputStr = stream_get_contents($pipes[1]);
                $outputStr .= stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                $exitCode = proc_close($process);
            }
        } elseif (function_exists('exec')) {
            $output = [];
            exec($command, $output, $exitCode);
            $outputStr = implode("\n", $output);
        } else {
            throw new \RuntimeException('No available method to execute Node.js (exec and proc_open are both disabled).');
        }

        // 6. Cleanup temp JSON
        @unlink($jsonPath);

        // 7. Handle errors

        if ($exitCode !== 0) {
            Log::error('Puppeteer PDF generation failed', [
                'inspection_id' => $inspection->id,
                'exit_code'     => $exitCode,
                'output'        => $outputStr,
                'command'       => $command,
            ]);
            throw new \RuntimeException('PDF generation failed: ' . $outputStr);
        }

        // Parse success response
        $result = json_decode($outputStr, true);
        if (!isset($result['success'])) {
            throw new \RuntimeException('Unexpected output from PDF generator: ' . $outputStr);
        }

        return $pdfPath;
    }

    /**
     * Generate PDF and return as HTTP response for download.
     */
    public function downloadPdf(Inspection $inspection, string $lang = null): \Symfony\Component\HttpFoundation\Response
    {
        $lang    = $lang ?? app()->getLocale();
        $pdfPath = $this->generatePdf($inspection, $lang);

        $filename = 'inspection-' . $inspection->reference_number . '.pdf';

        return response()->download($pdfPath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Generate PDF and return inline (for browser preview).
     */
    public function viewPdf(Inspection $inspection, string $lang = null): \Symfony\Component\HttpFoundation\Response
    {
        $lang    = $lang ?? app()->getLocale();
        $pdfPath = $this->generatePdf($inspection, $lang);

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ─── Build Report Data ────────────────────────────────────────────────────

    private function buildReportData(Inspection $inspection, string $lang): array
    {
        $isAr = $lang === 'ar';

        // Load eager relations
        $inspection->loadMissing([
            'vehicle.customer',
            'template.sections.questions',
            'results.question',
            'results.media',
            'inspector',
        ]);

        // Company info
        $companyName = Setting::get(
            $isAr ? 'company_name_ar' : 'company_name_en',
            config('vis.company.' . ($isAr ? 'name_ar' : 'name_en'), 'VIS')
        );

        // Vehicle image
        $vehicleImage = null;
        if ($inspection->vehicle?->image) {
            $imgPath = storage_path('app/public/' . $inspection->vehicle->image);
            if (file_exists($imgPath)) {
                $mime = mime_content_type($imgPath);
                $vData = file_get_contents($imgPath);
                if (strlen($vData) > 150000 && function_exists('imagecreatefromstring')) {
                    $im = @imagecreatefromstring($vData);
                    if ($im) {
                        ob_start();
                        imagejpeg($im, null, 55);
                        $c = ob_get_clean();
                        imagedestroy($im);
                        if ($c) {
                            $vData = $c;
                            $mime = 'image/jpeg';
                        }
                    }
                }
                $vehicleImage = 'data:' . $mime . ';base64,' . base64_encode($vData);
            }
        }

        // Grade
        $gradeStr   = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
        $gradeLabel = $inspection->grade_label ?? strtoupper($gradeStr ?: 'C');
        $gradeLetter = match (true) {
            $inspection->percentage >= 90 => 'A',
            $inspection->percentage >= 75 => 'B',
            $inspection->percentage >= 60 => 'C',
            $inspection->percentage >= 45 => 'D',
            default                        => 'F',
        };

        // Build sections
        $sections         = [];
        $sectionsOverview = [];
        $allNotes         = [];

        foreach ($inspection->template->sections ?? [] as $section) {
            $sectionStatus = 'ok';
            $items         = [];

            foreach ($section->questions as $question) {
                $result = $inspection->results->firstWhere('question_id', $question->id);
                $notes  = [];
                $hasImg = false;
                $images = [];

                // Determine status
                $itemStatus = 'ok';
                if ($result) {
                    if ($result->is_critical_fail) {
                        $itemStatus = 'bad';
                        $sectionStatus = 'bad';
                    } elseif ($result->remarks) {
                        $itemStatus = 'warn';
                        if ($sectionStatus === 'ok') $sectionStatus = 'warn';
                        $notes[] = $result->remarks;
                        $allNotes[] = ['type' => 'warn', 'text' => $result->remarks];
                    }

                    // Convert media to base64 (images only, max 4 per question)
                    if ($result->media && $result->media->count()) {
                        $hasImg = true;
                        foreach ($result->media->where('type', 'image')->take(2) as $media) {
                            $fullPath = storage_path('app/public/' . ltrim($media->path, '/'));
                            if (file_exists($fullPath)) {
                                $mime = $media->mime_type ?? mime_content_type($fullPath);
                                if (str_starts_with($mime, 'image/')) {
                                    $imgData = file_get_contents($fullPath);
                                    // Compress images > 150KB
                                    if (strlen($imgData) > 150000 && function_exists('imagecreatefromstring')) {
                                        $im = @imagecreatefromstring($imgData);
                                        if ($im) {
                                            ob_start();
                                            imagejpeg($im, null, 55);
                                            $c = ob_get_clean();
                                            imagedestroy($im);
                                            if ($c && strlen($c) < strlen($imgData)) {
                                                $imgData = $c;
                                                $mime = 'image/jpeg';
                                            }
                                        }
                                    }
                                    $images[] = 'data:' . $mime . ';base64,' . base64_encode($imgData);
                                }
                            }
                        }
                    }
                }

                $items[] = [
                    'name'    => $question->label,
                    'status'  => $itemStatus,
                    'notes'   => $notes,
                    'has_img' => $hasImg,
                    'images'  => $images,
                ];
            }

            $sections[] = [
                'id'     => $section->id,
                'icon'   => $this->sectionIcon($section->name),
                'title'  => $section->name,
                'sub'    => $section->description ?? '',
                'status' => $sectionStatus,
                'badge'  => $this->sectionBadge($sectionStatus, $isAr),
                'intro'  => $section->description ?? '',
                'items'  => $items,
            ];

            $sectionsOverview[] = [
                'icon'   => $this->sectionIcon($section->name),
                'name'   => $section->name,
                'status' => $sectionStatus,
            ];
        }

        return [
            'report_number'      => $inspection->reference_number,
            'date'               => $inspection->completed_at
                ? $inspection->completed_at->format('Y-m-d')
                : now()->format('Y-m-d'),
            'center_name'        => $companyName,
            'center_logo'        => null,
            'result_pass'        => !$inspection->has_critical_failure,
            'final_grade'        => $gradeLetter,
            'score_pct'          => (int) round($inspection->percentage ?? 0),
            'market_value'       => number_format($inspection->vehicle?->market_value ?? 0),
            'currency'           => $isAr ? 'د.أ' : 'JOD',
            'vehicle'            => [
                'image'   => $vehicleImage,
                'name'    => trim(($inspection->vehicle->year ?? '') . ' '
                    . ($inspection->vehicle->make ?? '') . ' '
                    . ($inspection->vehicle->model ?? '')),
                'sub'     => ($inspection->vehicle->fuel_type ?? '') . ' / ' . ($inspection->vehicle->color ?? ''),
                'vin'     => $inspection->vehicle->vin ?? '—',
                'plate'   => $inspection->vehicle->license_plate ?? '—',
                'mileage' => $inspection->vehicle->mileage
                    ? number_format($inspection->vehicle->mileage) . ' KM' : '—',
                'engine'  => $inspection->vehicle->engine_size ?? ($inspection->vehicle->fuel_type ?? '—'),
                'color'   => $inspection->vehicle->color ?? '—',
                'year'    => $inspection->vehicle->year ?? '—',
                'make'    => $inspection->vehicle->make ?? '—',
                'model'   => $inspection->vehicle->model ?? '—',
            ],
            'owner_name'         => $inspection->vehicle?->owner_name
                ?? $inspection->vehicle?->customer?->name ?? '—',
            'buyer_name'         => $inspection->vehicle?->customer?->name ?? '—',
            'inspector_name'     => $inspection->inspector?->name ?? '—',
            'sections_overview'  => $sectionsOverview,
            'history'            => [],
            'sections'           => $sections,
            'bosch_results'      => [],
            'all_notes'          => $allNotes,
            'share_url'          => $inspection->share_token
                ? url('/share/' . $inspection->share_token)
                : null,
            'gallery'            => $this->buildGallery($inspection),
        ];
    }

    private function buildGallery(Inspection $inspection): array
    {
        $gallery = [];

        // Load all media for this inspection
        $allMedia = \App\Domain\Models\InspectionMedia::where('inspection_id', $inspection->id)
            ->where('type', 'image')
            ->orderBy('created_at')
            ->get();

        foreach ($allMedia as $media) {
            $fullPath = storage_path('app/public/' . ltrim($media->path, '/'));
            if (!file_exists($fullPath)) continue;

            $mime = $media->mime_type ?? mime_content_type($fullPath);
            if (!str_starts_with($mime, 'image/')) continue;

            // Find question label for caption
            $caption = $media->question?->label ?? ($media->original_name ?? '');

            $gData = file_get_contents($fullPath);
            if (strlen($gData) > 150000 && function_exists('imagecreatefromstring')) {
                $im = @imagecreatefromstring($gData);
                if ($im) {
                    ob_start();
                    imagejpeg($im, null, 50);
                    $c = ob_get_clean();
                    imagedestroy($im);
                    if ($c) {
                        $gData = $c;
                        $mime = 'image/jpeg';
                    }
                }
            }
            $gallery[] = [
                'src'     => 'data:' . $mime . ';base64,' . base64_encode($gData),
                'caption' => $caption,
            ];
        }

        return $gallery;
    }

    private function sectionIcon(string $name): string
    {
        $map = [
            'هيكل'     => '🚗',
            'exterior' => '🚗',
            'شاصي'     => '🔩',
            'chassis'  => '🔩',
            'محرك'     => '⚙️',
            'engine'   => '⚙️',
            'كهرباء'   => '⚡',
            'electrical' => '⚡',
            'تكييف'    => '❄️',
            'brakes'   => '🛑',
            'مكابح'    => '🛑',
            'توجيه'    => '🔧',
            'steering' => '🔧',
            'تاريخ'    => '📜',
            'history'  => '📜',
            'طريق'     => '🛣️',
            'road'     => '🛣️',
            'bosch'    => '🔬',
        ];

        $lower = mb_strtolower($name);
        foreach ($map as $key => $icon) {
            if (str_contains($lower, $key)) return $icon;
        }

        return '📋';
    }

    private function sectionBadge(string $status, bool $isAr): string
    {
        return match ($status) {
            'ok'   => $isAr ? '✔ جيد' : '✔ Good',
            'warn' => $isAr ? '⚠ يحتاج انتباه' : '⚠ Attention',
            'bad'  => $isAr ? '❌ مشاكل' : '❌ Issues',
            default => '—',
        };
    }
}
