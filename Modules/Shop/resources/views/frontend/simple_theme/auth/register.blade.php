@extends($theme_path . 'common.layout.master_layout')
@section('hide_flash', 1)

@push('css')
<style>
    /* ── page shell ── */
    .auth-page {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: stretch;
    }

    /* ── left brand panel ── */
    .auth-brand {
        flex: 0 0 42%;
        background: #{{ $brand['primary'] }};
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 48px;
        position: relative;
        overflow: hidden;
    }

    /* decorative circles */
    .auth-brand::before {
        content: '';
        position: absolute;
        width: 340px; height: 340px;
        border-radius: 50%;
        border: 60px solid rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.06);
        top: -80px; right: -80px;
    }
    .auth-brand::after {
        content: '';
        position: absolute;
        width: 220px; height: 220px;
        border-radius: 50%;
        border: 50px solid rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.06);
        bottom: -50px; left: -50px;
    }

    .auth-brand-inner { position: relative; z-index: 1; text-align: center; }

    .auth-brand-logo {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 34px; font-weight: 700; font-style: italic;
        color: #{{ $brand['text'] }};
        text-transform: uppercase; letter-spacing: 3px;
        text-decoration: none; display: block; margin-bottom: 20px;
    }
    .auth-brand-logo:hover { color: #{{ $brand['text'] }}; opacity: 0.9; }

    .auth-brand-divider {
        width: 40px; height: 2px;
        background: rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.4);
        margin: 0 auto 20px;
    }

    .auth-brand-tagline {
        font-size: 13.5px;
        color: rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.7);
        line-height: 1.75; max-width: 260px; margin: 0 auto 36px;
    }

    .auth-brand-perks { list-style: none; padding: 0; margin: 0 0 40px; text-align: left; display: inline-block; }
    .auth-brand-perks li {
        font-size: 13px;
        color: rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.8);
        margin-bottom: 12px;
        display: flex; align-items: center; gap: 10px;
    }
    .auth-brand-perks li i {
        width: 20px; font-size: 13px;
        color: rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.6);
    }

    .auth-brand-link {
        font-size: 12.5px;
        color: rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.65);
    }
    .auth-brand-link a {
        color: #{{ $brand['text'] }};
        font-weight: 600; border-bottom: 1px solid rgba({{ hexdec(substr($brand['text'],0,2)) }},{{ hexdec(substr($brand['text'],2,2)) }},{{ hexdec(substr($brand['text'],4,2)) }},0.4);
        padding-bottom: 1px; transition: border-color 0.2s;
    }
    .auth-brand-link a:hover { border-color: #{{ $brand['text'] }}; color: #{{ $brand['text'] }}; }

    /* ── right form panel ── */
    .auth-form-panel {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 32px;
        background: #fff;
    }

    .auth-form-wrap {
        width: 100%;
        max-width: 420px;
    }

    .auth-form-heading {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px; font-weight: 700; font-style: italic;
        color: #1a1a1a; margin-bottom: 6px;
    }

    .auth-form-sub {
        font-size: 13px; color: #aaa;
        margin-bottom: 32px;
    }

    /* Field groups */
    .auth-field {
        position: relative;
        margin-bottom: 18px;
    }

    .auth-field-icon {
        position: absolute;
        left: 16px; top: 50%;
        transform: translateY(-50%);
        color: #ccc; font-size: 13px;
        pointer-events: none;
        transition: color 0.2s;
    }

    .auth-field input {
        width: 100%;
        padding: 13px 16px 13px 42px;
        border: 1.5px solid #e8e8e8;
        border-radius: 4px;
        font-size: 13.5px;
        font-family: 'Poppins', sans-serif;
        color: #333;
        background: #faf9f7;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
    }

    .auth-field input::placeholder { color: #bbb; }

    .auth-field input:focus {
        border-color: #{{ $brand['primary'] }};
        background: #fff;
    }
    .auth-field input:focus + .auth-field-label,
    .auth-field input:not(:placeholder-shown) + .auth-field-label {
        opacity: 1;
    }
    .auth-field input:focus ~ .auth-field-icon { color: #{{ $brand['primary'] }}; }

    /* two-column row */
    .auth-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    /* password toggle */
    .auth-field .pw-toggle {
        position: absolute;
        right: 14px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none;
        color: #bbb; cursor: pointer;
        font-size: 13px; padding: 0;
        transition: color 0.2s;
    }
    .auth-field .pw-toggle:hover { color: #{{ $brand['primary'] }}; }
    .auth-field.has-toggle input { padding-right: 40px; }

    /* Error messages */
    .auth-error {
        font-size: 11.5px; color: #e53e3e;
        margin-top: 4px; display: block;
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

    /* Submit button */
    .auth-btn {
        width: 100%;
        padding: 14px;
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        border: none; border-radius: 4px;
        font-size: 13px; font-weight: 700;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 1.5px; text-transform: uppercase;
        cursor: pointer; transition: opacity 0.2s, transform 0.15s;
        margin-top: 8px;
    }
    .auth-btn:hover { opacity: 0.88; transform: translateY(-1px); }
    .auth-btn:active { transform: translateY(0); }

    /* Footer link */
    .auth-footer-link {
        text-align: center;
        margin-top: 24px;
        font-size: 13px; color: #aaa;
    }
    .auth-footer-link a {
        color: #{{ $brand['primary'] }};
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .auth-footer-link a:hover { opacity: 0.75; }

    /* Terms note */
    .auth-terms {
        font-size: 11.5px; color: #ccc;
        text-align: center; margin-top: 16px; line-height: 1.6;
    }
    .auth-terms a { color: #aaa; text-decoration: underline; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .auth-brand { display: none; }
        .auth-page { min-height: auto; padding: 32px 0; }
        .auth-row { grid-template-columns: 1fr; gap: 0; }
    }
</style>
@endpush

@section('contant')
<div class="auth-page">

    {{-- LEFT: Brand Panel --}}
    <div class="auth-brand">
        <div class="auth-brand-inner">
            <a href="{{ route('shop.front.home') }}" class="auth-brand-logo">{{ $shop->name }}</a>
            <div class="auth-brand-divider"></div>
            <p class="auth-brand-tagline">
                Join thousands of happy customers and enjoy an exclusive shopping experience.
            </p>
            <ul class="auth-brand-perks">
                <li><i class="fas fa-tag"></i> Exclusive member discounts</li>
                <li><i class="fas fa-truck"></i> Free delivery on first order</li>
                <li><i class="fas fa-history"></i> Easy order tracking</li>
                <li><i class="fas fa-undo-alt"></i> Hassle-free returns</li>
            </ul>
            <p class="auth-brand-link">
                Already have an account? <a href="{{ route('shop.front.login') }}">Sign In</a>
            </p>
        </div>
    </div>

    {{-- RIGHT: Form Panel --}}
    <div class="auth-form-panel">
        <div class="auth-form-wrap">

            <h2 class="auth-form-heading">Create Account</h2>
            <p class="auth-form-sub">Fill in the details below to get started.</p>

            {{-- Alerts --}}
            @if(session('error') || $errors->any())
                <div class="auth-alert">{{ session('error') ?: $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="auth-alert auth-alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('shop.front.register.post') }}" method="post" autocomplete="off">
                @csrf

                {{-- Name row --}}
                <div class="auth-row">
                    <div class="auth-field">
                        <input type="text" name="first_name" placeholder="First Name"
                               value="{{ old('first_name') }}" autocomplete="given-name">
                        <i class="fas fa-user auth-field-icon"></i>
                        @error('first_name')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-field">
                        <input type="text" name="last_name" placeholder="Last Name"
                               value="{{ old('last_name') }}" autocomplete="family-name">
                        <i class="fas fa-user auth-field-icon"></i>
                        @error('last_name')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="auth-field">
                    <input type="email" name="email" placeholder="Email Address"
                           value="{{ old('email') }}" autocomplete="email">
                    <i class="fas fa-envelope auth-field-icon"></i>
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="auth-field">
                    <input type="text" name="phone_number" placeholder="Phone Number"
                           value="{{ old('phone_number') }}" autocomplete="tel">
                    <i class="fas fa-phone auth-field-icon"></i>
                    @error('phone_number')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="auth-field">
                    <input type="text" name="address" placeholder="Address"
                           value="{{ old('address') }}" autocomplete="street-address">
                    <i class="fas fa-map-marker-alt auth-field-icon"></i>
                    @error('address')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password row --}}
                <div class="auth-row">
                    <div class="auth-field has-toggle">
                        <input type="password" name="password" placeholder="Password" id="regPassword"
                               autocomplete="new-password">
                        <i class="fas fa-lock auth-field-icon"></i>
                        <button type="button" class="pw-toggle" onclick="togglePw('regPassword', this)">
                            <i class="far fa-eye"></i>
                        </button>
                        @error('password')
                            <span class="auth-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="auth-field has-toggle">
                        <input type="password" name="password_confirmation" placeholder="Confirm Password"
                               id="regPasswordConfirm" autocomplete="new-password">
                        <i class="fas fa-lock auth-field-icon"></i>
                        <button type="button" class="pw-toggle" onclick="togglePw('regPasswordConfirm', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Create Account</button>
            </form>

            <div class="auth-footer-link">
                Already have an account? <a href="{{ route('shop.front.login') }}">Sign In</a>
            </div>
            <p class="auth-terms">
                By registering, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
            </p>

        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    function togglePw(id, btn) {
        var input = document.getElementById(id);
        var icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endpush
