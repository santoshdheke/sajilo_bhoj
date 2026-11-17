<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop login - {{ $shopName ?? 'SajiloBhoj' }}</title>
    @include('shop::partials.auth-split.head')
</head>

<body>
    @include('shop::partials.auth-split.decor')

    <main class="ws-card">
        @include('shop::partials.auth-split.side', [
            'title'     => 'Good to see you',
            'highlight' => 'again',
            'lead'      => 'Your shelves are stocked and your counter is ready. Let\'s make some sales today.',
            'note'      => '<i class="bi bi-shop"></i>Staff login for <strong>' . e($shopName ?? request()->getHost()) . '</strong>',
        ])

        <section class="ws-form">
            <form action="{{ route('shop.admin.auth.login') }}" method="POST">
                @csrf
                <div class="eyebrow">Welcome back</div>
                <h2 class="mb-4">Sign in to your shop</h2>

                @if (session('success'))
                    <div class="alert alert-success py-2 small">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <div class="ws-field"><i class="bi bi-envelope"></i>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email"
                               value="{{ old('email') }}" placeholder="you@shop.com" required autofocus autocomplete="username">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <div class="ws-field has-eye"><i class="bi bi-lock"></i>
                        <input class="form-control" type="password" id="password" name="password" placeholder="Your password" required autocomplete="current-password">
                        <button type="button" class="ws-eye" data-toggle-pwd="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" checked>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button class="ws-submit" type="submit">Sign in <i class="bi bi-arrow-right"></i></button>
                <div class="ws-secure"><i class="bi bi-shield-lock-fill"></i>Safe and secure for the owner and every staff member</div>
            </form>
        </section>
    </main>

    @include('shop::partials.auth-split.toggle-password')
</body>

</html>
