<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose your plan - RestoPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}?v={{ filemtime(public_path('assets/backend/css/style.css')) }}" rel="stylesheet">
    <style>
        :root { --primary: {{ $themeColor ?? '#198754' }}; }
        body{background:radial-gradient(circle at top left,var(--primary-soft) 0,#f8fbf9 36%,#eef4f0 100%);min-height:100vh;padding:48px 16px}
        .plan-wrap{max-width:1180px;margin:0 auto}
        .plan-pill{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:999px;background:var(--primary-soft);color:var(--primary-2);font-weight:600;font-size:.85rem}
        .plan-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:34px}
        @media(max-width:1100px){.plan-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:640px){.plan-grid{grid-template-columns:1fr}}
        .plan-card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:26px;display:flex;flex-direction:column;box-shadow:var(--shadow)}
        .plan-icon{width:54px;height:54px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;margin-bottom:16px}
        .plan-name{font-weight:700;font-size:1.15rem;margin-bottom:2px}
        .plan-price{font-size:1.6rem;font-weight:800;margin-bottom:4px}
        .plan-price span{font-size:.95rem;font-weight:500;color:var(--muted)}
        .plan-trial{font-size:.82rem;color:var(--muted);margin-bottom:18px}
        .plan-features{list-style:none;padding:0;margin:0 0 22px;flex:1}
        .plan-features li{display:flex;align-items:center;gap:8px;padding:6px 0;font-size:.92rem}
        .plan-features i{color:var(--primary);font-size:1rem}
        .plan-cta{border:none;border-radius:14px;padding:.75rem 1rem;font-weight:600;width:100%;color:#fff;background:#16181d}
        .plan-cta.enterprise{background:#dc3545}
    </style>
</head>

<body>
    <div class="plan-wrap text-center">
        <span class="plan-pill">🎁 Most plans include a 1-month free trial</span>
        <h1 class="fw-bold mt-3 mb-2">Choose Your Plan</h1>
        <p class="text-secondary mb-0">Pick the plan that fits your store. Most plans give you 30 days free before billing begins.</p>

        @if (session('status'))
            <div class="alert alert-success mt-4 d-inline-block">{{ session('status') }}</div>
        @endif

        <form action="{{ route('admin.subscription.store') }}" method="POST">
            @csrf
            <div class="plan-grid text-start">
                @foreach ($plans as $plan)
                    <div class="plan-card">
                        <div class="plan-icon" style="background:{{ $plan->color }}"><i class="bi {{ $plan->icon }}"></i></div>
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-price">Nrs{{ number_format($plan->monthly_price, 0) }}<span>/Month</span></div>
                        <div class="plan-trial">
                            @if ($plan->trial_days > 0)
                                First month free — then Nrs{{ $plan->hourlyRate() }}/hr
                            @else
                                No free trial — billing starts immediately
                            @endif
                        </div>
                        <ul class="plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> {{ $plan->item_limit ?? 'Unlimited' }} Items</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $plan->staff_limit ?? 'Unlimited' }} Staff</li>
                            <li><i class="bi bi-check-circle-fill"></i> {{ $plan->customer_limit ?? 'Unlimited' }} Customers</li>
                        </ul>
                        <button type="submit" name="plan_id" value="{{ $plan->id }}"
                                class="plan-cta {{ $plan->trial_days === 0 ? 'enterprise' : '' }}">
                            {{ $plan->trial_days > 0 ? 'Start Free Trial' : 'Get Started' }}
                        </button>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
</body>

</html>
