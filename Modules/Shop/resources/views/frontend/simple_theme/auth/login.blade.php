@extends($theme_path . 'common.layout.master_layout')
@section('hide_flash', 1)

@push('css')
<style>
    /* ── Page shell ── */
    .auth-page {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: stretch;
    }

    /* ── LEFT — dynamic banner ── */
    .auth-banner {
        flex: 0 0 55%;
        position: relative;
        overflow: hidden;
        background: #1a1a2e;
    }

    .auth-banner-img {
        width: 100%; height: 100%;
        object-fit: cover; object-position: center;
        display: block;
    }

    /* gradient overlay on banner */
    .auth-banner-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(
            135deg,
            rgba(0,0,0,0.68) 0%,
            rgba(0,0,0,0.35) 60%,
            rgba(0,0,0,0.15) 100%
        );
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 40px 48px;
    }

    /* Brand on banner */
    .auth-banner-brand {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 24px; font-weight: 700; font-style: italic;
        color: #fff;
        text-transform: uppercase; letter-spacing: 3px;
        text-decoration: none; display: inline-block;
    }
    .auth-banner-brand:hover { color: rgba(255,255,255,0.85); }

    /* Banner text at bottom */
    .auth-banner-text { }
    .auth-banner-tagline {
        font-size: 11px; font-weight: 600;
        color: rgba(255,255,255,0.55);
        text-transform: uppercase; letter-spacing: 3px;
        margin-bottom: 12px; display: block;
    }
    .auth-banner-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(26px, 3.5vw, 44px);
        font-weight: 700; font-style: italic;
        color: #fff; margin: 0 0 12px;
        line-height: 1.15;
    }
    .auth-banner-sub {
        font-size: 14px; color: rgba(255,255,255,0.65);
        line-height: 1.7; margin: 0;
        max-width: 360px;
    }

    /* Dots for multiple banners */
    .auth-banner-dots {
        display: flex; gap: 7px;
        margin-top: 24px;
    }
    .auth-banner-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: rgba(255,255,255,0.35);
        cursor: pointer; border: none; padding: 0;
        transition: all 0.22s;
    }
    .auth-banner-dot.active {
        background: #fff; width: 22px; border-radius: 50px;
    }

    /* Fallback when no banner — dark gradient panel */
    .auth-banner-fallback {
        width: 100%; height: 100%;
        background: linear-gradient(145deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        position: relative; overflow: hidden;
    }
    .auth-banner-fallback::before {
        content: '';
        position: absolute; width: 400px; height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, #{{ $brand['primary'] }} 0%, transparent 70%);
        opacity: 0.15; top: -80px; right: -80px;
    }
    .auth-banner-fallback::after {
        content: '';
        position: absolute; width: 250px; height: 250px;
        border-radius: 50%;
        background: radial-gradient(circle, #{{ $brand['primary'] }} 0%, transparent 70%);
        opacity: 0.1; bottom: -60px; left: -60px;
    }

    /* ── RIGHT — login form ── */
    .auth-form-panel {
        flex: 1;
        display: flex; align-items: center; justify-content: center;
        padding: 48px 40px;
        background: #fff;
    }

    .auth-form-wrap { width: 100%; max-width: 380px; }

    .auth-form-heading {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px; font-weight: 700; font-style: italic;
        color: #1a1a1a; margin-bottom: 6px;
    }
    .auth-form-sub {
        font-size: 13px; color: #aaa; margin-bottom: 32px;
    }

    /* Alert */
    .auth-alert {
        padding: 12px 16px;
        background: #fff5f5; border: 1px solid #fed7d7;
        border-radius: 4px; font-size: 12.5px; color: #c53030;
        margin-bottom: 20px;
    }
    .auth-alert-success {
        background: #f0fff4; border-color: #c6f6d5; color: #276749;
    }

    /* Fields */
    .auth-field { position: relative; margin-bottom: 18px; }
    .auth-field-inner { position: relative; }
    .auth-field-inner i {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        color: #ccc; font-size: 13px; pointer-events: none;
        transition: color 0.2s;
    }
    .auth-field input {
        width: 100%;
        padding: 13px 14px 13px 42px;
        border: 1.5px solid #e8e8e8; border-radius: 4px;
        font-size: 13.5px; font-family: 'Poppins', sans-serif;
        color: #333; background: #faf9f7; outline: none;
        transition: border-color 0.2s, background 0.2s;
    }
    .auth-field input::placeholder { color: #bbb; }
    .auth-field input:focus {
        border-color: #{{ $brand['primary'] }};
        background: #fff;
    }
    .auth-field input:focus ~ i,
    .auth-field-inner:focus-within i { color: #{{ $brand['primary'] }}; }

    /* Password toggle */
    .pw-eye {
        position: absolute; right: 12px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; color: #ccc;
        cursor: pointer; font-size: 13px; padding: 0;
        transition: color 0.2s;
    }
    .pw-eye:hover { color: #{{ $brand['primary'] }}; }

    /* Error */
    span.auth-error {
        display: block; font-size: 11.5px; color: #e53e3e;
        margin-top: 5px; font-weight: 500;
    }

    /* Remember + Forgot row */
    .auth-options {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 22px; font-size: 12.5px;
    }
    .auth-options label { display: flex; align-items: center; gap: 6px; color: #666; cursor: pointer; }
    .auth-options input[type=checkbox] { accent-color: #{{ $brand['primary'] }}; }
    .auth-forgot { color: #{{ $brand['primary'] }}; transition: opacity 0.2s; }
    .auth-forgot:hover { opacity: 0.7; color: #{{ $brand['primary'] }}; }

    /* Submit */
    .auth-btn {
        width: 100%; padding: 14px;
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        border: none; border-radius: 4px;
        font-size: 13px; font-weight: 700;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 1.5px; text-transform: uppercase;
        cursor: pointer; transition: opacity 0.2s, transform 0.15s;
    }
    .auth-btn:hover { opacity: 0.88; transform: translateY(-1px); }
    .auth-btn:active { transform: translateY(0); }

    /* Divider */
    .auth-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 22px 0; color: #ddd; font-size: 12px;
    }
    .auth-divider::before, .auth-divider::after {
        content: ''; flex: 1; height: 1px; background: #ebebeb;
    }

    /* Footer link */
    .auth-footer-link {
        text-align: center; margin-top: 22px;
        font-size: 13px; color: #aaa;
    }
    .auth-footer-link a {
        color: #{{ $brand['primary'] }}; font-weight: 600;
        transition: opacity 0.2s;
    }
    .auth-footer-link a:hover { opacity: 0.75; }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .auth-banner { display: none; }
        .auth-page { min-height: auto; padding: 32px 0; }
        .auth-form-panel { padding: 32px 20px; }
    }
</style>
@endpush

@section('contant')
@php
    $banners = Modules\Shop\Models\Banner::active()->where('status', 1)->get();
    if ($banners->isEmpty()) {
        $banners = Modules\Shop\Models\Banner::active()->get();
    }
    $firstBanner = $banners->first();
@endphp

<div class="auth-page">

    {{-- LEFT: Dynamic Banner --}}
    <div class="auth-banner">
        @if ($firstBanner && $firstBanner->image_path)
            {{-- Slideshow if multiple banners --}}
            <div id="loginBannerCarousel" class="carousel slide" data-ride="carousel" data-interval="4500"
                 style="height:100%;">
                <div class="carousel-inner" style="height:100%;">
                    @foreach ($banners as $bi => $banner)
                        @if ($banner->image_path)
                            <div class="carousel-item{{ $bi === 0 ? ' active' : '' }}" style="height:100%;">
                                <img class="auth-banner-img"
                                     src="{{ $banner->image_path }}"
                                     alt="{{ $banner->title }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="auth-banner-overlay">
                <a href="{{ route('shop.front.home') }}" class="auth-banner-brand">{{ $shop->name }}</a>
                <div class="auth-banner-text">
                    <span class="auth-banner-tagline">Welcome Back</span>
                    <h2 class="auth-banner-title" id="bannerTitle">
                        {{ $firstBanner->title ?: 'Sign in to your account' }}
                    </h2>
                    <p class="auth-banner-sub" id="bannerSub">
                        {{ $firstBanner->sub_title ?: 'Discover exclusive collections and manage your orders.' }}
                    </p>
                    @if ($banners->count() > 1)
                        <div class="auth-banner-dots" id="bannerDots">
                            @foreach ($banners as $bi => $banner)
                                <button class="auth-banner-dot{{ $bi === 0 ? ' active' : '' }}"
                                        data-slide-to="{{ $bi }}"
                                        data-target="#loginBannerCarousel"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Fallback when no banners --}}
            <div class="auth-banner-fallback"></div>
            <div class="auth-banner-overlay">
                <a href="{{ route('shop.front.home') }}" class="auth-banner-brand">{{ $shop->name }}</a>
                <div class="auth-banner-text">
                    <span class="auth-banner-tagline">Welcome Back</span>
                    <h2 class="auth-banner-title">Sign in to continue</h2>
                    <p class="auth-banner-sub">Access your account, track orders, and explore exclusive collections.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- RIGHT: Login Form --}}
    <div class="auth-form-panel">
        <div class="auth-form-wrap">

            <h2 class="auth-form-heading">Welcome Back</h2>
            <p class="auth-form-sub">Sign in to your {{ $shop->name }} account.</p>

            {{-- Alerts --}}
            @if(session('error') || $errors->any())
                <div class="auth-alert">{{ session('error') ?: $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="auth-alert auth-alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('shop.front.login.post') }}" method="post">
                @csrf

                <div class="auth-field">
                    <div class="auth-field-inner">
                        <input type="email" name="email" placeholder="Email Address"
                               value="{{ old('email') }}" autocomplete="email">
                        <i class="fas fa-envelope"></i>
                    </div>
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-field">
                    <div class="auth-field-inner">
                        <input type="password" name="password" id="loginPassword"
                               placeholder="Password" autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="pw-eye" onclick="toggleLoginPw()">
                            <i class="far fa-eye" id="loginEyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-options">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                </div>

                <button type="submit" class="auth-btn">
                    Sign In
                </button>
            </form>

            <div class="auth-divider">or</div>

            <div class="auth-footer-link">
                Don't have an account? <a href="{{ route('shop.front.register') }}">Create Account</a>
            </div>

        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    function toggleLoginPw() {
        var input = document.getElementById('loginPassword');
        var icon  = document.getElementById('loginEyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    @if($banners->count() > 1)
    // Sync dots and text with banner carousel
    var bannerTitles = @json($banners->pluck('title'));
    var bannerSubs   = @json($banners->pluck('sub_title'));

    $('#loginBannerCarousel').on('slid.bs.carousel', function (e) {
        var idx = $(e.relatedTarget).index();

        // Update dots
        $('#bannerDots .auth-banner-dot').removeClass('active');
        $('#bannerDots .auth-banner-dot').eq(idx).addClass('active');

        // Update title & subtitle
        if (bannerTitles[idx]) $('#bannerTitle').text(bannerTitles[idx]);
        if (bannerSubs[idx])   $('#bannerSub').text(bannerSubs[idx]);
    });
    @endif
</script>
@endpush
