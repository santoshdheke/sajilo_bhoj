@extends($theme_path . 'common.layout.master_layout')

@section('contant')
<div class="eh-login-page">
    <div class="container">
        <div class="eh-login-card">
            <div class="eh-login-logo">
                @if($shopLogoUrl)
                    <img src="{{ $shopLogoUrl }}" alt="{{ $shop->name }}">
                @else
                    <span style="font-size:22px;font-weight:800;color:var(--primary);">{{ $shop->name }}</span>
                @endif
            </div>
            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to your account</p>

            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('shop.front.login.post') }}" method="POST">
                @csrf
                <div class="eh-form-group">
                    <label>Email Address</label>
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com"
                               value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="eh-form-group">
                    <label>Password</label>
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div style="text-align:right;margin-bottom:4px;">
                </div>
                <button type="submit" class="eh-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;margin-top:8px;">
                    Sign In
                </button>
            </form>

            <div class="eh-login-foot">
                Don't have an account? <a href="{{ route('shop.front.register') }}">Register Free</a>
            </div>
        </div>
    </div>
</div>
@endsection
