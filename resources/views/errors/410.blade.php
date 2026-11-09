<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create your restaurant - SajiloBhoj</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @php
        $host     = $exception->getMessage() ?: request()->getHost();
        $color    = old('theme_color', '#198754');
        $presets  = ['#198754', '#22c55e', '#f59e0b', '#2563eb', '#ec4899', '#8b5cf6', '#1e3a8a', '#64748b'];
    @endphp
    <style>:root { --primary: {{ $color }}; }</style>
    @include('partials.auth-split.styles')
</head>

<body>
    @include('partials.auth-split.decor')

    <main class="ws-card">
        @include('partials.auth-split.side', ['note' => '<i class="bi bi-globe2"></i>No workspace at <strong>' . e($host) . '</strong> yet'])

        {{-- ── Right: sign-up form ── --}}
        <section class="ws-form">
            <form action="{{ route('register.store') }}" method="POST" novalidate>
                @csrf
                <div class="eyebrow">Get Started</div>
                <h2>Create your workspace</h2>
                <p class="signin">Your kitchen, your menu, your way. Let's cook up something delicious together <i class="bi bi-heart-fill" style="color: var(--primary)"></i></p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="company_name">Company name</label>
                        <div class="ws-field"><i class="bi bi-building"></i>
                            <input class="form-control @error('company_name') is-invalid @enderror" type="text" id="company_name" name="company_name"
                                   value="{{ old('company_name') }}" placeholder="The Green Fork" required autocomplete="organization">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="owner_name">Your name</label>
                        <div class="ws-field"><i class="bi bi-person"></i>
                            <input class="form-control @error('owner_name') is-invalid @enderror" type="text" id="owner_name" name="owner_name"
                                   value="{{ old('owner_name') }}" placeholder="Owner full name" required autocomplete="name">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="owner_email">Email</label>
                        <div class="ws-field"><i class="bi bi-envelope"></i>
                            <input class="form-control @error('owner_email') is-invalid @enderror" type="email" id="owner_email" name="owner_email"
                                   value="{{ old('owner_email') }}" placeholder="owner@restaurant.com" required autocomplete="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <div class="ws-field"><i class="bi bi-lock"></i>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password"
                                   placeholder="8+ characters" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <div class="ws-field"><i class="bi bi-lock"></i>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Repeat password" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Brand color</label>
                        <input type="hidden" name="theme_color" id="theme_color" value="{{ $color }}">
                        <div class="ws-colors" role="radiogroup" aria-label="Brand color">
                            @foreach ($presets as $p)
                                <button type="button" class="ws-color" data-color="{{ $p }}" style="background: {{ $p }}" role="radio" aria-label="{{ $p }}"></button>
                            @endforeach
                            <label class="ws-color ws-color-custom" title="Custom colour" aria-label="Custom colour">
                                <input type="color" id="theme_color_custom" value="{{ $color }}">
                            </label>
                        </div>
                        @error('theme_color')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <button class="ws-submit" type="submit">Create workspace <i class="bi bi-arrow-right"></i></button>
            </form>
        </section>
    </main>

    <script>
    (function () {
        var hidden = document.getElementById('theme_color'), custom = document.getElementById('theme_color_custom'),
            swatches = document.querySelectorAll('.ws-color[data-color]'), customBox = document.querySelector('.ws-color-custom');

        // Pick a brand colour: recolours the whole page (chef included) and fills the form field.
        function setColor(hex) {
            hex = hex.toLowerCase();
            hidden.value = hex;
            document.documentElement.style.setProperty('--primary', hex);
            var matched = false;
            swatches.forEach(function (b) {
                var on = b.dataset.color.toLowerCase() === hex;
                b.classList.toggle('active', on); b.setAttribute('aria-checked', on ? 'true' : 'false');
                matched = matched || on;
            });
            customBox.classList.toggle('active', !matched);
            customBox.style.background = matched ? '' : hex;
        }
        swatches.forEach(function (b) { b.addEventListener('click', function () { setColor(b.dataset.color); }); });
        custom.addEventListener('input', function () { setColor(custom.value); });
        setColor(hidden.value);
    })();
    </script>
</body>

</html>
