@extends('admin.theme_one.component.layout')

@section('content')
@php
    $ini         = $user->initials ?: 'U';
    $roleName    = ucfirst($user->role->name ?? 'Administrator');
    $stats = [
        ['green',  'Total Tables', \App\Models\FloorTable::count(), '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="6" width="18" height="3.4" rx="1"/><rect x="5" y="9" width="2.4" height="10" rx=".8"/><rect x="16.6" y="9" width="2.4" height="10" rx=".8"/><rect x="5" y="13" width="14" height="2" rx=".6" opacity=".6"/></svg>'],
        ['blue',   'Orders',       \App\Models\Order::count(),      '<i class="bi bi-cart3"></i>'],
        ['orange', 'Menu Items',   \App\Models\MenuItem::count(),   '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 2h1.6v6h1.2V2h1.6v6h1.2V2h1.6v7a3 3 0 0 1-2.4 2.9V22H7.6V11.9A3 3 0 0 1 5 9z"/><path d="M17.5 2c-2 0-3.5 3-3.5 6.5 0 2.4 1 4 2.3 4.4V22h2.4v-9.1C20 12.5 21 10.9 21 8.5 21 5 19.5 2 17.5 2z"/></svg>'],
        ['purple', 'Staff',        \App\Models\User::count(),       '<i class="bi bi-people-fill"></i>'],
    ];
@endphp

<style>
    .pf-hero { position: relative; overflow: hidden; margin: -22px -28px 24px; padding: 30px 36px; display: flex; align-items: center; gap: 28px;
               background: linear-gradient(120deg, color-mix(in srgb, var(--primary) 14%, white) 0%, color-mix(in srgb, var(--primary) 7%, white) 55%, color-mix(in srgb, var(--primary) 18%, white) 100%); }
    .pf-hero .wave { position: absolute; right: 0; bottom: 0; width: 60%; height: 100%; pointer-events: none; }
    .pf-avatar { position: relative; z-index: 1; width: 132px; height: 132px; flex-shrink: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                 font-size: 2.8rem; font-weight: 800; color: #fff; background: linear-gradient(135deg, color-mix(in srgb, var(--primary) 80%, white), color-mix(in srgb, var(--primary) 75%, black));
                 border: 6px solid #fff; box-shadow: 0 12px 30px rgba(15, 40, 25, .18); }
    .pf-hero .info { position: relative; z-index: 1; }
    .pf-hero h1 { font-size: clamp(1.7rem, 3vw, 2.3rem); font-weight: 800; letter-spacing: -.02em; color: #14201a; margin: 0 0 6px; }
    .pf-hero .role { display: inline-flex; align-items: center; gap: 8px; font-size: 1.05rem; color: #1c2620; margin-bottom: 6px; }
    .pf-hero .role .bi { color: #e8a317; }
    .pf-hero p { color: #2f3b34; margin: 0; }
    .pf-hero .ghost { position: absolute; right: 56px; top: 50%; transform: translateY(-50%); font-size: 7rem; color: color-mix(in srgb, var(--primary) 30%, white); opacity: .7; z-index: 1; }

    .pf-card { background: #fff; border-radius: 18px; box-shadow: 0 10px 30px rgba(15, 40, 25, .07); padding: 22px 24px; }
    .pf-card + .pf-card { margin-top: 22px; }
    .pf-head { display: flex; align-items: center; gap: 12px; padding-bottom: 14px; margin-bottom: 18px; border-bottom: 1px solid #eef1ef; }
    .pf-head .hico { width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
                     background: color-mix(in srgb, var(--primary) 12%, white); color: var(--primary); }
    .pf-head h5 { margin: 0; font-size: 1.25rem; font-weight: 700; color: #14201a; }
    .pf-head small { display: block; color: #4b5a51; font-size: .85rem; margin-top: 2px; }

    .pf-list { list-style: none; margin: 0; padding: 0; }
    .pf-list li { display: flex; align-items: center; gap: 16px; padding: 10px 0; font-size: 1.05rem; color: #14201a; word-break: break-word; }
    .pf-list li .bi { font-size: 1.25rem; color: #1c2620; width: 26px; text-align: center; flex-shrink: 0; }

    .pf-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .pf-stat { display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 14px; }
    .pf-stat .sico { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.9rem; flex-shrink: 0; }
    .pf-stat .sico svg { width: 40px; height: 40px; }
    .pf-stat strong { display: block; font-size: 1.6rem; font-weight: 800; line-height: 1.1; color: #14201a; }
    .pf-stat span { font-size: .92rem; }
    .pf-stat { --c: var(--primary); background: color-mix(in srgb, var(--c) 12%, white); }
    .pf-stat .sico, .pf-stat span { color: color-mix(in srgb, var(--c) 85%, black); }
    .pf-stat.green  { --c: var(--primary); }
    .pf-stat.blue   { --c: color-mix(in srgb, var(--primary) 65%, black); }
    .pf-stat.orange { --c: color-mix(in srgb, var(--primary) 80%, #f59e0b); }
    .pf-stat.purple { --c: color-mix(in srgb, var(--primary) 60%, white); }

    .pf-card .form-label { font-weight: 600; color: #14201a; margin-bottom: 6px; }
    .pf-card .form-control, .pf-card .form-select { border-radius: 10px; padding: .7rem .95rem; border: 1px solid #dfe5e2; background: #fff; }
    .pf-card .form-control:disabled, .pf-card .form-select:disabled { background: #fff; color: #14201a; opacity: 1; }
    .pf-field { position: relative; }
    .pf-field > .bi:first-child { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #4b5a51; font-size: 1.05rem; pointer-events: none; z-index: 2; }
    .pf-field .form-control { padding-left: 42px; }
    .pf-field .eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: none; color: #4b5a51; padding: 6px 8px; z-index: 2; }
    .pf-field.has-eye .form-control { padding-right: 44px; }
    .pf-btn-outline { border: 1.5px solid var(--primary); color: var(--primary); background: #fff; }
    .pf-btn-outline:hover { background: color-mix(in srgb, var(--primary) 8%, white); color: var(--primary); }
    .pwd-strength { height: 4px; border-radius: 999px; background: #e9ecef; margin-top: 6px; overflow: hidden; }
    .pwd-strength-fill { height: 100%; border-radius: 999px; transition: .3s; width: 0; }
    .pwd-hint { font-size: .78rem; margin-top: 3px; }

    @media (max-width: 991px) {
        .pf-hero { margin: -16px -16px 18px; padding: 22px 18px; }
        .pf-hero .ghost { display: none; }
    }
    @media (max-width: 575px) {
        .pf-hero { flex-direction: column; text-align: center; gap: 14px; }
        .pf-avatar { width: 104px; height: 104px; font-size: 2.2rem; }
    }
</style>

{{-- ── Hero ─────────────────────────────────────── --}}
<div class="pf-hero">
    <svg class="wave" viewBox="0 0 800 220" preserveAspectRatio="none" aria-hidden="true">
        <path d="M200 220C320 150 520 90 800 60V220z" fill="color-mix(in srgb, var(--primary) 12%, white)" opacity=".8"/>
        <path d="M380 220C500 170 640 140 800 130V220z" fill="color-mix(in srgb, var(--primary) 20%, white)" opacity=".7"/>
    </svg>
    <div class="pf-avatar">{{ $ini }}</div>
    <div class="info">
        <h1>{{ $user->name }}</h1>
        <div class="role"><i class="bi bi-award-fill"></i>{{ $roleName }}</div>
        <p>Manage your account details and password.</p>
    </div>
    <i class="bi bi-person-fill ghost" aria-hidden="true"></i>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:14px;">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if (session('password_success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:14px;">
    <i class="bi bi-shield-check me-2"></i>{{ session('password_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- ── Left column ──────────────────────────── --}}
    <div class="col-lg-5 col-xl-4">
        <div class="pf-card">
            <div class="pf-head"><span class="hico"><i class="bi bi-person-fill"></i></span><h5>Profile Summary</h5></div>
            <ul class="pf-list">
                <li><i class="bi bi-envelope-fill"></i>{{ $user->email }}</li>
                @if ($user->phone)
                <li><i class="bi bi-telephone-fill"></i>{{ $user->phone }}</li>
                @endif
                <li><i class="bi bi-briefcase-fill"></i>{{ $roleName }}</li>
                <li><i class="bi bi-calendar3"></i>Member Since: {{ $user->created_at->format('M Y') }}</li>
            </ul>
        </div>

        <div class="pf-card">
            <div class="pf-head"><span class="hico"><i class="bi bi-bar-chart-fill"></i></span><h5>Quick Stats</h5></div>
            <div class="pf-stats">
                @foreach ($stats as [$color, $label, $value, $icon])
                <div class="pf-stat {{ $color }}">
                    <span class="sico">{!! $icon !!}</span>
                    <div><strong>{{ $value }}</strong><span>{{ $label }}</span></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Right column ─────────────────────────── --}}
    <div class="col-lg-7 col-xl-8">
        <div class="pf-card">
            <div class="pf-head"><span class="hico"><i class="bi bi-pencil-fill"></i></span><h5>Update Profile</h5></div>
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="pfName">Full Name</label>
                        <input type="text" id="pfName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="pfEmail">Email Address</label>
                        <input type="email" id="pfEmail" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select class="form-select" disabled><option>{{ $roleName }}</option></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Account Created</label>
                        <div class="pf-field">
                            <i class="bi bi-calendar3"></i>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d M Y, h:i A') }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-floppy-fill me-2"></i>Save Changes</button>
                    <button type="reset" class="btn pf-btn-outline px-4"><i class="bi bi-arrow-repeat me-2"></i>Reset</button>
                </div>
            </form>
        </div>

        <div class="pf-card">
            <div class="pf-head">
                <span class="hico"><i class="bi bi-shield-lock-fill"></i></span>
                <div><h5>Change Password</h5><small>Keep your account secure with a strong password.</small></div>
            </div>
            <form action="{{ route('admin.profile.update_password') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="curPwd">Current Password</label>
                        <div class="pf-field has-eye">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="current_password" id="curPwd" placeholder="Enter current password"
                                   class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                            <button type="button" class="eye" onclick="togglePwd('curPwd', this)" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                        @error('current_password')<div class="text-danger" style="font-size:.82rem;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="newPwd">New Password</label>
                        <div class="pf-field has-eye">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="password" id="newPwd" placeholder="Enter new password"
                                   class="form-control @error('password') is-invalid @enderror" oninput="checkStrength(this.value)" autocomplete="new-password">
                            <button type="button" class="eye" onclick="togglePwd('newPwd', this)" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="pwd-strength"><div class="pwd-strength-fill" id="pwdBar"></div></div>
                        <div class="pwd-hint" id="pwdHint" style="color:var(--muted);">At least 8 characters</div>
                        @error('password')<div class="text-danger" style="font-size:.82rem;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="confPwd">Confirm New Password</label>
                        <div class="pf-field has-eye">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="password_confirmation" id="confPwd" placeholder="Confirm new password" class="form-control" autocomplete="new-password">
                            <button type="button" class="eye" onclick="togglePwd('confPwd', this)" aria-label="Show password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-lock-fill me-2"></i>Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
function togglePwd(id, btn) {
    const el = document.getElementById(id);
    el.type = el.type === 'text' ? 'password' : 'text';
    btn.innerHTML = el.type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
}
function checkStrength(val) {
    let s = 0;
    if (val.length >= 8)          s++;
    if (/[A-Z]/.test(val))        s++;
    if (/[0-9]/.test(val))        s++;
    if (/[^A-Za-z0-9]/.test(val)) s++;
    const cfg = [['0%','#e9ecef','Too short'],['25%','#ef4444','Weak'],['50%','#f59e0b','Fair'],['75%','#3b82f6','Good'],['100%','#10b981','Strong ✓']];
    const [w, c, t] = cfg[s];
    const bar = document.getElementById('pwdBar');
    const hint = document.getElementById('pwdHint');
    bar.style.width = w; bar.style.background = c;
    hint.textContent = t; hint.style.color = c;
}
</script>
@endpush
@endsection
