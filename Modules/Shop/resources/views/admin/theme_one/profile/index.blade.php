@extends('shop::admin.theme_one.component.layout')
@section('title', 'Profile')

@section('content')
<div class="page-title mb-4">
    <h2>My Profile</h2>
    <p>Your account details and password.</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-soft">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-person-circle me-2 text-primary"></i>Account</h5></div>
            <div class="card-body">
                <form action="{{ route('shop.admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
                        @else
                            <span class="avatar d-inline-flex align-items-center justify-content-center fw-bold text-white" style="width:64px;height:64px;border-radius:50%;background:var(--primary);font-size:1.3rem;">{{ $user->initials }}</span>
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <span class="pill pill-blue">{{ $user->role_label }}</span>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required maxlength="100"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
                            <input class="form-control" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" maxlength="20"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Address</label>
                            <input class="form-control" name="address" value="{{ old('address', $user->address) }}" maxlength="255"></div>
                        <div class="col-12"><label class="form-label fw-semibold">Profile picture</label>
                            <input class="form-control" type="file" name="profile_picture" accept="image/*"></div>
                    </div>
                    <button class="btn btn-primary mt-4"><i class="bi bi-check-lg me-1"></i>Save changes</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-soft">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Change password</h5></div>
            <div class="card-body">
                <form action="{{ route('shop.admin.profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3"><label class="form-label fw-semibold">Current password</label>
                        <input class="form-control @error('current_password') is-invalid @enderror" type="password" name="current_password" required autocomplete="current-password"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">New password</label>
                        <input class="form-control" type="password" name="password" required minlength="8" autocomplete="new-password"></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Confirm new password</label>
                        <input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password"></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-key me-1"></i>Update password</button>
                </form>
            </div>
        </div>
        <div class="card-soft mt-4">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-palette me-2 text-primary"></i>Theme colour</h5></div>
            <div class="card-body">
                <p class="text-muted small">The colour of your store and this back office. Pick one to preview it, then save.</p>
                <form action="{{ route('shop.admin.profile.theme') }}" method="POST">
                    @csrf @method('PUT')
                    @include('shop::partials.color-picker', ['name' => 'theme_color', 'value' => old('theme_color', $themeColor)])
                    <button class="btn btn-primary w-100 mt-4"><i class="bi bi-check-lg me-1"></i>Save theme colour</button>
                </form>
                <a class="small d-inline-block mt-3" href="{{ route('shop.admin.setting.index', 'theme') }}">More branding options (secondary colour, font…)</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.ws-colors { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
.ws-color { width: 36px; height: 36px; border-radius: 50%; border: 0; padding: 0; position: relative; cursor: pointer; transition: transform .15s; box-shadow: 0 4px 10px rgba(0,0,0,.12); }
.ws-color:hover { transform: scale(1.1); }
.ws-color.active { box-shadow: 0 0 0 3px #fff, 0 0 0 5px var(--primary); }
.ws-color.active::after { content: '\F26E'; font-family: bootstrap-icons; color: #fff; font-size: 1.1rem; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
.ws-color-custom { background: conic-gradient(#ef4444, #f59e0b, #22c55e, #06b6d4, #3b82f6, #8b5cf6, #ec4899, #ef4444); overflow: hidden; }
.ws-color-custom input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
</style>
@endpush
