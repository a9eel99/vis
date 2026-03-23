function editVehicleShow() { openModal('edit-vehicle-modal'); }
function editFillCustomer(sel) {
    var opt = sel.options[sel.selectedIndex];
    var form = document.getElementById('edit-vehicle-modal');
    var owner = form.querySelector('input[name="owner_name"]');
    var phone = form.querySelector('input[name="owner_phone"]');
    var email = form.querySelector('input[name="owner_email"]');
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

/* Score History Chart */
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('scoreChart');
    if (!ctx || typeof Chart === 'undefined') return;
    var labels = JSON.parse(ctx.dataset.labels || '[]');
    var values = JSON.parse(ctx.dataset.values || '[]');
    var label = ctx.dataset.label || 'Score %';
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: values,
                borderColor: '#1e3a5f',
                backgroundColor: 'rgba(30,58,95,0.1)',
                borderWidth: 2.5,
                pointRadius: 5,
                pointBackgroundColor: '#1e3a5f',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: function(v) { return v + '%'; } } },
                x: { grid: { display: false } }
            }
        }
    });
});