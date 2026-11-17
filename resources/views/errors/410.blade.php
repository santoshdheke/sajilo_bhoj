<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create your workspace - SajiloBhoj</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @php
        // Shown when no workspace exists on this domain (see Modules\Restaurant\Http\Middleware\ResolveTenant).
        // Kept free of module partials so it renders whichever modules are enabled.
        $host    = $exception->getMessage() ?: request()->getHost();
        $options = array_filter([
            Route::has('restaurant.register.create') ? [
                'url'   => route('restaurant.register.create'),
                'icon'  => 'bi-cup-hot',
                'color' => '#198754',
                'title' => 'Create restaurant',
                'text'  => 'Menu, tables, orders, kitchen display and an online ordering website.',
            ] : null,
            Route::has('shop.register.create') ? [
                'url'   => route('shop.register.create'),
                'icon'  => 'bi-bag-check',
                'color' => '#2563eb',
                'title' => 'Create shop',
                'text'  => 'Products, stock, billing and sales for your retail shop.',
            ] : null,
            Route::has('school.register.create') ? [
                'url'   => route('school.register.create'),
                'icon'  => 'bi-mortarboard',
                'color' => '#0b6bcb',
                'title' => 'Create school',
                'text'  => 'Students, teachers, classes, sections and subjects for your school.',
            ] : null,
        ]);
    @endphp
    <style>
        body { font-family: Inter, system-ui, sans-serif; background: #f4f6fb; min-height: 100vh; display: grid; place-items: center; padding: 16px; color: #1f2937; }
        .wrap { max-width: {{ count($options) > 2 ? 1000 : 760 }}px; width: 100%; text-align: center; }
        .logo { display: inline-flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1.35rem; color: #1f2937; text-decoration: none; margin-bottom: 28px; }
        .logo svg { width: 36px; height: 36px; color: #198754; }
        .logo span span { color: #198754; }
        h1 { font-size: 2rem; font-weight: 800; margin-bottom: 8px; }
        .note { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 1px solid #e5e7eb; border-radius: 999px; padding: 6px 14px; font-size: .9rem; color: #4b5563; margin-bottom: 32px; }
        .option { display: block; height: 100%; background: #fff; border: 2px solid transparent; border-radius: 20px; padding: 32px 24px; text-align: left; text-decoration: none; color: inherit;
                  box-shadow: 0 12px 32px rgba(15, 23, 42, .06); transition: transform .15s, border-color .15s, box-shadow .15s; }
        .option:hover, .option:focus-visible { transform: translateY(-3px); border-color: var(--c); box-shadow: 0 18px 40px rgba(15, 23, 42, .1); }
        .option .icon { width: 56px; height: 56px; border-radius: 16px; display: grid; place-items: center; font-size: 26px; color: var(--c); background: color-mix(in srgb, var(--c) 12%, white); margin-bottom: 16px; }
        .option h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 6px; }
        .option p { color: #6b7280; margin-bottom: 16px; }
        .option .go { color: var(--c); font-weight: 600; }
    </style>
</head>

<body>
    <main class="wrap">
        <a class="logo" href="#" aria-label="SajiloBhoj">
            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M13 26c-5 0-8-4-8-8 0-5 4-8 9-7 1-5 6-8 10-8s9 3 10 8c5-1 9 2 9 7 0 4-3 8-8 8"/>
                <path d="M13 24v16h22V24"/><path d="M13 33h22"/><path d="M20 15v4M28 15v4"/>
            </svg>
            <span>Sajilo<span>Bhoj</span></span>
        </a>

        <h1>Create your workspace</h1>
        <div class="note"><i class="bi bi-globe2"></i>No workspace at <strong>{{ $host }}</strong> yet</div>

        <div class="row g-4 justify-content-center">
            @foreach ($options as $option)
                <div class="{{ count($options) > 2 ? 'col-md-4' : 'col-md-6' }}">
                    <a class="option" href="{{ $option['url'] }}" style="--c: {{ $option['color'] }}">
                        <div class="icon"><i class="bi {{ $option['icon'] }}"></i></div>
                        <h2>{{ $option['title'] }}</h2>
                        <p>{{ $option['text'] }}</p>
                        <span class="go">Get started <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            @endforeach
        </div>
    </main>
</body>

</html>
