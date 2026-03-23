@extends('layouts.app')
@section('title', __('inspections'))

@php $lang = app()->getLocale(); @endphp
 
@section('content')
<div class="page-header">
    <h1>{{ __('inspections') }}</h1>
    <div class="header-actions">
        @can("create inspections")
        @can('create inspections')
        <a href="{{ route('inspections.create') }}" class="btn btn-primary">+ {{ __('new_inspection') }}</a>
        @endcan
        @endcan
    </div>
</div>

<div class="card mb-2">
    <div class="card-body" style="padding:.75rem 1rem">
        <form method="GET" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="flex:1;min-width:200px" placeholder="{{ __('search') }}..." value="{{ request('search') }}">
            <select name="status" class="form-control filter-select" onchange="this.form.submit()">
                <option value="">{{ $lang === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                @foreach(['draft' => $lang === 'ar' ? 'مسودة' : 'Draft', 'in_progress' => $lang === 'ar' ? 'قيد الإنجاز' : 'In Progress', 'completed' => $lang === 'ar' ? 'مكتمل' : 'Completed'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="grade" class="form-control filter-select" onchange="this.form.submit()">
                <option value="">{{ $lang === 'ar' ? 'كل التقييمات' : 'All Grades' }}</option>
                @foreach(['excellent' => $lang === 'ar' ? 'ممتاز' : 'Excellent', 'good' => $lang === 'ar' ? 'جيد' : 'Good', 'needs_attention' => $lang === 'ar' ? 'يحتاج اهتمام' : 'Needs Attention', 'critical' => $lang === 'ar' ? 'حرج' : 'Critical'] as $val => $label)
                    <option value="{{ $val }}" {{ request('grade') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $lang === 'ar' ? 'الرقم المرجعي' : 'Reference' }}</th>
                    <th>{{ __('vehicle') }}</th>
                    <th>{{ $lang === 'ar' ? 'الفاحص' : 'Inspector' }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ $lang === 'ar' ? 'الدرجة' : 'Score' }}</th>
                    <th>{{ $lang === 'ar' ? 'التقييم' : 'Grade' }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $ins)
                <tr>
                    <td class="font-mono" style="font-size:.82rem">{{ $ins->reference_number }}</td>
                    <td>{{ $ins->vehicle?->full_name ?? '-' }}</td>
                    <td>{{ $ins->inspector?->name ?? '-' }}</td>
                    <td><span class="badge badge-{{ $ins->status->color() }}">{{ $ins->status->label() }}</span></td>
                    <td style="font-weight:700">{{ $ins->percentage ? $ins->percentage . '%' : '-' }}</td>
                    <td>
                        @if($ins->grade)
                            @php $g = is_object($ins->grade) ? $ins->grade->value : $ins->grade; @endphp
                            <span class="badge badge-{{ $g === 'excellent' ? 'success' : ($g === 'good' ? 'primary' : ($g === 'needs_attention' ? 'warning' : 'danger')) }}">
                                {{ $lang === 'ar' ? __($g) : ucfirst(str_replace('_',' ',$g)) }}
                            </span>
                        @else - @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)">{{ $ins->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="action-buttons">
                            @can('conduct inspections')
                            @if(in_array($ins->status->value, ['draft','in_progress']))
                                <a href="{{ route('inspections.conduct', $ins) }}" class="btn btn-success btn-sm">{{ $lang === 'ar' ? 'استكمال' : 'Continue' }}</a>
                            @endif
                            @endcan
                            <a href="{{ route('inspections.show', $ins) }}" class="btn btn-ghost btn-sm">{{ __('view') }}</a>
                            @can('delete inspections')
                            @if($ins->status->value !== 'completed')
                                <form id="del-i-{{ $ins->id }}" action="{{ route('inspections.destroy', $ins) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
                                <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-i-{{ $ins->id }}', '{{ $ins->reference_number }}')">🗑️</button>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted" style="padding:2rem">{{ $lang === 'ar' ? 'لا توجد فحوصات' : 'No inspections found' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($inspections->hasPages())
<div style="margin-top:1rem">{{ $inspections->appends(request()->query())->links() }}</div>
@endif
@endsection

@section('modals')
@include('partials.delete-modal')

{{-- Create Inspection Modal --}}
<div class="modal" id="create-inspection-modal">
    <div class="modal-header">
        <h3>🔍 {{ $lang==='ar' ? 'فحص جديد' : 'New Inspection' }}</h3>
        <button class="modal-close" onclick="closeModal('create-inspection-modal')">✕</button>
    </div>
    <form method="POST" action="{{ route('inspections.store') }}">
        @csrf
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">{{ __('vehicle') }} <span class="required">*</span></label>
                <select name="vehicle_id" id="m-vehicle" class="form-control" required>
                    <option value="">-- {{ $lang==='ar' ? 'اختر المركبة' : 'Select Vehicle' }} --</option>
                    @foreach($vehicles ?? [] as $v)
                        <option value="{{ $v->id }}" data-fuel="{{ $v->fuel_type ?? '' }}">{{ $v->year }} {{ $v->make }} {{ $v->model }}{{ $v->license_plate ? ' ('.$v->license_plate.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div id="m-fuel-info" style="display:none;margin-bottom:1rem;padding:.5rem .75rem;background:var(--gray-100);border-radius:8px;font-size:.82rem;color:var(--info)"></div>
            <div class="form-group">
                <label class="form-label">
                    {{ $lang==='ar' ? 'قالب الفحص' : 'Template' }} <span class="required">*</span>
                    <span id="m-auto-label" style="display:none;font-size:.72rem;color:var(--success);font-weight:400;margin-right:.35rem">✓ {{ $lang === 'ar' ? 'تلقائي' : 'Auto' }}</span>
                </label>
                <select name="template_id" id="m-template" class="form-control" required>
                    <option value="">-- {{ $lang==='ar' ? 'اختر القالب' : 'Select Template' }} --</option>
                    @foreach($templates ?? [] as $t)
                        <option value="{{ $t->id }}" data-fuel="{{ $t->fuel_type ?? '' }}">{{ $t->name }}@if($t->fuel_type) [{{ $t->fuel_type }}]@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('notes') }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ $lang==='ar' ? 'ملاحظات...' : 'Notes...' }}"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('create-inspection-modal')">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary">{{ $lang==='ar' ? 'إنشاء وبدء الفحص' : 'Create & Start' }}</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/inspections-index.js') }}"></script>
@endsection