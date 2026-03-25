@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'فحص جديد' : 'New Inspection')
@php $lang = app()->getLocale(); @endphp

@section('content')
<div class="page-header">
    <h1>{{ $lang === 'ar' ? '🔍 فحص جديد' : '🔍 New Inspection' }}</h1>
    <a href="{{ route('inspections.index') }}" class="btn btn-secondary">{{ $lang === 'ar' ? 'رجوع' : 'Back' }}</a>
</div>

<form method="POST" action="{{ route('inspections.store') }}" id="create-form">
    @csrf

    {{-- ===== MODE TABS ===== --}}
    <div class="card mb-2">
        <div class="card-body" style="padding:.75rem 1rem">
            <div style="display:flex;gap:.5rem">
                <button type="button" class="btn btn-sm mode-tab active" data-mode="existing" onclick="switchMode('existing')">
                    🚗 {{ $lang === 'ar' ? 'مركبة موجودة' : 'Existing Vehicle' }}
                </button>
                <button type="button" class="btn btn-sm mode-tab" data-mode="new" onclick="switchMode('new')">
                    ➕ {{ $lang === 'ar' ? 'مركبة جديدة + فحص مباشر' : 'New Vehicle + Quick Start' }}
                </button>
            </div>
        </div>
    </div>
    <input type="hidden" name="vehicle_mode" id="vehicle-mode" value="existing">

    {{-- ===== EXISTING VEHICLE MODE ===== --}}
    <div id="mode-existing">
        <div class="card mb-2">
            <div class="card-header"><h3>🚗 {{ $lang === 'ar' ? 'اختر المركبة' : 'Select Vehicle' }}</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <select name="vehicle_id" id="vehicle-select" class="form-control">
                        <option value="">-- {{ $lang === 'ar' ? 'اختر مركبة' : 'Choose vehicle' }} --</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" data-fuel="{{ $v->fuel_type }}"
                            {{ old('vehicle_id', request('vehicle_id')) == $v->id ? 'selected' : '' }}>
                            {{ $v->year }} {{ $v->make }} {{ $v->model }}
                            @if($v->license_plate)({{ $v->license_plate }})@endif
                            @if($v->owner_name) — {{ $v->owner_name }}@endif
                        </option>
                        @endforeach
                    </select>
                    <div id="fuel-info" style="display:none;margin-top:.5rem">
                        <span style="font-size:.78rem;padding:2px 10px;background:#eff6ff;color:var(--primary);border-radius:4px" id="fuel-info-text"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== NEW VEHICLE MODE ===== --}}
    <div id="mode-new" style="display:none">
        <div class="card mb-2">
            <div class="card-header"><h3>🚗 {{ $lang === 'ar' ? 'بيانات المركبة' : 'Vehicle Info' }}</h3></div>
            <div class="card-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'الشركة المصنّعة' : 'Make' }} *</label>
                        <select name="make" id="new-car-make" class="form-control new-field" required
                            data-value="{{ old('make') }}">
                            <option value="">{{ $lang === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'الموديل' : 'Model' }} *</label>
                        <select name="model" id="new-car-model" class="form-control new-field" required
                            data-value="{{ old('model') }}">
                            <option value="">{{ $lang === 'ar' ? 'اختر الشركة أولاً' : 'Select make first' }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'سنة الصنع' : 'Year' }} *</label>
                        <input type="number" name="year" class="form-control new-field" placeholder="{{ date('Y') }}" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('year') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'اللون' : 'Color' }}</label>
                        <select name="color" id="new-car-color" class="form-control"
                            data-value="{{ old('color') }}">
                            <option value="">{{ $lang === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">VIN</label>
                        <input type="text" name="vin" class="form-control" maxlength="17" placeholder="{{ $lang === 'ar' ? 'رقم الشاسيه' : 'Chassis number' }}" value="{{ old('vin') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'رقم اللوحة' : 'Plate' }}</label>
                        <input type="text" name="license_plate" id="new-plate" class="form-control" value="{{ old('license_plate') }}">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'الكيلومتر' : 'Mileage' }}</label>
                        <input type="number" name="mileage" class="form-control" min="0" value="{{ old('mileage') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</label>
                        <select name="fuel_type" class="form-control" id="new-fuel-select">
                            <option value="">--</option>
                            @foreach(['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid','lpg'=>$lang==='ar'?'غاز':'LPG'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ old('fuel_type') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Owner / Customer --}}
        <div class="card mb-2">
            <div class="card-header"><h3>👤 {{ $lang === 'ar' ? 'المالك / العميل' : 'Owner / Customer' }}</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'عميل مسجّل' : 'Existing Customer' }}</label>
                    <select name="customer_id" class="form-control" id="customer-select" onchange="fillCustomer(this)">
                        <option value="">{{ $lang === 'ar' ? '— بدون / عميل جديد —' : '— None / New —' }}</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" data-email="{{ $c->email }}">
                            {{ $c->name }} {{ $c->phone ? '('.$c->phone.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'اسم المالك' : 'Owner Name' }}</label>
                        <input type="text" name="owner_name" id="owner-name" class="form-control" value="{{ old('owner_name') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $lang === 'ar' ? 'هاتف المالك' : 'Owner Phone' }}</label>
                        <input type="text" name="owner_phone" id="owner-phone" class="form-control" value="{{ old('owner_phone') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'البريد' : 'Email' }}</label>
                    <input type="email" name="owner_email" id="owner-email" class="form-control" value="{{ old('owner_email') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TEMPLATE + INSPECTOR (shared) ===== --}}
    <div class="card mb-2">
        <div class="card-header"><h3>📋 {{ $lang === 'ar' ? 'قالب الفحص والفاحص' : 'Template & Inspector' }}</h3></div>
        <div class="card-body">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'قالب الفحص' : 'Template' }} *</label>
                    <select name="template_id" id="template-select" class="form-control" required>
                        <option value="">-- {{ $lang === 'ar' ? 'اختر قالب' : 'Choose template' }} --</option>
                        @foreach($templates as $t)
                        <option value="{{ $t->id }}" data-fuel="{{ $t->fuel_type }}" data-mode="{{ $t->scoring_mode ?? 'scored' }}"
                            {{ old('template_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                            @if($t->fuel_type) ({{ $t->fuel_type }}) @endif
                            @if(($t->scoring_mode ?? 'scored') === 'descriptive') — {{ $lang === 'ar' ? '📝 وصفي' : '📝 Desc' }} @endif
                        </option>
                        @endforeach
                    </select>
                    <span id="auto-label" style="display:none;font-size:.72rem;color:var(--success);margin-top:4px">{{ $lang === 'ar' ? '✓ تم الاختيار تلقائياً' : '✓ Auto-selected' }}</span>
                    <div id="template-info" style="display:none;margin-top:.5rem">
                        <span id="template-mode-badge" style="font-size:.75rem;padding:2px 8px;border-radius:4px"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'الفاحص' : 'Inspector' }}</label>
                    <select name="inspector_id" class="form-control">
                        <option value="">{{ $lang === 'ar' ? '— أنا الفاحص —' : '— I am inspector —' }}</option>
                        @foreach($inspectors as $ins)
                        <option value="{{ $ins->id }}" {{ old('inspector_id') == $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ $lang === 'ar' ? 'ملاحظات أولية (اختياري)' : 'Initial notes (optional)' }}">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div style="display:flex;justify-content:flex-end;gap:.5rem">
        <a href="{{ route('inspections.index') }}" class="btn btn-secondary">{{ $lang === 'ar' ? 'إلغاء' : 'Cancel' }}</a>
        <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
            🚀 {{ $lang === 'ar' ? 'إنشاء وبدء الفحص' : 'Create & Start Inspection' }}
        </button>
    </div>
</form>
@endsection

@section('modals')
<script src="{{ asset('js/car-selector.js') }}"></script>
<script src="{{ asset('js/plate-input.js') }}"></script>
<script src="{{ asset('js/inspections-create.js') }}"></script>
<script>
    initCarSelector('new-car-make', 'new-car-model', 'new-car-color');
    initPlateInput('new-plate');
</script>
@endsection