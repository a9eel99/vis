/**
 * Settings Page Scripts
 * public/js/settings.js
 */

// ─── Image Previews ───

function previewLogo(input) {
    var preview = document.getElementById('logo-preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="settings-img-preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewFavicon(input) {
    var preview = document.getElementById('favicon-preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="settings-img-sm">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ─── Live Header Preview ───

document.addEventListener('DOMContentLoaded', function() {
    // Company name preview
    document.querySelectorAll('input[name="company_name_ar"], input[name="company_name_en"]').forEach(function(el) {
        el.addEventListener('input', function() {
            var ar = document.querySelector('input[name="company_name_ar"]').value;
            var en = document.querySelector('input[name="company_name_en"]').value;
            var prev = document.getElementById('prev-name');
            if (prev) prev.textContent = ar || en || '...';
        });
    });

    // Address preview
    document.querySelectorAll('input[name="company_address_ar"], input[name="company_address_en"]').forEach(function(el) {
        el.addEventListener('input', function() {
            var ar = document.querySelector('input[name="company_address_ar"]').value;
            var en = document.querySelector('input[name="company_address_en"]').value;
            var prev = document.getElementById('prev-address');
            if (prev) prev.textContent = ar || en || '';
        });
    });

    // Contact preview
    document.querySelectorAll('input[name="company_phone"], input[name="company_email"]').forEach(function(el) {
        el.addEventListener('input', function() {
            var ph = document.querySelector('input[name="company_phone"]').value;
            var em = document.querySelector('input[name="company_email"]').value;
            var prev = document.getElementById('prev-contact');
            if (prev) prev.textContent = ph + (ph && em ? ' | ' : '') + em;
        });
    });

    // Mark current accent on load
    initAccent();
});

// ─── Color Utilities ───

function getTextColor(hex) {
    var r = parseInt(hex.slice(1, 3), 16);
    var g = parseInt(hex.slice(3, 5), 16);
    var b = parseInt(hex.slice(5, 7), 16);
    var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.55 ? '#152d4a' : '#ffffff';
}

function darkenColor(hex, percent) {
    var r = parseInt(hex.slice(1, 3), 16);
    var g = parseInt(hex.slice(3, 5), 16);
    var b = parseInt(hex.slice(5, 7), 16);
    r = Math.max(0, Math.round(r * (1 - percent / 100)));
    g = Math.max(0, Math.round(g * (1 - percent / 100)));
    b = Math.max(0, Math.round(b * (1 - percent / 100)));
    return '#' + [r, g, b].map(function(c) { return c.toString(16).padStart(2, '0'); }).join('');
}

// ─── Custom Color Picker ───

function applyCustomColor(hex) {
    var root = document.documentElement;
    root.removeAttribute('data-accent');
    root.style.setProperty('--accent', hex);
    root.style.setProperty('--accent-hover', darkenColor(hex, 15));
    root.style.setProperty('--accent-text', getTextColor(hex));

    localStorage.setItem('vis-custom-accent', hex);
    localStorage.removeItem('vis-accent');

    // Update UI
    document.querySelectorAll('.accent-dot').forEach(function(d) { d.classList.remove('active'); });
    var customDot = document.getElementById('custom-dot');
    if (customDot) {
        customDot.classList.add('active');
        customDot.style.background = hex;
    }

    var display = document.getElementById('custom-color-display');
    var hexCode = document.getElementById('custom-color-hex');
    if (display) display.style.display = 'block';
    if (hexCode) hexCode.textContent = hex;
}

// ─── Preset Accent (from dots) ───

function pickAccent(el) {
    var root = document.documentElement;

    // Clear custom
    root.style.removeProperty('--accent');
    root.style.removeProperty('--accent-hover');
    root.style.removeProperty('--accent-text');
    localStorage.removeItem('vis-custom-accent');

    // Apply preset
    document.querySelectorAll('.accent-dot').forEach(function(d) { d.classList.remove('active'); });
    el.classList.add('active');
    root.setAttribute('data-accent', el.dataset.accent);
    localStorage.setItem('vis-accent', el.dataset.accent);

    // Hide custom display
    var display = document.getElementById('custom-color-display');
    if (display) display.style.display = 'none';

    var customDot = document.getElementById('custom-dot');
    if (customDot) customDot.style.background = 'var(--accent)';
}

// ─── Theme Presets ───

function applyPreset(mode, accent) {
    var root = document.documentElement;

    if (mode === 'dark') root.classList.add('dark');
    else root.classList.remove('dark');
    localStorage.setItem('vis-theme', mode);

    if (accent) {
        root.style.removeProperty('--accent');
        root.style.removeProperty('--accent-hover');
        root.style.removeProperty('--accent-text');
        localStorage.removeItem('vis-custom-accent');

        root.setAttribute('data-accent', accent);
        localStorage.setItem('vis-accent', accent);

        document.querySelectorAll('.accent-dot').forEach(function(d) {
            d.classList.toggle('active', d.dataset.accent === accent);
        });

        var display = document.getElementById('custom-color-display');
        if (display) display.style.display = 'none';
    }
}

// ─── Reset to Default ───

function resetToPreset() {
    var root = document.documentElement;
    root.style.removeProperty('--accent');
    root.style.removeProperty('--accent-hover');
    root.style.removeProperty('--accent-text');
    localStorage.removeItem('vis-custom-accent');

    root.setAttribute('data-accent', 'orange');
    localStorage.setItem('vis-accent', 'orange');

    document.querySelectorAll('.accent-dot').forEach(function(d) {
        d.classList.toggle('active', d.dataset.accent === 'orange');
    });

    var display = document.getElementById('custom-color-display');
    if (display) display.style.display = 'none';

    var customDot = document.getElementById('custom-dot');
    if (customDot) {
        customDot.classList.remove('active');
        customDot.style.background = 'var(--accent)';
    }
}

// ─── Init Accent on Page Load ───

function initAccent() {
    var customHex = localStorage.getItem('vis-custom-accent');
    var presetAccent = localStorage.getItem('vis-accent') || 'orange';

    if (customHex) {
        applyCustomColor(customHex);
        var input = document.getElementById('custom-color-input');
        if (input) input.value = customHex;
    } else {
        var dot = document.querySelector('.accent-dot[data-accent="' + presetAccent + '"]');
        if (dot) dot.classList.add('active');
    }
}