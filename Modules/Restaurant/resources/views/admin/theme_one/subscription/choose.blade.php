<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose your plan - SajiloBhoj</title>
    @include('restaurant::partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root { --primary: {{ $themeColor ?? '#198754' }}; --ink: #14201a; --muted: #4b5a51; --orange: #f5a524; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--ink); overflow-x: hidden;
               background: linear-gradient(180deg, color-mix(in srgb, var(--primary) 7%, white) 0%, #fbfdfc 55%, color-mix(in srgb, var(--primary) 5%, white) 100%); }

        /* background decoration */
        .deco { position: fixed; pointer-events: none; z-index: 0; }
        .deco-blob { border-radius: 50%; background: color-mix(in srgb, var(--primary) 12%, white); }
        .deco-blob.b1 { width: 64px; height: 64px; left: 30px; top: 30px; opacity: .8; }
        .deco-blob.b2 { width: 120px; height: 120px; left: 22%; top: 50px; opacity: .45; }
        .deco-blob.b3 { width: 520px; height: 520px; right: -200px; top: -160px; opacity: .55; }
        .deco-wave { left: 0; right: 0; bottom: 0; width: 100%; height: 240px; }
        .deco-lamps { right: 6%; top: 60px; width: 220px; color: color-mix(in srgb, var(--primary) 30%, white); }

        .wrap { position: relative; z-index: 1; max-width: 1240px; margin: 0 auto; padding: 34px 20px 56px; }

        /* header */
        .head { text-align: center; position: relative; }
        .logo { display: inline-flex; align-items: center; gap: 10px; font-weight: 800; font-size: 2rem; letter-spacing: -.02em; color: var(--primary); }
        .logo svg { width: 58px; height: 58px; }
        .logo span { color: var(--orange); }
        .pill { display: inline-flex; align-items: center; gap: 8px; margin-top: 16px; padding: 9px 18px; border-radius: 999px; font-weight: 600; font-size: .95rem;
                background: color-mix(in srgb, var(--primary) 14%, white); color: color-mix(in srgb, var(--primary) 70%, black); }
        h1 { font-weight: 800; letter-spacing: -.03em; font-size: clamp(2.2rem, 4.6vw, 3.6rem); margin: 18px 0 10px; color: color-mix(in srgb, var(--primary) 30%, #0f1d16); }
        .sub { color: #2f3b34; font-size: 1.1rem; margin: 0 auto; max-width: 760px; }
        .bar { display: block; width: 90px; height: 5px; border-radius: 5px; background: var(--primary); margin: 18px auto 0; }
        .back-btn { position: absolute; left: 0; top: 4px; z-index: 2; display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 18px;
                    border-radius: 999px; font-weight: 600; font-size: .95rem; text-decoration: none; background: #fff; transition: .2s;
                    color: color-mix(in srgb, var(--primary) 70%, black); border: 1.5px solid color-mix(in srgb, var(--primary) 25%, white);
                    box-shadow: 0 6px 16px rgba(15, 40, 25, .06); }
        .back-btn:hover { background: color-mix(in srgb, var(--primary) 8%, white); transform: translateX(-2px); }

        .hero-chef { position: absolute; left: -70px; top: 74px; width: 270px; animation: bob 3.2s ease-in-out infinite; }
        .hero-plant { position: absolute; left: -120px; top: 200px; width: 100px; color: color-mix(in srgb, var(--primary) 80%, #22c55e); animation: sway 4s ease-in-out infinite; transform-origin: 50% 100%; }
        .hero-dish { position: absolute; right: -10px; top: 110px; width: 210px; animation: bob 3.6s ease-in-out infinite .4s; }
        .hero-dish .steam path { animation: steam 2.8s ease-in-out infinite; opacity: 0; }
        .hero-dish .steam path:nth-child(2) { animation-delay: .9s; } .hero-dish .steam path:nth-child(3) { animation-delay: 1.8s; }
        @keyframes bob   { 0%, 100% { transform: translateY(0) } 50% { transform: translateY(-8px) } }
        @keyframes sway  { 0%, 100% { transform: rotate(-4deg) } 50% { transform: rotate(4deg) } }
        @keyframes steam { 0% { transform: translateY(6px); opacity: 0 } 35% { opacity: .8 } 100% { transform: translateY(-22px); opacity: 0 } }

        @if (session('status'))
        .flash { margin: 18px auto 0; }
        @endif

        /* plans */
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 24px; margin-top: 64px; }
        .plan { position: relative; display: flex; flex-direction: column; align-items: center; text-align: center; background: #fff; border-radius: 26px; padding: 26px 24px 30px;
                box-shadow: 0 18px 44px rgba(15, 40, 25, .08); border: 2px solid transparent; overflow: hidden; transition: transform .25s, box-shadow .25s;
                opacity: 0; animation: rise .6s ease-out forwards; }
        .plan:nth-child(1) { animation-delay: .05s } .plan:nth-child(2) { animation-delay: .15s } .plan:nth-child(3) { animation-delay: .25s } .plan:nth-child(4) { animation-delay: .35s }
        @keyframes rise { from { opacity: 0; transform: translateY(22px) } to { opacity: 1; transform: none } }
        .plan:hover { transform: translateY(-6px); box-shadow: 0 26px 56px color-mix(in srgb, var(--primary) 16%, transparent); }
        .plan::after { content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 36px; pointer-events: none;
                       background: radial-gradient(70% 140% at 50% 120%, color-mix(in srgb, var(--primary) 14%, white) 0%, color-mix(in srgb, var(--primary) 6%, white) 45%, transparent 75%); }
        .plan.rec { border-color: color-mix(in srgb, var(--primary) 55%, white); overflow: visible; }
        .rec-tag { position: absolute; top: -20px; left: 50%; transform: translateX(-50%); display: inline-flex; align-items: center; gap: 7px; white-space: nowrap;
                   padding: 8px 20px; border-radius: 999px; color: #fff; font-weight: 700; font-size: .95rem;
                   background: linear-gradient(135deg, var(--primary), color-mix(in srgb, var(--primary) 75%, black)); box-shadow: 0 10px 22px color-mix(in srgb, var(--primary) 30%, transparent); }
        .plan-ico { width: 78px; height: 78px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.1rem; margin: 4px 0 16px; }
        .plan-name { font-weight: 800; font-size: 1.55rem; margin-bottom: 8px; }
        .plan-price { font-weight: 800; font-size: 2.3rem; letter-spacing: -.02em; color: color-mix(in srgb, var(--primary) 72%, black); line-height: 1.1; }
        .plan-price span { font-size: 1.1rem; font-weight: 500; color: var(--muted); }
        .plan-trial { color: var(--muted); font-size: .95rem; margin: 10px 0 0; min-height: 44px; }
        .plan hr { width: 100%; border-color: #eef1ef; opacity: 1; margin: 16px 0; }
        .plan-features { list-style: none; padding: 0; margin: 0 0 26px; align-self: stretch; text-align: left; flex: 1; }
        .plan-features li { display: flex; align-items: center; gap: 12px; padding: 7px 0; font-size: 1.02rem; }
        .plan-features i { font-size: 1.15rem; flex-shrink: 0; }
        .plan-features li.off { color: #a3aca7; }
        .plan-features li.off i { color: #cfd5d1; }
        .plan-cta { position: relative; z-index: 1; width: 100%; height: 52px; border-radius: 999px; font-weight: 700; font-size: 1.05rem; transition: .2s;
                    border: 2px solid color-mix(in srgb, var(--primary) 70%, black); color: color-mix(in srgb, var(--primary) 70%, black); background: #fff; }
        .plan-cta:hover { background: color-mix(in srgb, var(--primary) 8%, white); }
        .plan-cta.fill { border: 0; color: #fff; background: linear-gradient(135deg, var(--primary), color-mix(in srgb, var(--primary) 72%, black)); box-shadow: 0 12px 26px color-mix(in srgb, var(--primary) 30%, transparent); }
        .plan-cta.fill:hover { transform: translateY(-1px); filter: brightness(1.05); }

        @media (max-width: 1399px) { .hero-chef { left: -20px; width: 210px; top: 96px; } .hero-plant { display: none; } }
        @media (max-width: 1199px) { .hero-chef { width: 180px; top: 110px; } .hero-dish { width: 160px; } .hero-plant { display: none; } }
        @media (max-width: 1099px) { .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 38px; } }
        @media (max-width: 991px)  { .hero-chef, .hero-dish, .deco-lamps { display: none; } .back-btn { position: static; margin-bottom: 18px; } .head:has(.back-btn) { display: flex; flex-direction: column; align-items: center; } }
        @media (max-width: 640px)  { .grid { grid-template-columns: 1fr; } .logo { font-size: 1.6rem; } .logo svg { width: 46px; height: 46px; } .sub { font-size: 1rem; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } .plan { opacity: 1; } }
    </style>
</head>

<body>
    {{-- background --}}
    <span class="deco deco-blob b1"></span>
    <span class="deco deco-blob b2"></span>
    <span class="deco deco-blob b3"></span>
    <svg class="deco deco-lamps" viewBox="0 0 220 120" fill="currentColor" aria-hidden="true">
        <rect x="58" y="0" width="3" height="40"/><path d="M36 64a24 22 0 0 1 48 0z"/><rect x="34" y="62" width="52" height="5" rx="2.5"/>
        <rect x="158" y="0" width="3" height="60"/><path d="M136 84a24 22 0 0 1 48 0z"/><rect x="134" y="82" width="52" height="5" rx="2.5"/>
    </svg>
    <svg class="deco deco-wave" viewBox="0 0 1440 240" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 140C260 60 520 80 780 130s460 40 660-30V240H0z" style="fill: color-mix(in srgb, var(--primary) 7%, white)"/>
        <path d="M0 190C300 140 600 160 900 185s380 10 540-20V240H0z" style="fill: color-mix(in srgb, var(--primary) 11%, white)"/>
    </svg>

    <div class="wrap">
        <header class="head">
            @if ($changing)
                @php
                    // Back to where they came from (usually Balance & Subscription); dashboard if that was this page or another site.
                    $back = url()->previous();
                    if ($back === url()->current() || ! str_starts_with($back, url('/'))) $back = route('admin.dashboard.index');
                @endphp
                <a href="{{ $back }}" class="back-btn"><i class="bi bi-arrow-left"></i>Back</a>
            @endif
            {{-- chef + plant on the left, steaming dish on the right --}}
            <div class="hero-chef" aria-hidden="true">@include('restaurant::partials.chef-illustration', ['class' => 'w-100 h-auto'])</div>
            <svg class="hero-plant" viewBox="0 0 80 120" aria-hidden="true">
                <path d="M26 120h28l4-30H22z" fill="#f4efe6" stroke="#e1d7c4" stroke-width="2"/>
                <g fill="currentColor"><path d="M40 92V36" fill="none" stroke="currentColor" stroke-width="3"/>
                    <path d="M40 60c-20-2-30-16-30-28 18 0 30 10 30 28z"/><path d="M40 52c18-2 28-16 28-28-16 0-28 10-28 28z"/>
                    <path d="M40 80c16 0 26-8 28-18-16-2-26 6-28 18z"/><path d="M40 74c-14 0-24-8-26-18 14-2 24 6 26 18z"/></g>
            </svg>
            <svg class="hero-dish" viewBox="0 0 220 150" aria-hidden="true">
                <g class="steam" fill="none" stroke-width="5" stroke-linecap="round" style="stroke: color-mix(in srgb, var(--primary) 30%, white)">
                    <path d="M92 50c-8-9 8-13 0-24"/><path d="M112 44c-8-9 8-13 0-24"/><path d="M132 50c-8-9 8-13 0-24"/>
                </g>
                <ellipse cx="110" cy="110" rx="100" ry="30" fill="#e7ece9"/>
                <ellipse cx="110" cy="104" rx="96" ry="28" fill="#fff" stroke="#e1e7e4" stroke-width="2"/>
                <ellipse cx="110" cy="100" rx="66" ry="17" fill="#f3f6f4"/>
                <path d="M64 98c10-18 32-24 46-20 16-8 36-2 46 14-14 14-78 18-92 6z" fill="#c9672d"/>
                <path d="M76 92c8-10 22-12 32-8" fill="none" stroke="#e98a44" stroke-width="4" stroke-linecap="round"/>
                <g fill="#22c55e"><circle cx="86" cy="84" r="6"/><circle cx="128" cy="82" r="6"/><path d="M104 74c6-8 16-8 20-2-8 4-14 4-20 2z"/></g>
                <g fill="#ef4444"><circle cx="146" cy="90" r="5"/><circle cx="74" cy="94" r="4"/></g>
                <circle cx="112" cy="88" r="4" fill="#facc15"/>
            </svg>

            <div class="logo">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M13 26c-5 0-8-4-8-8 0-5 4-8 9-7 1-5 6-8 10-8s9 3 10 8c5-1 9 2 9 7 0 4-3 8-8 8"/>
                    <path d="M13 24v16h22V24"/><path d="M13 33h22"/><path d="M20 15v4M28 15v4"/>
                </svg>
                <div>Sajilo<span>Bhoj</span></div>
            </div>
            @php $credit = \Modules\Restaurant\Support\Billing::creditLimit(); @endphp
            @if ($changing)
                <div><span class="pill"><i class="bi bi-arrow-left-right"></i>Change your plan any time</span></div>
                <h1>Change Your Plan</h1>
                <p class="sub">
                    The new plan starts right away and is billed hourly from your prepaid balance.
                    @if ($credit > 0) You can run up to Nrs{{ number_format($credit, 0) }} on credit before you need to top up. @endif
                </p>
                @if ($current?->isTrialing())
                    <div class="alert alert-warning d-inline-block mt-4 mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Changing plan ends your free trial ({{ $current->trialDaysLeft() }} {{ \Illuminate\Support\Str::plural('day', $current->trialDaysLeft()) }} left). Billing starts immediately.
                    </div>
                @endif
            @else
                <div><span class="pill"><span aria-hidden="true">👑</span>Most plans include a 1-month free trial</span></div>
                <h1>Choose Your Plan</h1>
                <p class="sub">Pick the plan that fits your store. Most plans give you 30 days free before billing begins.</p>
            @endif
            <span class="bar"></span>

            @if (session('status'))
                <div class="alert alert-success d-inline-block mt-4 mb-0">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger d-inline-block mt-4 mb-0">{{ $errors->first() }}</div>
            @endif
        </header>

        <form action="{{ route('admin.subscription.store') }}" method="POST">
            @csrf
            <div class="grid">
                @foreach ($plans as $plan)
                    @php
                        $rec     = $plan->slug === \Modules\Restaurant\Models\Plan::RECOMMENDED;
                        $trial   = ! $changing && $plan->trial_days > 0;
                        $isCurrent = $current && $current->plan_id === $plan->id && ! $current->isExpired();
                        $tint    = "color-mix(in srgb, {$plan->color} 14%, white)";
                        $limit   = fn ($v) => $v === null ? 'Unlimited' : number_format($v);
                    @endphp
                    <div class="plan {{ $rec ? 'rec' : '' }}">
                        @if ($rec)<span class="rec-tag"><i class="bi bi-star-fill"></i>Recommended</span>@endif
                        <div class="plan-ico" style="background: {{ $tint }}; color: {{ $plan->color }};"><i class="bi {{ $plan->icon }}"></i></div>
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-price">Nrs{{ number_format($plan->monthly_price, 0) }}<span>/Month</span></div>
                        <div class="plan-trial">
                            @if ($trial)
                                First month free — then Nrs{{ $plan->hourlyRate() }}/hr
                            @elseif ($changing)
                                Nrs{{ $plan->hourlyRate() }}/hr from your prepaid balance
                            @else
                                No free trial — billing starts immediately
                            @endif
                        </div>
                        <hr>
                        @php $tick = $rec ? $plan->color : 'var(--primary)'; @endphp
                        <ul class="plan-features">
                            @foreach ([[$plan->item_limit, 'Menu items'], [$plan->staff_limit, 'Staff accounts'], [$plan->customer_limit, 'Customers']] as [$val, $label])
                                <li><i class="bi bi-check-circle-fill" style="color: {{ $tick }}"></i><span><strong>{{ $limit($val) }}</strong> {{ $label }}</span></li>
                            @endforeach
                            @foreach (\Modules\Restaurant\Models\Plan::EXTRA_FEATURES as $key => $label)
                                @if ($plan->hasFeature($key))
                                    <li><i class="bi bi-check-circle-fill" style="color: {{ $tick }}"></i>{{ $label }}</li>
                                @else
                                    <li class="off"><i class="bi bi-x-circle"></i>{{ $label }}</li>
                                @endif
                            @endforeach
                        </ul>
                        @if ($isCurrent)
                            <button type="button" class="plan-cta" disabled>Current Plan</button>
                        @else
                            <button type="submit" name="plan_id" value="{{ $plan->id }}" class="plan-cta {{ $rec ? 'fill' : '' }}"
                                @if ($changing) data-confirm-plan="{{ $plan->name }}" data-confirm-trial="{{ $current?->isTrialing() ? 1 : 0 }}" @endif>
                                {{ $changing ? 'Switch to this plan' : ($trial ? 'Start Free Trial' : 'Get Started') }}
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script>
    // Plan switch confirmation (SweetAlert2 instead of the browser's confirm box).
    document.querySelectorAll('[data-confirm-plan]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (btn.dataset.confirmed) return; // second pass: let the form submit
            e.preventDefault();
            var primary = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#198754',
                trial = btn.dataset.confirmTrial === '1';
            Swal.fire({
                icon: 'question',
                title: 'Switch to the ' + btn.dataset.confirmPlan + ' plan?',
                html: trial
                    ? 'Your <strong>free trial will end now</strong> and billing for the new plan starts immediately.'
                    : 'Your plan will change right away.',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-arrow-repeat me-1"></i> Yes, switch plan',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
                confirmButtonColor: primary,
                customClass: { popup: 'rounded-4' }
            }).then(function (result) {
                if (!result.isConfirmed) return;
                btn.dataset.confirmed = 1;
                Swal.fire({ title: 'Switching plan…', allowOutsideClick: false, showConfirmButton: false, didOpen: function () { Swal.showLoading(); } });
                btn.form.requestSubmit ? btn.form.requestSubmit(btn) : btn.click(); // keeps plan_id from the clicked button
            });
        });
    });
    </script>
</body>

</html>
