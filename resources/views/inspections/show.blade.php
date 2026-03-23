@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'تفاصيل الفحص' : 'Inspection Details')

@php
    $lang = app()->getLocale();
    $isScored = $inspection->template->isScored();
    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeColor = $inspection->grade_color ?? '#6b7280';
    $gradeMap = ['excellent'=>'success','good'=>'primary','needs_attention'=>'warning','critical'=>'danger'];
    $gradeBadge = $gradeMap[$gradeStr] ?? 'secondary';
@endphp

@section('content')

{{-- Hidden Banner (Super Admin only) --}}
@if($inspection->is_hidden)
<div class="alert-warning" style="border-radius:8px;padding:12px 20px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <span style="font-weight:700;color:#92400e">🙈 {{ $lang === 'ar' ? 'هذا الفحص مخفي' : 'This inspection is hidden' }}</span>
        @if($inspection->hidden_reason)
            <span style="color:#92400e;font-size:.85rem"> — {{ $inspection->hidden_reason }}</span>
        @endif
        <div style="font-size:.75rem;color:#b45309;margin-top:2px">
            {{ $lang === 'ar' ? 'مخفي بواسطة' : 'Hidden by' }}: {{ $inspection->hiddenByUser?->name ?? '—' }}
            · {{ $inspection->hidden_at?->format('Y-m-d H:i') }}
        </div>
    </div>
    <form method="POST" action="{{ route('inspections.toggleHidden', $inspection) }}" style="margin:0">
        @csrf
        <button type="submit" class="btn btn-sm btn-warning" style="white-space:nowrap">
            👁️ {{ $lang === 'ar' ? 'إظهار الفحص' : 'Show Inspection' }}
        </button>
    </form>
</div>
@endif

{{-- ===== HERO BANNER ===== --}}
<div class="ins-hero">
    @if($inspection->status->value === 'completed')
    @php
        if (!$inspection->share_token) {
            $inspection->share_token = bin2hex(random_bytes(32));
            $inspection->saveQuietly();
        }
        $cust = $inspection->vehicle?->customer;
        $ownerPhone = $cust?->phone ?? $inspection->vehicle?->owner_phone;
        $ownerEmail = $cust?->email ?? $inspection->vehicle?->owner_email;
        $ownerName = $cust?->name ?? $inspection->vehicle?->owner_name ?? '';
        $waPhone = $ownerPhone ? preg_replace('/[^0-9]/', '', $ownerPhone) : null;
        if ($waPhone && str_starts_with($waPhone, '0')) { $waPhone = '962' . substr($waPhone, 1); }
        $shareUrl = url('/share/' . $inspection->share_token);
        $pdfUrl = route('share.pdf', $inspection->share_token);
    @endphp
    @endif

    <div class="ins-hero-top">
        <div>
            <div class="ins-hero-title">
                {{ $lang === 'ar' ? 'تفاصيل الفحص' : 'Inspection Details' }}
                @if(!$isScored)
                    <span style="font-size:.7rem;background:rgba(255,255,255,.2);padding:2px 10px;border-radius:20px;margin-inline-start:8px">{{ $lang === 'ar' ? '📝 وصفي' : '📝 Descriptive' }}</span>
                @endif
            </div>
            <div class="ins-hero-ref">{{ $inspection->reference_number }}</div>
        </div>
        <div class="ins-hero-actions">
            @if($inspection->status->value === 'completed')
                {{-- Payment Status --}}
                @if($inspection->payment_status === 'paid')
                    <span class="hbtn" style="background:#dcfce7;color:#16a34a;cursor:default;font-weight:700">
                        💰 {{ $lang === 'ar' ? 'مدفوع' : 'Paid' }} — {{ number_format($inspection->price - $inspection->discount, 2) }} {{ $lang === 'ar' ? 'د.أ' : 'JOD' }}
                    </span>
                @elseif($inspection->price > 0)
                    <form method="POST" action="{{ route('finance.markPaid', $inspection->id) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="hbtn" style="background:#f59e0b;color:#fff">
                            💵 {{ $lang === 'ar' ? 'قبض ' . number_format($inspection->price, 2) . ' د.أ' : 'Collect ' . number_format($inspection->price, 2) . ' JOD' }}
                        </button>
                    </form>
                @endif

                <a href="{{ route('reports.pdf', $inspection) }}" class="hbtn hbtn-pdf">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="M9 15h6"/></svg>
                    PDF
                </a>
                <a href="{{ $shareUrl }}" target="_blank" class="hbtn hbtn-preview">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ $lang === 'ar' ? 'معاينة' : 'Preview' }}
                </a>
                <button type="button" class="hbtn hbtn-link" onclick="copyShareLink()" id="share-btn"
                    data-copied="{{ $lang === 'ar' ? '✅ تم النسخ!' : '✅ Copied!' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                    {{ $lang === 'ar' ? 'نسخ الرابط' : 'Copy Link' }}
                </button>
                <input type="hidden" id="share-url" value="{{ $shareUrl }}">
                @if($waPhone)
                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode(($lang === 'ar' ? 'مرحباً '.$ownerName."،\nتقرير فحص مركبتك جاهز:\n" : 'Hi '.$ownerName.",\nYour report:\n").$shareUrl) }}" target="_blank" class="hbtn hbtn-wa">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    {{ $lang === 'ar' ? 'واتساب' : 'WhatsApp' }}
                </a>
                @endif
                @if($ownerEmail)
                <a href="mailto:{{ $ownerEmail }}?subject={{ urlencode(($lang === 'ar' ? 'تقرير فحص - ' : 'Report - ').$inspection->reference_number) }}&body={{ urlencode(($lang === 'ar' ? "مرحباً ".$ownerName."\n\nتقرير فحص مركبتك:\n".$shareUrl."\n\nPDF: ".$pdfUrl : "Hi ".$ownerName."\n\nYour report:\n".$shareUrl."\n\nPDF: ".$pdfUrl)) }}" class="hbtn hbtn-email">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg>
                    {{ $lang === 'ar' ? 'إيميل' : 'Email' }}
                </a>
                @endif
            @endif
            @can('conduct inspections')
            @if(in_array($inspection->status->value, ['draft','in_progress']))
                <a href="{{ route('inspections.conduct', $inspection) }}" class="hbtn hbtn-continue">{{ $lang === 'ar' ? 'استكمال الفحص' : 'Continue' }}</a>
            @endif
            @endcan

            {{-- Hide/Show Toggle (Super Admin only) --}}
            @if(auth()->user()->hasRole('Super Admin') && !$inspection->is_hidden)
                <button type="button" class="hbtn hbtn-continue" onclick="document.getElementById('hide-modal').style.display='flex'">
                    🙈 {{ $lang === 'ar' ? 'إخفاء' : 'Hide' }}
                </button>
            @endif
        </div>
    </div>

    {{-- Score Ring + Stats --}}
    <div class="score-ring-wrap">
        @if($isScored)
            @php
                $pct = $inspection->percentage ?? 0;
                $circumference = 2 * 3.14159 * 38;
                $offset = $circumference - ($pct / 100) * $circumference;
                $ringColor = $pct >= 75 ? '#6ee7b7' : ($pct >= 50 ? '#fcd34d' : '#fca5a5');
            @endphp
            <div class="score-ring">
                <svg viewBox="0 0 88 88" width="100%" height="100%">
                    <circle class="score-ring-bg" cx="44" cy="44" r="38"/>
                    <circle class="score-ring-fill" cx="44" cy="44" r="38"
                        stroke="{{ $ringColor }}"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="score-ring-text">
                    <span class="score-ring-value">{{ $pct ? $pct.'%' : '—' }}</span>
                    <span class="score-ring-sub">{{ $lang === 'ar' ? 'النتيجة' : 'Score' }}</span>
                </div>
            </div>
        @endif

        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-label">{{ __('status') }}</span>
                <span class="hero-badge hero-badge-{{ $inspection->status->color() }}">{{ $inspection->status->label() }}</span>
            </div>
            @if($isScored)
            <div class="hero-stat">
                <span class="hero-stat-label">{{ $lang === 'ar' ? 'التقييم' : 'Grade' }}</span>
                @if($gradeStr)
                <span class="hero-badge hero-badge-{{ $gradeBadge }}">{{ $lang === 'ar' ? __($gradeStr) : $gradeLabel }}</span>
                @else <span class="hero-badge hero-badge-secondary">—</span> @endif
            </div>
            <div class="hero-stat">
                <span class="hero-stat-label">{{ $lang === 'ar' ? 'إخفاق حرج' : 'Critical' }}</span>
                <span class="hero-badge {{ $inspection->has_critical_failure ? 'hero-badge-danger' : 'hero-badge-success' }}">
                    {{ $inspection->has_critical_failure ? ($lang === 'ar' ? 'نعم' : 'Yes') : ($lang === 'ar' ? 'لا' : 'No') }}
                </span>
            </div>
            @else
            <div class="hero-stat">
                <span class="hero-stat-label">{{ $lang === 'ar' ? 'النمط' : 'Mode' }}</span>
                <span class="hero-badge hero-badge-secondary">{{ $lang === 'ar' ? 'فحص وصفي' : 'Descriptive' }}</span>
            </div>
            @endif
            <div class="hero-stat">
                <span class="hero-stat-label">{{ $lang === 'ar' ? 'المالك' : 'Owner' }}</span>
                <span class="hero-stat-value">{{ $inspection->vehicle?->owner_name ?? '—' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ===== INFO CARDS ===== --}}
<div class="ins-info-grid">
    <div class="ins-info-card">
        <div class="ins-info-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
            {{ __('vehicle') }}
        </div>
        <div class="ins-info-body">
            <div><div class="ins-detail-label">{{ __('vehicle') }}</div><div class="ins-detail-value">{{ $inspection->vehicle->full_name }}</div></div>
            <div><div class="ins-detail-label">{{ __('plate_number') }}</div><div class="ins-detail-value ins-detail-mono">{{ $inspection->vehicle->license_plate ?? '—' }}</div></div>
            <div><div class="ins-detail-label">{{ __('vin') }}</div><div class="ins-detail-value ins-detail-mono">{{ $inspection->vehicle->vin ?? '—' }}</div></div>
            <div><div class="ins-detail-label">{{ __('owner_name') }}</div><div class="ins-detail-value">{{ $inspection->vehicle->owner_name ?? '—' }}</div></div>
        </div>
    </div>
    <div class="ins-info-card">
        <div class="ins-info-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            {{ $lang === 'ar' ? 'تفاصيل الفحص' : 'Inspection Info' }}
        </div>
        <div class="ins-info-body">
            <div><div class="ins-detail-label">{{ $lang === 'ar' ? 'القالب' : 'Template' }}</div><div class="ins-detail-value">{{ $inspection->template->name }}</div></div>
            <div><div class="ins-detail-label">{{ $lang === 'ar' ? 'الفاحص' : 'Inspector' }}</div><div class="ins-detail-value">{{ $inspection->inspector?->name ?? '—' }}</div></div>
            <div><div class="ins-detail-label">{{ $lang === 'ar' ? 'بدأ في' : 'Started' }}</div><div class="ins-detail-value">{{ $inspection->started_at?->format('Y-m-d H:i') ?? '—' }}</div></div>
            <div><div class="ins-detail-label">{{ $lang === 'ar' ? 'اكتمل في' : 'Completed' }}</div><div class="ins-detail-value">{{ $inspection->completed_at?->format('Y-m-d H:i') ?? '—' }}</div></div>
        </div>
    </div>
</div>

{{-- ===== SECTIONS ===== --}}
@foreach($inspection->template->sections as $section)
@php
    $sectionResults = $inspection->results->whereIn('question_id', $section->questions->pluck('id'));
    if ($isScored) {
        $sectionMax = $section->questions->where('max_score', '>', 0)->sum('max_score');
        $sectionScore = $sectionResults->sum('score');
        $sectionPct = $sectionMax > 0 ? round($sectionScore / $sectionMax * 100) : null;
    } else {
        $sectionPct = null;
    }
@endphp
<div class="ins-section">
    <div class="ins-section-header" onclick="toggleSec('isec-{{ $loop->index }}')">
        <div class="ins-section-title">
            <span class="ins-section-num">{{ $loop->iteration }}</span>
            {{ $section->name }}
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            @if($isScored && $sectionPct !== null)
            <span style="font-size:.82rem;font-weight:700;color:{{ $sectionPct >= 75 ? '#6ee7b7' : ($sectionPct >= 50 ? '#fcd34d' : '#fca5a5') }}">{{ $sectionPct }}%</span>
            @endif
            <span class="ins-section-arrow" id="isec-arrow-{{ $loop->index }}">▼</span>
        </div>
    </div>
    <div id="isec-{{ $loop->index }}">
        @foreach($section->questions as $question)
        @php $result = $inspection->results->where('question_id', $question->id)->first(); @endphp
        <div class="ins-q-row">
            <div style="flex:1;min-width:0">
                <div class="ins-q-label">
                    {{ $question->label }}
                    @if($isScored && $question->is_critical) <span class="ins-q-critical">{{ $lang === 'ar' ? 'حرج' : 'CRITICAL' }}</span> @endif
                </div>
                <div class="ins-q-answer">
                    @if($result)
                        @if($question->type->value === 'checkbox')
                            @if(($result->answer ?? '0') == '1')
                                <span style="color:var(--success)">✓ {{ $lang==='ar'?'نعم':'Yes' }}</span>
                            @else
                                <span style="color:var(--danger)">✗ {{ $lang==='ar'?'لا':'No' }}</span>
                            @endif
                        @else
                            {{ $result->answer ?? '—' }}
                        @endif
                    @else
                        <span style="color:var(--gray-300)">{{ $lang === 'ar' ? 'لم يُجب' : 'No answer' }}</span>
                    @endif
                </div>
                @if($result?->remarks)
                <div class="ins-q-remark">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    {{ $result->remarks }}
                </div>
                @endif
                @if($isScored && $result?->is_critical_fail)
                <span class="ins-q-critical" style="display:inline-block;margin-top:4px">{{ $lang === 'ar' ? '⚠ إخفاق حرج' : '⚠ Critical Fail' }}</span>
                @endif
                @if($result && $result->media && $result->media->count())
                <div class="ins-q-media">
                    @foreach($result->media as $media)
                        <div style="position:relative;display:inline-block">
                        @if($media->isImage())
                        <a href="{{ $media->url }}" target="_blank"><img src="{{ $media->url }}" alt="{{ $media->original_name }}"></a>
                        @else
                        <a href="{{ $media->url }}" target="_blank" class="ins-q-remark" style="padding:4px 10px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:6px;text-decoration:none;color:var(--primary)">📎 {{ \Illuminate\Support\Str::limit($media->original_name, 20) }}</a>
                        @endif
                        @can('edit inspections')
                        <form method="POST" action="{{ route('inspections.deleteMedia', $media->id) }}" style="position:absolute;top:-6px;{{ $lang === 'ar' ? 'left' : 'right' }}:-6px;margin:0" onsubmit="return confirm('{{ $lang === 'ar' ? 'حذف هذه الصورة؟' : 'Delete this image?' }}')">
                            @csrf @method('DELETE')
                            <button type="submit" style="width:20px;height:20px;border-radius:50%;background:#ef4444;color:#fff;border:2px solid #fff;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;box-shadow:0 1px 3px rgba(0,0,0,.3)" title="{{ $lang === 'ar' ? 'حذف' : 'Delete' }}">✕</button>
                        </form>
                        @endcan
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
            @if($isScored)
                @php $qTypeVal = is_object($question->type) ? $question->type->value : $question->type; @endphp
                @if($question->max_score > 0 && $result && !in_array($qTypeVal, ['text', 'photo']))
                @php $scorePct = $question->max_score > 0 ? ($result->score / $question->max_score * 100) : 0; @endphp
                <div class="ins-q-score">
                    <div class="ins-q-score-val" style="color:{{ $scorePct >= 75 ? 'var(--success)' : ($scorePct >= 50 ? 'var(--warning)' : 'var(--danger)') }}">{{ $result->score }}</div>
                    <div class="ins-q-score-max">/ {{ intval($question->max_score) }}</div>
                </div>
                @endif
            @endif
        </div>
        @endforeach
    </div>
</div>
@endforeach

@if($inspection->notes)
<div class="ins-info-card" style="margin-top:12px">
    <div class="ins-info-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
        {{ __('notes') }}
    </div>
    <div style="padding:16px 20px;font-size:.9rem;color:var(--gray-700);line-height:1.6">{{ $inspection->notes }}</div>
</div>
@endif

@can('delete inspections')
@if($inspection->status->value !== 'completed')
<div style="margin-top:20px;text-align:{{ $lang === 'ar' ? 'left' : 'right' }}">
    <form id="del-inspection" action="{{ route('inspections.destroy', $inspection) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('del-inspection', '{{ $inspection->reference_number }}')">{{ __('delete') }}</button>
</div>
@endif
@endcan
@endsection

@section('modals')
@include('partials.delete-modal')

{{-- Hide Inspection Modal (Super Admin only) --}}
@if(auth()->user()->hasRole('Super Admin'))
<div id="hide-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div class="card" style="max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div class="card-header"><h3 style="color:#92400e;margin:0">🙈 {{ $lang === 'ar' ? 'إخفاء الفحص' : 'Hide Inspection' }}</h3></div>
        <div class="card-body">
            <p style="color:var(--gray-500);font-size:.85rem;margin:0 0 16px">
                {{ $lang === 'ar' ? 'الفحص المخفي لن يظهر لأحد غيرك، ولن يحتسب بالإحصائيات أو التقارير.' : 'Hidden inspections are invisible to all users except you, and excluded from all stats and reports.' }}
            </p>
            <form method="POST" action="{{ route('inspections.toggleHidden', $inspection) }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px">
                    <label class="form-label">{{ $lang === 'ar' ? 'السبب (اختياري)' : 'Reason (optional)' }}</label>
                    <input type="text" name="hidden_reason" class="form-control" placeholder="{{ $lang === 'ar' ? 'مثال: فحص تجريبي' : 'e.g. Test inspection' }}">
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('hide-modal').style.display='none'">{{ $lang === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary">🙈 {{ $lang === 'ar' ? 'إخفاء' : 'Hide' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script src="{{ asset('js/inspection-show.js') }}"></script>
@endsection