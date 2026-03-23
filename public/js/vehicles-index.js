function setView(v) {
    document.getElementById('view-input').value = v;
    document.getElementById('view-table').style.display = v === 'table' ? '' : 'none';
    document.getElementById('view-grid').style.display = v === 'grid' ? '' : 'none';
    document.querySelectorAll('.view-btn').forEach(function(b) { b.classList.remove('active'); });
    event.currentTarget.classList.add('active');
}
function resetVehicleForm() {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var f = document.getElementById('vehicle-form');
    f.action = f.dataset.storeUrl;
    document.getElementById('vehicle-method').value = 'POST';
    document.getElementById('vehicle-modal-title').textContent = lang === 'ar' ? '🚗 إضافة مركبة' : '🚗 Add Vehicle';
    document.getElementById('vehicle-submit-btn').textContent = lang === 'ar' ? 'حفظ' : 'Save';
    ['v-make','v-model','v-color','v-vin','v-plate','v-mileage','v-owner','v-phone','v-email','v-notes'].forEach(function(id) { var el = document.getElementById(id); if (el) el.value = ''; });
    document.getElementById('v-year').value = new Date().getFullYear();
    document.getElementById('v-fuel').selectedIndex = 0;
    document.getElementById('v-transmission').selectedIndex = 0;
    document.getElementById('v-customer').value = '';
    ['v-owner','v-phone','v-email'].forEach(function(id) { var el = document.getElementById(id); el.readOnly = false; el.style.background = ''; });
}
function editVehicle(v) {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    document.getElementById('vehicle-form').action = '/vehicles/' + v.id;
    document.getElementById('vehicle-method').value = 'PUT';
    document.getElementById('vehicle-modal-title').textContent = lang === 'ar' ? '🚗 تعديل مركبة' : '🚗 Edit Vehicle';
    document.getElementById('vehicle-submit-btn').textContent = lang === 'ar' ? 'تحديث' : 'Update';
    document.getElementById('v-make').value = v.make || '';
    document.getElementById('v-model').value = v.model || '';
    document.getElementById('v-year').value = v.year || new Date().getFullYear();
    document.getElementById('v-color').value = v.color || '';
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
function fillFromCustomer(sel) {
    var opt = sel.options[sel.selectedIndex];
    var owner = document.getElementById('v-owner');
    var phone = document.getElementById('v-phone');
    var email = document.getElementById('v-email');
    if (opt && opt.value) {
        owner.value = opt.getAttribute('data-name') || '';
        phone.value = opt.getAttribute('data-phone') || '';
        email.value = opt.getAttribute('data-email') || '';
        owner.readOnly = true; phone.readOnly = true; email.readOnly = true;
        owner.style.background = 'var(--gray-50)'; phone.style.background = 'var(--gray-50)'; email.style.background = 'var(--gray-50)';
    } else {
        owner.readOnly = false; phone.readOnly = false; email.readOnly = false;
        owner.style.background = ''; phone.style.background = ''; email.style.background = '';
    }
}