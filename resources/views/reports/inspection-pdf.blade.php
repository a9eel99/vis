@php
    $isRtl    = $lang === 'ar';
    $dir      = $isRtl ? 'rtl' : 'ltr';
    $align    = $isRtl ? 'right' : 'left';
    $alignOpp = $isRtl ? 'left' : 'right';
    $isScored = $inspection->template->isScored();

    $gradeStr   = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeColor = $inspection->grade_color ?? '#6b7280';
    $gradeMap   = ['excellent'=>'#10b981','good'=>'#3b82f6','needs_attention'=>'#f59e0b','critical'=>'#ef4444'];
    $gradeBg    = $gradeMap[$gradeStr] ?? '#6b7280';
    $gradeTxt   = $gradeStr === 'needs_attention' ? '#1a1a2e' : '#ffffff';
@endphp
<html dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<style>
    body { font-size: 10pt; color: #1a1a2e; direction: {{ $dir }}; }
    table { border-collapse: collapse; }
    .c { text-align: center; }
    .r { text-align: right; }
    .l { text-align: left; }

    /* Header */
    .hdr { width: 100%; border-bottom: 3px solid #1e3a5f; margin-bottom: 10px; padding-bottom: 8px; }
    .hdr td { vertical-align: middle; border: none; padding: 3px; }
    .co-name { font-size: 16pt; font-weight: bold; color: #1e3a5f; }
    .co-det { font-size: 8pt; color: #6b7280; }
    .ref-lbl { font-size: 7pt; color: #9ca3af; }
    .ref-val { font-size: 10pt; font-weight: bold; color: #1e3a5f; }

    /* Grade */
    .grade { width: 100%; padding: 10px; text-align: center; border-radius: 6px; margin-bottom: 10px; color: {{ $gradeTxt }}; background: {{ $gradeBg }}; }
    .grade-l { font-size: 16pt; font-weight: bold; }
    .grade-s { font-size: 10pt; opacity: 0.9; }

    /* Alert */
    .alert { background: #fef2f2; border: 2px solid #ef4444; border-radius: 6px; padding: 8px; margin-bottom: 10px; color: #991b1b; font-weight: bold; text-align: {{ $align }}; }

    /* Info tables */
    .info { width: 100%; margin-bottom: 10px; }
    .info td { padding: 5px 8px; border: 1px solid #e5e7eb; text-align: {{ $align }}; }
    .info .lb { font-weight: bold; color: #6b7280; font-size: 8pt; background: #f9fafb; width: 90px; }
    .info .vl { color: #1a1a2e; }

    /* Summary */
    .summary { width: 100%; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; margin-bottom: 10px; }
    .summary td { text-align: center; padding: 5px; border: none; }
    .st-lbl { color: #6b7280; font-size: 7pt; }
    .st-val { font-size: 13pt; font-weight: bold; color: #1e3a5f; }

    /* Section */
    .sec-hdr { background: #1e3a5f; color: #fff; padding: 7px 10px; font-size: 11pt; font-weight: bold; text-align: {{ $align }}; }
    .sec-num { opacity: 0.6; font-size: 8pt; }

    /* Questions */
    .qt { width: 100%; margin-bottom: 12px; }
    .qt th { background: #f3f4f6; padding: 5px 6px; text-align: {{ $align }}; font-size: 8pt; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
    .qt td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; text-align: {{ $align }}; font-size: 9pt; vertical-align: top; }
    .crit { color: #ef4444; font-weight: bold; font-size: 8pt; }
    .pill { display: inline; padding: 1px 5px; border-radius: 8px; font-size: 8pt; font-weight: bold; }
    .p-hi { background: #d1fae5; color: #065f46; }
    .p-md { background: #fef3c7; color: #92400e; }
    .p-lo { background: #fee2e2; color: #991b1b; }
    .rmk { font-style: italic; color: #6b7280; font-size: 8pt; }
    .na { color: #9ca3af; font-size: 7pt; }

    /* Notes */
    .notes { background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px; margin-top: 12px; text-align: {{ $align }}; }
    .notes-t { font-weight: bold; color: #1e3a5f; font-size: 9pt; margin-bottom: 3px; }
    .notes-b { color: #374151; font-size: 8pt; line-height: 1.8; }

    /* Footer */
    .ftr { border-top: 2px solid #e5e7eb; padding-top: 6px; margin-top: 15px; text-align: center; font-size: 7pt; color: #9ca3af; }
</style>
</head>
<body>

{{-- ===== HEADER ===== --}}
<table class="hdr">
<tr>
    @if($isRtl)
    <td style="width:25%">
        <div class="ref-lbl">رقم المرجع</div>
        <div class="ref-val">{{ $inspection->reference_number }}</div>
        <div style="font-size:7pt;color:#9ca3af">{{ $inspection->completed_at ? $inspection->completed_at->format('Y-m-d') : now()->format('Y-m-d') }}</div>
    </td>
    <td class="c">
        <div class="co-name">{{ $company['name'] }}</div>
        @if($company['address'])<div class="co-det">{{ $company['address'] }}</div>@endif
        @if($company['phone'] || $company['email'])
        <div class="co-det">
            @if($company['phone']){{ $company['phone'] }}@endif
            @if($company['email']) | {{ $company['email'] }}@endif
        </div>
        @endif
    </td>
    <td style="width:80px;text-align:right">
        @if($logoBase64)<img src="{{ $logoBase64 }}" style="max-width:70px;max-height:70px">@endif
    </td>
    @else
    <td style="width:80px">
        @if($logoBase64)<img src="{{ $logoBase64 }}" style="max-width:70px;max-height:70px">@endif
    </td>
    <td class="c">
        <div class="co-name">{{ $company['name'] }}</div>
        @if($company['address'])<div class="co-det">{{ $company['address'] }}</div>@endif
        @if($company['phone'] || $company['email'])
        <div class="co-det">@if($company['phone']){{ $company['phone'] }}@endif @if($company['email']) | {{ $company['email'] }}@endif</div>
        @endif
    </td>
    <td style="width:25%">
        <div class="ref-lbl">Reference</div>
        <div class="ref-val">{{ $inspection->reference_number }}</div>
        <div style="font-size:7pt;color:#9ca3af">{{ $inspection->completed_at ? $inspection->completed_at->format('Y-m-d') : now()->format('Y-m-d') }}</div>
    </td>
    @endif
</tr>
</table>

{{-- ===== GRADE ===== --}}
@if($isScored && $gradeStr)
<div class="grade">
    <div class="grade-l">{{ $gradeLabel }}</div>
    <div class="grade-s">
        {{ $isRtl ? 'النسبة' : 'Score' }}: {{ number_format($inspection->percentage, 1) }}%
        @if($inspection->has_critical_failure) — {{ $isRtl ? 'إخفاق حرج' : 'CRITICAL FAILURE' }} @endif
    </div>
</div>
@elseif(!$isScored)
<div class="grade" style="background:#1e3a5f">
    <div class="grade-l">{{ $isRtl ? 'تقرير فحص وصفي' : 'Descriptive Inspection Report' }}</div>
</div>
@endif

@if($isScored && $inspection->has_critical_failure)
<div class="alert">⚠ {{ $isRtl ? 'تحذير: يوجد إخفاق حرج في هذه المركبة يتطلب إصلاحاً فورياً.' : 'WARNING: Critical failures detected.' }}</div>
@endif

{{-- ===== VEHICLE ===== --}}
<table class="info">
<tr>
    <td class="lb">{{ $isRtl ? 'المركبة' : 'Vehicle' }}</td>
    <td class="vl" colspan="3">
        {{ $inspection->vehicle->year ?? '' }} {{ $inspection->vehicle->make ?? '' }} {{ $inspection->vehicle->model ?? '' }}
        @if($inspection->vehicle->color ?? null)({{ $inspection->vehicle->color }})@endif
    </td>
</tr>
<tr>
    <td class="lb">{{ $isRtl ? 'اللوحة' : 'Plate' }}</td>
    <td class="vl">{{ $inspection->vehicle->license_plate ?? '—' }}</td>
    <td class="lb">{{ $isRtl ? 'الشاسيه' : 'VIN' }}</td>
    <td class="vl" style="font-size:8pt">{{ $inspection->vehicle->vin ?? '—' }}</td>
</tr>
<tr>
    <td class="lb">{{ $isRtl ? 'الكيلومتر' : 'Mileage' }}</td>
    <td class="vl">{{ $inspection->vehicle->mileage ? number_format($inspection->vehicle->mileage) . ' km' : '—' }}</td>
    <td class="lb">{{ $isRtl ? 'المالك' : 'Owner' }}</td>
    <td class="vl">{{ $inspection->vehicle->owner_name ?? '—' }}</td>
</tr>
</table>

{{-- ===== INSPECTION INFO ===== --}}
<table class="info">
<tr>
    <td class="lb">{{ $isRtl ? 'القالب' : 'Template' }}</td>
    <td class="vl">{{ $inspection->template->name ?? '—' }}</td>
    <td class="lb">{{ $isRtl ? 'الحالة' : 'Status' }}</td>
    <td class="vl">{{ $inspection->status_label }}</td>
</tr>
<tr>
    <td class="lb">{{ $isRtl ? 'الفاحص' : 'Inspector' }}</td>
    <td class="vl">{{ $inspection->inspector->name ?? '—' }}</td>
    <td class="lb">{{ $isRtl ? 'أنشئ بواسطة' : 'Created By' }}</td>
    <td class="vl">{{ $inspection->creator->name ?? '—' }}</td>
</tr>
<tr>
    <td class="lb">{{ $isRtl ? 'بدأ في' : 'Started' }}</td>
    <td class="vl">{{ $inspection->started_at ? $inspection->started_at->format('Y-m-d H:i') : '—' }}</td>
    <td class="lb">{{ $isRtl ? 'اكتمل في' : 'Completed' }}</td>
    <td class="vl">{{ $inspection->completed_at ? $inspection->completed_at->format('Y-m-d H:i') : '—' }}</td>
</tr>
</table>

{{-- ===== SUMMARY (scored only) ===== --}}
@if($isScored)
<table class="summary">
<tr>
    <td>
        <div class="st-lbl">{{ $isRtl ? 'الدرجة الكلية' : 'TOTAL SCORE' }}</div>
        <div class="st-val">{{ number_format($inspection->total_score ?? 0, 1) }}</div>
    </td>
    <td>
        <div class="st-lbl">{{ $isRtl ? 'النسبة' : 'PERCENTAGE' }}</div>
        <div class="st-val">{{ number_format($inspection->percentage ?? 0, 1) }}%</div>
    </td>
    <td>
        <div class="st-lbl">{{ $isRtl ? 'التقييم' : 'GRADE' }}</div>
        <div class="st-val" style="color:{{ $gradeColor }}">{{ $gradeLabel }}</div>
    </td>
    <td>
        <div class="st-lbl">{{ $isRtl ? 'إخفاق حرج' : 'CRITICAL' }}</div>
        <div class="st-val">{{ $inspection->has_critical_failure ? ($isRtl ? 'نعم' : 'Yes') : ($isRtl ? 'لا' : 'No') }}</div>
    </td>
</tr>
</table>
@endif

{{-- ===== SECTIONS ===== --}}
@foreach($sectionResults as $sectionId => $data)
    @php $section = $data['section']; $results = $data['results']; @endphp

    <div class="sec-hdr">
        <span class="sec-num">{{ $isRtl ? 'القسم' : 'Section' }} {{ $loop->iteration }}</span>
        &nbsp; {{ $section->name }}
    </div>

    <table class="qt">
    <thead>
    <tr>
        <th style="width:4%">#</th>
        <th style="width:{{ $isScored ? '28' : '35' }}%">{{ $isRtl ? 'السؤال' : 'Question' }}</th>
        <th style="width:{{ $isScored ? '20' : '30' }}%">{{ $isRtl ? 'الإجابة' : 'Answer' }}</th>
        @if($isScored)<th style="width:12%">{{ $isRtl ? 'الدرجة' : 'Score' }}</th>@endif
        <th>{{ $isRtl ? 'ملاحظات' : 'Remarks' }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($results as $item)
        @php
            $question  = $item['question'];
            $result    = $item['result'];
            $qType     = is_object($question->type) ? $question->type->value : $question->type;
            $isScorable = !in_array($qType, ['text', 'photo', 'video']) && $question->max_score > 0 && $question->weight > 0;
            $score     = $result?->score ?? 0;
            $maxScore  = $question->max_score;
            $pct       = ($isScorable && $maxScore > 0) ? ($score / $maxScore) * 100 : -1;
            $pc        = $pct >= 75 ? 'p-hi' : ($pct >= 50 ? 'p-md' : 'p-lo');
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                {{ $question->label }}
                @if($isScored && $question->is_critical)
                    <span class="crit">{{ $isRtl ? '★ حرج' : '★ CRIT' }}</span>
                @endif
            </td>
            <td>
                @if($result)
                    @if($qType === 'checkbox')
                        {{ $result->answer == '1' ? ($isRtl ? 'نعم ✅' : 'Yes ✅') : ($isRtl ? 'لا ☐' : 'No ☐') }}
                    @elseif(in_array($qType, ['photo', 'video']))
                        <span class="na">{{ $isRtl ? 'مرفق' : 'Attached' }}</span>
                    @else
                        {{ $result->answer ?? '—' }}
                    @endif
                    @if($isScored && $result->is_critical_fail)
                        <br><span class="crit">{{ $isRtl ? '⚠ إخفاق' : '⚠ FAIL' }}</span>
                    @endif
                @else
                    —
                @endif
            </td>
            @if($isScored)
            <td style="text-align:center">
                @if($isScorable && $result)
                    <span class="pill {{ $pc }}">{{ number_format($score, 1) }} / {{ intval($maxScore) }}</span>
                @elseif(!$isScorable)
                    <span class="na">{{ $isRtl ? 'توثيق' : 'N/A' }}</span>
                @else
                    —
                @endif
            </td>
            @endif
            <td>
                @if($result && $result->remarks)
                    <span class="rmk">{{ $result->remarks }}</span>
                @else
                    —
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
    </table>
@endforeach

{{-- ===== INSPECTOR NOTES ===== --}}
@if($inspection->notes)
<div class="notes">
    <div class="notes-t">{{ $isRtl ? 'ملاحظات الفاحص' : 'Inspector Notes' }}</div>
    <div class="notes-b">{{ $inspection->notes }}</div>
</div>
@endif

{{-- ===== COMPANY NOTES ===== --}}
@if(!empty($company['notes']))
<div class="notes">
    <div class="notes-t">{{ $isRtl ? 'ملاحظات عامة' : 'General Notes' }}</div>
    <div class="notes-b">{{ $company['notes'] }}</div>
</div>
@endif

{{-- ===== FOOTER ===== --}}
<div class="ftr">
    {{ $isRtl ? 'تم إنشاء التقرير بتاريخ' : 'Generated on' }}
    {{ now()->format('Y-m-d H:i') }}
    | {{ $inspection->reference_number }}
    | {{ $company['name'] }}
    @if(!empty($company['website'])) | {{ $company['website'] }} @endif
</div>

</body>
</html>