
<?php $__env->startSection('title', $lang === 'ar' ? 'التقارير المالية' : 'Financial Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>💰 <?php echo e($lang === 'ar' ? 'التقارير المالية' : 'Financial Reports'); ?></h1>
</div>


<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;color:#16a34a">💵</div>
        <div>
            <div class="stat-value" style="font-size:1.4rem;font-weight:800;color:var(--gray-900)"><?php echo e(number_format($summary['today'], 2)); ?></div>
            <div class="stat-label" style="font-size:.75rem;color:var(--gray-500)"><?php echo e($lang === 'ar' ? 'إيرادات اليوم' : "Today's Revenue"); ?></div>
            <div style="font-size:.7rem;color:var(--gray-400)"><?php echo e($summary['today_count']); ?> <?php echo e($lang === 'ar' ? 'فحص' : 'inspections'); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;color:#2563eb">📊</div>
        <div>
            <div class="stat-value" style="font-size:1.4rem;font-weight:800;color:var(--gray-900)"><?php echo e(number_format($summary['this_month'], 2)); ?></div>
            <div class="stat-label" style="font-size:.75rem;color:var(--gray-500)"><?php echo e($lang === 'ar' ? 'إيرادات الشهر' : 'This Month'); ?></div>
            <div style="font-size:.7rem;color:var(--gray-400)"><?php echo e($summary['this_month_count']); ?> <?php echo e($lang === 'ar' ? 'فحص' : 'inspections'); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3e8ff;color:#7c3aed">📈</div>
        <div>
            <div class="stat-value" style="font-size:1.4rem;font-weight:800;color:var(--gray-900)"><?php echo e(number_format($summary['last_month'], 2)); ?></div>
            <div class="stat-label" style="font-size:.75rem;color:var(--gray-500)"><?php echo e($lang === 'ar' ? 'الشهر الماضي' : 'Last Month'); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#d97706">⏳</div>
        <div>
            <div class="stat-value" style="font-size:1.4rem;font-weight:800;color:#d97706"><?php echo e(number_format($summary['total_unpaid'], 2)); ?></div>
            <div class="stat-label" style="font-size:.75rem;color:var(--gray-500)"><?php echo e($lang === 'ar' ? 'غير مدفوع' : 'Unpaid'); ?></div>
            <div style="font-size:.7rem;color:var(--gray-400)"><?php echo e($unpaid->count()); ?> <?php echo e($lang === 'ar' ? 'فحص' : 'inspections'); ?></div>
        </div>
    </div>
</div>


<div class="card mb-2">
    <div class="card-header"><h3>📈 <?php echo e($lang === 'ar' ? 'الإيرادات الشهرية' : 'Monthly Revenue'); ?></h3></div>
    <div class="card-body">
        <canvas id="monthly-chart" height="200"></canvas>
    </div>
</div>


<div class="card mb-2">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
        <h3>📅 <?php echo e($lang === 'ar' ? 'التقرير اليومي' : 'Daily Report'); ?> — <?php echo e($dailyReport['month_label']); ?></h3>
        <form method="GET" style="display:flex;gap:6px;align-items:center">
            <input type="month" name="month" value="<?php echo e($month); ?>" class="form-control" style="width:auto" onchange="this.form.submit()">
        </form>
    </div>
    <div class="card-body" style="overflow-x:auto">
        <table class="data-table" style="width:100%">
            <thead>
                <tr>
                    <th><?php echo e($lang === 'ar' ? 'التاريخ' : 'Date'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'عدد الفحوصات' : 'Inspections'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'الإيرادات' : 'Revenue'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'الخصومات' : 'Discounts'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'الصافي' : 'Net'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $dailyReport['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-mono" style="font-size:.82rem"><?php echo e($day->date); ?></td>
                    <td><strong><?php echo e($day->count); ?></strong></td>
                    <td style="color:var(--success);font-weight:600"><?php echo e(number_format($day->revenue + $day->total_discount, 2)); ?></td>
                    <td style="color:var(--danger)"><?php echo e($day->total_discount > 0 ? '-' . number_format($day->total_discount, 2) : '-'); ?></td>
                    <td style="font-weight:700"><?php echo e(number_format($day->revenue, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center text-muted" style="padding:2rem"><?php echo e($lang === 'ar' ? 'لا توجد بيانات لهذا الشهر' : 'No data for this month'); ?></td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($dailyReport['days']->count()): ?>
            <tfoot>
                <tr style="font-weight:700;border-top:2px solid var(--gray-300)">
                    <td><?php echo e($lang === 'ar' ? 'المجموع' : 'Total'); ?></td>
                    <td><?php echo e($dailyReport['total_count']); ?></td>
                    <td style="color:var(--success)"><?php echo e(number_format($dailyReport['total_revenue'] + $dailyReport['total_discount'], 2)); ?></td>
                    <td style="color:var(--danger)"><?php echo e($dailyReport['total_discount'] > 0 ? '-' . number_format($dailyReport['total_discount'], 2) : '-'); ?></td>
                    <td><?php echo e(number_format($dailyReport['total_revenue'], 2)); ?></td>
                </tr>
                <tr style="font-size:.8rem;color:var(--gray-500)">
                    <td colspan="2"><?php echo e($lang === 'ar' ? 'المتوسط اليومي' : 'Daily Avg'); ?>: <?php echo e(number_format($dailyReport['avg_per_day'], 2)); ?></td>
                    <td colspan="3"><?php echo e($lang === 'ar' ? 'متوسط الفحص' : 'Per Inspection'); ?>: <?php echo e(number_format($dailyReport['avg_per_inspection'], 2)); ?></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>


<?php if($unpaid->count()): ?>
<div class="card mb-2">
    <div class="card-header" style="background:#fef3c7;border-bottom-color:#f59e0b">
        <h3 style="color:#92400e">⏳ <?php echo e($lang === 'ar' ? 'فحوصات غير مدفوعة' : 'Unpaid Inspections'); ?> (<?php echo e($unpaid->count()); ?>)</h3>
    </div>
    <div class="card-body" style="overflow-x:auto">
        <table class="data-table" style="width:100%">
            <thead>
                <tr>
                    <th><?php echo e($lang === 'ar' ? 'الرقم المرجعي' : 'Reference'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'المركبة' : 'Vehicle'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'القالب' : 'Template'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'السعر' : 'Price'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'التاريخ' : 'Date'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $unpaid; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="font-mono" style="font-size:.82rem"><?php echo e($ins->reference_number); ?></td>
                    <td><?php echo e($ins->vehicle->full_name ?? '-'); ?></td>
                    <td><?php echo e($ins->template->name ?? '-'); ?></td>
                    <td style="font-weight:700"><?php echo e(number_format($ins->price, 2)); ?></td>
                    <td style="font-size:.82rem;color:var(--gray-500)"><?php echo e($ins->completed_at?->format('Y-m-d') ?? $ins->created_at->format('Y-m-d')); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('finance.markPaid', $ins->id)); ?>" style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
                            <?php echo csrf_field(); ?>
                            <input type="number" name="discount" value="0" min="0" step="0.01" class="form-control" style="width:65px;padding:4px 6px;font-size:.8rem" placeholder="<?php echo e($lang === 'ar' ? 'خصم' : 'Disc'); ?>">
                            <input type="text" name="payment_note" class="form-control" style="width:120px;padding:4px 6px;font-size:.8rem" placeholder="<?php echo e($lang === 'ar' ? 'ملاحظة...' : 'Note...'); ?>">
                            <button type="submit" class="btn btn-success btn-sm">💵 <?php echo e($lang === 'ar' ? 'قبض' : 'Paid'); ?></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
var monthlyData = <?php echo json_encode($monthlyReport['months'], 15, 512) ?>;
var isDark = document.documentElement.classList.contains('dark');
var textColor = isDark ? '#e2e8f0' : '#475569';
var gridColor = isDark ? '#334155' : '#e2e8f0';

new Chart(document.getElementById('monthly-chart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(function(m) { return m.month; }),
        datasets: [{
            label: '<?php echo e($lang === "ar" ? "الإيرادات" : "Revenue"); ?>',
            data: monthlyData.map(function(m) { return m.revenue; }),
            backgroundColor: 'rgba(16,185,129,0.3)',
            borderColor: '#10b981',
            borderWidth: 2,
            borderRadius: 6,
        }, {
            label: '<?php echo e($lang === "ar" ? "عدد الفحوصات" : "Count"); ?>',
            data: monthlyData.map(function(m) { return m.count; }),
            type: 'line',
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            yAxisID: 'y1',
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#3b82f6',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: textColor, font: { family: 'Cairo' } } } },
        scales: {
            x: { ticks: { color: textColor }, grid: { color: gridColor } },
            y: { position: '<?php echo e($lang === "ar" ? "right" : "left"); ?>', ticks: { color: textColor }, grid: { color: gridColor } },
            y1: { position: '<?php echo e($lang === "ar" ? "left" : "right"); ?>', ticks: { color: textColor }, grid: { display: false } }
        }
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/finance/index.blade.php ENDPATH**/ ?>