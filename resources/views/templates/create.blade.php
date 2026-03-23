@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'إنشاء قالب جديد' : 'Create Template')

@php $lang = app()->getLocale(); @endphp

@section('content')
<div class="page-header">
    <h1>{{ $lang==='ar' ? 'إنشاء قالب فحص جديد' : 'Create New Template' }}</h1>
    <div class="header-actions">
        <a href="{{ route('templates.index') }}" class="btn btn-secondary">{{ $lang==='ar' ? 'رجوع' : 'Back' }}</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('templates.store') }}" method="POST">
            @csrf
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? 'اسم القالب' : 'Template Name' }} <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ $lang==='ar' ? 'مثال: فحص شامل للمركبة' : 'e.g., Standard Vehicle Inspection' }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $lang==='ar' ? 'نوع الوقود' : 'Fuel Type' }}</label>
                    <select name="fuel_type" class="form-control">
                        <option value="">{{ $lang==='ar' ? 'عام (جميع الأنواع)' : 'General (All Types)' }}</option>
                        @foreach(['gasoline'=>$lang==='ar'?'بنزين':'Gasoline','diesel'=>$lang==='ar'?'ديزل':'Diesel','electric'=>$lang==='ar'?'كهربائي':'Electric','hybrid'=>$lang==='ar'?'هجين':'Hybrid'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('fuel_type')===$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ $lang==='ar' ? 'الوصف' : 'Description' }}</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="{{ $lang==='ar' ? 'وصف الغرض من هذا القالب...' : 'Describe the purpose of this template...' }}">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>{{ $lang==='ar' ? 'نشط (متاح للفحوصات الجديدة)' : 'Active (available for new inspections)' }}</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ $lang==='ar' ? 'إنشاء القالب' : 'Create Template' }}</button>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">{{ $lang==='ar' ? 'إلغاء' : 'Cancel' }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
