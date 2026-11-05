<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['restaurant_name'] ?: 'SajiloBhoj' }} - Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}?v={{ filemtime(public_path('assets/frontend/css/style.css')) }}">
    <style>
        :root {
            --primary: {{ $settings['theme_color'] ?? '#198754' }};
            --green: color-mix(in srgb, var(--primary) 80%, black);
            --green-two: color-mix(in srgb, var(--primary) 65%, black);
            --footer: color-mix(in srgb, var(--primary) 85%, black);
            --footer-light: color-mix(in srgb, var(--primary) 75%, white);
            --banner: color-mix(in srgb, var(--primary) 88%, black);
            --sb-green-dark: color-mix(in srgb, var(--primary) 78%, black);
            --sb-green-progress: var(--primary);
            --sb-green: var(--primary);
            --sb-green-soft: color-mix(in srgb, var(--primary) 18%, white);
            --sb-green-pale: color-mix(in srgb, var(--primary) 8%, white);
            --sb-accent: #d9922c;
            --sb-text: color-mix(in srgb, var(--primary) 70%, black);
            --sb-muted: #6a7c73; --sb-white: #ffffff;
            --sb-border: color-mix(in srgb, var(--primary) 10%, white);
            --sb-shadow: 0 16px 45px color-mix(in srgb, var(--primary) 10%, transparent);
            --sb-radius: 22px;
        }
    </style>
</head>
<body>

{{-- ── Sidebar Overlay ── --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ── Mobile Sidebar ── --}}
<aside class="mobile-sidebar" id="mobileSidebar">
    <div class="sidebar-head">
        <img src="{{ asset('assets/frontend/img/logo.png') }}" alt="SajiloBhoj" style="height:38px;filter:brightness(10);">
        <button class="sidebar-close" onclick="closeSidebar()"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="sidebar-nav">
        @foreach($navMenuItems as $item)
        @php($navPath = trim(parse_url($item->url, PHP_URL_PATH) ?? '/', '/'))
        <a href="{{ $item->url }}" class="{{ Request::is($navPath === '' ? '/' : $navPath.'*') ? 'active' : '' }}"
           @if($item->is_highlighted) style="color: var(--sb-accent); border-left-color: var(--sb-accent);" @endif>
            <i class="bi {{ $item->icon ?: 'bi-link-45deg' }}"></i> {{ $item->label }}
        </a>
        @endforeach
        <div class="sidebar-divider"></div>
        @auth('customer')
            <a href="{{ route('frontend.account.index') }}" class="{{ Request::is('account*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> My Account
            </a>
            <form method="POST" action="{{ route('frontend.auth.logout') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0" style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:8px;width:100%;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        @else
            <a href="{{ route('frontend.auth.show_login_form') }}" class="{{ Request::is('login') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
        @endauth
    </nav>
    <div class="sidebar-foot">
        <button class="cart-fab w-100 justify-content-center" onclick="closeSidebar(); openCartModal()">
            <i class="bi bi-bag"></i>
            <span class="cart-btn-label">View Order</span>
            <span class="cart-badge" id="cartBadgeSidebar">0</span>
        </button>
    </div>
</aside>

{{-- ── Cart Slide-over Modal ── --}}
<div class="cart-modal-overlay" id="cartOverlay" onclick="closeCartModal()"></div>
<div class="cart-modal-panel" id="cartPanel">
    <div class="cart-modal-head">
        <div>
            <h5><i class="bi bi-bag me-2"></i>Your Order</h5>
            <div style="font-size:.78rem;opacity:.8;" id="cartHeadMeta">0 items · NRS 0</div>
        </div>
        <button class="cart-modal-close" onclick="closeCartModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="cart-modal-body" id="cartModalBody">
        <div class="cart-modal-empty">
            <i class="bi bi-bag"></i>
            <span class="fw-semibold">Your cart is empty</span>
            <span style="font-size:.83rem;">Browse our menu and add items.</span>
            <a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-primary btn-sm mt-2"
               onclick="closeCartModal()">
                <i class="bi bi-grid me-1"></i>View Menu
            </a>
        </div>
    </div>
    <div class="cart-modal-foot" id="cartModalFoot" style="display:none;">
        <div class="cart-total-row">
            <span class="text-muted">Subtotal</span>
            <span id="cartSubtotalModal" class="fw-semibold">NRS 0</span>
        </div>
        <div class="cart-total-row" style="margin-bottom:16px;">
            <span class="fw-bold" style="font-size:1rem;">Total</span>
            <strong id="cartGrandModal">NRS 0</strong>
        </div>
        <button class="btn btn-outline-danger w-100 mb-2 rounded-3" onclick="clearCart()">
            <i class="bi bi-trash me-1"></i>Clear Cart
        </button>
        <a href="{{ route('frontend.checkout.index') }}" class="btn btn-sb-primary w-100 rounded-3"
           onclick="closeCartModal()">
            <i class="bi bi-bag-check me-1"></i>Checkout
        </a>
    </div>
</div>

{{-- ── Topbar / Navbar ── --}}
<nav class="navbar topbar">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('frontend.home.index') }}">
            <img src="{{ asset('assets/frontend/img/logo.png') }}" alt="SajiloBhoj">
        </a>

        {{-- Desktop Nav --}}
        <ul class="navbar-nav mx-auto align-items-center desktop-nav main-nav">
            @foreach($navMenuItems as $item)
            @php($navPath = trim(parse_url($item->url, PHP_URL_PATH) ?? '/', '/'))
            <li class="nav-item">
                <a class="nav-link {{ Request::is($navPath === '' ? '/' : $navPath.'*') ? 'active' : '' }}"
                   href="{{ $item->url }}" @if($item->is_highlighted) style="color:var(--sb-accent);" @endif>{{ $item->label }}</a>
            </li>
            @endforeach
            @auth('customer')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>{{ Auth::guard('customer')->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('frontend.account.index') }}"><i class="bi bi-receipt me-1"></i>My Account</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('frontend.auth.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <li class="nav-item"><a class="nav-link {{ Request::is('login') || Request::is('register') ? 'active' : '' }}" href="{{ route('frontend.auth.show_login_form') }}">Sign In</a></li>
            @endauth
        </ul>

        {{-- Desktop cart --}}
        <div class="desktop-nav nav-cart">
            <button class="cart-fab" onclick="openCartModal()">
                <i class="bi bi-cart3"></i>
                <span class="cart-btn-label">Order</span>
                <span class="cart-badge" id="cartBadgeNav">0</span>
            </button>
        </div>

        {{-- Mobile right: cart + hamburger --}}
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <button class="cart-fab" onclick="openCartModal()" style="padding:8px 14px;">
                <i class="bi bi-cart3"></i>
                <span class="cart-badge" id="cartBadgeMobile">0</span>
            </button>
            <button class="hamburger" onclick="openSidebar()" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</nav>

@yield('content')

<footer class="footer">
    <svg class="footer-wave" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 38C200 8 420 4 700 30s520 36 740 6V90H0z" fill="var(--footer-light)"/>
        <path d="M0 58C260 30 520 34 780 52s460 20 660 0V90H0z" fill="var(--footer)"/>
    </svg>
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col footer-brand">
                    <svg class="footer-logo" viewBox="0 0 64 64" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M40 6 30 26M50 10 34 28"/>
                        <path d="M18 26c0-6 5-9 9-6 2-4 9-4 10 1"/>
                        <path d="M22 18c-2-4 0-8 4-9-1 4 1 7 4 8"/>
                        <path d="M6 30h52c0 14-11 24-26 24S6 44 6 30z"/>
                        <path d="M14 36c6 4 16 6 26 4"/>
                        <path d="M20 58h24"/>
                    </svg>
                    <div>
                        <div class="footer-name">{{ $settings['restaurant_name'] ?: 'SajiloBhoj Restaurant' }}</div>
                        <div class="footer-tagline">{{ $settings['tagline'] ?: 'Fresh taste, warm service, elegant dining.' }}</div>
                    </div>
                </div>

                <div class="footer-col footer-links">
                    <div class="footer-title">Quick Links</div>
                    <nav>
                        @foreach($navMenuItems as $item)
                        <a href="{{ $item->url }}">{{ $item->label }}</a>
                        @endforeach
                    </nav>
                </div>

                <div class="footer-col footer-copy">
                    <div>© {{ date('Y') }} {{ $settings['restaurant_name'] ?: 'SajiloBhoj' }}.</div>
                    <div>All rights reserved.</div>
                </div>

                <div class="footer-col footer-social">
                    @foreach ([
                        'facebook_url'  => ['bi-facebook',  'Facebook'],
                        'instagram_url' => ['bi-instagram', 'Instagram'],
                        'youtube_url'   => ['bi-youtube',   'YouTube'],
                        'twitter_url'   => ['bi-twitter-x', 'X'],
                    ] as $socialKey => $social)
                        @if (!empty($settings[$socialKey]))
                        <a href="{{ $settings[$socialKey] }}" target="_blank" rel="noopener" aria-label="{{ $social[1] }}"><i class="bi {{ $social[0] }}"></i></a>
                        @elseif ($socialKey !== 'twitter_url')
                        <a href="#" aria-label="{{ $social[1] }}"><i class="bi {{ $social[0] }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Toast notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
    <div id="cartToast" class="toast align-items-center text-white border-0"
         role="alert" style="background:var(--sb-green-dark);border-radius:14px;">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>
                <span id="cartToastItem"></span> added!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/frontend/js/script.js') }}"></script>
<script>
/* ════════════ Sidebar ════════════ */
function openSidebar() {
    document.getElementById('mobileSidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('mobileSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

/* ════════════ Cart modal ════════════ */
function openCartModal() {
    document.getElementById('cartPanel').classList.add('open');
    document.getElementById('cartOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeCartModal() {
    document.getElementById('cartPanel').classList.remove('open');
    document.getElementById('cartOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

/* ════════════ Cart (localStorage) ════════════ */
function getCart()  { try { return JSON.parse(localStorage.getItem('sb_cart') || '[]'); } catch { return []; } }
function saveCart(c){ localStorage.setItem('sb_cart', JSON.stringify(c)); renderCart(); }
function clearCart(){ saveCart([]); }

function addToCart(id, name, price, img) {
    const cart = getCart(), idx = cart.findIndex(i => i.id === id);
    if (idx >= 0) cart[idx].qty++;
    else cart.push({ id, name, price, img, qty: 1 });
    saveCart(cart);
    const t = document.getElementById('cartToast');
    if (t) {
        document.getElementById('cartToastItem').textContent = name;
        bootstrap.Toast.getOrCreateInstance(t, { delay: 2500 }).show();
    }
}

function removeFromCart(id) {
    saveCart(getCart().filter(i => i.id !== id));
}

function changeQty(id, delta) {
    const cart = getCart(), idx = cart.findIndex(i => i.id === id);
    if (idx < 0) return;
    cart[idx].qty = Math.max(1, cart[idx].qty + delta);
    saveCart(cart);
}

function renderCart() {
    const cart  = getCart();
    const count = cart.reduce((s, i) => s + i.qty, 0);
    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const totalNrs = 'NRS ' + Math.round(total * 133).toLocaleString(); // approx NRS

    // Badges
    ['cartBadgeNav','cartBadgeMobile','cartBadgeSidebar'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = count;
        el.classList.toggle('visible', count > 0);
    });

    // Button labels
    document.querySelectorAll('.cart-btn-label').forEach(el => {
        el.textContent = count > 0 ? `Order (${count})` : 'Order';
    });

    // Modal head meta
    const headMeta = document.getElementById('cartHeadMeta');
    if (headMeta) headMeta.textContent = `${count} item${count !== 1 ? 's' : ''} · ${totalNrs}`;

    // Modal body
    const body = document.getElementById('cartModalBody');
    const foot = document.getElementById('cartModalFoot');
    if (!body) return;

    if (!cart.length) {
        body.innerHTML = `<div class="cart-modal-empty">
            <i class="bi bi-bag"></i>
            <span class="fw-semibold">Your cart is empty</span>
            <span style="font-size:.83rem;">Browse our menu and add items.</span>
            <a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-primary btn-sm mt-2" onclick="closeCartModal()">
                <i class="bi bi-grid me-1"></i>View Menu
            </a>
        </div>`;
        if (foot) foot.style.display = 'none';
        return;
    }

    body.innerHTML = cart.map(i => `
        <div class="cart-item">
            <div class="cart-item-img">
                ${i.img ? `<img src="${i.img}" alt="${i.name}">` : `<i class="bi bi-egg-fried" style="font-size:1.4rem;color:var(--sb-green);"></i>`}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-name">${i.name}</div>
                <div class="cart-item-price">NRS ${Math.round(i.price * 133).toLocaleString()}</div>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
                <div class="qty-ctrl">
                    <button class="qty-btn" onclick="changeQty(${i.id},-1)">−</button>
                    <span class="qty-val">${i.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${i.id},1)">+</button>
                </div>
                <button onclick="removeFromCart(${i.id})"
                        style="border:none;background:none;color:#dc3545;font-size:.73rem;cursor:pointer;padding:0;">
                    Remove
                </button>
            </div>
        </div>`).join('');

    const subtotal = 'NRS ' + Math.round(total * 133).toLocaleString();
    if (foot) {
        foot.style.display = '';
        document.getElementById('cartSubtotalModal').textContent = subtotal;
        document.getElementById('cartGrandModal').textContent    = subtotal;
    }
}

document.addEventListener('DOMContentLoaded', renderCart);
</script>
@stack('scripts')
</body>
</html>
