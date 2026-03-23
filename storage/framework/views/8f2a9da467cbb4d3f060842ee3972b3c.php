<?php $__env->startSection('title', __('user_management')); ?>

<?php $lang = app()->getLocale(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e(__('user_management')); ?></h1>
    <div class="header-actions">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create users')): ?>
        <button type="button" class="btn btn-primary" onclick="openModal('user-modal'); resetUserForm()">+ <?php echo e(__('add_user')); ?></button>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('name')); ?></th>
                    <th><?php echo e(__('email')); ?></th>
                    <th><?php echo e(__('role')); ?></th>
                    <th><?php echo e(__('status')); ?></th>
                    <th><?php echo e(__('date')); ?></th>
                    <th><?php echo e(__('actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem"><?php echo e($user->initials); ?></div>
                            <strong><?php echo e($user->name); ?></strong>
                        </div>
                    </td>
                    <td style="font-size:.85rem"><?php echo e($user->email); ?></td>
                    <td>
                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge badge-primary"><?php echo e(ucfirst($role->name)); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo e($user->is_active ? 'success' : 'danger'); ?>">
                            <?php echo e($user->is_active ? ($lang==='ar'?'نشط':'Active') : ($lang==='ar'?'معطل':'Inactive')); ?>

                        </span>
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)"><?php echo e($user->created_at->format('Y-m-d')); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit users')): ?>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="editUser(<?php echo e(json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'phone'=>$user->phone,'role'=>$user->roles->first()?->name])); ?>)"><?php echo e(__('edit')); ?></button>
                            <form action="<?php echo e(route('users.toggleActive', $user)); ?>" method="POST" style="display:inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="btn btn-sm btn-<?php echo e($user->is_active ? 'ghost' : 'success'); ?>">
                                    <?php echo e($user->is_active ? ($lang==='ar'?'تعطيل':'Deactivate') : ($lang==='ar'?'تفعيل':'Activate')); ?>

                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete users')): ?>
                            <?php if(auth()->id() !== $user->id): ?>
                                <form id="del-u-<?php echo e($user->id); ?>" action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                <button type="button" class="btn btn-sm btn-ghost" style="color:var(--danger)" onclick="confirmDelete('del-u-<?php echo e($user->id); ?>', '<?php echo e($user->name); ?>')">🗑️</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted" style="padding:2rem"><?php echo e($lang==='ar' ? 'لا يوجد مستخدمين' : 'No users found'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(method_exists($users, 'links') && $users->hasPages()): ?>
    <div class="card-footer"><?php echo e($users->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<?php echo $__env->make('partials.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="modal modal-lg" id="user-modal">
    <div class="modal-header">
        <h3 id="user-modal-title"><?php echo e($lang==='ar' ? 'إضافة مستخدم' : 'Add User'); ?></h3>
        <button class="modal-close" onclick="closeModal('user-modal')">✕</button>
    </div>
    <form id="user-form" method="POST" action="<?php echo e(route('users.store')); ?>" data-store-url="<?php echo e(route('users.store')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_method" id="user-method" value="POST">
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"><?php echo e(__('name')); ?> <span class="required">*</span></label>
                    <input type="text" name="name" id="u-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo e(__('email')); ?> <span class="required">*</span></label>
                    <input type="email" name="email" id="u-email" class="form-control" required>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"><?php echo e(__('password')); ?> <span class="required" id="pwd-required">*</span></label>
                    <input type="password" name="password" id="u-password" class="form-control">
                    <small id="pwd-hint" class="text-muted" style="display:none"><?php echo e($lang==='ar' ? 'اتركه فارغاً لعدم التغيير' : 'Leave blank to keep unchanged'); ?></small>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo e($lang==='ar' ? 'تأكيد كلمة المرور' : 'Confirm Password'); ?></label>
                    <input type="password" name="password_confirmation" id="u-password-confirm" class="form-control">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"><?php echo e(__('phone')); ?></label>
                    <input type="text" name="phone" id="u-phone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo e(__('role')); ?> <span class="required">*</span></label>
                    <select name="role" id="u-role" class="form-control" required>
                        <option value="">-- <?php echo e($lang==='ar' ? 'اختر' : 'Select'); ?> --</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role->name); ?>"><?php echo e(ucfirst($role->name)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('user-modal')"><?php echo e($lang==='ar' ? 'إلغاء' : 'Cancel'); ?></button>
            <button type="submit" class="btn btn-primary" id="user-submit-btn"><?php echo e($lang==='ar' ? 'حفظ' : 'Save'); ?></button>
        </div>
    </form>
</div>

<script src="<?php echo e(asset('js/users-index.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/users/index.blade.php ENDPATH**/ ?>