<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - RestoPro Admin</title>
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
        <div class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('admin.dashboard.index') }}">
                <img src="{{ asset('assets/frontend/img/logo.png') }}" alt="Logo">
            </a>
            @if (! request()->routeIs('admin.settings.*'))
            <div class="section-title">Main</div>
            <a class="nav-link{{ Request::is('admin/dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard.index') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a>
            <a class="nav-link{{ Request::is('admin/tables') ? ' active' : '' }}" href="{{ route('admin.table.index') }}"><i class="bi bi-grid-3x3-gap"></i> Tables</a>
            <a class="nav-link{{ Request::is('admin/orders') ? ' active' : '' }}" href="{{ route('admin.order.index') }}"><i class="bi bi-receipt"></i> Orders <span
                    class="badge">24</span></a>
            <a class="nav-link{{ Request::is('admin/kitchen') ? ' active' : '' }}" href="{{ route('admin.kitchen.index') }}"><i class="bi bi-fire"></i> Kitchen Display</a>
            <a class="nav-link{{ Request::is('admin/billing') ? ' active' : '' }}" href="{{ route('admin.billing.index') }}"><i class="bi bi-wallet2"></i> Billing</a>
            <div class="section-title">Menu</div>
            <div class="has-sub open">
                <a class="nav-link menu-toggle" href="#"><i class="bi bi-basket2"></i> Menu Management <i
                        class="bi bi-chevron-down ms-auto"></i></a>
                <div class="submenu">
                    <a class="nav-link{{ Request::is('admin/menu-categor*') || request()->routeIs('admin.menu_category.*') ? ' active' : '' }}" href="{{ route('admin.menu_category.index') }}"><i class="bi bi-tags"></i> Categories</a>
                    <a class="nav-link{{ request()->routeIs('admin.menu.*') ? ' active' : '' }}" href="{{ route('admin.menu.index') }}"><i class="bi bi-egg-fried"></i> Menu Items</a>
                </div>
            </div>
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
            <a class="nav-link{{ Request::is('admin/report') ? ' active' : '' }}" href="{{ route("admin.report.index") }}"><i class="bi bi-bar-chart"></i> Reports</a>
            @if(auth()->user()?->hasPermission('coupons.manage'))
            <a class="nav-link{{ Request::is('admin/coupons') ? ' active' : '' }}" href="{{ route('admin.coupon.index') }}"><i class="bi bi-ticket-perforated"></i> Coupons</a>
            @endif
            @if(auth()->user()?->hasPermission('customers.manage'))
            <a class="nav-link{{ Request::is('admin/customers') ? ' active' : '' }}" href="{{ route('admin.customer.index') }}"><i class="bi bi-person-hearts"></i> Customers</a>
            @endif
            @if(auth()->user()?->hasPermission('payments.manage'))
            <a class="nav-link{{ Request::is('admin/payments*') ? ' active' : '' }}" href="{{ route('admin.payment.index') }}"><i class="bi bi-credit-card"></i> Payments</a>
            @endif
            <a class="nav-link{{ Request::is('admin/user') ? ' active' : '' }}" href="{{ route("admin.user.index") }}"><i class="bi bi-people"></i> Users</a>
            <a class="nav-link{{ Request::is('admin/profile') ? ' active' : '' }}" href="{{ route("admin.profile.index") }}"><i class="bi bi-person-circle"></i> Profile</a>
            <a class="nav-link{{ request()->routeIs('admin.settings.*') ? ' active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear"></i> Settings</a>
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
    document.querySelector('.menu-btn')?.addEventListener('click', function () {
        if (window.innerWidth < 992) { document.getElementById('sidebar').classList.toggle('show'); }
        else { document.body.classList.toggle('sb-collapsed'); try { localStorage.setItem('sbCollapsed', document.body.classList.contains('sb-collapsed') ? '1' : ''); } catch (e) {} }
    });
    try { if (localStorage.getItem('sbCollapsed') === '1' && window.innerWidth >= 992) document.body.classList.add('sb-collapsed'); } catch (e) {}

    // Bootstrap modals must live directly under <body>, otherwise the backdrop can cover them.
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target.parentElement !== document.body) document.body.appendChild(e.target);
    });
    </script>
    @stack('js')
</body>

</html>
