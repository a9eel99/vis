@extends('layouts.app')
@section('title', $customer->name)

@php
    $lang = app()->getLocale();
    $completedInspections = $customer->inspections()->where('status', 'completed')->count();
    $avgScore = $customer->inspections()->where('status', 'completed')->avg('percentage');
    $avgScore = $avgScore ? round($avgScore, 1) : 0;
@endphp

@section('content')
<div class="page-header">
    <h1>👤 {{ $customer->name }}</h1>
    <div class="header-actions">
        @can('edit vehicles')
        <button type="button" class="btn btn-secondary" onclick="openModal('edit-customer-modal')">{{ __('edit') }}</button>
        @endcan
        @can('create inspections')
        <a href="{{ route('vehicles.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">+ {{ $lang === 'ar' ? 'مركبة جديدة' : 'New Vehicle' }}</a>
        @endcan
        @can('delete vehicles')
        <form id="del-customer" action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('del-customer', '{{ $customer->name }}')">🗑️</button>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="background:#d1fae5;border:1px solid #10b981;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:600">✅ {{ session('success') }}</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:16px">
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.72rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'المركبات' : 'Vehicles' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--primary)">{{ $customer->vehicles_count }}</div>
    </div>
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.72rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'الفحوصات' : 'Inspections' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--primary)">{{ $customer->inspections_count }}</div>
    </div>
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.72rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'متوسط النتيجة' : 'Avg Score' }}</div>
        <div style="font-size:1.8rem;font-weight:800;color:{{ $avgScore >= 75 ? '#10b981' : ($avgScore >= 50 ? '#f59e0b' : '#ef4444') }}">{{ $avgScore }}%</div>
    </div>
    <div class="card" style="text-align:center;padding:16px">
        <div style="font-size:.72rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'تواصل سريع' : 'Quick Contact' }}</div>
        <div style="display:flex;justify-content:center;gap:8px;margin-top:6px">
            @if($customer->phone)
            <a href="{{ $customer->whatsapp_link }}" target="_blank" style="background:#25d366;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none" title="WhatsApp"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
            <a href="tel:{{ $customer->phone }}" style="background:var(--primary);color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:1rem" title="{{ $lang === 'ar' ? 'اتصال' : 'Call' }}">📞</a>
            @endif
            @if($customer->email)
            <a href="mailto:{{ $customer->email }}" style="background:#3b82f6;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none" title="{{ $lang === 'ar' ? 'إيميل' : 'Email' }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg></a>
            @endif
        </div>
    </div>
</div>

<div class="form-grid-2 mb-2">
    {{-- Customer Info --}}
    <div class="card">
        <div class="card-header"><h3>📋 {{ $lang === 'ar' ? 'بيانات العميل' : 'Customer Info' }}</h3></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><label>{{ $lang === 'ar' ? 'الاسم' : 'Name' }}</label><span>{{ $customer->name }}</span></div>
                <div class="detail-item"><label>{{ $lang === 'ar' ? 'الهاتف' : 'Phone' }}</label><span dir="ltr">{{ $customer->phone ?? '—' }}</span></div>
                <div class="detail-item"><label>{{ $lang === 'ar' ? 'البريد' : 'Email' }}</label><span>{{ $customer->email ?? '—' }}</span></div>
                <div class="detail-item"><label>{{ $lang === 'ar' ? 'رقم الهوية' : 'ID Number' }}</label><span>{{ $customer->id_number ?? '—' }}</span></div>
                <div class="detail-item"><label>{{ $lang === 'ar' ? 'العنوان' : 'Address' }}</label><span>{{ $customer->address ?? '—' }}</span></div>
            </div>
            @if($customer->notes)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--gray-100)">
                <label style="font-size:.8rem;color:var(--gray-500);font-weight:600">{{ $lang === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                <p style="margin-top:.25rem;font-size:.88rem">{{ $customer->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Vehicles --}}
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
            <h3>🚗 {{ $lang === 'ar' ? 'المركبات' : 'Vehicles' }}</h3>
            <div style="display:flex;gap:6px">
                @can('create vehicles')
                <button type="button" class="btn btn-ghost btn-sm" onclick="openModal('link-vehicle-modal')">🔗 {{ $lang === 'ar' ? 'ربط موجودة' : 'Link Existing' }}</button>
                <a href="{{ route('vehicles.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary btn-sm">+ {{ $lang === 'ar' ? 'مركبة جديدة' : 'New Vehicle' }}</a>
                @endcan
            </div>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($customer->vehicles as $v)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--gray-100)">
                <a href="{{ route('vehicles.show', $v) }}" style="display:flex;align-items:center;gap:12px;flex:1;text-decoration:none;color:inherit">
                    <div style="width:40px;height:40px;background:var(--gray-100);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">🚗</div>
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:.88rem">{{ $v->full_name }}</div>
                        <div style="font-size:.75rem;color:var(--gray-400)">{{ $v->license_plate ?? '—' }} {{ $v->color ? '• '.$v->color : '' }}</div>
                    </div>
                    @if($v->latestInspection)
                    @php $g = is_object($v->latestInspection->grade) ? $v->latestInspection->grade->value : $v->latestInspection->grade; @endphp
                    <span class="badge badge-{{ $g === 'excellent' ? 'success' : ($g === 'good' ? 'primary' : ($g === 'needs_attention' ? 'warning' : 'danger')) }}" style="font-size:.72rem">
                        {{ $v->latestInspection->percentage }}%
                    </span>
                    @endif
                </a>
                @can('edit vehicles')
                <button type="button" class="btn btn-ghost btn-sm" style="color:var(--gray-400);font-size:.7rem" title="{{ $lang === 'ar' ? 'فك الربط' : 'Unlink' }}" onclick="confirmUnlink('{{ route('customers.unlink-vehicle', [$customer, $v]) }}', '{{ $v->full_name }}')">✕</button>
                @endcan
            </div>
            @empty
            <div style="padding:2rem;text-align:center;color:var(--gray-400)">
                {{ $lang === 'ar' ? 'لا توجد مركبات مربوطة' : 'No linked vehicles' }}
                @can('create vehicles')
                <br><button type="button" class="btn btn-ghost btn-sm" onclick="openModal('link-vehicle-modal')" style="margin-top:.5rem;color:var(--primary)">🔗 {{ $lang === 'ar' ? 'ربط مركبة' : 'Link a vehicle' }}</button>
                @endcan
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Inspections History --}}
<div class="card">
    <div class="card-header"><h3>📋 {{ $lang === 'ar' ? 'سجل الفحوصات' : 'Inspection History' }}</h3></div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $lang === 'ar' ? 'المرجع' : 'Ref' }}</th>
                    <th>{{ $lang === 'ar' ? 'المركبة' : 'Vehicle' }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ $lang === 'ar' ? 'النتيجة' : 'Score' }}</th>
                    <th>{{ $lang === 'ar' ? 'التقييم' : 'Grade' }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ $lang === 'ar' ? 'إرسال' : 'Send' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $ins)
                <tr>
                    <td class="font-mono" style="font-size:.82rem">
                        <a href="{{ route('inspections.show', $ins) }}">{{ $ins->reference_number }}</a>
                    </td>
                    <td style="font-size:.85rem">{{ $ins->vehicle?->full_name }}</td>
                    <td><span class="badge badge-{{ $ins->status->color() }}">{{ $ins->status->label() }}</span></td>
                    <td>
                        @if($ins->percentage)
                        <span style="font-weight:700;color:{{ $ins->percentage >= 75 ? '#10b981' : ($ins->percentage >= 50 ? '#f59e0b' : '#ef4444') }}">{{ $ins->percentage }}%</span>
                        @else — @endif
                    </td>
                    <td>
                        @if($ins->grade)
                        @php $g = is_object($ins->grade) ? $ins->grade->value : $ins->grade; @endphp
                        <span class="badge badge-{{ $g === 'excellent' ? 'success' : ($g === 'good' ? 'primary' : ($g === 'needs_attention' ? 'warning' : 'danger')) }}">{{ $lang === 'ar' ? __($g) : ucfirst(str_replace('_',' ',$g)) }}</span>
                        @else — @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)">{{ $ins->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if($ins->status->value === 'completed' && $ins->share_token)
                        <div style="display:flex;gap:4px">
                            @if($customer->phone)
                            <a href="{{ $customer->whatsapp_link }}?text={{ urlencode(($lang === 'ar' ? 'مرحباً '.$customer->name.'، تقرير فحص مركبتك: ' : 'Hi '.$customer->name.', your inspection report: ') . route('share.view', $ins->share_token)) }}" target="_blank" class="btn btn-ghost btn-sm" style="color:#25d366;padding:2px 6px" title="WhatsApp"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                            @endif
                            @if($customer->email)
                            <a href="mailto:{{ $customer->email }}?subject={{ urlencode(($lang === 'ar' ? 'تقرير فحص - ' : 'Inspection Report - ') . $ins->reference_number) }}&body={{ urlencode(($lang === 'ar' ? 'مرحباً '.$customer->name."\n\nتقرير فحص مركبتك جاهز:\n" : 'Hi '.$customer->name."\n\nYour inspection report is ready:\n") . route('share.view', $ins->share_token) . "\n\n" . route('share.pdf', $ins->share_token)) }}" class="btn btn-ghost btn-sm" style="color:#3b82f6;padding:2px 6px" title="Email"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg></a>
                            @endif
                            <button type="button" class="btn btn-ghost btn-sm" style="padding:2px 6px" onclick="copyLink('{{ route('share.view', $ins->share_token) }}', this)" title="{{ $lang === 'ar' ? 'نسخ الرابط' : 'Copy link' }}">🔗</button>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">{{ $lang === 'ar' ? 'لا توجد فحوصات' : 'No inspections' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($inspections->hasPages())
    <div style="padding:12px 16px">{{ $inspections->links() }}</div>
    @endif
</div>
@endsection

@section('modals')
@include('partials.delete-modal')

{{-- Edit Customer --}}
<div class="modal" id="edit-customer-modal">
    <div class="modal-header">
        <h3>{{ $lang === 'ar' ? 'تعديل بيانات العميل' : 'Edit Customer' }}</h3>
        <button class="modal-close" onclick="closeModal('edit-customer-modal')">✕</button>
    </div>
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf @method('PUT')
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'اسم العميل' : 'Name' }} *</label>
                <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                    <input type="text" name="phone" class="form-control" dir="ltr" value="{{ $customer->phone }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'البريد' : 'Email' }}</label>
                    <input type="email" name="email" class="form-control" dir="ltr" value="{{ $customer->email }}">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'رقم الهوية' : 'ID Number' }}</label>
                    <input type="text" name="id_number" class="form-control" value="{{ $customer->id_number }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'العنوان' : 'Address' }}</label>
                    <input type="text" name="address" class="form-control" value="{{ $customer->address }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                <textarea name="notes" class="form-control" rows="2">{{ $customer->notes }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('edit-customer-modal')">{{ $lang === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary">{{ $lang === 'ar' ? 'حفظ' : 'Save' }}</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/customers-show.js') }}"></script>

{{-- Unlink Vehicle Confirm Modal --}}
<div class="modal modal-sm" id="unlink-modal">
    <div class="modal-body" style="padding:2rem;text-align:center">
        <div style="font-size:2rem;margin-bottom:8px">🔗</div>
        <div style="font-weight:700;font-size:1.05rem;margin-bottom:8px">{{ $lang === 'ar' ? 'فك ربط المركبة' : 'Unlink Vehicle' }}</div>
        <div style="color:var(--gray-500);font-size:.9rem">
            {{ $lang === 'ar' ? 'هل تريد فك ربط' : 'Are you sure you want to unlink' }}
            <strong id="unlink-vehicle-name"></strong>
            {{ $lang === 'ar' ? 'من هذا العميل؟' : 'from this customer?' }}
        </div>
    </div>
    <div class="modal-footer" style="justify-content:center">
        <button type="button" class="btn btn-secondary" onclick="closeModal('unlink-modal')">{{ $lang === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
        <form id="unlink-form" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-danger">{{ $lang === 'ar' ? 'نعم، فك الربط' : 'Yes, Unlink' }}</button>
        </form>
    </div>
</div>

{{-- Link Existing Vehicle Modal --}}
<div class="modal" id="link-vehicle-modal">
    <div class="modal-header">
        <h3>🔗 {{ $lang === 'ar' ? 'ربط مركبة موجودة' : 'Link Existing Vehicle' }}</h3>
        <button class="modal-close" onclick="closeModal('link-vehicle-modal')">✕</button>
    </div>
    <div class="modal-body">
        <input type="text" id="vehicle-search" class="form-control" placeholder="{{ $lang === 'ar' ? '🔍 ابحث عن مركبة...' : '🔍 Search vehicle...' }}" oninput="filterVehicles()" style="margin-bottom:12px">
        <div style="max-height:350px;overflow-y:auto">
            @php $allVehicles = \App\Domain\Models\Vehicle::whereNull('customer_id')->orWhere('customer_id', '!=', $customer->id)->orderBy('make')->get(); @endphp
            @forelse($allVehicles as $v)
            <form action="{{ route('customers.link-vehicle', $customer) }}" method="POST" class="link-vehicle-item" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-bottom:1px solid var(--gray-100)">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $v->id }}">
                <div style="flex:1">
                    <div style="font-weight:600;font-size:.88rem">{{ $v->full_name }}</div>
                    <div style="font-size:.75rem;color:var(--gray-400)">{{ $v->license_plate ?? '—' }} {{ $v->owner_name ? '• '.$v->owner_name : '' }}</div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">{{ $lang === 'ar' ? 'ربط' : 'Link' }}</button>
            </form>
            @empty
            <div style="padding:2rem;text-align:center;color:var(--gray-400)">{{ $lang === 'ar' ? 'لا توجد مركبات متاحة' : 'No available vehicles' }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection