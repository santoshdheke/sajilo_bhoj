<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $name }} — coming soon</title>
    @include('partials.favicon', ['faviconColor' => $color])
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
    </style>
</head>
<body>
    <div class="box">
        <div class="mark">@include('partials.brand-logo', ['variant' => 'color', 'height' => 64, 'showName' => false])</div>
        <h1>{{ $name }}</h1>
        <p>Our website is coming soon. Please visit us in person or check back later. Thank you!</p>
    </div>
</body>
</html>
