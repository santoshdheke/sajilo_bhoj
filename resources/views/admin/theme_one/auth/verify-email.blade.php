<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your email - {{ $brandName ?? 'SajiloBhoj' }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>:root { --primary: {{ $themeColor ?? '#198754' }}; }</style>
    @include('partials.auth-split.styles')
    <style>
        .ws-form { display: flex; flex-direction: column; justify-content: center; }
        .vf-icon { width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;
                   color: var(--primary); background: color-mix(in srgb, var(--primary) 12%, white); margin-bottom: 18px; }
        .vf-email { font-weight: 700; color: #14201a; word-break: break-all; }
        .vf-code { display: flex; gap: 10px; margin: 22px 0 8px; }
        .vf-code input { width: 100%; min-width: 0; height: 58px; text-align: center; font-size: 1.5rem; font-weight: 700; border-radius: 12px;
                         border: 1px solid #dfe5e2; background: #fff; color: #14201a; }
        .vf-code input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary) 14%, transparent); }
        .vf-code.is-invalid input { border-color: #dc3545; }
        .vf-error { color: #dc3545; font-size: .88rem; min-height: 1.3em; }
        .vf-links { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 18px; font-size: .9rem; }
        .vf-links button { border: 0; background: none; padding: 0; color: var(--primary); font-weight: 600; }
        .vf-links button:disabled { color: var(--muted); font-weight: 500; }
        .vf-hint { margin-top: 22px; font-size: .82rem; color: var(--muted); }
        @media (max-width: 400px) { .vf-code { gap: 6px; } .vf-code input { height: 50px; font-size: 1.25rem; } }
    </style>
</head>

<body>
    @include('partials.auth-split.decor')

    <main class="ws-card">
        @include('partials.auth-split.side', [
            'title'     => 'Almost there,',
            'highlight' => 'one quick check',
            'lead'      => 'We just want to be sure this email is yours, so your restaurant stays safe.',
        ])

        <section class="ws-form">
            <div class="vf-icon"><i class="bi bi-envelope-check"></i></div>
            <div class="eyebrow">Verify your email</div>
            <h2 class="mb-2">Check your inbox</h2>
            <p class="text-secondary mb-0">We sent a 6-digit code to <span class="vf-email">{{ $email }}</span>.</p>

            @if (session('status'))
                <div class="alert alert-success py-2 small mt-3 mb-0">{{ session('status') }}</div>
            @endif

            <form action="{{ route('admin.verify_email.verify') }}" method="POST" id="vfForm" novalidate>
                @csrf
                <input type="hidden" name="code" id="vfCode">
                <div class="vf-code {{ $errors->has('code') ? 'is-invalid' : '' }}" id="vfBoxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit {{ $i + 1 }}" {{ $i === 0 ? 'autofocus autocomplete=one-time-code' : 'autocomplete=off' }}>
                    @endfor
                </div>
                <div class="vf-error">@error('code'){{ $message }}@enderror</div>
                <button class="ws-submit" type="submit">Verify email <i class="bi bi-arrow-right"></i></button>
            </form>

            <div class="vf-links">
                <form action="{{ route('admin.verify_email.resend') }}" method="POST">
                    @csrf
                    <button type="submit" id="vfResend" {{ $resendIn ? 'disabled' : '' }} data-wait="{{ $resendIn }}">
                        <i class="bi bi-arrow-clockwise"></i> <span>{{ $resendIn ? "Send a new code in {$resendIn}s" : 'Send a new code' }}</span>
                    </button>
                </form>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="color:var(--muted);font-weight:500;"><i class="bi bi-box-arrow-left"></i> Use a different account</button>
                </form>
            </div>
            <p class="vf-hint"><i class="bi bi-info-circle me-1"></i>Can’t find it? Check your spam folder. The code works for 15 minutes.</p>
        </section>
    </main>

    <script>
    (function () {
        var boxes = Array.prototype.slice.call(document.querySelectorAll('#vfBoxes input')),
            hidden = document.getElementById('vfCode'), form = document.getElementById('vfForm');

        function fill(digits, from) {
            digits.split('').forEach(function (d, i) { if (boxes[from + i]) boxes[from + i].value = d; });
            var next = boxes[Math.min(from + digits.length, boxes.length - 1)];
            next.focus();
            sync();
        }
        function sync() {
            hidden.value = boxes.map(function (b) { return b.value; }).join('');
            if (hidden.value.length === 6) form.requestSubmit ? form.requestSubmit() : form.submit();
        }
        boxes.forEach(function (box, i) {
            box.addEventListener('input', function () {
                var digits = box.value.replace(/\D/g, '');
                box.value = '';
                if (digits) fill(digits, i); else sync();
            });
            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !box.value && i > 0) { boxes[i - 1].value = ''; boxes[i - 1].focus(); sync(); e.preventDefault(); }
                if (e.key === 'ArrowLeft' && i > 0) boxes[i - 1].focus();
                if (e.key === 'ArrowRight' && i < 5) boxes[i + 1].focus();
            });
            // Pasting the whole code into any box fills all six.
            box.addEventListener('paste', function (e) {
                var digits = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                if (digits) { e.preventDefault(); boxes.forEach(function (b) { b.value = ''; }); fill(digits, 0); }
            });
        });
        form.addEventListener('submit', function (e) {
            hidden.value = boxes.map(function (b) { return b.value; }).join('');
            if (hidden.value.length !== 6) { e.preventDefault(); document.querySelector('.vf-error').textContent = 'Enter all 6 digits.'; }
        });

        // Countdown on the resend button.
        var btn = document.getElementById('vfResend'), wait = parseInt(btn.dataset.wait, 10) || 0;
        if (wait) {
            var t = setInterval(function () {
                wait--;
                if (wait <= 0) { clearInterval(t); btn.disabled = false; btn.querySelector('span').textContent = 'Send a new code'; }
                else btn.querySelector('span').textContent = 'Send a new code in ' + wait + 's';
            }, 1000);
        }
    })();
    </script>
</body>

</html>
