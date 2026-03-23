@extends('layouts.app')
@section('title', __('user_management'))

@php $lang = app()->getLocale(); @endphp

@section('content')
<div class="page-header">
    <h1>{{ __('user_management') }}</h1>
    <div class="header-actions">
        @can('create users')
        <button type="button" class="btn btn-primary" onclick="openModal('user-modal'); resetUserForm()">+ {{ __('add_user') }}</button>
        @endcan
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('name') }}</th>
                    <th>{{ __('email') }}</th>
                    <th>{{ __('role') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem">{{ $user->initials }}</div>
                            <strong>{{ $user->name }}</strong>
                        </div>
                    </td>
                    <td style="font-size:.85rem">{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? ($lang==='ar'?'نشط':'Active') : ($lang==='ar'?'معطل':'Inactive') }}
                        </span>
                    </td>
                    <td style="font-size:.82rem;color:var(--gray-500)">{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="action-buttons">
                            @can('edit users')
                            <button type="button" class="btn btn-sm btn-secondary" onclick="editUser({{ json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'phone'=>$user->phone,'role'=>$user->roles->first()?->name]) }})">{{ __('edit') }}</button>
                            <form action="{{ route('users.toggleActive', $user) }}" method="POST" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'ghost' : 'success' }}">
                                    {{ $user->is_active ? ($lang==='ar'?'تعطيل':'Deactivate') : ($lang==='ar'?'تفعيل':'Activate') }}
                                </button>
                            </form>
                            @endcan
                            @can('delete users')
                            @if(auth()->id() !== $user->id)
                                <form id="del-u-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" style="display:none">@csrf @method('DELETE')</form>
                                <button type="button" class="btn btn-sm btn-ghost" style="color:var(--danger)" onclick="confirmDelete('del-u-{{ $user->id }}', '{{ $user->name }}')">🗑️</button>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted" style="padding:2rem">{{ $lang==='ar' ? 'لا يوجد مستخدمين' : 'No users found' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($users, 'links') && $users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection

@section('modals')
@include('partials.delete-modal')

{{-- User Create/Edit Modal --}}
<div class="modal modal-lg" id="user-modal">
    <div class="modal-header">
        <h3 id="user-modal-title">{{ $lang==='ar' ? 'إضافة مستخدم' : 'Add User' }}</h3>
        <button class="modal-close" onclick="closeModal('user-modal')">✕</button>
    </div>
    <form id="user-form" method="POST" action="{{ route('users.store') }}" data-store-url="{{ route('users.store') }}">
        @csrf
        <input type="hidden" name="_method" id="user-method" value="POST">
        <div class="modal-body">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('name') }} <span class="required">*</span></label>
                    <input type="text" name="name" id="u-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('email') }} <span class="required">*</span></label>
                    <input type="email" name="email" id="u-email" class="form-control" required>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('password') }} <span class="required" id="pwd-required">*</span></label>
                    <input type="password" name="password" id="u-password" class="form-control">
                    <small id="pwd-hint" class="text-muted" style="display:none">{{ $lang==='ar' ? 'اتركه فارغاً لعدم التغيير' : 'Leave blank to keep unchanged' }}</small>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                    <input type="password" name="password_confirmation" id="u-password-confirm" class="form-control">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('phone') }}</label>
                    <input type="text" name="phone" id="u-phone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('role') }} <span class="required">*</span></label>
                    <select name="role" id="u-role" class="form-control" required>
                        <option value="">-- {{ $lang==='ar' ? 'اختر' : 'Select' }} --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('user-modal')">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary" id="user-submit-btn">{{ $lang==='ar' ? 'حفظ' : 'Save' }}</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/users-index.js') }}"></script>
@endsection