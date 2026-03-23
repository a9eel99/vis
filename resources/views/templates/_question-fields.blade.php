@php
    $lang = app()->getLocale();
    $qType = $q ? (is_object($q->type) ? $q->type->value : $q->type) : 'dropdown';
@endphp

<div class="form-grid-2">
    <div class="form-group">
        <label class="form-label">{{ $lang==='ar' ? 'عنوان السؤال' : 'Label' }} *</label>
        <input type="text" name="label" class="form-control" value="{{ $q?->label ?? '' }}" required placeholder="{{ $lang==='ar' ? 'مثال: حالة الباب الأمامي أيمن' : 'e.g., Front right door' }}">
    </div>
    <div class="form-group">
        <label class="form-label">{{ $lang==='ar' ? 'النوع' : 'Type' }} *</label>
        <select name="type" class="form-control q-type-sel" required onchange="toggleOptsArea(this)">
            @foreach([
                'dropdown' => $lang==='ar' ? '📋 قائمة خيارات' : '📋 Dropdown',
                'checkbox' => $lang==='ar' ? '✅ خانة اختيار (نعم/لا)' : '✅ Checkbox',
                'number'   => $lang==='ar' ? '🔢 رقم' : '🔢 Number',
                'text'     => $lang==='ar' ? '📝 نص حر' : '📝 Text',
                'photo'    => $lang==='ar' ? '📸 صورة' : '📸 Photo',
            ] as $t => $l)
                <option value="{{ $t }}" {{ $qType===$t ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
        <div class="type-hint" style="margin-top:6px;padding:8px 12px;border-radius:6px;font-size:.78rem;font-weight:600;transition:all .3s"></div>
    </div>
</div>

{{-- Scoring fields — hidden in descriptive mode --}}
<div class="form-grid-2 scoring-field">
    <div class="form-group">
        <label class="form-label">{{ $lang==='ar' ? 'الوزن' : 'Weight' }} <span class="text-muted" style="font-size:.7rem">({{ $lang==='ar' ? '0=لا يؤثر' : '0=no impact' }})</span></label>
        <input type="number" name="weight" class="form-control" value="{{ $q?->weight ?? 1 }}" step="0.01" min="0">
    </div>
    <div class="form-group">
        <label class="form-label">{{ $lang==='ar' ? 'أعلى درجة' : 'Max Score' }}</label>
        <input type="number" name="max_score" class="form-control q-ms" value="{{ $q?->max_score ?? 10 }}" step="0.01" min="0">
    </div>
</div>

<div style="display:flex;gap:1.5rem;margin-bottom:.75rem">
    <label class="scoring-field" style="display:flex;align-items:center;gap:.35rem;cursor:pointer">
        <input type="checkbox" name="is_critical" value="1" {{ ($q && $q->is_critical) ? 'checked' : '' }}>
        <span>{{ $lang==='ar' ? '⚠️ حرج' : '⚠️ Critical' }}</span>
    </label>
    <label style="display:flex;align-items:center;gap:.35rem;cursor:pointer">
        <input type="checkbox" name="is_required" value="1" {{ ($q && !$q->is_required) ? '' : 'checked' }}>
        <span>{{ $lang==='ar' ? 'مطلوب' : 'Required' }}</span>
    </label>
</div>

{{-- OPTIONS BUILDER --}}
<div class="opts-area" style="{{ $qType==='dropdown' ? '' : 'display:none' }}">
    <label class="form-label" style="font-weight:700">{{ $lang==='ar' ? '🎯 الخيارات' : '🎯 Options' }}</label>
    <p class="text-muted" style="font-size:.75rem;margin:0 0 .5rem">
        <span class="scoring-field">{{ $lang==='ar' ? 'أضف الخيارات بالترتيب. الدرجة الأعلى = أفضل حالة.' : 'Add options in order. Higher score = better.' }}</span>
        <span class="descriptive-field" style="display:none">{{ $lang==='ar' ? 'أضف الخيارات التي سيختار منها الفاحص.' : 'Add options for the inspector to choose from.' }}</span>
    </p>

    <div class="opts-list">
        @if($q && $q->options && is_array($q->options))
            @foreach($q->options as $opt)
                @php $oL = is_array($opt) ? ($opt['label'] ?? '') : (string)$opt; $oS = is_array($opt) ? ($opt['score'] ?? '') : ''; @endphp
                <div class="opt-row">
                    <input type="text" class="form-control opt-l" value="{{ $oL }}" placeholder="{{ $lang==='ar' ? 'اسم الخيار' : 'Label' }}">
                    <input type="number" class="form-control opt-s scoring-field" value="{{ $oS }}" placeholder="{{ $lang==='ar' ? 'درجة' : 'Score' }}" step="0.01">
                    <button type="button" class="btn btn-ghost btn-sm" style="color:var(--danger);padding:0 .3rem" onclick="this.closest('.opt-row').remove()">✕</button>
                </div>
            @endforeach
        @endif
    </div>

    <button type="button" class="btn btn-secondary btn-sm" onclick="addOpt(this.closest('.opts-area').querySelector('.opts-list'),'','')" style="margin-top:.4rem">+ {{ $lang==='ar' ? 'خيار' : 'Option' }}</button>

    <div class="scoring-field" style="margin-top:.4rem;border-top:1px dashed var(--gray-200);padding-top:.4rem">
        <span class="text-muted" style="font-size:.7rem">{{ $lang==='ar' ? 'تحميل سريع:' : 'Quick load:' }}</span>
        <button type="button" class="btn btn-ghost btn-sm" onclick="loadPR(this,'body')" style="font-size:.7rem">{{ $lang==='ar' ? 'بودي' : 'Body' }}</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="loadPR(this,'chassis')" style="font-size:.7rem">{{ $lang==='ar' ? 'شاصي' : 'Chassis' }}</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="loadPR(this,'engine')" style="font-size:.7rem">{{ $lang==='ar' ? 'محرك' : 'Engine' }}</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="loadPR(this,'yesno')" style="font-size:.7rem">{{ $lang==='ar' ? 'نعم/لا' : 'Yes/No' }}</button>
    </div>
</div>
<input type="hidden" name="options_json" class="opts-json">