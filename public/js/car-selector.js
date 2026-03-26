/**
 * Car Selector — Searchable Make → Model → Color dropdowns
 * Usage: initCarSelector('makeId', 'modelId', 'colorId')
 */

var carMakesCache = null;
var carColorsCache = null;

function isElementVisible(el) {
    var node = el;
    while (node && node !== document.body) {
        if (node.style && node.style.display === 'none') return false;
        node = node.parentElement;
    }
    return true;
}

function initCarSelector(makeId, modelId, colorId) {
    var lang = document.documentElement.getAttribute('lang') || 'en';

    // Setup form validation for required selects
    var makeEl = document.getElementById(makeId);
    if (makeEl) {
        var form = makeEl.closest('form');
        if (form && !form._carSelectorValidation) {
            form._carSelectorValidation = true;
            form.addEventListener('submit', function(e) {
                var requiredSelects = form.querySelectorAll('select[data-ss-required]');
                for (var i = 0; i < requiredSelects.length; i++) {
                    var sel = requiredSelects[i];
                    // Skip if inside a hidden container
                    if (!isElementVisible(sel)) continue;

                    if (!sel.value) {
                        e.preventDefault();
                        var wrapper = sel.parentNode.querySelector('.ss-btn');
                        if (wrapper) {
                            wrapper.style.borderColor = 'var(--danger)';
                            wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    }
                }
            });
        }
    }

    // Load makes
    fetch('/api/car-data/makes')
        .then(function(r) { return r.json(); })
        .then(function(makes) {
            carMakesCache = makes;
            var makeEl = document.getElementById(makeId);
            if (!makeEl) return;

            var makeOptions = makes.map(function(m) {
                return {
                    value: m.name_en,
                    label: lang === 'ar' ? m.name_ar + ' — ' + m.name_en : m.name_en + ' — ' + m.name_ar,
                    search: m.name_en.toLowerCase() + ' ' + m.name_ar,
                    models: m.models || []
                };
            });

            var makePicker = createSearchSelect(makeEl, makeOptions, lang === 'ar' ? 'ابحث عن الشركة...' : 'Search make...');

            // On make change → load models
            makeEl.addEventListener('change', function() {
                var selected = makeOptions.find(function(o) { return o.value === makeEl.value; });
                var models = selected ? selected.models : [];
                var modelEl = document.getElementById(modelId);
                if (!modelEl) return;

                var modelOptions = models.map(function(m) {
                    return { value: m, label: m, search: m.toLowerCase() };
                });

                createSearchSelect(modelEl, modelOptions, lang === 'ar' ? 'ابحث عن الموديل...' : 'Search model...');
            });

            // Pre-select if data-value exists
            if (makeEl.dataset.value) {
                makePicker.setValue(makeEl.dataset.value);
                // Trigger model load
                makeEl.dispatchEvent(new Event('change'));
                // Pre-select model after short delay
                setTimeout(function() {
                    var modelEl = document.getElementById(modelId);
                    if (modelEl && modelEl.dataset.value && modelEl._picker) {
                        modelEl._picker.setValue(modelEl.dataset.value);
                    }
                }, 300);
            }
        });

    // Load colors
    if (colorId) {
        fetch('/api/car-data/colors')
            .then(function(r) { return r.json(); })
            .then(function(colors) {
                carColorsCache = colors;
                var colorEl = document.getElementById(colorId);
                if (!colorEl) return;

                var colorOptions = colors.map(function(c) {
                    var name = lang === 'ar' ? c.name_ar : c.name_en;
                    return {
                        value: name,
                        label: lang === 'ar' ? c.name_ar + ' ' + c.name_en : c.name_en + ' ' + c.name_ar,
                        search: c.name_en.toLowerCase() + ' ' + c.name_ar,
                        hex: c.hex
                    };
                });

                var colorPicker = createSearchSelect(colorEl, colorOptions, lang === 'ar' ? 'ابحث عن اللون...' : 'Search color...', true);

                if (colorEl.dataset.value) {
                    colorPicker.setValue(colorEl.dataset.value);
                }
            });
    }
}

/**
 * Create a searchable select dropdown
 */
function createSearchSelect(originalSelect, options, placeholder, showColor) {
    // Clean up existing picker
    if (originalSelect._pickerWrap) {
        originalSelect._pickerWrap.remove();
    }

    var lang = document.documentElement.getAttribute('lang') || 'en';
    var isRtl = lang === 'ar';
    var isDark = document.documentElement.classList.contains('dark');

    // Hide original select but keep it accessible for form submission
    var isRequired = originalSelect.hasAttribute('required') || originalSelect.hasAttribute('data-ss-required');
    originalSelect.removeAttribute('required');
    originalSelect.style.position = 'absolute';
    originalSelect.style.opacity = '0';
    originalSelect.style.height = '0';
    originalSelect.style.width = '0';
    originalSelect.style.overflow = 'hidden';
    originalSelect.style.pointerEvents = 'none';
    originalSelect.tabIndex = -1;

    // Create wrapper
    var wrap = document.createElement('div');
    wrap.className = 'ss-wrap';
    wrap.style.cssText = 'position:relative;width:100%';
    originalSelect.parentNode.insertBefore(wrap, originalSelect.nextSibling);
    originalSelect._pickerWrap = wrap;

    // Display button
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'form-control ss-btn';
    btn.style.cssText = 'text-align:' + (isRtl ? 'right' : 'left') + ';display:flex;align-items:center;justify-content:space-between;gap:8px;cursor:pointer;min-height:40px';
    var emptyLabel = placeholder.replace('ابحث عن ', '').replace('Search ', '').replace('...', '');
    btn.innerHTML = '<span class="ss-label" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--gray-400)">' + (isRtl ? 'اختر ' + emptyLabel : 'Select') + '</span><span style="font-size:.7rem;color:var(--gray-400)">▼</span>';
    wrap.appendChild(btn);

    // Dropdown
    var dropdown = document.createElement('div');
    dropdown.className = 'ss-dropdown';
    dropdown.style.cssText = 'display:none;position:absolute;top:100%;' + (isRtl ? 'right' : 'left') + ':0;width:100%;z-index:1000;background:var(--white);border:1px solid var(--gray-300);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.15);margin-top:4px;max-height:280px;overflow:hidden;flex-direction:column';
    wrap.appendChild(dropdown);

    // Search input
    var searchWrap = document.createElement('div');
    searchWrap.style.cssText = 'padding:8px;border-bottom:1px solid var(--gray-200)';
    var searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control';
    searchInput.placeholder = placeholder;
    searchInput.style.cssText = 'font-size:.85rem;padding:6px 10px;border-radius:6px';
    searchWrap.appendChild(searchInput);
    dropdown.appendChild(searchWrap);

    // Options list
    var list = document.createElement('div');
    list.className = 'ss-list';
    list.style.cssText = 'overflow-y:auto;max-height:220px;padding:4px';
    dropdown.appendChild(list);

    // Render options
    function renderOptions(filter) {
        list.innerHTML = '';
        var filtered = options.filter(function(o) {
            if (!filter) return true;
            return o.search.indexOf(filter.toLowerCase()) !== -1;
        });

        if (filtered.length === 0) {
            list.innerHTML = '<div style="padding:12px;text-align:center;color:var(--gray-400);font-size:.85rem">' + (isRtl ? 'لا توجد نتائج' : 'No results') + '</div>';
            return;
        }

        filtered.forEach(function(o) {
            var item = document.createElement('div');
            item.className = 'ss-item';
            item.style.cssText = 'padding:8px 12px;cursor:pointer;border-radius:6px;font-size:.85rem;display:flex;align-items:center;gap:8px;transition:background .15s';

            if (showColor && o.hex) {
                item.innerHTML = '<span style="width:16px;height:16px;border-radius:50%;border:1px solid var(--gray-300);flex-shrink:0;background:' + o.hex + '"></span>' + o.label;
            } else {
                item.textContent = o.label;
            }

            item.addEventListener('mouseenter', function() { this.style.background = 'var(--gray-100)'; });
            item.addEventListener('mouseleave', function() { this.style.background = ''; });

            item.addEventListener('click', function() {
                selectOption(o);
                closeDropdown();
            });

            list.appendChild(item);
        });
    }

    function selectOption(o) {
        originalSelect.innerHTML = '<option value="' + o.value + '" selected>' + o.label + '</option>';
        originalSelect.value = o.value;
        originalSelect.dispatchEvent(new Event('change'));
        btn.style.borderColor = '';

        var labelEl = btn.querySelector('.ss-label');
        if (showColor && o.hex) {
            labelEl.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px;color:var(--gray-800)"><span style="width:14px;height:14px;border-radius:50%;border:1px solid var(--gray-300);background:' + o.hex + '"></span>' + o.label + '</span>';
        } else {
            labelEl.textContent = o.label;
            labelEl.style.color = 'var(--gray-800)';
        }
    }

    function openDropdown() {
        dropdown.style.display = 'flex';
        searchInput.value = '';
        renderOptions('');
        searchInput.focus();
        document.addEventListener('click', outsideClick);
    }

    function closeDropdown() {
        dropdown.style.display = 'none';
        document.removeEventListener('click', outsideClick);
    }

    function outsideClick(e) {
        if (!wrap.contains(e.target)) closeDropdown();
    }

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        if (dropdown.style.display === 'flex') closeDropdown();
        else openDropdown();
    });

    searchInput.addEventListener('input', function() {
        renderOptions(this.value);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDropdown();
    });

    // Public API
    var picker = {
        setValue: function(val) {
            var found = options.find(function(o) { return o.value === val; });
            if (found) selectOption(found);
        }
    };
    originalSelect._picker = picker;

    // Mark as required for form validation
    if (isRequired) {
        btn.style.borderColor = 'var(--gray-300)';
        originalSelect.setAttribute('data-ss-required', '1');
    }

    // Dark mode support
    if (isDark) {
        dropdown.style.background = '#1e293b';
        dropdown.style.borderColor = '#334155';
    }

    return picker;
}