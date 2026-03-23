<?php $__env->startSection('title', __('vehicles')); ?>

<?php
    $lang = app()->getLocale();
    $fuelMap = [
        'gasoline' => $lang === 'ar' ? 'بنزين' : 'Gasoline',
        'diesel'   => $lang === 'ar' ? 'ديزل' : 'Diesel',
        'electric' => $lang === 'ar' ? 'كهربائي' : 'Electric',
        'hybrid'   => $lang === 'ar' ? 'هجين' : 'Hybrid',
        'lpg'      => $lang === 'ar' ? 'غاز' : 'LPG',
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e(__('vehicles')); ?></h1>
    <div class="header-actions">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create vehicles')): ?>
        <button type="button" class="btn btn-primary" onclick="openModal('vehicle-modal'); resetVehicleForm()">+ <?php echo e(__('add_vehicle')); ?></button>
        <?php endif; ?>
    </div>
</div>


<div class="card mb-2">
    <div class="card-body" style="padding:.75rem 1rem">
        <form method="GET" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="flex:1;min-width:200px" placeholder="<?php echo e($lang === 'ar' ? 'بحث بالاسم، VIN، أو رقم اللوحة...' : 'Search by name, VIN, or plate...'); ?>" value="<?php echo e(request('search')); ?>">
            <select name="fuel_type" class="form-control" style="width:auto;min-width:130px" onchange="this.form.submit()">
                <option value=""><?php echo e($lang === 'ar' ? 'كل الأنواع' : 'All Fuel Types'); ?></option>
                <?php $__currentLoopData = ['gasoline' => $lang==='ar' ? 'بنزين' : 'Gasoline', 'diesel' => $lang==='ar' ? 'ديزل' : 'Diesel', 'electric' => $lang==='ar' ? 'كهربائي' : 'Electric', 'hybrid' => $lang==='ar' ? 'هجين' : 'Hybrid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(request('fuel_type') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="sort" class="form-control" style="width:auto;min-width:130px" onchange="this.form.submit()">
                <option value="latest" <?php echo e(request('sort','latest')==='latest' ? 'selected' : ''); ?>><?php echo e($lang === 'ar' ? 'الأحدث' : 'Newest'); ?></option>
                <option value="name" <?php echo e(request('sort')==='name' ? 'selected' : ''); ?>><?php echo e($lang === 'ar' ? 'الاسم' : 'Name'); ?></option>
                <option value="mileage" <?php echo e(request('sort')==='mileage' ? 'selected' : ''); ?>><?php echo e($lang === 'ar' ? 'المسافة' : 'Mileage'); ?></option>
                <option value="inspections" <?php echo e(request('sort')==='inspections' ? 'selected' : ''); ?>><?php echo e($lang === 'ar' ? 'عدد الفحوصات' : 'Inspections'); ?></option>
            </select>
            <button type="submit" class="btn btn-secondary"><?php echo e(__('search')); ?></button>
            
            <div class="view-toggle">
                <button type="button" class="view-btn <?php echo e(request('view','table')==='table' ? 'active' : ''); ?>" onclick="setView('table')" title="<?php echo e($lang==='ar' ? 'جدول' : 'Table'); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <button type="button" class="view-btn <?php echo e(request('view')==='grid' ? 'active' : ''); ?>" onclick="setView('grid')" title="<?php echo e($lang==='ar' ? 'بطاقات' : 'Grid'); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                </button>
            </div>
            <input type="hidden" name="view" id="view-input" value="<?php echo e(request('view', 'table')); ?>">
        </form>
    </div>
</div>


<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
    <span class="text-muted" style="font-size:.82rem">
        <?php echo e($lang === 'ar' ? 'إجمالي:' : 'Total:'); ?> <strong><?php echo e($vehicles->total()); ?></strong> <?php echo e($lang === 'ar' ? 'مركبة' : 'vehicles'); ?>

    </span>
</div>


<div id="view-table" class="card" style="<?php echo e(request('view')==='grid' ? 'display:none' : ''); ?>">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('vehicle')); ?></th>
                    <th><?php echo e(__('plate_number')); ?></th>
                    <th><?php echo e(__('color')); ?></th>
                    <th><?php echo e($lang==='ar' ? 'الوقود' : 'Fuel'); ?></th>
                    <th><?php echo e(__('mileage')); ?></th>
                    <th><?php echo e(__('owner_name')); ?></th>
                    <th><?php echo e($lang==='ar' ? 'الفحوصات' : 'Insp.'); ?></th>
                    <th><?php echo e(__('actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.65rem">
                            <div style="width:36px;height:36px;border-radius:8px;background:var(--gray-100);display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0">🚗</div>
                            <div>
                                <div style="font-weight:700;font-size:.9rem"><?php echo e($vehicle->make); ?> <?php echo e($vehicle->model); ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?php echo e($vehicle->year); ?><?php echo e($vehicle->vin ? ' · '.$vehicle->vin : ''); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="font-mono" style="font-size:.85rem;background:#eff6ff;padding:.15rem .5rem;border-radius:4px;color:var(--primary);font-weight:600"><?php echo e($vehicle->license_plate ?? '-'); ?></span></td>
                    <td><?php echo e($vehicle->color ?? '-'); ?></td>
                    <td>
                        <?php if($vehicle->fuel_type): ?>
                            <span class="badge badge-info" style="font-size:.7rem"><?php echo e($fuelMap[$vehicle->fuel_type] ?? $vehicle->fuel_type); ?></span>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td><?php echo e($vehicle->mileage ? number_format($vehicle->mileage) : '-'); ?></td>
                    <td style="font-size:.85rem"><?php echo e($vehicle->owner_name ?? '-'); ?></td>
                    <td>
                        <span style="background:var(--gray-100);padding:.2rem .5rem;border-radius:10px;font-size:.78rem;font-weight:600"><?php echo e($vehicle->inspections_count); ?></span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('vehicles.show', $vehicle)); ?>" class="btn btn-ghost btn-sm"><?php echo e(__('view')); ?></a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit vehicles')): ?>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="editVehicle(<?php echo e(json_encode($vehicle)); ?>)"><?php echo e(__('edit')); ?></button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete vehicles')): ?>
                            <form id="del-v-<?php echo e($vehicle->id); ?>" action="<?php echo e(route('vehicles.destroy', $vehicle)); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-v-<?php echo e($vehicle->id); ?>', '<?php echo e($vehicle->full_name); ?>')">🗑️</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center text-muted" style="padding:2.5rem">
                    <div style="font-size:2rem;margin-bottom:.5rem">🚗</div>
                    <?php echo e($lang === 'ar' ? 'لا توجد مركبات' : 'No vehicles found'); ?>

                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="view-grid" class="vehicle-grid" style="<?php echo e(request('view','table')==='table' ? 'display:none' : ''); ?>">
    <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="vehicle-card">
        <div class="vehicle-image">🚗</div>
        <div class="vehicle-info">
            <div class="vehicle-name"><?php echo e($vehicle->full_name); ?></div>
            <?php if($vehicle->license_plate): ?>
                <div class="vehicle-plate"><?php echo e($vehicle->license_plate); ?></div>
            <?php endif; ?>
            <div class="vehicle-meta">
                <?php if($vehicle->color): ?><span><?php echo e($vehicle->color); ?></span><?php endif; ?>
                <?php if($vehicle->mileage): ?><span><?php echo e(number_format($vehicle->mileage)); ?> <?php echo e($lang === 'ar' ? 'كم' : 'km'); ?></span><?php endif; ?>
                <span><?php echo e($vehicle->inspections_count); ?> <?php echo e($lang === 'ar' ? 'فحص' : 'insp.'); ?></span>
            </div>
        </div>
        <div class="vehicle-actions">
            <a href="<?php echo e(route('vehicles.show', $vehicle)); ?>" class="btn btn-ghost btn-sm"><?php echo e(__('view')); ?></a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit vehicles')): ?>
            <button type="button" class="btn btn-secondary btn-sm" onclick="editVehicle(<?php echo e(json_encode($vehicle)); ?>)"><?php echo e(__('edit')); ?></button>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete vehicles')): ?>
            <form id="del-vg-<?php echo e($vehicle->id); ?>" action="<?php echo e(route('vehicles.destroy', $vehicle)); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-vg-<?php echo e($vehicle->id); ?>', '<?php echo e($vehicle->full_name); ?>')">🗑️</button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="empty-state" style="grid-column:1/-1">
        <div class="empty-state-icon">🚗</div>
        <h3><?php echo e($lang === 'ar' ? 'لا توجد مركبات' : 'No vehicles found'); ?></h3>
        <button type="button" class="btn btn-primary mt-2" onclick="openModal('vehicle-modal'); resetVehicleForm()">+ <?php echo e(__('add_vehicle')); ?></button>
    </div>
    <?php endif; ?>
</div>


<?php if($vehicles->hasPages()): ?>
<div style="margin-top:1rem"><?php echo e($vehicles->appends(request()->query())->links()); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<?php echo $__env->make('partials.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="modal modal-lg" id="vehicle-modal">
    <div class="modal-header">
        <h3 id="vehicle-modal-title">🚗 <?php echo e($lang === 'ar' ? 'إضافة مركبة' : 'Add Vehicle'); ?></h3>
        <button class="modal-close" onclick="closeModal('vehicle-modal')">✕</button>
    </div>
    <form id="vehicle-form" method="POST" action="<?php echo e(route('vehicles.store')); ?>" enctype="multipart/form-data" data-store-url="<?php echo e(route('vehicles.store')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_method" id="vehicle-method" value="POST">
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label"><?php echo e(__('make')); ?> <span class="required">*</span></label><input type="text" name="make" id="v-make" class="form-control" required></div>
                <div class="form-group"><label class="form-label"><?php echo e(__('model')); ?> <span class="required">*</span></label><input type="text" name="model" id="v-model" class="form-control" required></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label"><?php echo e(__('year')); ?> <span class="required">*</span></label><input type="number" name="year" id="v-year" class="form-control" value="<?php echo e(date('Y')); ?>" min="1900" max="<?php echo e(date('Y')+1); ?>" required></div>
                <div class="form-group"><label class="form-label"><?php echo e(__('color')); ?></label><input type="text" name="color" id="v-color" class="form-control"></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">VIN</label><input type="text" name="vin" id="v-vin" class="form-control font-mono" maxlength="17"></div>
                <div class="form-group"><label class="form-label"><?php echo e(__('plate_number')); ?></label><input type="text" name="license_plate" id="v-plate" class="form-control"></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label"><?php echo e(__('mileage')); ?></label><input type="number" name="mileage" id="v-mileage" class="form-control" min="0"></div>
                <div class="form-group">
                    <label class="form-label"><?php echo e($lang === 'ar' ? 'نوع الوقود' : 'Fuel Type'); ?></label>
                    <select name="fuel_type" id="v-fuel" class="form-control">
                        <option value="">--</option>
                        <?php $__currentLoopData = ['gasoline' => $lang==='ar' ? 'بنزين' : 'Gasoline', 'diesel' => $lang==='ar' ? 'ديزل' : 'Diesel', 'electric' => $lang==='ar' ? 'كهربائي' : 'Electric', 'hybrid' => $lang==='ar' ? 'هجين' : 'Hybrid', 'lpg' => $lang==='ar' ? 'غاز' : 'LPG']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label"><?php echo e($lang === 'ar' ? 'ناقل الحركة' : 'Transmission'); ?></label>
                <select name="transmission" id="v-transmission" class="form-control">
                    <option value="">--</option>
                    <option value="automatic"><?php echo e($lang === 'ar' ? 'أوتوماتيك' : 'Automatic'); ?></option>
                    <option value="manual"><?php echo e($lang === 'ar' ? 'يدوي' : 'Manual'); ?></option>
                </select>
            </div>
            <div style="margin:1rem 0 .5rem;padding-top:.75rem;border-top:1px solid var(--gray-200)">
                <label class="form-label" style="font-size:.92rem;font-weight:700"><?php echo e($lang === 'ar' ? 'العميل / المالك' : 'Customer / Owner'); ?></label>
            </div>
            <div class="form-group">
                <label class="form-label"><?php echo e($lang === 'ar' ? 'اختر عميل' : 'Select Customer'); ?></label>
                <select name="customer_id" id="v-customer" class="form-control" onchange="fillFromCustomer(this)">
                    <option value=""><?php echo e($lang === 'ar' ? '— بدون عميل —' : '— No customer —'); ?></option>
                    <?php $__currentLoopData = \App\Domain\Models\Customer::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" data-name="<?php echo e($c->name); ?>" data-phone="<?php echo e($c->phone); ?>" data-email="<?php echo e($c->email); ?>"><?php echo e($c->name); ?> <?php echo e($c->phone ? '('.$c->phone.')' : ''); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label"><?php echo e(__('owner_name')); ?></label><input type="text" name="owner_name" id="v-owner" class="form-control"></div>
                <div class="form-group"><label class="form-label"><?php echo e(__('owner_phone')); ?></label><input type="text" name="owner_phone" id="v-phone" class="form-control"></div>
            </div>
            <div class="form-group"><label class="form-label"><?php echo e($lang === 'ar' ? 'بريد المالك' : 'Owner Email'); ?></label><input type="email" name="owner_email" id="v-email" class="form-control"></div>
            <div class="form-group"><label class="form-label"><?php echo e(__('notes')); ?></label><textarea name="notes" id="v-notes" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('vehicle-modal')"><?php echo e($lang==='ar' ? 'إلغاء' : 'Cancel'); ?></button>
            <button type="submit" class="btn btn-primary" id="vehicle-submit-btn"><?php echo e($lang==='ar' ? 'حفظ' : 'Save'); ?></button>
        </div>
    </form>
</div>

<script src="<?php echo e(asset('js/vehicles-index.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/vehicles/index.blade.php ENDPATH**/ ?>