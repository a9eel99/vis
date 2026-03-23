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
    openModal('user-modal');
}