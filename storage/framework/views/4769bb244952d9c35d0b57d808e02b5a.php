<?php $__env->startSection('title', app()->getLocale() === 'ar' ? 'سجل النظام' : 'Audit Logs'); ?>

<?php
    $lang = app()->getLocale();

    $actionLabels = [
        // Inspections
        'inspection_created' => $lang==='ar' ? 'إنشاء فحص' : 'Inspection Created',
        'inspection_started' => $lang==='ar' ? 'بدء فحص' : 'Inspection Started',
        'inspection_completed' => $lang==='ar' ? 'اكتمال فحص' : 'Inspection Completed',
        'inspection_cancelled' => $lang==='ar' ? 'إلغاء فحص' : 'Inspection Cancelled',
        'inspection_deleted' => $lang==='ar' ? 'حذف فحص' : 'Inspection Deleted',
        'inspection_hidden' => $lang==='ar' ? 'إخفاء فحص' : 'Inspection Hidden',
        'inspection_shown' => $lang==='ar' ? 'إظهار فحص' : 'Inspection Shown',
        // Vehicles
        'vehicle_created' => $lang==='ar' ? 'إضافة مركبة' : 'Vehicle Created',
        'vehicle_updated' => $lang==='ar' ? 'تعديل مركبة' : 'Vehicle Updated',
        'vehicle_deleted' => $lang==='ar' ? 'حذف مركبة' : 'Vehicle Deleted',
        // Users
        'user_created' => $lang==='ar' ? 'إضافة مستخدم' : 'User Created',
        'user_updated' => $lang==='ar' ? 'تعديل مستخدم' : 'User Updated',
        'user_deleted' => $lang==='ar' ? 'حذف مستخدم' : 'User Deleted',
        // Templates
        'template_created' => $lang==='ar' ? 'إنشاء قالب' : 'Template Created',
        'template_updated' => $lang==='ar' ? 'تعديل قالب' : 'Template Updated',
        'template_deleted' => $lang==='ar' ? 'حذف قالب' : 'Template Deleted',
        'template_duplicated' => $lang==='ar' ? 'نسخ قالب' : 'Template Duplicated',
        // Customers
        'customer_created' => $lang==='ar' ? 'إضافة عميل' : 'Customer Created',
        'customer_updated' => $lang==='ar' ? 'تعديل عميل' : 'Customer Updated',
        'customer_deleted' => $lang==='ar' ? 'حذف عميل' : 'Customer Deleted',
        // Settings
        'settings_updated' => $lang==='ar' ? 'تعديل الإعدادات' : 'Settings Updated',
        // Auth
        'login' => $lang==='ar' ? 'تسجيل دخول' : 'Login',
        'logout' => $lang==='ar' ? 'تسجيل خروج' : 'Logout',
    ];

    $actionColors = [
        'created' => 'success', 'started' => 'info', 'completed' => 'success',
        'cancelled' => 'warning', 'deleted' => 'danger', 'updated' => 'primary',
        'hidden' => 'warning', 'shown' => 'info', 'duplicated' => 'info',
        'login' => 'info', 'logout' => 'secondary',
    ];

    $actionIcons = [
        'created' => '➕', 'started' => '▶️', 'completed' => '✅',
        'cancelled' => '🚫', 'deleted' => '🗑️', 'updated' => '✏️',
        'hidden' => '🙈', 'shown' => '👁️', 'duplicated' => '📋',
        'login' => '🔑', 'logout' => '🚪',
    ];

    // Match model_type regardless of full namespace path
    $typeLabels = [
        'Inspection' => $lang==='ar' ? 'فحص' : 'Inspection',
        'Vehicle' => $lang==='ar' ? 'مركبة' : 'Vehicle',
        'User' => $lang==='ar' ? 'مستخدم' : 'User',
        'InspectionTemplate' => $lang==='ar' ? 'قالب فحص' : 'Template',
        'Template' => $lang==='ar' ? 'قالب فحص' : 'Template',
        'Customer' => $lang==='ar' ? 'عميل' : 'Customer',
        'Setting' => $lang==='ar' ? 'إعدادات' : 'Settings',
        'InspectionSection' => $lang==='ar' ? 'قسم' : 'Section',
        'InspectionQuestion' => $lang==='ar' ? 'سؤال' : 'Question',
        'InspectionMedia' => $lang==='ar' ? 'ملف' : 'Media',
        'InspectionResult' => $lang==='ar' ? 'نتيجة' : 'Result',
    ];

    // Helper to get type label from full model_type string
    function getTypeLabel($modelType, $labels) {
        if (!$modelType) return '-';
        $basename = class_basename($modelType);
        return $labels[$basename] ?? $basename;
    }
?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e($lang === 'ar' ? 'سجل النظام' : 'Audit Logs'); ?></h1>
    <div class="header-actions">
        <span class="text-muted" style="font-size:.82rem"><?php echo e($lang==='ar' ? 'إجمالي:' : 'Total:'); ?> <strong><?php echo e($logs->total()); ?></strong> <?php echo e($lang==='ar' ? 'سجل' : 'records'); ?></span>
    </div>
</div>


<div class="card mb-2">
    <div class="card-body" style="padding:.75rem 1rem">
        <form method="GET" style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="flex:1;min-width:180px" placeholder="<?php echo e($lang==='ar' ? 'بحث...' : 'Search...'); ?>" value="<?php echo e(request('search')); ?>">
            <select name="action" class="form-control" style="width:auto;min-width:150px" onchange="this.form.submit()">
                <option value=""><?php echo e($lang==='ar' ? 'كل الأحداث' : 'All Events'); ?></option>
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($act); ?>" <?php echo e(request('action')===$act ? 'selected' : ''); ?>><?php echo e($actionLabels[$act] ?? $act); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="type" class="form-control" style="width:auto;min-width:130px" onchange="this.form.submit()">
                <option value=""><?php echo e($lang==='ar' ? 'كل الأنواع' : 'All Types'); ?></option>
                <option value="Inspection" <?php echo e(request('type')==='Inspection' ? 'selected' : ''); ?>><?php echo e($lang==='ar' ? 'فحص' : 'Inspection'); ?></option>
                <option value="Vehicle" <?php echo e(request('type')==='Vehicle' ? 'selected' : ''); ?>><?php echo e($lang==='ar' ? 'مركبة' : 'Vehicle'); ?></option>
                <option value="User" <?php echo e(request('type')==='User' ? 'selected' : ''); ?>><?php echo e($lang==='ar' ? 'مستخدم' : 'User'); ?></option>
                <option value="Template" <?php echo e(request('type')==='Template' ? 'selected' : ''); ?>><?php echo e($lang==='ar' ? 'قالب' : 'Template'); ?></option>
                <option value="Customer" <?php echo e(request('type')==='Customer' ? 'selected' : ''); ?>><?php echo e($lang==='ar' ? 'عميل' : 'Customer'); ?></option>
            </select>
            <input type="date" name="from" class="form-control" style="width:auto" value="<?php echo e(request('from')); ?>" title="<?php echo e($lang==='ar' ? 'من تاريخ' : 'From date'); ?>">
            <input type="date" name="to" class="form-control" style="width:auto" value="<?php echo e(request('to')); ?>" title="<?php echo e($lang==='ar' ? 'إلى تاريخ' : 'To date'); ?>">
            <button type="submit" class="btn btn-secondary"><?php echo e($lang==='ar' ? 'بحث' : 'Search'); ?></button>
            <?php if(request()->hasAny(['search','action','type','from','to'])): ?>
                <a href="<?php echo e(route('audit-logs.index')); ?>" class="btn btn-ghost btn-sm" style="color:var(--danger)">✕ <?php echo e($lang==='ar' ? 'مسح' : 'Clear'); ?></a>
            <?php endif; ?>
        </form>
    </div>
</div>


<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e($lang==='ar' ? 'الحدث' : 'Event'); ?></th>
                    <th><?php echo e($lang==='ar' ? 'النوع' : 'Type'); ?></th>
                    <th><?php echo e($lang==='ar' ? 'التفاصيل' : 'Details'); ?></th>
                    <th><?php echo e($lang==='ar' ? 'المستخدم' : 'User'); ?></th>
                    <th><?php echo e($lang==='ar' ? 'التاريخ' : 'Date'); ?></th>
                    <th><?php echo e($lang==='ar' ? 'الإجراء' : 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $basename = class_basename($log->model_type ?? '');
                    $colorKey = collect($actionColors)->keys()->first(fn($k) => str_contains($log->action, $k)) ?? 'secondary';
                    $color = $actionColors[$colorKey] ?? 'secondary';
                    $icon = $actionIcons[$colorKey] ?? '📋';
                ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem">
                            <span style="font-size:1.1rem"><?php echo e($icon); ?></span>
                            <span class="badge badge-<?php echo e($color); ?>"><?php echo e($actionLabels[$log->action] ?? $log->action); ?></span>
                        </div>
                    </td>
                    <td><span class="badge badge-secondary" style="font-size:.72rem"><?php echo e(getTypeLabel($log->model_type, $typeLabels)); ?></span></td>
                    <td style="font-size:.82rem;max-width:200px">
                        <?php if($log->model): ?>
                            <?php if($basename === 'Inspection'): ?>
                                <a href="<?php echo e(route('inspections.show', $log->model_id)); ?>" style="color:var(--primary);text-decoration:none" class="font-mono"><?php echo e($log->model->reference_number ?? Str::limit($log->model_id, 12)); ?></a>
                            <?php elseif($basename === 'Vehicle'): ?>
                                <a href="<?php echo e(route('vehicles.show', $log->model_id)); ?>" style="color:var(--primary);text-decoration:none"><?php echo e($log->model->full_name ?? Str::limit($log->model_id, 12)); ?></a>
                            <?php elseif($basename === 'User'): ?>
                                <?php echo e($log->model->name ?? Str::limit($log->model_id, 12)); ?>

                            <?php elseif($basename === 'Customer'): ?>
                                <a href="<?php echo e(route('customers.show', $log->model_id)); ?>" style="color:var(--primary);text-decoration:none"><?php echo e($log->model->name ?? Str::limit($log->model_id, 12)); ?></a>
                            <?php elseif(in_array($basename, ['InspectionTemplate'])): ?>
                                <?php echo e($log->model->name ?? Str::limit($log->model_id, 12)); ?>

                            <?php else: ?>
                                <span class="text-muted font-mono" style="font-size:.75rem"><?php echo e(Str::limit($log->model_id, 12)); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:.78rem"><?php echo e($lang === 'ar' ? 'محذوف' : 'Deleted'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem">
                            <?php if($log->user): ?>
                                <div class="user-avatar" style="width:26px;height:26px;font-size:.65rem"><?php echo e(mb_substr($log->user->name, 0, 1)); ?></div>
                                <span style="font-size:.85rem"><?php echo e($log->user->name); ?></span>
                            <?php else: ?>
                                <span class="text-muted"><?php echo e($lang==='ar' ? 'نظام' : 'System'); ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="font-size:.8rem;color:var(--gray-500);white-space:nowrap"><?php echo e($log->created_at->format('Y-m-d H:i')); ?></td>
                    <td>
                        <?php if($log->new_values || $log->old_values): ?>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="showLogDetails(<?php echo e(json_encode(['old'=>$log->old_values,'new'=>$log->new_values,'action'=>$log->action])); ?>)"><?php echo e($lang==='ar' ? 'تفاصيل' : 'Details'); ?></button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted" style="padding:2.5rem">
                    <div style="font-size:2rem;margin-bottom:.5rem">📋</div>
                    <?php echo e($lang==='ar' ? 'لا توجد سجلات' : 'No logs found'); ?>

                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if($logs->hasPages()): ?>
<div style="margin-top:1rem"><?php echo e($logs->appends(request()->query())->links()); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<div class="modal" id="log-details-modal">
    <div class="modal-header">
        <h3>📋 <?php echo e($lang==='ar' ? 'تفاصيل السجل' : 'Log Details'); ?></h3>
        <button class="modal-close" onclick="closeModal('log-details-modal')">✕</button>
    </div>
    <div class="modal-body" id="log-details-body"></div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('log-details-modal')"><?php echo e($lang==='ar' ? 'إغلاق' : 'Close'); ?></button>
    </div>
</div>

<script src="<?php echo e(asset('js/audit-logs.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/audit-logs/index.blade.php ENDPATH**/ ?>