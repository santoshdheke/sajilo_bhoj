@extends('shop::admin.theme_one.component.layout')
@section('title', $order->order_number_format)

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>{{ $order->order_number_format }}</h2>
        <p>{{ $order->created_at->format('j M Y, g:i A') }} · {{ $order->sale_type === 'pos' ? 'Counter sale' : 'Online order' }}{{ $order->user ? ' by '.$order->user->name : '' }}</p></div>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.order.index') }}"><i class="bi bi-arrow-left me-1"></i>Orders</a>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.product.invoice', $order) }}"><i class="bi bi-printer me-1"></i>Invoice</a>
        @if (! in_array($order->payment_status, ['paid', 'credit']))
            <form method="POST" action="{{ route('shop.admin.order.verify_payment', $order) }}" data-confirm="Mark this order as paid?">@csrf
                <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle me-1"></i>Verify payment</button></form>
        @endif
        @if (! in_array($order->order_status, ['delivered', 'cancelled']))
            <form method="POST" action="{{ route('shop.admin.order.status.update', [$order, 'delivered']) }}">@csrf
                <button class="btn btn-primary btn-sm"><i class="bi bi-truck me-1"></i>Mark delivered</button></form>
        @endif
        @if ($order->order_status !== 'cancelled')
            <form method="POST" action="{{ route('shop.admin.order.status.update', [$order, 'cancelled']) }}" data-confirm="Cancel this order? Its items go back into stock.">@csrf
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Cancel</button></form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Items</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @foreach ($order->orderProducts as $line)
                                <tr>
                                    <td class="fw-semibold">{{ $line->product?->name ?? 'Deleted product' }}
                                        @include('shop::partials.line-options', ['attributes' => $line->attribute_description])</td>
                                    <td class="text-center">{{ $line->quantity }}</td>
                                    <td class="text-end money">{{ $rs($line->unit_price) }}</td>
                                    <td class="text-end money fw-semibold">{{ $rs($line->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-soft mb-4">
            <div class="card-header"><h5 class="mb-0">Summary</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Status</span>@include('shop::admin.theme_one.order._status-pill')</div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Payment</span>@include('shop::admin.theme_one.order._payment-pill')</div>
                <div class="d-flex justify-content-between mb-3"><span class="text-muted">Method</span><span>{{ $order->payment_method_label }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span class="money">{{ $rs($order->total_amount) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Discount</span><span class="money">- {{ $rs($order->discount) }}</span></div>
                <div class="d-flex justify-content-between fw-bold border-top mt-2 pt-2"><span>Total</span><span class="money">{{ $rs($order->grand_total) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Paid</span><span class="money">{{ $rs($order->paid_amount) }}</span></div>
                @if ($order->due_amount > 0)<div class="d-flex justify-content-between text-danger fw-semibold"><span>Due</span><span class="money">{{ $rs($order->due_amount) }}</span></div>@endif
            </div>
        </div>
        @if ($order->payment_screenshot_url || ! empty($order->billing_details['transaction_id']) || $order->address_detail)
        <div class="card-soft mb-4">
            <div class="card-header"><h5 class="mb-0">Payment &amp; delivery notes</h5></div>
            <div class="card-body small">
                @if ($order->payment_screenshot_url)
                    <div class="text-muted mb-1">Payment screenshot</div>
                    <a href="{{ $order->payment_screenshot_url }}" target="_blank"><img src="{{ $order->payment_screenshot_url }}" alt="Payment screenshot" style="max-width:100%;max-height:260px;border-radius:12px;border:1px solid #e5e7eb;"></a>
                    @if ($order->payment_status !== 'paid')<div class="text-muted mt-2">Check the amount, then use <strong>Verify payment</strong> above.</div>@endif
                @endif
                @if (! empty($order->billing_details['transaction_id']))<div class="mt-2">Khalti transaction: <code>{{ $order->billing_details['transaction_id'] }}</code></div>@endif
                @if ($order->address_detail)<div class="mt-2"><span class="text-muted">Delivery note:</span> {{ $order->address_detail }}</div>@endif
            </div>
        </div>
        @endif
        <div class="card-soft">
            <div class="card-header"><h5 class="mb-0">Customer</h5></div>
            <div class="card-body small">
                <div class="fw-semibold">{{ $order->full_name ?: 'Walk-in customer' }}</div>
                <div>{{ $order->phone_number }}</div><div>{{ $order->email }}</div><div>{{ $order->address }}</div>
                @if ($order->customer)<a class="d-inline-block mt-2" href="{{ route('shop.admin.customer.show', $order->customer) }}">Open customer account →</a>@endif
            </div>
        </div>
    </div>
</div>
@endsection
