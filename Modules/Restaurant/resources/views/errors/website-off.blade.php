<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $name }} — under maintenance</title>
    @include('restaurant::partials.favicon', ['faviconColor' => $color])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: {{ preg_match('/^#[0-9a-f]{6}$/i', $color) ? $color : '#198754' }}; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; box-sizing: border-box;
               font-family: Inter, system-ui, sans-serif; color: #14201a; text-align: center;
               background: linear-gradient(160deg, color-mix(in srgb, var(--primary) 10%, white), #f7faf8 60%); }
        .box { max-width: 460px; }
        .mark { margin: 0 auto 18px; }
        h1 { font-size: 1.9rem; font-weight: 800; margin: 0 0 10px; color: color-mix(in srgb, var(--primary) 60%, black); }
        p { color: #5b6660; line-height: 1.6; margin: 0; }
        .badge { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; padding: 5px 12px; border-radius: 999px; font-size: .8rem; font-weight: 600;
                 color: color-mix(in srgb, var(--primary) 70%, black); background: color-mix(in srgb, var(--primary) 14%, white); }
    </style>
</head>
<body>
    <div class="box">
        <div class="mark">@include('restaurant::partials.brand-logo', ['variant' => 'color', 'height' => 64, 'showName' => false])</div>
        <div class="badge">&#128295; Under maintenance</div>
        <h1>{{ $name }}</h1>
        <p>Our website is currently under maintenance. We'll be back shortly. In the meantime, you're welcome to visit us in person. Thank you for your patience!</p>
    </div>
</body>
</html>
