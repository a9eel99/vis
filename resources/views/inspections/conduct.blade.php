@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'تنفيذ الفحص' : 'Conduct Inspection')

@section('content')
@php
    $sections = $inspection->template->sections;
    $totalSections = $sections->count();
    $lang = app()->getLocale();
    $isScored = $inspection->template->isScored();
@endphp

{{-- Wizard Header --}}
<div class="wizard-header">
    <div class="wizard-info">
        <div class="wizard-vehicle-badge">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
            <span>{{ $inspection->vehicle->full_name }}</span>
        </div>
        <div class="wizard-meta">
            <span class="wizard-ref">{{ $inspection->reference_number }}</span>
            <span class="wizard-template">{{ $inspection->template->name }}</span>
            @if(!$isScored)
                <span class="badge badge-secondary" style="font-size:.7rem">{{ $lang === 'ar' ? '📝 فحص وصفي' : '📝 Descriptive' }}</span>
            @endif
        </div>
    </div>
    @if($isScored)
    <div class="wizard-score-box">
        <div class="wizard-score-ring" id="score-ring">
            <svg viewBox="0 0 80 80">
                <circle cx="40" cy="40" r="35" fill="none" stroke="#e5e7eb" stroke-width="5"/>
                <circle cx="40" cy="40" r="35" fill="none" stroke="var(--accent)" stroke-width="5" stroke-linecap="round" stroke-dasharray="220" stroke-dashoffset="220" id="score-circle" style="transition:stroke-dashoffset .5s ease,stroke .3s"/>
            </svg>
            <div class="wizard-score-value" id="score-percentage">0%</div>
        </div>
        <div id="live-grade" class="wizard-grade">-</div>
    </div>
    @endif
</div>

{{-- Wizard Steps Indicator --}}
<div class="wizard-steps" id="wizard-steps">
    @foreach($sections as $i => $section)
    <div class="wizard-step {{ $i === 0 ? 'active' : '' }}" data-step="{{ $i }}" onclick="goToStep({{ $i }})">
        <div class="step-number">{{ $i + 1 }}</div>
        <div class="step-label">{{ $section->name }}</div>
    </div>
    @endforeach
</div>

<form method="POST" action="{{ route('inspections.submit', $inspection) }}"
          id="inspection-form"
          data-total-steps="{{ $totalSections }}"
          data-scoring-mode="{{ $inspection->template->scoring_mode ?? 'scored' }}"
          enctype="multipart/form-data">
              @csrf

    @foreach($sections as $sIdx => $section)
    <div class="wizard-panel {{ $sIdx === 0 ? 'active' : '' }}" id="panel-{{ $sIdx }}">
        <div class="wizard-panel-header">
            <div class="panel-step-badge">{{ $lang === 'ar' ? 'الخطوة' : 'Step' }} {{ $sIdx + 1 }} / {{ $totalSections }}</div>
            <h2>{{ $section->name }}</h2>
            @if($section->description)
                <p>{{ $section->description }}</p>
            @endif
        </div>

        <div class="wizard-panel-body">
            @foreach($section->questions as $qIdx => $question)
            @php $existing = $existingAnswers[$question->id] ?? null; @endphp
            <div class="wizard-question {{ $question->is_critical && $isScored ? 'critical' : '' }}">
                <div class="wq-header">
                    <span class="wq-num">{{ $qIdx + 1 }}</span>
                    <div class="wq-title">
                        {{ $question->label }}
                        @if($question->is_critical && $isScored)
                            <span class="wq-critical-tag">{{ $lang === 'ar' ? '⚠ حرج' : '⚠ Critical' }}</span>
                        @endif
                    </div>
                    @if($isScored && $question->max_score > 0 && !in_array(is_object($question->type) ? $question->type->value : $question->type, ['text', 'photo', 'video']))
                        <div class="wq-score-badge" id="qscore-{{ $question->id }}">0/{{ intval($question->max_score) }}</div>
                    @endif
                </div>

                @if($question->description)
                    <p class="wq-desc">{{ $question->description }}</p>
                @endif

                <div class="wq-body">
                    @switch($question->type->value)
                        @case('text')
                            <textarea name="answers[{{ $question->id }}][answer]" class="form-control" rows="2" placeholder="{{ $lang === 'ar' ? 'أدخل ملاحظاتك...' : 'Enter your notes...' }}">{{ $existing?->answer ?? '' }}</textarea>
                            @break

                        @case('number')
                            <div class="wq-number-input">
                                <button type="button" class="num-btn" onclick="stepNum(this,-1)">−</button>
                                <input type="number" name="answers[{{ $question->id }}][answer]" class="form-control"
                                    value="{{ $existing?->answer ?? '' }}" min="0" max="999999"
                                    step="any" placeholder="0"
                                    data-question="{{ $question->id }}" data-weight="{{ $question->weight }}" data-max="{{ $question->max_score }}"
                                    {{ $isScored ? 'oninput=updateNumScore(this)' : '' }}>
                                <button type="button" class="num-btn" onclick="stepNum(this,1)">+</button>
                            </div>
                            @break

                        @case('checkbox')
                            <div class="wq-toggle-group">
                                <input type="hidden" name="answers[{{ $question->id }}][answer]" value="0" id="hidden-{{ $question->id }}">
                                <button type="button" class="wq-toggle {{ ($existing?->answer ?? '') == '1' ? 'active' : '' }}" onclick="toggleCheck(this, '{{ $question->id }}', {{ intval($question->max_score) }})" data-question="{{ $question->id }}" data-weight="{{ $question->weight }}" data-max="{{ $question->max_score }}">
                                    <span class="toggle-icon">{{ ($existing?->answer ?? '') == '1' ? '✅' : '☐' }}</span>
                                    <span>{{ $lang === 'ar' ? 'نعم / متوفر' : 'Yes / Available' }}</span>
                                </button>
                            </div>
                            @break

                        @case('dropdown')
                            <div class="wq-options-grid">
                                <input type="hidden" name="answers[{{ $question->id }}][answer]" value="{{ $existing?->answer ?? '' }}" id="dd-{{ $question->id }}">
                                @if(is_array($question->options))
                                    @foreach($question->options as $optIdx => $opt)
                                        @php
                                            $optLabel = is_array($opt) ? ($opt['label'] ?? '') : $opt;
                                            $optScore = is_array($opt) ? ($opt['score'] ?? 0) : 0;
                                            $isSelected = ($existing?->answer ?? '') == $optLabel;
                                        @endphp
                                        <button type="button"
                                            class="wq-option {{ $isSelected ? 'selected' : '' }}"
                                            data-label="{{ $optLabel }}"
                                            data-score="{{ $optScore }}"
                                            data-question="{{ $question->id }}"
                                            data-weight="{{ $question->weight }}"
                                            data-max="{{ $question->max_score }}"
                                            onclick="selectOption(this)">
                                            @if($isScored)
                                                @if($optScore >= 8)
                                                    <span class="opt-indicator green"></span>
                                                @elseif($optScore >= 5)
                                                    <span class="opt-indicator amber"></span>
                                                @else
                                                    <span class="opt-indicator red"></span>
                                                @endif
                                            @endif
                                            {{ $optLabel }}
                                        </button>
                                    @endforeach
                                @endif

                                {{-- Custom Option Button --}}
                                <button type="button"
                                    class="wq-option wq-custom-btn {{ ($existing?->remarks_score !== null) ? 'selected' : '' }}"
                                    onclick="toggleCustomOption('{{ $question->id }}')"
                                    style="border-style:dashed">
                                    <span class="opt-indicator" style="background:var(--gray-400)"></span>
                                    ✚ {{ $lang === 'ar' ? 'حالة أخرى' : 'Other' }}
                                </button>
                            </div>

                            {{-- Custom Option Panel (hidden by default) --}}
                            <div class="wq-custom-panel" id="custom-panel-{{ $question->id }}" style="display:{{ ($existing?->remarks_score !== null) ? 'block' : 'none' }};margin-top:8px;padding:10px;background:var(--gray-50);border:1px dashed var(--gray-300);border-radius:8px">
                                <textarea id="custom-text-{{ $question->id }}" class="form-control" rows="2" placeholder="{{ $lang === 'ar' ? '📝 صف الحالة...' : '📝 Describe the condition...' }}" style="resize:vertical;font-size:.85rem;margin-bottom:8px" oninput="var dd=document.getElementById('dd-{{ $question->id }}');if(dd)dd.value=this.value">{{ ($existing?->remarks_score !== null) ? ($existing?->answer ?? '') : '' }}</textarea>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span style="font-size:.8rem;font-weight:600;white-space:nowrap">⭐ {{ $lang === 'ar' ? 'التقييم:' : 'Score:' }}</span>
                                    <input type="range" id="custom-score-{{ $question->id }}" min="0" max="{{ intval($question->max_score) }}" value="{{ $existing?->remarks_score ?? 0 }}" class="remarks-range" style="flex:1;height:6px;accent-color:var(--accent)" oninput="updateCustomScore('{{ $question->id }}', this.value, {{ intval($question->max_score) }})">
                                    <span id="custom-score-label-{{ $question->id }}" style="font-size:.85rem;font-weight:700;min-width:40px;color:var(--accent)">{{ ($existing?->remarks_score ?? 0) }}/{{ intval($question->max_score) }}</span>
                                    <input type="hidden" name="answers[{{ $question->id }}][remarks_score]" id="custom-score-input-{{ $question->id }}" value="{{ $existing?->remarks_score ?? '' }}">
                                </div>
                            </div>
                            @break

                        @case('photo')
                            <div class="wq-upload-area" id="upload-{{ $question->id }}">
                                <div class="upload-files-list" id="files-{{ $question->id }}"></div>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <button type="button" class="wq-upload-btn" onclick="document.getElementById('fcam-{{ $question->id }}').click()">
                                        <span>📷</span>
                                        {{ $lang === 'ar' ? 'التقاط صورة' : 'Take Photo' }}
                                    </button>
                                    <button type="button" class="wq-upload-btn" onclick="document.getElementById('finput-{{ $question->id }}').click()" style="background:var(--gray-100);color:var(--gray-700)">
                                        <span>🖼️</span>
                                        {{ $lang === 'ar' ? 'من المعرض' : 'Gallery' }}
                                    </button>
                                </div>
                                {{-- Camera input --}}
                                <input type="file" id="fcam-{{ $question->id }}"
                                    accept="image/*" capture="environment"
                                    style="display:none"
                                    onchange="addFiles(this, '{{ $question->id }}')">
                                {{-- Gallery input --}}
                                <input type="file" id="finput-{{ $question->id }}"
                                    name="media[{{ $question->id }}][]"
                                    accept="image/*"
                                    multiple style="display:none"
                                    onchange="addFiles(this, '{{ $question->id }}')">
                            </div>
                            @break
                    @endswitch

                    {{-- Hidden score field — only for scored mode --}}
                    @if($isScored && $question->max_score > 0 && !in_array(is_object($question->type) ? $question->type->value : $question->type, ['text', 'photo', 'video']))
                        <input type="hidden" name="answers[{{ $question->id }}][score]" class="score-input" value="{{ $existing?->score ?? '' }}" data-weight="{{ $question->weight }}" data-max-score="{{ $question->max_score }}" id="score-{{ $question->id }}">
                    @endif

                    {{-- Remarks (available for all question types) --}}
                    <div class="wq-remarks">
                        <input type="text" name="answers[{{ $question->id }}][remarks]" class="form-control" placeholder="{{ $lang === 'ar' ? '💬 ملاحظات...' : '💬 Remarks...' }}" value="{{ $existing?->remarks ?? '' }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Navigation --}}
        <div class="wizard-nav">
            @if($sIdx > 0)
                <button type="button" class="btn btn-secondary btn-lg" onclick="goToStep({{ $sIdx - 1 }})">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="{{ $lang === 'ar' ? '9 18 15 12 9 6' : '15 18 9 12 15 6' }}"/></svg>
                    {{ $lang === 'ar' ? 'السابق' : 'Previous' }}
                </button>
            @else
                <div></div>
            @endif

            <div class="wizard-nav-center">
                <span class="text-muted" style="font-size:.82rem">{{ $lang === 'ar' ? 'الخطوة' : 'Step' }} {{ $sIdx + 1 }} {{ $lang === 'ar' ? 'من' : 'of' }} {{ $totalSections }}</span>
            </div>

            @if($sIdx < $totalSections - 1)
                <button type="button" class="btn btn-primary btn-lg" onclick="goToStep({{ $sIdx + 1 }})">
                    {{ $lang === 'ar' ? 'التالي' : 'Next' }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="{{ $lang === 'ar' ? '15 18 9 12 15 6' : '9 18 15 12 9 6' }}"/></svg>
                </button>
            @else
                <button type="submit" class="btn btn-success btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ $lang === 'ar' ? 'إرسال وإنهاء الفحص' : 'Submit & Complete' }}
                </button>
            @endif
        </div>
    </div>
    @endforeach
</form>
@endsection

@section('modals')
<script src="{{ asset('js/inspection-wizard.js') }}"></script>
@endsection