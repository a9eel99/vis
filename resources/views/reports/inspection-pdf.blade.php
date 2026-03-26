{{-- @php
    $isRtl = $lang === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
    $alignOpp = $isRtl ? 'left' : 'right';
    $isScored = $inspection->template->isScored();

    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeMap = ['excellent'=>'#10b981','good'=>'#3b82f6','needs_attention'=>'#f59e0b','critical'=>'#ef4444'];
    $gradeBg = $gradeMap[$gradeStr] ?? '#6b7280';

    $vMake = $inspection->vehicle->make ?? '';
    $vModel = $inspection->vehicle->model ?? '';
    $vColor = $inspection->vehicle->color ?? '';
    $vYear = $inspection->vehicle->year ?? '';
    $vehicleStr = trim("$vMake $vModel") . ($vColor || $vYear ? " ($vColor $vYear)" : '');

    $arNums = ['الأول','الثاني','الثالث','الرابع','الخامس','السادس','السابع','الثامن','التاسع','العاشر'];
@endphp
<html dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; }
    body {
        font-family: cairo, XBRiyaz, sans-serif;
        font-size: 10pt;
        color: #333;
        direction: {{ $dir }};
        background: #fff;
    }
    table { border-collapse: collapse; width: 100%; }

    /* ─── Header ─── */
    .header {
        background: #1e3a8a;
        color: #fff;
        padding: 12px 20px;
    }
    .header td { border: none; vertical-align: middle; }
    .header-title { font-size: 20pt; font-weight: bold; color: #fff; text-align: center; }
    .header-logo { font-size: 12pt; font-weight: bold; color: #fff; text-align: {{ $alignOpp }}; }
    .header-ref-lbl { font-size: 7pt; color: rgba(255,255,255,0.65); }
    .header-ref-val { font-size: 9.5pt; font-weight: bold; color: #fff; }
    .header-ref-date { font-size: 7.5pt; color: rgba(255,255,255,0.6); }

    /* ─── Template Banner ─── */
    .tpl-banner {
        background: #1e3a8a;
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        margin: 12px 0 10px 0;
        font-weight: bold;
        font-size: 12pt;
    }
    .tpl-banner td { border: none; vertical-align: middle; }
    .tpl-name { text-align: {{ $align }}; }
    .tpl-icon-box {
        width: 28px;
        height: 28px;
        border: 2px solid rgba(255,255,255,0.6);
        border-radius: 4px;
        text-align: center;
        line-height: 26px;
        font-size: 14pt;
    }

    /* ─── Data Tables ─── */
    .data-tbl { width: 100%; margin-bottom: 6px; font-size: 10pt; }
    .data-tbl td {
        border: 1px solid #e0e0e0;
        padding: 7px 10px;
        text-align: {{ $align }};
    }
    .data-tbl .lbl {
        background: #fafafa;
        width: 14%;
        color: #666;
        font-size: 9pt;
    }
    .data-tbl .val { width: 36%; font-weight: bold; color: #333; }

    /* ─── Score Box ─── */
    .score-box {
        background: #f7f9d9;
        border-radius: 10px;
        padding: 4px;
        margin: 10px 0 16px 0;
    }
    .score-box td { text-align: center; padding: 10px 4px; border: none; }
    .score-label { font-size: 9pt; color: #555; font-weight: bold; margin: 0; }
    .score-value { font-size: 18pt; font-weight: bold; color: #000; margin: 4px 0 0 0; }
    .score-value-sm { font-size: 13pt; font-weight: bold; }

    /* ─── Descriptive Box ─── */
    .desc-box {
        background: #dbeafe; border-radius: 10px;
        padding: 12px; margin: 10px 0 16px 0; text-align: center;
    }
    .desc-title { font-size: 14pt; font-weight: bold; color: #1e3a8a; }
    .desc-sub { font-size: 9pt; color: #64748b; margin-top: 3px; }

    /* ─── Alert ─── */
    .alert { background: #fef2f2; border: 2px solid #fca5a5; border-radius: 8px; padding: 8px 12px; margin-bottom: 8px; color: #991b1b; font-weight: bold; font-size: 9.5pt; text-align: {{ $align }}; }

    /* ─── Section Container ─── */
    .insp-section {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        margin-bottom: 14px;
        overflow: hidden;
    }
    .insp-header {
        background: #1e3a8a;
        color: #fff;
        padding: 7px 14px;
        font-size: 10.5pt;
        font-weight: bold;
        text-align: {{ $align }};
    }
    .insp-header-en { font-size: 9pt; opacity: 0.85; margin-{{ $isRtl ? 'right' : 'left' }}: 4px; }

    /* ─── Question Rows ─── */
    .q-tbl { width: 100%; }
    .q-tbl td {
        padding: 7px 10px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 9.5pt;
        text-align: {{ $align }};
        vertical-align: top;
    }
    .q-tbl tr:last-child td { border-bottom: none; }
    .q-label { color: #1e293b; font-weight: bold; }
    .q-answer { color: #374155; }
    .q-remark { color: #888; font-size: 8.5pt; font-style: italic; margin-top: 2px; }
    .pill { display: inline; padding: 2px 8px; border-radius: 10px; font-size: 9pt; font-weight: bold; }
    .p-hi { background: #dcfce7; color: #166534; }
    .p-md { background: #fef9c3; color: #854d0e; }
    .p-lo { background: #fee2e2; color: #991b1b; }
    .crit-tag { color: #ef4444; font-weight: bold; font-size: 8.5pt; }
    .crit-fail { background: #fef2f2; color: #dc2626; font-size: 8.5pt; font-weight: bold; padding: 1px 5px; border-radius: 3px; }
    .na { color: #aaa; font-size: 8pt; }
    .check-y { color: #16a34a; font-weight: bold; }
    .check-n { color: #dc2626; font-weight: bold; }

    /* ─── QR ─── */
    .qr-box {
        text-align: center;
        margin: 14px 0 6px 0;
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
    }
    .qr-label { font-size: 8pt; color: #666; margin-top: 4px; }

    /* ─── Notes ─── */
    .notes-box {
        background: #f7f9d9;
        padding: 12px 16px;
        border-radius: 10px;
        margin-top: 10px;
        font-size: 9.5pt;
        color: #444;
        text-align: {{ $align }};
    }
    .notes-box strong { color: #1a1a2e; font-size: 10.5pt; }

    /* ─── Footer ─── */
    .footer {
        text-align: center;
        font-size: 8pt;
        color: #777;
        padding: 12px 0 0 0;
        margin-top: 10px;
    }
</style>
</head>
<body>

{{-- ═══════ HEADER ═══════ --}}
<div class="header">
    <table>
    <tr>
        @if($isRtl)
        <td style="width:26%;border:none">
            <div class="header-ref-lbl">رقم المرجع</div>
            <div class="header-ref-val" dir="ltr" style="unicode-bidi:embed">{{ $inspection->reference_number }}</div>
            <div class="header-ref-date" dir="ltr" style="unicode-bidi:embed">{{ $inspection->completed_at ? $inspection->completed_at->format('Y-m-d') : now()->format('Y-m-d') }}</div>
        </td>
        <td class="header-title" style="border:none">{{ $company['name'] }}</td>
        <td style="width:90px;text-align:{{ $alignOpp }};border:none">
            @if($logoBase64)<img src="{{ $logoBase64 }}" style="max-width:50px;max-height:50px"><br>@endif
            <span class="header-logo">Auto Check</span>
        </td>
        @else
        <td style="width:90px;border:none">
            @if($logoBase64)<img src="{{ $logoBase64 }}" style="max-width:50px;max-height:50px"><br>@endif
            <span class="header-logo">Auto Check</span>
        </td>
        <td class="header-title" style="border:none">{{ $company['name'] }}</td>
        <td style="width:26%;border:none">
            <div class="header-ref-lbl">Reference</div>
            <div class="header-ref-val">{{ $inspection->reference_number }}</div>
            <div class="header-ref-date">{{ $inspection->completed_at ? $inspection->completed_at->format('Y-m-d') : now()->format('Y-m-d') }}</div>
        </td>
        @endif
    </tr>
    </table>
</div>

{{-- ═══════ TEMPLATE BANNER (icon on left in RTL) ═══════ --}}
<table class="tpl-banner">
<tr>
    @if($isRtl)
    <td class="tpl-name" style="border:none">{{ $inspection->template->name ?? '—' }}</td>
    <td style="width:36px;text-align:{{ $alignOpp }};border:none"><div class="tpl-icon-box">📋</div></td>
    @else
    <td style="width:36px;border:none"><div class="tpl-icon-box">📋</div></td>
    <td class="tpl-name" style="border:none">{{ $inspection->template->name ?? '—' }}</td>
    @endif
</tr>
</table>

{{-- ═══════ VEHICLE INFO ═══════ --}}
<table class="data-tbl">
<tr>
    <td class="lbl">{{ $isRtl ? 'المركبة' : 'Vehicle' }}</td>
    <td class="val" colspan="3"><span dir="ltr" style="unicode-bidi:embed">{{ $vehicleStr }}</span></td>
</tr>
<tr>
    <td class="lbl">{{ $isRtl ? 'اللوحة' : 'Plate' }}</td>
    <td class="val"><span dir="ltr" style="unicode-bidi:embed">{{ $inspection->vehicle->license_plate ?? '—' }}</span></td>
    <td class="lbl">{{ $isRtl ? 'الشاسيه' : 'VIN' }}</td>
    <td class="val" style="font-size:9pt"><span dir="ltr" style="unicode-bidi:embed">{{ $inspection->vehicle->vin ?? '—' }}</span></td>
</tr>
<tr>
    <td class="lbl">{{ $isRtl ? 'الكيلومتر' : 'Mileage' }}</td>
    <td class="val"><span dir="ltr" style="unicode-bidi:embed">{{ $inspection->vehicle->mileage ? 'km ' . number_format($inspection->vehicle->mileage) : '—' }}</span></td>
    <td class="lbl">{{ $isRtl ? 'المالك' : 'Owner' }}</td>
    <td class="val">{{ $inspection->vehicle->owner_name ?? '—' }}</td>
</tr>
</table>

{{-- ═══════ INSPECTION INFO ═══════ --}}
<table class="data-tbl">
<tr>
    <td class="lbl">{{ $isRtl ? 'الفاحص' : 'Inspector' }}</td>
    <td class="val">{{ $inspection->inspector->name ?? '—' }}</td>
    <td class="lbl">{{ $isRtl ? 'الحالة' : 'Status' }}</td>
    <td class="val" style="color:{{ $inspection->status->value === 'completed' ? '#16a34a' : '#333' }}">{{ $inspection->status_label }}</td>
</tr>
<tr>
    <td class="lbl">{{ $isRtl ? 'بدأ في' : 'Started' }}</td>
    <td class="val"><span dir="ltr" style="unicode-bidi:embed">{{ $inspection->started_at ? $inspection->started_at->format('H:i Y-m-d') : '—' }}</span></td>
    <td class="lbl">{{ $isRtl ? 'اكتمل في' : 'Completed' }}</td>
    <td class="val"><span dir="ltr" style="unicode-bidi:embed">{{ $inspection->completed_at ? $inspection->completed_at->format('H:i Y-m-d') : '—' }}</span></td>
</tr>
</table>

{{-- ═══════ SCORE / DESCRIPTIVE ═══════ --}}
@if($isScored)

    @if($inspection->has_critical_failure)
    <div class="alert">⚠ {{ $isRtl ? 'تحذير: يوجد إخفاق حرج في هذه المركبة يتطلب إصلاحاً فورياً.' : 'WARNING: Critical failures detected.' }}</div>
    @endif

    <table class="score-box">
    <tr>
        <td style="width:25%">
            <div class="score-label">{{ $isRtl ? 'الدرجة الكلية' : 'Total Score' }}</div>
            <div class="score-value">{{ number_format($inspection->total_score ?? 0, 1) }}</div>
        </td>
        <td style="width:25%">
            <div class="score-label">{{ $isRtl ? 'النسبة' : 'Percentage' }}</div>
            <div class="score-value" dir="ltr" style="unicode-bidi:embed">{{ number_format($inspection->percentage ?? 0, 1) }}%</div>
        </td>
        <td style="width:25%">
            <div class="score-label">{{ $isRtl ? 'التقييم' : 'Grade' }}</div>
            <div class="score-value-sm" style="color:{{ $gradeBg }}">{{ $gradeLabel ?: '—' }}</div>
        </td>
        <td style="width:25%">
            <div class="score-label">{{ $isRtl ? 'إخفاء حرج' : 'Critical' }}</div>
            <div class="score-value-sm">{{ $inspection->has_critical_failure ? ($isRtl ? 'نعم' : 'Yes') : ($isRtl ? 'لا' : 'No') }}</div>
        </td>
    </tr>
    </table>

@else

    <div class="desc-box">
        <div class="desc-title">📝 {{ $isRtl ? 'تقرير فحص وصفي' : 'Descriptive Inspection Report' }}</div>
        <div class="desc-sub">{{ $isRtl ? 'هذا الفحص وصفي — لا يتضمن درجات أو تقييمات' : 'Descriptive — no scores or grades' }}</div>
    </div>

@endif

{{-- ═══════ SECTIONS ═══════ --}}
@foreach($sectionResults as $sectionId => $data)
    @php $section = $data['section']; $results = $data['results']; @endphp

    <div class="insp-section">
        <div class="insp-header">
            {{ $isRtl ? 'القسم ' . ($arNums[$loop->index] ?? $loop->iteration) : 'Section ' . $loop->iteration }}
            <span class="insp-header-en">{{ $section->name }}</span>
        </div>

        <table class="q-tbl">
        @foreach($results as $item)
            @php
                $question = $item['question'];
                $result = $item['result'];
                $media = $item['media'];
                $qType = is_object($question->type) ? $question->type->value : $question->type;
                $isScorable = $isScored && !in_array($qType, ['text', 'photo']) && $question->max_score > 0;
                $score = $result?->score ?? 0;
                $maxScore = $question->max_score;
                $pct = ($isScorable && $maxScore > 0) ? ($score / $maxScore) * 100 : -1;
                $pc = $pct >= 75 ? 'p-hi' : ($pct >= 50 ? 'p-md' : 'p-lo');
            @endphp
            <tr>
                <td style="width:{{ $isScored ? '50%' : '60%' }}">
                    <div class="q-label">
                        {{ $question->label }}
                        @if($isScored && $question->is_critical) <span class="crit-tag">{{ $isRtl ? '★ حرج' : '★ CRIT' }}</span> @endif
                    </div>
                    @if($result?->remarks)
                        <div class="q-remark">💬 {{ $result->remarks }}</div>
                    @endif
                </td>
                <td style="width:{{ $isScored ? '30%' : '40%' }}">
                    @if($result)
                        @if($qType === 'checkbox')
                            @if($result->answer == '1')
                                <span class="check-y">✓ {{ $isRtl ? 'نعم' : 'Yes' }}</span>
                            @else
                                <span class="check-n">✗ {{ $isRtl ? 'لا' : 'No' }}</span>
                            @endif
                        @elseif($qType === 'photo')
                            <span class="na">{{ $isRtl ? '📷 صور مرفقة' : '📷 Photos attached' }}</span>
                        @else
                            <span class="q-answer">{{ $result->answer ?? '—' }}</span>
                        @endif
                        @if($isScored && $result->is_critical_fail)
                            <br><span class="crit-fail">{{ $isRtl ? '⚠ إخفاق' : '⚠ FAIL' }}</span>
                        @endif
                    @else
                        <span class="na">—</span>
                    @endif
                </td>
                @if($isScored)
                <td style="width:20%;text-align:center">
                    @if($isScorable && $result)
                        <span class="pill {{ $pc }}" dir="ltr" style="unicode-bidi:embed">{{ number_format($score, 1) }}/{{ intval($maxScore) }}</span>
                    @elseif(!$isScorable)
                        <span class="na">—</span>
                    @else —
                    @endif
                </td>
                @endif
            </tr>
        @endforeach
        </table>
    </div>
@endforeach

{{-- ═══════ NOTES ═══════ --}}
@if($inspection->notes)
<div class="notes-box">
    <strong>{{ $isRtl ? 'ملاحظات الفاحص:' : 'Inspector Notes:' }}</strong><br>
    {{ $inspection->notes }}
</div>
@endif

@if($company['notes'])
<div class="notes-box" style="margin-top:6px">
    <strong>{{ $isRtl ? 'ملاحظات عامة:' : 'General Notes:' }}</strong><br>
    {{ $company['notes'] }}
</div>
@endif

{{-- ═══════ QR CODE (Verification) ═══════ --}}
@php
    $qrUrl = ($shareUrl ?? null) ?: url('/share/' . ($inspection->share_token ?? $inspection->reference_number));
@endphp
<div class="qr-box">
    <barcode code="{{ $qrUrl }}" type="QR" class="barcode" size="1.1" error="M" />
    <div class="qr-label">{{ $isRtl ? 'امسح للتحقق من صحة التقرير' : 'Scan to verify this report' }}</div>
    <div style="font-size:7pt;color:#999;margin-top:2px" dir="ltr">{{ $inspection->reference_number }}</div>
</div>

{{-- ═══════ FOOTER ═══════ --}}
<div class="footer">
    {{ $isRtl ? 'تم إنشاء التقرير بتاريخ' : 'Generated on' }}
    <span dir="ltr" style="unicode-bidi:embed">{{ now()->format('d-m-Y') }} | {{ now()->format('H:i') }}</span>
    | <span dir="ltr" style="unicode-bidi:embed">{{ $inspection->reference_number }}</span>
    | {{ $company['name'] }}
</div> --}}

</body>
</html>
@php
    $isRtl = $lang === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
    $alignOpp = $isRtl ? 'left' : 'right';
    $isScored = $inspection->template->isScored();

    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeColor = $inspection->grade_color ?? '#6b7280';
    $gradeMap = ['excellent'=>'#10b981','good'=>'#3b82f6','needs_attention'=>'#f59e0b','critical'=>'#ef4444'];
    $gradeBg = $gradeMap[$gradeStr] ?? '#6b7280';
    $gradeTxt = $gradeStr === 'needs_attention' ? '#1a1a2e' : '#ffffff';
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
    .vid { display: inline; background: #eff6ff; color: #1e40af; padding: 1px 4px; border-radius: 3px; font-size: 7pt; }

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

{{-- ===== GRADE (scored only) ===== --}}
@if($isScored && $gradeStr)
<div class="grade">
    <div class="grade-l">{{ $gradeLabel }}</div>
    <div class="grade-s">{{ $isRtl ? 'النسبة' : 'Score' }}: {{ number_format($inspection->percentage, 1) }}% @if($inspection->has_critical_failure) — {{ $isRtl ? 'إخفاق حرج' : 'CRITICAL FAILURE' }} @endif</div>
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
    <td class="vl" colspan="3">{{ $inspection->vehicle->year ?? '' }} {{ $inspection->vehicle->make ?? '' }} {{ $inspection->vehicle->model ?? '' }} @if($inspection->vehicle->color)({{ $inspection->vehicle->color }})@endif</td>
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
    <td><div class="st-lbl">{{ $isRtl ? 'الدرجة الكلية' : 'TOTAL SCORE' }}</div><div class="st-val">{{ number_format($inspection->total_score, 1) }}</div></td>
    <td><div class="st-lbl">{{ $isRtl ? 'النسبة' : 'PERCENTAGE' }}</div><div class="st-val">{{ number_format($inspection->percentage, 1) }}%</div></td>
    <td><div class="st-lbl">{{ $isRtl ? 'التقييم' : 'GRADE' }}</div><div class="st-val" style="color:{{ $gradeColor }}">{{ $gradeLabel }}</div></td>
    <td><div class="st-lbl">{{ $isRtl ? 'إخفاق حرج' : 'CRITICAL' }}</div><div class="st-val">{{ $inspection->has_critical_failure ? ($isRtl ? 'نعم' : 'Yes') : ($isRtl ? 'لا' : 'No') }}</div></td>
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
        <th style="width:{{ $isScored ? '16' : '22' }}%">{{ $isRtl ? 'الإجابة' : 'Answer' }}</th>
        @if($isScored)<th style="width:12%">{{ $isRtl ? 'الدرجة' : 'Score' }}</th>@endif
        <th style="width:16%">{{ $isRtl ? 'ملاحظات' : 'Remarks' }}</th>
        <th style="width:24%">{{ $isRtl ? 'مرفقات' : 'Media' }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($results as $item)
        @php
            $question = $item['question'];
            $result = $item['result'];
            $media = $item['media'];
            $qType = is_object($question->type) ? $question->type->value : $question->type;
            $isScorable = !in_array($qType, ['text', 'photo', 'video']) && $question->max_score > 0 && $question->weight > 0;
            $score = $result?->score ?? 0;
            $maxScore = $question->max_score;
            $pct = ($isScorable && $maxScore > 0) ? ($score / $maxScore) * 100 : -1;
            $pc = $pct >= 75 ? 'p-hi' : ($pct >= 50 ? 'p-md' : 'p-lo');
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                {{ $question->label }}
                @if($isScored && $question->is_critical) <span class="crit">{{ $isRtl ? '★ حرج' : '★ CRIT' }}</span> @endif
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
                    @if($isScored && $result->is_critical_fail) <br><span class="crit">{{ $isRtl ? '⚠ إخفاق' : '⚠ FAIL' }}</span> @endif
                @else —
                @endif
            </td>
            @if($isScored)
            <td>
                @if($isScorable && $result)
                    <span class="pill {{ $pc }}">{{ number_format($score, 1) }} / {{ intval($maxScore) }}</span>
                @elseif(!$isScorable)
                    <span class="na">{{ $isRtl ? 'توثيق' : 'N/A' }}</span>
                @else —
                @endif
            </td>
            @endif
            <td>
                @if($result && $result->remarks) <span class="rmk">{{ $result->remarks }}@if($result->remarks_score !== null) <span style="display:inline-block;padding:0 4px;border-radius:3px;font-size:7pt;font-weight:bold;background:{{ $result->remarks_score >= 7 ? '#fee2e2' : ($result->remarks_score >= 4 ? '#fef3c7' : '#dcfce7') }};color:{{ $result->remarks_score >= 7 ? '#dc2626' : ($result->remarks_score >= 4 ? '#d97706' : '#16a34a') }}">⭐{{ $result->remarks_score }}/10</span>@endif</span> @else — @endif
            </td>
            <td>
                @foreach($media as $m)
                    @if($m['type'] === 'image')
                        <img src="{{ $m['src'] }}" style="max-width:100px;max-height:70px;border:1px solid #e5e7eb;border-radius:3px;margin:1px">
                    @else
                        <span class="vid">🎬 {{ \Illuminate\Support\Str::limit($m['name'], 20) }}</span>
                    @endif
                @endforeach
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
@if($company['notes'])
<div class="notes">
    <div class="notes-t">{{ $isRtl ? 'ملاحظات عامة' : 'General Notes' }}</div>
    <div class="notes-b">{{ $company['notes'] }}</div>
</div>
@endif

{{-- ===== FOOTER ===== --}}
<div class="ftr">
    {{ $isRtl ? 'تم إنشاء التقرير بتاريخ' : 'Generated on' }} {{ now()->format('Y-m-d H:i') }}
    | {{ $inspection->reference_number }}
    | {{ $company['name'] }}
    @if($company['website']) | {{ $company['website'] }} @endif
</div>

</body>
</html>