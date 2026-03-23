@extends('layouts.app')
@section('title', 'لوحة التحكم')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['total_vehicles'] }}</div>
            <div class="stat-label">إجمالي المركبات</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['completed_inspections'] }}</div>
            <div class="stat-label">فحوصات مكتملة</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['pending_inspections'] }}</div>
            <div class="stat-label">فحوصات قيد الإنجاز</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['critical_count'] }}</div>
            <div class="stat-label">حالات حرجة</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['avg_score'] }}%</div>
            <div class="stat-label">متوسط النتائج</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    {{-- Recent Inspections --}}
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3>آخر الفحوصات</h3>
            <a href="{{ route('inspections.create') }}" class="btn btn-primary btn-sm"> فحص جديد</a>
        </div>
        <div class="table-container" style="border: none; border-radius: 0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الرقم المرجعي</th>
                        <th>المركبة</th>
                        <th>الفاحص</th>
                        <th>الحالة</th>
                        <th>النتيجة</th>
                        <th>التقييم</th>
                        <th>التاريخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentInspections as $ins)
                    <tr>
                        <td><span class="font-mono text-sm">{{ $ins->reference_number }}</span></td>
                        <td>{{ $ins->vehicle?->full_name ?? '-' }}</td>
                        <td>{{ $ins->inspector?->name ?? '-' }}</td>
                        <td><span class="badge badge-{{ $ins->status->color() }}">{{ $ins->status->label() }}</span></td>
                        <td>{{ $ins->percentage ? $ins->percentage . '%' : '-' }}</td>
                        <td>
                            @if($ins->grade)
                                <span class="grade-indicator grade-{{ $ins->grade }}">
                                    <span class="grade-dot"></span>
                                    {{ $ins->grade_enum?->label() ?? $ins->grade }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-sm text-muted">{{ $ins->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('inspections.show', $ins) }}" class="btn btn-ghost btn-sm">عرض</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted" style="padding: 2rem;">لا توجد فحوصات بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
