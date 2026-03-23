@php $lang = app()->getLocale(); @endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — {{ $lang === 'ar' ? 'خطأ في الخادم' : 'Server Error' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; color: #e2e8f0; text-align: center; padding: 2rem; }
        .code { font-size: 8rem; font-weight: 800; color: #ef4444; line-height: 1; }
        .title { font-size: 1.5rem; font-weight: 600; margin: 1rem 0 .5rem; }
        .desc { color: #94a3b8; font-size: .95rem; margin-bottom: 2rem; max-width: 400px; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 2rem; background: #f59e0b; color: #1e293b; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; text-decoration: none; font-family: inherit; transition: background .2s; }
        .btn:hover { background: #d97706; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div>
        <div class="icon">⚠️</div>
        <div class="code">500</div>
        <div class="title">{{ $lang === 'ar' ? 'خطأ في الخادم' : 'Server Error' }}</div>
        <div class="desc">{{ $lang === 'ar' ? 'حدث خطأ غير متوقع. حاول مرة أخرى أو تواصل مع الدعم الفني.' : 'An unexpected error occurred. Please try again or contact support.' }}</div>
        <a href="{{ url('/') }}" class="btn">
            {{ $lang === 'ar' ? '← الرئيسية' : '← Home' }}
        </a>
    </div>
</body>
</html>