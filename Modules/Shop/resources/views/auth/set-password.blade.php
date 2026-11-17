<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set your password - {{ $shopName }}</title>
    @include('shop::partials.auth-split.head')
</head>

<body>
    @include('shop::partials.auth-split.decor')

    <main class="ws-card">
        @include('shop::partials.auth-split.side', [
            'title'     => 'Welcome to',
            'highlight' => 'the team',
            'lead'      => 'Choose a password to activate your staff account.',
            'note'      => '<i class="bi bi-shop"></i>Staff of <strong>' . e($shopName) . '</strong>',
        ])

        <section class="ws-form">
            <form action="{{ route('shop.staff.set_password.submit', $token) }}" method="POST">
                @csrf
                <div class="eyebrow">Hi {{ $user->name }}</div>
                <h2 class="mb-4">Set your password</h2>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="ws-field"><i class="bi bi-envelope"></i>
                        <input class="form-control" type="email" value="{{ $user->email }}" disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">New password</label>
                    <div class="ws-field has-eye"><i class="bi bi-lock"></i>
                        <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password"
                               placeholder="8+ characters" required autocomplete="new-password">
                        <button type="button" class="ws-eye" data-toggle-pwd="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <div class="ws-field"><i class="bi bi-lock"></i>
                        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Repeat password" required autocomplete="new-password">
                    </div>
                </div>

                <button class="ws-submit" type="submit">Activate my account <i class="bi bi-arrow-right"></i></button>
            </form>
        </section>
    </main>

    @include('shop::partials.auth-split.toggle-password')
</body>

</html>
