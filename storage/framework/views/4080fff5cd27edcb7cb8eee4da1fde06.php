<?php $__env->startSection('title', app()->getLocale() === 'ar' ? 'فحص جديد' : 'New Inspection'); ?>
<?php $lang = app()->getLocale(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><?php echo e($lang === 'ar' ? '🔍 فحص جديد' : '🔍 New Inspection'); ?></h1>
    <a href="<?php echo e(route('inspections.index')); ?>" class="btn btn-secondary"><?php echo e($lang === 'ar' ? 'رجوع' : 'Back'); ?></a>
</div>

<form method="POST" action="<?php echo e(route('inspections.store')); ?>" id="create-form">
    <?php echo csrf_field(); ?>

    
    <div class="card mb-2">
        <div class="card-body" style="padding:.75rem 1rem">
            <div style="display:flex;gap:.5rem">
                <button type="button" class="btn btn-sm mode-tab active" data-mode="existing" onclick="switchMode('existing')">
                    🚗 <?php echo e($lang === 'ar' ? 'مركبة موجودة' : 'Existing Vehicle'); ?>

                </button>
                <button type="button" class="btn btn-sm mode-tab" data-mode="new" onclick="switchMode('new')">
                    ➕ <?php echo e($lang === 'ar' ? 'مركبة جديدة + فحص مباشر' : 'New Vehicle + Quick Start'); ?>

                </button>
            </div>
        </div>
    </div>
    <input type="hidden" name="vehicle_mode" id="vehicle-mode" value="existing">

    
    <div id="mode-existing">
        <div class="card mb-2">
            <div class="card-header"><h3>🚗 <?php echo e($lang === 'ar' ? 'اختر المركبة' : 'Select Vehicle'); ?></h3></div>
            <div class="card-body">
                <div class="form-group">
                    <select name="vehicle_id" id="vehicle-select" class="form-control">
                        <option value="">-- <?php echo e($lang === 'ar' ? 'اختر مركبة' : 'Choose vehicle'); ?> --</option>
                        <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>" data-fuel="<?php echo e($v->fuel_type); ?>"
                            <?php echo e(old('vehicle_id', request('vehicle_id')) == $v->id ? 'selected' : ''); ?>>
                            <?php echo e($v->year); ?> <?php echo e($v->make); ?> <?php echo e($v->model); ?>

                            <?php if($v->license_plate): ?>(<?php echo e($v->license_plate); ?>)<?php endif; ?>
                            <?php if($v->owner_name): ?> — <?php echo e($v->owner_name); ?><?php endif; ?>
                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="fuel-info" style="display:none;margin-top:.5rem">
                        <span style="font-size:.78rem;padding:2px 10px;background:#eff6ff;color:var(--primary);border-radius:4px" id="fuel-info-text"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="mode-new" style="display:none">
        <div class="card mb-2">
            <div class="card-header"><h3>🚗 <?php echo e($lang === 'ar' ? 'بيانات المركبة' : 'Vehicle Info'); ?></h3></div>
            <div class="card-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'الشركة المصنّعة' : 'Make'); ?> *</label>
                        <input type="text" name="make" class="form-control new-field" placeholder="<?php echo e($lang === 'ar' ? 'مثال: Toyota' : 'e.g. Toyota'); ?>" value="<?php echo e(old('make')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'الموديل' : 'Model'); ?> *</label>
                        <input type="text" name="model" class="form-control new-field" placeholder="<?php echo e($lang === 'ar' ? 'مثال: Camry' : 'e.g. Camry'); ?>" value="<?php echo e(old('model')); ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'سنة الصنع' : 'Year'); ?> *</label>
                        <input type="number" name="year" class="form-control new-field" placeholder="<?php echo e(date('Y')); ?>" min="1900" max="<?php echo e(date('Y') + 1); ?>" value="<?php echo e(old('year')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'اللون' : 'Color'); ?></label>
                        <input type="text" name="color" class="form-control" value="<?php echo e(old('color')); ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">VIN</label>
                        <input type="text" name="vin" class="form-control" maxlength="17" placeholder="<?php echo e($lang === 'ar' ? 'رقم الشاسيه' : 'Chassis number'); ?>" value="<?php echo e(old('vin')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'رقم اللوحة' : 'Plate'); ?></label>
                        <input type="text" name="license_plate" class="form-control" value="<?php echo e(old('license_plate')); ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'الكيلومتر' : 'Mileage'); ?></label>
                        <input type="number" name="mileage" class="form-control" min="0" value="<?php echo e(old('mileage')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'نوع الوقود' : 'Fuel Type'); ?></label>
                        <select name="fuel_type" class="form-control" id="new-fuel-select">
                            <option value="">--</option>
                            <?php $__currentLoopData = ['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid','lpg'=>$lang==='ar'?'غاز':'LPG']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(old('fuel_type') === $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card mb-2">
            <div class="card-header"><h3>👤 <?php echo e($lang === 'ar' ? 'المالك / العميل' : 'Owner / Customer'); ?></h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label"><?php echo e($lang === 'ar' ? 'عميل مسجّل' : 'Existing Customer'); ?></label>
                    <select name="customer_id" class="form-control" id="customer-select" onchange="fillCustomer(this)">
                        <option value=""><?php echo e($lang === 'ar' ? '— بدون / عميل جديد —' : '— None / New —'); ?></option>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" data-name="<?php echo e($c->name); ?>" data-phone="<?php echo e($c->phone); ?>" data-email="<?php echo e($c->email); ?>">
                            <?php echo e($c->name); ?> <?php echo e($c->phone ? '('.$c->phone.')' : ''); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'اسم المالك' : 'Owner Name'); ?></label>
                        <input type="text" name="owner_name" id="owner-name" class="form-control" value="<?php echo e(old('owner_name')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo e($lang === 'ar' ? 'هاتف المالك' : 'Owner Phone'); ?></label>
                        <input type="text" name="owner_phone" id="owner-phone" class="form-control" value="<?php echo e(old('owner_phone')); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo e($lang === 'ar' ? 'البريد' : 'Email'); ?></label>
                    <input type="email" name="owner_email" id="owner-email" class="form-control" value="<?php echo e(old('owner_email')); ?>">
                </div>
            </div>
        </div>
    </div>

    
    <div class="card mb-2">
        <div class="card-header"><h3>📋 <?php echo e($lang === 'ar' ? 'قالب الفحص والفاحص' : 'Template & Inspector'); ?></h3></div>
        <div class="card-body">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"><?php echo e($lang === 'ar' ? 'قالب الفحص' : 'Template'); ?> *</label>
                    <select name="template_id" id="template-select" class="form-control" required>
                        <option value="">-- <?php echo e($lang === 'ar' ? 'اختر قالب' : 'Choose template'); ?> --</option>
                        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->id); ?>" data-fuel="<?php echo e($t->fuel_type); ?>" data-mode="<?php echo e($t->scoring_mode ?? 'scored'); ?>"
                            <?php echo e(old('template_id') == $t->id ? 'selected' : ''); ?>>
                            <?php echo e($t->name); ?>

                            <?php if($t->fuel_type): ?> (<?php echo e($t->fuel_type); ?>) <?php endif; ?>
                            <?php if(($t->scoring_mode ?? 'scored') === 'descriptive'): ?> — <?php echo e($lang === 'ar' ? '📝 وصفي' : '📝 Desc'); ?> <?php endif; ?>
                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="auto-label" style="display:none;font-size:.72rem;color:var(--success);margin-top:4px"><?php echo e($lang === 'ar' ? '✓ تم الاختيار تلقائياً' : '✓ Auto-selected'); ?></span>
                    <div id="template-info" style="display:none;margin-top:.5rem">
                        <span id="template-mode-badge" style="font-size:.75rem;padding:2px 8px;border-radius:4px"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo e($lang === 'ar' ? 'الفاحص' : 'Inspector'); ?></label>
                    <select name="inspector_id" class="form-control">
                        <option value=""><?php echo e($lang === 'ar' ? '— أنا الفاحص —' : '— I am inspector —'); ?></option>
                        <?php $__currentLoopData = $inspectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ins->id); ?>" <?php echo e(old('inspector_id') == $ins->id ? 'selected' : ''); ?>><?php echo e($ins->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label"><?php echo e($lang === 'ar' ? 'ملاحظات' : 'Notes'); ?></label>
                <textarea name="notes" class="form-control" rows="2" placeholder="<?php echo e($lang === 'ar' ? 'ملاحظات أولية (اختياري)' : 'Initial notes (optional)'); ?>"><?php echo e(old('notes')); ?></textarea>
            </div>
        </div>
    </div>

    
    <div style="display:flex;justify-content:flex-end;gap:.5rem">
        <a href="<?php echo e(route('inspections.index')); ?>" class="btn btn-secondary"><?php echo e($lang === 'ar' ? 'إلغاء' : 'Cancel'); ?></a>
        <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
            🚀 <?php echo e($lang === 'ar' ? 'إنشاء وبدء الفحص' : 'Create & Start Inspection'); ?>

        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<script src="<?php echo e(asset('js/inspections-create.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/inspections/create.blade.php ENDPATH**/ ?>