<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff login - {{ $brandName ?? 'SajiloBhoj' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>:root { --primary: {{ $themeColor ?? '#198754' }}; }</style>
    @include('partials.auth-split.styles')
    <style>
        .ws-form .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        .ws-form .form-check-input:focus { box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary) 14%, transparent); border-color: var(--primary); }
        .ws-form .form-check-label { color: #2f3b34; }
        .ws-eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: none; color: #7a867f; padding: 6px 8px; }
        .ws-field.has-eye .form-control { padding-right: 46px; }
        .ws-form { display: flex; flex-direction: column; justify-content: center; }
        .ws-secure { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 18px; font-size: .85rem; color: var(--muted); }
        .ws-secure i { color: var(--primary); }
    </style>
</head>

<body>
    @include('partials.auth-split.decor')

    <main class="ws-card">
        @include('partials.auth-split.side', [
            'title'     => 'Good to see you',
            'highlight' => 'again',
            'lead'      => 'Your kitchen is warm and your tables are waiting. Let\'s make today delicious.',
            'note' => '<i class="bi bi-shop"></i>Staff login for <strong>' . e($brandName ?? request()->getHost()) . '</strong>',
        ])

        {{-- ── Right: staff login form ── --}}
        <section class="ws-form">
            <form action="{{ route('admin.auth.login') }}" method="POST">
                @csrf
                <div class="eyebrow">Welcome back, chef</div>
                <h2 class="mb-4">Sign in and start serving smiles</h2>

                @if (session('status'))
                    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <div class="ws-field"><i class="bi bi-envelope"></i>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email"
                               value="{{ old('email') }}" placeholder="you@restaurant.com" required autofocus autocomplete="username">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <div class="ws-field has-eye"><i class="bi bi-lock"></i>
                        <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password"
                               placeholder="Your password" required autocomplete="current-password">
                        <button type="button" class="ws-eye" id="togglePwd" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button class="ws-submit" type="submit">Sign in <i class="bi bi-arrow-right"></i></button>
                <div class="ws-secure"><i class="bi bi-shield-lock-fill"></i>Safe and secure for your whole team: managers, waiters and kitchen</div>
            </form>
        </section>
    </main>

    <script>
        document.getElementById('togglePwd').addEventListener('click', function () {
            var p = document.getElementById('password'), show = p.type === 'password';
            p.type = show ? 'text' : 'password';
            this.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    </script>
</body>

</html>
