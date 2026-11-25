@extends('restaurant::admin.theme_one.component.layout')

@section('content')
@php
    $pct = rtrim(rtrim(number_format($percent, 2), '0'), '.');
@endphp
@include('restaurant::admin.theme_one.subscription._styles')
<style>
.rf-avatar    { width:42px; height:42px; border-radius:12px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:800;
                color:var(--primary); background:color-mix(in srgb, var(--primary) 12%, white); }
.rf-domain    { color:var(--primary); text-decoration:none; font-weight:600; }
.rf-domain:hover { text-decoration:underline; }
.rf-earned    { margin-left:auto; text-align:right; white-space:nowrap; }
.rf-earned b  { display:block; color:#b7791f; font-weight:800; }
.rf-earned small { color:var(--muted); font-size:.76rem; }
.rf-steps     { list-style:none; padding:0; margin:0; display:grid; gap:12px; }
.rf-steps li  { display:flex; gap:12px; align-items:flex-start; font-size:.88rem; color:#334155; }
.rf-steps .n  { width:26px; height:26px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:800;
                color:var(--primary); background:color-mix(in srgb, var(--primary) 12%, white); }
.rf-color     { display:flex; align-items:center; gap:10px; }
.rf-color input[type=color] { width:46px; height:38px; padding:3px; border-radius:10px; }
.rf-back      { color:var(--primary); font-weight:700; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:10px; }
</style>

<a href="{{ route('admin.subscription.topup') }}" class="rf-back"><i class="bi bi-arrow-left"></i> Balance &amp; Subscription</a>
<div class="page-title mb-4">
    <h2>Refer &amp; Earn</h2>
    <p>Set up SajiloBhoj for other restaurants as your clients. Every time a client tops up, you get {{ $pct }}% of it added to your own balance — automatically.</p>
</div>

<div class="tp-grid">
    <div>
        <div class="tp-balance">
            <i class="bi bi-gift wallet" aria-hidden="true"></i>
            <div class="lbl">Bonus Earned</div>
            <div class="amt">Nrs {{ number_format($totalEarned, 2) }}</div>
            <div class="sub"><i class="bi bi-stars"></i><span>{{ $pct }}% of every client top-up goes into your balance and pays for your plan.</span></div>
        </div>

        <div class="tp-facts">
            <div class="tp-fact"><span class="tp-ic"><i class="bi bi-shop"></i></span>
                <div><small>Clients</small><b>{{ number_format($clientCount) }}</b></div></div>
            <div class="tp-fact"><span class="tp-ic"><i class="bi bi-wallet2"></i></span>
                <div><small>Paying</small><b>{{ number_format($payingCount) }}</b></div></div>
            <div class="tp-fact"><span class="tp-ic"><i class="bi bi-percent"></i></span>
                <div><small>Your Bonus</small><b>{{ $pct }}%</b></div></div>
        </div>

        <div class="tp-card mt-4">
            <div class="tp-card-head">
                <h5><i class="bi bi-shop-window"></i>My Client Restaurants</h5>
            </div>
            <div class="tp-card-body">
                @forelse ($clients as $client)
                @php
                    $domain  = $client->domains->first()?->domain;
                    $csub    = $client->currentSubscription();
                    [$stCls, $stLabel] = match (true) {
                        ! $csub                     => ['new', 'No plan yet'],
                        $csub->isTrialing()         => ['trial', 'Free trial'],
                        $csub->status === 'active'  => ['active', 'Active'],
                        default                     => ['paused', 'Paused'],
                    };
                    $row = $earned->get($client->id);
                @endphp
                <div class="tp-tx">
                    <span class="rf-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($client->name, 0, 1)) }}</span>
                    <div style="min-width:0;">
                        <div class="t d-flex align-items-center gap-2 flex-wrap">{{ $client->name }}
                            <span class="tp-status {{ $stCls }}">{{ $stLabel }}{{ $csub?->plan && $stCls !== 'new' ? ' · ' . $csub->plan->name : '' }}</span></div>
                        <div class="m">
                            @if ($domain)<a class="rf-domain" href="{{ request()->getScheme() }}://{{ $domain }}" target="_blank" rel="noopener">{{ $domain }}</a> · @endif
                            Added {{ $client->created_at->format('d M Y') }}
                        </div>
                    </div>
                    <div class="rf-earned">
                        <b>+Nrs {{ number_format((float) ($row->total ?? 0), 2) }}</b>
                        <small>{{ $row ? $row->payments . ' ' . \Illuminate\Support\Str::plural('top-up', (int) $row->payments) : 'No top-ups yet' }}</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-4 mb-0"><i class="bi bi-shop d-block fs-3 opacity-50 mb-1"></i>No clients yet. Add your first client restaurant with the form.</p>
                @endforelse
                @if ($clients->hasPages())
                    <div class="pt-3">{{ $clients->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div>
        <div class="tp-card">
            <div class="tp-card-head"><h5><i class="bi bi-plus-circle-fill"></i>Add Client Restaurant</h5></div>
            <div class="tp-card-body pt-3">
                <form method="POST" action="{{ route('admin.subscription.referrals.store') }}" id="rfForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="rfName">Restaurant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="rfName" name="company_name" maxlength="255"
                               value="{{ old('company_name') }}" placeholder="e.g. Himalayan Momo House" required>
                        @error('company_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="rfDomain">Web Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('domain') is-invalid @enderror" id="rfDomain" name="domain" maxlength="253"
                               value="{{ old('domain') }}" placeholder="{{ $domainSuffix ? 'momohouse' . $domainSuffix : 'momohouse.com' }}" autocapitalize="off" spellcheck="false" required>
                        @error('domain')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <div class="tp-note"><i class="bi bi-globe2"></i>The client's website and admin panel open on this address. It must point to SajiloBhoj.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="rfColor">Brand Colour</label>
                        <div class="rf-color">
                            <input type="color" class="form-control form-control-color" id="rfColor" name="theme_color" value="{{ old('theme_color', '#198754') }}">
                            <small class="text-muted">The client can change it later in their settings.</small>
                        </div>
                        @error('theme_color')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="rfOwner">Owner Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="rfOwner" name="owner_name" maxlength="255" value="{{ old('owner_name') }}" required>
                            @error('owner_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="rfEmail">Owner Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('owner_email') is-invalid @enderror" id="rfEmail" name="owner_email" maxlength="255" value="{{ old('owner_email') }}" required>
                            @error('owner_email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="rfPassword">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="rfPassword" name="password" minlength="8" placeholder="Min 8 characters" autocomplete="new-password" required>
                            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="rfPassword2">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="rfPassword2" name="password_confirmation" minlength="8" autocomplete="new-password" required>
                        </div>
                    </div>

                    <button class="tp-btn" type="submit" id="rfBtn"><i class="bi bi-shop"></i><span>Create Client Restaurant</span></button>
                    <div class="tp-note"><i class="bi bi-shield-check"></i>The owner can sign in right away with this email and password. Share them privately.</div>
                </form>
            </div>
        </div>

        <div class="tp-card mt-4">
            <div class="tp-card-head"><h5><i class="bi bi-lightbulb"></i>How it works</h5></div>
            <div class="tp-card-body pt-3">
                <ol class="rf-steps">
                    <li><span class="n">1</span><span>Create the client's restaurant here. It comes with a ready website, menu, tables and kitchen.</span></li>
                    <li><span class="n">2</span><span>The owner signs in, picks a plan and starts with the free trial.</span></li>
                    <li><span class="n">3</span><span>Each time they top up with Khalti or eSewa, {{ $pct }}% of the amount is added to your balance as a referral bonus.</span></li>
                </ol>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.getElementById('rfForm')?.addEventListener('submit', function () {
    var btn = document.getElementById('rfBtn');
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Setting up the restaurant…';
});
</script>
@endpush
@endsection
