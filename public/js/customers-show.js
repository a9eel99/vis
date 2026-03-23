function copyLink(url, btn) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function() { done(btn); });
    } else {
        var ta = document.createElement('textarea');
        ta.value = url; ta.style.position = 'fixed'; ta.style.left = '-9999px';
        document.body.appendChild(ta); ta.select(); document.execCommand('copy');
        document.body.removeChild(ta); done(btn);
    }
}
function done(btn) {
    var o = btn.innerHTML;
    btn.innerHTML = '✅';
    setTimeout(function() { btn.innerHTML = o; }, 1500);
}
function filterVehicles() {
    var q = document.getElementById('vehicle-search').value.toLowerCase();
    document.querySelectorAll('.link-vehicle-item').forEach(function(el) {
        el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
function confirmUnlink(action, name) {
    document.getElementById('unlink-vehicle-name').textContent = name;
    document.getElementById('unlink-form').action = action;
    openModal('unlink-modal');
}