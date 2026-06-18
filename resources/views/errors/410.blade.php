<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create your restaurant - RestoPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}?v={{ filemtime(public_path('assets/backend/css/style.css')) }}" rel="stylesheet">
    <style>
        :root {
            --primary: {{ old('theme_color', '#198754') }};
        }
    </style>
</head>

<body>
    @php($host = $exception->getMessage() ?: request()->getHost())
    <div class="login-wrap">
        <div class="login-card card-soft row g-0">
            <div class="col-lg-6 login-side">
                <div class="hero-pill"><i class="bi bi-exclamation-octagon"></i> No workspace at {{ $host }}</div>
                <h1 class="display-6 fw-bold mb-3">Bring your restaurant online in minutes</h1>
                <p class="mb-0 text-white-50">This domain isn't linked to a workspace yet. If it's yours, set one up
                    below and we'll connect it to <strong>{{ $host }}</strong> right away — dashboard, tables, orders,
                    kitchen, inventory, staff, and reports, all on your own domain.</p>
                <div class="metric-grid">
                    <div class="metric">
                        <div class="small text-white-50">Setup</div><strong class="fs-4">One step</strong>
                    </div>
                    <div class="metric">
                        <div class="small text-white-50">Branches</div><strong class="fs-4">Unlimited</strong>
                    </div>
                    <div class="metric">
                        <div class="small text-white-50">Access</div><strong class="fs-4">Role-based</strong>
                    </div>
                    <div class="metric">
                        <div class="small text-white-50">Stack</div><strong class="fs-4">Bootstrap 5</strong>
                    </div>
                </div>
                <div class="list-inline-dot mt-4">
                    <span>Dashboard</span><span>Tables</span><span>Orders</span><span>Kitchen</span><span>Inventory</span>
                </div>
            </div>
            <div class="col-lg-6 login-form">
                <form action="{{ route('register.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <div class="text-brand fw-semibold mb-2">Get started</div>
                        <h2 class="fw-bold mb-1">Create your workspace</h2>
                        <p class="text-secondary mb-0">Have an account? <a href="{{ route('admin.auth.show_login_form') }}">Sign in</a></p>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company name</label>
                            <input class="form-control @error('company_name') is-invalid @enderror" type="text"
                                   name="company_name" value="{{ old('company_name') }}" placeholder="The Green Fork" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your name</label>
                            <input class="form-control @error('owner_name') is-invalid @enderror" type="text"
                                   name="owner_name" value="{{ old('owner_name') }}" placeholder="Owner full name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input class="form-control @error('owner_email') is-invalid @enderror" type="email"
                                   name="owner_email" value="{{ old('owner_email') }}" placeholder="owner@restaurant.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                   name="password" placeholder="8+ characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm password</label>
                            <input class="form-control" type="password" name="password_confirmation"
                                   placeholder="Repeat password" required>
                        </div>
                        <div class="col-12">
                            <div class="brand-color-card @error('theme_color') is-invalid @enderror">
                                <div class="brand-color-label"><i class="bi bi-palette-fill"></i> Brand color</div>
                                <div class="d-flex align-items-center gap-3">
                                    <label class="color-swatch" id="theme_color_swatch" style="background:{{ old('theme_color', '#198754') }}">
                                        <input type="color" id="theme_color_input" name="theme_color"
                                               value="{{ old('theme_color', '#198754') }}" onchange="setThemeColor(this.value)">
                                    </label>
                                    <input type="text" id="theme_color_hex" class="form-control color-hex-input"
                                           value="{{ old('theme_color', '#198754') }}" maxlength="7"
                                           oninput="setThemeColorFromHex(this.value)">
                                </div>
                                <div class="color-presets mt-3">
                                    @foreach(['#7c83fd','#e6194b','#0e7c4a','#caa325','#7b1fa2','#1f5fbf','#e8590c','#1a1a2e','#e91e8c','#17c3c3'] as $preset)
                                        <button type="button" class="color-preset" style="background:{{ $preset }}"
                                                onclick="setThemeColor('{{ $preset }}')" aria-label="Use {{ $preset }}"></button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-4" type="submit">Create workspace</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/backend/js/custom.js') }}"></script>
    <script>
        function setThemeColor(hex) {
            document.getElementById('theme_color_input').value = hex;
            document.getElementById('theme_color_hex').value = hex;
            document.getElementById('theme_color_swatch').style.background = hex;
            document.documentElement.style.setProperty('--primary', hex);
        }
        function setThemeColorFromHex(value) {
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                setThemeColor(value);
            }
        }
        setThemeColor(document.getElementById('theme_color_input').value);
    </script>
</body>

</html>
