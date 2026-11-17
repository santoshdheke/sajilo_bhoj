<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create your shop - SajiloBhoj</title>
    @include('shop::partials.auth-split.head')
    <style>
        .otp-row { display: flex; gap: 8px; }
        .otp-row .ws-field { flex: 1; }
        .otp-btn { border: 0; border-radius: 12px; padding: 0 16px; font-weight: 600; white-space: nowrap; background: color-mix(in srgb, var(--primary) 12%, white); color: var(--primary); }
        .otp-btn:disabled { opacity: .55; }
        .otp-msg { font-size: .82rem; margin-top: 6px; }
        .otp-ok { color: #16a34a; font-weight: 600; }
        .ws-submit:disabled { opacity: .55; cursor: not-allowed; }
    </style>
</head>

<body>
    @include('shop::partials.auth-split.decor')

    <main class="ws-card">
        @include('shop::partials.auth-split.side', ['note' => '<i class="bi bi-globe2"></i>No workspace at <strong>' . e($host) . '</strong> yet'])

        <section class="ws-form">
            <form action="{{ route('shop.register.store') }}" method="POST" novalidate id="registerForm">
                @csrf
                <div class="eyebrow">Get Started</div>
                <h2>Create your shop</h2>
                <p class="signin">Sell, stock and keep accounts in one place. We'll verify your email first.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="shop_name">Shop name</label>
                        <div class="ws-field"><i class="bi bi-shop"></i>
                            <input class="form-control @error('shop_name') is-invalid @enderror" type="text" id="shop_name" name="shop_name"
                                   value="{{ old('shop_name') }}" placeholder="Sajilo Mart" required autocomplete="organization">
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
                        <label class="form-label" for="email">Email</label>
                        <div class="otp-row">
                            <div class="ws-field"><i class="bi bi-envelope"></i>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email"
                                       value="{{ old('email') }}" placeholder="owner@shop.com" required autocomplete="email">
                            </div>
                            <button type="button" class="otp-btn" id="sendOtp">Send code</button>
                        </div>
                        <div class="otp-msg" id="sendMsg"></div>
                    </div>

                    <div class="col-12 d-none" id="otpBlock">
                        <label class="form-label" for="otp">Verification code</label>
                        <div class="otp-row">
                            <div class="ws-field"><i class="bi bi-shield-check"></i>
                                <input class="form-control" type="text" id="otp" inputmode="numeric" maxlength="6" placeholder="6-digit code" autocomplete="one-time-code">
                            </div>
                            <button type="button" class="otp-btn" id="verifyOtp">Verify</button>
                        </div>
                        <div class="otp-msg" id="verifyMsg"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="phone_number">Phone</label>
                        <div class="ws-field"><i class="bi bi-telephone"></i>
                            <input class="form-control @error('phone_number') is-invalid @enderror" type="tel" id="phone_number" name="phone_number"
                                   value="{{ old('phone_number') }}" placeholder="98XXXXXXXX" maxlength="10" required autocomplete="tel">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="address">Address <span class="text-muted fw-normal">(optional)</span></label>
                        <div class="ws-field"><i class="bi bi-geo-alt"></i>
                            <input class="form-control" type="text" id="address" name="address" value="{{ old('address') }}" placeholder="City, street" autocomplete="street-address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <div class="ws-field has-eye"><i class="bi bi-lock"></i>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password"
                                   placeholder="8+ characters" required autocomplete="new-password">
                            <button type="button" class="ws-eye" data-toggle-pwd="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <div class="ws-field"><i class="bi bi-lock"></i>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Repeat password" required autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <button class="ws-submit" type="submit" id="submitBtn" disabled>Create shop <i class="bi bi-arrow-right"></i></button>
                <div class="ws-secure"><i class="bi bi-info-circle"></i><span id="submitHint">Verify your email to continue</span></div>
            </form>
        </section>
    </main>

    @include('shop::partials.auth-split.toggle-password')
    <script>
    (function () {
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var email = document.getElementById('email'), sendBtn = document.getElementById('sendOtp'),
            verifyBtn = document.getElementById('verifyOtp'), otp = document.getElementById('otp'),
            submit = document.getElementById('submitBtn'), verifiedFor = null;

        function msg(id, text, ok) {
            var el = document.getElementById(id);
            el.textContent = text || '';
            el.className = 'otp-msg ' + (ok ? 'otp-ok' : 'text-danger');
        }
        function post(url, body) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(body)
            }).then(function (r) {
                return r.json().catch(function () { return {}; }).then(function (d) {
                    if (!r.ok && !d.message) d.message = d.errors ? Object.values(d.errors)[0][0] : 'Something went wrong. Please try again.';
                    d.success = r.ok && d.success !== false;
                    return d;
                });
            });
        }
        function setVerified(addr) {
            verifiedFor = addr;
            submit.disabled = !addr;
            document.getElementById('submitHint').textContent = addr ? 'Email verified. You can create your shop.' : 'Verify your email to continue';
        }

        // Back from a validation error with an email that was already verified: no new code needed.
        @if (old('email') && session('shop_otp_verified_email') === old('email'))
            setVerified(@json(old('email'))); msg('sendMsg', '✓ Email verified', true);
        @endif

        sendBtn.addEventListener('click', function () {
            if (!email.value.trim()) { msg('sendMsg', 'Enter your email first.'); return; }
            sendBtn.disabled = true; msg('sendMsg', 'Sending...', true);
            post(@json(route('shop.register.send_otp')), { email: email.value.trim() }).then(function (d) {
                msg('sendMsg', d.message, d.success);
                if (d.success) { document.getElementById('otpBlock').classList.remove('d-none'); otp.focus(); }
            }).finally(function () { setTimeout(function () { sendBtn.disabled = false; sendBtn.textContent = 'Resend'; }, 15000); });
        });

        verifyBtn.addEventListener('click', function () {
            verifyBtn.disabled = true;
            post(@json(route('shop.register.verify_otp')), { email: email.value.trim(), otp: otp.value.trim() }).then(function (d) {
                msg('verifyMsg', d.success ? '✓ Email verified' : d.message, d.success);
                if (d.success) { setVerified(email.value.trim()); otp.disabled = true; } else { verifyBtn.disabled = false; }
            });
        });

        // Changing the email after verifying needs a new code.
        email.addEventListener('input', function () {
            if (verifiedFor && email.value.trim() !== verifiedFor) {
                setVerified(null); otp.disabled = false; otp.value = ''; verifyBtn.disabled = false; msg('verifyMsg', '');
            }
        });
    })();
    </script>
</body>

</html>
