<?php $__env->startSection('title', __('dashboard')); ?>

<?php
    $lang = app()->getLocale();
    $isRtl = $lang === 'ar';

    $gradeLabels = [
        'excellent' => $isRtl ? 'ممتاز' : 'Excellent',
        'good' => $isRtl ? 'جيد' : 'Good',
        'needs_attention' => $isRtl ? 'يحتاج اهتمام' : 'Needs Attention',
        'critical' => $isRtl ? 'حرج' : 'Critical',
    ];
    $gradeDist = $stats['grade_distribution'] ?? [];

    $mLabels = $monthlyStats->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m.'-01')->format('M'))->toArray();
    $mTotals = $monthlyStats->pluck('total')->toArray();
    $mCompleted = $monthlyStats->pluck('completed')->toArray();
    $mAvgScores = $monthlyStats->pluck('avg_score')->map(fn($v) => round($v ?? 0, 1))->toArray();

    $inspectors = \App\Domain\Models\User::role('Inspector')
        ->withCount(['inspections', 'inspections as completed_count' => fn($q) => $q->where('status', 'completed')])
        ->orderByDesc('inspections_count')->take(5)->get();

    $recentInspections = \App\Domain\Models\Inspection::with(['vehicle','inspector'])->latest()->take(7)->get();

    $todayCount = \App\Domain\Models\Inspection::whereDate('created_at', today())->count();
    $todayCompleted = \App\Domain\Models\Inspection::whereDate('completed_at', today())->where('status', 'completed')->count();
?>

<?php $__env->startSection('content'); ?>

<div class="welcome-section">
    <h2><?php echo e(__('welcome_msg')); ?></h2>
    <p><?php echo e(__('welcome_desc')); ?></p>
</div>


<div class="dash-kpis">
    <div class="card dash-kpi" style="border-<?php echo e($isRtl ? 'right' : 'left'); ?>:4px solid var(--primary)">
        <div class="dash-kpi-label"><?php echo e($isRtl ? 'إجمالي الفحوصات' : 'Total Inspections'); ?></div>
        <div class="dash-kpi-value" style="color:var(--primary)"><?php echo e($stats['total_inspections']); ?></div>
        <div class="dash-kpi-sub"><?php echo e($isRtl ? 'هذا الشهر:' : 'This month:'); ?> <span style="color:var(--primary);font-weight:700"><?php echo e($stats['this_month']); ?></span></div>
    </div>
    <div class="card dash-kpi" style="border-<?php echo e($isRtl ? 'right' : 'left'); ?>:4px solid #10b981">
        <div class="dash-kpi-label"><?php echo e($isRtl ? 'نسبة النجاح' : 'Pass Rate'); ?></div>
        <div class="dash-kpi-value" style="color:#10b981"><?php echo e($stats['pass_rate']); ?>%</div>
        <div class="dash-kpi-sub"><?php echo e($isRtl ? 'نجح:' : 'Passed:'); ?> <?php echo e($stats['passed']); ?> &bull; <?php echo e($isRtl ? 'فشل:' : 'Failed:'); ?> <?php echo e($stats['failed']); ?></div>
    </div>
    <div class="card dash-kpi" style="border-<?php echo e($isRtl ? 'right' : 'left'); ?>:4px solid #3b82f6">
        <div class="dash-kpi-label"><?php echo e($isRtl ? 'متوسط النتيجة' : 'Avg Score'); ?></div>
        <div class="dash-kpi-value" style="color:#3b82f6"><?php echo e($stats['average_score']); ?>%</div>
        <div class="dash-kpi-sub"><?php echo e($isRtl ? 'من جميع الفحوصات المكتملة' : 'From all completed'); ?></div>
    </div>
    <div class="card dash-kpi" style="border-<?php echo e($isRtl ? 'right' : 'left'); ?>:4px solid #f59e0b">
        <div class="dash-kpi-label"><?php echo e($isRtl ? 'اليوم' : 'Today'); ?></div>
        <div class="dash-kpi-value" style="color:#f59e0b"><?php echo e($todayCount); ?></div>
        <div class="dash-kpi-sub"><?php echo e($isRtl ? 'مكتمل:' : 'Completed:'); ?> <span style="color:#10b981;font-weight:700"><?php echo e($todayCompleted); ?></span></div>
    </div>
</div>


<div class="dash-charts">
    <div class="card">
        <div class="card-header"><h3>📈 <?php echo e($isRtl ? 'الفحوصات الشهرية' : 'Monthly Inspections'); ?></h3></div>
        <div class="card-body dash-chart-box"><canvas id="monthlyChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h3>📊 <?php echo e($isRtl ? 'توزيع التقييمات' : 'Grade Distribution'); ?></h3></div>
        <div class="card-body dash-chart-box" style="display:flex;align-items:center;justify-content:center">
            <?php if(array_sum($gradeDist) > 0): ?>
            <canvas id="gradeChart"></canvas>
            <?php else: ?>
            <p style="color:var(--gray-400);font-size:.9rem"><?php echo e($isRtl ? 'لا توجد بيانات بعد' : 'No data yet'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php if(count($mAvgScores) >= 2): ?>
<div class="card mb-2">
    <div class="card-header"><h3>📉 <?php echo e($isRtl ? 'متوسط النتيجة الشهري' : 'Monthly Avg Score'); ?></h3></div>
    <div class="card-body dash-chart-box" style="height:220px"><canvas id="scoreChart"></canvas></div>
</div>
<?php endif; ?>


<div class="dash-bottom">
    <div class="card">
        <div class="card-header">
            <h3>🕐 <?php echo e(__('recent_inspections')); ?></h3>
            <a href="<?php echo e(route('inspections.index')); ?>" class="btn btn-ghost btn-sm"><?php echo e(__('view_all')); ?></a>
        </div>
        <div class="card-body dash-table-wrap">
            <?php if($recentInspections->count()): ?>
            <table class="table">
                <thead><tr>
                    <th><?php echo e(__('vehicle')); ?></th>
                    <th><?php echo e(__('status')); ?></th>
                    <th><?php echo e($isRtl ? 'النتيجة' : 'Score'); ?></th>
                    <th><?php echo e(__('date')); ?></th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $recentInspections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="cursor:pointer" onclick="window.location='<?php echo e(route('inspections.show', $ins)); ?>'">
                        <td>
                            <div style="font-weight:600;font-size:.85rem"><?php echo e($ins->vehicle?->make); ?> <?php echo e($ins->vehicle?->model); ?></div>
                            <div style="font-size:.72rem;color:var(--gray-400)"><?php echo e($ins->vehicle?->license_plate); ?></div>
                        </td>
                        <td><span class="badge badge-<?php echo e($ins->status->color()); ?>"><?php echo e($ins->status->label()); ?></span></td>
                        <td>
                            <?php if($ins->percentage): ?>
                            <span style="font-weight:700;color:<?php echo e($ins->percentage >= 75 ? '#10b981' : ($ins->percentage >= 50 ? '#f59e0b' : '#ef4444')); ?>"><?php echo e($ins->percentage); ?>%</span>
                            <?php else: ?> <span style="color:var(--gray-400)">—</span> <?php endif; ?>
                        </td>
                        <td style="font-size:.78rem;color:var(--gray-500);white-space:nowrap"><?php echo e($ins->created_at->diffForHumans()); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state"><div class="empty-state-icon">📋</div><p><?php echo e(__('no_inspections')); ?></p></div>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['create inspections', 'create vehicles', 'view audit logs'])): ?>
        <div class="card mb-2">
            <div class="card-header"><h3>⚡ <?php echo e(__('quick_actions')); ?></h3></div>
            <div class="card-body">
                <div class="quick-actions">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create inspections')): ?>
                    <a href="<?php echo e(route('inspections.create')); ?>" class="quick-action-btn primary-action"><?php echo e(__('new_inspection')); ?></a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create vehicles')): ?>
                    <a href="<?php echo e(route('vehicles.create')); ?>" class="quick-action-btn">🚗 <?php echo e(__('add_vehicle')); ?></a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view audit logs')): ?>
                    <a href="<?php echo e(route('audit-logs.index')); ?>" class="quick-action-btn">📊 <?php echo e(__('reports')); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><h3>👥 <?php echo e(__('inspectors')); ?></h3></div>
            <div class="card-body" style="padding:0">
                <?php $__empty_1 = true; $__currentLoopData = $inspectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inspector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--gray-100)">
                    <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;background:<?php echo e(['#dbeafe','#dcfce7','#fef3c7','#fee2e2','#f3e8ff'][$loop->index % 5]); ?>;color:<?php echo e(['#1e40af','#166534','#92400e','#991b1b','#6b21a8'][$loop->index % 5]); ?>">
                        <?php echo e(mb_substr($inspector->name, 0, 1)); ?>

                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo e($inspector->name); ?></div>
                        <div style="font-size:.72rem;color:var(--gray-400)"><?php echo e($inspector->completed_count); ?> <?php echo e($isRtl ? 'مكتمل' : 'completed'); ?></div>
                    </div>
                    <div style="text-align:center;flex-shrink:0">
                        <div style="font-size:1.1rem;font-weight:800;color:var(--primary)"><?php echo e($inspector->inspections_count); ?></div>
                        <div style="font-size:.65rem;color:var(--gray-400)"><?php echo e($isRtl ? 'فحص' : 'total'); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted text-center" style="padding:1rem;font-size:.9rem"><?php echo e(__('no_inspectors')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var mCtx = document.getElementById('monthlyChart');
    if (mCtx) {
        new Chart(mCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($mLabels, 15, 512) ?>,
                datasets: [
                    { label: '<?php echo e($isRtl ? "إجمالي" : "Total"); ?>', data: <?php echo json_encode($mTotals, 15, 512) ?>, backgroundColor: 'rgba(30,58,95,0.15)', borderColor: '#1e3a5f', borderWidth: 1.5, borderRadius: 4 },
                    { label: '<?php echo e($isRtl ? "مكتمل" : "Completed"); ?>', data: <?php echo json_encode($mCompleted, 15, 512) ?>, backgroundColor: 'rgba(16,185,129,0.2)', borderColor: '#10b981', borderWidth: 1.5, borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', labels: { usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 11 } } } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } } }
        });
    }
    var gCtx = document.getElementById('gradeChart');
    if (gCtx) {
        new Chart(gCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php $__currentLoopData = $gradeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>'<?php echo e($v); ?>',<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>],
                datasets: [{ data: [<?php echo e($gradeDist['excellent'] ?? 0); ?>,<?php echo e($gradeDist['good'] ?? 0); ?>,<?php echo e($gradeDist['needs_attention'] ?? 0); ?>,<?php echo e($gradeDist['critical'] ?? 0); ?>], backgroundColor: ['#10b981','#3b82f6','#f59e0b','#ef4444'], borderWidth: 2, borderColor: '#fff' }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', padding: 10, font: { size: 11 } } } }, cutout: '65%' }
        });
    }
    var sCtx = document.getElementById('scoreChart');
    if (sCtx) {
        new Chart(sCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($mLabels, 15, 512) ?>,
                datasets: [{ label: '<?php echo e($isRtl ? "متوسط النتيجة %" : "Avg Score %"); ?>', data: <?php echo json_encode($mAvgScores, 15, 512) ?>, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.08)', borderWidth: 2.5, pointRadius: 5, pointBackgroundColor: '#3b82f6', tension: 0.3, fill: true }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { min: 0, max: 100, ticks: { callback: function(v) { return v + '%'; } } }, x: { grid: { display: false } } } }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/dashboard/index.blade.php ENDPATH**/ ?>