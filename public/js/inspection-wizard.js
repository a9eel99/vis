var currentStep = 0;
var totalSteps = parseInt(document.getElementById('inspection-form').dataset.totalSteps) || 1;

// Store files per question (allows add/remove)
var questionFiles = {};

function goToStep(step) {
    if (step < 0 || step >= totalSteps) return;
    document.querySelectorAll('.wizard-panel').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.wizard-step').forEach(function(s) { s.classList.remove('active'); s.classList.remove('completed'); });
    document.getElementById('panel-' + step).classList.add('active');
    for (var i = 0; i < totalSteps; i++) {
        var s = document.querySelectorAll('.wizard-step')[i];
        if (i < step) s.classList.add('completed');
        if (i === step) s.classList.add('active');
    }
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectOption(btn) {
    var qId = btn.dataset.question;
    btn.parentElement.querySelectorAll('.wq-option').forEach(function(b) { b.classList.remove('selected'); });
    btn.classList.add('selected');
    document.getElementById('dd-' + qId).value = btn.dataset.label;
    var scoreInput = document.getElementById('score-' + qId);
    if (scoreInput) scoreInput.value = btn.dataset.score;
    var badge = document.getElementById('qscore-' + qId);
    if (badge) {
        badge.textContent = btn.dataset.score + '/' + parseInt(btn.dataset.max);
        badge.className = 'wq-score-badge ' + (btn.dataset.score >= btn.dataset.max * 0.75 ? 'good' : btn.dataset.score >= btn.dataset.max * 0.5 ? 'ok' : 'bad');
    }
    updateLiveScore();
}

function toggleCheck(btn, qId, maxScore) {
    var hidden = document.getElementById('hidden-' + qId);
    var isActive = btn.classList.toggle('active');
    hidden.value = isActive ? '1' : '0';
    btn.querySelector('.toggle-icon').textContent = isActive ? '✅' : '☐';
    var scoreInput = document.getElementById('score-' + qId);
    if (scoreInput) scoreInput.value = isActive ? maxScore : 0;
    var badge = document.getElementById('qscore-' + qId);
    if (badge) {
        badge.textContent = (isActive ? maxScore : 0) + '/' + maxScore;
        badge.className = 'wq-score-badge ' + (isActive ? 'good' : '');
    }
    updateLiveScore();
}

function stepNum(btn, dir) {
    var input = btn.parentElement.querySelector('input');
    var val = Math.max(0, (parseFloat(input.value) || 0) + dir);
    input.value = val;
    updateNumScore(input);
}

function updateNumScore(input) {
    var val = parseFloat(input.value) || 0;
    if (val < 0) { val = 0; input.value = 0; }

    var max = parseFloat(input.dataset.max) || 10;
    var qId = input.dataset.question;

    // Score = min(value, max_score) — the input itself is never capped
    var scoreVal = Math.min(val, max);

    var scoringMode = document.getElementById('inspection-form')?.dataset.scoringMode || 'scored';
    if (scoringMode === 'descriptive') scoreVal = 0;

    var scoreInput = document.getElementById('score-' + qId);
    if (scoreInput) scoreInput.value = scoreVal;

    var badge = document.getElementById('qscore-' + qId);
    if (badge) {
        badge.textContent = Math.round(scoreVal) + '/' + Math.round(max);
        badge.className = 'wq-score-badge ' + (scoreVal >= max * 0.75 ? 'good' : scoreVal >= max * 0.5 ? 'ok' : 'bad');
    }
    updateLiveScore();
}

// ─── Photo Management (add / remove / preview + COMPRESSION) ───

// Compress image — uses createObjectURL (less memory than readAsDataURL)
function compressImage(file, maxWidth, quality) {
    return new Promise(function(resolve) {
        if (!file.type.startsWith('image/')) { resolve(file); return; }

        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function() {
            var w = img.width, h = img.height;
            if (w > maxWidth) { h = Math.round(h * maxWidth / w); w = maxWidth; }

            var canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            canvas.toBlob(function(blob) {
                // Free memory immediately
                URL.revokeObjectURL(url);
                canvas.width = 0; canvas.height = 0;

                if (blob && blob.size < file.size) {
                    resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                } else {
                    resolve(file);
                }
            }, 'image/jpeg', quality);
        };
        img.onerror = function() {
            URL.revokeObjectURL(url);
            resolve(file);
        };
        img.src = url;
    });
}

// Process files ONE BY ONE (sequential) — prevents browser freeze
function addFiles(input, qId) {
    if (!input.files.length) return;
    if (!questionFiles[qId]) questionFiles[qId] = [];

    var filesToProcess = Array.from(input.files);
    var total = filesToProcess.length;
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var list = document.getElementById('files-' + qId);

    // Show progress
    var loader = document.createElement('div');
    loader.id = 'compress-loader-' + qId;
    loader.style.cssText = 'text-align:center;padding:8px;color:#64748b;font-size:.82rem';
    loader.textContent = lang === 'ar' ? '⏳ جاري ضغط الصور... 0/' + total : '⏳ Compressing... 0/' + total;
    list.appendChild(loader);

    // Determine max width: smaller on mobile for speed
    var maxW = window.innerWidth <= 768 ? 1200 : 1600;

    // Sequential processing chain
    var chain = Promise.resolve();
    filesToProcess.forEach(function(f, i) {
        chain = chain.then(function() {
            // Update progress
            loader.textContent = (lang === 'ar' ? '⏳ جاري ضغط الصور... ' : '⏳ Compressing... ') + (i + 1) + '/' + total;

            return compressImage(f, maxW, 0.7).then(function(compressed) {
                questionFiles[qId].push(compressed);
            });
        });
    });

    chain.then(function() {
        syncFiles(qId);
    });

    // Clear input so same file can be re-selected
    input.value = '';
}

function removeFile(qId, index) {
    if (!questionFiles[qId]) return;

    // Revoke object URL to free memory
    var item = document.querySelector('#files-' + qId + ' [data-index="' + index + '"] img');
    if (item) URL.revokeObjectURL(item.src);

    // Remove from array
    questionFiles[qId].splice(index, 1);

    // Sync
    syncFiles(qId);
}

function syncFiles(qId) {
    var files = questionFiles[qId] || [];
    var finput = document.getElementById('finput-' + qId);

    // Use DataTransfer to set files on the real input
    var dt = new DataTransfer();
    files.forEach(function(f) { dt.items.add(f); });
    finput.files = dt.files;

    // Render preview
    renderPreview(qId);
}

function renderPreview(qId) {
    var list = document.getElementById('files-' + qId);
    var files = questionFiles[qId] || [];

    // Revoke old object URLs to free memory
    list.querySelectorAll('img').forEach(function(img) {
        if (img.src.startsWith('blob:')) URL.revokeObjectURL(img.src);
    });

    if (!files.length) {
        list.innerHTML = '';
        return;
    }

    var html = '';
    files.forEach(function(f, i) {
        var url = URL.createObjectURL(f);
        var sizeMB = (f.size / 1024 / 1024).toFixed(1);
        html += '<div class="upload-file-item" data-index="' + i + '" style="position:relative;display:inline-block">'
              + '<img src="' + url + '" class="upload-thumb" loading="lazy">'
              + '<button type="button" onclick="removeFile(\'' + qId + '\',' + i + ')" '
              + 'style="position:absolute;top:-6px;right:-6px;width:22px;height:22px;border-radius:50%;'
              + 'background:#ef4444;color:#fff;border:2px solid #fff;font-size:12px;cursor:pointer;'
              + 'display:flex;align-items:center;justify-content:center;padding:0;'
              + 'box-shadow:0 1px 4px rgba(0,0,0,.3);line-height:1" title="حذف">✕</button>'
              + '<span class="upload-fname">' + sizeMB + 'MB</span>'
              + '</div>';
    });

    list.innerHTML = html;
}

// Keep old function as fallback
function previewFiles(input, qId) {
    addFiles(input, qId);
}

// ─── Live Score ───

function updateLiveScore() {
    var totalWeighted = 0, maxWeighted = 0;
    document.querySelectorAll('.score-input').forEach(function(input) {
        var score = parseFloat(input.value) || 0;
        var weight = parseFloat(input.dataset.weight) || 1;
        var maxScore = parseFloat(input.dataset.maxScore) || 10;
        if (maxScore > 0) { totalWeighted += score * weight; maxWeighted += maxScore * weight; }
    });
    var pct = maxWeighted > 0 ? Math.round((totalWeighted / maxWeighted) * 100) : 0;
    document.getElementById('score-percentage').textContent = pct + '%';
    var circle = document.getElementById('score-circle');
    if (circle) {
        circle.style.strokeDashoffset = 220 - (220 * pct / 100);
        circle.style.stroke = pct >= 75 ? '#10b981' : pct >= 50 ? '#f59e0b' : '#ef4444';
    }
    var gradeEl = document.getElementById('live-grade');
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var grades = {ar:{e:'ممتاز',g:'جيد',n:'يحتاج اهتمام',c:'حرج'},en:{e:'Excellent',g:'Good',n:'Needs Attention',c:'Critical'}};
    var g = grades[lang] || grades.en;
    if (pct >= 90) { gradeEl.textContent = g.e; gradeEl.className = 'wizard-grade excellent'; }
    else if (pct >= 75) { gradeEl.textContent = g.g; gradeEl.className = 'wizard-grade good'; }
    else if (pct >= 50) { gradeEl.textContent = g.n; gradeEl.className = 'wizard-grade warning'; }
    else { gradeEl.textContent = g.c; gradeEl.className = 'wizard-grade critical'; }
}

updateLiveScore();