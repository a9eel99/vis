@extends('layouts.app')
@section('title', __('vehicles'))

@php
    $lang = app()->getLocale();
    $fuelMap = [
        'gasoline' => $lang === 'ar' ? 'بنزين' : 'Gasoline',
        'diesel'   => $lang === 'ar' ? 'ديزل' : 'Diesel',
        'electric' => $lang === 'ar' ? 'كهربائي' : 'Electric',
        'hybrid'   => $lang === 'ar' ? 'هجين' : 'Hybrid',
        'lpg'      => $lang === 'ar' ? 'غاز' : 'LPG',
    ];
@endphp

@section('content')
<div class="page-header">
    <h1>{{ __('vehicles') }}</h1>
    <div class="header-actions">
        @can('create vehicles')
        <button type="button" class="btn btn-primary" onclick="openModal('vehicle-modal'); resetVehicleForm()">+ {{ __('add_vehicle') }}</button>
        @endcan
    </div>
</div>

{{-- Search & Filters --}}
<div class="card mb-2">
    <div class="card-body" style="padding:.75rem 1rem">
        <form method="GET" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="flex:1;min-width:200px" placeholder="{{ $lang === 'ar' ? 'بحث بالاسم، VIN، أو رقم اللوحة...' : 'Search by name, VIN, or plate...' }}" value="{{ request('search') }}">
            <select name="fuel_type" class="form-control" style="width:auto;min-width:130px" onchange="this.form.submit()">
                <option value="">{{ $lang === 'ar' ? 'كل الأنواع' : 'All Fuel Types' }}</option>
                @foreach(['gasoline' => $lang==='ar' ? 'بنزين' : 'Gasoline', 'diesel' => $lang==='ar' ? 'ديزل' : 'Diesel', 'electric' => $lang==='ar' ? 'كهربائي' : 'Electric', 'hybrid' => $lang==='ar' ? 'هجين' : 'Hybrid'] as $val => $label)
                    <option value="{{ $val }}" {{ request('fuel_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="sort" class="form-control" style="width:auto;min-width:130px" onchange="this.form.submit()">
                <option value="latest" {{ request('sort','latest')==='latest' ? 'selected' : '' }}>{{ $lang === 'ar' ? 'الأحدث' : 'Newest' }}</option>
                <option value="name" {{ request('sort')==='name' ? 'selected' : '' }}>{{ $lang === 'ar' ? 'الاسم' : 'Name' }}</option>
                <option value="mileage" {{ request('sort')==='mileage' ? 'selected' : '' }}>{{ $lang === 'ar' ? 'المسافة' : 'Mileage' }}</option>
                <option value="inspections" {{ request('sort')==='inspections' ? 'selected' : '' }}>{{ $lang === 'ar' ? 'عدد الفحوصات' : 'Inspections' }}</option>
            </select>
            <button type="submit" class="btn btn-secondary">{{ __('search') }}</button>
            <div class="view-toggle">
                <button type="button" class="view-btn {{ request('view','table')==='table' ? 'active' : '' }}" onclick="setView('table')" title="{{ $lang==='ar' ? 'جدول' : 'Table' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <button type="button" class="view-btn {{ request('view')==='grid' ? 'active' : '' }}" onclick="setView('grid')" title="{{ $lang==='ar' ? 'بطاقات' : 'Grid' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                </button>
            </div>
            <input type="hidden" name="view" id="view-input" value="{{ request('view', 'table') }}">
        </form>
    </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
    <span class="text-muted" style="font-size:.82rem">
        {{ $lang === 'ar' ? 'إجمالي:' : 'Total:' }} <strong>{{ $vehicles->total() }}</strong> {{ $lang === 'ar' ? 'مركبة' : 'vehicles' }}
    </span>
</div>

{{-- TABLE VIEW --}}
<div id="view-table" class="card" style="{{ request('view')==='grid' ? 'display:none' : '' }}">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('vehicle') }}</th>
                    <th>{{ __('plate_number') }}</th>
                    <th>{{ __('color') }}</th>
                    <th>{{ $lang==='ar' ? 'الوقود' : 'Fuel' }}</th>
                    <th>{{ __('mileage') }}</th>
                    <th>{{ __('owner_name') }}</th>
                    <th>{{ $lang==='ar' ? 'الفحوصات' : 'Insp.' }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.65rem">
                            <div style="width:36px;height:36px;border-radius:8px;background:var(--gray-100);display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0">🚗</div>
                            <div>
                                <div style="font-weight:700;font-size:.9rem">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $vehicle->year }}{{ $vehicle->vin ? ' · '.$vehicle->vin : '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="font-mono" style="font-size:.85rem;background:#eff6ff;padding:.15rem .5rem;border-radius:4px;color:var(--primary);font-weight:600">{{ $vehicle->license_plate ?? '-' }}</span></td>
                    <td>{{ $vehicle->color ?? '-' }}</td>
                    <td>
                        @if($vehicle->fuel_type)
                            <span class="badge badge-info" style="font-size:.7rem">{{ $fuelMap[$vehicle->fuel_type] ?? $vehicle->fuel_type }}</span>
                        @else - @endif
                    </td>
                    <td>{{ $vehicle->mileage ? number_format($vehicle->mileage) : '-' }}</td>
                    <td style="font-size:.85rem">{{ $vehicle->owner_name ?? '-' }}</td>
                    <td>
                        <span style="background:var(--gray-100);padding:.2rem .5rem;border-radius:10px;font-size:.78rem;font-weight:600">{{ $vehicle->inspections_count }}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-ghost btn-sm">{{ __('view') }}</a>
                            @can('edit vehicles')
                            <button type="button" class="btn btn-secondary btn-sm" onclick="editVehicle({{ json_encode($vehicle) }})">{{ __('edit') }}</button>
                            @endcan
                            @can('delete vehicles')
                            <form id="del-v-{{ $vehicle->id }}" action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
                            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-v-{{ $vehicle->id }}', '{{ $vehicle->full_name }}')">🗑️</button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted" style="padding:2.5rem">
                    <div style="font-size:2rem;margin-bottom:.5rem">🚗</div>
                    {{ $lang === 'ar' ? 'لا توجد مركبات' : 'No vehicles found' }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- GRID VIEW --}}
<div id="view-grid" class="vehicle-grid" style="{{ request('view','table')==='table' ? 'display:none' : '' }}">
    @forelse($vehicles as $vehicle)
    <div class="vehicle-card">
        <div class="vehicle-image">🚗</div>
        <div class="vehicle-info">
            <div class="vehicle-name">{{ $vehicle->full_name }}</div>
            @if($vehicle->license_plate)
                <div class="vehicle-plate">{{ $vehicle->license_plate }}</div>
            @endif
            <div class="vehicle-meta">
                @if($vehicle->color)<span>{{ $vehicle->color }}</span>@endif
                @if($vehicle->mileage)<span>{{ number_format($vehicle->mileage) }} {{ $lang === 'ar' ? 'كم' : 'km' }}</span>@endif
                <span>{{ $vehicle->inspections_count }} {{ $lang === 'ar' ? 'فحص' : 'insp.' }}</span>
            </div>
        </div>
        <div class="vehicle-actions">
            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-ghost btn-sm">{{ __('view') }}</a>
            @can('edit vehicles')
            <button type="button" class="btn btn-secondary btn-sm" onclick="editVehicle({{ json_encode($vehicle) }})">{{ __('edit') }}</button>
            @endcan
            @can('delete vehicles')
            <form id="del-vg-{{ $vehicle->id }}" action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
            <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-vg-{{ $vehicle->id }}', '{{ $vehicle->full_name }}')">🗑️</button>
            @endcan
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column:1/-1">
        <div class="empty-state-icon">🚗</div>
        <h3>{{ $lang === 'ar' ? 'لا توجد مركبات' : 'No vehicles found' }}</h3>
        <button type="button" class="btn btn-primary mt-2" onclick="openModal('vehicle-modal'); resetVehicleForm()">+ {{ __('add_vehicle') }}</button>
    </div>
    @endforelse
</div>

@if($vehicles->hasPages())
<div style="margin-top:1rem">{{ $vehicles->appends(request()->query())->links() }}</div>
@endif
@endsection

@section('modals')
@include('partials.delete-modal')

{{-- Vehicle Create/Edit Modal --}}
<div class="modal modal-lg" id="vehicle-modal">
    <div class="modal-header">
        <h3 id="vehicle-modal-title">🚗 {{ $lang === 'ar' ? 'إضافة مركبة' : 'Add Vehicle' }}</h3>
        <button class="modal-close" onclick="closeModal('vehicle-modal')">✕</button>
    </div>
    <form id="vehicle-form" method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data" data-store-url="{{ route('vehicles.store') }}">
        @csrf
        <input type="hidden" name="_method" id="vehicle-method" value="POST">
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('make') }} <span class="required">*</span></label>
                    <select name="make" id="v-make" class="form-control" required data-value=""></select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('model') }} <span class="required">*</span></label>
                    <select name="model" id="v-model" class="form-control" required data-value=""></select>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('year') }} <span class="required">*</span></label>
                    <input type="number" name="year" id="v-year" class="form-control" value="{{ date('Y') }}" min="1900" max="{{ date('Y')+1 }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('color') }}</label>
                    <select name="color" id="v-color" class="form-control" data-value=""></select>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">VIN</label><input type="text" name="vin" id="v-vin" class="form-control font-mono" maxlength="17"></div>
                <div class="form-group"><label class="form-label">{{ __('plate_number') }}</label><input type="text" name="license_plate" id="v-plate" class="form-control"></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">{{ __('mileage') }}</label><input type="number" name="mileage" id="v-mileage" class="form-control" min="0"></div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</label>
                    <select name="fuel_type" id="v-fuel" class="form-control">
                        <option value="">--</option>
                        @foreach(['gasoline' => $lang==='ar' ? 'بنزين' : 'Gasoline', 'diesel' => $lang==='ar' ? 'ديزل' : 'Diesel', 'electric' => $lang==='ar' ? 'كهربائي' : 'Electric', 'hybrid' => $lang==='ar' ? 'هجين' : 'Hybrid', 'lpg' => $lang==='ar' ? 'غاز' : 'LPG'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'ناقل الحركة' : 'Transmission' }}</label>
                <select name="transmission" id="v-transmission" class="form-control">
                    <option value="">--</option>
                    <option value="automatic">{{ $lang === 'ar' ? 'أوتوماتيك' : 'Automatic' }}</option>
                    <option value="manual">{{ $lang === 'ar' ? 'يدوي' : 'Manual' }}</option>
                </select>
            </div>
            <div style="margin:1rem 0 .5rem;padding-top:.75rem;border-top:1px solid var(--gray-200)">
                <label class="form-label" style="font-size:.92rem;font-weight:700">{{ $lang === 'ar' ? 'العميل / المالك' : 'Customer / Owner' }}</label>
            </div>
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'اختر عميل' : 'Select Customer' }}</label>
                <select name="customer_id" id="v-customer" class="form-control" onchange="fillFromCustomer(this)">
                    <option value="">{{ $lang === 'ar' ? '— بدون عميل —' : '— No customer —' }}</option>
                    @foreach(\App\Domain\Models\Customer::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" data-email="{{ $c->email }}">{{ $c->name }} {{ $c->phone ? '('.$c->phone.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">{{ __('owner_name') }}</label><input type="text" name="owner_name" id="v-owner" class="form-control"></div>
                <div class="form-group"><label class="form-label">{{ __('owner_phone') }}</label><input type="text" name="owner_phone" id="v-phone" class="form-control"></div>
            </div>
            <div class="form-group"><label class="form-label">{{ $lang === 'ar' ? 'بريد المالك' : 'Owner Email' }}</label><input type="email" name="owner_email" id="v-email" class="form-control"></div>
            <div class="form-group"><label class="form-label">{{ __('notes') }}</label><textarea name="notes" id="v-notes" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('vehicle-modal')">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary" id="vehicle-submit-btn">{{ $lang==='ar' ? 'حفظ' : 'Save' }}</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/car-selector.js') }}"></script>
<script src="{{ asset('js/plate-input.js') }}"></script>
<script src="{{ asset('js/vehicles-index.js') }}"></script>
<script>
// Init car selector for modal
var modalCarInit = false;

function initModalCarSelector() {
    if (modalCarInit) return;
    modalCarInit = true;
    initCarSelector('v-make', 'v-model', 'v-color');
    initPlateInput('v-plate');
}

// Override resetVehicleForm to work with searchable selects
var _origReset = typeof resetVehicleForm === 'function' ? resetVehicleForm : null;

function resetVehicleForm() {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var f = document.getElementById('vehicle-form');
    f.action = f.dataset.storeUrl;
    document.getElementById('vehicle-method').value = 'POST';
    document.getElementById('vehicle-modal-title').textContent = lang === 'ar' ? '🚗 إضافة مركبة' : '🚗 Add Vehicle';
    document.getElementById('vehicle-submit-btn').textContent = lang === 'ar' ? 'حفظ' : 'Save';

    // Clear simple fields
    ['v-vin','v-plate','v-mileage','v-owner','v-phone','v-email','v-notes'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('v-year').value = new Date().getFullYear();
    document.getElementById('v-fuel').selectedIndex = 0;
    document.getElementById('v-transmission').selectedIndex = 0;
    document.getElementById('v-customer').value = '';
    ['v-owner','v-phone','v-email'].forEach(function(id) {
        var el = document.getElementById(id);
        el.readOnly = false;
        el.style.background = '';
    });

    // Reset searchable selects — remove pickers and re-init
    ['v-make','v-model','v-color'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.dataset.value = '';
            el.style.display = '';
            if (el._pickerWrap) { el._pickerWrap.remove(); el._pickerWrap = null; }
            el._picker = null;
        }
    });
    modalCarInit = false;
    initModalCarSelector();
}

function editVehicle(v) {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    document.getElementById('vehicle-form').action = '/vehicles/' + v.id;
    document.getElementById('vehicle-method').value = 'PUT';
    document.getElementById('vehicle-modal-title').textContent = lang === 'ar' ? '🚗 تعديل مركبة' : '🚗 Edit Vehicle';
    document.getElementById('vehicle-submit-btn').textContent = lang === 'ar' ? 'تحديث' : 'Update';

    // Set data-value for searchable selects
    document.getElementById('v-make').dataset.value = v.make || '';
    document.getElementById('v-model').dataset.value = v.model || '';
    document.getElementById('v-color').dataset.value = v.color || '';

    // Remove old pickers and re-init with values
    ['v-make','v-model','v-color'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.style.display = '';
            if (el._pickerWrap) { el._pickerWrap.remove(); el._pickerWrap = null; }
            el._picker = null;
        }
    });
    modalCarInit = false;
    initModalCarSelector();

    // Fill simple fields
    document.getElementById('v-year').value = v.year || new Date().getFullYear();
    document.getElementById('v-vin').value = v.vin || '';
    document.getElementById('v-plate').value = v.license_plate || '';
    document.getElementById('v-mileage').value = v.mileage || '';
    document.getElementById('v-owner').value = v.owner_name || '';
    document.getElementById('v-phone').value = v.owner_phone || '';
    document.getElementById('v-email').value = v.owner_email || '';
    document.getElementById('v-notes').value = v.notes || '';
    document.getElementById('v-customer').value = v.customer_id || '';
    fillFromCustomer(document.getElementById('v-customer'));

    var fuelSel = document.getElementById('v-fuel');
    for (var i = 0; i < fuelSel.options.length; i++) fuelSel.options[i].selected = fuelSel.options[i].value === (v.fuel_type || '');
    var transSel = document.getElementById('v-transmission');
    for (var i = 0; i < transSel.options.length; i++) transSel.options[i].selected = transSel.options[i].value === (v.transmission || '');

    openModal('vehicle-modal');
}
</script>
@endsection