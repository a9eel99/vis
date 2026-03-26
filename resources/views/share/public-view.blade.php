@php
    $isRtl = $lang === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeColor = $inspection->grade_color ?? '#6b7280';
    $gradeMap = ['excellent'=>'#10b981','good'=>'#3b82f6','needs_attention'=>'#f59e0b','critical'=>'#ef4444'];
    $gradeBg = $gradeMap[$gradeStr] ?? '#6b7280';
    $gradeTxt = $gradeStr === 'needs_attention' ? '#1a1a2e' : '#fff';
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isRtl ? 'تقرير الفحص' : 'Inspection Report' }} — {{ $inspection->reference_number }}</title>
    <style>
        :root { --primary:#1e3a5f; --success:#10b981; --warning:#f59e0b; --danger:#ef4444; --gray-50:#f9fafb; --gray-100:#f3f4f6; --gray-200:#e5e7eb; --gray-500:#6b7280; --gray-700:#374151; --gray-900:#1a1a2e; --radius:10px; }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;background:#f0f2f5;color:var(--gray-900);direction:{{ $dir }};line-height:1.6;min-height:100vh}
        .container{max-width:800px;margin:0 auto;padding:20px 16px}
        .header{background:var(--primary);color:#fff;padding:24px;border-radius:var(--radius);margin-bottom:16px;text-align:center}
        .header img{max-height:60px;margin-bottom:10px}
        .header .co-name{font-size:1.4rem;font-weight:700}
        .header .co-info{font-size:.8rem;opacity:.8;margin-top:4px}
        .header .ref{font-size:.8rem;opacity:.7;margin-top:8px}
        .grade-card{background:{{ $gradeBg }};color:{{ $gradeTxt }};padding:20px;border-radius:var(--radius);text-align:center;margin-bottom:16px}
        .grade-card .g-label{font-size:1.8rem;font-weight:800}
        .grade-card .g-score{font-size:1rem;opacity:.9;margin-top:4px}
        .card{background:#fff;border-radius:var(--radius);box-shadow:0 1px 3px rgba(0,0,0,.08);margin-bottom:16px;overflow:hidden}
        .card-title{padding:14px 20px;font-weight:700;font-size:.95rem;border-bottom:1px solid var(--gray-200);color:var(--primary)}
        .card-body{padding:16px 20px}
        .info-row{display:flex;padding:8px 0;border-bottom:1px solid var(--gray-100)}
        .info-row:last-child{border-bottom:none}
        .info-label{width:120px;flex-shrink:0;font-size:.8rem;color:var(--gray-500);font-weight:600}
        .info-value{flex:1;font-size:.9rem}
        .stats-row{display:flex;gap:12px}
        .stat-box{flex:1;text-align:center;padding:12px;background:var(--gray-50);border-radius:8px}
        .stat-box .st-lbl{font-size:.7rem;color:var(--gray-500);text-transform:uppercase;font-weight:600}
        .stat-box .st-val{font-size:1.3rem;font-weight:800;color:var(--primary);margin-top:2px}
        .sec-title{background:var(--primary);color:#fff;padding:10px 20px;font-weight:700;font-size:.9rem}
        .q-row{padding:12px 20px;border-bottom:1px solid var(--gray-100)}
        .q-row:last-child{border-bottom:none}
        .q-label{font-weight:600;font-size:.88rem;display:flex;align-items:center;gap:6px}
        .q-answer{margin-top:4px;font-size:.85rem;color:var(--gray-700)}
        .q-meta{display:flex;align-items:center;gap:8px;margin-top:4px;flex-wrap:wrap}
        .q-score{display:inline-block;padding:2px 8px;border-radius:12px;font-size:.75rem;font-weight:700}
        .s-hi{background:#d1fae5;color:#065f46}.s-md{background:#fef3c7;color:#92400e}.s-lo{background:#fee2e2;color:#991b1b}
        .q-remark{font-style:italic;color:var(--gray-500);font-size:.78rem}
        .crit-badge{background:var(--danger);color:#fff;padding:1px 6px;border-radius:4px;font-size:.7rem;font-weight:700}
        .na-badge{color:var(--gray-500);font-size:.75rem}
        .q-media{margin-top:6px;display:flex;flex-wrap:wrap;gap:6px}
        .q-media img{width:70px;height:52px;object-fit:cover;border-radius:6px;border:1px solid var(--gray-200)}
        .q-media .vid-badge{background:#eff6ff;color:#1e40af;padding:3px 8px;border-radius:6px;font-size:.72rem;text-decoration:none}
        .alert-danger{background:#fef2f2;border:2px solid var(--danger);border-radius:var(--radius);padding:12px 16px;color:#991b1b;font-weight:600;margin-bottom:16px;text-align:center}
        .dl-btn{display:block;width:100%;padding:14px;background:var(--primary);color:#fff;text-align:center;border-radius:var(--radius);text-decoration:none;font-weight:700;font-size:1rem;margin-top:16px;transition:opacity .2s}
        .dl-btn:hover{opacity:.9}
        .footer{text-align:center;padding:20px;font-size:.75rem;color:var(--gray-500)}
        @media(max-width:600px){.stats-row{flex-wrap:wrap}.stat-box{min-width:45%}.info-label{width:90px}}
    </style>
</head>
<body>
<div class="container">

    {{-- HEADER --}}
    <div class="header">
        @if($logoBase64)<img src="{{ $logoBase64 }}" alt="Logo"><br>@endif
        <div class="co-name">{{ $company['name'] }}</div>
        @if($company['address'])<div class="co-info">{{ $company['address'] }}</div>@endif
        @if($company['phone'] || $company['email'])
        <div class="co-info">@if($company['phone']){{ $company['phone'] }}@endif @if($company['email']) &bull; {{ $company['email'] }}@endif</div>
        @endif
        <div class="ref">{{ $inspection->reference_number }} &bull; {{ $inspection->completed_at?->format('Y-m-d') }}</div>
    </div>

    {{-- GRADE --}}
    @if($gradeStr)
    <div class="grade-card">
        <div class="g-label">{{ $gradeLabel }}</div>
        <div class="g-score">{{ number_format($inspection->percentage, 1) }}% @if($inspection->has_critical_failure) — {{ $isRtl ? '⚠ إخفاق حرج' : '⚠ Critical Failure' }} @endif</div>
    </div>
    @endif

    @if($inspection->has_critical_failure)
    <div class="alert-danger">⚠ {{ $isRtl ? 'تحذير: يوجد إخفاق حرج يتطلب إصلاحاً فورياً' : 'Warning: Critical failures detected' }}</div>
    @endif

    {{-- VEHICLE --}}
    <div class="card">
        <div class="card-title">{{ $isRtl ? '🚗 معلومات المركبة' : '🚗 Vehicle Information' }}</div>
        <div class="card-body">
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'المركبة' : 'Vehicle' }}</div><div class="info-value">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }} @if($inspection->vehicle->color)({{ $inspection->vehicle->color }})@endif</div></div>
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'اللوحة' : 'Plate' }}</div><div class="info-value">{{ $inspection->vehicle->license_plate ?? '—' }}</div></div>
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'الشاسيه' : 'VIN' }}</div><div class="info-value" style="font-size:.82rem">{{ $inspection->vehicle->vin ?? '—' }}</div></div>
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'الكيلومتر' : 'Mileage' }}</div><div class="info-value">{{ $inspection->vehicle->mileage ? number_format($inspection->vehicle->mileage).' km' : '—' }}</div></div>
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'المالك' : 'Owner' }}</div><div class="info-value">{{ $inspection->vehicle->owner_name ?? '—' }}</div></div>
        </div>
    </div>

    {{-- SCORE --}}
    <div class="card">
        <div class="card-body">
            <div class="stats-row">
                <div class="stat-box"><div class="st-lbl">{{ $isRtl ? 'الدرجة' : 'Score' }}</div><div class="st-val">{{ number_format($inspection->total_score, 1) }}</div></div>
                <div class="stat-box"><div class="st-lbl">{{ $isRtl ? 'النسبة' : 'Percentage' }}</div><div class="st-val">{{ number_format($inspection->percentage, 1) }}%</div></div>
                <div class="stat-box"><div class="st-lbl">{{ $isRtl ? 'التقييم' : 'Grade' }}</div><div class="st-val" style="color:{{ $gradeColor }}">{{ $gradeLabel }}</div></div>
            </div>
        </div>
    </div>

    {{-- INSPECTION INFO --}}
    <div class="card">
        <div class="card-title">{{ $isRtl ? '📋 تفاصيل الفحص' : '📋 Inspection Details' }}</div>
        <div class="card-body">
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'القالب' : 'Template' }}</div><div class="info-value">{{ $inspection->template->name ?? '—' }}</div></div>
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'الفاحص' : 'Inspector' }}</div><div class="info-value">{{ $inspection->inspector->name ?? '—' }}</div></div>
            <div class="info-row"><div class="info-label">{{ $isRtl ? 'التاريخ' : 'Date' }}</div><div class="info-value">{{ $inspection->completed_at?->format('Y-m-d H:i') ?? '—' }}</div></div>
        </div>
    </div>

    {{-- SECTIONS --}}
    @foreach($sectionResults as $sectionId => $data)
        @php $section = $data['section']; $results = $data['results']; @endphp
        <div class="card">
            <div class="sec-title" onclick="toggleSection('sec-{{ $loop->index }}')" style="cursor:pointer;display:flex;justify-content:space-between;align-items:center">
                <span>{{ $isRtl ? 'القسم' : 'Section' }} {{ $loop->iteration }} &nbsp; {{ $section->name }}</span>
                <span class="sec-arrow" id="arrow-{{ $loop->index }}" style="transition:transform .3s;font-size:1.2rem">▼</span>
            </div>
            <div class="sec-body" id="sec-{{ $loop->index }}" style="display:block">
            @foreach($results as $item)
                @php
                    $question = $item['question']; $result = $item['result'];
                    $qType = is_object($question->type) ? $question->type->value : $question->type;
                    $isScorable = !in_array($qType, ['text','photo','video']) && $question->max_score > 0;
                    $score = $result?->score ?? 0; $maxScore = $question->max_score;
                    $pct = ($isScorable && $maxScore > 0) ? ($score/$maxScore)*100 : -1;
                    $sc = $pct >= 75 ? 's-hi' : ($pct >= 50 ? 's-md' : 's-lo');
                @endphp
                <div class="q-row">
                    <div class="q-label">{{ $question->label }} @if($question->is_critical)<span class="crit-badge">{{ $isRtl ? 'حرج' : 'CRIT' }}</span>@endif</div>
                    <div class="q-answer">
                        @if($result)
                            @if($qType === 'checkbox') {{ $result->answer == '1' ? ($isRtl ? '✅ نعم' : '✅ Yes') : ($isRtl ? '☐ لا' : '☐ No') }}
                            @elseif(in_array($qType, ['photo','video'])) <span class="na-badge">{{ $isRtl ? '📎 مرفق' : '📎 Attached' }}</span>
                            @else {{ $result->answer ?? '—' }}
                            @endif
                        @else <span class="na-badge">—</span>
                        @endif
                    </div>
                    <div class="q-meta">
                        @if($isScorable && $result)<span class="q-score {{ $sc }}">{{ number_format($score,1) }} / {{ intval($maxScore) }}</span>
                        @elseif(!$isScorable)<span class="na-badge">{{ $isRtl ? 'توثيق' : 'Doc' }}</span>@endif
                        @if($result?->remarks)<span class="q-remark">💬 {{ $result->remarks }}@if($result->remarks_score !== null) <span style="display:inline-block;padding:1px 6px;border-radius:8px;font-size:.7rem;font-weight:700;margin-right:4px;margin-left:4px;background:{{ $result->remarks_score >= 7 ? 'rgba(239,68,68,.12)' : ($result->remarks_score >= 4 ? 'rgba(245,158,11,.12)' : 'rgba(34,197,94,.12)') }};color:{{ $result->remarks_score >= 7 ? '#ef4444' : ($result->remarks_score >= 4 ? '#f59e0b' : '#22c55e') }}">⭐ {{ $result->remarks_score }}/10</span>@endif</span>@endif
                        @if($result?->is_critical_fail)<span class="crit-badge">{{ $isRtl ? '⚠ إخفاق' : '⚠ FAIL' }}</span>@endif
                    </div>
                    @if($result && $result->media && $result->media->count())
                    <div class="q-media">
                        @foreach($result->media as $m)
                            @if($m->isImage())<a href="{{ $m->url }}" target="_blank"><img src="{{ $m->url }}" alt="{{ $m->original_name }}"></a>
                            @elseif($m->isVideo())<a href="{{ $m->url }}" target="_blank" class="vid-badge">🎬 {{ \Illuminate\Support\Str::limit($m->original_name, 20) }}</a>@endif
                        @endforeach
                    </div>
                    @endif
                </div>
            @endforeach
            </div>{{-- /sec-body --}}
        </div>
    @endforeach

    @if($inspection->notes)
    <div class="card"><div class="card-title">{{ $isRtl ? '📝 ملاحظات الفاحص' : '📝 Notes' }}</div><div class="card-body">{{ $inspection->notes }}</div></div>
    @endif

    <a href="{{ route('share.pdf', $token) }}" class="dl-btn">📄 {{ $isRtl ? 'تحميل التقرير PDF' : 'Download PDF Report' }}</a>

    <div class="footer">
        {{ $company['name'] }} @if($company['website']) &bull; {{ $company['website'] }} @endif @if($company['phone']) &bull; {{ $company['phone'] }} @endif
        <br>{{ $isRtl ? 'تم إنشاء التقرير بتاريخ' : 'Generated on' }} {{ $inspection->completed_at?->format('Y-m-d H:i') }}
    </div>

</div>
<script>
function toggleSection(id) {
    const body = document.getElementById(id);
    const idx = id.replace('sec-', '');
    const arrow = document.getElementById('arrow-' + idx);
    if (body.style.display === 'none') {
        body.style.display = 'block';
        arrow.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        arrow.style.transform = 'rotate(-90deg)';
    }
}
</script>
</body>
</html>