@extends('layouts.app')
@section('title', $vehicle->full_name)

@php
    $lang = app()->getLocale();
    $fuelMap = ['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid','lpg'=>$lang==='ar'?'غاز':'LPG'];

    $completedInspections = $vehicle->inspections->where('status.value', 'completed')->sortBy('completed_at');
    $scoredInspections = $completedInspections->filter(fn($i) => $i->template && $i->template->isScored());
    $totalInspections = $vehicle->inspections->count();
    $completedCount = $completedInspections->count();
    $scoredCount = $scoredInspections->count();
    $avgScore = $scoredCount > 0 ? round($scoredInspections->avg('percentage'), 1) : 0;
    $latestInspection = $completedInspections->last();
    $latestIsScored = $latestInspection?->template?->isScored() ?? false;
    $latestGrade = $latestInspection?->grade;
    $latestGradeStr = is_object($latestGrade) ? $latestGrade->value : ($latestGrade ?? '');

    // Trend: compare last 2 scored inspections
    $trend = 0;
    if ($scoredCount >= 2) {
        $sorted = $scoredInspections->values();
        $last = $sorted[$scoredCount - 1]->percentage;
        $prev = $sorted[$scoredCount - 2]->percentage;
        $trend = $last - $prev;
    }

    // Chart data (scored only)
    $chartLabels = $scoredInspections->map(fn($i) => $i->completed_at?->format('m/d'))->values()->toArray();
    $chartData = $scoredInspections->map(fn($i) => $i->percentage)->values()->toArray();
@endphp

@section('content')
<div class="page-header">
    <h1>{{ $vehicle->full_name }}</h1>
    <div class="header-actions">
        @can('edit vehicles')
        <button type="button" class="btn btn-secondary" onclick="editVehicleShow()">{{ __('edit') }}</button>
        @endcan
        @can('create inspections')
        <a href="{{ route('inspections.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-primary">+ {{ __('new_inspection') }}</a>
        @endcan
        @can('delete vehicles')
        <form id="del-vehicle" action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('del-vehicle', '{{ $vehicle->full_name }}')">🗑️</button>
        @endcan
    </div>
</div>

{{-- Stats Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:16px">
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.75rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'عدد الفحوصات' : 'Total Inspections' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--primary)">{{ $totalInspections }}</div>
    </div>
    @if($scoredCount > 0)
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.75rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'متوسط النتيجة' : 'Avg Score' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:{{ $avgScore >= 75 ? 'var(--success)' : ($avgScore >= 50 ? 'var(--warning)' : 'var(--danger)') }}">{{ $avgScore }}%</div>
    </div>
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.75rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'آخر تقييم' : 'Latest Grade' }}</div>
        <div style="font-size:1.3rem;font-weight:800;margin-top:4px">
            @if($latestIsScored && $latestGradeStr)
                <span class="badge badge-{{ $latestGradeStr === 'excellent' ? 'success' : ($latestGradeStr === 'good' ? 'primary' : ($latestGradeStr === 'needs_attention' ? 'warning' : 'danger')) }}" style="font-size:.9rem;padding:4px 12px">
                    {{ $lang === 'ar' ? __($latestGradeStr) : ucfirst(str_replace('_',' ',$latestGradeStr)) }}
                </span>
            @else — @endif
        </div>
    </div>
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.75rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'الاتجاه' : 'Trend' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:{{ $trend > 0 ? 'var(--success)' : ($trend < 0 ? 'var(--danger)' : 'var(--gray-500)') }}">
            @if($trend > 0) ↑ +{{ number_format($trend, 1) }}%
            @elseif($trend < 0) ↓ {{ number_format($trend, 1) }}%
            @else —
            @endif
        </div>
    </div>
    @else
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.75rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'المكتمل' : 'Completed' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--success)">{{ $completedCount }}</div>
    </div>
    @endif
</div>

<div class="form-grid-2 mb-2">
    {{-- Vehicle Info --}}
    <div class="card">
        <div class="card-header"><h3>🚗 {{ $lang === 'ar' ? 'معلومات المركبة' : 'Vehicle Info' }}</h3></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><label>{{ __('make') }}</label><span>{{ $vehicle->make }}</span></div>
                <div class="detail-item"><label>{{ __('model') }}</label><span>{{ $vehicle->model }}</span></div>
                <div class="detail-item"><label>{{ __('year') }}</label><span>{{ $vehicle->year }}</span></div>
                <div class="detail-item"><label>{{ __('vin') }}</label><span class="font-mono">{{ $vehicle->vin ?? '-' }}</span></div>
                <div class="detail-item"><label>{{ __('plate_number') }}</label><span class="font-mono">{{ $vehicle->license_plate ?? '-' }}</span></div>
                <div class="detail-item"><label>{{ __('color') }}</label><span>{{ $vehicle->color ?? '-' }}</span></div>
                <div class="detail-item"><label>{{ __('mileage') }}</label><span>{{ $vehicle->mileage ? number_format($vehicle->mileage) . ' km' : '-' }}</span></div>
                <div class="detail-item"><label>{{ $lang === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</label><span>{{ $fuelMap[$vehicle->fuel_type] ?? ($vehicle->fuel_type ?? '-') }}</span></div>
            </div>
        </div>
    </div>

    {{-- Owner + Chart --}}
    <div>
        <div class="card mb-2">
            <div class="card-header"><h3>👤 {{ $lang === 'ar' ? 'العميل / المالك' : 'Customer / Owner' }}</h3></div>
            <div class="card-body">
                @if($vehicle->customer)
                <div style="margin-bottom:.75rem;padding-bottom:.75rem;border-bottom:1px solid var(--gray-100)">
                    <a href="{{ route('customers.show', $vehicle->customer) }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:inherit">
                        <div style="width:36px;height:36px;border-radius:50%;background:#dbeafe;color:#1e40af;display:flex;align-items:center;justify-content:center;font-weight:700">{{ mb_substr($vehicle->customer->name, 0, 1) }}</div>
                        <div>
                            <div style="font-weight:600">{{ $vehicle->customer->name }}</div>
                            <div style="font-size:.75rem;color:var(--gray-400)">{{ $lang === 'ar' ? 'عرض ملف العميل' : 'View customer profile' }} →</div>
                        </div>
                    </a>
                </div>
                @endif
                <div class="detail-grid">
                    <div class="detail-item"><label>{{ __('owner_name') }}</label><span>{{ $vehicle->owner_name ?? '-' }}</span></div>
                    <div class="detail-item"><label>{{ __('owner_phone') }}</label><span>{{ $vehicle->owner_phone ?? '-' }}</span></div>
                    <div class="detail-item"><label>{{ $lang === 'ar' ? 'البريد' : 'Email' }}</label><span>{{ $vehicle->owner_email ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        @if($scoredCount >= 2)
        <div class="card">
            <div class="card-header"><h3>📈 {{ $lang === 'ar' ? 'تطور النتيجة' : 'Score Trend' }}</h3></div>
            <div class="card-body" style="padding:12px">
                <canvas id="scoreChart" height="150" data-labels="{{ json_encode($chartLabels) }}" data-values="{{ json_encode($chartData) }}" data-label="{{ $lang === 'ar' ? 'النتيجة %' : 'Score %' }}"></canvas>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Inspection History --}}
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
        <h3>📋 {{ $lang === 'ar' ? 'سجل الفحوصات' : 'Inspection History' }}</h3>
        <span style="font-size:.8rem;color:var(--gray-500)">{{ $totalInspections }} {{ $lang === 'ar' ? 'فحص' : 'inspections' }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $lang === 'ar' ? 'المرجع' : 'Ref' }}</th>
                    <th>{{ $lang === 'ar' ? 'القالب' : 'Template' }}</th>
                    <th>{{ $lang === 'ar' ? 'الفاحص' : 'Inspector' }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ $lang === 'ar' ? 'النتيجة' : 'Result' }}</th>
                    <th>{{ __('date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicle->inspections->sortByDesc('created_at') as $ins)
                @php $insIsScored = $ins->template?->isScored() ?? true; @endphp
                <tr>
                    <td class="font-mono" style="font-size:.82rem">{{ $ins->reference_number }}</td>
                    <td style="font-size:.82rem">
                        {{ $ins->template?->name ?? '-' }}
                        @if(!$insIsScored)
                            <span style="font-size:.6rem;background:var(--gray-100);padding:1px 6px;border-radius:4px;color:var(--gray-500)">{{ $lang === 'ar' ? 'وصفي' : 'Desc' }}</span>
                        @endif
                    </td>
                    <td>{{ $ins->inspector?->name ?? '-' }}</td>
                    <td><span class="badge badge-{{ $ins->status->color() }}">{{ $ins->status->label() }}</span></td>
                    <td>
                        @if($insIsScored && $ins->percentage)
                        <div style="display:flex;align-items:center;gap:6px">
                            <div style="width:50px;height:6px;background:var(--gray-200);border-radius:3px;overflow:hidden">
                                <div style="width:{{ $ins->percentage }}%;height:100%;background:{{ $ins->percentage >= 75 ? 'var(--success)' : ($ins->percentage >= 50 ? 'var(--warning)' : 'var(--danger)') }};border-radius:3px"></div>
                            </div>
                            <span style="font-weight:700;font-size:.85rem;color:{{ $ins->percentage >= 75 ? 'var(--success)' : ($ins->percentage >= 50 ? 'var(--warning)' : 'var(--danger)') }}">{{ $ins->percentage }}%</span>
                            @if($ins->has_critical_failure) <span style="color:var(--danger);font-size:.7rem;font-weight:700">⚠</span> @endif
                        </div>
                        @elseif(!$insIsScored && $ins->status->value === 'completed')
                            <span style="font-size:.78rem;color:var(--gray-500)">{{ $lang === 'ar' ? '✓ مكتمل' : '✓ Done' }}</span>
                        @else - @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)">{{ $ins->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('inspections.show', $ins) }}" class="btn btn-ghost btn-sm">{{ __('view') }}</a>
                        @if($ins->status->value === 'completed' && $ins->share_token)
                        <a href="{{ route('share.view', $ins->share_token) }}" target="_blank" class="btn btn-ghost btn-sm" title="{{ $lang === 'ar' ? 'رابط العميل' : 'Client Link' }}">🔗</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">
                    {{ $lang === 'ar' ? 'لا توجد فحوصات بعد' : 'No inspections yet' }}
                    @can('create inspections')
                    <br><a href="{{ route('inspections.create') }}?vehicle_id={{ $vehicle->id }}" style="color:var(--primary);font-weight:600">+ {{ $lang === 'ar' ? 'إنشاء أول فحص' : 'Create first inspection' }}</a>
                    @endcan
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('modals')
@include('partials.delete-modal')

{{-- Edit Vehicle --}}
<div class="modal modal-lg" id="edit-vehicle-modal">
    <div class="modal-header">
        <h3>{{ $lang === 'ar' ? 'تعديل المركبة' : 'Edit Vehicle' }}</h3>
        <button class="modal-close" onclick="closeModal('edit-vehicle-modal')">✕</button>
    </div>
    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">{{ __('make') }} *</label><input type="text" name="make" class="form-control" value="{{ $vehicle->make }}" required></div>
                <div class="form-group"><label class="form-label">{{ __('model') }} *</label><input type="text" name="model" class="form-control" value="{{ $vehicle->model }}" required></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">{{ __('year') }} *</label><input type="number" name="year" class="form-control" value="{{ $vehicle->year }}" required></div>
                <div class="form-group"><label class="form-label">{{ __('color') }}</label><input type="text" name="color" class="form-control" value="{{ $vehicle->color }}"></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">VIN</label><input type="text" name="vin" class="form-control font-mono" value="{{ $vehicle->vin }}" maxlength="17"></div>
                <div class="form-group"><label class="form-label">{{ __('plate_number') }}</label><input type="text" name="license_plate" class="form-control" value="{{ $vehicle->license_plate }}"></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">{{ __('mileage') }}</label><input type="number" name="mileage" class="form-control" value="{{ $vehicle->mileage }}"></div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</label>
                    <select name="fuel_type" class="form-control">
                        <option value="">--</option>
                        @foreach(['gasoline' => $lang === 'ar' ? 'بنزين' : 'Gasoline', 'diesel' => $lang === 'ar' ? 'ديزل' : 'Diesel', 'electric' => $lang === 'ar' ? 'كهربائي' : 'Electric', 'hybrid' => $lang === 'ar' ? 'هجين' : 'Hybrid', 'lpg' => $lang === 'ar' ? 'غاز' : 'LPG'] as $val => $label)
                            <option value="{{ $val }}" {{ $vehicle->fuel_type === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin:1rem 0 .5rem;padding-top:1rem;border-top:1px solid var(--gray-200)"><label class="form-label" style="font-size:.9rem">{{ $lang === 'ar' ? 'العميل / المالك' : 'Customer / Owner' }}</label></div>
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'اختر عميل' : 'Select Customer' }}</label>
                <select name="customer_id" class="form-control" onchange="editFillCustomer(this)">
                    <option value="">{{ $lang === 'ar' ? '— بدون عميل —' : '— No customer —' }}</option>
                    @foreach(\App\Domain\Models\Customer::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}" {{ $vehicle->customer_id === $c->id ? 'selected' : '' }} data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" data-email="{{ $c->email }}">{{ $c->name }} {{ $c->phone ? '('.$c->phone.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grid-2">
                <div class="form-group"><label class="form-label">{{ __('owner_name') }}</label><input type="text" name="owner_name" class="form-control" value="{{ $vehicle->owner_name }}"></div>
                <div class="form-group"><label class="form-label">{{ __('owner_phone') }}</label><input type="text" name="owner_phone" class="form-control" value="{{ $vehicle->owner_phone }}"></div>
            </div>
            <div class="form-group"><label class="form-label">{{ $lang === 'ar' ? 'بريد المالك' : 'Email' }}</label><input type="email" name="owner_email" class="form-control" value="{{ $vehicle->owner_email }}"></div>
            <div class="form-group"><label class="form-label">{{ __('notes') }}</label><textarea name="notes" class="form-control" rows="2">{{ $vehicle->notes }}</textarea></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('edit-vehicle-modal')">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary">{{ $lang==='ar' ? 'تحديث' : 'Update' }}</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/vehicles-show.js') }}"></script>
@if($scoredCount >= 2)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
@endif
@endsection