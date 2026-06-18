@extends('admin.theme_one.component.layout')

@section('content')
<style>
.profile-card     { text-align:center;padding:36px 28px;position:relative;overflow:hidden; }
.profile-card::before { content:'';position:absolute;top:0;left:0;right:0;height:90px;background:linear-gradient(135deg,#113a26,#198754);border-radius:22px 22px 0 0; }
.profile-avatar   { width:90px;height:90px;border-radius:50%;font-size:1.8rem;font-weight:800;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#b8f1ce,#54d68f);color:#135e3b;border:4px solid #fff;margin:0 auto 16px;position:relative;z-index:1;box-shadow:0 4px 16px rgba(0,0,0,.15); }
.profile-name     { font-size:1.25rem;font-weight:800;margin-bottom:4px; }
.profile-email    { font-size:.85rem;color:var(--muted);margin-bottom:12px; }
.profile-stat     { display:flex;align-items:center;justify-content:center;gap:24px;margin-top:20px;padding-top:18px;border-top:1px solid var(--line); }
.pstat            { display:flex;flex-direction:column;align-items:center; }
.pstat .val       { font-size:1.05rem;font-weight:800; }
.pstat .lbl       { font-size:.72rem;color:var(--muted); }
.section-hdr      { display:flex;align-items:center;gap:10px; }
.section-hdr-icon { width:36px;height:36px;border-radius:11px;background:#edfaf3;color:#198754;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0; }
.section-hdr h5   { margin:0;font-size:.97rem;font-weight:700; }
.pwd-strength     { height:4px;border-radius:999px;background:#e9ecef;margin-top:6px;overflow:hidden; }
.pwd-strength-fill{ height:100%;border-radius:999px;transition:.3s;width:0; }
.pwd-hint         { font-size:.74rem;margin-top:3px; }
</style>

@php
    $ini         = collect(explode(' ',$user->name))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('');
    $memberSince = $user->created_at->format('M Y');
    $lastUpdate  = $user->updated_at->diffForHumans();
    $totalOrders   = \App\Models\Order::count();
    $totalBookings = \App\Models\TableBooking::count();
    $totalItems    = \App\Models\MenuItem::count();
    $totalStaff    = \App\Models\User::count();
@endphp

<div class="page-title mb-4">
    <h2>My Profile</h2>
    <p>Manage your account details and password.</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:14px;">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('password_success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:14px;">
    <i class="bi bi-shield-check me-2"></i>{{ session('password_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- Profile card --}}
    <div class="col-lg-4">
        <div class="card-soft profile-card mb-4">
            <div class="profile-avatar">{{ $ini }}</div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>
            <span class="status-badge status-ready"><i class="bi bi-shield-check me-1"></i>{{ ucfirst($user->role->name ?? 'Administrator') }}</span>
            <div class="profile-stat">
                <div class="pstat">
                    <span class="val">{{ $memberSince }}</span>
                    <span class="lbl">Member Since</span>
                </div>
                <div style="width:1px;height:36px;background:var(--line);"></div>
                <div class="pstat">
                    <span class="val" style="font-size:.85rem;">{{ $lastUpdate }}</span>
                    <span class="lbl">Last Update</span>
                </div>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="card-soft" style="padding:20px;">
            <div class="section-hdr mb-3">
                <div class="section-hdr-icon"><i class="bi bi-lightning-charge"></i></div>
                <h5>System Overview</h5>
            </div>
            <div class="row g-2 text-center">
                @foreach([
                    ['Orders',    $totalOrders,   'bi-receipt',       '#edfaf3','#198754'],
                    ['Bookings',  $totalBookings, 'bi-calendar-check','#e0f2fe','#0369a1'],
                    ['Menu Items',$totalItems,    'bi-basket',        '#fff8e8','#92400e'],
                    ['Staff',     $totalStaff,    'bi-people',        '#eef0ff','#3730a3'],
                ] as [$l,$v,$i,$bg,$c])
                <div class="col-6">
                    <div style="background:{{ $bg }};border-radius:14px;padding:14px 10px;">
                        <div style="font-size:1.3rem;font-weight:800;color:{{ $c }};">{{ $v }}</div>
                        <div style="font-size:.72rem;color:{{ $c }};opacity:.8;">{{ $l }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Forms --}}
    <div class="col-lg-8">

        <div class="card-soft mb-4">
            <div class="card-header">
                <div class="section-hdr">
                    <div class="section-hdr-icon"><i class="bi bi-person"></i></div>
                    <h5>Personal Information</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$user->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email',$user->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role->name ?? 'Administrator') }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Created</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d M Y, h:i A') }}" disabled>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2 me-1"></i>Save Changes</button>
                        <button type="reset" class="btn btn-soft">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-soft">
            <div class="card-header">
                <div class="section-hdr">
                    <div class="section-hdr-icon"><i class="bi bi-shield-lock"></i></div>
                    <h5>Change Password</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update_password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="curPwd"
                                       class="form-control @error('current_password') is-invalid @enderror">
                                <button type="button" class="btn btn-soft" onclick="togglePwd('curPwd',this)"><i class="bi bi-eye"></i></button>
                            </div>
                            @error('current_password')<div class="text-danger" style="font-size:.82rem;margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="newPwd"
                                       class="form-control @error('password') is-invalid @enderror"
                                       oninput="checkStrength(this.value)">
                                <button type="button" class="btn btn-soft" onclick="togglePwd('newPwd',this)"><i class="bi bi-eye"></i></button>
                            </div>
                            <div class="pwd-strength"><div class="pwd-strength-fill" id="pwdBar"></div></div>
                            <div class="pwd-hint" id="pwdHint" style="color:var(--muted);">Enter new password</div>
                            @error('password')<div class="text-danger" style="font-size:.82rem;margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confPwd" class="form-control">
                                <button type="button" class="btn btn-soft" onclick="togglePwd('confPwd',this)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-key me-1"></i>Update Password</button>
                    </div>
                </form>
            </div>
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
