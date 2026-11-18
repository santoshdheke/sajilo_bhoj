<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $shopName }}</title>
    @include('shop::partials.favicon', ['color' => $themeColor, 'logoUrl' => $shopLogoUrl])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.css" rel="stylesheet">
    <link href="{{ asset('shop/backend/css/style.css') }}?v={{ filemtime(public_path('shop/backend/css/style.css')) }}" rel="stylesheet">
    <style>
        :root { --primary: {{ $themeColor }}; }
        .modal-content { border: none; }
        .modal-header { background-color: var(--primary) !important; color: #fff !important; }

        /* Shared shop back-office bits */
        .tbl-action { width: 30px; height: 30px; border: none; border-radius: 9px; font-size: .82rem; cursor: pointer;
                      display: inline-flex; align-items: center; justify-content: center; transition: .12s; text-decoration: none; }
        .tbl-action.view { background: #eefaf3; color: #15803d; }  .tbl-action.view:hover { background: #15803d; color: #fff; }
        .tbl-action.edit { background: #eef0ff; color: #3730a3; }  .tbl-action.edit:hover { background: #3730a3; color: #fff; }
        .tbl-action.del  { background: #fff0f0; color: #dc3545; }  .tbl-action.del:hover  { background: #dc3545; color: #fff; }
        .form-panel { position: sticky; top: 24px; }
        .empty-row { text-align: center; padding: 48px 12px !important; color: #6b7280; }
        .empty-row i { font-size: 2rem; display: block; margin-bottom: 8px; opacity: .35; }
        .pill { display: inline-flex; align-items: center; gap: 4px; border-radius: 999px; padding: 3px 10px; font-size: .75rem; font-weight: 600; }
        .pill-green { background: #dcfce7; color: #166534; } .pill-red { background: #fee2e2; color: #991b1b; }
        .pill-amber { background: #fef3c7; color: #92400e; } .pill-blue { background: #dbeafe; color: #1e40af; }
        .pill-gray { background: #f1f5f9; color: #475569; } .pill-purple { background: #ede9fe; color: #5b21b6; }
        .thumb { width: 42px; height: 42px; border-radius: 10px; object-fit: cover; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; }
        .page-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 12px; }
        .money { font-variant-numeric: tabular-nums; white-space: nowrap; }
        .filter-bar .form-control, .filter-bar .form-select { min-width: 150px; }
        .pagination { margin: 0; }
    </style>
    @stack('css')
</head>

<body>
    <div class="layout">
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>
        <div class="sidebar" id="sidebar">
            <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
            <a class="brand d-flex align-items-center gap-2 text-white text-decoration-none" href="{{ route('shop.admin.dashboard.index') }}">
                @if ($shopLogoUrl)
                    <img src="{{ $shopLogoUrl }}" alt="" style="height:40px;width:40px;border-radius:10px;object-fit:cover;">
                @else
                    <span class="d-inline-flex align-items-center justify-content-center" style="height:40px;width:40px;border-radius:12px;background:rgba(255,255,255,.15);"><i class="bi bi-shop fs-5"></i></span>
                @endif
                <span class="fw-bold text-truncate" style="max-width:170px;">{{ $shopName }}</span>
            </a>

            @php($is = fn (...$names) => request()->routeIs(...$names) ? ' active' : '')
            @if (! request()->routeIs('shop.admin.setting.*'))
            <div class="section-title">Main</div>
            <a class="nav-link{{ $is('shop.admin.dashboard.*') }}" href="{{ route('shop.admin.dashboard.index') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a>
            <a class="nav-link{{ $is('shop.admin.product.sell') }}" href="{{ route('shop.admin.product.sell') }}"><i class="bi bi-upc-scan"></i> POS / Sell</a>
            <a class="nav-link{{ $is('shop.admin.order.*', 'shop.admin.product.invoice') }}" href="{{ route('shop.admin.order.index') }}"><i class="bi bi-receipt"></i> Orders</a>

            <div class="section-title">Catalog</div>
            <a class="nav-link{{ $is('shop.admin.product.index', 'shop.admin.product.create', 'shop.admin.product.edit') }}" href="{{ route('shop.admin.product.index') }}"><i class="bi bi-box"></i> Products</a>
            <a class="nav-link{{ $is('shop.admin.category.*') }}" href="{{ route('shop.admin.category.index') }}"><i class="bi bi-diagram-3"></i> Categories</a>
            <a class="nav-link{{ $is('shop.admin.brand.*') }}" href="{{ route('shop.admin.brand.index') }}"><i class="bi bi-award"></i> Brands</a>
            <a class="nav-link{{ $is('shop.admin.attribute.*') }}" href="{{ route('shop.admin.attribute.index') }}"><i class="bi bi-sliders"></i> Attributes</a>

            <div class="section-title">Homepage</div>
            <a class="nav-link{{ $is('shop.admin.banner.*') }}" href="{{ route('shop.admin.banner.index') }}"><i class="bi bi-images"></i> Banners</a>
            <a class="nav-link{{ $is('shop.admin.offer.*') }}" href="{{ route('shop.admin.offer.index') }}"><i class="bi bi-gift"></i> Special offers</a>
            <a class="nav-link{{ $is('shop.admin.section.*') }}" href="{{ route('shop.admin.section.index') }}"><i class="bi bi-layout-text-window"></i> Home sections</a>

            <div class="section-title">Stock</div>
            <a class="nav-link{{ $is('shop.admin.inventory.*') }}" href="{{ route('shop.admin.inventory.index') }}"><i class="bi bi-box-seam"></i> Inventory</a>
            <a class="nav-link{{ $is('shop.admin.supplier.purchases', 'shop.admin.supplier.purchase.*') }}" href="{{ route('shop.admin.supplier.purchases') }}"><i class="bi bi-cart-plus"></i> Purchases</a>
            <a class="nav-link{{ $is('shop.admin.supplier.index', 'shop.admin.supplier.show') }}" href="{{ route('shop.admin.supplier.index') }}"><i class="bi bi-truck"></i> Suppliers</a>

            <div class="section-title">Customers</div>
            <a class="nav-link{{ $is('shop.admin.customer.index', 'shop.admin.customer.show') }}" href="{{ route('shop.admin.customer.index') }}"><i class="bi bi-person-hearts"></i> Customers</a>
            <a class="nav-link{{ $is('shop.admin.customer.credit') }}" href="{{ route('shop.admin.customer.credit') }}"><i class="bi bi-journal-text"></i> Credit (Udhar)</a>

            <div class="section-title">Reports</div>
            <a class="nav-link{{ $is('shop.admin.report.sales') }}" href="{{ route('shop.admin.report.sales') }}"><i class="bi bi-graph-up-arrow"></i> Sales</a>
            <a class="nav-link{{ $is('shop.admin.report.stock') }}" href="{{ route('shop.admin.report.stock') }}"><i class="bi bi-clipboard-data"></i> Stock</a>
            <a class="nav-link{{ $is('shop.admin.report.profit') }}" href="{{ route('shop.admin.report.profit') }}"><i class="bi bi-pie-chart"></i> Profit</a>
            <a class="nav-link{{ $is('shop.admin.report.expenses*') }}" href="{{ route('shop.admin.report.expenses') }}"><i class="bi bi-wallet2"></i> Expenses</a>

            <div class="section-title">Management</div>
            <a class="nav-link{{ $is('shop.admin.staff.*') }}" href="{{ route('shop.admin.staff.index') }}"><i class="bi bi-people"></i> Staff</a>
            <a class="nav-link{{ $is('shop.admin.profile.*') }}" href="{{ route('shop.admin.profile.index') }}"><i class="bi bi-person-circle"></i> Profile</a>
            <a class="nav-link" href="{{ route('shop.admin.setting.index') }}"><i class="bi bi-gear"></i> Settings</a>

            <div class="section-title">Account</div>
            <a class="nav-link" href="javascript:void(0)" onclick="document.getElementById('logout').submit()"><i class="bi bi-box-arrow-right"></i> Logout</a>
            @else
            <a class="nav-link" href="{{ route('shop.admin.dashboard.index') }}"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <div class="section-title">Website Settings</div>
            @foreach (\Modules\Shop\Http\Controllers\Admin\SettingController::SECTIONS as $key => $sectionMeta)
            <a class="nav-link{{ request()->routeIs('shop.admin.setting.index') && request()->route('section', 'general') === $key ? ' active' : '' }}"
               href="{{ route('shop.admin.setting.index', $key) }}"><i class="bi {{ $sectionMeta['icon'] }}"></i> {{ $sectionMeta['label'] }}</a>
            @endforeach
            <div class="section-title">Account</div>
            <a class="nav-link" href="javascript:void(0)" onclick="document.getElementById('logout').submit()"><i class="bi bi-box-arrow-right"></i> Logout</a>
            @endif
            <form action="{{ route('shop.admin.logout') }}" method="post" id="logout">@csrf</form>

            <svg class="sidebar-deco" viewBox="0 0 280 120" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0 70C70 40 150 40 280 10V120H0z" fill="rgba(255,255,255,.05)"/>
                <path d="M0 95C90 70 180 80 280 50V120H0z" fill="rgba(255,255,255,.05)"/>
            </svg>
        </div>
        <div class="admin-deco" aria-hidden="true">
            <svg class="d-wave-br" viewBox="0 0 800 300" preserveAspectRatio="none"><path d="M0 300C200 220 420 260 800 120V300z"/><path class="w2" d="M200 300C380 250 560 270 800 200V300z"/></svg>
        </div>
        <main class="main">
            <div class="topbar">
                <svg class="topbar-wave" viewBox="0 0 600 80" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M120 0C220 40 330 70 600 40V0z" fill="color-mix(in srgb, var(--primary) 10%, white)"/>
                    <path d="M260 0C340 30 440 46 600 18V0z" fill="color-mix(in srgb, var(--primary) 16%, white)"/>
                </svg>
                <div class="d-flex align-items-center gap-3 flex-grow-1 position-relative">
                    <button class="menu-btn" type="button" aria-label="Toggle menu"><i class="bi bi-list"></i></button>
                    <form class="search-box" action="{{ route('shop.admin.product.index') }}" method="get" role="search">
                        <i class="bi bi-search"></i><input class="form-control" name="search" value="{{ request()->routeIs('shop.admin.product.index') ? request('search') : '' }}" placeholder="Search products by name, SKU or barcode...">
                    </form>
                </div>
                <div class="top-actions position-relative">
                    <a class="btn btn-primary btn-sm d-none d-md-inline-flex align-items-center gap-1" href="{{ route('shop.admin.product.sell') }}"><i class="bi bi-upc-scan"></i> New sale</a>
                    @php($me = auth('shop')->user())
                    <div class="dropdown">
                        <button class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar">{{ $me?->initials ?: 'U' }}</span>
                            <span class="text-start d-none d-sm-block">
                                <span class="d-block fw-semibold lh-sm">{{ $me?->name }}</span>
                                <small class="text-secondary">{{ $me?->role_label }}</small>
                            </span>
                            <i class="bi bi-chevron-down small ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('shop.admin.profile.index') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('logout').submit()"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Sidebar: collapse on desktop, slide in on mobile (same behaviour as the restaurant admin).
    function setMobileSidebar(open) {
        document.getElementById('sidebar').classList.toggle('show', open);
        document.body.classList.toggle('sb-open', open);
    }
    document.getElementById('sidebarClose')?.addEventListener('click', function () { setMobileSidebar(false); });
    document.getElementById('sidebarBackdrop')?.addEventListener('click', function () { setMobileSidebar(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && document.body.classList.contains('sb-open')) setMobileSidebar(false); });
    window.addEventListener('resize', function () { if (window.innerWidth >= 992) setMobileSidebar(false); });
    document.querySelector('.menu-btn')?.addEventListener('click', function () {
        if (window.innerWidth < 992) { setMobileSidebar(!document.getElementById('sidebar').classList.contains('show')); }
        else { document.body.classList.toggle('sb-collapsed'); try { localStorage.setItem('sbCollapsed', document.body.classList.contains('sb-collapsed') ? '1' : ''); } catch (e) {} }
    });
    try { if (localStorage.getItem('sbCollapsed') === '1' && window.innerWidth >= 992) document.body.classList.add('sb-collapsed'); } catch (e) {}

    // Bootstrap modals must live directly under <body>, otherwise the backdrop can cover them.
    document.addEventListener('show.bs.modal', function (e) { if (e.target.parentElement !== document.body) document.body.appendChild(e.target); });

    // <form data-confirm="Delete this?"> asks before submitting.
    document.addEventListener('submit', function (e) {
        var msg = e.target.getAttribute('data-confirm');
        if (msg && !confirm(msg)) e.preventDefault();
    });

    // Turn a title into a slug: <input data-slug-source="#slugInput">.
    document.querySelectorAll('[data-slug-source]').forEach(function (input) {
        var target = document.querySelector(input.dataset.slugSource);
        if (!target) return;
        var touched = target.value !== '';
        target.addEventListener('input', function () { touched = target.value !== ''; });
        input.addEventListener('input', function () {
            if (!touched) target.value = input.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.js"></script>
    <script>
    (function () {
        var notyf = new Notyf({ duration: 4000, dismissible: true, ripple: false, position: { x: 'right', y: 'top' },
            types: [
                { type: 'success', icon: { className: 'bi bi-check-circle-fill', tagName: 'i', color: '#fff' } },
                { type: 'error', background: '#dc2626', icon: { className: 'bi bi-exclamation-circle-fill', tagName: 'i', color: '#fff' } },
            ] });
        function brand() { return getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#f97316'; }
        window.notify = {
            success: function (m) { return notyf.open({ type: 'success', message: m, background: brand() }); },
            error:   function (m) { return notyf.error(m); },
        };
    })();
    @if (session('success')) notify.success(@json(session('success'))); @endif
    @if (session('error')) notify.error(@json(session('error'))); @endif
    @if ($errors->any()) notify.error(@json($errors->first())); @endif
    </script>
    <script>
    // Phones: list tables become one card per row; label each cell with its column name.
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
    })();
    </script>
    @stack('js')
</body>

</html>
