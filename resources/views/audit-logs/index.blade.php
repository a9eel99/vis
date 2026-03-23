@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'سجل النظام' : 'Audit Logs')

@php
    $lang = app()->getLocale();
    $actionLabels = [
        'inspection_created' => $lang==='ar' ? 'إنشاء فحص' : 'Inspection Created',
        'inspection_started' => $lang==='ar' ? 'بدء فحص' : 'Inspection Started',
        'inspection_completed' => $lang==='ar' ? 'اكتمال فحص' : 'Inspection Completed',
        'inspection_cancelled' => $lang==='ar' ? 'إلغاء فحص' : 'Inspection Cancelled',
        'inspection_deleted' => $lang==='ar' ? 'حذف فحص' : 'Inspection Deleted',
        'vehicle_created' => $lang==='ar' ? 'إضافة مركبة' : 'Vehicle Created',
        'vehicle_updated' => $lang==='ar' ? 'تعديل مركبة' : 'Vehicle Updated',
        'vehicle_deleted' => $lang==='ar' ? 'حذف مركبة' : 'Vehicle Deleted',
        'user_created' => $lang==='ar' ? 'إضافة مستخدم' : 'User Created',
        'user_updated' => $lang==='ar' ? 'تعديل مستخدم' : 'User Updated',
        'user_deleted' => $lang==='ar' ? 'حذف مستخدم' : 'User Deleted',
        'template_created' => $lang==='ar' ? 'إنشاء قالب' : 'Template Created',
        'template_updated' => $lang==='ar' ? 'تعديل قالب' : 'Template Updated',
        'login' => $lang==='ar' ? 'تسجيل دخول' : 'Login',
        'logout' => $lang==='ar' ? 'تسجيل خروج' : 'Logout',
    ];
    $actionColors = [
        'created' => 'success', 'started' => 'info', 'completed' => 'success',
        'cancelled' => 'warning', 'deleted' => 'danger', 'updated' => 'primary',
        'login' => 'info', 'logout' => 'secondary',
    ];
    $actionIcons = [
        'created' => '➕', 'started' => '▶️', 'completed' => '✅',
        'cancelled' => '🚫', 'deleted' => '🗑️', 'updated' => '✏️',
        'login' => '🔑', 'logout' => '🚪',
    ];
    $typeLabels = [
        'Inspection' => $lang==='ar' ? 'فحص' : 'Inspection',
        'Vehicle' => $lang==='ar' ? 'مركبة' : 'Vehicle',
        'User' => $lang==='ar' ? 'مستخدم' : 'User',
        'Template' => $lang==='ar' ? 'قالب' : 'Template',
        'InspectionTemplate' => $lang==='ar' ? 'قالب' : 'Template',
    ];
@endphp

@section('content')
<div class="page-header">
    <h1>{{ $lang === 'ar' ? 'سجل النظام' : 'Audit Logs' }}</h1>
    <div class="header-actions">
        <span class="text-muted" style="font-size:.82rem">{{ $lang==='ar' ? 'إجمالي:' : 'Total:' }} <strong>{{ $logs->total() }}</strong> {{ $lang==='ar' ? 'سجل' : 'records' }}</span>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-2">
    <div class="card-body" style="padding:.75rem 1rem">
        <form method="GET" style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="flex:1;min-width:180px" placeholder="{{ $lang==='ar' ? 'بحث...' : 'Search...' }}" value="{{ request('search') }}">
            <select name="action" class="form-control" style="width:auto;min-width:150px" onchange="this.form.submit()">
                <option value="">{{ $lang==='ar' ? 'كل الأحداث' : 'All Events' }}</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action')===$act ? 'selected' : '' }}>{{ $actionLabels[$act] ?? $act }}</option>
                @endforeach
            </select>
            <select name="type" class="form-control" style="width:auto;min-width:130px" onchange="this.form.submit()">
                <option value="">{{ $lang==='ar' ? 'كل الأنواع' : 'All Types' }}</option>
                <option value="Inspection" {{ request('type')==='Inspection' ? 'selected' : '' }}>{{ $lang==='ar' ? 'فحص' : 'Inspection' }}</option>
                <option value="Vehicle" {{ request('type')==='Vehicle' ? 'selected' : '' }}>{{ $lang==='ar' ? 'مركبة' : 'Vehicle' }}</option>
                <option value="User" {{ request('type')==='User' ? 'selected' : '' }}>{{ $lang==='ar' ? 'مستخدم' : 'User' }}</option>
                <option value="Template" {{ request('type')==='Template' ? 'selected' : '' }}>{{ $lang==='ar' ? 'قالب' : 'Template' }}</option>
            </select>
            <input type="date" name="from" class="form-control" style="width:auto" value="{{ request('from') }}" title="{{ $lang==='ar' ? 'من تاريخ' : 'From date' }}">
            <input type="date" name="to" class="form-control" style="width:auto" value="{{ request('to') }}" title="{{ $lang==='ar' ? 'إلى تاريخ' : 'To date' }}">
            <button type="submit" class="btn btn-secondary">{{ $lang==='ar' ? 'بحث' : 'Search' }}</button>
            @if(request()->hasAny(['search','action','type','from','to']))
                <a href="{{ route('audit-logs.index') }}" class="btn btn-ghost btn-sm" style="color:var(--danger)">✕ {{ $lang==='ar' ? 'مسح' : 'Clear' }}</a>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $lang==='ar' ? 'الحدث' : 'Event' }}</th>
                    <th>{{ $lang==='ar' ? 'النوع' : 'Type' }}</th>
                    <th>{{ $lang==='ar' ? 'التفاصيل' : 'Details' }}</th>
                    <th>{{ $lang==='ar' ? 'المستخدم' : 'User' }}</th>
                    <th>{{ $lang==='ar' ? 'التاريخ' : 'Date' }}</th>
                    <th>{{ $lang==='ar' ? 'الإجراء' : 'Action' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $basename = class_basename($log->model_type ?? '');
                    $colorKey = collect($actionColors)->keys()->first(fn($k) => str_contains($log->action, $k)) ?? 'secondary';
                    $color = $actionColors[$colorKey] ?? 'secondary';
                    $icon = $actionIcons[$colorKey] ?? '📋';
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem">
                            <span style="font-size:1.1rem">{{ $icon }}</span>
                            <span class="badge badge-{{ $color }}">{{ $actionLabels[$log->action] ?? $log->action }}</span>
                        </div>
                    </td>
                    <td><span class="badge badge-secondary" style="font-size:.72rem">{{ $typeLabels[$basename] ?? $basename }}</span></td>
                    <td style="font-size:.82rem;max-width:200px">
                        @if($log->model)
                            @if($basename === 'Inspection')
                                <span class="font-mono">{{ $log->model->reference_number ?? $log->model_id }}</span>
                            @elseif($basename === 'Vehicle')
                                {{ $log->model->full_name ?? $log->model_id }}
                            @elseif($basename === 'User')
                                {{ $log->model->name ?? $log->model_id }}
                            @else
                                <span class="text-muted font-mono" style="font-size:.75rem">{{ Str::limit($log->model_id, 12) }}</span>
                            @endif
                        @else
                            <span class="text-muted font-mono" style="font-size:.75rem">{{ $log->model_id ? Str::limit($log->model_id, 12) : '-' }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem">
                            @if($log->user)
                                <div class="user-avatar" style="width:26px;height:26px;font-size:.65rem">{{ mb_substr($log->user->name, 0, 1) }}</div>
                                <span style="font-size:.85rem">{{ $log->user->name }}</span>
                            @else
                                <span class="text-muted">{{ $lang==='ar' ? 'نظام' : 'System' }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="font-size:.8rem;color:var(--gray-500);white-space:nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($log->model_id && $basename === 'Inspection' && $log->model)
                            <a href="{{ route('inspections.show', $log->model_id) }}" class="btn btn-ghost btn-sm">{{ $lang==='ar' ? 'عرض' : 'View' }}</a>
                        @elseif($log->model_id && $basename === 'Vehicle' && $log->model)
                            <a href="{{ route('vehicles.show', $log->model_id) }}" class="btn btn-ghost btn-sm">{{ $lang==='ar' ? 'عرض' : 'View' }}</a>
                        @elseif($log->new_values || $log->old_values)
                            <button type="button" class="btn btn-ghost btn-sm" onclick="showLogDetails({{ json_encode(['old'=>$log->old_values,'new'=>$log->new_values,'action'=>$log->action]) }})">{{ $lang==='ar' ? 'تفاصيل' : 'Details' }}</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted" style="padding:2.5rem">
                    <div style="font-size:2rem;margin-bottom:.5rem">📋</div>
                    {{ $lang==='ar' ? 'لا توجد سجلات' : 'No logs found' }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($logs->hasPages())
<div style="margin-top:1rem">{{ $logs->appends(request()->query())->links() }}</div>
@endif
@endsection

@section('modals')
{{-- Log Details Modal --}}
<div class="modal" id="log-details-modal">
    <div class="modal-header">
        <h3>📋 {{ $lang==='ar' ? 'تفاصيل السجل' : 'Log Details' }}</h3>
        <button class="modal-close" onclick="closeModal('log-details-modal')">✕</button>
    </div>
    <div class="modal-body" id="log-details-body"></div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('log-details-modal')">{{ $lang==='ar' ? 'إغلاق' : 'Close' }}</button>
    </div>
</div>

<script src="{{ asset('js/audit-logs.js') }}"></script>
@endsection