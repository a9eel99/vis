@extends('layouts.app')
@section('title', __('templates'))

@php
    $lang = app()->getLocale();
    $fuelMap = ['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid','lpg'=>$lang==='ar'?'غاز':'LPG'];
@endphp

@section('content')
<div class="page-header">
    <h1>{{ __('templates') }}</h1>
    <div class="header-actions">
        @can('create templates')
        <a href="{{ route('templates.create') }}" class="btn btn-primary">+ {{ $lang === 'ar' ? 'قالب جديد' : 'New Template' }}</a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $lang === 'ar' ? 'اسم القالب' : 'Template Name' }}</th>
                    <th>{{ $lang === 'ar' ? 'النمط' : 'Mode' }}</th>
                    <th>{{ $lang === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</th>
                    <th>{{ $lang === 'ar' ? 'الأقسام' : 'Sections' }}</th>
                    <th>💰 {{ $lang === 'ar' ? 'السعر' : 'Price' }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $t)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $t->name }}</div>
                        @if($t->description)
                        <div style="font-size:.78rem;color:var(--gray-400);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ is_array($t->description) ? implode(', ', $t->description) : $t->description }}</div>
                        @endif
                    </td>
                    <td>
                        @if($t->isScored())
                            <span class="badge badge-info" style="font-size:.7rem">📊 {{ $lang === 'ar' ? 'مُقيّم' : 'Scored' }}</span>
                        @else
                            <span class="badge badge-secondary" style="font-size:.7rem">📝 {{ $lang === 'ar' ? 'وصفي' : 'Descriptive' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($t->fuel_type)
                            <span class="badge badge-info">{{ $fuelMap[$t->fuel_type] ?? $t->fuel_type }}</span>
                        @else
                            <span class="badge badge-secondary">{{ $lang === 'ar' ? 'عام' : 'General' }}</span>
                        @endif
                    </td>
                    <td>{{ $t->sections->count() }}</td>
                    <td>
                        @if($t->price > 0)
                            <span style="font-weight:700;color:var(--success)">{{ number_format($t->price, 2) }}</span>
                            <span style="font-size:.7rem;color:var(--gray-400)">{{ $lang === 'ar' ? 'د.أ' : 'JOD' }}</span>
                        @else
                            <span style="color:var(--gray-400)">—</span>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $t->is_active ? 'success' : 'danger' }}">{{ $t->is_active ? ($lang==='ar'?'نشط':'Active') : ($lang==='ar'?'معطل':'Inactive') }}</span></td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('templates.show', $t) }}" class="btn btn-ghost btn-sm">{{ __('view') }}</a>
                            @can('edit templates')
                            <a href="{{ route('templates.edit', $t) }}" class="btn btn-secondary btn-sm">{{ __('edit') }}</a>
                            @endcan
                            @can('create templates')
                            @if(Route::has('templates.duplicate'))
                            <form action="{{ route('templates.duplicate', $t) }}" method="POST" style="display:inline">@csrf<button type="submit" class="btn btn-sm btn-ghost">{{ $lang === 'ar' ? 'نسخ' : 'Copy' }}</button></form>
                            @endif
                            @endcan
                            @can('delete templates')
                            <form id="del-t-{{ $t->id }}" action="{{ route('templates.destroy', $t) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
                            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-t-{{ $t->id }}', '{{ $t->name }}')">🗑️</button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">{{ $lang === 'ar' ? 'لا توجد قوالب' : 'No templates' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('modals')
@include('partials.delete-modal')
@endsection