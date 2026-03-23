/* ========================================
   Global App Scripts
   ======================================== */

/* Sidebar */
function toggleSidebar() {
    var sb = document.getElementById('sidebar');
    sb.classList.toggle('collapsed');
    localStorage.setItem('sidebar_collapsed', sb.classList.contains('collapsed') ? '1' : '0');
    setTimeout(function() { window.dispatchEvent(new Event('resize')); }, 350);
}
(function() {
    if (localStorage.getItem('sidebar_collapsed') === '1') {
        document.getElementById('sidebar').classList.add('collapsed');
    }
})();

/* Mobile Sidebar */
function openMobileSidebar() {
    var sb = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    sb.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
    var sb = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    sb.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Close sidebar when clicking a nav item (mobile only)
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth <= 1024) {
        document.querySelectorAll('.sidebar-nav .nav-item').forEach(function(item) {
            item.addEventListener('click', function() {
                closeMobileSidebar();
            });
        });
    }
});

// Close sidebar on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMobileSidebar();
        closeAllModals();
        closeAllDrawers();
    }
});

/* Auto-dismiss alerts */
document.querySelectorAll('[data-auto-dismiss]').forEach(function(el) {
    setTimeout(function() {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(function() { el.remove(); }, 400);
    }, 5000);
});

/* Modal System */
function openModal(id) {
    document.getElementById('modal-backdrop').classList.add('active');
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.getElementById('modal-backdrop').classList.remove('active');
    document.body.style.overflow = '';
}
function closeAllModals() {
    document.querySelectorAll('.modal.active').forEach(function(m) { m.classList.remove('active'); });
    var backdrop = document.getElementById('modal-backdrop');
    if (backdrop) backdrop.classList.remove('active');
    document.body.style.overflow = '';
}

/* Drawer System */
function openDrawer(id) {
    document.getElementById('drawer-backdrop').classList.add('active');
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeDrawer(id) {
    document.getElementById(id).classList.remove('active');
    document.getElementById('drawer-backdrop').classList.remove('active');
    document.body.style.overflow = '';
}
function closeAllDrawers() {
    document.querySelectorAll('.drawer.active').forEach(function(d) { d.classList.remove('active'); });
    var backdrop = document.getElementById('drawer-backdrop');
    if (backdrop) backdrop.classList.remove('active');
    document.body.style.overflow = '';
}

/* Delete Confirm */
function confirmDelete(formId, name) {
    var nameEl = document.getElementById('delete-item-name');
    var confirmForm = document.getElementById('delete-confirm-form');
    var methodField = document.getElementById('delete-method-field');
    if (nameEl) nameEl.textContent = name || '';
    if (confirmForm) {
        var srcForm = document.getElementById(formId);
        if (srcForm) {
            confirmForm.action = srcForm.action;
            var srcMethod = srcForm.querySelector('input[name="_method"]');
            if (srcMethod && methodField) {
                methodField.value = srcMethod.value;
                methodField.disabled = false;
            } else if (methodField) {
                methodField.disabled = true;
            }
        }
    }
    openModal('delete-modal');
}