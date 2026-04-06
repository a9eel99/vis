@php
    $isRtl = $lang === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $pct = (float)($inspection->percentage ?? 0);
    $gradeLetter = match(true) {
        $pct >= 90 => 'A',
        $pct >= 75 => 'B',
        $pct >= 60 => 'C',
        $pct >= 45 => 'D',
        default    => 'F',
    };
    $gradeMap = ['excellent'=>'#10b981','good'=>'#3b82f6','needs_attention'=>'#f59e0b','critical'=>'#ef4444'];
    $gradeBg  = $gradeMap[$gradeStr] ?? '#6b7280';
    $isPass   = !$inspection->has_critical_failure;
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $isRtl ? 'تقرير الفحص' : 'Inspection Report' }} — {{ $inspection->reference_number }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
  --orange:  #f59e0b;
  --orange-d:#d97706;
  --green:   #10b981;
  --red:     #ef4444;
  --blue:    #3b82f6;
  --dark:    #0f172a;
  --dark2:   #1e293b;
  --gray:    #64748b;
  --light:   #f8fafc;
  --border:  #e2e8f0;
  --white:   #ffffff;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;background:#f1f5f9;color:var(--dark);direction:{{$dir}};line-height:1.6;min-height:100vh}
.wrap{max-width:820px;margin:0 auto;padding:16px}

/* ── TOP BAR ── */
.top-bar{background:var(--dark);color:white;padding:10px 20px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center}
.brand{font-size:18px;font-weight:900;letter-spacing:-0.5px}
.brand em{color:var(--orange);font-style:normal}
.top-meta{font-size:10px;color:#94a3b8}
.top-meta strong{color:white}

/* ── COVER CARD ── */
.cover-card{background:linear-gradient(145deg,var(--dark) 0%,var(--dark2) 60%,#0c1526 100%);border-radius:0 0 16px 16px;padding:24px 20px 20px;color:white;margin-bottom:14px;position:relative;overflow:hidden}
.cover-card::before{content:'';position:absolute;top:-40px;left:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(245,158,11,.15) 0%,transparent 70%);pointer-events:none}
.cover-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px}
.company-name{font-size:16px;font-weight:800}
.premium-tag{background:linear-gradient(135deg,var(--orange),var(--orange-d));color:white;padding:4px 14px;border-radius:20px;font-size:10px;font-weight:800}

.car-row{display:flex;gap:16px;align-items:flex-start;margin-bottom:16px}
.car-img-box{width:160px;height:105px;background:rgba(255,255,255,.06);border:2px solid rgba(255,255,255,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;font-size:44px;opacity:.6}
.car-img-box img{width:100%;height:100%;object-fit:cover;opacity:1;border-radius:10px}
.car-info h1{font-size:22px;font-weight:900;line-height:1.1;margin-bottom:3px}
.car-sub{color:rgba(255,255,255,.5);font-size:11px;margin-bottom:12px}
.chips{display:flex;flex-wrap:wrap;gap:5px}
.chip{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:4px 10px;font-size:9px;display:flex;flex-direction:column;align-items:center;min-width:64px}
.chip-lbl{color:rgba(255,255,255,.4);font-size:7px;margin-bottom:1px}
.chip-val{font-weight:700;font-size:10px}

/* ── SCORE PANEL ── */
.score-panel{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.grade-circle{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:900;flex-shrink:0}
.g-A{background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 0 18px rgba(16,185,129,.4)}
.g-B{background:linear-gradient(135deg,#2563eb,#3b82f6);box-shadow:0 0 18px rgba(59,130,246,.4)}
.g-C{background:linear-gradient(135deg,var(--orange-d),var(--orange));box-shadow:0 0 18px rgba(245,158,11,.4)}
.g-D{background:linear-gradient(135deg,#ea580c,#f97316);box-shadow:0 0 18px rgba(249,115,22,.4)}
.g-F{background:linear-gradient(135deg,#dc2626,var(--red));box-shadow:0 0 18px rgba(239,68,68,.4)}
.score-info{flex:1;min-width:120px}
.score-lbl{font-size:8px;color:rgba(255,255,255,.4);margin-bottom:2px}
.score-num{font-size:32px;font-weight:900;color:var(--orange);line-height:1;margin-bottom:4px}
.score-bar-bg{height:5px;background:rgba(255,255,255,.1);border-radius:10px;overflow:hidden}
.score-bar-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,var(--orange),var(--green))}
.result-badge{padding:6px 14px;border-radius:20px;font-weight:800;font-size:13px;display:inline-block;margin-bottom:6px}
.pass{background:rgba(16,185,129,.2);color:#6ee7b7;border:1px solid rgba(16,185,129,.25)}
.fail{background:rgba(239,68,68,.2);color:#fca5a5;border:1px solid rgba(239,68,68,.25)}
.mkt-lbl{font-size:8px;color:rgba(255,255,255,.4)}
.mkt-val{font-size:14px;font-weight:900;color:var(--orange)}

/* ── SECTIONS OVERVIEW ── */
.overview-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:14px}
.ov-card{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:9px 12px;display:flex;align-items:center;gap:8px}
.ov-icon{font-size:16px}
.ov-name{font-size:11px;font-weight:700;flex:1}
.ov-st{width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0}
.ok{background:rgba(16,185,129,.12);color:var(--green)}
.warn{background:rgba(245,158,11,.12);color:var(--orange)}
.bad{background:rgba(239,68,68,.1);color:var(--red)}

/* ── SECTION CARD ── */
.section-wrap{background:var(--white);border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:12px}
.sec-header{background:var(--dark2);color:white;padding:13px 18px;display:flex;align-items:center;gap:10px;cursor:pointer}
.sec-icon{width:38px;height:38px;background:rgba(245,158,11,.15);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.sec-title-txt{font-size:15px;font-weight:800;flex:1}
.sec-sub-txt{font-size:9px;color:#94a3b8;margin-top:1px}
.sec-badge-wrap{padding:3px 10px;border-radius:20px;font-size:9px;font-weight:700}
.sb-ok{background:rgba(16,185,129,.2);color:#6ee7b7;border:1px solid rgba(16,185,129,.25)}
.sb-warn{background:rgba(245,158,11,.2);color:#fde68a;border:1px solid rgba(245,158,11,.25)}
.sb-bad{background:rgba(239,68,68,.2);color:#fca5a5;border:1px solid rgba(239,68,68,.25)}
.sec-arrow{color:rgba(255,255,255,.5);font-size:12px;transition:transform .3s}

.sec-body{padding:12px 16px}
.checks-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px}
.check-card{border:1px solid var(--border);border-radius:9px;padding:9px 11px;background:var(--white)}
.check-card.c-warn{border-color:rgba(245,158,11,.3);background:#fffbeb}
.check-card.c-bad{border-color:rgba(239,68,68,.25);background:#fff1f2}
.check-card.c-ok{border-color:rgba(16,185,129,.2)}
.check-top{display:flex;align-items:flex-start;gap:7px}
.check-ic{width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0;margin-top:1px}
.ic-ok{background:rgba(16,185,129,.12);color:var(--green)}
.ic-warn{background:rgba(245,158,11,.12);color:var(--orange)}
.ic-bad{background:rgba(239,68,68,.1);color:var(--red)}
.check-name{font-size:11px;font-weight:700;flex:1;line-height:1.3}
.check-answer{font-size:10px;color:var(--gray);margin-top:3px}
.check-remark{font-size:9.5px;color:#92400e;background:rgba(245,158,11,.1);border-radius:5px;padding:3px 7px;margin-top:4px;line-height:1.5}
.check-remark-red{font-size:9.5px;color:#991b1b;background:rgba(239,68,68,.07);border-radius:5px;padding:3px 7px;margin-top:4px;line-height:1.5}
.check-score{display:inline-block;padding:1px 8px;border-radius:10px;font-size:9px;font-weight:700;margin-top:3px}
.cs-hi{background:#d1fae5;color:#065f46}.cs-md{background:#fef3c7;color:#92400e}.cs-lo{background:#fee2e2;color:#991b1b}
.media-row{display:flex;flex-wrap:wrap;gap:5px;margin-top:6px}
.media-row a img{width:64px;height:48px;object-fit:cover;border-radius:6px;border:1px solid var(--border)}
.vid-link{background:#eff6ff;color:#1e40af;padding:3px 8px;border-radius:6px;font-size:9px;text-decoration:none}
.crit-tag{background:var(--red);color:white;padding:1px 6px;border-radius:4px;font-size:8px;font-weight:700;margin-right:4px}
.doc-tag{color:var(--gray);font-size:9px}

/* ── PEOPLE CARDS ── */
.people-row{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:14px}
.people-card{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:8px;padding:9px 12px}
.people-lbl{font-size:8px;color:rgba(255,255,255,.5);margin-bottom:2px}
.people-val{font-weight:700;font-size:11px;color:white}

/* ── NOTES SECTION ── */
.notes-section{background:var(--white);border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:12px}
.notes-header{background:var(--dark2);color:white;padding:13px 18px;display:flex;align-items:center;gap:10px}
.notes-body{padding:14px 16px}
.note-item{display:flex;align-items:flex-start;gap:8px;padding:7px 10px;border-radius:8px;border:1px solid var(--border);margin-bottom:5px;background:var(--white)}
.note-item:nth-child(even){background:var(--light)}
.note-dot{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:9px;flex-shrink:0;margin-top:1px}
.nd-warn{background:rgba(245,158,11,.15);color:var(--orange)}
.nd-bad{background:rgba(239,68,68,.15);color:var(--red)}
.nd-ok{background:rgba(16,185,129,.15);color:var(--green)}
.note-txt{font-size:11px;flex:1;line-height:1.5}

/* ── DL BUTTON ── */
.dl-btn{display:block;width:100%;padding:14px;background:linear-gradient(135deg,var(--orange-d),var(--orange));color:white;text-align:center;border-radius:12px;text-decoration:none;font-weight:800;font-size:14px;margin:16px 0;box-shadow:0 4px 16px rgba(245,158,11,.35);transition:opacity .2s}
.dl-btn:hover{opacity:.9}
.footer-txt{text-align:center;padding:16px;font-size:10px;color:var(--gray);border-top:1px solid var(--border);margin-top:8px}

@media(max-width:600px){
  .car-row{flex-wrap:wrap}
  .car-img-box{width:100%;height:140px}
  .checks-grid,.overview-grid,.people-row{grid-template-columns:1fr}
  .score-panel{flex-direction:column;align-items:flex-start}
}
</style>
</head>
<body>
<div class="wrap">

{{-- ── TOP BAR ── --}}
<div class="top-bar">
  <div class="brand">auto<em>score</em></div>
  <div style="color:var(--orange);font-weight:800;font-size:10px">{{ $isRtl ? 'الفحص الشامل PREMIUM' : 'PREMIUM INSPECTION' }}</div>
  <div class="top-meta">{{ $isRtl ? 'رقم التقرير:' : 'Report#:' }} <strong>{{ $inspection->reference_number }}</strong></div>
</div>

{{-- ── COVER ── --}}
<div class="cover-card">
  <div class="cover-head">
    <div>
      <div style="color:rgba(255,255,255,.4);font-size:9px;margin-bottom:2px">{{ $inspection->completed_at?->format('Y-m-d') }}</div>
      <div class="company-name">{{ $company['name'] }}</div>
    </div>
    <div class="premium-tag">PREMIUM 200+</div>
  </div>

  <div class="car-row">
    <div class="car-img-box">
      @if($logoBase64)<img src="{{ $logoBase64 }}" alt="logo">@else 🚗 @endif
    </div>
    <div class="car-info">
      <h1>{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</h1>
      <div class="car-sub">{{ $inspection->vehicle->fuel_type ?? '' }}{{ $inspection->vehicle->color ? ' / '.$inspection->vehicle->color : '' }}</div>
      <div class="chips">
        <div class="chip"><div class="chip-lbl">{{ $isRtl ? 'الشاسيه' : 'VIN' }}</div><div class="chip-val" style="font-size:7px;font-family:monospace">{{ $inspection->vehicle->vin ?? '—' }}</div></div>
        <div class="chip"><div class="chip-lbl">{{ $isRtl ? 'اللوحة' : 'Plate' }}</div><div class="chip-val">{{ $inspection->vehicle->license_plate ?? '—' }}</div></div>
        @if($inspection->vehicle->mileage)<div class="chip"><div class="chip-lbl">{{ $isRtl ? 'العداد' : 'KM' }}</div><div class="chip-val">{{ number_format($inspection->vehicle->mileage) }}</div></div>@endif
        @if($inspection->vehicle->engine_size ?? $inspection->vehicle->fuel_type)<div class="chip"><div class="chip-lbl">{{ $isRtl ? 'المحرك' : 'Engine' }}</div><div class="chip-val">{{ $inspection->vehicle->engine_size ?? $inspection->vehicle->fuel_type }}</div></div>@endif
      </div>
    </div>
  </div>

  <div class="score-panel">
    <div class="grade-circle g-{{ $gradeLetter }}" style="color:white">{{ $gradeLetter }}</div>
    <div class="score-info">
      <div class="score-lbl">{{ $isRtl ? 'النسبة الإجمالية' : 'Overall Score' }}</div>
      <div class="score-num">{{ round($pct) }}<span style="font-size:16px">%</span></div>
      <div class="score-bar-bg"><div class="score-bar-fill" style="width:{{ $pct }}%"></div></div>
    </div>
    <div style="text-align:center">
      <div class="result-badge {{ $isPass ? 'pass' : 'fail' }}">{{ $isPass ? '✓ '.($isRtl ? 'ناجحة' : 'Pass') : '✗ '.($isRtl ? 'مرفوضة' : 'Fail') }}</div>
      <div class="mkt-lbl">{{ $isRtl ? 'التقييم' : 'Grade' }}</div>
      <div class="mkt-val">{{ $gradeLabel }}</div>
    </div>
  </div>

  <div class="people-row">
    <div class="people-card"><div class="people-lbl">{{ $isRtl ? 'اسم المالك' : 'Owner' }}</div><div class="people-val">{{ $inspection->vehicle->owner_name ?? '—' }}</div></div>
    <div class="people-card"><div class="people-lbl">{{ $isRtl ? 'الفاحص' : 'Inspector' }}</div><div class="people-val">{{ $inspection->inspector->name ?? '—' }}</div></div>
  </div>
</div>

{{-- ── CRITICAL ALERT ── --}}
@if($inspection->has_critical_failure)
<div style="background:#fff1f2;border:2px solid var(--red);border-radius:12px;padding:12px 16px;color:#991b1b;font-weight:700;margin-bottom:12px;text-align:center">
  ⚠ {{ $isRtl ? 'تحذير: يوجد إخفاق حرج يتطلب إصلاحاً فورياً' : 'Warning: Critical failures detected — immediate repair required' }}
</div>
@endif

{{-- ── SECTIONS OVERVIEW ── --}}
@php
$sectionStatuses = [];
foreach($sectionResults as $sid => $sdata) {
    $st = 'ok';
    foreach($sdata['results'] as $item) {
        $r = $item['result'];
        if($r && $r->is_critical_fail) { $st = 'bad'; break; }
        if($r && $r->remarks) $st = $st === 'bad' ? 'bad' : 'warn';
    }
    $sectionStatuses[$sid] = $st;
}
@endphp
<div class="overview-grid" style="margin-bottom:12px">
  @foreach($sectionResults as $sid => $sdata)
  @php $st = $sectionStatuses[$sid]; $secName = $sdata['section']->name; @endphp
  <div class="ov-card">
    <div class="ov-icon">📋</div>
    <div class="ov-name">{{ $secName }}</div>
    <div class="ov-st {{ $st }}">{{ $st==='ok' ? '✔' : ($st==='warn' ? '⚠' : '❌') }}</div>
  </div>
  @endforeach
</div>

{{-- ── SECTION CARDS ── --}}
@foreach($sectionResults as $sectionId => $data)
@php
  $section = $data['section'];
  $results = $data['results'];
  $st = $sectionStatuses[$sectionId];
  $badgeClass = $st === 'ok' ? 'sb-ok' : ($st === 'warn' ? 'sb-warn' : 'sb-bad');
  $badgeText = $st === 'ok' ? ($isRtl ? '✔ جيد' : '✔ Good') : ($st === 'warn' ? ($isRtl ? '⚠ انتباه' : '⚠ Attention') : ($isRtl ? '❌ مشاكل' : '❌ Issues'));
  $idx = $loop->index;
@endphp
<div class="section-wrap">
  <div class="sec-header" onclick="toggleSec({{ $idx }})">
    <div class="sec-icon">📋</div>
    <div style="flex:1">
      <div class="sec-title-txt">{{ $section->name }}</div>
      @if($section->description)<div class="sec-sub-txt">{{ \Illuminate\Support\Str::limit($section->description, 60) }}</div>@endif
    </div>
    <span class="sec-badge-wrap {{ $badgeClass }}">{{ $badgeText }}</span>
    <span class="sec-arrow" id="arrow-{{ $idx }}">▾</span>
  </div>

  <div class="sec-body" id="secbody-{{ $idx }}">
    <div class="checks-grid">
    @foreach($results as $item)
    @php
      $question = $item['question'];
      $result   = $item['result'];
      $qType    = is_object($question->type) ? $question->type->value : $question->type;
      $isScorable = !in_array($qType, ['text','photo','video']) && $question->max_score > 0;
      $score    = $result?->score ?? 0;
      $maxScore = $question->max_score;
      $pctQ     = ($isScorable && $maxScore > 0) ? ($score/$maxScore)*100 : -1;
      $scoreClass = $pctQ >= 75 ? 'cs-hi' : ($pctQ >= 50 ? 'cs-md' : 'cs-lo');

      // Determine status
      $qs = 'ok';
      if($result?->is_critical_fail) $qs = 'bad';
      elseif($result?->remarks) $qs = 'warn';
      elseif($isScorable && $result && $pctQ < 50) $qs = $pctQ < 30 ? 'bad' : 'warn';

      $cardClass = $qs === 'bad' ? 'c-bad' : ($qs === 'warn' ? 'c-warn' : 'c-ok');
      $icClass   = $qs === 'bad' ? 'ic-bad' : ($qs === 'warn' ? 'ic-warn' : 'ic-ok');
      $icIcon    = $qs === 'bad' ? '❌' : ($qs === 'warn' ? '⚠' : '✔');
    @endphp
    <div class="check-card {{ $cardClass }}">
      <div class="check-top">
        <div class="check-ic {{ $icClass }}">{{ $icIcon }}</div>
        <div class="check-name">
          {{ $question->label }}
          @if($question->is_critical)<span class="crit-tag">{{ $isRtl ? 'حرج' : 'CRIT' }}</span>@endif
        </div>
      </div>
      @if($result)
        @if($qType === 'checkbox')
          <div class="check-answer">{{ $result->answer == '1' ? '✅ '.($isRtl?'نعم':'Yes') : '☐ '.($isRtl?'لا':'No') }}</div>
        @elseif(in_array($qType, ['photo','video']))
          <div class="doc-tag">📎 {{ $isRtl ? 'مرفق' : 'Attached' }}</div>
        @else
          <div class="check-answer">{{ $result->answer }}</div>
        @endif
        @if($isScorable && $result)
          <div><span class="check-score {{ $scoreClass }}">{{ number_format($score,1) }} / {{ intval($maxScore) }}</span></div>
        @endif
        @if($result->remarks)
          <div class="{{ $qs === 'bad' ? 'check-remark-red' : 'check-remark' }}">● {{ $result->remarks }}</div>
        @endif
        @if($result->is_critical_fail)
          <div style="margin-top:4px"><span class="crit-tag">⚠ {{ $isRtl ? 'إخفاق حرج' : 'Critical Fail' }}</span></div>
        @endif
        @if($result->media && $result->media->count())
          <div class="media-row">
            @foreach($result->media as $m)
              @if($m->isImage())<a href="{{ $m->url }}" target="_blank"><img src="{{ $m->url }}" alt=""></a>
              @elseif($m->isVideo())<a href="{{ $m->url }}" target="_blank" class="vid-link">🎬 {{ \Illuminate\Support\Str::limit($m->original_name,18) }}</a>@endif
            @endforeach
          </div>
        @endif
      @else
        <div class="doc-tag">—</div>
      @endif
    </div>
    @endforeach
    </div>{{-- /checks-grid --}}
  </div>
</div>
@endforeach

{{-- ── INSPECTOR NOTES ── --}}
@if($inspection->notes)
<div class="notes-section">
  <div class="notes-header">
    <div class="sec-icon">📝</div>
    <div class="sec-title-txt">{{ $isRtl ? 'ملاحظات الفاحص' : 'Inspector Notes' }}</div>
  </div>
  <div style="padding:14px 16px;font-size:12px;color:var(--dark)">{{ $inspection->notes }}</div>
</div>
@endif

{{-- ── DOWNLOAD BUTTON ── --}}
<a href="{{ route('share.pdf', $token) }}" class="dl-btn">
  📄 {{ $isRtl ? 'تحميل التقرير PDF' : 'Download PDF Report' }}
</a>

<div class="footer-txt">
  {{ $company['name'] }}
  @if($company['website']) &bull; {{ $company['website'] }} @endif
  @if($company['phone']) &bull; {{ $company['phone'] }} @endif
  <br>{{ $isRtl ? 'تم إنشاء التقرير بتاريخ' : 'Generated on' }} {{ $inspection->completed_at?->format('Y-m-d H:i') }}
</div>

</div>{{-- /wrap --}}

<script>
function toggleSec(idx) {
    const body  = document.getElementById('secbody-' + idx);
    const arrow = document.getElementById('arrow-' + idx);
    const open  = body.style.display !== 'none';
    body.style.display  = open ? 'none' : 'block';
    arrow.style.transform = open ? 'rotate(-90deg)' : 'rotate(0deg)';
}
</script>
</body>
</html>