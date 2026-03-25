@extends('layouts.app')
@section('title', __('edit_vehicle'))

@section('content')
<div class="page-header">
    <h1>{{ __('edit_vehicle') }}: {{ $vehicle->full_name }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('make') }} <span class="required">*</span></label>
                    <select name="make" id="v-car-make" class="form-control @error('make') is-invalid @enderror" required
                        data-value="{{ old('make', $vehicle->make) }}">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
                    </select>
                    @error('make') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('model') }} <span class="required">*</span></label>
                    <select name="model" id="v-car-model" class="form-control @error('model') is-invalid @enderror" required
                        data-value="{{ old('model', $vehicle->model) }}">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
                    </select>
                    @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('year') }} <span class="required">*</span></label>
                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                    @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('vin') }}</label>
                    <input type="text" name="vin" class="form-control font-mono @error('vin') is-invalid @enderror" value="{{ old('vin', $vehicle->vin) }}" maxlength="17">
                    @error('vin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('plate_number') }}</label>
                    <input type="text" name="license_plate" id="v-plate" class="form-control" value="{{ old('license_plate', $vehicle->license_plate) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('color') }}</label>
                    <select name="color" id="v-car-color" class="form-control"
                        data-value="{{ old('color', $vehicle->color) }}">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('mileage') }}</label>
                    <input type="number" name="mileage" class="form-control" value="{{ old('mileage', $vehicle->mileage) }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نوع الوقود' : 'Fuel Type' }}</label>
                    <select name="fuel_type" class="form-control">
                        <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر' : 'Select' }} --</option>
                        @foreach(['gasoline' => app()->getLocale() === 'ar' ? 'بنزين' : 'Gasoline', 'diesel' => app()->getLocale() === 'ar' ? 'ديزل' : 'Diesel', 'electric' => app()->getLocale() === 'ar' ? 'كهربائي' : 'Electric', 'hybrid' => app()->getLocale() === 'ar' ? 'هجين' : 'Hybrid', 'lpg' => app()->getLocale() === 'ar' ? 'غاز' : 'LPG'] as $val => $label)
                            <option value="{{ $val }}" {{ old('fuel_type', $vehicle->fuel_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'ناقل الحركة' : 'Transmission' }}</label>
                    <select name="transmission" class="form-control">
                        <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر' : 'Select' }} --</option>
                        @foreach(['automatic' => app()->getLocale() === 'ar' ? 'أوتوماتيك' : 'Automatic', 'manual' => app()->getLocale() === 'ar' ? 'يدوي' : 'Manual'] as $val => $label)
                            <option value="{{ $val }}" {{ old('transmission', $vehicle->transmission) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h4 style="margin:1.5rem 0 .75rem;font-size:1rem;color:var(--gray-700)">{{ __('owner_name') }}</h4>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('owner_name') }}</label>
                    <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $vehicle->owner_name) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('owner_phone') }}</label>
                    <input type="text" name="owner_phone" class="form-control" value="{{ old('owner_phone', $vehicle->owner_phone) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'بريد المالك' : 'Owner Email' }}</label>
                    <input type="email" name="owner_email" class="form-control" value="{{ old('owner_email', $vehicle->owner_email) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('notes') }}</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $vehicle->notes) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('save') }}</button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">{{ __('cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('modals')
<script src="{{ asset('js/car-selector.js') }}"></script>
<script src="{{ asset('js/plate-input.js') }}"></script>
<script>
initCarSelector('v-car-make', 'v-car-model', 'v-car-color');
initPlateInput('v-plate');
</script>
@endsection