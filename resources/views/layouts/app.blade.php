<!DOCTYPE html>
@php $lang = app()->getLocale(); @endphp
<html lang="{{ $lang }}" dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('dashboard')) - {{ __('app_name') }}</title>
    @php
        $favicon = \App\Domain\Models\Setting::get('company_favicon');
        $brandLogo = \App\Domain\Models\Setting::get('company_logo');
        $brandName = \App\Domain\Models\Setting::get(
            $lang === 'ar' ? 'company_name_ar' : 'company_name_en',
            $lang === 'ar' ? config('vis.company.name_ar', 'VIS') : config('vis.company.name_en', 'VIS')
        );
    @endphp
    @if($favicon)
    <link rel="icon" type="image/png" href="{{ Storage::url($favicon) }}">
    <link rel="shortcut icon" href="{{ Storage::url($favicon) }}">
    @endif

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $brandName ?: 'VIS' }}">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        (function(){
            var t = localStorage.getItem('vis-theme') || 'light';
            var customHex = localStorage.getItem('vis-custom-accent');
            var preset = localStorage.getItem('vis-accent') || '';
            var d = document.documentElement;

            if (t === 'dark') d.classList.add('dark');

            if (customHex) {
                // Custom color: set variables directly
                d.style.setProperty('--accent', customHex);
                // Darken for hover
                var r=parseInt(customHex.slice(1,3),16), g=parseInt(customHex.slice(3,5),16), b=parseInt(customHex.slice(5,7),16);
                var dr=Math.max(0,Math.round(r*0.85)), dg=Math.max(0,Math.round(g*0.85)), db=Math.max(0,Math.round(b*0.85));
                d.style.setProperty('--accent-hover', '#'+[dr,dg,db].map(function(c){return c.toString(16).padStart(2,'0')}).join(''));
                // Text contrast
                var lum = (0.299*r + 0.587*g + 0.114*b) / 255;
                d.style.setProperty('--accent-text', lum > 0.55 ? '#152d4a' : '#ffffff');
            } else if (preset) {
                d.setAttribute('data-accent', preset);
            }
        })();
    </script>
</head>
<body>
<div class="app-layout">
    {{-- Sidebar --}}
    <aside class="app-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                @if($brandLogo)
                    <img src="{{ Storage::url($brandLogo) }}" alt="{{ $brandName }}" style="width:24px;height:24px;object-fit:contain;border-radius:4px">
                @else
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
                @endif
            </div>
            <div class="brand-info">
                <div class="brand-text">{{ $brandName ?: 'VIS' }}</div>
            </div>
            <button class="sidebar-close-btn" onclick="closeMobileSidebar()" aria-label="Close menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">{{ __('main_menu') }}</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tip="{{ __('home') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>{{ __('home') }}</span>
            </a>

            <div class="nav-section-label">{{ __('inspections_menu') }}</div>

            @can('view inspections')
            <a href="{{ route('inspections.index') }}" class="nav-item {{ request()->routeIs('inspections.*') ? 'active' : '' }}" data-tip="{{ __('inspections') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span>{{ __('inspections') }}</span>
            </a>
            @endcan

            @can('view vehicles')
            <a href="{{ route('vehicles.index') }}" class="nav-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" data-tip="{{ __('vehicles') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
                <span>{{ __('vehicles') }}</span>
            </a>
            @endcan

            @can('view vehicles')
            <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}" data-tip="{{ $lang === 'ar' ? 'العملاء' : 'Customers' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>{{ $lang === 'ar' ? 'العملاء' : 'Customers' }}</span>
            </a>
            @endcan

            @can('view audit logs')
            <a href="{{ route('audit-logs.index') }}" class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" data-tip="{{ __('reports') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <span>{{ __('reports') }}</span>
            </a>
            @endcan
            
            @can('view finance')
            <a href="{{ route('finance.index') }}" class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}" data-tip="{{ $lang === 'ar' ? 'المالية' : 'Finance' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                <span>{{ $lang === 'ar' ? 'المالية' : 'Finance' }}</span>
            </a>
            @endcan

            @canany(['manage templates', 'manage users'])
            <div class="nav-section-label">{{ __('settings') }}</div>
            @endcanany

            @can('manage templates')
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" data-tip="{{ $lang === 'ar' ? 'الإعدادات' : 'Settings' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                <span>{{ $lang === 'ar' ? 'الإعدادات' : 'Settings' }}</span>
            </a>
            @endcan

            @can('view templates')
            <a href="{{ route('templates.index') }}" class="nav-item {{ request()->routeIs('templates.*') ? 'active' : '' }}" data-tip="{{ __('templates') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                <span>{{ __('templates') }}</span>
            </a>
            @endcan

            @can('view users')
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" data-tip="{{ __('users') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                <span>{{ __('users') }}</span>
            </a>
            @endcan
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>{{ __('logout') }}</span>
                </button>
            </form>

            <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                <span>{{ __('collapse_menu') }}</span>
            </button>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeMobileSidebar()"></div>

    {{-- Main --}}
    <main class="app-main">
        <header class="app-header">
            <div class="header-left">
                <button class="mobile-toggle" onclick="openMobileSidebar()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h2 class="page-title">@yield('title', __('dashboard'))</h2>
            </div>
            <div class="header-right">
                <button class="theme-toggle" onclick="toggleDarkMode()" title="{{ app()->getLocale() === 'ar' ? 'تغيير المظهر' : 'Toggle theme' }}">
                    <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>
                <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="lang-toggle" title="{{ app()->getLocale() === 'ar' ? 'Switch to English' : 'التبديل للعربية' }}">
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}
                </a>
                <div class="sidebar-user">
                    <div class="user-info" style="text-align:{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}">
                        <div class="user-name" style="color:var(--gray-800)">{{ auth()->user()->name ?? '' }}</div>
                        <div class="user-role" style="color:var(--gray-500)">{{ auth()->user()->roles->first()?->name ?? '' }}</div>
                    </div>
                    <div class="user-avatar">{{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}</div>
                </div>
            </div>
        </header>

        <div class="app-content">
            @if(session('success'))
                <div class="alert alert-success" data-auto-dismiss>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" data-auto-dismiss>
                    {{ session('error') }}
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<div class="toast-container" id="toast-container"></div>
<div class="modal-backdrop" id="modal-backdrop" onclick="closeAllModals()"></div>
<div class="drawer-backdrop" id="drawer-backdrop" onclick="closeAllDrawers()"></div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
function toggleDarkMode(){
    var d=document.documentElement;
    d.classList.toggle('dark');
    localStorage.setItem('vis-theme', d.classList.contains('dark') ? 'dark' : 'light');
}
function setAccent(color){
    document.documentElement.setAttribute('data-accent', color);
    localStorage.setItem('vis-accent', color);
}
</script>

@yield('modals')

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(function() {});
}
</script>
</body>
</html>