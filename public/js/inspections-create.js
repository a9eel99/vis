// ─── Mode Switching (existing / new vehicle) ───

function switchMode(mode) {
    document.getElementById('vehicle-mode').value = mode;
    document.getElementById('mode-existing').style.display = mode === 'existing' ? 'block' : 'none';
    document.getElementById('mode-new').style.display = mode === 'new' ? 'block' : 'none';

    var vSelect = document.getElementById('vehicle-select');
    if (vSelect) vSelect.required = (mode === 'existing');

    document.querySelectorAll('.new-field').forEach(function(f) {
        f.required = (mode === 'new');
    });

    document.querySelectorAll('.mode-tab').forEach(function(t) {
        t.classList.toggle('active', t.getAttribute('data-mode') === mode);
    });
}

// ─── Fill Customer Info ───

function fillCustomer(sel) {
    var opt = sel.options[sel.selectedIndex];
    var name = document.getElementById('owner-name');
    var phone = document.getElementById('owner-phone');
    var email = document.getElementById('owner-email');
    if (opt && opt.value) {
        if (name) name.value = opt.getAttribute('data-name') || '';
        if (phone) phone.value = opt.getAttribute('data-phone') || '';
        if (email) email.value = opt.getAttribute('data-email') || '';
    }
}

// ─── Vehicle Select → Auto-match Template ───

document.addEventListener('DOMContentLoaded', function() {
    var vSel = document.getElementById('vehicle-select');
    var tSel = document.getElementById('template-select');
    var autoLabel = document.getElementById('auto-label');
    var fuelInfo = document.getElementById('fuel-info');
    var fuelText = document.getElementById('fuel-info-text');
    var lang = document.documentElement.getAttribute('lang') || 'en';

    var fuels = {
        ar: { gasoline: 'بنزين', diesel: 'ديزل', electric: 'كهربائي', hybrid: 'هجين', lpg: 'غاز' },
        en: { gasoline: 'Gasoline', diesel: 'Diesel', electric: 'Electric', hybrid: 'Hybrid', lpg: 'LPG' }
    };

    if (vSel) {
        vSel.addEventListener('change', function() {
            var fuel = this.options[this.selectedIndex].getAttribute('data-fuel');
            autoLabel.style.display = 'none';
            fuelInfo.style.display = 'none';

            if (!fuel) return;

            fuelText.textContent = (lang === 'ar' ? 'نوع الوقود: ' : 'Fuel: ') + ((fuels[lang] && fuels[lang][fuel]) || fuel);
            fuelInfo.style.display = 'block';

            // Auto-match template by fuel type
            var matched = false;
            for (var i = 0; i < tSel.options.length; i++) {
                if (tSel.options[i].getAttribute('data-fuel') === fuel) {
                    tSel.selectedIndex = i;
                    autoLabel.style.display = 'inline';
                    matched = true;
                    break;
                }
            }

            // Fallback: pick first template without fuel restriction
            if (!matched) {
                for (var i = 0; i < tSel.options.length; i++) {
                    if (tSel.options[i].value && !tSel.options[i].getAttribute('data-fuel')) {
                        tSel.selectedIndex = i;
                        autoLabel.style.display = 'inline';
                        break;
                    }
                }
            }
        });

        // Trigger on page load if vehicle pre-selected
        if (vSel.value) vSel.dispatchEvent(new Event('change'));
    }

    // ─── New Vehicle: Fuel Select → Auto-match Template ───

    var newFuelSel = document.getElementById('new-fuel-select');
    if (newFuelSel) {
        newFuelSel.addEventListener('change', function() {
            var fuel = this.value;
            if (!fuel || !tSel) return;

            for (var i = 0; i < tSel.options.length; i++) {
                if (tSel.options[i].getAttribute('data-fuel') === fuel) {
                    tSel.selectedIndex = i;
                    if (autoLabel) autoLabel.style.display = 'inline';
                    break;
                }
            }
        });
    }
});