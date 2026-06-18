<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - RestoPro Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}?v={{ filemtime(public_path('assets/backend/css/style.css')) }}" rel="stylesheet">
    <style>
        :root {
            --primary: {{ $themeColor ?? '#198754' }};
        }
    </style>
</head>

<body>
    <div class="login-wrap">
        <div class="login-card card-soft row g-0">
            <div class="col-lg-6 login-side">
                <div class="hero-pill"><i class="bi bi-stars"></i> Inspired by premium green admin dashboards</div>
                <h1 class="display-6 fw-bold mb-3">Restaurant management website template</h1>
                <p class="mb-0 text-white-50">Static HTML5, CSS3, Bootstrap, and jQuery pages for dashboard, orders,
                    tables, kitchen, reports, users, settings, billing, and profile.</p>
                <div class="metric-grid">
                    <div class="metric">
                        <div class="small text-white-50">Pages</div><strong class="fs-4">10+</strong>
                    </div>
                    <div class="metric">
                        <div class="small text-white-50">Style</div><strong class="fs-4">Modern Green</strong>
                    </div>
                    <div class="metric">
                        <div class="small text-white-50">UI Type</div><strong class="fs-4">Admin Panel</strong>
                    </div>
                    <div class="metric">
                        <div class="small text-white-50">Stack</div><strong class="fs-4">Bootstrap 5</strong>
                    </div>
                </div>
                <div class="list-inline-dot mt-4">
                    <span>Dashboard</span><span>Tables</span><span>Orders</span><span>Kitchen</span><span>Billing</span>
                </div>
            </div>
            <div class="col-lg-6 login-form">
                <form action="{{ route('admin.auth.login') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <div class="text-brand fw-semibold mb-2">Welcome back</div>
                        <h2 class="fw-bold mb-1">Login to RestoPro</h2>
                        <p class="text-secondary mb-0">Sign in with your staff account to manage this restaurant.</p>
                        <p class="text-secondary mb-0">New restaurant? <a href="{{ route('register.create') }}">Create a workspace</a></p>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success py-2">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                    @endif
                    <div class="mb-3"><label class="form-label">Email Address</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email"
                               value="{{ old('email') }}" placeholder="admin@restopro.com" required autofocus></div>
                    <div class="mb-3"><label class="form-label">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" type="password"
                               name="password" placeholder="••••••••" required></div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="remember"
                                id="remember" checked><label class="form-check-label" for="remember">Remember me</label></div>
                    </div><button class="btn btn-primary w-100" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/backend/js/custom.js') }}"></script>

    <canvas id="qrcode" data-value="askdhgsdkasdf"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        const qrValue = document.getElementById('qrcode').getAttribute('data-value');
        var qr = new QRious({
            element: document.getElementById('qrcode'),
            value: 'https://example.com/' + qrValue,
            size: 250
        });
    </script>

</body>

</html>
