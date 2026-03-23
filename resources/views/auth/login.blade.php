<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('login') }} - {{ __('app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
</head>
<body>

<div class="login-page">
    {{-- Form Side --}}
    <div class="login-form-side">
        {{-- Language Switcher --}}
        <div style="position:absolute;top:1.5rem;{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}:1.5rem;display:flex;gap:.5rem">
            <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ app()->getLocale() === 'ar' ? 'active' : '' }}">عربي</a>
            <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
        </div>

        <div class="login-logo">VIS</div>
        <h1>{{ __('welcome_back') }}</h1>
        <p class="login-subtitle">{{ __('login_subtitle') }}</p>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="example@vis.com" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('password') }}</label>
                <div class="password-wrapper">
                    <input type="password" name="password" class="form-control" id="password" placeholder="••••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">{{ __('remember_me') }}</label>
            </div>

            <button type="submit" class="btn-login">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                {{ __('login') }}
            </button>
        </form>

        <div class="login-demo-info">
            <div style="margin-bottom:.25rem">{{ __('demo_credentials') }}</div>
            <code>admin@vis.local</code> / <code>password</code>
        </div>
    </div>

    {{-- Hero Side --}}
    <div class="login-hero-side">
        <div class="hero-badge">🚀 {{ __('version') }}</div>
        <h2>{{ __('hero_title') }}</h2>
        <p class="hero-desc">{{ __('hero_desc') }}</p>

        <div class="hero-features">
            <div class="hero-feature">
                <div class="hero-feature-icon blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                <span>{{ __('hero_feature_1') }}</span>
            </div>
            <div class="hero-feature">
                <div class="hero-feature-icon green">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                </div>
                <span>{{ __('hero_feature_2') }}</span>
            </div>
            <div class="hero-feature">
                <div class="hero-feature-icon red">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <span>{{ __('hero_feature_3') }}</span>
            </div>
        </div>

        <div class="login-footer">© VIS {{ date('Y') }} - {{ __('all_rights') }}</div>
    </div>
</div>

<script>
function togglePassword() {
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
