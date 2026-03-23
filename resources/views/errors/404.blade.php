@php $lang = app()->getLocale(); @endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — {{ $lang === 'ar' ? 'الصفحة غير موجودة' : 'Page Not Found' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; color: #e2e8f0; text-align: center; padding: 2rem; }
        .code { font-size: 8rem; font-weight: 800; color: #f59e0b; line-height: 1; }
        .title { font-size: 1.5rem; font-weight: 600; margin: 1rem 0 .5rem; }
        .desc { color: #94a3b8; font-size: .95rem; margin-bottom: 2rem; max-width: 400px; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 2rem; background: #f59e0b; color: #1e293b; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; text-decoration: none; font-family: inherit; transition: background .2s; }
        .btn:hover { background: #d97706; }
        .car { font-size: 4rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div>
        <div class="car">🚗</div>
        <div class="code">404</div>
        <div class="title">{{ $lang === 'ar' ? 'الصفحة غير موجودة' : 'Page Not Found' }}</div>
        <div class="desc">{{ $lang === 'ar' ? 'الصفحة اللي بتدوّر عليها مش موجودة أو تم نقلها.' : 'The page you are looking for does not exist or has been moved.' }}</div>
        <a href="{{ url('/') }}" class="btn">
            {{ $lang === 'ar' ? '← الرئيسية' : '← Home' }}
        </a>
    </div>
</body>
</html>