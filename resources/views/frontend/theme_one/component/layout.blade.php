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
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}">
    <style>
        :root {
            --primary: {{ $settings['theme_color'] ?? '#198754' }};
            --green: color-mix(in srgb, var(--primary) 80%, black);
            --green-two: color-mix(in srgb, var(--primary) 65%, black);
            --footer: color-mix(in srgb, var(--primary) 62%, black);
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
        <a href="{{ route('frontend.home.index') }}" class="{{ Request::is('/') ? 'active' : '' }}">
            <i class="bi bi-house"></i> Home
        </a>
        <a href="{{ route('frontend.about.index') }}" class="{{ Request::is('about') ? 'active' : '' }}">
            <i class="bi bi-info-circle"></i> About
        </a>
        <a href="{{ route('frontend.menu.index') }}" class="{{ Request::is('menu*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i> Menu
        </a>
        <a href="{{ route('frontend.table.index') }}" class="{{ Request::is('table*') ? 'active' : '' }}"
           style="color: var(--sb-accent); border-left-color: var(--sb-accent);">
            <i class="bi bi-calendar-check"></i> Book a Table
        </a>
        <a href="{{ route('frontend.contact.index') }}" class="{{ Request::is('contact') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i> Contact
        </a>
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
        <ul class="navbar-nav ms-auto align-items-center desktop-nav" style="flex-direction:row;gap:4px;">
            <li class="nav-item"><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ route('frontend.home.index') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('about') ? 'active' : '' }}" href="{{ route('frontend.about.index') }}">About</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('menu*') ? 'active' : '' }}" href="{{ route('frontend.menu.index') }}">Menu</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('table*') ? 'active' : '' }}" href="{{ route('frontend.table.index') }}" style="color:var(--sb-accent);">Book a Table</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="{{ route('frontend.contact.index') }}">Contact</a></li>
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
            <li class="nav-item ms-2">
                <button class="cart-fab" onclick="openCartModal()">
                    <i class="bi bi-bag"></i>
                    <span class="cart-btn-label">Order</span>
                    <span class="cart-badge" id="cartBadgeNav">0</span>
                </button>
            </li>
        </ul>

        {{-- Mobile right: cart + hamburger --}}
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <button class="cart-fab" onclick="openCartModal()" style="padding:8px 12px;">
                <i class="bi bi-bag"></i>
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
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-start gap-3">
                    <img src="{{ asset('assets/frontend/img/logo.png') }}" alt="SajiloBhoj"
                         style="height:44px;filter:grayscale(100%);flex-shrink:0;">
                    <div>
                        <div class="fw-bold">{{ $settings['restaurant_name'] ?: 'SajiloBhoj Restaurant' }}</div>
                        <div class="small" style="opacity:.75;margin-top:4px;font-size:.82rem;">
                            {{ $settings['tagline'] ?: 'Fresh taste, warm service, elegant dining. Your neighbourhood restaurant with authentic flavours and a welcoming atmosphere for every occasion.' }}
                        </div>
                        @if($settings['contact_address'] || $settings['contact_phone'] || $settings['contact_email'])
                        <div class="small mt-2" style="opacity:.7;font-size:.8rem;">
                            @if($settings['contact_address'])<div><i class="bi bi-geo-alt me-1"></i>{{ $settings['contact_address'] }}</div>@endif
                            @if($settings['contact_phone'])<div><i class="bi bi-telephone me-1"></i>{{ $settings['contact_phone'] }}</div>@endif
                            @if($settings['contact_email'])<div><i class="bi bi-envelope me-1"></i>{{ $settings['contact_email'] }}</div>@endif
                        </div>
                        @endif
                        @if($settings['facebook_url'] || $settings['instagram_url'] || $settings['twitter_url'])
                        <div class="d-flex gap-2 mt-2">
                            @if($settings['facebook_url'])<a href="{{ $settings['facebook_url'] }}" target="_blank" style="color:rgba(255,255,255,.7);"><i class="bi bi-facebook"></i></a>@endif
                            @if($settings['instagram_url'])<a href="{{ $settings['instagram_url'] }}" target="_blank" style="color:rgba(255,255,255,.7);"><i class="bi bi-instagram"></i></a>@endif
                            @if($settings['twitter_url'])<a href="{{ $settings['twitter_url'] }}" target="_blank" style="color:rgba(255,255,255,.7);"><i class="bi bi-twitter-x"></i></a>@endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="fw-semibold mb-2" style="font-size:.88rem;">Quick Links</div>
                <div class="d-flex flex-column gap-1" style="font-size:.82rem;">
                    <a href="{{ route('frontend.menu.index') }}" style="color:rgba(255,255,255,.7);">Our Menu</a>
                    <a href="{{ route('frontend.table.index') }}" style="color:rgba(255,255,255,.7);">Book a Table</a>
                    <a href="{{ route('frontend.about.index') }}" style="color:rgba(255,255,255,.7);">About Us</a>
                    <a href="{{ route('frontend.contact.index') }}" style="color:rgba(255,255,255,.7);">Contact</a>
                </div>
            </div>
            <div class="col-lg-3 text-lg-end">
                <div class="small" style="opacity:.7;">© {{ date('Y') }} {{ $settings['restaurant_name'] ?: 'SajiloBhoj' }}.</div>
                <div class="small" style="opacity:.55;margin-top:3px;">All rights reserved.</div>
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
