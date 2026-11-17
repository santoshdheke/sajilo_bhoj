@extends($theme_path . 'common.layout.master_layout')
@section('hide_flash', 1)

@push('css')
<style>
    .od-page { padding: 36px 0 72px; }
    .od-wrap { max-width: 860px; margin: 0 auto; }

    /* ── Top breadcrumb ── */
    .od-breadcrumb {
        font-size: 12.5px; color: #9ca3af;
        display: flex; align-items: center; gap: 6px;
        margin-bottom: 28px; flex-wrap: wrap;
    }
    .od-breadcrumb a { color: #{{ $brand['primary'] }}; }
    .od-breadcrumb a:hover { text-decoration: underline; }
    .od-breadcrumb i { font-size: 9px; color: #d1d5db; }

    /* ── Success banner ── */
    .od-success {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #a7f3d0;
        border-radius: 14px;
        padding: 32px 28px;
        text-align: center;
        margin-bottom: 28px;
    }
    .od-success-icon {
        width: 72px; height: 72px; border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #34d399);
        color: #fff; font-size: 30px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 6px 20px rgba(16,185,129,.25);
    }
    .od-success h2 { font-size: 22px; font-weight: 800; color: #065f46; margin-bottom: 6px; }
    .od-success p  { font-size: 14px; color: #047857; margin: 0; }

    /* ── Section cards ── */
    .od-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        margin-bottom: 20px;
    }
    .od-card-header {
        display: flex; align-items: center; gap: 10px;
        padding: 16px 22px;
        border-bottom: 1px solid #e8eaed;
        background: #fafbfc;
    }
    .od-card-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0;
        background: #{{ $brand['primary'] }}18; color: #{{ $brand['primary'] }};
    }
    .od-card-title { font-size: 14px; font-weight: 800; color: #111; margin: 0; }

    /* ── Order items ── */
    .od-item {
        display: flex; align-items: center; gap: 16px;
        padding: 16px 22px;
        border-bottom: 1px solid #f3f4f6;
    }
    .od-item:last-child { border-bottom: none; }
    .od-item-img {
        width: 64px; height: 64px; border-radius: 8px;
        object-fit: cover; border: 1px solid #f0f0f0;
        background: #f6f6f6; flex-shrink: 0;
    }
    .od-item-name  { font-size: 14px; font-weight: 600; color: #111; margin-bottom: 3px; }
    .od-item-meta  { font-size: 12.5px; color: #9ca3af; }
    .od-item-total { margin-left: auto; font-size: 15px; font-weight: 800; color: #111; white-space: nowrap; }

    /* ── Info grid ── */
    .od-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }
    @media (max-width: 575px) { .od-info-grid { grid-template-columns: 1fr; } }

    .od-info-row {
        padding: 14px 22px;
        border-bottom: 1px solid #f3f4f6;
    }
    .od-info-row:nth-child(odd)  { border-right: 1px solid #f3f4f6; }
    .od-info-label { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
    .od-info-value { font-size: 14px; font-weight: 600; color: #111; }

    /* ── Order summary row ── */
    .od-summary-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 22px; font-size: 13.5px; color: #6b7280;
        border-bottom: 1px solid #f3f4f6;
    }
    .od-summary-row:last-child { border-bottom: none; }
    .od-total-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 22px;
        font-size: 16px; font-weight: 800; color: #111;
        background: #fafbfc; border-top: 1px solid #e8eaed;
    }
    .od-total-amt { color: #{{ $brand['primary'] }}; font-size: 20px; }

    /* ── Status badge ── */
    .od-status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 700;
        padding: 5px 12px; border-radius: 20px; white-space: nowrap;
    }
    .sb-pending    { background:#fef3c7;color:#92400e;border:1px solid #fde68a; }
    .sb-processing { background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe; }
    .sb-shipped    { background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe; }
    .sb-delivered  { background:#d1fae5;color:#065f46;border:1px solid #a7f3d0; }
    .sb-cancelled  { background:#fee2e2;color:#991b1b;border:1px solid #fecaca; }
    .sb-paid       { background:#d1fae5;color:#065f46;border:1px solid #a7f3d0; }
    .sb-unpaid     { background:#fef3c7;color:#92400e;border:1px solid #fde68a; }
</style>
@endpush

@section('contant')
@php
    $total = 0;
    if (isset($order->orderProduces)) {
        foreach ($order->orderProduces as $op) {
            if (isset($op->product)) {
                $total += $op->unit_price * $op->quantity;
            }
        }
    }
    $os = strtolower($order->order_status ?? 'pending');
    $ps = strtolower($order->payment_status ?? 'unpaid');
    $osBadge = match($os) {
        'delivered'=>'sb-delivered','cancelled'=>'sb-cancelled',
        'shipped'=>'sb-shipped','processing'=>'sb-processing',
        default=>'sb-pending',
    };
    $psBadge = $ps === 'paid' ? 'sb-paid' : 'sb-unpaid';
@endphp

<div class="od-page">
<div class="container">
<div class="od-wrap">

    {{-- Breadcrumb --}}
    <nav class="od-breadcrumb">
        <a href="{{ route('shop.front.home') }}">Home</a>
        <i class="fas fa-chevron-right"></i>
        @if(auth('shop_customer')->check())
            <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}">My Orders</a>
            <i class="fas fa-chevron-right"></i>
        @endif
        <span style="color:#111;">Order Detail</span>
    </nav>

    {{-- Success banner --}}
    @if(session('success'))
    <div class="od-success">
        <div class="od-success-icon"><i class="fas fa-check"></i></div>
        <h2>Order Placed Successfully!</h2>
        <p>Thank you for your order. We'll start processing it right away.</p>
    </div>
    @endif

    {{-- Order header card --}}
    <div class="od-card">
        <div class="od-card-header" style="justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="od-card-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="od-card-title">{{ $order->order_number_format ?? 'Order' }}</div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:1px;">{{ optional($order->created_at)->format('d M Y, h:i A') }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="od-status-badge {{ $osBadge }}"><i class="fas fa-circle" style="font-size:7px;"></i> {{ ucfirst($os) }}</span>
                <span class="od-status-badge {{ $psBadge }}">{{ ucfirst($ps) }}</span>
            </div>
        </div>
    </div>

    {{-- Payment --}}
    @if (session('error'))
        <div class="od-card" style="border-left:4px solid #ef4444;padding:14px 18px;color:#991b1b;font-size:13.5px;"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
    @endif
    <div class="od-card" style="padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-size:13.5px;color:#374151;">
            <i class="fas fa-wallet mr-2" style="color:#{{ $brand['primary'] }};"></i>Payment: <strong>{{ $order->payment_method_label }}</strong>
            @if (in_array($order->payment_method, ['khalti_qr', 'esewa_qr']) && $order->payment_status === 'pending')
                <span style="color:#9ca3af;"> — we'll confirm your payment screenshot shortly.</span>
            @endif
        </div>
        @if ($order->payment_method === 'khalti' && $order->payment_status !== 'paid' && $order->order_status !== 'cancelled' && $settings->khalti_secret_key)
            <a href="{{ route('shop.front.order.pay_khalti', $order->order_number) }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:#5c2d91;color:#fff;border-radius:8px;font-size:13px;font-weight:700;">
                <i class="fas fa-bolt"></i> Pay Rs. {{ number_format($order->grand_total) }} with Khalti
            </a>
        @endif
    </div>

    {{-- Order items --}}
    <div class="od-card">
        <div class="od-card-header">
            <div class="od-card-icon"><i class="fas fa-box"></i></div>
            <h3 class="od-card-title">Order Items</h3>
        </div>

        @if(isset($order->orderProduces) && count($order->orderProduces) > 0)
            @foreach($order->orderProduces as $op)
                @if(isset($op->product))
                <div class="od-item">
                    @if($op->product->image_path)
                        <img class="od-item-img"
                             src="{{ $op->product->image_path }}"
                             alt="{{ $op->product->name }}"
                             onerror="this.style.display='none'">
                    @else
                        <div class="od-item-img" style="display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:22px;">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <div class="od-item-name">{{ $op->product->name }}</div>
                        <div class="od-item-meta">Rs. {{ number_format($op->unit_price) }} × {{ $op->quantity }}</div>
                    </div>
                    <div class="od-item-total">Rs. {{ number_format($op->unit_price * $op->quantity) }}</div>
                </div>
                @endif
            @endforeach
        @else
            <div style="padding:40px;text-align:center;color:#9ca3af;font-size:13.5px;">
                <i class="fas fa-box-open" style="font-size:32px;display:block;margin-bottom:10px;color:#d1d5db;"></i>
                No items found
            </div>
        @endif

        {{-- Summary --}}
        <div class="od-summary-row">
            <span>Subtotal</span>
            <span style="font-weight:600;color:#111;">Rs. {{ number_format($total) }}</span>
        </div>
        <div class="od-summary-row">
            <span>Delivery</span>
            <span style="font-weight:600;color:#10b981;">Free</span>
        </div>
        <div class="od-total-row">
            <span>Total</span>
            <span class="od-total-amt">Rs. {{ number_format($total) }}</span>
        </div>
    </div>

    {{-- Delivery info --}}
    <div class="od-card">
        <div class="od-card-header">
            <div class="od-card-icon"><i class="fas fa-map-marker-alt"></i></div>
            <h3 class="od-card-title">Delivery Information</h3>
        </div>
        <div class="od-info-grid">
            <div class="od-info-row">
                <div class="od-info-label">Full Name</div>
                <div class="od-info-value">{{ $order->full_name ?? '—' }}</div>
            </div>
            <div class="od-info-row">
                <div class="od-info-label">Phone Number</div>
                <div class="od-info-value">{{ $order->phone_number ?? '—' }}</div>
            </div>
            <div class="od-info-row" style="border-right:1px solid #f3f4f6;">
                <div class="od-info-label">Address</div>
                <div class="od-info-value">{{ $order->address ?? '—' }}</div>
            </div>
            <div class="od-info-row">
                <div class="od-info-label">Address Hints</div>
                <div class="od-info-value">{{ $order->address_detail ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-top:4px;">
        @if(auth('shop_customer')->check())
        <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:11px 20px;border:1px solid #e8eaed;background:#fff;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
            <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to Orders
        </a>
        @endif
        <a href="{{ route('shop.front.store') }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:11px 20px;background:#{{ $brand['primary'] }};color:#{{ $brand['text'] }};border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
            <i class="fas fa-store" style="font-size:11px;"></i> Continue Shopping
        </a>
    </div>

</div>
</div>
</div>
@endsection
