function showLogDetails(data) {
    var body = document.getElementById('log-details-body');
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var html = '<div style="font-weight:700;margin-bottom:.75rem;font-size:.95rem">' + data.action + '</div>';
    if (data.old && Object.keys(data.old).length) {
        html += '<div style="margin-bottom:1rem"><div style="font-size:.82rem;font-weight:600;color:var(--danger);margin-bottom:.35rem">' + (lang==='ar'?'القيم القديمة':'Old Values') + '</div>';
        html += '<div style="background:#fef2f2;border-radius:8px;padding:.75rem;font-size:.82rem">';
        for (var k in data.old) { html += '<div style="display:flex;justify-content:space-between;padding:.2rem 0;border-bottom:1px solid #fecaca"><span style="color:var(--gray-500)">' + k + '</span><span style="font-weight:600">' + (data.old[k] ?? '-') + '</span></div>'; }
        html += '</div></div>';
    }
    if (data.new && Object.keys(data.new).length) {
        html += '<div><div style="font-size:.82rem;font-weight:600;color:var(--success);margin-bottom:.35rem">' + (lang==='ar'?'القيم الجديدة':'New Values') + '</div>';
        html += '<div style="background:#f0fdf4;border-radius:8px;padding:.75rem;font-size:.82rem">';
        for (var k in data.new) { html += '<div style="display:flex;justify-content:space-between;padding:.2rem 0;border-bottom:1px solid #bbf7d0"><span style="color:var(--gray-500)">' + k + '</span><span style="font-weight:600">' + (data.new[k] ?? '-') + '</span></div>'; }
        html += '</div></div>';
    }
    if ((!data.old || !Object.keys(data.old).length) && (!data.new || !Object.keys(data.new).length)) {
        html += '<p class="text-muted text-center">' + (lang==='ar'?'لا توجد تفاصيل إضافية':'No additional details') + '</p>';
    }
    body.innerHTML = html;
    openModal('log-details-modal');
}