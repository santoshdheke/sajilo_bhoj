<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription paused - {{ $brandName ?? 'SajiloBhoj' }}</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: {{ $themeColor ?? '#198754' }}; }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; box-sizing:border-box;
               font-family:Inter, system-ui, sans-serif; text-align:center; color:#14201a;
               background:linear-gradient(160deg, color-mix(in srgb, var(--primary) 10%, white), #f7faf8 60%); }
        .box { max-width:440px; background:#fff; padding:34px 28px; border-radius:22px; box-shadow:0 14px 40px rgba(15,40,25,.08); }
        .ic { font-size:2.6rem; color:var(--primary); }
        h1 { font-size:1.5rem; font-weight:800; margin:10px 0 8px; }
        p { color:#5b6660; line-height:1.6; margin:0 0 20px; }
        button { border:0; background:none; color:var(--primary); font-weight:600; cursor:pointer; font-size:.95rem; }
    </style>
</head>
<body>
    <div class="box">
        <div class="ic"><i class="bi bi-pause-circle"></i></div>
        <h1>The subscription is paused</h1>
        <p>{{ $brandName ?? 'This restaurant' }}’s plan has run out of balance. Please ask the owner to top up — you can sign in again as soon as it’s done.</p>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit"><i class="bi bi-box-arrow-left"></i> Sign out</button></form>
    </div>
</body>
</html>
