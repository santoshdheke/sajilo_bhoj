@extends('frontend.theme_one.component.layout')

@section('content')
    <style>
        .login-hero {
            position: relative; overflow: hidden; flex: 1 0 auto;
            display: flex; align-items: center;
            padding: 56px 0 40px;
            background: linear-gradient(135deg, var(--sb-green-pale) 0%, #fff 50%, var(--sb-green-pale) 100%);
        }
        .login-hero .blob { position: absolute; border-radius: 50%; background: var(--sb-green-soft); opacity: .45; pointer-events: none; }
        .login-hero .blob-1 { width: 260px; height: 260px; left: -130px; top: 30px; }
        .login-hero .blob-2 { width: 220px; height: 220px; right: -110px; top: 20px; }
        .login-hero .leaf { position: absolute; color: var(--sb-green-soft); pointer-events: none; }
        .login-hero .leaf-1 { width: 60px; left: 5%; bottom: 170px; transform: rotate(-30deg); }
        .login-hero .leaf-2 { width: 46px; right: 6%; top: 42%; transform: rotate(25deg); }
        .login-hero .leaf-3 { width: 90px; right: 3%; bottom: 140px; transform: rotate(10deg); }
        .login-hero .container { position: relative; z-index: 1; max-width: 1080px; }

        .login-intro h1 { font-size: clamp(2rem, 4.5vw, 3.2rem); font-weight: 800; color: var(--sb-green-dark); margin-bottom: 14px; letter-spacing: -.02em; }
        .login-intro p { font-size: 1.3rem; color: var(--sb-muted); max-width: 340px; line-height: 1.45; margin-bottom: 18px; }
        .login-intro .bar { width: 78px; height: 5px; border-radius: 4px; background: var(--primary); margin-bottom: 28px; }
        .login-intro .art { width: 240px; max-width: 100%; margin-left: 70px; display: block; }

        .login-card { max-width: 430px; margin-left: auto; background: #fff; border-radius: 18px; padding: 28px 24px 22px; box-shadow: 0 20px 50px color-mix(in srgb, var(--primary) 12%, transparent); }
        .login-card .badge-icon { width: 68px; height: 68px; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; background: var(--sb-green-soft); color: var(--sb-green-dark); font-size: 1.9rem; }
        .login-card h2 { text-align: center; font-size: 1.9rem; font-weight: 800; color: var(--sb-green-dark); margin-bottom: 20px; }

        .login-card .field { position: relative; margin-bottom: 16px; }
        .login-card .field > .bi { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--sb-text); font-size: 1.05rem; pointer-events: none; }
        .login-card .field .form-control { height: 46px; padding: 0 44px; border-radius: 10px; border: 1px solid #dde5e0; font-size: .95rem; }
        .login-card .field .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 15%, transparent); }
        .login-card .toggle-pass { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: none; color: var(--sb-muted); padding: 6px 8px; }

        .login-card .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        .login-card .form-check-input:focus { box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 15%, transparent); }
        .login-card .form-check-label { font-size: .92rem; color: var(--sb-text); }
        .login-card a { color: var(--primary); font-weight: 600; }
        .login-card a:hover { text-decoration: underline; }
        .login-card .btn-sb-primary { height: 48px; border-radius: 10px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .login-card .foot { border-top: 1px solid var(--sb-border); margin: 18px 12px 0; padding-top: 14px; text-align: center; font-size: .95rem; color: var(--sb-text); }

        @media (max-width: 991px) {
            .login-hero { padding: 40px 0 48px; }
            .login-intro { text-align: center; margin-bottom: 28px; }
            .login-intro p { margin: 0 auto 14px; font-size: 1.1rem; }
            .login-intro .bar { margin: 0 auto; }
            .login-intro .art, .login-hero .leaf { display: none; }
            .login-card { margin: 0 auto; }
        }
    </style>

    <section class="login-hero">
        <span class="blob blob-1"></span>
        <span class="blob blob-2"></span>

        @php $leaf = '<svg viewBox="0 0 40 60" fill="currentColor" aria-hidden="true"><path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z"/><path d="M20 8v50" stroke="#fff" stroke-width="1.5" opacity=".6"/></svg>'; @endphp
        <span class="leaf leaf-1">{!! $leaf !!}</span>
        <span class="leaf leaf-2">{!! $leaf !!}</span>
        <span class="leaf leaf-3">{!! $leaf !!}</span>

        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 login-intro">
                    <h1>Welcome back</h1>
                    <p>Sign in to track your orders and loyalty points.</p>
                    <div class="bar"></div>

                    {{-- Serving cloche illustration --}}
                    <svg class="art" viewBox="0 0 240 150" aria-hidden="true">
                        <g fill="none" stroke="var(--sb-green-soft)" stroke-width="4" stroke-linecap="round">
                            <path d="M100 30c-8-10 8-14 0-26"/>
                            <path d="M116 26c-8-10 8-14 0-24"/>
                        </g>
                        <path d="M60 100a60 56 0 0 1 120 0z" fill="var(--sb-green)"/>
                        <path d="M78 82a44 40 0 0 1 30-28" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" opacity=".35"/>
                        <circle cx="120" cy="40" r="8" fill="var(--sb-green-dark)"/>
                        <rect x="50" y="98" width="140" height="10" rx="5" fill="var(--sb-green-dark)"/>
                        <path d="M10 130c40-24 90-30 150-18 25 5 45 2 70-8-20 22-60 30-100 24-45-6-85-4-120 2z" fill="var(--sb-green)"/>
                        <path d="M170 92c10-22 30-26 44-22-6 18-24 28-44 22z" fill="var(--sb-green)" opacity=".8"/>
                        <path d="M178 104c14-10 30-8 40 0-12 10-28 10-40 0z" fill="var(--sb-green-soft)"/>
                    </svg>
                </div>

                <div class="col-lg-6">
                    <div class="login-card">
                        <div class="badge-icon">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3v7a2 2 0 0 0 2 2v9"/><path d="M10 3v7a2 2 0 0 1-2 2"/><path d="M8 3v6"/><path d="M17 21V13"/><path d="M17 13c-2 0-3-2.5-3-5s1-5 3-5 3 2.5 3 5-1 5-3 5z" fill="currentColor"/></svg>
                        </div>
                        <h2>Sign In</h2>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small">
                                {{ $errors->first() }}
                                @if (str_contains($errors->first(), 'staff account'))
                                    <a href="{{ route('admin.auth.show_login_form') }}" class="alert-link d-block mt-1">Go to staff login →</a>
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('frontend.auth.login') }}">
                            @csrf
                            <div class="field">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="you@example.com" aria-label="Email"
                                       required autofocus autocomplete="email">
                            </div>
                            <div class="field">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="password" id="loginPassword"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Password" aria-label="Password" required autocomplete="current-password">
                                <button type="button" class="toggle-pass" aria-label="Show password"
                                        onclick="const p=document.getElementById('loginPassword');const show=p.type==='password';p.type=show?'text':'password';this.firstElementChild.className=show?'bi bi-eye-slash':'bi bi-eye';">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                @if (Route::has('frontend.password.request'))
                                    <a href="{{ route('frontend.password.request') }}" class="small">Forgot password?</a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-sb-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        </form>

                        <p class="foot mb-0">New here? <a href="{{ route('frontend.auth.show_register_form') }}">Create an account</a></p>
                        <p class="text-center small mt-2 mb-0" style="color:var(--sb-muted);">Restaurant staff? <a href="{{ route('admin.auth.show_login_form') }}">Staff login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
