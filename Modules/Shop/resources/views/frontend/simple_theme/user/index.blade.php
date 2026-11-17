@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php
    $user        = auth('shop_customer')->user();
    $allOrders   = \Modules\Shop\Models\Order::where('customer_id', $user?->id ?? 0)->get();
    $totalOrders = $allOrders->count();
    $delivered   = $allOrders->where('order_status', 'delivered')->count();
    $cancelled   = $allOrders->where('order_status', 'cancelled')->count();
    $recent      = $allOrders->sortByDesc('created_at')->take(5);
@endphp

<div class="account-page">
<div class="container">
<div class="account-layout">

    {{-- Sidebar --}}
    @include($theme_path . 'user.sidebar')

    {{-- Main content --}}
    <div>

        {{-- Welcome banner --}}
        <div style="background:linear-gradient(135deg,#{{ $brand['primary'] }},#{{ $brand['secondary'] }});border-radius:14px;padding:24px 28px;margin-bottom:24px;display:flex;align-items:center;gap:20px;color:#fff;">
            <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;opacity:.75;margin-bottom:4px;">Welcome back</div>
                <div style="font-size:20px;font-weight:800;letter-spacing:-.3px;">{{ $user?->name ?? 'Customer' }}</div>
                <div style="font-size:12.5px;opacity:.7;margin-top:2px;">Here's what's happening with your orders today.</div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="acc-stats">
            <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="acc-stat-card stat-blue">
                <div class="acc-stat-icon si-blue"><i class="fas fa-shopping-bag"></i></div>
                <div class="acc-stat-value">{{ $totalOrders }}</div>
                <div class="acc-stat-label">Total Orders</div>
            </a>
            <a href="{{ route('shop.front.account.orders', ['order-status' => 'delivered']) }}" class="acc-stat-card stat-green">
                <div class="acc-stat-icon si-green"><i class="fas fa-check-circle"></i></div>
                <div class="acc-stat-value">{{ $delivered }}</div>
                <div class="acc-stat-label">Delivered</div>
            </a>
            <a href="{{ route('shop.front.account.orders', ['order-status' => 'cancelled']) }}" class="acc-stat-card stat-red">
                <div class="acc-stat-icon si-red"><i class="fas fa-times-circle"></i></div>
                <div class="acc-stat-value">{{ $cancelled }}</div>
                <div class="acc-stat-label">Cancelled</div>
            </a>
        </div>

        {{-- Recent orders --}}
        <div class="account-content">
            <div class="acc-content-header">
                <h2 class="acc-content-title">
                    <i class="fas fa-receipt"></i>
                    Recent Orders
                </h2>
                <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}"
                   style="font-size:13px;font-weight:600;color:#{{ $brand['primary'] }};">
                    View all <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                </a>
            </div>
            <div class="acc-content-body" style="padding:0;">
                @if($recent->count() > 0)
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                            <thead>
                                <tr style="background:#f8f9fb;">
                                    <th style="padding:11px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e8eaed;text-align:left;">Order</th>
                                    <th style="padding:11px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e8eaed;text-align:left;">Items</th>
                                    <th style="padding:11px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e8eaed;text-align:right;">Amount</th>
                                    <th style="padding:11px 20px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e8eaed;text-align:center;">Status</th>
                                    <th style="padding:11px 20px;width:60px;border-bottom:1px solid #e8eaed;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent as $order)
                                @php
                                    $os = strtolower($order->order_status ?? 'pending');
                                    $osBadge = match($os) {
                                        'delivered'=>'badge-delivered','cancelled'=>'badge-cancelled',
                                        'shipped'=>'badge-shipped','processing'=>'badge-processing',
                                        default=>'badge-pending',
                                    };
                                @endphp
                                <tr style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#f8f9fb'" onmouseout="this.style.background=''">
                                    <td style="padding:14px 20px;">
                                        <div style="font-weight:700;color:#111;">{{ $order->order_number_format }}</div>
                                        <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ optional($order->created_at)->format('d M Y') }}</div>
                                    </td>
                                    <td style="padding:14px 20px;color:#6b7280;">{{ count($order->orderProduces) }} item{{ count($order->orderProduces) != 1 ? 's' : '' }}</td>
                                    <td style="padding:14px 20px;text-align:right;font-weight:700;color:#111;">Rs. {{ number_format($order->total_amount) }}</td>
                                    <td style="padding:14px 20px;text-align:center;">
                                        <span class="order-badge {{ $osBadge }}">{{ ucfirst($os) }}</span>
                                    </td>
                                    <td style="padding:14px 20px;text-align:center;">
                                        <a href="{{ route('shop.front.order.show', $order->order_number) }}"
                                           style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e8eaed;background:#fff;color:#6b7280;font-size:12px;"
                                           title="View" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="acc-empty">
                        <div class="acc-empty-icon"><i class="fas fa-shopping-bag"></i></div>
                        <h4>No orders yet</h4>
                        <p>Start shopping to see your orders here.</p>
                        <a href="{{ route('shop.front.store') }}"
                           style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#{{ $brand['primary'] }};color:#{{ $brand['text'] }};border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                            <i class="fas fa-store"></i> Browse Products
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
</div>
</div>
@endsection
