@php $lang = app()->getLocale(); @endphp

{{-- Make --}}
<div class="form-group">
    <label class="form-label">{{ $lang === 'ar' ? 'الشركة المصنعة' : 'Make' }} *</label>
    <select name="make" id="{{ $prefix ?? '' }}car-make" class="form-control" required
        data-value="{{ old('make', $vehicle->make ?? '') }}">
        <option value="">{{ $lang === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
    </select>
</div>

{{-- Model --}}
<div class="form-group">
    <label class="form-label">{{ $lang === 'ar' ? 'الموديل' : 'Model' }} *</label>
    <select name="model" id="{{ $prefix ?? '' }}car-model" class="form-control" required
        data-value="{{ old('model', $vehicle->model ?? '') }}">
        <option value="">{{ $lang === 'ar' ? 'اختر الشركة أولاً' : 'Select make first' }}</option>
    </select>
</div>

{{-- Color --}}
<div class="form-group">
    <label class="form-label">{{ $lang === 'ar' ? 'اللون' : 'Color' }}</label>
    <select name="color" id="{{ $prefix ?? '' }}car-color" class="form-control"
        data-value="{{ old('color', $vehicle->color ?? '') }}">
        <option value="">{{ $lang === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
    </select>
</div>