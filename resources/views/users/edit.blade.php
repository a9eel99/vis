@extends('layouts.app')
@section('title', __('edit_user'))

@section('content')
<div class="page-header"><h1>{{ __('edit_user') }}: {{ $user->name }}</h1></div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('name') }} <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('email') }} <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('password') }}</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    <small class="text-muted">{{ __('leave_blank') }}</small>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('confirm_password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('phone') }}</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('role') }} <span class="required">*</span></label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->roles->first()?->name == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
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
