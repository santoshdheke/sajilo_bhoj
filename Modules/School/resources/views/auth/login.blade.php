@php $name = $school?->name ?? config('app.name'); @endphp
<x-school::layouts.auth title="Sign in">
    <div class="sms-login__brand">
        <span class="sms-login__brand-icon"><span class="material-icons-outlined" aria-hidden="true">school</span></span>
        <span class="sms-login__brand-text">
            <span class="sms-login__brand-name">{{ $name }}</span>
            <span class="sms-login__brand-tag">School Portal</span>
        </span>
    </div>

    <h1 class="sms-login__title">Sign In to Your Account</h1>
    <p class="sms-login__subtitle">
        {{ $school ? 'Enter your school credentials to continue' : 'Enter your administrator credentials to continue' }}
    </p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('school.login.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label sms-login__label">Email</label>
            <div class="sms-login__field">
                <span class="material-icons-outlined sms-login__field-icon" aria-hidden="true">person</span>
                <input type="email" id="email" name="email"
                    class="form-control sms-login__input @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="name@school.edu" required autofocus autocomplete="username">
            </div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label sms-login__label">Password</label>
            <div class="sms-login__field">
                <span class="material-icons-outlined sms-login__field-icon" aria-hidden="true">lock</span>
                <input type="password" id="password" name="password"
                    class="form-control sms-login__input sms-login__input--trailing @error('password') is-invalid @enderror"
                    placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="sms-login__toggle" data-sms-toggle-password="password" aria-label="Show password" aria-pressed="false">
                    <span class="material-icons-outlined" aria-hidden="true">visibility</span>
                </button>
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn sms-login__submit w-100">
            Sign In <span class="material-icons-outlined" aria-hidden="true">arrow_forward</span>
        </button>
    </form>

    <x-slot:footer>
        <span class="material-icons-outlined sms-login__footer-icon" aria-hidden="true">verified_user</span>
        Secure sign-in for {{ $school ? $school->name : config('app.name') }}
    </x-slot:footer>

    <script>
        document.querySelectorAll('[data-sms-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.smsTogglePassword);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', show);
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                btn.querySelector('span').textContent = show ? 'visibility_off' : 'visibility';
            });
        });
    </script>
</x-school::layouts.auth>
