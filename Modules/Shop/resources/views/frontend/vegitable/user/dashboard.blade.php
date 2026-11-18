@extends($theme_path . 'common.layout.master_layout')
@section('hide_flash', 1)

@section('contant')
@php
    $user        = auth('shop_customer')->user();
    $allOrders   = \Modules\Shop\Models\Order::where('customer_id', $user?->id ?? 0)->get();
    $totalOrders = $allOrders->count();
    $delivered   = $allOrders->where('order_status', 'delivered')->count();
    $cancelled   = $allOrders->where('order_status', 'cancelled')->count();
    $recent      = $allOrders->sortByDesc('created_at')->take(5);
@endphp

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">My Account</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">My Account</li>
        </ol></nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="eh-summary-card text-center mb-3">
                <div style="width:72px;height:72px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:28px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div style="font-size:16px;font-weight:700;">{{ $user->name }}</div>
                <div style="font-size:13px;color:var(--muted);">{{ $user->email }}</div>
            </div>
            <div class="eh-sidebar-box">
                <a href="{{ route('shop.front.account.dashboard') }}" class="eh-mobile-menu-link" style="background:var(--primary-light);color:var(--primary);">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('shop.front.account.profile') }}" class="eh-mobile-menu-link">
                    <i class="bi bi-person"></i> My Profile
                </a>
                <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="eh-mobile-menu-link">
                    <i class="bi bi-box-seam"></i> My Orders
                </a>
                <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();" class="eh-mobile-menu-link">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-lg-9">


            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="eh-summary-card text-decoration-none d-block">
                        <div style="font-size:22px;font-weight:800;color:var(--primary);">{{ $totalOrders }}</div>
                        <div style="font-size:12.5px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Total Orders</div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'delivered']) }}" class="eh-summary-card text-decoration-none d-block">
                        <div style="font-size:22px;font-weight:800;color:#15803d;">{{ $delivered }}</div>
                        <div style="font-size:12.5px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Delivered</div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'cancelled']) }}" class="eh-summary-card text-decoration-none d-block">
                        <div style="font-size:22px;font-weight:800;color:#b91c1c;">{{ $cancelled }}</div>
                        <div style="font-size:12.5px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Cancelled</div>
                    </a>
                </div>
            </div>

            <!-- Recent orders -->
            <div class="eh-summary-card">
                <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:12px;border-bottom:2px solid var(--primary);margin-bottom:16px;">
                    <h5 style="font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin:0;">
                        <i class="bi bi-receipt" style="color:var(--primary);"></i> Recent Orders
                    </h5>
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" style="font-size:13px;font-weight:600;color:var(--primary);">
                        View all <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @if($recent->count() > 0)
                    @foreach($recent as $order)
                        @php
                            $os = strtolower($order->order_status ?? 'pending');
                            $oColor = match($os) {
                                'delivered'  => 'color:#065f46;background:#d1fae5;',
                                'cancelled'  => 'color:#991b1b;background:#fee2e2;',
                                'shipped'    => 'color:#3730a3;background:#e0e7ff;',
                                'processing' => 'color:#1e40af;background:#dbeafe;',
                                default      => 'color:#92400e;background:#fef3c7;',
                            };
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:12px 0;border-bottom:1px solid var(--border);">
                            <div>
                                <div style="font-size:13.5px;font-weight:700;">{{ $order->order_number_format ?? ('Order #'.$order->order_number) }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ optional($order->created_at)->format('d M Y') }} &bull; {{ count($order->orderProduces) }} item{{ count($order->orderProduces) != 1 ? 's' : '' }}</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span class="eh-badge" style="padding:4px 12px;font-size:12px;font-weight:700;{{ $oColor }}">{{ ucfirst($os) }}</span>
                                <div style="font-size:14px;font-weight:800;color:var(--primary);">Rs. {{ number_format($order->total_amount) }}</div>
                                <a href="{{ route('shop.front.order.show', $order->order_number) }}"
                                   class="eh-btn-ghost" style="font-size:12px;padding:6px 14px;">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="eh-empty-state py-5">
                        <i class="bi bi-box-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px;"></i>
                        <p>No orders yet. Start shopping to see your orders here.</p>
                        <a href="{{ route('shop.front.store') }}">Browse Products</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
