@extends($theme_path . 'common.layout.master_layout')

@section('contant')
<div class="eh-login-page">
    <div class="container">
        <div class="eh-login-card" style="max-width:480px;">
            <div class="eh-login-logo">
                @if($shopLogoUrl)
                    <img src="{{ $shopLogoUrl }}" alt="{{ $shop->name }}">
                @else
                    <span style="font-size:22px;font-weight:800;color:var(--primary);">{{ $shop->name }}</span>
                @endif
            </div>
            <h2>Create Account</h2>
            <p class="subtitle">Join us today — it's free</p>

            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('shop.front.register.post') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="eh-form-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="eh-form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                        </div>
                    </div>
                </div>
                <div class="eh-form-group">
                    <label>Email Address *</label>
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com"
                               value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="eh-form-group">
                    <label>Phone Number</label>
                    <div class="input-icon">
                        <i class="bi bi-telephone"></i>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                    </div>
                </div>
                <div class="eh-form-group">
                    <label>Password *</label>
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    </div>
                </div>
                <div class="eh-form-group">
                    <label>Confirm Password *</label>
                    <div class="input-icon">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="eh-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;margin-top:8px;">
                    <i class="bi bi-person-plus"></i> Create Account
                </button>
            </form>

            <div class="eh-login-foot">
                Already have an account? <a href="{{ route('shop.front.login') }}">Sign In</a>
            </div>
        </div>
    </div>
</div>
@endsection
