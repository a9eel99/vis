<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غير متصل — VIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; color: #e2e8f0; text-align: center; padding: 2rem; }
        .icon { font-size: 5rem; margin-bottom: 1rem; }
        .title { font-size: 1.5rem; font-weight: 700; margin-bottom: .5rem; }
        .desc { color: #94a3b8; font-size: .95rem; margin-bottom: 2rem; max-width: 350px; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 2rem; background: #f59e0b; color: #1e293b; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; font-family: inherit; }
    </style>
</head>
<body>
    <div>
        <div class="icon">📡</div>
        <div class="title">غير متصل بالإنترنت</div>
        <div class="desc">تأكد من اتصالك بالإنترنت وحاول مرة أخرى.</div>
        <button class="btn" onclick="location.reload()">🔄 إعادة المحاولة</button>
    </div>
</body>
</html>