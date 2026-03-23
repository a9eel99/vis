/**
 * Inspection Details Page Scripts
 */

// ─── Copy Share Link ───

function copyShareLink() {
    var url = document.getElementById('share-url');
    if (!url || !url.value) return;
    var btn = document.getElementById('share-btn');
    var orig = btn.innerHTML;
    var lang = document.documentElement.getAttribute('lang') || 'en';

    function done() {
        btn.innerHTML = btn.dataset.copied || (lang === 'ar' ? '✅ تم النسخ!' : '✅ Copied!');
        btn.style.background = '#10b981';
        setTimeout(function() {
            btn.innerHTML = orig;
            btn.style.background = '';
        }, 2500);
    }

    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url.value).then(done).catch(function() {
            fallbackCopy(url.value);
            done();
        });
    } else {
        fallbackCopy(url.value);
        done();
    }
}

function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;left:-9999px;top:-9999px;opacity:0';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try {
        document.execCommand('copy');
    } catch (e) {
        // Silent fail
    }
    document.body.removeChild(ta);
}

// ─── Toggle Sections (expand/collapse) ───

function toggleSec(id) {
    var body = document.getElementById(id);
    var idx = id.replace('isec-', '');
    var arrow = document.getElementById('isec-arrow-' + idx);
    if (body.style.display === 'none') {
        body.style.display = 'block';
        if (arrow) arrow.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        if (arrow) arrow.classList.add('collapsed');
    }
}

// ─── Close modals on Escape key ───

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var hideModal = document.getElementById('hide-modal');
        if (hideModal && hideModal.style.display === 'flex') {
            hideModal.style.display = 'none';
        }
    }
});