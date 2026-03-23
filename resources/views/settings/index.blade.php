@extends('layouts.app')
@section('title', $lang === 'ar' ? 'الإعدادات' : 'Settings')

@section('content')
<div class="page-header">
    <h1>⚙️ {{ $lang === 'ar' ? 'إعدادات النظام' : 'System Settings' }}</h1>
</div>

@if(session('success'))
<div class="alert alert-success" data-auto-dismiss>
    ✅ {{ session('success') }}
</div>
@endif

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- Company Info --}}
<div class="card mb-2">
    <div class="card-header"><h3>🏢 {{ $lang === 'ar' ? 'معلومات المركز' : 'Center Information' }}</h3></div>
    <div class="card-body">

        {{-- Logo --}}
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--gray-100)">
            <label style="font-weight:600;display:block;margin-bottom:.5rem">{{ $lang === 'ar' ? 'شعار المركز' : 'Center Logo' }}</label>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                @if($settings['company_logo'])
                <div style="position:relative">
                    <img src="{{ Storage::url($settings['company_logo']) }}" alt="Logo"
                        style="max-width:120px;max-height:80px;border:2px solid var(--gray-200);border-radius:8px;padding:4px;background:var(--white)">
                    <label style="display:flex;align-items:center;gap:.3rem;margin-top:.4rem;font-size:.8rem;color:var(--danger);cursor:pointer">
                        <input type="checkbox" name="remove_logo" value="1"> {{ $lang === 'ar' ? 'حذف الشعار' : 'Remove logo' }}
                    </label>
                </div>
                @endif
                <div>
                    <input type="file" name="company_logo" accept="image/*" id="logo-input" style="display:none"
                        onchange="previewLogo(this)">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('logo-input').click()">
                        📷 {{ $lang === 'ar' ? ($settings['company_logo'] ? 'تغيير الشعار' : 'رفع شعار') : ($settings['company_logo'] ? 'Change Logo' : 'Upload Logo') }}
                    </button>
                    <div id="logo-preview" style="margin-top:.5rem"></div>
                    <div style="font-size:.75rem;color:var(--gray-500);margin-top:.3rem">PNG, JPG, SVG — {{ $lang === 'ar' ? 'حد أقصى 2MB' : 'Max 2MB' }}</div>
                </div>
            </div>
        </div>

        {{-- Favicon --}}
        <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--gray-100)">
            <label style="font-weight:600;display:block;margin-bottom:.5rem">{{ $lang === 'ar' ? 'أيقونة الموقع (Favicon)' : 'Site Favicon' }}</label>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                @if($settings['company_favicon'])
                <div style="position:relative">
                    <img src="{{ Storage::url($settings['company_favicon']) }}" alt="Favicon"
                        style="width:48px;height:48px;border:2px solid var(--gray-200);border-radius:8px;padding:4px;background:var(--white);object-fit:contain">
                    <label style="display:flex;align-items:center;gap:.3rem;margin-top:.4rem;font-size:.8rem;color:var(--danger);cursor:pointer">
                        <input type="checkbox" name="remove_favicon" value="1"> {{ $lang === 'ar' ? 'حذف الأيقونة' : 'Remove favicon' }}
                    </label>
                </div>
                @endif
                <div>
                    <input type="file" name="company_favicon" accept="image/png,image/x-icon,image/svg+xml" id="favicon-input" style="display:none"
                        onchange="previewFavicon(this)">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('favicon-input').click()">
                        🌐 {{ $lang === 'ar' ? ($settings['company_favicon'] ? 'تغيير الأيقونة' : 'رفع أيقونة') : ($settings['company_favicon'] ? 'Change Favicon' : 'Upload Favicon') }}
                    </button>
                    <div id="favicon-preview" style="margin-top:.5rem"></div>
                    <div style="font-size:.75rem;color:var(--gray-500);margin-top:.3rem">PNG, ICO, SVG — {{ $lang === 'ar' ? 'يفضل 32×32 أو 64×64 بكسل' : 'Recommended 32×32 or 64×64px' }}</div>
                </div>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'اسم المركز (عربي)' : 'Center Name (Arabic)' }} *</label>
                <input type="text" name="company_name_ar" value="{{ $settings['company_name_ar'] }}" class="form-control" dir="rtl" placeholder="مركز فحص المركبات">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'اسم المركز (إنجليزي)' : 'Center Name (English)' }}</label>
                <input type="text" name="company_name_en" value="{{ $settings['company_name_en'] }}" class="form-control" dir="ltr" placeholder="Vehicle Inspection Center">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'العنوان (عربي)' : 'Address (Arabic)' }}</label>
                <input type="text" name="company_address_ar" value="{{ $settings['company_address_ar'] }}" class="form-control" dir="rtl" placeholder="اربد - شارع الجامعة">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'العنوان (إنجليزي)' : 'Address (English)' }}</label>
                <input type="text" name="company_address_en" value="{{ $settings['company_address_en'] }}" class="form-control" dir="ltr" placeholder="Irbid - University St.">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                <input type="text" name="company_phone" value="{{ $settings['company_phone'] }}" class="form-control" dir="ltr" placeholder="+962 7 9999 9999">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="company_email" value="{{ $settings['company_email'] }}" class="form-control" dir="ltr" placeholder="info@center.com">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'الموقع الإلكتروني' : 'Website' }}</label>
                <input type="text" name="company_website" value="{{ $settings['company_website'] }}" class="form-control" dir="ltr" placeholder="www.center.com">
            </div>
            <div class="form-group">
                <label>{{ $lang === 'ar' ? 'الرقم الضريبي' : 'Tax Number' }}</label>
                <input type="text" name="company_tax_number" value="{{ $settings['company_tax_number'] }}" class="form-control" dir="ltr">
            </div>
        </div>
    </div>
</div>

{{-- Scoring --}}
<div class="card mb-2">
    <div class="card-header"><h3>📊 {{ $lang === 'ar' ? 'حدود التقييم' : 'Scoring Thresholds' }}</h3></div>
    <div class="card-body">
        <p style="font-size:.85rem;color:var(--gray-500);margin-bottom:1rem">
            {{ $lang === 'ar' ? 'حدد النسب المئوية لكل تقييم. مثلاً: 90% فما فوق = ممتاز' : 'Set percentage thresholds. Example: 90%+ = Excellent' }}
        </p>
        <div class="form-grid-2" style="max-width:600px">
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#10b981;display:inline-block"></span>
                    {{ $lang === 'ar' ? 'ممتاز (% فما فوق)' : 'Excellent (% and above)' }}
                </label>
                <input type="number" name="score_excellent" value="{{ $settings['score_excellent'] }}" class="form-control" min="1" max="100">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#3b82f6;display:inline-block"></span>
                    {{ $lang === 'ar' ? 'جيد (% فما فوق)' : 'Good (% and above)' }}
                </label>
                <input type="number" name="score_good" value="{{ $settings['score_good'] }}" class="form-control" min="1" max="100">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#f59e0b;display:inline-block"></span>
                    {{ $lang === 'ar' ? 'يحتاج اهتمام (% فما فوق)' : 'Needs Attention (% and above)' }}
                </label>
                <input type="number" name="score_needs_attention" value="{{ $settings['score_needs_attention'] }}" class="form-control" min="1" max="100">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#ef4444;display:inline-block"></span>
                    {{ $lang === 'ar' ? 'حرج (أقل من يحتاج اهتمام)' : 'Critical (below Needs Attention)' }}
                </label>
                <input type="text" class="form-control" disabled value="{{ $lang === 'ar' ? 'تلقائي' : 'Automatic' }}" style="background:var(--gray-50)">
            </div>
        </div>
    </div>
</div>

{{-- PDF Notes --}}
<div class="card mb-2">
    <div class="card-header"><h3>📄 {{ $lang === 'ar' ? 'ملاحظات PDF' : 'PDF Notes' }}</h3></div>
    <div class="card-body">
        <p style="font-size:.85rem;color:var(--gray-500);margin-bottom:1rem">
            {{ $lang === 'ar' ? 'تظهر هذه الملاحظات في أسفل تقرير الفحص PDF' : 'These notes appear at the bottom of the PDF inspection report' }}
        </p>
        <div class="form-group">
            <label>{{ $lang === 'ar' ? 'ملاحظات عربي' : 'Arabic Notes' }}</label>
            <textarea name="pdf_notes_ar" class="form-control" rows="3" dir="rtl" placeholder="{{ $lang === 'ar' ? 'هذا التقرير لأغراض إعلامية فقط...' : 'Arabic notes for PDF footer...' }}">{{ $settings['pdf_notes_ar'] }}</textarea>
        </div>
        <div class="form-group">
            <label>{{ $lang === 'ar' ? 'ملاحظات إنجليزي' : 'English Notes' }}</label>
            <textarea name="pdf_notes_en" class="form-control" rows="3" dir="ltr" placeholder="This report is for informational purposes only...">{{ $settings['pdf_notes_en'] }}</textarea>
        </div>
    </div>
</div>

{{-- PDF Preview --}}
<div class="card mb-2">
    <div class="card-header"><h3>👁 {{ $lang === 'ar' ? 'معاينة الهيدر' : 'Header Preview' }}</h3></div>
    <div class="card-body">
        <div id="header-preview" style="border:2px solid var(--gray-200);border-radius:8px;padding:16px;background:var(--white);text-align:center">
            <div id="prev-logo" style="margin-bottom:8px">
                @if($settings['company_logo'])
                <img src="{{ Storage::url($settings['company_logo']) }}" style="max-height:50px">
                @else
                <span style="color:var(--gray-400);font-size:.85rem">{{ $lang === 'ar' ? '(لا يوجد شعار)' : '(No logo)' }}</span>
                @endif
            </div>
            <div style="font-size:1.2rem;font-weight:700;color:var(--primary)" id="prev-name">{{ $settings['company_name_ar'] ?: $settings['company_name_en'] ?: '...' }}</div>
            <div style="font-size:.8rem;color:var(--gray-500)" id="prev-address">{{ $settings['company_address_ar'] ?: $settings['company_address_en'] }}</div>
            <div style="font-size:.8rem;color:var(--gray-500)" id="prev-contact">
                {{ $settings['company_phone'] }}{{ $settings['company_phone'] && $settings['company_email'] ? ' | ' : '' }}{{ $settings['company_email'] }}
            </div>
        </div>
    </div>
</div>

{{-- Theme & Appearance --}}
<div class="card mb-2">
    <div class="card-header"><h3>🎨 {{ $lang === 'ar' ? 'المظهر والألوان' : 'Appearance & Theme' }}</h3></div>
    <div class="card-body">

        {{-- Light/Dark Toggle --}}
        <div class="settings-section">
            <label class="form-label fw-700">{{ $lang === 'ar' ? 'المظهر' : 'Theme Mode' }}</label>
            <div class="form-grid-2" style="margin-top:6px">
                <button type="button" onclick="applyPreset('light',null)" class="btn btn-secondary" style="padding:12px;text-align:center">☀️ {{ $lang === 'ar' ? 'فاتح' : 'Light' }}</button>
                <button type="button" onclick="applyPreset('dark',null)" class="btn btn-secondary" style="padding:12px;text-align:center">🌙 {{ $lang === 'ar' ? 'داكن' : 'Dark' }}</button>
            </div>
        </div>

        {{-- Accent Color --}}
        <div class="settings-section">
            <label class="form-label fw-700">{{ $lang === 'ar' ? 'اللون الأساسي' : 'Accent Color' }}</label>
            <p class="settings-hint" style="margin-bottom:8px">{{ $lang === 'ar' ? 'اختر لون الأزرار والعناصر الرئيسية' : 'Choose the color for buttons and key elements' }}</p>
            <div class="accent-picker" id="accent-picker">
                @foreach(['#f59e0b'=>'orange','#3b82f6'=>'blue','#10b981'=>'green','#8b5cf6'=>'purple','#ef4444'=>'red','#ec4899'=>'pink','#14b8a6'=>'teal','#6366f1'=>'indigo'] as $hex => $name)
                <div class="accent-dot" style="background:{{ $hex }}" data-accent="{{ $name }}" onclick="pickAccent(this)"></div>
                @endforeach
                <label class="accent-dot custom-color-dot" id="custom-dot" title="{{ $lang === 'ar' ? 'لون مخصص' : 'Custom color' }}">
                    <span>🎨</span>
                    <input type="color" id="custom-color-input" value="#f59e0b" style="position:absolute;width:0;height:0;opacity:0" onchange="applyCustomColor(this.value)">
                </label>
            </div>
            <div id="custom-color-display" style="display:none;margin-top:8px">
                <span class="settings-hint">{{ $lang === 'ar' ? 'اللون المختار:' : 'Selected:' }} <code class="font-mono" id="custom-color-hex"></code></span>
                <button type="button" onclick="resetToPreset()" class="btn btn-ghost btn-sm" style="color:var(--danger)">{{ $lang === 'ar' ? 'إعادة تعيين' : 'Reset' }}</button>
            </div>
        </div>

        {{-- Theme Presets --}}
        <div>
            <label class="form-label fw-700">{{ $lang === 'ar' ? 'ثيمات جاهزة' : 'Theme Presets' }}</label>
            <div class="accent-presets">
                <button type="button" onclick="applyPreset('light','orange')" class="accent-preset-btn">
                    <div class="preset-dots"><span class="preset-dot" style="background:#1e3a5f"></span><span class="preset-dot" style="background:#f59e0b"></span><span class="preset-dot" style="background:#f9fafb"></span></div>
                    <div class="preset-label">{{ $lang === 'ar' ? 'كلاسيك' : 'Classic' }}</div>
                </button>
                <button type="button" onclick="applyPreset('light','blue')" class="accent-preset-btn">
                    <div class="preset-dots"><span class="preset-dot" style="background:#1e3a5f"></span><span class="preset-dot" style="background:#3b82f6"></span><span class="preset-dot" style="background:#f9fafb"></span></div>
                    <div class="preset-label">{{ $lang === 'ar' ? 'محيط' : 'Ocean' }}</div>
                </button>
                <button type="button" onclick="applyPreset('dark','green')" class="accent-preset-btn accent-preset-dark">
                    <div class="preset-dots"><span class="preset-dot" style="background:#0f172a"></span><span class="preset-dot" style="background:#10b981"></span><span class="preset-dot" style="background:#1e293b"></span></div>
                    <div class="preset-label preset-label-dark">{{ $lang === 'ar' ? 'ليلي' : 'Night' }}</div>
                </button>
                <button type="button" onclick="applyPreset('dark','purple')" class="accent-preset-btn accent-preset-dark">
                    <div class="preset-dots"><span class="preset-dot" style="background:#0f172a"></span><span class="preset-dot" style="background:#8b5cf6"></span><span class="preset-dot" style="background:#1e293b"></span></div>
                    <div class="preset-label preset-label-dark">{{ $lang === 'ar' ? 'بنفسجي' : 'Violet' }}</div>
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Save --}}
<div style="display:flex;justify-content:flex-end;gap:.5rem;margin-bottom:2rem">
    <button type="submit" class="btn btn-primary" style="min-width:200px;padding:12px 24px;font-size:1rem">
        💾 {{ $lang === 'ar' ? 'حفظ الإعدادات' : 'Save Settings' }}
    </button>
</div>

</form>

<script src="{{ asset('js/settings.js') }}"></script>
@endsection