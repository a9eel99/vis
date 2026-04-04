@extends('layouts.app')
@section('title', __('dashboard'))

@php
    $lang  = app()->getLocale();
    $isRtl = $lang === 'ar';

    $gradeLabels = [
        'excellent'       => $isRtl ? 'ممتاز'         : 'Excellent',
        'good'            => $isRtl ? 'جيد'           : 'Good',
        'needs_attention' => $isRtl ? 'يحتاج اهتمام' : 'Needs Attention',
        'critical'        => $isRtl ? 'حرج'           : 'Critical',
    ];

    $gradeDist  = $stats['grade_distribution'] ?? [];
    $mLabels    = $monthlyStats->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m.'-01')->format('M y'))->toArray();
    $mTotals    = $monthlyStats->pluck('total')->toArray();
    $mCompleted = $monthlyStats->pluck('completed')->toArray();
    $mAvgScores = $monthlyStats->pluck('avg_score')->map(fn($v) => round($v ?? 0, 1))->toArray();
@endphp

@section('content')

<div class="welcome-section">
    <h2>{{ __('welcome_msg') }}</h2>
    <p>{{ __('welcome_desc') }}</p>
</div>

{{-- KPI Cards --}}
<div class="dash-kpis">
    <div class="card dash-kpi" style="border-{{ $isRtl ? 'right' : 'left' }}:4px solid var(--primary)">
        <div class="dash-kpi-label">{{ $isRtl ? 'إجمالي الفحوصات' : 'Total Inspections' }}</div>
        <div class="dash-kpi-value" style="color:var(--primary)">{{ $stats['total_inspections'] ?? 0 }}</div>
        <div class="dash-kpi-sub">
            {{ $isRtl ? 'هذا الشهر:' : 'This month:' }}
            <span style="color:var(--primary);font-weight:700">{{ $stats['this_month'] ?? 0 }}</span>
        </div>
    </div>

    <div class="card dash-kpi" style="border-{{ $isRtl ? 'right' : 'left' }}:4px solid #10b981">
        <div class="dash-kpi-label">{{ $isRtl ? 'نسبة النجاح' : 'Pass Rate' }}</div>
        <div class="dash-kpi-value" style="color:#10b981">{{ $stats['pass_rate'] ?? 0 }}%</div>
        <div class="dash-kpi-sub">
            {{ $isRtl ? 'نجح:' : 'Passed:' }} {{ $stats['passed'] ?? 0 }}
            &bull;
            {{ $isRtl ? 'فشل:' : 'Failed:' }} {{ $stats['failed'] ?? 0 }}
        </div>
    </div>

    <div class="card dash-kpi" style="border-{{ $isRtl ? 'right' : 'left' }}:4px solid #3b82f6">
        <div class="dash-kpi-label">{{ $isRtl ? 'متوسط النتيجة' : 'Avg Score' }}</div>
        <div class="dash-kpi-value" style="color:#3b82f6">{{ $stats['avg_score'] ?? 0 }}%</div>
        <div class="dash-kpi-sub">{{ $isRtl ? 'من جميع الفحوصات المكتملة' : 'From all completed' }}</div>
    </div>

    <div class="card dash-kpi" style="border-{{ $isRtl ? 'right' : 'left' }}:4px solid #f59e0b">
        <div class="dash-kpi-label">{{ $isRtl ? 'اليوم' : 'Today' }}</div>
        <div class="dash-kpi-value" style="color:#f59e0b">{{ $todayCount ?? 0 }}</div>
        <div class="dash-kpi-sub">
            {{ $isRtl ? 'مكتمل:' : 'Completed:' }}
            <span style="color:#10b981;font-weight:700">{{ $todayCompleted ?? 0 }}</span>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="dash-charts">
    <div class="card">
        <div class="card-header">
            <h3>📈 {{ $isRtl ? 'الفحوصات الشهرية' : 'Monthly Inspections' }}</h3>
        </div>
        <div class="card-body dash-chart-box">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>📊 {{ $isRtl ? 'توزيع التقييمات' : 'Grade Distribution' }}</h3>
        </div>
        <div class="card-body dash-chart-box" style="display:flex;align-items:center;justify-content:center">
            @if(array_sum($gradeDist) > 0)
                <canvas id="gradeChart"></canvas>
            @else
                <p style="color:var(--gray-400);font-size:.9rem">
                    {{ $isRtl ? 'لا توجد بيانات بعد' : 'No data yet' }}
                </p>
            @endif
        </div>
    </div>
</div>

{{-- Score Trend --}}
@if(count($mAvgScores) >= 2)
<div class="card mb-2">
    <div class="card-header">
        <h3>📉 {{ $isRtl ? 'متوسط النتيجة الشهري' : 'Monthly Avg Score' }}</h3>
    </div>
    <div class="card-body dash-chart-box" style="height:220px">
        <canvas id="scoreChart"></canvas>
    </div>
</div>
@endif

{{-- Bottom Row --}}
<div class="dash-bottom">

    {{-- Recent Inspections --}}
    <div class="card">
        <div class="card-header">
            <h3>🕐 {{ __('recent_inspections') }}</h3>
            <a href="{{ route('inspections.index') }}" class="btn btn-ghost btn-sm">{{ __('view_all') }}</a>
        </div>
        <div class="card-body dash-table-wrap">
            @if($recentInspections->count())
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('vehicle') }}</th>
                        <th>{{ __('status') }}</th>
                        <th>{{ $isRtl ? 'النتيجة' : 'Score' }}</th>
                        <th>{{ __('date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentInspections as $ins)
                    <tr style="cursor:pointer" onclick="window.location='{{ route('inspections.show', $ins) }}'">
                        <td>
                            <div style="font-weight:600;font-size:.85rem">
                                {{ $ins->vehicle?->make }} {{ $ins->vehicle?->model }}
                            </div>
                            <div style="font-size:.72rem;color:var(--gray-400)">
                                {{ $ins->vehicle?->license_plate }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $ins->status->color() }}">
                                {{ $ins->status->label() }}
                            </span>
                        </td>
                        <td>
                            @if($ins->percentage)
                                <span style="font-weight:700;color:{{ $ins->percentage >= 75 ? '#10b981' : ($ins->percentage >= 50 ? '#f59e0b' : '#ef4444') }}">
                                    {{ $ins->percentage }}%
                                </span>
                            @else
                                <span style="color:var(--gray-400)">—</span>
                            @endif
                        </td>
                        <td style="font-size:.78rem;color:var(--gray-500);white-space:nowrap">
                            {{ $ins->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <p>{{ __('no_inspections') }}</p>
            </div>
            @endif
        </div>
    </div>

    <div>
        {{-- Quick Actions --}}
        @canany(['create inspections', 'create vehicles', 'view audit logs'])
        <div class="card mb-2">
            <div class="card-header">
                <h3>⚡ {{ __('quick_actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    @can('create inspections')
                    <a href="{{ route('inspections.create') }}" class="quick-action-btn primary-action">
                        {{ __('new_inspection') }}
                    </a>
                    @endcan
                    @can('create vehicles')
                    <a href="{{ route('vehicles.create') }}" class="quick-action-btn">
                        🚗 {{ __('add_vehicle') }}
                    </a>
                    @endcan
                    @can('view audit logs')
                    <a href="{{ route('audit-logs.index') }}" class="quick-action-btn">
                        📊 {{ __('reports') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        {{-- Inspectors --}}
        <div class="card">
            <div class="card-header">
                <h3>👥 {{ __('inspectors') }}</h3>
            </div>
            <div class="card-body" style="padding:0">
                @forelse($inspectors as $inspector)
                <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--gray-100)">
                    <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;
                        background:{{ ['#dbeafe','#dcfce7','#fef3c7','#fee2e2','#f3e8ff'][$loop->index % 5] }};
                        color:{{ ['#1e40af','#166534','#92400e','#991b1b','#6b21a8'][$loop->index % 5] }}">
                        {{ mb_substr($inspector->name, 0, 1) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $inspector->name }}
                        </div>
                        <div style="font-size:.72rem;color:var(--gray-400)">
                            {{ $inspector->completed_count }} {{ $isRtl ? 'مكتمل' : 'completed' }}
                        </div>
                    </div>
                    <div style="text-align:center;flex-shrink:0">
                        <div style="font-size:1.1rem;font-weight:800;color:var(--primary)">
                            {{ $inspector->inspections_count }}
                        </div>
                        <div style="font-size:.65rem;color:var(--gray-400)">
                            {{ $isRtl ? 'فحص' : 'total' }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center" style="padding:1rem;font-size:.9rem">
                    {{ __('no_inspectors') }}
                </p>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var isDark    = document.documentElement.classList.contains('dark');
    var textColor = isDark ? '#e2e8f0' : '#475569';
    var gridColor = isDark ? '#334155' : '#e2e8f0';

    // Monthly Chart
    var mCtx = document.getElementById('monthlyChart');
    if (mCtx) {
        new Chart(mCtx, {
            type: 'bar',
            data: {
                labels: @json($mLabels),
                datasets: [
                    {
                        label: '{{ $isRtl ? "إجمالي" : "Total" }}',
                        data: @json($mTotals),
                        backgroundColor: 'rgba(30,58,95,0.15)',
                        borderColor: '#1e3a5f',
                        borderWidth: 1.5,
                        borderRadius: 4,
                    },
                    {
                        label: '{{ $isRtl ? "مكتمل" : "Completed" }}',
                        data: @json($mCompleted),
                        backgroundColor: 'rgba(16,185,129,0.2)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 11 }, color: textColor }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: textColor }, grid: { color: gridColor } },
                    x: { grid: { display: false }, ticks: { color: textColor } }
                }
            }
        });
    }

    // Grade Distribution Chart
    var gCtx = document.getElementById('gradeChart');
    if (gCtx) {
        new Chart(gCtx, {
            type: 'doughnut',
            data: {
                labels: [@foreach($gradeLabels as $k => $v)'{{ $v }}',@endforeach],
                datasets: [{
                    data: [
                        {{ $gradeDist['excellent']       ?? 0 }},
                        {{ $gradeDist['good']            ?? 0 }},
                        {{ $gradeDist['needs_attention'] ?? 0 }},
                        {{ $gradeDist['critical']        ?? 0 }}
                    ],
                    backgroundColor: ['#10b981','#3b82f6','#f59e0b','#ef4444'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 10, font: { size: 11 }, color: textColor }
                    }
                },
                cutout: '65%',
            }
        });
    }

    // Score Trend Chart
    var sCtx = document.getElementById('scoreChart');
    if (sCtx) {
        new Chart(sCtx, {
            type: 'line',
            data: {
                labels: @json($mLabels),
                datasets: [{
                    label: '{{ $isRtl ? "متوسط النتيجة %" : "Avg Score %" }}',
                    data: @json($mAvgScores),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { min: 0, max: 100, ticks: { callback: function(v) { return v + '%'; }, color: textColor }, grid: { color: gridColor } },
                    x: { grid: { display: false }, ticks: { color: textColor } }
                }
            }
        });
    }
});
</script>
@endsection