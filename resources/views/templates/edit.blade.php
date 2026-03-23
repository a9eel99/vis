@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'تعديل القالب: ' . $template->name : 'Edit Template: ' . $template->name)
@php $lang = app()->getLocale(); @endphp

@section('content')
<div class="page-header">
    <h1>{{ $lang==='ar' ? 'تعديل القالب' : 'Edit Template' }}</h1>
    <div class="header-actions">
        <a href="{{ route('templates.show', $template) }}" class="btn btn-secondary">{{ $lang==='ar' ? 'معاينة' : 'Preview' }}</a>
        <a href="{{ route('templates.index') }}" class="btn btn-secondary">{{ $lang==='ar' ? 'رجوع' : 'Back' }}</a>
    </div>
</div>

{{-- Template Info --}}
<div class="card mb-2">
    <div class="card-header"><h3>{{ $lang==='ar' ? 'بيانات القالب' : 'Template Details' }}</h3></div>
    <div class="card-body">
        <form action="{{ route('templates.update', $template) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? 'اسم القالب' : 'Template Name' }} *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? 'نوع الوقود' : 'Fuel Type' }}</label>
                    <select name="fuel_type" class="form-control">
                        <option value="">{{ $lang==='ar' ? 'عام (جميع الأنواع)' : 'General (All)' }}</option>
                        @foreach(['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid','lpg'=>$lang==='ar'?'غاز':'LPG'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ $template->fuel_type===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ $lang==='ar' ? 'الوصف' : 'Description' }}</label>
                <textarea name="description" rows="2" class="form-control">{{ old('description', $template->description) }}</textarea>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? 'نمط الفحص' : 'Inspection Mode' }} *</label>
                    <select name="scoring_mode" class="form-control" id="scoring-mode-select" onchange="toggleScoringUI(this.value)">
                        <option value="scored" {{ ($template->scoring_mode ?? 'scored') === 'scored' ? 'selected' : '' }}>
                            {{ $lang==='ar' ? '📊 تقييم بالعلامات — درجات ونسبة ونتيجة' : '📊 Scored — grades, percentages & results' }}
                        </option>
                        <option value="descriptive" {{ $template->scoring_mode === 'descriptive' ? 'selected' : '' }}>
                            {{ $lang==='ar' ? '📝 فحص وصفي — ملاحظات وصور فقط' : '📝 Descriptive — observations & photos only' }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? '💰 سعر الفحص (د.أ)' : '💰 Price (JOD)' }}</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $template->price ?? 0) }}" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
            <div style="display:flex;gap:1rem;align-items:center;margin-bottom:.75rem">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $template->is_active?'checked':'' }}>
                    <span>{{ $lang==='ar' ? 'نشط' : 'Active' }}</span>
                </label>
            </div>
            <button type="submit" class="btn btn-primary">{{ $lang==='ar' ? 'تحديث القالب' : 'Update' }}</button>
        </form>
    </div>
</div>

{{-- Sections & Questions --}}
<div class="card mb-2">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
        <h3>{{ $lang==='ar' ? 'الأقسام والأسئلة' : 'Sections & Questions' }}</h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleEl('add-section-form')">+ {{ $lang==='ar' ? 'قسم' : 'Section' }}</button>
    </div>
    <div class="card-body">

        {{-- Add Section --}}
        <div id="add-section-form" style="display:none;margin-bottom:1.5rem">
            <form action="{{ route('templates.sections.store', $template) }}" method="POST" class="card" style="background:var(--gray-50);border:2px dashed var(--primary)">
                <div class="card-body">@csrf
                    <h4 style="margin-bottom:.75rem">📁 {{ $lang==='ar' ? 'قسم جديد' : 'New Section' }}</h4>
                    <div class="form-group"><label class="form-label">{{ $lang==='ar' ? 'اسم القسم' : 'Name' }} *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">{{ $lang==='ar' ? 'الوصف' : 'Description' }}</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div style="display:flex;gap:.5rem"><button type="submit" class="btn btn-success btn-sm">{{ $lang==='ar' ? 'حفظ' : 'Save' }}</button><button type="button" class="btn btn-secondary btn-sm" onclick="toggleEl('add-section-form')">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</button></div>
                </div>
            </form>
        </div>

        @forelse($template->sections as $section)
        <div class="tmpl-section" id="section-{{ $section->id }}">
            <div class="tmpl-section-header">
                <div>
                    <h3 style="margin:0;display:flex;align-items:center;gap:.5rem"><span style="opacity:.4;font-size:.8rem">{{ $loop->iteration }}</span>{{ $section->name }}<span class="badge badge-secondary" style="font-size:.65rem">{{ $section->questions->count() }} {{ $lang==='ar'?'سؤال':'Q' }}</span></h3>
                    @if($section->description)<p class="text-muted" style="font-size:.8rem;margin:.2rem 0 0">{{ $section->description }}</p>@endif
                </div>
                <div style="display:flex;gap:.35rem">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEl('edit-sec-{{ $section->id }}')">{{ $lang==='ar'?'تعديل':'Edit' }}</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddQ('{{ $section->id }}')">+ {{ $lang==='ar'?'سؤال':'Q' }}</button>
                    <form action="{{ route('templates.sections.destroy', $section->id) }}" method="POST" style="display:inline" onsubmit="return confirm('{{ $lang==='ar'?'حذف القسم وأسئلته؟':'Delete section?' }}')">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger)">🗑️</button></form>
                </div>
            </div>

            {{-- Edit Section --}}
            <div id="edit-sec-{{ $section->id }}" style="display:none;padding:.75rem 1rem;background:var(--gray-50);border-bottom:1px solid var(--gray-200)">
                <form action="{{ route('templates.sections.update', $section->id) }}" method="POST">@csrf @method('PUT')
                    <div class="form-grid-2">
                        <div class="form-group"><label class="form-label">{{ $lang==='ar'?'الاسم':'Name' }} *</label><input type="text" name="name" class="form-control" value="{{ $section->name }}" required></div>
                        <div class="form-group"><label class="form-label">{{ $lang==='ar'?'الوصف':'Desc' }}</label><textarea name="description" class="form-control" rows="1">{{ $section->description }}</textarea></div>
                    </div>
                    <div style="display:flex;gap:.5rem"><button type="submit" class="btn btn-success btn-sm">{{ $lang==='ar'?'تحديث':'Update' }}</button><button type="button" class="btn btn-secondary btn-sm" onclick="toggleEl('edit-sec-{{ $section->id }}')">{{ $lang==='ar'?'إلغاء':'Cancel' }}</button></div>
                </form>
            </div>

            {{-- Add Question --}}
            <div id="add-q-{{ $section->id }}" style="display:none;padding:1rem;background:#fffbeb;border-bottom:1px solid var(--gray-200)">
                <form action="{{ route('templates.questions.store', $section->id) }}" method="POST" onsubmit="return prepareOpts(this)">@csrf
                    <h4 style="margin-bottom:.75rem">➕ {{ $lang==='ar'?'سؤال جديد':'New Question' }}</h4>
                    @include('templates._question-fields', ['q' => null])
                    <div style="display:flex;gap:.5rem;margin-top:1rem"><button type="submit" class="btn btn-success btn-sm">{{ $lang==='ar'?'إضافة':'Add' }}</button><button type="button" class="btn btn-secondary btn-sm" onclick="toggleEl('add-q-{{ $section->id }}')">{{ $lang==='ar'?'إلغاء':'Cancel' }}</button></div>
                </form>
            </div>

            {{-- Questions --}}
            @forelse($section->questions as $question)
            <div class="tmpl-question">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div style="flex:1">
                        <div style="font-weight:650;font-size:.9rem;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                            <span class="text-muted" style="font-size:.75rem">{{ $loop->iteration }}.</span>{{ $question->label }}
                            @if($question->is_critical)<span class="q-scoring-info" style="background:#fef2f2;color:#dc2626;font-size:.65rem;padding:.1rem .4rem;border-radius:4px;font-weight:700">{{ $lang==='ar'?'حرج':'Critical' }}</span>@endif
                        </div>
                        <div style="display:flex;gap:.75rem;font-size:.72rem;color:var(--gray-400);margin-top:.15rem;flex-wrap:wrap">
                            <span style="background:var(--gray-100);padding:.1rem .4rem;border-radius:4px">{{ ucfirst(is_object($question->type)?$question->type->value:$question->type) }}</span>
                            <span class="q-scoring-info">{{ $lang==='ar'?'وزن':'W' }}: <strong>{{ $question->weight }}</strong></span>
                            <span class="q-scoring-info">{{ $lang==='ar'?'أعلى':'Max' }}: <strong>{{ $question->max_score }}</strong></span>
                        </div>
                        @if($question->options && is_array($question->options) && count($question->options))
                        <div style="margin-top:.35rem;display:flex;gap:.2rem;flex-wrap:wrap">
                            @foreach($question->options as $opt)
                                @php $oL=is_array($opt)?($opt['label']??''):(string)$opt; $oS=is_array($opt)?($opt['score']??null):null; @endphp
                                <span style="font-size:.65rem;padding:.1rem .35rem;border-radius:4px;background:#eff6ff;color:var(--primary)">{{ $oL }}@if($oS!==null)<strong class="q-scoring-info"> ({{ $oS }})</strong>@endif</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div style="display:flex;gap:.25rem;flex-shrink:0">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEl('edit-q-{{ $question->id }}')">{{ $lang==='ar'?'تعديل':'Edit' }}</button>
                        <form action="{{ route('templates.questions.destroy', $question->id) }}" method="POST" style="display:inline" onsubmit="return confirm('{{ $lang==='ar'?'حذف؟':'Delete?' }}')">@csrf @method('DELETE')<button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger)">🗑️</button></form>
                    </div>
                </div>

                {{-- Edit Question --}}
                <div id="edit-q-{{ $question->id }}" style="display:none;margin-top:.75rem;padding:.75rem;background:var(--gray-50);border-radius:var(--radius-sm)">
                    <form action="{{ route('templates.questions.update', $question->id) }}" method="POST" onsubmit="return prepareOpts(this)">@csrf @method('PUT')
                        @include('templates._question-fields', ['q' => $question])
                        <div style="display:flex;gap:.5rem;margin-top:.75rem"><button type="submit" class="btn btn-success btn-sm">{{ $lang==='ar'?'تحديث':'Update' }}</button><button type="button" class="btn btn-secondary btn-sm" onclick="toggleEl('edit-q-{{ $question->id }}')">{{ $lang==='ar'?'إلغاء':'Cancel' }}</button></div>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:1.25rem;text-align:center;color:var(--gray-400);font-size:.85rem">{{ $lang==='ar'?'لا توجد أسئلة. اضغط "+ سؤال"':'No questions. Click "+ Q"' }}</div>
            @endforelse
        </div>
        @empty
        <div style="padding:2rem;text-align:center"><div style="font-size:2.5rem">📋</div><h3>{{ $lang==='ar'?'لا توجد أقسام':'No Sections' }}</h3><p class="text-muted">{{ $lang==='ar'?'اضغط "+ قسم" لبناء القالب':'Click "+ Section" to start' }}</p></div>
        @endforelse
    </div>
</div>
@endsection

@section('modals')
<script>
    var currentScoringMode = '{{ $template->scoring_mode ?? "scored" }}';
</script>
<script src="{{ asset('js/templates-edit.js') }}"></script>
@endsection