@extends('layouts.app')
@section('title', __('add_user'))

@section('content')
<div class="page-header"><h1>{{ __('add_user') }}</h1></div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('name') }} <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('email') }} <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('password') }} <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('confirm_password') }} <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('phone') }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('role') }} <span class="required">*</span></label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر' : 'Select' }} --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('save') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
