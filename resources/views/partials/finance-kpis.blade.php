@php
    $currency = $lang === 'ar' ? 'د.أ' : 'JOD';
    $diff = $summary['this_month'] - $summary['last_month'];
    $isUp = $diff >= 0;
    $diffPct = $summary['last_month'] > 0
        ? number_format(abs($diff / $summary['last_month'] * 100), 1) . '%'
        : '—';
@endphp

<div class="fin-kpis">
    <div class="fin-kpi green">
        <div class="fin-kpi-bar"></div>
        <div class="fin-kpi-body">
            <div class="fin-kpi-icon">💵</div>
            <div class="fin-kpi-content">
                <div class="fin-kpi-label">{{ $lang === 'ar' ? 'إيرادات اليوم' : "Today's Revenue" }}</div>
                <div class="fin-kpi-value">{{ number_format($summary['today'], 2) }}<span class="fin-kpi-currency">{{ $currency }}</span></div>
                <div class="fin-kpi-sub"><span class="dot"></span>{{ $summary['today_count'] }} {{ $lang === 'ar' ? 'فحص مدفوع' : 'paid inspections' }}</div>
            </div>
        </div>
    </div>
    <div class="fin-kpi blue">
        <div class="fin-kpi-bar"></div>
        <div class="fin-kpi-body">
            <div class="fin-kpi-icon">📊</div>
            <div class="fin-kpi-content">
                <div class="fin-kpi-label">{{ $lang === 'ar' ? 'إيرادات الشهر' : 'This Month' }}</div>
                <div class="fin-kpi-value">{{ number_format($summary['this_month'], 2) }}<span class="fin-kpi-currency">{{ $currency }}</span></div>
                <div class="fin-kpi-sub"><span class="dot"></span>{{ $summary['this_month_count'] }} {{ $lang === 'ar' ? 'فحص' : 'inspections' }}</div>
            </div>
        </div>
    </div>
    <div class="fin-kpi purple">
        <div class="fin-kpi-bar"></div>
        <div class="fin-kpi-body">
            <div class="fin-kpi-icon">📈</div>
            <div class="fin-kpi-content">
                <div class="fin-kpi-label">{{ $lang === 'ar' ? 'الشهر الماضي' : 'Last Month' }}</div>
                <div class="fin-kpi-value">{{ number_format($summary['last_month'], 2) }}<span class="fin-kpi-currency">{{ $currency }}</span></div>
                <div class="fin-kpi-sub" style="color:{{ $isUp ? '#10b981' : '#ef4444' }};font-weight:700">
                    {{ $isUp ? '↑' : '↓' }} {{ $diffPct }}
                    <span style="color:var(--gray-400);font-weight:400">{{ $lang === 'ar' ? 'مقارنة بهذا الشهر' : 'vs this month' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="fin-kpi amber">
        <div class="fin-kpi-bar"></div>
        <div class="fin-kpi-body">
            <div class="fin-kpi-icon">⏳</div>
            <div class="fin-kpi-content">
                <div class="fin-kpi-label">{{ $lang === 'ar' ? 'غير مدفوع' : 'Unpaid' }}</div>
                <div class="fin-kpi-value">{{ number_format($summary['total_unpaid'], 2) }}<span class="fin-kpi-currency">{{ $currency }}</span></div>
                <div class="fin-kpi-sub"><span class="dot"></span>{{ $unpaid->count() }} {{ $lang === 'ar' ? 'فحص بانتظار الدفع' : 'awaiting payment' }}</div>
            </div>
        </div>
    </div>
</div>