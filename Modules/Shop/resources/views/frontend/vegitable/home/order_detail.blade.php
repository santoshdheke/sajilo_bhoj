@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php
    $total = 0;
    if (isset($order->orderProduces)) {
        foreach ($order->orderProduces as $op) {
            if (isset($op->product)) $total += $op->unit_price * $op->quantity;
        }
    }
    $os = strtolower($order->order_status ?? 'pending');
    $ps = strtolower($order->payment_status ?? 'unpaid');
    $osBadge = match($os) {
        'delivered'  => 'eh-badge-green',
        'cancelled'  => 'background:#fee2e2;color:#991b1b;',
        'shipped'    => 'background:#e0e7ff;color:#3730a3;',
        'processing' => 'background:#dbeafe;color:#1e40af;',
        default      => 'background:#fef3c7;color:#92400e;',
    };
@endphp

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">Order Detail</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            @if(auth('shop_customer')->check())
                <li class="breadcrumb-item"><a href="{{ route('shop.front.account.orders', ['order-status'=>'pending']) }}">My Orders</a></li>
            @endif
            <li class="breadcrumb-item active">Order Detail</li>
        </ol></nav>
    </div>
</div>

<div class="container py-4" style="max-width:860px;">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2" style="font-size:18px;"></i>
            <div><strong>Order Placed!</strong> Thank you. We'll start processing right away.</div>
        </div>
    @endif

    <!-- Order header -->
    <div class="eh-summary-card mb-3">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <div>
                <div style="font-size:16px;font-weight:700;color:var(--dark);">{{ $order->order_number_format ?? ('Order #'.$order->order_number) }}</div>
                <div style="font-size:12px;color:var(--muted);">{{ optional($order->created_at)->format('d M Y, h:i A') }}</div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span class="eh-badge" style="padding:5px 14px;font-size:12px;font-weight:700;{{ is_string($osBadge) && str_contains($osBadge,'background') ? $osBadge : '' }}">
                    {{ ucfirst($os) }}
                </span>
                <span class="eh-badge {{ $ps === 'paid' ? 'eh-badge-green' : 'eh-badge-orange' }}" style="padding:5px 14px;font-size:12px;font-weight:700;">
                    {{ ucfirst($ps) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="eh-summary-card mb-3">
        <h6 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:10px;border-bottom:1px solid var(--border);margin-bottom:0;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-box-seam" style="color:var(--primary);"></i> Order Items
        </h6>
        @if(isset($order->orderProduces) && count($order->orderProduces))
            @foreach($order->orderProduces as $op)
                @if(isset($op->product))
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);">
                        <img src="{{ $op->product->image_path }}" alt="{{ $op->product->name }}"
                             style="width:60px;height:60px;object-fit:cover;border-radius:3px;border:1px solid var(--border);flex-shrink:0;">
                        <div style="flex:1;">
                            <div style="font-size:14px;font-weight:600;color:var(--dark);">{{ $op->product->name }}</div>
                            <div style="font-size:12px;color:var(--muted);">Rs. {{ number_format($op->unit_price) }} × {{ $op->quantity }}</div>
                        </div>
                        <div style="font-size:15px;font-weight:700;white-space:nowrap;">Rs. {{ number_format($op->unit_price * $op->quantity) }}</div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="eh-empty-state sm"><i class="bi bi-box-open"></i><p>No items found</p></div>
        @endif

        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted);">
            <span>Subtotal</span><span style="font-weight:600;color:var(--dark);">Rs. {{ number_format($total) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted);">
            <span>Delivery</span><span style="color:#16a34a;font-weight:600;">Free</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:14px 0;font-size:17px;font-weight:800;">
            <span>Total</span><span style="color:var(--primary);">Rs. {{ number_format($total) }}</span>
        </div>
    </div>

    <!-- Delivery info -->
    <div class="eh-summary-card mb-4">
        <h6 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:10px;border-bottom:1px solid var(--border);margin-bottom:16px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-geo-alt-fill" style="color:var(--primary);"></i> Delivery Information
        </h6>
        <div class="row g-2">
            <div class="col-sm-6">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Full Name</div>
                <div style="font-size:13.5px;font-weight:600;">{{ $order->full_name ?? '—' }}</div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Phone</div>
                <div style="font-size:13.5px;font-weight:600;">{{ $order->phone_number ?? '—' }}</div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Address</div>
                <div style="font-size:13.5px;font-weight:600;">{{ $order->address ?? '—' }}</div>
            </div>
            <div class="col-sm-6">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Hints</div>
                <div style="font-size:13.5px;font-weight:600;">{{ $order->address_detail ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-5">
        @if(auth('shop_customer')->check())
            <a href="{{ route('shop.front.account.orders', ['order-status'=>'pending']) }}" class="eh-btn-ghost">
                <i class="bi bi-arrow-left"></i> My Orders
            </a>
        @endif
        <a href="{{ route('shop.front.store') }}" class="eh-btn-primary">
            <i class="bi bi-bag"></i> Continue Shopping
        </a>
    </div>
</div>
@endsection
