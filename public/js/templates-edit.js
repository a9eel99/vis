function toggleEl(id){var e=document.getElementById(id);if(!e)return;e.style.display=e.style.display==='none'?'block':'none';if(e.style.display==='block'){var i=e.querySelector('input[type="text"]');if(i)i.focus()}}
function openAddQ(sid){toggleEl('add-q-'+sid);var a=document.getElementById('add-q-'+sid);if(a)toggleOptsArea(a.querySelector('.q-type-sel'))}

// ─── Scoring Mode Toggle ───

function toggleScoringUI(mode) {
    currentScoringMode = mode;
    var isDesc = mode === 'descriptive';

    // Show/hide all scoring fields
    document.querySelectorAll('.scoring-field').forEach(function(el) {
        el.style.display = isDesc ? 'none' : '';
    });

    // Show/hide descriptive-only fields
    document.querySelectorAll('.descriptive-field').forEach(function(el) {
        el.style.display = isDesc ? '' : 'none';
    });

    // Show/hide scoring info in question listing
    document.querySelectorAll('.q-scoring-info').forEach(function(el) {
        el.style.display = isDesc ? 'none' : '';
    });
}

// Type hint descriptions
var typeHintsData = {
    ar: {
        dropdown: {t:'📋 قائمة بخيارات محددة — كل خيار له درجة. أضف الخيارات بالأسفل ⬇️',bg:'#eff6ff',c:'#1e40af'},
        checkbox: {t:'✅ سؤال نعم/لا — يحصل على الدرجة الكاملة أو صفر',bg:'#ecfdf5',c:'#065f46'},
        number:   {t:'🔢 إدخال رقمي — الفاحص يدخل رقم من 0 إلى أعلى درجة',bg:'#fefce8',c:'#854d0e'},
        text:     {t:'📝 حقل نصي للملاحظات — لا يؤثر على الدرجة',bg:'#f8fafc',c:'#475569'},
        photo:    {t:'📸 رفع صور — للتوثيق فقط، لا يؤثر على الدرجة',bg:'#fdf4ff',c:'#86198f'}
    },
    ar_desc: {
        dropdown: {t:'📋 قائمة خيارات — الفاحص يختار وصف الحالة',bg:'#eff6ff',c:'#1e40af'},
        checkbox: {t:'✅ خانة اختيار — نعم أو لا',bg:'#ecfdf5',c:'#065f46'},
        number:   {t:'🔢 إدخال رقمي — مثل عدد الكيلومترات',bg:'#fefce8',c:'#854d0e'},
        text:     {t:'📝 حقل نصي حر للملاحظات',bg:'#f8fafc',c:'#475569'},
        photo:    {t:'📸 رفع صور للتوثيق',bg:'#fdf4ff',c:'#86198f'}
    },
    en: {
        dropdown: {t:'📋 Multiple choice list — each option has a score. Add options below ⬇️',bg:'#eff6ff',c:'#1e40af'},
        checkbox: {t:'✅ Yes/No toggle — gets full score or zero',bg:'#ecfdf5',c:'#065f46'},
        number:   {t:'🔢 Numeric input — inspector enters 0 to max score',bg:'#fefce8',c:'#854d0e'},
        text:     {t:'📝 Free text for notes — does not affect score',bg:'#f8fafc',c:'#475569'},
        photo:    {t:'📸 Photo upload — documentation only, no score impact',bg:'#fdf4ff',c:'#86198f'}
    },
    en_desc: {
        dropdown: {t:'📋 Dropdown list — inspector picks a description',bg:'#eff6ff',c:'#1e40af'},
        checkbox: {t:'✅ Checkbox — yes or no',bg:'#ecfdf5',c:'#065f46'},
        number:   {t:'🔢 Numeric input — e.g. mileage',bg:'#fefce8',c:'#854d0e'},
        text:     {t:'📝 Free text for notes',bg:'#f8fafc',c:'#475569'},
        photo:    {t:'📸 Photo upload for documentation',bg:'#fdf4ff',c:'#86198f'}
    }
};

function updateTypeHint(form, type) {
    var hint = form.querySelector('.type-hint');
    if (!hint) return;
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var isDesc = (typeof currentScoringMode !== 'undefined' && currentScoringMode === 'descriptive');
    var key = isDesc ? lang + '_desc' : lang;
    var hints = typeHintsData[key] || typeHintsData[lang] || typeHintsData['en'];
    var h = hints[type];
    if (h) {
        hint.textContent = h.t;
        hint.style.background = h.bg;
        hint.style.color = h.c;
        hint.style.display = 'block';
    } else {
        hint.style.display = 'none';
    }
}

function toggleOptsArea(sel){
    if(!sel)return;
    var form=sel.closest('form'),area=form.querySelector('.opts-area');
    if(!area)return;
    area.style.display=sel.value==='dropdown'?'block':'none';
    var ms=form.querySelector('.q-ms');
    if(ms) {
        if(['text','photo'].includes(sel.value))ms.value=0;
        else if(ms.value==='0'&&['dropdown','number','checkbox'].includes(sel.value))ms.value=10;
    }
    updateTypeHint(form, sel.value);
}

function addOpt(list,label,score){
    var r=document.createElement('div');r.className='opt-row';
    var lang=document.documentElement.getAttribute('lang')||'en';
    var isDesc = (typeof currentScoringMode !== 'undefined' && currentScoringMode === 'descriptive');
    var scoreField = isDesc ? '' : '<input type="number" class="form-control opt-s scoring-field" value="'+score+'" placeholder="'+(lang==='ar'?'درجة':'Score')+'" step="0.01">';
    r.innerHTML='<input type="text" class="form-control opt-l" value="'+label+'" placeholder="'+(lang==='ar'?'اسم الخيار':'Label')+'">' + scoreField + '<button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger);padding:0 .3rem" onclick="this.closest(\'.opt-row\').remove()">✕</button>';
    list.appendChild(r);r.querySelector('.opt-l').focus();
}

var PR=(function(){
    var l=document.documentElement.getAttribute('lang')||'en';
    var a=l==='ar';
    return {
        body:[{l:a?'جيد / أصلي':'Good / Original',s:10},{l:a?'ضربة ومشغولة ممتاز':'Hit & repaired excellent',s:8},{l:a?'ضربة ومشغولة جيد':'Hit & repaired good',s:6},{l:a?'ضربة ومشغولة سيء':'Hit & repaired poor',s:3},{l:a?'ضربة غير مشغولة':'Hit, not repaired',s:1},{l:a?'غير موجود / تالف':'Missing / Damaged',s:0}],
        chassis:[{l:a?'جيد / أصلي':'Good / Original',s:10},{l:a?'ضربة ومشغولة ممتاز':'Hit & repaired excellent',s:8},{l:a?'ضربة ومشغولة جيد':'Hit & repaired good',s:6},{l:a?'ضربة ومشغولة سيء':'Hit & repaired poor',s:3},{l:a?'مقصوص وموصول':'Cut & welded',s:1},{l:a?'مبدل':'Replaced',s:0}],
        engine:[{l:a?'ممتاز':'Excellent',s:10},{l:a?'جيد':'Good',s:7},{l:a?'مقبول':'Fair',s:4},{l:a?'ضعيف':'Poor',s:1}],
        yesno:[{l:a?'نعم / يعمل':'Yes / Working',s:10},{l:a?'لا / لا يعمل':'No / Not working',s:0}],
    };
})();

function loadPR(btn,key){
    var lang=document.documentElement.getAttribute('lang')||'en';
    var list=btn.closest('.opts-area').querySelector('.opts-list');
    if(!confirm(lang==='ar'?'استبدال الخيارات الحالية؟':'Replace current options?'))return;
    list.innerHTML='';PR[key].forEach(function(o){addOpt(list,o.l,o.s)});
    var ms=btn.closest('form').querySelector('.q-ms');
    if(ms)ms.value=Math.max.apply(null,PR[key].map(function(o){return o.s}));
}

function prepareOpts(form){
    var sel=form.querySelector('.q-type-sel'),json=form.querySelector('.opts-json');
    if(sel&&sel.value==='dropdown'){
        var rows=form.querySelectorAll('.opt-row'),opts=[];
        rows.forEach(function(r){
            var l=r.querySelector('.opt-l').value.trim();
            var sEl=r.querySelector('.opt-s');
            var s=sEl ? (parseFloat(sEl.value)||0) : 0;
            if(l)opts.push({label:l,score:s});
        });
        json.value=JSON.stringify(opts);
    }else if(json){json.value='';}
    return true;
}

// ─── Init on page load ───
document.addEventListener('DOMContentLoaded', function() {
    // Init type hints
    document.querySelectorAll('.q-type-sel').forEach(function(sel) {
        var form = sel.closest('form');
        if (form) updateTypeHint(form, sel.value);
    });

    // Apply scoring mode on load
    if (typeof currentScoringMode !== 'undefined') {
        toggleScoringUI(currentScoringMode);
    }
});