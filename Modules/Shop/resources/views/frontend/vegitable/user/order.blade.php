@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php $user = auth('shop_customer')->user(); @endphp

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">My Orders</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">My Orders</li>
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
            </div>
            <div class="eh-sidebar-box">
                <a href="{{ route('shop.front.account.profile') }}" class="eh-mobile-menu-link">
                    <i class="bi bi-person"></i> My Profile
                </a>
                <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="eh-mobile-menu-link"
                   style="background:var(--primary-light);color:var(--primary);">
                    <i class="bi bi-box-seam"></i> My Orders
                </a>
                <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();" class="eh-mobile-menu-link">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-lg-9">
            <!-- Status tabs -->
            <div style="display:flex;gap:4px;margin-bottom:16px;border-bottom:1px solid var(--border);flex-wrap:wrap;">
                @foreach(['pending','processing','shipped','delivered','cancelled'] as $status)
                    <a href="{{ route('shop.front.account.orders', ['order-status' => $status]) }}"
                       style="padding:10px 16px;font-size:13px;font-weight:600;border-bottom:2px solid {{ request('order-status') == $status ? 'var(--primary)' : 'transparent' }};color:{{ request('order-status') == $status ? 'var(--primary)' : 'var(--muted)' }};text-decoration:none;white-space:nowrap;">
                        {{ ucfirst($status) }}
                    </a>
                @endforeach
            </div>

            @if(isset($orders) && $orders->count())
                @foreach($orders as $order)
                    @php
                        $os = strtolower($order->order_status ?? 'pending');
                        $oColor = match($os) {
                            'delivered' => 'color:#065f46;background:#d1fae5;',
                            'cancelled' => 'color:#991b1b;background:#fee2e2;',
                            'shipped'   => 'color:#3730a3;background:#e0e7ff;',
                            'processing'=> 'color:#1e40af;background:#dbeafe;',
                            default     => 'color:#92400e;background:#fef3c7;',
                        };
                        $oTotal = 0;
                        foreach ($order->orderProduces as $op) {
                            if (isset($op->product)) $oTotal += $op->unit_price * $op->quantity;
                        }
                    @endphp
                    <div class="eh-summary-card mb-3">
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding-bottom:12px;border-bottom:1px solid var(--border);margin-bottom:12px;">
                            <div>
                                <div style="font-size:14px;font-weight:700;">{{ $order->order_number_format ?? ('Order #'.$order->order_number) }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ optional($order->created_at)->format('d M Y') }}</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="eh-badge" style="padding:4px 12px;font-size:12px;font-weight:700;{{ $oColor }}">{{ ucfirst($os) }}</span>
                                <div style="font-size:16px;font-weight:800;color:var(--primary);">Rs. {{ number_format($oTotal) }}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            @foreach($order->orderProduces->take(4) as $op)
                                @if(isset($op->product))
                                    <img src="{{ $op->product->image_path }}" alt="{{ $op->product->name }}"
                                         style="width:48px;height:48px;object-fit:cover;border-radius:3px;border:1px solid var(--border);">
                                @endif
                            @endforeach
                            @if($order->orderProduces->count() > 4)
                                <span style="font-size:12px;color:var(--muted);">+{{ $order->orderProduces->count() - 4 }} more</span>
                            @endif
                            <a href="{{ route('shop.front.order.show', $order->order_number) }}"
                               class="eh-btn-ghost" style="margin-left:auto;font-size:12px;padding:6px 14px;">
                                <i class="bi bi-eye"></i> View Detail
                            </a>
                        </div>
                    </div>
                @endforeach
                <div>{{ $orders->links('pagination::bootstrap-5') }}</div>
            @else
                <div class="eh-empty-state py-5">
                    <i class="bi bi-box-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px;"></i>
                    <p>No {{ request('order-status') }} orders found.</p>
                    <a href="{{ route('shop.front.store') }}">Start Shopping</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
