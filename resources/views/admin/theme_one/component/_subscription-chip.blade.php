{{-- Header chip beside the bell: trial days left, or remaining balance (red when it has run out). Links to Balance & Top-up. --}}
@php
    $chipCompany = \App\Support\Tenancy::company();
    $chipSub     = $chipCompany?->currentSubscription();
@endphp
@if ($chipSub)
@php
    $chipRunway = \App\Support\Billing::runwayDays($chipCompany, $chipSub->plan);
    if ($chipSub->isTrialing()) {
        $chipDays  = $chipSub->trialDaysLeft();
        $chipState = $chipDays <= 5 ? 'warn' : 'trial';
        $chipIcon  = 'bi-gift';
        $chipText  = 'Trial · ' . $chipDays . ' ' . \Illuminate\Support\Str::plural('day', $chipDays) . ' left';
    } elseif ($chipSub->isExpired()) {
        $chipState = 'out'; $chipIcon = 'bi-exclamation-circle-fill'; $chipText = 'Top up to continue';
    } else {
        $chipState = $chipRunway !== null && $chipRunway < 5 ? 'warn' : 'ok';
        $chipIcon  = 'bi-wallet2';
        $chipText  = 'Nrs ' . number_format((float) $chipCompany->balance, 0)
                   . ($chipRunway !== null ? ' · ~' . ($chipRunway >= 1 ? floor($chipRunway) . 'd' : max(1, floor($chipRunway * 24)) . 'h') . ' left' : '');
    }
@endphp
<a href="{{ route('admin.subscription.topup') }}" class="sub-chip {{ $chipState }}" title="{{ $chipSub->plan?->name }} plan — balance &amp; top-up">
    <i class="bi {{ $chipIcon }}"></i><span>{{ $chipText }}</span>
</a>
@once
<style>
    .sub-chip { display:inline-flex; align-items:center; gap:7px; height:38px; padding:0 14px; margin-right:4px; border-radius:999px; text-decoration:none;
                font-size:.84rem; font-weight:700; white-space:nowrap; transition:.15s;
                background:color-mix(in srgb, var(--primary) 10%, white); color:color-mix(in srgb, var(--primary) 75%, black); }
    .sub-chip:hover { background:color-mix(in srgb, var(--primary) 18%, white); }
    .sub-chip.warn { background:#fff4dc; color:#8a5a00; }
    .sub-chip.out  { background:#fdecee; color:#b42335; }
    @media (max-width:575px) { .sub-chip { height:34px; padding:0 10px; font-size:.76rem; } .sub-chip span { max-width:118px; overflow:hidden; text-overflow:ellipsis; } }
</style>
@endonce
@endif
