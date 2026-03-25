@extends('layouts.app')
@section('title', __('user_management'))

@php
    $lang = app()->getLocale();

    // Group permissions for display
    $permissionGroups = [
        'dashboard' => [
            'icon' => '📊',
            'label' => $lang === 'ar' ? 'لوحة التحكم' : 'Dashboard',
            'permissions' => ['view dashboard'],
        ],
        'vehicles' => [
            'icon' => '🚗',
            'label' => $lang === 'ar' ? 'المركبات' : 'Vehicles',
            'permissions' => ['view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles'],
        ],
        'inspections' => [
            'icon' => '📋',
            'label' => $lang === 'ar' ? 'الفحوصات' : 'Inspections',
            'permissions' => ['view inspections', 'create inspections', 'conduct inspections', 'delete inspections'],
        ],
        'templates' => [
            'icon' => '📁',
            'label' => $lang === 'ar' ? 'القوالب' : 'Templates',
            'permissions' => ['view templates', 'create templates', 'edit templates', 'delete templates'],
        ],
        'users' => [
            'icon' => '👥',
            'label' => $lang === 'ar' ? 'المستخدمون' : 'Users',
            'permissions' => ['view users', 'create users', 'edit users', 'delete users'],
        ],
        'reports' => [
            'icon' => '📈',
            'label' => $lang === 'ar' ? 'التقارير' : 'Reports',
            'permissions' => ['view reports', 'export reports', 'view audit logs'],
        ],
        'finance' => [
            'icon' => '💰',
            'label' => $lang === 'ar' ? 'المالية' : 'Finance',
            'permissions' => ['view finance', 'manage finance'],
        ],
    ];

    // Permission label translations
    $permLabels = [
        'view dashboard' => $lang === 'ar' ? 'عرض' : 'View',
        'view vehicles' => $lang === 'ar' ? 'عرض' : 'View',
        'create vehicles' => $lang === 'ar' ? 'إنشاء' : 'Create',
        'edit vehicles' => $lang === 'ar' ? 'تعديل' : 'Edit',
        'delete vehicles' => $lang === 'ar' ? 'حذف' : 'Delete',
        'manage vehicles' => $lang === 'ar' ? 'إدارة' : 'Manage',
        'view inspections' => $lang === 'ar' ? 'عرض' : 'View',
        'create inspections' => $lang === 'ar' ? 'إنشاء' : 'Create',
        'conduct inspections' => $lang === 'ar' ? 'تنفيذ' : 'Conduct',
        'delete inspections' => $lang === 'ar' ? 'حذف' : 'Delete',
        'manage inspections' => $lang === 'ar' ? 'إدارة' : 'Manage',
        'view templates' => $lang === 'ar' ? 'عرض' : 'View',
        'create templates' => $lang === 'ar' ? 'إنشاء' : 'Create',
        'edit templates' => $lang === 'ar' ? 'تعديل' : 'Edit',
        'delete templates' => $lang === 'ar' ? 'حذف' : 'Delete',
        'manage templates' => $lang === 'ar' ? 'إدارة' : 'Manage',
        'view users' => $lang === 'ar' ? 'عرض' : 'View',
        'create users' => $lang === 'ar' ? 'إنشاء' : 'Create',
        'edit users' => $lang === 'ar' ? 'تعديل' : 'Edit',
        'delete users' => $lang === 'ar' ? 'حذف' : 'Delete',
        'manage users' => $lang === 'ar' ? 'إدارة' : 'Manage',
        'view reports' => $lang === 'ar' ? 'عرض' : 'View',
        'export reports' => $lang === 'ar' ? 'تصدير' : 'Export',
        'view audit logs' => $lang === 'ar' ? 'سجل النظام' : 'Audit Logs',
        'view finance' => $lang === 'ar' ? 'عرض' : 'View',
        'manage finance' => $lang === 'ar' ? 'إدارة' : 'Manage',
    ];

    // Role → permissions map for JS
    $rolePermissions = [];
    foreach ($roles as $role) {
        $rolePermissions[$role->name] = $role->permissions->pluck('name')->toArray();
    }
@endphp

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
                            <button type="button" class="btn btn-sm btn-secondary" onclick="editUser({{ json_encode([
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'role' => $user->roles->first()?->name,
                                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                            ]) }})">{{ __('edit') }}</button>
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
                    <select name="role" id="u-role" class="form-control" required onchange="onRoleChange(this.value)">
                        <option value="">-- {{ $lang==='ar' ? 'اختر' : 'Select' }} --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Permissions Section --}}
            <div style="margin:1rem 0 .5rem;padding-top:.75rem;border-top:1px solid var(--gray-200)">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <label class="form-label" style="font-size:.92rem;font-weight:700;margin:0">🔐 {{ $lang === 'ar' ? 'الصلاحيات' : 'Permissions' }}</label>
                    <small style="color:var(--gray-400);font-size:.75rem">{{ $lang === 'ar' ? 'اختر الدور أولاً ثم عدّل حسب الحاجة' : 'Select role first, then customize' }}</small>
                </div>
            </div>

            <div id="permissions-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.75rem">
                @foreach($permissionGroups as $groupKey => $group)
                <div class="perm-group" style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:.75rem">
                    <div style="font-weight:600;font-size:.85rem;margin-bottom:.5rem;display:flex;align-items:center;gap:6px">
                        <span>{{ $group['icon'] }}</span> {{ $group['label'] }}
                        <label style="margin-right:auto;margin-left:auto"></label>
                        <label style="font-size:.7rem;color:var(--gray-400);cursor:pointer;font-weight:400" onclick="toggleGroupPerms('{{ $groupKey }}')">{{ $lang === 'ar' ? 'تحديد الكل' : 'Toggle all' }}</label>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px 12px">
                        @foreach($group['permissions'] as $perm)
                        <label style="display:flex;align-items:center;gap:4px;font-size:.8rem;cursor:pointer;padding:2px 0">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" class="perm-cb perm-{{ $groupKey }}" data-group="{{ $groupKey }}">
                            <span>{{ $permLabels[$perm] ?? $perm }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('user-modal')">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="submit" class="btn btn-primary" id="user-submit-btn">{{ $lang==='ar' ? 'حفظ' : 'Save' }}</button>
        </div>
    </form>
</div>

<script>
// Role → permissions mapping
var rolePermissions = @json($rolePermissions);

function onRoleChange(roleName) {
    // Uncheck all
    document.querySelectorAll('.perm-cb').forEach(function(cb) { cb.checked = false; });

    if (!roleName || roleName === 'Super Admin') {
        // Super Admin = all permissions
        if (roleName === 'Super Admin') {
            document.querySelectorAll('.perm-cb').forEach(function(cb) { cb.checked = true; });
        }
        return;
    }

    var perms = rolePermissions[roleName] || [];
    perms.forEach(function(p) {
        var cb = document.querySelector('.perm-cb[value="' + p + '"]');
        if (cb) cb.checked = true;
    });
}

function toggleGroupPerms(group) {
    var cbs = document.querySelectorAll('.perm-' + group);
    var allChecked = Array.from(cbs).every(function(cb) { return cb.checked; });
    cbs.forEach(function(cb) { cb.checked = !allChecked; });
}

function setPermissions(perms) {
    document.querySelectorAll('.perm-cb').forEach(function(cb) { cb.checked = false; });
    (perms || []).forEach(function(p) {
        var cb = document.querySelector('.perm-cb[value="' + p + '"]');
        if (cb) cb.checked = true;
    });
}

function resetUserForm() {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var form = document.getElementById('user-form');
    form.action = form.dataset.storeUrl;
    document.getElementById('user-method').value = 'POST';
    document.getElementById('user-modal-title').textContent = lang === 'ar' ? 'إضافة مستخدم' : 'Add User';
    document.getElementById('user-submit-btn').textContent = lang === 'ar' ? 'حفظ' : 'Save';
    ['u-name','u-email','u-password','u-password-confirm','u-phone'].forEach(function(id) { document.getElementById(id).value = ''; });
    document.getElementById('u-role').selectedIndex = 0;
    document.getElementById('u-password').required = true;
    document.getElementById('pwd-required').style.display = '';
    document.getElementById('pwd-hint').style.display = 'none';
    // Reset permissions
    document.querySelectorAll('.perm-cb').forEach(function(cb) { cb.checked = false; });
}

function editUser(u) {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    document.getElementById('user-form').action = '/users/' + u.id;
    document.getElementById('user-method').value = 'PUT';
    document.getElementById('user-modal-title').textContent = lang === 'ar' ? 'تعديل مستخدم' : 'Edit User';
    document.getElementById('user-submit-btn').textContent = lang === 'ar' ? 'تحديث' : 'Update';
    document.getElementById('u-name').value = u.name || '';
    document.getElementById('u-email').value = u.email || '';
    document.getElementById('u-phone').value = u.phone || '';
    document.getElementById('u-password').value = '';
    document.getElementById('u-password-confirm').value = '';
    document.getElementById('u-password').required = false;
    document.getElementById('pwd-required').style.display = 'none';
    document.getElementById('pwd-hint').style.display = 'block';
    var sel = document.getElementById('u-role');
    for (var i = 0; i < sel.options.length; i++) { sel.options[i].selected = sel.options[i].value === (u.role || ''); }
    // Set actual user permissions (not role defaults)
    setPermissions(u.permissions || []);
    openModal('user-modal');
}
</script>
@endsection