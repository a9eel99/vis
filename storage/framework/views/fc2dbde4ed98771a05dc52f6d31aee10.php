<?php $__env->startSection('title', __('templates')); ?>

<?php
    $lang = app()->getLocale();
    $fuelMap = ['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid','lpg'=>$lang==='ar'?'غاز':'LPG'];
?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e(__('templates')); ?></h1>
    <div class="header-actions">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create templates')): ?>
        <a href="<?php echo e(route('templates.create')); ?>" class="btn btn-primary">+ <?php echo e($lang === 'ar' ? 'قالب جديد' : 'New Template'); ?></a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e($lang === 'ar' ? 'اسم القالب' : 'Template Name'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'النمط' : 'Mode'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'نوع الوقود' : 'Fuel Type'); ?></th>
                    <th><?php echo e($lang === 'ar' ? 'الأقسام' : 'Sections'); ?></th>
                    <th>💰 <?php echo e($lang === 'ar' ? 'السعر' : 'Price'); ?></th>
                    <th><?php echo e(__('status')); ?></th>
                    <th><?php echo e(__('actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="font-weight:600"><?php echo e($t->name); ?></div>
                        <?php if($t->description): ?>
                        <div style="font-size:.78rem;color:var(--gray-400);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo e(is_array($t->description) ? implode(', ', $t->description) : $t->description); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($t->isScored()): ?>
                            <span class="badge badge-info" style="font-size:.7rem">📊 <?php echo e($lang === 'ar' ? 'مُقيّم' : 'Scored'); ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary" style="font-size:.7rem">📝 <?php echo e($lang === 'ar' ? 'وصفي' : 'Descriptive'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($t->fuel_type): ?>
                            <span class="badge badge-info"><?php echo e($fuelMap[$t->fuel_type] ?? $t->fuel_type); ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?php echo e($lang === 'ar' ? 'عام' : 'General'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($t->sections->count()); ?></td>
                    <td>
                        <?php if($t->price > 0): ?>
                            <span style="font-weight:700;color:var(--success)"><?php echo e(number_format($t->price, 2)); ?></span>
                            <span style="font-size:.7rem;color:var(--gray-400)"><?php echo e($lang === 'ar' ? 'د.أ' : 'JOD'); ?></span>
                        <?php else: ?>
                            <span style="color:var(--gray-400)">—</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?php echo e($t->is_active ? 'success' : 'danger'); ?>"><?php echo e($t->is_active ? ($lang==='ar'?'نشط':'Active') : ($lang==='ar'?'معطل':'Inactive')); ?></span></td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('templates.show', $t)); ?>" class="btn btn-ghost btn-sm"><?php echo e(__('view')); ?></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit templates')): ?>
                            <a href="<?php echo e(route('templates.edit', $t)); ?>" class="btn btn-secondary btn-sm"><?php echo e(__('edit')); ?></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create templates')): ?>
                            <?php if(Route::has('templates.duplicate')): ?>
                            <form action="<?php echo e(route('templates.duplicate', $t)); ?>" method="POST" style="display:inline"><?php echo csrf_field(); ?><button type="submit" class="btn btn-sm btn-ghost"><?php echo e($lang === 'ar' ? 'نسخ' : 'Copy'); ?></button></form>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete templates')): ?>
                            <form id="del-t-<?php echo e($t->id); ?>" action="<?php echo e(route('templates.destroy', $t)); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-t-<?php echo e($t->id); ?>', '<?php echo e($t->name); ?>')">🗑️</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center text-muted" style="padding:2rem"><?php echo e($lang === 'ar' ? 'لا توجد قوالب' : 'No templates'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<?php echo $__env->make('partials.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/templates/index.blade.php ENDPATH**/ ?>