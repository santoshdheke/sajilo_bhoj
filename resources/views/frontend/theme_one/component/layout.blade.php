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
        @include('partials.brand-logo', ['variant' => 'white', 'height' => 36])
        <button class="sidebar-close" onclick="closeSidebar()"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="sidebar-nav">
        @foreach($navMenuItems as $item)
        @php($navPath = trim(parse_url($item->url, PHP_URL_PATH) ?? '/', '/'))
        <a href="{{ $item->url }}" class="{{ Request::is($navPath === '' ? '/' : $navPath.'*') ? 'active' : '' }}">
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
            @include('partials.brand-logo', ['variant' => 'color', 'height' => 46])
        </a>

        {{-- Desktop Nav --}}
        <ul class="navbar-nav mx-auto align-items-center desktop-nav main-nav">
            @foreach($navMenuItems as $item)
            @php($navPath = trim(parse_url($item->url, PHP_URL_PATH) ?? '/', '/'))
            <li class="nav-item">
                <a class="nav-link {{ Request::is($navPath === '' ? '/' : $navPath.'*') ? 'active' : '' }}"
                   href="{{ $item->url }}">@if ($item->icon)<i class="bi {{ $item->icon }}"></i>@endif{{ $item->label }}</a>
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
                <li class="nav-item"><a class="nav-link {{ Request::is('login') || Request::is('register') ? 'active' : '' }}" href="{{ route('frontend.auth.show_login_form') }}"><i class="bi bi-person"></i>Sign In</a></li>
            @endauth
        </ul>

        {{-- Desktop cart --}}
        <div class="desktop-nav nav-cart">
            <button class="cart-fab" onclick="openCartModal()">
                <i class="bi bi-cart3"></i>
                <span class="cart-btn-label">Order</span>
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
        <svg class="footer-leaves footer-leaves--left" viewBox="0 0 120 140" aria-hidden="true">
            <path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z" transform="translate(10 40) rotate(-35 20 30) scale(1.4)"/>
            <path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z" transform="translate(48 70) rotate(20 20 30)" opacity=".7"/>
            <path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z" transform="translate(0 92) rotate(-70 20 30) scale(.9)" opacity=".55"/>
        </svg>
        <svg class="footer-leaves footer-leaves--right" viewBox="0 0 120 140" aria-hidden="true">
            <path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z" transform="translate(56 30) rotate(35 20 30) scale(1.4)"/>
            <path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z" transform="translate(30 76) rotate(-20 20 30)" opacity=".7"/>
            <path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z" transform="translate(78 92) rotate(70 20 30) scale(.9)" opacity=".55"/>
        </svg>
        <div class="container position-relative">
            <div class="footer-grid {{ $socialLinks->isEmpty() ? 'footer-grid--3' : '' }}">
                <div class="footer-col footer-brand">
                    <span class="footer-logo">
                        @if (!empty($brandLogoUrl))
                            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}">
                        @else
                            @include('partials.brand-logo', ['variant' => 'white', 'height' => 48, 'showName' => false])
                        @endif
                    </span>
                    <div>
                        <div class="footer-name">{{ $brandName }}</div>
                        <div class="footer-tagline">{{ $settings['tagline'] ?: 'Fresh taste, warm service, elegant dining.' }}</div>
                    </div>
                </div>

                <div class="footer-col footer-links">
                    <div class="footer-title">Quick Links</div>
                    <div class="footer-nav-clip">
                        <nav>
                            @foreach($navMenuItems as $item)
                            <a href="{{ $item->url }}">{{ $item->label }}</a>
                            @endforeach
                        </nav>
                    </div>
                </div>

                <div class="footer-col footer-contact">
                    <div class="footer-title">Contact Us</div>
                    <ul>
                        @if ($settings['contact_phone'])<li><i class="bi bi-telephone-fill"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}">{{ $settings['contact_phone'] }}</a></li>@endif
                        @if ($settings['contact_email'])<li><i class="bi bi-envelope-fill"></i><a href="mailto:{{ $settings['contact_email'] }}">{{ $settings['contact_email'] }}</a></li>@endif
                        @if ($settings['contact_address'])<li><i class="bi bi-geo-alt-fill"></i><span>{{ $settings['contact_address'] }}</span></li>@endif
                        @if (!$settings['contact_phone'] && !$settings['contact_email'] && !$settings['contact_address'])
                            <li><i class="bi bi-chat-dots-fill"></i><a href="{{ route('frontend.contact.index') }}">Get in touch</a></li>
                        @endif
                    </ul>
                </div>

                @if ($socialLinks->isNotEmpty())
                <div class="footer-col footer-follow">
                    <div class="footer-title">Follow Us</div>
                    <div class="footer-social">
                        @foreach ($socialLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" aria-label="{{ $link['label'] }}" title="{{ $link['label'] }}"><i class="bi {{ $link['icon'] }}"></i></a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="footer-bottom">
                <span>© {{ date('Y') }} {{ $brandName }}. All rights reserved.</span>
                <span>Made with <i class="bi bi-heart-fill"></i> in Nepal</span>
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
    syncCart();
    document.getElementById('cartPanel').classList.add('open');
    document.getElementById('cartOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeCartModal() {
    cartNotice = '';
    document.getElementById('cartPanel').classList.remove('open');
    document.getElementById('cartOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

/* ════════════ Cart (stored in the browser, kept in sync with the live menu) ════════════ */
const CART_KEY  = 'sb_cart';
const CART_SYNC = @json(route('frontend.cart.items'));
const CURRENCY  = @json($currencySymbol ?? 'Rs.');

function money(v) {
    v = Number(v) || 0;
    return CURRENCY + ' ' + v.toLocaleString(undefined, { minimumFractionDigits: Number.isInteger(v) ? 0 : 2, maximumFractionDigits: 2 });
}
function escHtml(t) { const d = document.createElement('div'); d.textContent = t == null ? '' : String(t); return d.innerHTML; }

function getCart() {
    try {
        const c = JSON.parse(localStorage.getItem(CART_KEY) || '[]');
        return Array.isArray(c) ? c.filter(i => i && Number.isInteger(i.id) && i.qty > 0) : [];
    } catch { return []; }
}
function saveCart(c) {
    try { localStorage.setItem(CART_KEY, JSON.stringify(c)); } catch (e) {}
    renderCart();
    document.dispatchEvent(new CustomEvent('cart:updated', { detail: c }));
}
function clearCart() { saveCart([]); }

function addToCart(id, name, price, img) {
    id = Number(id);
    const cart = getCart(), idx = cart.findIndex(i => i.id === id);
    if (idx >= 0) cart[idx].qty = Math.min(99, cart[idx].qty + 1);
    else cart.push({ id, name, price: Number(price), img: img || '', qty: 1 });
    saveCart(cart);
    const t = document.getElementById('cartToast');
    if (t) {
        document.getElementById('cartToastItem').textContent = name;
        bootstrap.Toast.getOrCreateInstance(t, { delay: 2500 }).show();
    }
}
function removeFromCart(id) { saveCart(getCart().filter(i => i.id !== id)); }
function changeQty(id, delta) {
    const cart = getCart(), idx = cart.findIndex(i => i.id === id);
    if (idx < 0) return;
    cart[idx].qty = Math.min(99, Math.max(1, cart[idx].qty + delta));
    saveCart(cart);
}

// Refresh names, prices and photos from the live menu; drop items that were removed or switched off.
let cartNotice = '';
async function syncCart() {
    const cart = getCart();
    if (!cart.length) return;
    try {
        const res = await fetch(CART_SYNC + '?ids=' + cart.map(i => i.id).join(','), { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const live = new Map((await res.json()).items.map(i => [i.id, i]));
        let removed = 0, changed = 0;
        const next = [];
        cart.forEach(i => {
            const l = live.get(i.id);
            if (!l) { removed++; return; }
            if (l.price !== Number(i.price) || l.name !== i.name || l.img !== (i.img || '')) changed++;
            next.push({ id: l.id, name: l.name, price: l.price, img: l.img, qty: i.qty });
        });
        if (removed || changed) {
            cartNotice = [removed ? `${removed} item${removed > 1 ? 's are' : ' is'} no longer available and ${removed > 1 ? 'were' : 'was'} removed.` : '',
                          changed ? 'Prices and details were updated to the current menu.' : ''].filter(Boolean).join(' ');
            saveCart(next);
        }
    } catch (e) { /* offline: keep the stored cart */ }
}

function renderCart() {
    const cart  = getCart();
    const count = cart.reduce((s, i) => s + i.qty, 0);
    const total = cart.reduce((s, i) => s + Number(i.price) * i.qty, 0);

    ['cartBadgeMobile'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = count;
        el.classList.toggle('visible', count > 0);
    });
    document.querySelectorAll('.cart-btn-label').forEach(el => {
        el.textContent = count > 0 ? `Order (${count})` : 'Order';
    });

    const headMeta = document.getElementById('cartHeadMeta');
    if (headMeta) headMeta.textContent = `${count} item${count !== 1 ? 's' : ''} · ${money(total)}`;

    const body = document.getElementById('cartModalBody');
    const foot = document.getElementById('cartModalFoot');
    if (!body) return;

    const notice = cartNotice ? `<div class="cart-notice"><i class="bi bi-info-circle"></i><span>${escHtml(cartNotice)}</span></div>` : '';

    if (!cart.length) {
        body.innerHTML = notice + `<div class="cart-modal-empty">
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

    body.innerHTML = notice + cart.map(i => `
        <div class="cart-item">
            <div class="cart-item-img">
                ${i.img ? `<img src="${escHtml(i.img)}" alt="${escHtml(i.name)}" onerror="this.replaceWith(Object.assign(document.createElement('i'),{className:'bi bi-egg-fried'}))">` : `<i class="bi bi-egg-fried" style="font-size:1.4rem;color:var(--sb-green);"></i>`}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-name">${escHtml(i.name)}</div>
                <div class="cart-item-price">${money(i.price)}${i.qty > 1 ? ` <small class="text-muted">× ${i.qty} = ${money(i.price * i.qty)}</small>` : ''}</div>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
                <div class="qty-ctrl">
                    <button class="qty-btn" onclick="changeQty(${i.id},-1)" aria-label="Decrease">−</button>
                    <span class="qty-val">${i.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${i.id},1)" aria-label="Increase">+</button>
                </div>
                <button onclick="removeFromCart(${i.id})"
                        style="border:none;background:none;color:#dc3545;font-size:.73rem;cursor:pointer;padding:0;">
                    Remove
                </button>
            </div>
        </div>`).join('');

    if (foot) {
        foot.style.display = '';
        document.getElementById('cartSubtotalModal').textContent = money(total);
        document.getElementById('cartGrandModal').textContent    = money(total);
    }
}

document.addEventListener('DOMContentLoaded', () => { renderCart(); syncCart(); });
window.addEventListener('storage', e => { if (e.key === CART_KEY) renderCart(); });  // keep other tabs in step
</script>
@stack('scripts')
</body>
</html>
