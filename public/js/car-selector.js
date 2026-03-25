/**
 * Car Selector — Dynamic Make → Model → Color dropdowns
 * Usage: initCarSelector(makeSelectId, modelSelectId, colorSelectId)
 */

var carMakesCache = null;

function initCarSelector(makeId, modelId, colorId) {
    var makeSelect = document.getElementById(makeId);
    var modelSelect = document.getElementById(modelId);
    var colorSelect = document.getElementById(colorId);
    var lang = document.documentElement.getAttribute('lang') || 'en';

    if (!makeSelect) return;

    // Load makes
    if (carMakesCache) {
        populateMakes(makeSelect, carMakesCache, lang);
        if (makeSelect.dataset.value) makeSelect.value = makeSelect.dataset.value;
        if (makeSelect.value) loadModels(makeSelect, modelSelect, lang);
    } else {
        fetch('/api/car-data/makes')
            .then(function(r) { return r.json(); })
            .then(function(makes) {
                carMakesCache = makes;
                populateMakes(makeSelect, makes, lang);
                if (makeSelect.dataset.value) {
                    makeSelect.value = makeSelect.dataset.value;
                    loadModels(makeSelect, modelSelect, lang);
                }
            });
    }

    // Load colors
    if (colorSelect) {
        fetch('/api/car-data/colors')
            .then(function(r) { return r.json(); })
            .then(function(colors) {
                populateColors(colorSelect, colors, lang);
                if (colorSelect.dataset.value) colorSelect.value = colorSelect.dataset.value;
            });
    }

    // Make change → load models
    makeSelect.addEventListener('change', function() {
        loadModels(makeSelect, modelSelect, lang);
    });
}

function populateMakes(select, makes, lang) {
    var current = select.dataset.value || '';
    var placeholder = lang === 'ar' ? 'اختر الشركة المصنعة' : 'Select Make';
    select.innerHTML = '<option value="">' + placeholder + '</option>';
    makes.forEach(function(m) {
        var label = lang === 'ar' ? m.name_ar + ' — ' + m.name_en : m.name_en + ' — ' + m.name_ar;
        var opt = document.createElement('option');
        opt.value = m.name_en;
        opt.textContent = label;
        opt.dataset.id = m.id;
        opt.dataset.models = JSON.stringify(m.models || []);
        if (m.name_en === current) opt.selected = true;
        select.appendChild(opt);
    });
}

function loadModels(makeSelect, modelSelect, lang) {
    if (!modelSelect) return;
    var selected = makeSelect.options[makeSelect.selectedIndex];
    var models = [];
    try { models = JSON.parse(selected.dataset.models || '[]'); } catch(e) {}

    var current = modelSelect.dataset.value || '';
    var placeholder = lang === 'ar' ? 'اختر الموديل' : 'Select Model';
    modelSelect.innerHTML = '<option value="">' + placeholder + '</option>';
    models.forEach(function(m) {
        var opt = document.createElement('option');
        opt.value = m;
        opt.textContent = m;
        if (m === current) opt.selected = true;
        modelSelect.appendChild(opt);
    });
}

function populateColors(select, colors, lang) {
    var current = select.dataset.value || '';
    var placeholder = lang === 'ar' ? 'اختر اللون' : 'Select Color';
    select.innerHTML = '<option value="">' + placeholder + '</option>';
    colors.forEach(function(c) {
        var label = lang === 'ar' ? c.name_ar : c.name_en;
        var opt = document.createElement('option');
        opt.value = lang === 'ar' ? c.name_ar : c.name_en;
        opt.textContent = label;
        opt.style.cssText = 'padding-right:24px';
        if ((lang === 'ar' ? c.name_ar : c.name_en) === current) opt.selected = true;
        select.appendChild(opt);
    });
}