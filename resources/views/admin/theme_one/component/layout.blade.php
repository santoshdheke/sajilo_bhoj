<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - RestoPro Admin</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.css" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}?v={{ filemtime(public_path('assets/backend/css/style.css')) }}" rel="stylesheet">
    <style>
        :root {
            --primary: {{ $themeColor ?? '#198754' }};
        }

        .modal-content{
            border: none;
        }

        .modal-header{
            background-color: var(--primary) !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>
        <div class="sidebar" id="sidebar">
            <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
            <a class="brand" href="{{ route('admin.dashboard.index') }}">
                @include('partials.brand-logo', ['variant' => 'light', 'height' => 44])
            </a>
            @if (! request()->routeIs('admin.settings.*'))
            <div class="section-title">Main</div>
            @if(auth()->user()?->hasPermission('dashboard.view'))
            <a class="nav-link{{ Request::is('admin/dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard.index') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a>
            @endif
            @if(auth()->user()?->hasPermission('tables.manage'))
            <a class="nav-link{{ Request::is('admin/tables') ? ' active' : '' }}" href="{{ route('admin.table.index') }}"><i class="bi bi-grid-3x3-gap"></i> Tables</a>
            @endif
            @if(auth()->user()?->hasPermission('orders.manage'))
            <a class="nav-link{{ Request::is('admin/orders') ? ' active' : '' }}" href="{{ route('admin.order.index') }}"><i class="bi bi-receipt"></i> Orders <span
                    class="badge">24</span></a>
            @endif
            @if(auth()->user()?->hasPermission('kitchen.manage'))
            <a class="nav-link{{ Request::is('admin/kitchen') ? ' active' : '' }}" href="{{ route('admin.kitchen.index') }}"><i class="bi bi-fire"></i> Kitchen Display</a>
            @endif
            @if(auth()->user()?->hasPermission('billing.view'))
            <a class="nav-link{{ Request::is('admin/billing') ? ' active' : '' }}" href="{{ route('admin.billing.index') }}"><i class="bi bi-wallet2"></i> Billing</a>
            @endif
            @if(auth()->user()?->hasPermission('menu.manage'))
            <a class="nav-link{{ request()->routeIs('admin.menu.*') || request()->routeIs('admin.menu_category.*') ? ' active' : '' }}" href="{{ route('admin.menu.index') }}"><i class="bi bi-journal-text"></i> Menu</a>
            @endif
            @if(auth()->user()?->hasPermission('suppliers.manage') || auth()->user()?->hasPermission('inventory.manage') || auth()->user()?->hasPermission('purchase_orders.manage'))
            <div class="section-title">Procurement</div>
            @endif
            @if(auth()->user()?->hasPermission('suppliers.manage'))
            <a class="nav-link{{ Request::is('admin/suppliers') ? ' active' : '' }}" href="{{ route('admin.supplier.index') }}"><i class="bi bi-truck"></i> Suppliers</a>
            @endif
            @if(auth()->user()?->hasPermission('inventory.manage'))
            <a class="nav-link{{ Request::is('admin/inventory*') ? ' active' : '' }}" href="{{ route('admin.inventory.index') }}"><i class="bi bi-box-seam"></i> Inventory</a>
            @endif
            @if(auth()->user()?->hasPermission('purchase_orders.manage'))
            <a class="nav-link{{ Request::is('admin/purchase-orders') ? ' active' : '' }}" href="{{ route('admin.purchase_order.index') }}"><i class="bi bi-receipt-cutoff"></i> Purchase Orders</a>
            @endif

            <div class="section-title">Management</div>
            @if(auth()->user()?->hasPermission('reports.view'))
            <a class="nav-link{{ Request::is('admin/report') ? ' active' : '' }}" href="{{ route("admin.report.index") }}"><i class="bi bi-bar-chart"></i> Reports</a>
            @endif
            @if(auth()->user()?->hasPermission('customers.manage'))
            <a class="nav-link{{ Request::is('admin/customers') ? ' active' : '' }}" href="{{ route('admin.customer.index') }}"><i class="bi bi-person-hearts"></i> Customers</a>
            @endif
            @if(auth()->user()?->hasPermission('payments.manage'))
            <a class="nav-link{{ Request::is('admin/payments*') ? ' active' : '' }}" href="{{ route('admin.payment.index') }}"><i class="bi bi-credit-card"></i> Payments</a>
            @endif
            @if(auth()->user()?->hasPermission('users.manage'))
            <a class="nav-link{{ Request::is('admin/user') ? ' active' : '' }}" href="{{ route("admin.user.index") }}"><i class="bi bi-people"></i> Users &amp; Roles</a>
            @endif
            @if(auth()->user()?->hasPermission('users.manage'))
            <a class="nav-link{{ request()->routeIs('admin.subscription.*') ? ' active' : '' }}" href="{{ route('admin.subscription.topup') }}"><i class="bi bi-wallet2"></i> Balance &amp; Top-up</a>
            @endif
            <a class="nav-link{{ Request::is('admin/profile') ? ' active' : '' }}" href="{{ route("admin.profile.index") }}"><i class="bi bi-person-circle"></i> Profile</a>
            @if(auth()->user()?->hasPermission('settings.manage'))
            <a class="nav-link{{ request()->routeIs('admin.settings.*') ? ' active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear"></i> Settings</a>
            @endif
            <div class="section-title">Account</div>
            <a class="nav-link" href="javascript:void(0)" onclick="document.getElementById('logout').submit()"><i class="bi bi-box-arrow-right"></i> Logout</a>
            @else
            <a class="nav-link" href="{{ route('admin.dashboard.index') }}"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <div class="section-title">Website Settings</div>
            @foreach (\App\Http\Controllers\Admin\SettingController::SECTIONS as $key => $sectionMeta)
            <a class="nav-link{{ request()->routeIs('admin.settings.index') && request()->route('section', 'general') === $key ? ' active' : '' }}"
               href="{{ route('admin.settings.index', $key) }}"><i class="bi {{ $sectionMeta['icon'] }}"></i> {{ $sectionMeta['label'] }}</a>
            @endforeach
            <a class="nav-link{{ request()->routeIs('admin.settings.navigation.*') ? ' active' : '' }}"
               href="{{ route('admin.settings.navigation.index') }}"><i class="bi bi-list-ul"></i> Navigation Menu</a>
            <div class="section-title">Account</div>
            <a class="nav-link" href="javascript:void(0)" onclick="document.getElementById('logout').submit()"><i class="bi bi-box-arrow-right"></i> Logout</a>
            @endif
            <form action="{{ route("admin.logout") }}" method="post" id="logout">
                @csrf
            </form>
            <svg class="sidebar-deco" viewBox="0 0 280 120" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0 70C70 40 150 40 280 10V120H0z" fill="rgba(255,255,255,.05)"/>
                <path d="M0 95C90 70 180 80 280 50V120H0z" fill="rgba(255,255,255,.05)"/>
                <g fill="rgba(255,255,255,.16)">
                    <path d="M236 108c-8-22 0-44 24-56 6 24-4 46-24 56z"/>
                    <path d="M222 110c-20-6-32-22-30-42 20 4 32 22 30 42z"/>
                </g>
            </svg>
        </div>
        {{-- Soft background decoration: leaves + cooking items --}}
        <div class="admin-deco" aria-hidden="true">
            <svg class="d-wave-br" viewBox="0 0 800 300" preserveAspectRatio="none"><path d="M0 300C200 220 420 260 800 120V300z"/><path class="w2" d="M200 300C380 250 560 270 800 200V300z"/></svg>
            <svg class="d-leaf d-leaf-1" viewBox="0 0 40 60"><path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z"/></svg>
            <svg class="d-leaf d-leaf-2" viewBox="0 0 40 60"><path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z"/></svg>
            <svg class="d-leaf d-leaf-3" viewBox="0 0 40 60"><path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z"/></svg>
        </div>
        <main class="main">
            <div class="topbar">
                <svg class="topbar-wave" viewBox="0 0 600 80" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M120 0C220 40 330 70 600 40V0z" fill="color-mix(in srgb, var(--primary) 10%, white)"/>
                    <path d="M260 0C340 30 440 46 600 18V0z" fill="color-mix(in srgb, var(--primary) 16%, white)"/>
                </svg>
                <div class="d-flex align-items-center gap-3 flex-grow-1 position-relative">
                    <button class="menu-btn" type="button" aria-label="Toggle menu"><i class="bi bi-list"></i></button>
                    <div class="search-box"><i class="bi bi-search"></i><input class="form-control"
                            placeholder="Search orders, tables, menu items..."></div>
                </div>
                <div class="top-actions position-relative">
                    @if (auth()->user()?->hasPermission('users.manage'))
                        @include('admin.theme_one.component._subscription-chip')
                    @endif
                    <button class="bell-btn" type="button" aria-label="Notifications"><i class="bi bi-bell"></i><span class="dot"></span></button>
                    <div class="dropdown">
                        <button class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar">{{ auth()->user()?->initials ?: 'U' }}</span>
                            <span class="text-start d-none d-sm-block">
                                <span class="d-block fw-semibold lh-sm">{{ auth()->user()?->name ?? 'User' }}</span>
                                <small class="text-secondary">{{ auth()->user()?->role?->name ?? 'Administrator' }}</small>
                            </span>
                            <i class="bi bi-chevron-down small ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('logout').submit()"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            @yield('content')

        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/backend/js/custom.js') }}"></script>
    <script>
    // Header menu button: collapse the sidebar on desktop, slide it in on mobile.
    // On mobile it closes again with the × button, a tap on the dimmed backdrop, Esc, or picking a link.
    function setMobileSidebar(open) {
        document.getElementById('sidebar').classList.toggle('show', open);
        document.body.classList.toggle('sb-open', open);
    }
    document.getElementById('sidebarClose')?.addEventListener('click', function () { setMobileSidebar(false); });
    document.getElementById('sidebarBackdrop')?.addEventListener('click', function () { setMobileSidebar(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && document.body.classList.contains('sb-open')) setMobileSidebar(false); });
    document.querySelectorAll('#sidebar a.nav-link[href]:not([href="#"])').forEach(function (a) {
        a.addEventListener('click', function () { if (window.innerWidth < 992) setMobileSidebar(false); });
    });
    window.addEventListener('resize', function () { if (window.innerWidth >= 992) setMobileSidebar(false); });
    document.querySelector('.menu-btn')?.addEventListener('click', function () {
        if (window.innerWidth < 992) { setMobileSidebar(!document.getElementById('sidebar').classList.contains('show')); }
        else { document.body.classList.toggle('sb-collapsed'); try { localStorage.setItem('sbCollapsed', document.body.classList.contains('sb-collapsed') ? '1' : ''); } catch (e) {} }
    });
    try { if (localStorage.getItem('sbCollapsed') === '1' && window.innerWidth >= 992) document.body.classList.add('sb-collapsed'); } catch (e) {}

    // Bootstrap modals must live directly under <body>, otherwise the backdrop can cover them.
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target.parentElement !== document.body) document.body.appendChild(e.target);
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.js"></script>
    <script>
    // Toast notifications (Notyf). Success uses the brand colour, errors stay red.
    (function () {
        var notyf = new Notyf({
            duration: 4000,
            dismissible: true,
            ripple: false,
            position: { x: 'right', y: 'top' },
            types: [
                { type: 'success', icon: { className: 'bi bi-check-circle-fill', tagName: 'i', color: '#fff' } },
                { type: 'error', background: '#dc2626', icon: { className: 'bi bi-exclamation-circle-fill', tagName: 'i', color: '#fff' } },
            ],
        });
        // Read the brand colour at show time, so toasts follow the (live) theme colour.
        function brand() { return getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#198754'; }
        // Skip the same message if it was just shown (e.g. page + global handler both report it).
        var last = { msg: '', at: 0 };
        function fresh(msg) { var now = Date.now(), dup = msg === last.msg && now - last.at < 2000; last = { msg: msg, at: now }; return !dup; }
        window.notify = {
            success: function (message) { if (fresh(message)) return notyf.open({ type: 'success', message: message, background: brand() }); },
            error:   function (message) { if (fresh(message)) return notyf.error(message); },
            dismissAll: function () { notyf.dismissAll(); },
        };
    })();
    @foreach (['status', 'success', 'password_success'] as $flashKey)
        @if (session($flashKey))
            notify.success(@json(session($flashKey)));
        @endif
    @endforeach
    @if (session('error'))
        notify.error(@json(session('error')));
    @endif
    </script>


    <script>
    // ── Toast for every admin action (save / update / delete) ──
    // Wraps fetch(): successful non-GET requests to /admin/... show a brand-coloured toast.
    // A page can opt out of a request by sending the header 'X-Silent-Toast: 1'.
    (function () {
        var NOUNS = {
            'bookings': 'Booking', 'chairs': 'Chair', 'customers': 'Customer',
            'inventory-categories': 'Category', 'inventory-items': 'Inventory item', 'kitchen/columns': 'Column',
            'kitchen/orders': 'Order', 'menu': 'Menu item', 'menu-category': 'Category', 'orders': 'Order',
            'payments': 'Payment', 'purchase-orders': 'Purchase order', 'rooms': 'Room', 'settings/navigation': 'Menu link',
            'suppliers': 'Supplier', 'table-photos': 'Photo', 'tables': 'Table', 'user': 'User'
        };
        var PENDING = 'admin-pending-toast';

        function headerOf(init, name) {
            var h = init && init.headers; if (!h) return null;
            if (typeof h.get === 'function') return h.get(name);
            for (var k in h) { if (k.toLowerCase() === name.toLowerCase()) return h[k]; }
            return null;
        }
        function bodyKeys(body) {
            if (!body) return [];
            if (body instanceof FormData) { var ks = []; body.forEach(function (v, k) { ks.push(k); }); return ks; }
            if (typeof body === 'string') { try { return Object.keys(JSON.parse(body)); } catch (e) { return []; } }
            return [];
        }
        function spoofed(body) {
            if (body instanceof FormData && body.get('_method')) return String(body.get('_method')).toUpperCase();
            if (typeof body === 'string') { try { var j = JSON.parse(body); if (j._method) return String(j._method).toUpperCase(); } catch (e) {} }
            return null;
        }
        function describe(url, method, body) {
            var path = new URL(url, location.href).pathname.replace(/^\/admin\//, '').replace(/\/$/, '');
            var parts = path.split('/'), noun = null, rest = [];
            for (var n = Math.min(parts.length, 2); n >= 1; n--) {
                var key = parts.slice(0, n).join('/');
                if (NOUNS[key]) { noun = NOUNS[key]; rest = parts.slice(n); break; }
            }
            noun = noun || 'Item';
            var last = rest[rest.length - 1] || '', hasId = rest.some(function (p) { return /^\d+$/.test(p); });
            var keys = bodyKeys(body).filter(function (k) { return k !== '_token' && k !== '_method'; });

            if (last === 'status')  return noun + ' status updated.';
            if (last === 'toggle')  return noun + ' updated.';
            if (last === 'reorder') return 'New order saved.';
            if (/^photos?$/.test(last) && method !== 'DELETE') return 'Photo uploaded.';
            if (keys.length && keys.every(function (k) { return /^(pos_x|pos_y|room_id|table_id|x|y|rotation)$/.test(k); })) return 'Layout saved.';
            if (method === 'DELETE') return noun + ' deleted.';
            if (method === 'PUT' || method === 'PATCH' || (method === 'POST' && hasId)) return noun + ' updated.';
            return noun + ' added.';
        }
        function show(type, msg) {
            if (!window.notify || !msg) return;
            notify[type](msg);
            try { sessionStorage.setItem(PENDING, JSON.stringify({ type: type, msg: msg, ts: Date.now() })); } catch (e) {}
        }

        // Re-show a toast whose page reloaded right after the action.
        try {
            var pending = JSON.parse(sessionStorage.getItem(PENDING) || 'null');
            sessionStorage.removeItem(PENDING);
            if (pending && Date.now() - pending.ts < 2500) notify[pending.type](pending.msg);
        } catch (e) {}

        var nativeFetch = window.fetch.bind(window);
        window.fetch = function (input, init) {
            var url = typeof input === 'string' ? input : (input && input.url) || '';
            var method = ((init && init.method) || (input && input.method) || 'GET').toUpperCase();
            var body = init && init.body;
            if (method === 'POST') method = spoofed(body) || 'POST';
            var watch = method !== 'GET' && method !== 'HEAD' && /\/admin\//.test(new URL(url, location.href).pathname)
                        && headerOf(init, 'X-Silent-Toast') !== '1';

            var p = nativeFetch(input, init);
            if (!watch) return p;
            return p.then(function (res) {
                var copy = res.clone();
                if (res.ok) {
                    copy.json().then(function (d) {
                        if (d && d.success === false) show('error', d.message || 'Action could not be completed.');
                        else show('success', (d && typeof d.message === 'string' && d.message) || describe(url, method, body));
                    }).catch(function () { show('success', describe(url, method, body)); });
                } else if (res.status !== 422) {
                    copy.json().then(function (d) { return d && d.message; }).catch(function () { return null; }).then(function (m) {
                        var fallback = res.status === 413 ? 'This file is too large to upload. Please choose a smaller file.'
                                     : res.status === 419 ? 'Your session expired. Please refresh the page.'
                                     : res.status === 403 ? 'You do not have permission to do that.'
                                     : 'Something went wrong. Please try again.';
                        show('error', (res.status === 413 || res.status === 419 || res.status >= 500) ? fallback : (m || fallback));
                    });
                }
                return res;
            });
        };
    })();
    </script>

    {{-- ── Floating Chat Support (all admin pages) ── --}}
    @if (auth()->user()?->hasPermission('dashboard.view'))
    <div class="cw" id="chatWidget">
        <div class="cw-panel" id="cwPanel" role="dialog" aria-label="Chat Support" hidden>
            <div class="cw-head">
                <h5><i class="bi bi-chat-dots-fill"></i>Chat Support</h5>
                <button type="button" id="cwClose" aria-label="Close chat">&minus;</button>
            </div>
            <div class="cw-body" id="cwBody">
                <div class="cw-msg bot">
                    <span class="av"><i class="bi bi-robot"></i></span>
                    <div class="bubble">Hello! 👋<br>How can I help you today?<time id="cwHelloTime"></time></div>
                </div>
            </div>
            <div class="cw-chips" id="cwChips">
                <button type="button">Today's sales</button>
                <button type="button">Orders today</button>
                <button type="button">Tables</button>
                <button type="button">Kitchen</button>
                <button type="button">Top items</button>
            </div>
            <form class="cw-foot" id="cwForm" autocomplete="off">
                <input type="text" id="cwInput" placeholder="Type a message..." aria-label="Message">
                <button type="submit" aria-label="Send"><i class="bi bi-send-fill"></i></button>
            </form>
        </div>
        <button type="button" class="cw-fab" id="cwFab" aria-label="Open chat support" aria-expanded="false">
            <i class="bi bi-chat-dots-fill cw-ico-open"></i><i class="bi bi-x-lg cw-ico-close"></i>
        </button>
    </div>
    <script>
    (function () {
        var factsUrl = @json(route('admin.dashboard.chat_facts'));
        var initials = @json(auth()->user()?->initials ?: 'U');
        var widget = document.getElementById('chatWidget'), panel = document.getElementById('cwPanel'),
            fab = document.getElementById('cwFab'), body = document.getElementById('cwBody'), facts = null;

        function esc(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
        function time() { return new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }); }
        document.getElementById('cwHelloTime').textContent = time();

        function loadFacts() {
            return fetch(factsUrl, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (d) { facts = d; return d; });
        }
        function setOpen(open) {
            widget.classList.toggle('open', open);
            panel.hidden = !open;
            fab.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) { loadFacts().catch(function () {}); setTimeout(function () { document.getElementById('cwInput').focus(); }, 50); }
        }
        fab.addEventListener('click', function () { setOpen(!widget.classList.contains('open')); });
        document.getElementById('cwClose').addEventListener('click', function () { setOpen(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && widget.classList.contains('open')) setOpen(false); });

        function add(who, html) {
            var el = document.createElement('div');
            el.className = 'cw-msg ' + who;
            el.innerHTML = '<span class="av">' + (who === 'bot' ? '<i class="bi bi-robot"></i>' : esc(initials)) + '</span>'
                         + '<div class="bubble">' + html + '<time>' + time() + '</time></div>';
            body.appendChild(el);
            body.scrollTop = body.scrollHeight;
        }
        function big(label, value) { return label + '<span class="big">' + esc(String(value)) + '</span>'; }

        // Answers simple questions from today's live numbers.
        function reply(q, f) {
            q = q.toLowerCase();
            if (/sale|revenue|earn|income/.test(q)) return /month/.test(q) ? big("This month's revenue is:", f.month) : big("Today's sales is:", f.sales);
            if (/order/.test(q))                 return big('Orders today:', f.orders);
            if (/table|occup|seat/.test(q))      return big('Occupied tables:', f.tables);
            if (/kitchen|pending|cook|queue/.test(q)) return big('Orders pending in the kitchen:', f.kitchen);
            if (/top|best|popular|item/.test(q)) return f.top ? 'Top selling items: ' + esc(f.top) : 'No sales data yet.';
            if (/average|avg|bill/.test(q))      return big('Average bill:', f.avgBill);
            if (/customer/.test(q))              return big('New customers this month:', f.customers);
            if (/^(hi|hello|hey|namaste)\b/.test(q)) return 'Hi! Ask me about sales, orders, tables, kitchen or top items.';
            return "Sorry, I can only answer about today's sales, orders, tables, kitchen, top items, average bill and customers.";
        }
        function ask(text) {
            text = text.trim(); if (!text) return;
            add('me', esc(text));
            // Always fetch fresh numbers so answers stay current.
            loadFacts().then(function (f) { add('bot', reply(text, f)); })
                       .catch(function () { add('bot', 'Sorry, I could not load the latest numbers. Please try again.'); });
        }
        document.getElementById('cwForm').addEventListener('submit', function (e) {
            e.preventDefault(); var i = document.getElementById('cwInput'); ask(i.value); i.value = '';
        });
        document.querySelectorAll('#cwChips button').forEach(function (b) { b.addEventListener('click', function () { ask(b.textContent); }); });
    })();
    </script>
    @endif
    <script>
    // Phones: list tables turn into one card per row. Give every cell its column name
    // (data-label) so the CSS can show it beside the value. Rows added later by page scripts are labelled too.
    (function () {
        function label() {
            document.querySelectorAll('.main table.table:not(.no-stack)').forEach(function (t) {
                if (t.closest('.modal')) return;
                var heads = Array.prototype.map.call(t.querySelectorAll('thead th'), function (th) { return th.textContent.trim(); });
                if (!heads.length) return;
                t.classList.add('table-stack');
                t.querySelectorAll('tbody tr').forEach(function (tr) {
                    var i = 0;
                    Array.prototype.forEach.call(tr.children, function (td) {
                        if (!td.hasAttribute('data-label') && !td.hasAttribute('colspan')) td.setAttribute('data-label', heads[i] || '');
                        i += td.colSpan || 1;
                    });
                });
            });
        }
        label();
        var main = document.querySelector('.main'), timer;
        if (main && window.MutationObserver) {
            new MutationObserver(function () { clearTimeout(timer); timer = setTimeout(label, 50); })
                .observe(main, { childList: true, subtree: true });
        }
    })();
    </script>
    @stack('js')
</body>

</html>
