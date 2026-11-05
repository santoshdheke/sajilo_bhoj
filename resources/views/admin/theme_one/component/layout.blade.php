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
            <div class="brand">

                <img src="{{ asset('assets/backend/img/logo-no-bg.png') }}" alt="Logo" style="height: 70px;">
            </div>
            @if (! request()->routeIs('admin.settings.*'))
            <div class="section-title">Main</div>
            <a class="nav-link{{ Request::is('admin/dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard.index') }}"><i class="bi bi-grid"></i> Dashboard</a>
            <a class="nav-link{{ Request::is('admin/tables') ? ' active' : '' }}" href="{{ route('admin.table.index') }}"><i class="bi bi-grid-3x3-gap"></i> Tables</a>
            <a class="nav-link{{ Request::is('admin/orders') ? ' active' : '' }}" href="{{ route('admin.order.index') }}"><i class="bi bi-receipt"></i> Orders <span
                    class="badge text-bg-light">24</span></a>
            <a class="nav-link{{ Request::is('admin/kitchen') ? ' active' : '' }}" href="{{ route('admin.kitchen.index') }}"><i class="bi bi-fire"></i> Kitchen Display</a>
            <a class="nav-link{{ Request::is('admin/billing') ? ' active' : '' }}" href="{{ route('admin.billing.index') }}"><i class="bi bi-wallet2"></i> Billing</a>
            <div class="section-title">Menu</div>
            <div class="has-sub open">
                <a class="nav-link menu-toggle" href="#"><i class="bi bi-basket2"></i> Menu Management <i
                        class="bi bi-chevron-down ms-auto"></i></a>
                <div class="submenu">
                    <a class="nav-link" href="{{ route('admin.menu_category.index') }}">Categories</a>
                    <a class="nav-link" href="{{ route('admin.menu.index') }}">Menu Items</a>
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
        </div>
        <main class="main">
            <div class="topbar">
                <div class="d-flex align-items-center gap-3 flex-grow-1"><button
                        class="btn btn-soft d-lg-none mobile-menu-btn"><i class="bi bi-list"></i></button>
                    <div class="search-box"><i class="bi bi-search"></i><input class="form-control"
                            placeholder="Search orders, tables, menu items..."></div>
                </div>
                <div class="top-actions"><button class="btn btn-soft"><i class="bi bi-bell"></i></button><button
                        class="btn btn-soft"><i class="bi bi-chat-dots"></i></button>
                    <div class="d-flex align-items-center gap-2 glass px-3 py-2">
                        <div class="avatar">SD</div>
                        <div>
                            <div class="fw-semibold">Santosh</div><small class="text-secondary">Administrator</small>
                        </div>
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
    @stack('js')
</body>

</html>
