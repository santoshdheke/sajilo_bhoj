@extends('admin.theme_one.component.layout')

@section('content')
@php
    $rate     = $plan?->hourlyRate() ?? 0;
    $daily    = $rate * 24;
    $monthly  = (float) ($plan?->monthly_price ?? 0);
    $expired  = $sub?->isExpired();
    $trialing = $sub?->isTrialing();
    $presets  = $monthly > 0 ? [[$monthly, '1 month'], [$monthly * 3, '3 months'], [$monthly * 6, '6 months'], [$monthly * 12, '1 year']]
                             : [[500, 'Top up'], [1000, 'Top up'], [2500, 'Top up'], [5000, 'Top up']];
    $chosenGateway = old('gateway', array_key_first($gateways));
@endphp
<style>
.tp-grid      { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(0,1fr); gap:22px; align-items:start; }
.tp-balance   { position:relative; overflow:hidden; padding:26px 28px; border-radius:22px; color:#fff; min-height:150px;
                background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 88%, white), color-mix(in srgb, var(--primary) 62%, black));
                box-shadow:0 14px 30px color-mix(in srgb, var(--primary) 25%, transparent); }
.tp-balance.expired { background:linear-gradient(135deg, #e5484d, #8f1d2a); box-shadow:0 14px 30px rgba(220,53,69,.25); }
.tp-balance .lbl { opacity:.88; font-size:.95rem; }
.tp-balance .amt { font-size:2.5rem; font-weight:800; letter-spacing:-.02em; margin:2px 0 10px; }
.tp-balance .sub { opacity:.92; font-size:.88rem; display:flex; align-items:center; gap:8px; max-width:78%; }
.tp-balance .wallet { position:absolute; right:26px; top:50%; transform:translateY(-50%) rotate(-8deg); font-size:5.4rem; line-height:1;
                      color:rgba(255,255,255,.22); filter:drop-shadow(0 6px 10px rgba(0,0,0,.12)); }
.tp-facts     { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(0,1fr) minmax(0,1fr); gap:14px; margin-top:16px; }
.tp-fact > div { min-width:0; }
.tp-fact      { display:flex; align-items:center; gap:12px; background:#fff; border-radius:16px; padding:14px; box-shadow:0 6px 18px rgba(15,40,25,.05); }
.tp-ic        { width:46px; height:46px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.2rem;
                color:var(--primary); background:color-mix(in srgb, var(--primary) 12%, white); }
.tp-fact small { display:block; color:var(--muted); font-size:.78rem; }
.tp-fact b    { color:#14201a; font-size:.98rem; }
.tp-fact a    { color:var(--primary); font-weight:600; font-size:.85rem; text-decoration:none; margin-left:6px; white-space:nowrap; display:inline-block; }
.tp-card      { background:#fff; border-radius:20px; box-shadow:0 8px 24px rgba(15,40,25,.06); }
.tp-card-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:18px 22px; border-bottom:1px solid rgba(24,52,34,.07); }
.tp-card-head h5 { margin:0; font-weight:800; display:flex; align-items:center; gap:10px; }
.tp-card-head h5 i { color:var(--primary); font-size:1.3rem; }
.tp-card-head a { color:var(--primary); font-weight:700; font-size:.9rem; text-decoration:none; }
.tp-card-body { padding:6px 22px 18px; }
.tp-tx        { display:flex; align-items:center; gap:14px; padding:14px 0; border-bottom:1px solid rgba(24,52,34,.07); }
.tp-tx:last-child { border-bottom:0; }
.tp-tx .ic    { width:42px; height:42px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
.tp-tx .ic.topup  { background:color-mix(in srgb, var(--primary) 12%, white); color:var(--primary); }
.tp-tx .ic.charge { background:#f2f4f3; color:#6b7280; }
.tp-tx .t     { font-weight:700; color:#14201a; font-size:.95rem; }
.tp-tx .m     { color:var(--muted); font-size:.8rem; }
.tp-tx .amt   { margin-left:auto; font-weight:800; white-space:nowrap; }
.tp-tx .amt.topup  { color:var(--primary); }
.tp-tx .amt.charge { color:#475569; }
.tp-presets   { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin:16px 0; }
.tp-preset    { border:1.5px solid rgba(24,52,34,.1); background:#fff; border-radius:14px; padding:12px 16px; text-align:left; transition:.15s; }
.tp-preset:hover, .tp-preset.on { border-color:var(--primary); background:color-mix(in srgb, var(--primary) 5%, white); }
.tp-preset b  { display:block; font-size:1.08rem; color:#14201a; }
.tp-preset small { color:var(--muted); font-size:.88rem; }
.tp-gw        { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
.tp-gw label  { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:14px; border:1.5px solid rgba(24,52,34,.1); cursor:pointer; margin:0; transition:.15s; }
.tp-gw input  { accent-color:var(--primary); width:17px; height:17px; }
.tp-gw label:has(input:checked) { border-color:var(--primary); background:color-mix(in srgb, var(--primary) 5%, white); }
.tp-gw .logo  { font-weight:800; font-size:1.05rem; letter-spacing:-.01em; }
.tp-gw .logo.khalti { color:#5c2d91; }
.tp-gw .logo.esewa  { color:#41a124; }
.tp-gw .logo.esewa span { color:#1d1d1d; }
.tp-note      { display:flex; align-items:flex-start; gap:8px; color:var(--muted); font-size:.84rem; margin-top:8px; }
.tp-btn       { width:100%; border:0; border-radius:16px; padding:15px; font-weight:700; font-size:1.02rem; color:#fff; margin-top:18px;
                background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 90%, white), color-mix(in srgb, var(--primary) 75%, black));
                box-shadow:0 10px 22px color-mix(in srgb, var(--primary) 28%, transparent); display:flex; align-items:center; justify-content:center; gap:10px; }
.tp-btn:hover { filter:brightness(1.05); }
.tp-btn:disabled { opacity:.6; }
@media (max-width:991px) { .tp-grid { grid-template-columns:1fr; } }
@media (max-width:575px) {
    .tp-facts { grid-template-columns:1fr; }
    .tp-balance .wallet { font-size:4rem; right:16px; }
    .tp-balance .sub { max-width:100%; }
    .tp-balance .amt { font-size:2rem; }
}
</style>

<div class="page-title mb-4">
    <h2>Balance &amp; Top-up</h2>
    <p>Your plan runs on a prepaid balance after the free trial. Top up with Khalti or eSewa.</p>
</div>

@if ($expired)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="border-radius:14px;">
    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
    <div><strong>Your subscription has run out.</strong> Your admin panel and website are paused. Top up below to start again right away.</div>
</div>
@endif

<div class="tp-grid">
    <div>
        <div class="tp-balance {{ $expired ? 'expired' : '' }}">
            <i class="bi bi-wallet2 wallet" aria-hidden="true"></i>
            <div class="lbl">Current Balance</div>
            <div class="amt">Nrs {{ number_format((float) $company->balance, 2) }}</div>
            <div class="sub">
                <i class="bi bi-clock"></i>
                <span>
                @if ($trialing)
                    Free trial · {{ $sub->trialDaysLeft() }} days left until {{ $sub->trial_ends_at->format('d M Y') }}. Your balance is used after that.
                @elseif ($expired)
                    Not enough balance to keep running.
                @elseif ($runway !== null)
                    Lasts about {{ $runway >= 1 ? number_format(floor($runway)) . ' ' . \Illuminate\Support\Str::plural('day', (int) floor($runway)) : number_format($runway * 24, 0) . ' hours' }} on your plan.
                @endif
                </span>
            </div>
        </div>

        <div class="tp-facts">
            <div class="tp-fact"><span class="tp-ic"><i class="bi bi-star"></i></span>
                <div><small>Plan</small><b>{{ $plan?->name ?? '—' }}</b><a href="{{ route('admin.subscription.choose') }}">Change</a></div></div>
            <div class="tp-fact"><span class="tp-ic"><i class="bi bi-calendar3"></i></span>
                <div><small>Per Day</small><b>Nrs {{ number_format($daily, 2) }}</b></div></div>
            <div class="tp-fact"><span class="tp-ic"><i class="bi bi-calendar-month"></i></span>
                <div><small>Per Month</small><b>Nrs {{ number_format($monthly, 0) }}</b></div></div>
        </div>

        <div class="tp-card mt-4">
            <div class="tp-card-head">
                <h5><i class="bi bi-clock-history"></i>History</h5>
                @if ($showAll)
                    <a href="{{ route('admin.subscription.topup') }}"><i class="bi bi-arrow-left"></i> Back</a>
                @elseif ($transactions->count() >= 5)
                    <a href="{{ route('admin.subscription.topup', ['all' => 1]) }}">View All <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>
            <div class="tp-card-body">
                @forelse ($transactions as $tx)
                <div class="tp-tx">
                    <span class="ic {{ $tx->type }}"><i class="bi {{ $tx->type === 'topup' ? 'bi-plus-lg' : 'bi-dash-lg' }}"></i></span>
                    <div style="min-width:0;">
                        <div class="t">{{ $tx->type === 'topup' ? 'Top-up' : 'Plan charge' }}</div>
                        <div class="m">{{ $tx->created_at->format('d M Y, h:i A') }} · {{ $tx->note }} · balance: Nrs {{ number_format((float) $tx->balance_after, 2) }}</div>
                    </div>
                    <div class="amt {{ $tx->type }}">{{ $tx->type === 'topup' ? '+' : '−' }}Nrs {{ number_format((float) $tx->amount, 2) }}</div>
                </div>
                @empty
                <p class="text-muted text-center py-4 mb-0"><i class="bi bi-receipt d-block fs-3 opacity-50 mb-1"></i>No top-ups or charges yet.</p>
                @endforelse
                @if ($showAll && method_exists($transactions, 'links'))
                    <div class="pt-3">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="tp-card">
        <div class="tp-card-head"><h5><i class="bi bi-plus-circle-fill"></i>Add balance</h5></div>
        <div class="tp-card-body">
            @if ($gateways)
            <form method="POST" action="{{ route('admin.subscription.topup.store') }}" id="tpForm">
                @csrf
                <div class="tp-presets">
                    @foreach ($presets as [$amt, $label])
                    <button type="button" class="tp-preset" data-amount="{{ round($amt) }}">
                        <b>Nrs {{ number_format($amt, 0) }}</b><small>{{ $label }}</small>
                    </button>
                    @endforeach
                </div>

                <label class="form-label fw-semibold" for="tpAmount">Amount (Nrs)</label>
                <input type="number" class="form-control @error('amount') is-invalid @enderror" id="tpAmount" name="amount"
                       min="{{ $min }}" max="{{ $max }}" step="1" value="{{ old('amount', round($monthly ?: 1000)) }}" required>
                @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="tp-note"><i class="bi bi-info-circle"></i>Minimum: Nrs {{ number_format($min) }} and Maximum: Nrs {{ number_format($max) }}.</div>

                <label class="form-label fw-semibold mt-3">Pay with</label>
                <div class="tp-gw">
                    @foreach ($gateways as $key => $label)
                    <label>
                        <input type="radio" name="gateway" value="{{ $key }}" {{ $chosenGateway === $key ? 'checked' : '' }}>
                        @if ($key === 'esewa')<span class="logo esewa">e<span>Sewa</span></span>@else<span class="logo khalti">khalti</span>@endif
                    </label>
                    @endforeach
                </div>
                @error('gateway')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <button class="tp-btn" type="submit" id="tpBtn"><i class="bi bi-credit-card-2-front"></i><span>Top up now</span></button>
                <div class="tp-note"><i class="bi bi-shield-check"></i>You pay securely on Khalti or eSewa. Your balance updates as soon as the payment is confirmed.</div>
                @if (config('billing.khalti.mode') === 'test' || config('billing.esewa.mode') === 'test')
                    <div class="tp-note"><i class="bi bi-info-circle"></i>Test mode: payments use the Khalti / eSewa sandbox, no real money is taken.</div>
                @endif
            </form>
            @else
            <div class="text-center py-4">
                <i class="bi bi-credit-card fs-1" style="color:var(--primary)"></i>
                <h6 class="fw-bold mt-2">Online top-up isn’t set up yet</h6>
                <p class="text-muted small mb-0">Khalti and eSewa keys need to be added by the SajiloBhoj team.</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
document.querySelectorAll('.tp-preset').forEach(function (b) {
    b.addEventListener('click', function () {
        document.querySelectorAll('.tp-preset').forEach(function (x) { x.classList.toggle('on', x === b); });
        document.getElementById('tpAmount').value = b.dataset.amount;
    });
});
document.getElementById('tpForm')?.addEventListener('submit', function () {
    var btn = document.getElementById('tpBtn');
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Taking you to the payment page…';
});
</script>
@endpush
@endsection
