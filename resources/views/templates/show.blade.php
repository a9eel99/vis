@extends('layouts.app')
@section('title', $template->name)

@php $lang = app()->getLocale(); @endphp

@section('content')
<div class="page-header">
    <h1>{{ $template->name }}</h1>
    <div class="header-actions">
        <a href="{{ route('templates.edit', $template) }}" class="btn btn-primary">{{ __('edit') }}</a>
        <a href="{{ route('templates.index') }}" class="btn btn-secondary">{{ $lang==='ar' ? 'رجوع' : 'Back' }}</a>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">{{ __('status') }}</div>
                <div class="detail-value">
                    <span class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}">
                        {{ $template->is_active ? ($lang==='ar'?'نشط':'Active') : ($lang==='ar'?'معطل':'Inactive') }}
                    </span>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ $lang==='ar' ? 'الأقسام' : 'Sections' }}</div>
                <div class="detail-value">{{ $template->sections->count() }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ $lang==='ar' ? 'الأسئلة' : 'Questions' }}</div>
                <div class="detail-value">{{ $template->questions->count() }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ $lang==='ar' ? 'نوع الوقود' : 'Fuel Type' }}</div>
                <div class="detail-value">
                    @if($template->fuel_type)
                        <span class="badge badge-info">{{ $template->fuel_type }}</span>
                    @else
                        <span class="badge badge-secondary">{{ $lang==='ar' ? 'عام' : 'General' }}</span>
                    @endif
                </div>
            </div>
        </div>
        @if($template->description)
            <p class="mt-2 text-muted">{{ is_array($template->description) ? json_encode($template->description) : $template->description }}</p>
        @endif
    </div>
</div>

@foreach($template->sections as $section)
    <div class="card mb-2">
        <div class="card-header" style="background:var(--primary);color:var(--white);border:none;display:flex;justify-content:space-between;align-items:center">
            <h3 style="color:var(--white);display:flex;align-items:center;gap:.5rem">
                <span style="opacity:.5;font-size:.8rem">{{ $loop->iteration }}</span>
                {{ $section->name }}
            </h3>
            <span class="badge" style="background:rgba(255,255,255,.2);color:var(--white)">{{ $section->questions->count() }} {{ $lang==='ar' ? 'سؤال' : 'Q' }}</span>
        </div>
        <div class="card-body" style="padding:0">
            @if($section->description)
                <div style="padding:.75rem 1.25rem;background:var(--gray-50);border-bottom:1px solid var(--gray-100);font-size:.85rem;color:var(--gray-500)">
                    {{ is_array($section->description) ? json_encode($section->description) : $section->description }}
                </div>
            @endif
            @foreach($section->questions as $question)
                <div style="padding:.85rem 1.25rem;border-bottom:1px solid var(--gray-100);{{ $loop->last ? 'border-bottom:none' : '' }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start">
                        <div style="flex:1">
                            <div style="font-weight:650;font-size:.92rem;display:flex;align-items:center;gap:.5rem">
                                <span class="text-muted" style="font-size:.78rem">{{ $loop->iteration }}.</span>
                                {{ $question->label }}
                                @if($question->is_critical)<span class="wq-critical-tag">{{ $lang==='ar' ? 'حرج' : 'Critical' }}</span>@endif
                            </div>
                            <div style="display:flex;gap:1rem;font-size:.78rem;color:var(--gray-400);margin-top:.25rem">
                                <span>{{ $lang==='ar' ? 'النوع' : 'Type' }}: <strong style="color:var(--gray-600)">{{ ucfirst(is_object($question->type) ? $question->type->value : $question->type) }}</strong></span>
                                <span>{{ $lang==='ar' ? 'الوزن' : 'Weight' }}: <strong style="color:var(--gray-600)">{{ $question->weight }}</strong></span>
                                <span>{{ $lang==='ar' ? 'أعلى درجة' : 'Max' }}: <strong style="color:var(--gray-600)">{{ $question->max_score }}</strong></span>
                            </div>
                        </div>
                    </div>
                    @if($question->options && is_array($question->options) && count($question->options) > 0)
                        <div style="margin-top:.5rem;display:flex;gap:.3rem;flex-wrap:wrap">
                            @foreach($question->options as $opt)
                                @php
                                    $optLabel = is_array($opt) ? ($opt['label'] ?? json_encode($opt)) : (string)$opt;
                                    $optScore = is_array($opt) ? ($opt['score'] ?? null) : null;
                                @endphp
                                <span class="badge badge-secondary" style="font-size:.72rem">
                                    {{ $optLabel }}@if($optScore !== null) <span style="opacity:.6">({{ $optScore }})</span>@endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endforeach
@endsection
