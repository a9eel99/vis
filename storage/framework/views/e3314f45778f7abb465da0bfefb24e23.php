
<?php $__env->startSection('title', $lang === 'ar' ? 'الإعدادات' : 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>⚙️ <?php echo e($lang === 'ar' ? 'إعدادات النظام' : 'System Settings'); ?></h1>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success" data-auto-dismiss>
    ✅ <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<form action="<?php echo e(route('settings.update')); ?>" method="POST" enctype="multipart/form-data">
<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>


<div class="card mb-2">
    <div class="card-header"><h3>🏢 <?php echo e($lang === 'ar' ? 'معلومات المركز' : 'Center Information'); ?></h3></div>
    <div class="card-body">

        
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--gray-100)">
            <label style="font-weight:600;display:block;margin-bottom:.5rem"><?php echo e($lang === 'ar' ? 'شعار المركز' : 'Center Logo'); ?></label>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                <?php if($settings['company_logo']): ?>
                <div style="position:relative">
                    <img src="<?php echo e(Storage::url($settings['company_logo'])); ?>" alt="Logo"
                        style="max-width:120px;max-height:80px;border:2px solid var(--gray-200);border-radius:8px;padding:4px;background:var(--white)">
                    <label style="display:flex;align-items:center;gap:.3rem;margin-top:.4rem;font-size:.8rem;color:var(--danger);cursor:pointer">
                        <input type="checkbox" name="remove_logo" value="1"> <?php echo e($lang === 'ar' ? 'حذف الشعار' : 'Remove logo'); ?>

                    </label>
                </div>
                <?php endif; ?>
                <div>
                    <input type="file" name="company_logo" accept="image/*" id="logo-input" style="display:none"
                        onchange="previewLogo(this)">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('logo-input').click()">
                        📷 <?php echo e($lang === 'ar' ? ($settings['company_logo'] ? 'تغيير الشعار' : 'رفع شعار') : ($settings['company_logo'] ? 'Change Logo' : 'Upload Logo')); ?>

                    </button>
                    <div id="logo-preview" style="margin-top:.5rem"></div>
                    <div style="font-size:.75rem;color:var(--gray-500);margin-top:.3rem">PNG, JPG, SVG — <?php echo e($lang === 'ar' ? 'حد أقصى 2MB' : 'Max 2MB'); ?></div>
                </div>
            </div>
        </div>

        
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--gray-100)">
            <label style="font-weight:600;display:block;margin-bottom:.5rem"><?php echo e($lang === 'ar' ? 'أيقونة الموقع (Favicon)' : 'Site Favicon'); ?></label>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                <?php if($settings['company_favicon']): ?>
                <div style="position:relative">
                    <img src="<?php echo e(Storage::url($settings['company_favicon'])); ?>" alt="Favicon"
                        style="width:48px;height:48px;border:2px solid var(--gray-200);border-radius:8px;padding:4px;background:var(--white);object-fit:contain">
                    <label style="display:flex;align-items:center;gap:.3rem;margin-top:.4rem;font-size:.8rem;color:var(--danger);cursor:pointer">
                        <input type="checkbox" name="remove_favicon" value="1"> <?php echo e($lang === 'ar' ? 'حذف الأيقونة' : 'Remove favicon'); ?>

                    </label>
                </div>
                <?php endif; ?>
                <div>
                    <input type="file" name="company_favicon" accept="image/png,image/x-icon,image/svg+xml" id="favicon-input" style="display:none"
                        onchange="previewFavicon(this)">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('favicon-input').click()">
                        🌐 <?php echo e($lang === 'ar' ? ($settings['company_favicon'] ? 'تغيير الأيقونة' : 'رفع أيقونة') : ($settings['company_favicon'] ? 'Change Favicon' : 'Upload Favicon')); ?>

                    </button>
                    <div id="favicon-preview" style="margin-top:.5rem"></div>
                    <div style="font-size:.75rem;color:var(--gray-500);margin-top:.3rem">PNG, ICO, SVG — <?php echo e($lang === 'ar' ? 'يفضل 32×32 أو 64×64 بكسل' : 'Recommended 32×32 or 64×64px'); ?></div>
                </div>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'اسم المركز (عربي)' : 'Center Name (Arabic)'); ?> *</label>
                <input type="text" name="company_name_ar" value="<?php echo e($settings['company_name_ar']); ?>" class="form-control" dir="rtl" placeholder="مركز فحص المركبات">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'اسم المركز (إنجليزي)' : 'Center Name (English)'); ?></label>
                <input type="text" name="company_name_en" value="<?php echo e($settings['company_name_en']); ?>" class="form-control" dir="ltr" placeholder="Vehicle Inspection Center">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'العنوان (عربي)' : 'Address (Arabic)'); ?></label>
                <input type="text" name="company_address_ar" value="<?php echo e($settings['company_address_ar']); ?>" class="form-control" dir="rtl" placeholder="اربد - شارع الجامعة">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'العنوان (إنجليزي)' : 'Address (English)'); ?></label>
                <input type="text" name="company_address_en" value="<?php echo e($settings['company_address_en']); ?>" class="form-control" dir="ltr" placeholder="Irbid - University St.">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'الهاتف' : 'Phone'); ?></label>
                <input type="text" name="company_phone" value="<?php echo e($settings['company_phone']); ?>" class="form-control" dir="ltr" placeholder="+962 7 9999 9999">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'البريد الإلكتروني' : 'Email'); ?></label>
                <input type="email" name="company_email" value="<?php echo e($settings['company_email']); ?>" class="form-control" dir="ltr" placeholder="info@center.com">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'الموقع الإلكتروني' : 'Website'); ?></label>
                <input type="text" name="company_website" value="<?php echo e($settings['company_website']); ?>" class="form-control" dir="ltr" placeholder="www.center.com">
            </div>
            <div class="form-group">
                <label><?php echo e($lang === 'ar' ? 'الرقم الضريبي' : 'Tax Number'); ?></label>
                <input type="text" name="company_tax_number" value="<?php echo e($settings['company_tax_number']); ?>" class="form-control" dir="ltr">
            </div>
        </div>
    </div>
</div>


<div class="card mb-2">
    <div class="card-header"><h3>📊 <?php echo e($lang === 'ar' ? 'حدود التقييم' : 'Scoring Thresholds'); ?></h3></div>
    <div class="card-body">
        <p style="font-size:.85rem;color:var(--gray-500);margin-bottom:1rem">
            <?php echo e($lang === 'ar' ? 'حدد النسب المئوية لكل تقييم. مثلاً: 90% فما فوق = ممتاز' : 'Set percentage thresholds. Example: 90%+ = Excellent'); ?>

        </p>
        <div class="form-grid-2" style="max-width:600px">
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#10b981;display:inline-block"></span>
                    <?php echo e($lang === 'ar' ? 'ممتاز (% فما فوق)' : 'Excellent (% and above)'); ?>

                </label>
                <input type="number" name="score_excellent" value="<?php echo e($settings['score_excellent']); ?>" class="form-control" min="1" max="100">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#3b82f6;display:inline-block"></span>
                    <?php echo e($lang === 'ar' ? 'جيد (% فما فوق)' : 'Good (% and above)'); ?>

                </label>
                <input type="number" name="score_good" value="<?php echo e($settings['score_good']); ?>" class="form-control" min="1" max="100">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#f59e0b;display:inline-block"></span>
                    <?php echo e($lang === 'ar' ? 'يحتاج اهتمام (% فما فوق)' : 'Needs Attention (% and above)'); ?>

                </label>
                <input type="number" name="score_needs_attention" value="<?php echo e($settings['score_needs_attention']); ?>" class="form-control" min="1" max="100">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#ef4444;display:inline-block"></span>
                    <?php echo e($lang === 'ar' ? 'حرج (أقل من يحتاج اهتمام)' : 'Critical (below Needs Attention)'); ?>

                </label>
                <input type="text" class="form-control" disabled value="<?php echo e($lang === 'ar' ? 'تلقائي' : 'Automatic'); ?>" style="background:var(--gray-50)">
            </div>
        </div>
    </div>
</div>


<div class="card mb-2">
    <div class="card-header"><h3>📄 <?php echo e($lang === 'ar' ? 'ملاحظات PDF' : 'PDF Notes'); ?></h3></div>
    <div class="card-body">
        <p style="font-size:.85rem;color:var(--gray-500);margin-bottom:1rem">
            <?php echo e($lang === 'ar' ? 'تظهر هذه الملاحظات في أسفل تقرير الفحص PDF' : 'These notes appear at the bottom of the PDF inspection report'); ?>

        </p>
        <div class="form-group">
            <label><?php echo e($lang === 'ar' ? 'ملاحظات عربي' : 'Arabic Notes'); ?></label>
            <textarea name="pdf_notes_ar" class="form-control" rows="3" dir="rtl" placeholder="<?php echo e($lang === 'ar' ? 'هذا التقرير لأغراض إعلامية فقط...' : 'Arabic notes for PDF footer...'); ?>"><?php echo e($settings['pdf_notes_ar']); ?></textarea>
        </div>
        <div class="form-group">
            <label><?php echo e($lang === 'ar' ? 'ملاحظات إنجليزي' : 'English Notes'); ?></label>
            <textarea name="pdf_notes_en" class="form-control" rows="3" dir="ltr" placeholder="This report is for informational purposes only..."><?php echo e($settings['pdf_notes_en']); ?></textarea>
        </div>
    </div>
</div>


<div class="card mb-2">
    <div class="card-header"><h3>👁 <?php echo e($lang === 'ar' ? 'معاينة الهيدر' : 'Header Preview'); ?></h3></div>
    <div class="card-body">
        <div id="header-preview" style="border:2px solid var(--gray-200);border-radius:8px;padding:16px;background:var(--white);text-align:center">
            <div id="prev-logo" style="margin-bottom:8px">
                <?php if($settings['company_logo']): ?>
                <img src="<?php echo e(Storage::url($settings['company_logo'])); ?>" style="max-height:50px">
                <?php else: ?>
                <span style="color:var(--gray-400);font-size:.85rem"><?php echo e($lang === 'ar' ? '(لا يوجد شعار)' : '(No logo)'); ?></span>
                <?php endif; ?>
            </div>
            <div style="font-size:1.2rem;font-weight:700;color:var(--primary)" id="prev-name"><?php echo e($settings['company_name_ar'] ?: $settings['company_name_en'] ?: '...'); ?></div>
            <div style="font-size:.8rem;color:var(--gray-500)" id="prev-address"><?php echo e($settings['company_address_ar'] ?: $settings['company_address_en']); ?></div>
            <div style="font-size:.8rem;color:var(--gray-500)" id="prev-contact">
                <?php echo e($settings['company_phone']); ?><?php echo e($settings['company_phone'] && $settings['company_email'] ? ' | ' : ''); ?><?php echo e($settings['company_email']); ?>

            </div>
        </div>
    </div>
</div>


<div class="card mb-2">
    <div class="card-header"><h3>🎨 <?php echo e($lang === 'ar' ? 'المظهر والألوان' : 'Appearance & Theme'); ?></h3></div>
    <div class="card-body">

        
        <div class="settings-section">
            <label class="form-label fw-700"><?php echo e($lang === 'ar' ? 'المظهر' : 'Theme Mode'); ?></label>
            <div class="form-grid-2" style="margin-top:6px">
                <button type="button" onclick="applyPreset('light',null)" class="btn btn-secondary" style="padding:12px;text-align:center">☀️ <?php echo e($lang === 'ar' ? 'فاتح' : 'Light'); ?></button>
                <button type="button" onclick="applyPreset('dark',null)" class="btn btn-secondary" style="padding:12px;text-align:center">🌙 <?php echo e($lang === 'ar' ? 'داكن' : 'Dark'); ?></button>
            </div>
        </div>

        
        <div class="settings-section">
            <label class="form-label fw-700"><?php echo e($lang === 'ar' ? 'اللون الأساسي' : 'Accent Color'); ?></label>
            <p class="settings-hint" style="margin-bottom:8px"><?php echo e($lang === 'ar' ? 'اختر لون الأزرار والعناصر الرئيسية' : 'Choose the color for buttons and key elements'); ?></p>
            <div class="accent-picker" id="accent-picker">
                <?php $__currentLoopData = ['#f59e0b'=>'orange','#3b82f6'=>'blue','#10b981'=>'green','#8b5cf6'=>'purple','#ef4444'=>'red','#ec4899'=>'pink','#14b8a6'=>'teal','#6366f1'=>'indigo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hex => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="accent-dot" style="background:<?php echo e($hex); ?>" data-accent="<?php echo e($name); ?>" onclick="pickAccent(this)"></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <label class="accent-dot custom-color-dot" id="custom-dot" title="<?php echo e($lang === 'ar' ? 'لون مخصص' : 'Custom color'); ?>">
                    <span>🎨</span>
                    <input type="color" id="custom-color-input" value="#f59e0b" style="position:absolute;width:0;height:0;opacity:0" onchange="applyCustomColor(this.value)">
                </label>
            </div>
            <div id="custom-color-display" style="display:none;margin-top:8px">
                <span class="settings-hint"><?php echo e($lang === 'ar' ? 'اللون المختار:' : 'Selected:'); ?> <code class="font-mono" id="custom-color-hex"></code></span>
                <button type="button" onclick="resetToPreset()" class="btn btn-ghost btn-sm" style="color:var(--danger)"><?php echo e($lang === 'ar' ? 'إعادة تعيين' : 'Reset'); ?></button>
            </div>
        </div>

        
        <div>
            <label class="form-label fw-700"><?php echo e($lang === 'ar' ? 'ثيمات جاهزة' : 'Theme Presets'); ?></label>
            <div class="accent-presets">
                <button type="button" onclick="applyPreset('light','orange')" class="accent-preset-btn">
                    <div class="preset-dots"><span class="preset-dot" style="background:#1e3a5f"></span><span class="preset-dot" style="background:#f59e0b"></span><span class="preset-dot" style="background:#f9fafb"></span></div>
                    <div class="preset-label"><?php echo e($lang === 'ar' ? 'كلاسيك' : 'Classic'); ?></div>
                </button>
                <button type="button" onclick="applyPreset('light','blue')" class="accent-preset-btn">
                    <div class="preset-dots"><span class="preset-dot" style="background:#1e3a5f"></span><span class="preset-dot" style="background:#3b82f6"></span><span class="preset-dot" style="background:#f9fafb"></span></div>
                    <div class="preset-label"><?php echo e($lang === 'ar' ? 'محيط' : 'Ocean'); ?></div>
                </button>
                <button type="button" onclick="applyPreset('dark','green')" class="accent-preset-btn accent-preset-dark">
                    <div class="preset-dots"><span class="preset-dot" style="background:#0f172a"></span><span class="preset-dot" style="background:#10b981"></span><span class="preset-dot" style="background:#1e293b"></span></div>
                    <div class="preset-label preset-label-dark"><?php echo e($lang === 'ar' ? 'ليلي' : 'Night'); ?></div>
                </button>
                <button type="button" onclick="applyPreset('dark','purple')" class="accent-preset-btn accent-preset-dark">
                    <div class="preset-dots"><span class="preset-dot" style="background:#0f172a"></span><span class="preset-dot" style="background:#8b5cf6"></span><span class="preset-dot" style="background:#1e293b"></span></div>
                    <div class="preset-label preset-label-dark"><?php echo e($lang === 'ar' ? 'بنفسجي' : 'Violet'); ?></div>
                </button>
            </div>
        </div>

    </div>
</div>


<div style="display:flex;justify-content:flex-end;gap:.5rem;margin-bottom:2rem">
    <button type="submit" class="btn btn-primary" style="min-width:200px;padding:12px 24px;font-size:1rem">
        💾 <?php echo e($lang === 'ar' ? 'حفظ الإعدادات' : 'Save Settings'); ?>

    </button>
</div>

</form>

<script src="<?php echo e(asset('js/settings.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/settings/index.blade.php ENDPATH**/ ?>