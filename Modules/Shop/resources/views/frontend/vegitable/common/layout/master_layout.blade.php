<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $shop->name)</title>
    @include('shop::partials.favicon', ['color' => $brand['primary'], 'logoUrl' => $shopLogoUrl])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family={{ $brand['font'] }}:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Page styles of the store pages (product, store, checkout...) --}}
    <link href="{{ asset('shop/frontend/vegitable/css/eh-style.css') }}?v={{ filemtime(public_path('shop/frontend/vegitable/css/eh-style.css')) }}" rel="stylesheet">
    @php
        // Brand colours from Settings (hex without #): primary + tints, secondary as the dark colour.
        $clrHex  = $brand['primary'] ?: 'f97316';
        $clrDark = $brand['secondary'] ?: '1a1a2e';
        [$r, $g, $b] = [hexdec(substr($clrHex, 0, 2)), hexdec(substr($clrHex, 2, 2)), hexdec(substr($clrHex, 4, 2))];
        $mix = fn ($p) => sprintf('%02x%02x%02x', (int) round($r + (255 - $r) * $p), (int) round($g + (255 - $g) * $p), (int) round($b + (255 - $b) * $p));
        $primaryDark   = sprintf('%02x%02x%02x', max(0, $r - 30), max(0, $g - 20), max(0, $b - 10));
        $primaryLight  = $mix(.88);
        $primaryBorder = $mix(.6);
    @endphp
    <style>
        :root {
            --primary: #{{ $clrHex }}; --primary-dark: #{{ $primaryDark }}; --primary-light: #{{ $primaryLight }};
            --primary-border: #{{ $primaryBorder }}; --primary-text: #{{ $brand['text'] }};
            --dark: #{{ $clrDark }}; --dark-2: #{{ $clrDark }};
        }
        body { font-family: '{{ $brand['font_css'] }}', 'Inter', system-ui, sans-serif; background: #f6f7fb; }

        /* Brand helpers on top of Bootstrap */
        .bg-brand { background: var(--primary) !important; color: var(--primary-text) !important; }
        .bg-brand-soft { background: var(--primary-light) !important; }
        .bg-dark-brand { background: var(--dark) !important; }
        .text-brand { color: var(--primary) !important; }
        .border-brand { border-color: var(--primary) !important; }
        .btn-brand { background: var(--primary); border-color: var(--primary); color: var(--primary-text); font-weight: 600; }
        .btn-brand:hover, .btn-brand:focus { background: var(--primary-dark); border-color: var(--primary-dark); color: var(--primary-text); }
        .btn-brand:disabled, .btn-brand.disabled { background: #cbd5e1; border-color: #cbd5e1; color: #fff; opacity: 1; }
        .btn-outline-brand { border-color: var(--primary); color: var(--primary); font-weight: 600; }
        .btn-outline-brand:hover { background: var(--primary); color: var(--primary-text); }
        .sf-lift { transition: transform .15s, box-shadow .15s; }
        .sf-lift:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(15, 23, 42, .1) !important; }

        /* Product card */
        .sf-pcard { box-shadow: 0 2px 10px rgba(15, 23, 42, .06); padding: 8px; transition: transform .15s, box-shadow .15s; }
        .sf-pcard:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(15, 23, 42, .1); }
        .sf-pcard-img { background: #fff; }
        .sf-pcard-img img { width: 100%; height: 100%; object-fit: contain; padding: 10px; transition: transform .25s; }
        .sf-pcard:hover .sf-pcard-img img { transform: scale(1.05); }
        .sf-pcard-badge { position: absolute; top: 8px; left: 8px; font-size: .68rem; font-weight: 700; padding: .3em .7em; }
        .sf-pcard-heart { position: absolute; top: 6px; right: 6px; width: 32px; height: 32px; border-radius: 50%; border: 0; background: #fff;
                          color: #475569; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(15, 23, 42, .1); transition: color .15s, transform .15s; }
        .sf-pcard-heart:hover { color: var(--primary); transform: scale(1.08); }
        .sf-pcard-heart.on { color: var(--primary); }
        .sf-pcard-name { font-size: .88rem; font-weight: 700; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.3em; }
        .sf-pcard-name:hover { color: var(--primary) !important; }
        .sf-row-scroll { scrollbar-width: thin; scroll-snap-type: x mandatory; }
        .sf-row-scroll > [class*="col-"] { scroll-snap-align: start; }

        /* Toasts (Notyf) */
        .notyf__toast { border-radius: 12px; box-shadow: 0 10px 30px rgba(15, 23, 42, .18); }
        .notyf__message { font-weight: 500; }

        /* Header */
        .sf-logo-mark { width: 42px; height: 42px; }
        .sf-search .form-select { max-width: 170px; }
        .sf-search .form-control:focus, .sf-search .form-select:focus { box-shadow: none; border-color: var(--primary); }

        /* Category bar */
        .sf-catnav .nav-link { color: rgba(255, 255, 255, .8); font-size: .82rem; font-weight: 500; white-space: nowrap; border-radius: .5rem; }
        .sf-catnav .nav-link:hover { color: #fff; background: rgba(255, 255, 255, .08); }
        .sf-catnav .nav-link.active { background: var(--primary); color: var(--primary-text); }
        .sf-scroll-x { overflow-x: auto; scrollbar-width: none; }
        .sf-scroll-x::-webkit-scrollbar { display: none; }

        /* Footer */
        .sf-footer { background: linear-gradient(180deg, var(--dark), color-mix(in srgb, var(--dark) 85%, black)) !important; }
        .sf-footer .text-white-75 { color: rgba(255, 255, 255, .75); }
        .sf-footer-links li { margin-bottom: .3rem; }
        .sf-footer-links a, .sf-footer-links span { color: rgba(255, 255, 255, .85); text-decoration: none; }
        .sf-footer-links a:hover { color: var(--primary); }
        .sf-social-link { color: #fff; text-decoration: none; transition: color .15s, transform .15s; }
        .sf-social-link:hover { color: var(--primary); transform: translateY(-2px); }
        .sf-footer-wave { width: 52%; height: 150px; pointer-events: none; }
    </style>
    @stack('css')
</head>
<body>

@php
    $cartCount     = count(json_decode(\Modules\Shop\Support\Cart::raw(), true) ?: []);
    $allCategories = Modules\Shop\Models\Category::where('parent_id', 0)->orderBy('title')->get();
    $customer      = auth('shop_customer')->user();
@endphp
<form id="veg-logout-form" action="{{ route('shop.front.logout') }}" method="POST" class="d-none">@csrf</form>

{{-- ════ TOP BAR ════ --}}
<div class="bg-dark-brand text-white-50 small py-2">
    <div class="container">
        <div class="row align-items-center g-2">
            <div class="col d-flex flex-wrap align-items-center gap-3">
                @if ($shop->address)<span><i class="bi bi-geo-alt-fill me-1"></i>{{ $shop->address }}</span>@endif
                @if ($shop->email)<span class="d-none d-sm-inline"><i class="bi bi-envelope-fill me-1"></i>{{ $shop->email }}</span>@endif
                @if ($shop->phone)<span class="d-none d-md-inline"><i class="bi bi-telephone-fill me-1"></i>{{ $shop->phone }}</span>@endif
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                @if ($customer)
                    <a class="link-light text-decoration-none" href="{{ route('shop.front.account.dashboard') }}"><i class="bi bi-person-fill me-1"></i>{{ $customer->name }}</a>
                    <span class="opacity-50">|</span>
                    <a class="link-light text-decoration-none" href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();">Logout</a>
                @else
                    <a class="link-light text-decoration-none" href="{{ route('shop.front.login') }}">Sign In</a>
                    <span class="opacity-50">|</span>
                    <a class="text-brand fw-semibold text-decoration-none" href="{{ route('shop.front.register') }}">Register</a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ════ HEADER ════ --}}
<header class="bg-white border-bottom sticky-top shadow-sm">
    <div class="container py-3">
        <div class="row align-items-center g-3">
            <div class="col-7 col-lg-3">
                <a href="{{ route('shop.front.home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                    @if ($shopLogoUrl)
                        <img src="{{ $shopLogoUrl }}" alt="{{ $shop->name }}" style="height:46px;width:auto;">
                    @else
                        <span class="sf-logo-mark rounded-3 bg-brand-soft text-brand d-inline-flex align-items-center justify-content-center fs-4"><i class="bi bi-shop"></i></span>
                    @endif
                    <span class="fs-4 fw-bold text-dark text-truncate">{{ $shop->name }}</span>
                </a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <form class="input-group sf-search" action="{{ route('shop.front.store') }}" method="GET">
                    <select name="category" class="form-select bg-light">
                        <option value="">All Categories</option>
                        @foreach ($allCategories as $cat)
                            <option value="{{ $cat->slug }}" @selected(request()->query('category') === $cat->slug)>{{ $cat->title }}</option>
                        @endforeach
                    </select>
                    <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search products...">
                    <button class="btn btn-brand px-4" type="submit"><i class="bi bi-search me-1"></i> Search</button>
                </form>
            </div>
            <div class="col-5 col-lg-3 d-flex justify-content-end align-items-center gap-2">
                @if ($customer)
                    <a href="{{ route('shop.front.account.dashboard') }}" class="btn btn-light border d-none d-sm-inline-flex align-items-center gap-1"><i class="bi bi-person-fill"></i> Account</a>
                @else
                    <a href="{{ route('shop.front.login') }}" class="btn btn-light border d-none d-sm-inline-flex align-items-center gap-1"><i class="bi bi-person-fill"></i> Sign In</a>
                @endif
                <a href="{{ route('shop.front.checkout') }}" class="btn btn-brand d-inline-flex align-items-center gap-1">
                    <i class="bi bi-cart3"></i><span class="d-none d-sm-inline">Cart</span> ({{ $cartCount }})
                </a>
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-label="Menu"><i class="bi bi-list"></i></button>
            </div>
            <div class="col-12 d-lg-none">
                <form class="input-group sf-search" action="{{ route('shop.front.store') }}" method="GET">
                    <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search products...">
                    <button class="btn btn-brand" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    {{-- ════ CATEGORY BAR ════ --}}
    <nav class="bg-dark-brand sf-catnav">
        <div class="container">
            <ul class="nav flex-nowrap sf-scroll-x gap-1 py-2">
                <li class="nav-item"><a class="nav-link px-3 {{ request()->routeIs('shop.front.home') ? 'active' : '' }}" href="{{ route('shop.front.home') }}"><i class="bi bi-house-door-fill me-1"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link px-3 {{ request()->routeIs('shop.front.store') ? 'active' : '' }}" href="{{ route('shop.front.store') }}"><i class="bi bi-grid me-1"></i> All Products</a></li>
                @foreach ($allCategories->take(8) as $cat)
                    <li class="nav-item"><a class="nav-link px-3 {{ request()->route('category') === $cat->slug ? 'active' : '' }}" href="{{ route('shop.front.category', $cat->slug) }}"><i class="bi {{ $cat->icon }} me-1"></i> {{ $cat->title }}</a></li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>

{{-- ════ MOBILE MENU ════ --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header bg-brand">
        <h5 class="offcanvas-title" id="mobileMenuLabel">{{ $shop->name }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <div class="list-group-item small text-uppercase text-muted fw-semibold bg-light">Categories</div>
            @foreach ($allCategories as $cat)
                <a href="{{ route('shop.front.category', $cat->slug) }}" class="list-group-item list-group-item-action"><i class="bi {{ $cat->icon }} me-2 text-brand"></i>{{ $cat->title }}</a>
            @endforeach
            <a href="{{ route('shop.front.store') }}" class="list-group-item list-group-item-action"><i class="bi bi-grid me-2 text-brand"></i>All Products</a>
            <div class="list-group-item small text-uppercase text-muted fw-semibold bg-light">Account</div>
            @if ($customer)
                <a href="{{ route('shop.front.account.profile') }}" class="list-group-item list-group-item-action"><i class="bi bi-person me-2"></i>My Profile</a>
                <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="list-group-item list-group-item-action"><i class="bi bi-box-seam me-2"></i>My Orders</a>
                <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();" class="list-group-item list-group-item-action"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            @else
                <a href="{{ route('shop.front.login') }}" class="list-group-item list-group-item-action"><i class="bi bi-person me-2"></i>Sign In</a>
                <a href="{{ route('shop.front.register') }}" class="list-group-item list-group-item-action"><i class="bi bi-person-plus me-2"></i>Register</a>
            @endif
        </div>
    </div>
</div>

<main>
    @yield('contant')
</main>

{{-- ════ FOOTER ════ --}}
@php
    $nameWords = preg_split('/\s+/', trim($shop->name));
    $nameLast  = count($nameWords) > 1 ? array_pop($nameWords) : null;
@endphp
<footer class="sf-footer bg-dark-brand text-white mt-5">
    <div class="position-relative overflow-hidden">
        {{-- brand-coloured wave, bottom right --}}
        <svg class="sf-footer-wave position-absolute bottom-0 end-0 d-none d-md-block" viewBox="0 0 520 200" preserveAspectRatio="none" aria-hidden="true">
            <defs>
                <linearGradient id="sfWave" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0" stop-color="var(--primary)" stop-opacity="0"/>
                    <stop offset=".55" stop-color="var(--primary)" stop-opacity=".55"/>
                    <stop offset="1" stop-color="var(--primary)" stop-opacity="1"/>
                </linearGradient>
            </defs>
            <path d="M0 200C120 150 230 110 330 112c90 2 140 30 190 18v70z" fill="url(#sfWave)"/>
            <path d="M120 200c90-40 180-62 270-58 60 3 95 18 130 10v48z" fill="var(--primary)" opacity=".45"/>
            <path d="M40 150C160 98 280 70 400 78c50 3 85 12 120 4" fill="none" stroke="var(--primary)" stroke-opacity=".35" stroke-width="2"/>
        </svg>

        <div class="container position-relative py-4 py-lg-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a href="{{ route('shop.front.home') }}" class="d-inline-flex align-items-center gap-2 mb-2 text-decoration-none">
                        @if ($shopLogoUrl)
                            <img src="{{ $shopLogoUrl }}" alt="{{ $shop->name }}" style="height:38px;width:auto;">
                        @else
                            <span class="sf-logo-mark rounded-3 bg-brand-soft text-brand d-inline-flex align-items-center justify-content-center fs-4"><i class="bi bi-shop"></i></span>
                        @endif
                        <span class="fs-4 fw-bold text-white">{{ implode(' ', $nameWords) }} @if ($nameLast)<span class="text-brand">{{ $nameLast }}</span>@endif</span>
                    </a>
                    <p class="small text-white-75 mb-3">{{ $settings->about_us ?: $shop->name.' — your trusted online store'.($shop->address ? ', '.$shop->address : '').'.' }}</p>
                    @if ($socialLinks)
                        <div class="d-flex align-items-center gap-3 fs-5">
                            @foreach ($socialLinks as $platform => $link)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" title="{{ $link['label'] }}" class="sf-social-link"><i class="bi {{ $link['bi'] }}"></i></a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 class="fw-bold mb-2 text-white">Shop</h6>
                    <ul class="list-unstyled small mb-0 sf-footer-links">
                        @foreach ($allCategories->take(6) as $cat)
                            <li><a href="{{ route('shop.front.category', $cat->slug) }}">{{ $cat->title }}</a></li>
                        @endforeach
                        <li><a href="{{ route('shop.front.store') }}">All Products</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <h6 class="fw-bold mb-2 text-white">My Account</h6>
                    <ul class="list-unstyled small mb-0 sf-footer-links">
                        @if ($customer)
                            <li><a href="{{ route('shop.front.account.dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}">My Orders</a></li>
                        @else
                            <li><a href="{{ route('shop.front.login') }}">Sign In</a></li>
                            <li><a href="{{ route('shop.front.register') }}">Register</a></li>
                        @endif
                        <li><a href="{{ route('shop.front.checkout') }}">Cart</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-lg-3">
                    <h6 class="fw-bold mb-2 text-white">Contact</h6>
                    <ul class="list-unstyled small mb-0 sf-footer-links">
                        @if ($shop->phone)<li><a href="tel:{{ $shop->phone }}"><i class="bi bi-telephone-fill me-2"></i>{{ $shop->phone }}</a></li>@endif
                        @if ($shop->email)<li><a href="mailto:{{ $shop->email }}"><i class="bi bi-envelope-fill me-2"></i>{{ $shop->email }}</a></li>@endif
                        @if ($shop->address)<li><span><i class="bi bi-geo-alt-fill me-2"></i>{{ $shop->address }}</span></li>@endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="border-top border-white border-opacity-10">
        <div class="container py-3">
            <div class="row align-items-center g-2 small">
                <div class="col-md text-white-75">&copy; {{ date('Y') }} {{ $shop->name }}. All rights reserved.</div>
                <div class="col-md-auto">
                    <nav class="d-flex align-items-center gap-2 sf-footer-links">
                        <a href="{{ route('shop.front.page.privacy') }}">Privacy</a><span class="text-white-50">|</span>
                        <a href="{{ route('shop.front.page.terms') }}">Terms</a><span class="text-white-50">|</span>
                        <a href="{{ route('shop.front.page.refund') }}">Refund Policy</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</footer>
@isset($socialLinks['whatsapp'])
    <a href="{{ $socialLinks['whatsapp']['url'] }}" target="_blank" rel="noopener" title="Chat on WhatsApp"
       class="position-fixed bottom-0 end-0 m-4 btn btn-success rounded-circle shadow d-inline-flex align-items-center justify-content-center fs-4" style="width:54px;height:54px;z-index:1030;"><i class="bi bi-whatsapp"></i></a>
@endisset

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.js"></script>
<script>
// Toast messages in the shop's brand colour: notify.success('...') / notify.error('...').
(function () {
    var brand = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#f97316';
    var notyf = new Notyf({ duration: 3500, dismissible: true, ripple: true, position: { x: 'right', y: 'top' },
        types: [
            { type: 'success', background: brand, icon: { className: 'bi bi-check-circle-fill', tagName: 'i', color: '#fff' } },
            { type: 'error', background: '#dc2626', duration: 5000, icon: { className: 'bi bi-exclamation-circle-fill', tagName: 'i', color: '#fff' } },
        ] });
    window.notify = { success: function (m) { return notyf.success(m); }, error: function (m) { return notyf.error(m); } };
})();
@if (session('success')) notify.success(@json(session('success'))); @endif
@hasSection('hide_flash')
@else
    @if (session('error')) notify.error(@json(session('error'))); @elseif ($errors->any()) notify.error(@json($errors->first())); @endif
@endif

// Heart on product cards: saved in this browser only.
(function () {
    var KEY = 'sf-saved-{{ $shop->id }}', saved = [];
    try { saved = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) {}
    function paint(btn) { var on = saved.indexOf(btn.dataset.wish) !== -1; btn.classList.toggle('on', on); btn.querySelector('i').className = on ? 'bi bi-heart-fill' : 'bi bi-heart'; }
    document.querySelectorAll('[data-wish]').forEach(function (btn) {
        paint(btn);
        btn.addEventListener('click', function () {
            var i = saved.indexOf(btn.dataset.wish);
            if (i === -1) { saved.push(btn.dataset.wish); notify.success('"' + btn.dataset.name + '" saved for later.'); }
            else { saved.splice(i, 1); notify.success('"' + btn.dataset.name + '" removed from saved.'); }
            try { localStorage.setItem(KEY, JSON.stringify(saved)); } catch (e) {}
            document.querySelectorAll('[data-wish="' + btn.dataset.wish + '"]').forEach(paint);
        });
    });
})();
</script>
@stack('js')
</body>
</html>
