<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $shop->name)</title>
    @if ($shopLogoUrl)<link rel="icon" href="{{ $shopLogoUrl }}">@endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family={{ $brand['font'] }}:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('shop/frontend/vegitable/css/eh-style.css') }}?v={{ filemtime(public_path('shop/frontend/vegitable/css/eh-style.css')) }}" rel="stylesheet">
    @stack('css')
@php
    $clrHex = $brand['primary'] ?: 'F47920';   // primary hex without #
    $clrDark = $brand['secondary'] ?: '1a1a2e';  // dark/secondary hex
    // Compute tinted variants from primary
    $r = hexdec(substr($clrHex,0,2));
    $g = hexdec(substr($clrHex,2,2));
    $b = hexdec(substr($clrHex,4,2));
    // dark variant: reduce each channel
    $primaryDark   = sprintf('%02x%02x%02x', max(0,$r-30), max(0,$g-20), max(0,$b-10));
    // light variant: blend 88% toward white
    $primaryLight  = sprintf('%02x%02x%02x', (int)round($r+(255-$r)*.88), (int)round($g+(255-$g)*.88), (int)round($b+(255-$b)*.88));
    // border variant: blend 60% toward white
    $primaryBorder = sprintf('%02x%02x%02x', (int)round($r+(255-$r)*.6),  (int)round($g+(255-$g)*.6),  (int)round($b+(255-$b)*.6));
@endphp
<style>
    :root {
        --primary:        #{{ $clrHex }};
        --primary-dark:   #{{ $primaryDark }};
        --primary-light:  #{{ $primaryLight }};
        --primary-border: #{{ $primaryBorder }};
        --dark:           #{{ $clrDark }};
        --dark-2:         #{{ $clrDark }};
    }
    body { font-family: '{{ $brand['font_css'] }}', 'Inter', system-ui, sans-serif; }
</style>
</head>
<body>


<form id="veg-logout-form" action="{{ route('shop.front.logout') }}" method="POST" style="display:none;">@csrf</form>

@php
    $cartCount = 0;
    $cartData = json_decode(\Modules\Shop\Support\Cart::raw(), true);
    if (is_array($cartData)) $cartCount = count($cartData);
    $allCategories = Modules\Shop\Models\Category::all();
@endphp

<!-- TOPBAR -->
<div class="eh-topbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-0">
            @if($shop->address)
                <span><i class="bi bi-geo-alt-fill me-1"></i> {{ $shop->address }}</span>
                <span class="sep">|</span>
            @endif
            @if($shop->phone)
                <span class="d-none d-md-inline"><i class="bi bi-telephone-fill me-1" style="color:var(--primary)"></i> {{ $shop->phone }}</span>
                <span class="sep d-none d-md-inline">|</span>
            @endif
            @if($shop->email)
                <span class="d-none d-lg-inline"><i class="bi bi-envelope-fill me-1"></i> {{ $shop->email }}</span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-0">
            @if(auth('shop_customer')->check())
                <a href="{{ route('shop.front.account.profile') }}"><i class="bi bi-person-fill me-1"></i> {{ auth('shop_customer')->user()->name }}</a>
                <span class="sep">|</span>
                <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();">Logout</a>
            @else
                <a href="{{ route('shop.front.login') }}">Sign In</a>
                <span class="sep">|</span>
                <a href="{{ route('shop.front.register') }}" style="color:var(--primary);font-weight:600;">Register</a>
            @endif
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="eh-navbar">
    <div class="container">
        <div class="eh-navbar-inner">
            <a href="{{ route('shop.front.home') }}" class="eh-logo flex-shrink-0">
                @if($shopLogoUrl)
                    <img src="{{ $shopLogoUrl }}" alt="{{ $shop->name }}" style="height:52px;width:auto;">
                @else
                    <span style="font-size:22px;font-weight:800;color:var(--primary);">{{ $shop->name }}</span>
                @endif
            </a>

            <form class="eh-search-wrap d-none d-lg-flex" action="{{ route('shop.front.store') }}" method="GET">
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach($allCategories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->title }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products…">
                <button type="submit"><i class="bi bi-search"></i> Search</button>
            </form>

            <div class="eh-nav-btns">
                @if(auth('shop_customer')->check())
                    <a href="{{ route('shop.front.account.profile') }}" class="eh-btn-ghost d-none d-lg-inline-flex">
                        <i class="bi bi-person"></i> {{ auth('shop_customer')->user()->name }}
                    </a>
                @else
                    <a href="{{ route('shop.front.login') }}" class="eh-btn-ghost d-none d-lg-inline-flex">
                        <i class="bi bi-person"></i> Sign In
                    </a>
                @endif
                <a href="{{ route('shop.front.checkout') }}" class="eh-btn-primary d-none d-lg-inline-flex" style="position:relative;">
                    <i class="bi bi-bag"></i> Cart
                    @if($cartCount > 0)
                        <span style="position:absolute;top:-8px;right:-8px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ $cartCount }}</span>
                    @endif
                </a>
                <button class="eh-mobile-toggle d-lg-none" id="mobileMenuBtn" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- MOBILE OVERLAY & MENU -->
<div class="eh-mobile-overlay" id="mobileOverlay"></div>
<div class="eh-mobile-menu" id="mobileMenu" aria-hidden="true">
    <div class="eh-mobile-menu-head">
        <span><i class="bi bi-grid-3x3-gap-fill me-2"></i>{{ $shop->name }}</span>
        <button class="eh-mobile-menu-close" id="mobileMenuClose"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="eh-mobile-menu-body">
        <div class="eh-mobile-search">
            <form action="{{ route('shop.front.store') }}" class="eh-mobile-search-form" method="GET">
                <i class="bi bi-search eh-mobile-search-icon"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products…" class="eh-mobile-search-input">
                <button type="submit" class="eh-mobile-search-btn"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="eh-mobile-menu-divider">Categories</div>
        @foreach($allCategories as $cat)
            <a href="{{ route('shop.front.category', ['category' => $cat->slug]) }}" class="eh-mobile-menu-link">
                <i class="bi bi-tag"></i> {{ $cat->title }}
            </a>
        @endforeach
        <div class="eh-mobile-menu-divider">Account</div>
        @if(auth('shop_customer')->check())
            <a href="{{ route('shop.front.account.profile') }}" class="eh-mobile-menu-link"><i class="bi bi-person"></i> My Profile</a>
            <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="eh-mobile-menu-link"><i class="bi bi-box-seam"></i> My Orders</a>
            <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();" class="eh-mobile-menu-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
        @else
            <a href="{{ route('shop.front.login') }}" class="eh-mobile-menu-link"><i class="bi bi-person"></i> Sign In</a>
            <a href="{{ route('shop.front.register') }}" class="eh-mobile-menu-link"><i class="bi bi-person-plus"></i> Register</a>
        @endif
    </div>
    <div class="eh-mobile-menu-footer">
        <a href="{{ route('shop.front.checkout') }}" class="eh-btn-primary justify-content-center">
            <i class="bi bi-bag"></i> Cart @if($cartCount > 0)({{ $cartCount }})@endif
        </a>
    </div>
</div>

<!-- CATBAR -->
<div class="eh-catbar">
    <div class="container">
        <div class="eh-catbar-inner">
            <a href="{{ route('shop.front.home') }}" class="eh-catbar-item {{ request()->routeIs('index') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i> Home
            </a>
            <div class="eh-catbar-divider"></div>
            <a href="{{ route('shop.front.store') }}" class="eh-catbar-item {{ request()->routeIs('store.index') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> All Products
            </a>
            @foreach($allCategories->take(8) as $cat)
                <div class="eh-catbar-divider"></div>
                <a href="{{ route('shop.front.category', ['category' => $cat->slug]) }}"
                   class="eh-catbar-item {{ request('category') == $cat->slug ? 'active' : '' }}">
                    {{ $cat->title }}
                </a>
            @endforeach
        </div>
    </div>
</div>

@yield('contant')

<!-- FOOTER -->
<footer class="eh-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="eh-footer-brand">
                    @if($shopLogoUrl)
                        <img src="{{ $shopLogoUrl }}" alt="{{ $shop->name }}">
                    @else
                        <span style="font-size:22px;font-weight:800;color:var(--primary);">{{ $shop->name }}</span>
                    @endif
                </div>
                <p class="eh-footer-desc">
                    {{ $settings->about_us ?: $shop->name.' — your trusted online store.' }}
                </p>
                <div class="eh-footer-social">
                    @foreach($socialLinks as $platform => $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" title="{{ $link['label'] }}"><i class="bi {{ $link['bi'] }}"></i></a>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <h6>Shop</h6>
                @foreach($allCategories->take(6) as $cat)
                    <a href="{{ route('shop.front.category', ['category' => $cat->slug]) }}">{{ $cat->title }}</a>
                @endforeach
                <a href="{{ route('shop.front.store') }}">All Products</a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <h6>My Account</h6>
                @if(auth('shop_customer')->check())
                    <a href="{{ route('shop.front.account.profile') }}">My Profile</a>
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}">My Orders</a>
                    <a href="{{ route('shop.front.checkout') }}">Cart</a>
                    <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();">Logout</a>
                @else
                    <a href="{{ route('shop.front.login') }}">Sign In</a>
                    <a href="{{ route('shop.front.register') }}">Register</a>
                    <a href="{{ route('shop.front.checkout') }}">Cart</a>
                @endif
            </div>
            <div class="col-sm-6 col-lg-3">
                <h6>Contact</h6>
                @if($shop->address)
                    <a href="#"><i class="bi bi-geo-alt me-1"></i> {{ $shop->address }}</a>
                @endif
                @if($shop->phone)
                    <a href="tel:{{ $shop->phone }}"><i class="bi bi-telephone me-1"></i> {{ $shop->phone }}</a>
                @endif
                @if($shop->email)
                    <a href="mailto:{{ $shop->email }}"><i class="bi bi-envelope me-1"></i> {{ $shop->email }}</a>
                @endif
            </div>
        </div>
        <hr>
        <div class="eh-footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $shop->name }}. All rights reserved.</span>
            <div class="d-flex gap-3">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var btn   = document.getElementById('mobileMenuBtn');
    var close = document.getElementById('mobileMenuClose');
    var menu  = document.getElementById('mobileMenu');
    var overlay = document.getElementById('mobileOverlay');
    function openMenu()  { menu.classList.add('open'); overlay.classList.add('open'); menu.setAttribute('aria-hidden','false'); }
    function closeMenu() { menu.classList.remove('open'); overlay.classList.remove('open'); menu.setAttribute('aria-hidden','true'); }
    if (btn)     btn.addEventListener('click', openMenu);
    if (close)   close.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);
})();

// Flyout sidebar
document.querySelectorAll('.eh-flyout-item').forEach(function(item) {
    var trigger = item.querySelector('.eh-flyout-trigger');
    var panel   = item.querySelector('.eh-flyout-panel');
    if (!trigger || !panel) return;
    trigger.addEventListener('mouseenter', function() {
        var rect = trigger.getBoundingClientRect();
        panel.style.top  = rect.top + 'px';
        panel.style.left = (rect.right + 4) + 'px';
        item.classList.add('open');
    });
    item.addEventListener('mouseleave', function() {
        item.classList.remove('open');
    });
});
</script>
@stack('js')
</body>
</html>
