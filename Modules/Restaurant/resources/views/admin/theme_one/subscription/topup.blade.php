@extends('restaurant::admin.theme_one.component.layout')

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
@include('restaurant::admin.theme_one.subscription._styles')

<div class="page-title mb-4">
    <h2>Balance &amp; Subscription</h2>
    <p>Your plan runs on a prepaid balance after the free trial. Top up with Khalti or eSewa.
        @if (($credit = \Modules\Restaurant\Support\Billing::creditLimit()) > 0) If your balance runs out you get Nrs {{ number_format($credit, 0) }} credit, so the balance can go down to Nrs -{{ number_format($credit, 0) }} before your plan pauses. @endif</p>
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
                    Balance and credit are used up. Top up to keep running.
                @elseif ((float) $company->balance < 0)
                    Using credit · {{ $runway !== null ? 'about ' . ($runway >= 1 ? number_format(floor($runway)) . ' ' . \Illuminate\Support\Str::plural('day', (int) floor($runway)) : number_format($runway * 24, 0) . ' hours') . ' left. ' : '' }}Top up soon.
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
                @php [$txIcon, $txTitle] = ['topup' => ['bi-plus-lg', 'Top-up'], 'bonus' => ['bi-gift', 'Referral earnings']][$tx->type] ?? ['bi-dash-lg', 'Plan charge']; @endphp
                <div class="tp-tx">
                    <span class="ic {{ $tx->type }}"><i class="bi {{ $txIcon }}"></i></span>
                    <div style="min-width:0;">
                        <div class="t">{{ $txTitle }}</div>
                        <div class="m">{{ $tx->created_at->format('d M Y, h:i A') }} · {{ $tx->note }} · balance: Nrs {{ number_format((float) $tx->balance_after, 2) }}</div>
                    </div>
                    <div class="amt {{ $tx->type }}">{{ $tx->type === 'charge' ? '−' : '+' }}Nrs {{ number_format((float) $tx->amount, 2) }}</div>
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

    <div>
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
                @if (config('restaurant.billing.khalti.mode') === 'test' || config('restaurant.billing.esewa.mode') === 'test')
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

    @if (($referralPercent = \Modules\Restaurant\Support\Billing::referralPercent()) > 0)
    <a href="{{ route('admin.subscription.referrals.index') }}" class="tp-promo mt-4">
        <span class="tp-promo-ic"><i class="bi bi-gift"></i></span>
        <span class="tp-promo-text">
            <b>Refer &amp; Earn</b>
            <small>Add client restaurants and earn {{ rtrim(rtrim(number_format($referralPercent, 2), '0'), '.') }}% of every top-up they make — add it to your balance or withdraw it.</small>
        </span>
        <span class="tp-promo-go">My clients <i class="bi bi-arrow-right"></i></span>
    </a>
    @endif
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
