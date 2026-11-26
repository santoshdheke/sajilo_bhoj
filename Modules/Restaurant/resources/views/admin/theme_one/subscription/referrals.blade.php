@extends('restaurant::admin.theme_one.component.layout')

@section('content')
@php
    $pct       = rtrim(rtrim(number_format($percent, 2), '0'), '.');
    $available = (float) $company->referral_earnings;
    $canWithdraw = $available >= $minWithdraw;
    $presets   = ['#198754', '#22c55e', '#f59e0b', '#2563eb', '#ec4899', '#8b5cf6', '#1e3a8a', '#64748b'];
    $pickColor = strtolower(old('theme_color', $ownColor));
    $txInfo    = [
        'bonus'      => ['bi-gift', 'Referral bonus', '+', 'in'],
        'to_balance' => ['bi-wallet2', 'Added to plan balance', '−', 'out'],
        'withdrawal' => ['bi-bank', 'Withdrawal', '−', 'out'],
        'refund'     => ['bi-arrow-counterclockwise', 'Withdrawal returned', '+', 'in'],
    ];
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
.rf-actions   { display:flex; flex-wrap:wrap; gap:10px; margin-top:16px; position:relative; }
.rf-act       { border:0; border-radius:12px; padding:9px 16px; font-weight:700; font-size:.9rem; display:inline-flex; align-items:center; gap:8px; transition:.15s; }
.rf-act.solid { background:#fff; color:color-mix(in srgb, var(--primary) 80%, black); }
.rf-act.ghost { background:rgba(255,255,255,.16); color:#fff; border:1px solid rgba(255,255,255,.45); }
.rf-act:hover:not(:disabled) { transform:translateY(-1px); }
.rf-act:disabled { opacity:.55; cursor:not-allowed; }
.rf-totals    { display:flex; flex-wrap:wrap; gap:6px 16px; font-size:.84rem; opacity:.92; position:relative; }
.tp-tx .ic.in  { background:#fff4dc; color:#b7791f; }
.tp-tx .ic.out { background:#f2f4f3; color:#6b7280; }
.tp-tx .amt.in  { color:#b7791f; }
.tp-tx .amt.out { color:#475569; }
.rf-pill      { font-size:.7rem; font-weight:700; padding:2px 8px; border-radius:999px; margin-left:6px; vertical-align:middle; }
.rf-pill.pending  { background:#fff4dc; color:#9a6700; }
.rf-pill.paid     { background:color-mix(in srgb, var(--primary) 12%, white); color:var(--primary); }
.rf-pill.rejected { background:#fff0f0; color:#dc3545; }
/* Brand colour picker — same as the sign-up page; the ring shows the colour being picked. */
.ws-colors    { --pick:#198754; display:flex; align-items:center; flex-wrap:wrap; gap:12px; }
.ws-color     { width:38px; height:38px; border-radius:50%; border:0; padding:0; position:relative; cursor:pointer; transition:transform .15s; box-shadow:0 4px 10px rgba(0,0,0,.12); }
.ws-color:hover { transform:scale(1.1); }
.ws-color.active { box-shadow:0 0 0 3px #fff, 0 0 0 5px var(--pick); }
.ws-color.active::after { content:'\F26E'; font-family:bootstrap-icons; color:#fff; font-size:1.15rem; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }
.ws-color-custom { background:conic-gradient(#ef4444, #f59e0b, #22c55e, #06b6d4, #3b82f6, #8b5cf6, #ec4899, #ef4444); overflow:hidden; }
.ws-color-custom input { position:absolute; inset:0; opacity:0; cursor:pointer; }
.rf-back      { color:var(--primary); font-weight:700; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:10px; }
</style>

<a href="{{ route('admin.subscription.topup') }}" class="rf-back"><i class="bi bi-arrow-left"></i> Balance &amp; Subscription</a>
<div class="page-title mb-4">
    <h2>Refer &amp; Earn</h2>
    <p>Set up SajiloBhoj for other restaurants as your clients. Every time a client tops up, you earn {{ $pct }}% of it — add it to your plan balance or withdraw it.</p>
</div>

<div class="tp-grid">
    <div>
        <div class="tp-balance">
            <i class="bi bi-gift wallet" aria-hidden="true"></i>
            <div class="lbl">Available Earnings</div>
            <div class="amt">Nrs {{ number_format($available, 2) }}</div>
            <div class="rf-totals">
                <span><i class="bi bi-stars"></i> Total earned: Nrs {{ number_format($totalEarned, 2) }}</span>
                <span>Withdrawn: Nrs {{ number_format($withdrawn, 2) }}</span>
                @if ($pendingOut > 0)<span>Being paid: Nrs {{ number_format($pendingOut, 2) }}</span>@endif
            </div>
            <div class="rf-actions">
                <button type="button" class="rf-act solid" data-bs-toggle="modal" data-bs-target="#toBalanceModal" @disabled($available <= 0)>
                    <i class="bi bi-wallet2"></i>Add to plan balance</button>
                <button type="button" class="rf-act ghost" data-bs-toggle="modal" data-bs-target="#withdrawModal" @disabled(! $canWithdraw)
                        @unless($canWithdraw) title="You can withdraw from Nrs {{ number_format($minWithdraw) }}" @endunless>
                    <i class="bi bi-bank"></i>Withdraw</button>
            </div>
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

        <div class="tp-card mt-4">
            <div class="tp-card-head"><h5><i class="bi bi-clock-history"></i>Earnings History</h5></div>
            <div class="tp-card-body">
                @forelse ($history as $tx)
                @php [$icon, $title, $sign, $dir] = $txInfo[$tx->type] ?? ['bi-dot', ucfirst($tx->type), '', 'out']; @endphp
                <div class="tp-tx">
                    <span class="ic {{ $dir }}"><i class="bi {{ $icon }}"></i></span>
                    <div style="min-width:0;">
                        <div class="t">{{ $title }}
                            @if ($tx->type === 'withdrawal' && $tx->withdrawal)
                                <span class="rf-pill {{ $tx->withdrawal->status }}">{{ ['pending' => 'Being paid', 'paid' => 'Paid', 'rejected' => 'Returned'][$tx->withdrawal->status] ?? $tx->withdrawal->status }}</span>
                            @endif
                        </div>
                        <div class="m">{{ $tx->created_at->format('d M Y, h:i A') }} · {{ $tx->note }}@if ($tx->withdrawal?->payout_reference) · Ref {{ $tx->withdrawal->payout_reference }}@endif</div>
                    </div>
                    <div class="amt {{ $dir }}">{{ $sign }}Nrs {{ number_format((float) $tx->amount, 2) }}</div>
                </div>
                @empty
                <p class="text-muted text-center py-4 mb-0"><i class="bi bi-gift d-block fs-3 opacity-50 mb-1"></i>Your referral bonuses will show here.</p>
                @endforelse
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
                        <label class="form-label fw-semibold d-block">Brand Colour</label>
                        <input type="hidden" name="theme_color" id="rfColor" value="{{ $pickColor }}">
                        <div class="ws-colors" id="rfColors" role="radiogroup" aria-label="Brand colour">
                            @foreach (array_unique(array_merge([strtolower($ownColor)], $presets)) as $p)
                                <button type="button" class="ws-color" data-color="{{ $p }}" style="background: {{ $p }}" role="radio" aria-label="{{ $p }}"
                                        @if ($p === strtolower($ownColor)) title="Your brand colour" @endif></button>
                            @endforeach
                            <label class="ws-color ws-color-custom" title="Custom colour" aria-label="Custom colour">
                                <input type="color" id="rfColorCustom" value="{{ $pickColor }}">
                            </label>
                        </div>
                        <div class="tp-note"><i class="bi bi-palette"></i>Starts with your own brand colour. The client can change it later in their settings.</div>
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
                    <li><span class="n">3</span><span>Each time they top up with Khalti or eSewa, you earn {{ $pct }}% of the amount.</span></li>
                    <li><span class="n">4</span><span>Add your earnings to your plan balance any time, or withdraw them to eSewa, Khalti or your bank (from Nrs {{ number_format($minWithdraw) }}).</span></li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ── Add earnings to plan balance ── --}}
<div class="modal fade" id="toBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.subscription.referrals.to_balance') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i>Add to Plan Balance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Moves earnings into your plan balance right away. It pays for your plan and starts it again if it was paused.</p>
                <label class="form-label fw-semibold" for="tbAmount">Amount (Nrs)</label>
                <input type="number" class="form-control @error('amount', 'toBalance') is-invalid @enderror" id="tbAmount" name="amount"
                       min="1" max="{{ $available }}" step="0.01" value="{{ old('amount', $available) }}" required>
                @error('amount', 'toBalance')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="tp-note"><i class="bi bi-info-circle"></i>Available: Nrs {{ number_format($available, 2) }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add to balance</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Withdraw earnings ── --}}
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.subscription.referrals.withdraw') }}" id="wdForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bank me-2"></i>Withdraw Earnings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold" for="wdAmount">Amount (Nrs)</label>
                <input type="number" class="form-control @error('amount', 'withdraw') is-invalid @enderror" id="wdAmount" name="amount"
                       min="{{ $minWithdraw }}" max="{{ $available }}" step="0.01" value="{{ old('amount', $available) }}" required>
                @error('amount', 'withdraw')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="tp-note"><i class="bi bi-info-circle"></i>Available: Nrs {{ number_format($available, 2) }} · minimum Nrs {{ number_format($minWithdraw) }}</div>

                <label class="form-label fw-semibold mt-3">Send to</label>
                <div class="tp-gw" style="grid-template-columns:repeat(3,minmax(0,1fr));">
                    @foreach ($methods as $key => $label)
                    <label>
                        <input type="radio" name="method" value="{{ $key }}" {{ old('method', 'esewa') === $key ? 'checked' : '' }}>
                        @if ($key === 'esewa')<span class="logo esewa">e<span>Sewa</span></span>
                        @elseif ($key === 'khalti')<span class="logo khalti">khalti</span>
                        @else<span class="logo"><i class="bi bi-bank"></i> Bank</span>@endif
                    </label>
                    @endforeach
                </div>
                @error('method', 'withdraw')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <div class="mb-3 d-none" id="wdBankWrap">
                    <label class="form-label fw-semibold" for="wdBank">Bank Name</label>
                    <input type="text" class="form-control @error('bank_name', 'withdraw') is-invalid @enderror" id="wdBank" name="bank_name" maxlength="120" value="{{ old('bank_name') }}" placeholder="e.g. Nabil Bank">
                    @error('bank_name', 'withdraw')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold" for="wdName">Account Holder Name</label>
                        <input type="text" class="form-control @error('account_name', 'withdraw') is-invalid @enderror" id="wdName" name="account_name" maxlength="120" value="{{ old('account_name', auth()->user()->name) }}" required>
                        @error('account_name', 'withdraw')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold" for="wdNumber" id="wdNumberLabel">eSewa / Khalti ID</label>
                        <input type="text" class="form-control @error('account_number', 'withdraw') is-invalid @enderror" id="wdNumber" name="account_number" maxlength="40" value="{{ old('account_number') }}" placeholder="98XXXXXXXX" required>
                        @error('account_number', 'withdraw')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="tp-note mt-3"><i class="bi bi-shield-check"></i>The amount is set aside now and sent by the SajiloBhoj team, usually within 2 working days. If it can't be sent, it comes back to your earnings.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Request withdrawal</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
// Brand colour: same picker as the sign-up page.
(function () {
    var hidden = document.getElementById('rfColor'), custom = document.getElementById('rfColorCustom'),
        box = document.getElementById('rfColors'), swatches = box.querySelectorAll('.ws-color[data-color]'),
        customBox = box.querySelector('.ws-color-custom');
    function setColor(hex) {
        hex = hex.toLowerCase();
        hidden.value = hex;
        box.style.setProperty('--pick', hex);
        var matched = false;
        swatches.forEach(function (b) {
            var on = b.dataset.color.toLowerCase() === hex;
            b.classList.toggle('active', on); b.setAttribute('aria-checked', on ? 'true' : 'false');
            matched = matched || on;
        });
        customBox.classList.toggle('active', !matched);
        customBox.style.background = matched ? '' : hex;
    }
    swatches.forEach(function (b) { b.addEventListener('click', function () { setColor(b.dataset.color); }); });
    custom.addEventListener('input', function () { setColor(custom.value); });
    setColor(hidden.value);
})();

// Withdraw: bank needs a bank name and an account number; eSewa / Khalti need the mobile number.
(function () {
    var wrap = document.getElementById('wdBankWrap'), label = document.getElementById('wdNumberLabel'), num = document.getElementById('wdNumber');
    function sync() {
        var bank = (document.querySelector('#wdForm input[name=method]:checked') || {}).value === 'bank';
        wrap.classList.toggle('d-none', !bank);
        document.getElementById('wdBank').required = bank;
        label.textContent = bank ? 'Account Number' : 'eSewa / Khalti ID';
        num.placeholder = bank ? 'Bank account number' : '98XXXXXXXX';
    }
    document.querySelectorAll('#wdForm input[name=method]').forEach(function (r) { r.addEventListener('change', sync); });
    sync();
    // Re-open the form that had an error after the page reloads.
    @if ($errors->toBalance->any()) bootstrap.Modal.getOrCreateInstance(document.getElementById('toBalanceModal')).show(); @endif
    @if ($errors->withdraw->any()) bootstrap.Modal.getOrCreateInstance(document.getElementById('withdrawModal')).show(); @endif
})();

document.getElementById('rfForm')?.addEventListener('submit', function () {
    var btn = document.getElementById('rfBtn');
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Setting up the restaurant…';
});
</script>
@endpush
@endsection
