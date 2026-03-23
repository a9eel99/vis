<?php $__env->startSection('title', __('inspections')); ?>

<?php $lang = app()->getLocale(); ?>
 
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e(__('inspections')); ?></h1>
    <div class="header-actions">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("create inspections")): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create inspections')): ?>
        <a href="<?php echo e(route('inspections.create')); ?>" class="btn btn-primary">+ <?php echo e(__('new_inspection')); ?></a>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body" style="padding:.75rem 1rem">
        <form method="GET" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="flex:1;min-width:200px" placeholder="<?php echo e(__('search')); ?>..." value="<?php echo e(request('search')); ?>">
            <select name="status" class="form-control filter-select" onchange="this.form.submit()">
                <option value=""><?php echo e($lang === 'ar' ? 'كل الحالات' : 'All Statuses'); ?></option>
                <?php $__currentLoopData = ['draft' => $lang === 'ar' ? 'مسودة' : 'Draft', 'in_progress' => $lang === 'ar' ? 'قيد الإنجاز' : 'In Progress', 'completed' => $lang === 'ar' ? 'مكتمل' : 'Completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(request('status') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="grade" class="form-control filter-select" onchange="this.form.submit()">
                <option value=""><?php echo e($lang === 'ar' ? 'كل التقييمات' : 'All Grades'); ?></option>
                <?php $__currentLoopData = ['excellent' => $lang === 'ar' ? 'ممتاز' : 'Excellent', 'good' => $lang === 'ar' ? 'جيد' : 'Good', 'needs_attention' => $lang === 'ar' ? 'يحتاج اهتمام' : 'Needs Attention', 'critical' => $lang === 'ar' ? 'حرج' : 'Critical']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(request('grade') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e($lang === 'ar' ? 'الرقم المرجعي' : 'Reference'); ?></th>
                    <th><?php echo e(__('vehicle')); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'الفاحص' : 'Inspector'); ?></th>
                    <th><?php echo e(__('status')); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'الدرجة' : 'Score'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'التقييم' : 'Grade'); ?></th>
                    <th><?php echo e(__('date')); ?></th>
                    <th><?php echo e(__('actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $inspections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-mono" style="font-size:.82rem"><?php echo e($ins->reference_number); ?></td>
                    <td><?php echo e($ins->vehicle?->full_name ?? '-'); ?></td>
                    <td><?php echo e($ins->inspector?->name ?? '-'); ?></td>
                    <td><span class="badge badge-<?php echo e($ins->status->color()); ?>"><?php echo e($ins->status->label()); ?></span></td>
                    <td style="font-weight:700"><?php echo e($ins->percentage ? $ins->percentage . '%' : '-'); ?></td>
                    <td>
                        <?php if($ins->grade): ?>
                            <?php $g = is_object($ins->grade) ? $ins->grade->value : $ins->grade; ?>
                            <span class="badge badge-<?php echo e($g === 'excellent' ? 'success' : ($g === 'good' ? 'primary' : ($g === 'needs_attention' ? 'warning' : 'danger'))); ?>">
                                <?php echo e($lang === 'ar' ? __($g) : ucfirst(str_replace('_',' ',$g))); ?>

                            </span>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)"><?php echo e($ins->created_at->format('Y-m-d')); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conduct inspections')): ?>
                            <?php if(in_array($ins->status->value, ['draft','in_progress'])): ?>
                                <a href="<?php echo e(route('inspections.conduct', $ins)); ?>" class="btn btn-success btn-sm"><?php echo e($lang === 'ar' ? 'استكمال' : 'Continue'); ?></a>
                            <?php endif; ?>
                            <?php endif; ?>
                            <a href="<?php echo e(route('inspections.show', $ins)); ?>" class="btn btn-ghost btn-sm"><?php echo e(__('view')); ?></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete inspections')): ?>
                            <?php if($ins->status->value !== 'completed'): ?>
                                <form id="del-i-<?php echo e($ins->id); ?>" action="<?php echo e(route('inspections.destroy', $ins)); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-i-<?php echo e($ins->id); ?>', '<?php echo e($ins->reference_number); ?>')">🗑️</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center text-muted" style="padding:2rem"><?php echo e($lang === 'ar' ? 'لا توجد فحوصات' : 'No inspections found'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if($inspections->hasPages()): ?>
<div style="margin-top:1rem"><?php echo e($inspections->appends(request()->query())->links()); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<?php echo $__env->make('partials.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="modal" id="create-inspection-modal">
    <div class="modal-header">
        <h3>🔍 <?php echo e($lang==='ar' ? 'فحص جديد' : 'New Inspection'); ?></h3>
        <button class="modal-close" onclick="closeModal('create-inspection-modal')">✕</button>
    </div>
    <form method="POST" action="<?php echo e(route('inspections.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label"><?php echo e(__('vehicle')); ?> <span class="required">*</span></label>
                <select name="vehicle_id" id="m-vehicle" class="form-control" required>
                    <option value="">-- <?php echo e($lang==='ar' ? 'اختر المركبة' : 'Select Vehicle'); ?> --</option>
                    <?php $__currentLoopData = $vehicles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>" data-fuel="<?php echo e($v->fuel_type ?? ''); ?>"><?php echo e($v->year); ?> <?php echo e($v->make); ?> <?php echo e($v->model); ?><?php echo e($v->license_plate ? ' ('.$v->license_plate.')' : ''); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div id="m-fuel-info" style="display:none;margin-bottom:1rem;padding:.5rem .75rem;background:var(--gray-100);border-radius:8px;font-size:.82rem;color:var(--info)"></div>
            <div class="form-group">
                <label class="form-label">
                    <?php echo e($lang==='ar' ? 'قالب الفحص' : 'Template'); ?> <span class="required">*</span>
                    <span id="m-auto-label" style="display:none;font-size:.72rem;color:var(--success);font-weight:400;margin-right:.35rem">✓ <?php echo e($lang === 'ar' ? 'تلقائي' : 'Auto'); ?></span>
                </label>
                <select name="template_id" id="m-template" class="form-control" required>
                    <option value="">-- <?php echo e($lang==='ar' ? 'اختر القالب' : 'Select Template'); ?> --</option>
                    <?php $__currentLoopData = $templates ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->id); ?>" data-fuel="<?php echo e($t->fuel_type ?? ''); ?>"><?php echo e($t->name); ?><?php if($t->fuel_type): ?> [<?php echo e($t->fuel_type); ?>]<?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label"><?php echo e(__('notes')); ?></label>
                <textarea name="notes" class="form-control" rows="2" placeholder="<?php echo e($lang==='ar' ? 'ملاحظات...' : 'Notes...'); ?>"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('create-inspection-modal')"><?php echo e($lang==='ar' ? 'إلغاء' : 'Cancel'); ?></button>
            <button type="submit" class="btn btn-primary"><?php echo e($lang==='ar' ? 'إنشاء وبدء الفحص' : 'Create & Start'); ?></button>
        </div>
    </form>
</div>

<script src="<?php echo e(asset('js/inspections-index.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/inspections/index.blade.php ENDPATH**/ ?>