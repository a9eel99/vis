@extends('layouts.app')
@php $lang = app()->getLocale(); @endphp
@section('title', $lang === 'ar' ? 'العملاء' : 'Customers')

@section('content')
<div class="page-header">
    <h1>👥 {{ $lang === 'ar' ? 'العملاء' : 'Customers' }}</h1>
    <div class="header-actions">
        @can('create vehicles')
        <button type="button" class="btn btn-primary" onclick="openModal('customer-modal'); resetForm()">+ {{ $lang === 'ar' ? 'عميل جديد' : 'New Customer' }}</button>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="background:#d1fae5;border:1px solid #10b981;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:600">✅ {{ session('success') }}</div>
@endif

{{-- Search --}}
<div class="card mb-2">
    <div class="card-body" style="padding:12px 16px">
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ $lang === 'ar' ? '🔍 بحث بالاسم أو الهاتف...' : '🔍 Search name or phone...' }}" style="max-width:350px">
            <button type="submit" class="btn btn-secondary">{{ $lang === 'ar' ? 'بحث' : 'Search' }}</button>
            @if(request('search'))
            <a href="{{ route('customers.index') }}" class="btn btn-ghost">✕</a>
            @endif
            <span style="margin-{{ $lang === 'ar' ? 'right' : 'left' }}:auto;font-size:.82rem;color:var(--gray-500)">{{ $customers->total() }} {{ $lang === 'ar' ? 'عميل' : 'customers' }}</span>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $lang === 'ar' ? 'الاسم' : 'Name' }}</th>
                    <th>{{ $lang === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                    <th>{{ $lang === 'ar' ? 'البريد' : 'Email' }}</th>
                    <th>{{ $lang === 'ar' ? 'رقم الهوية' : 'ID' }}</th>
                    <th>{{ $lang === 'ar' ? 'المركبات' : 'Vehicles' }}</th>
                    <th>{{ $lang === 'ar' ? 'الفحوصات' : 'Inspections' }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td style="font-weight:600">
                        <a href="{{ route('customers.show', $c) }}" style="color:var(--gray-900);text-decoration:none">{{ $c->name }}</a>
                    </td>
                    <td style="font-size:.85rem">
                        @if($c->phone)
                        <a href="{{ $c->whatsapp_link }}" target="_blank" style="color:#25d366;text-decoration:none;display:inline-flex" title="WhatsApp"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                        {{ $c->phone }}
                        @else — @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)">{{ $c->email ?? '—' }}</td>
                    <td style="font-size:.82rem">{{ $c->id_number ?? '—' }}</td>
                    <td><span class="badge badge-primary">{{ $c->vehicles_count }}</span></td>
                    <td><span class="badge badge-{{ $c->inspections_count > 0 ? 'success' : 'secondary' }}">{{ $c->inspections_count }}</span></td>
                    <td>
                        <a href="{{ route('customers.show', $c) }}" class="btn btn-ghost btn-sm">{{ __('view') }}</a>
                        @can('delete vehicles')
                        <form id="del-c-{{ $c->id }}" action="{{ route('customers.destroy', $c) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
                        <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger)" onclick="confirmDelete('del-c-{{ $c->id }}', '{{ $c->name }}')">🗑️</button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">
                    {{ $lang === 'ar' ? 'لا يوجد عملاء بعد' : 'No customers yet' }}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div style="padding:12px 16px">{{ $customers->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

@section('modals')
@include('partials.delete-modal')

<div class="modal" id="customer-modal">
    <div class="modal-header">
        <h3>{{ $lang === 'ar' ? 'عميل جديد' : 'New Customer' }}</h3>
        <button class="modal-close" onclick="closeModal('customer-modal')">✕</button>
    </div>
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'اسم العميل' : 'Customer Name' }} *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                    <input type="text" name="phone" class="form-control" dir="ltr" placeholder="+962 7XXXXXXXX">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'البريد' : 'Email' }}</label>
                    <input type="email" name="email" class="form-control" dir="ltr">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'رقم الهوية' : 'ID Number' }}</label>
                    <input type="text" name="id_number" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang === 'ar' ? 'العنوان' : 'Address' }}</label>
                    <input type="text" name="address" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ $lang === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('customer-modal')">{{ $lang === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary">{{ $lang === 'ar' ? 'حفظ' : 'Save' }}</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/customers-index.js') }}"></script>
@endsection