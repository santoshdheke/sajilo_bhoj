@extends('shop::admin.theme_one.component.layout')
@section('title', 'Invoice '.$order->order_number_format)

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@push('css')
<style>
.invoice { max-width: 760px; margin: 0 auto; }
@media print {
    .sidebar, .topbar, .admin-deco, .no-print, .notyf { display: none !important; }
    .main { margin: 0 !important; padding: 0 !important; }
    .invoice { box-shadow: none !important; max-width: none; }
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 no-print invoice">
    <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.product.sell') }}"><i class="bi bi-upc-scan me-1"></i>New sale</a>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('shop.admin.order.show', $order) }}">Order details</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
</div>

<div class="card-soft invoice p-4">
    <div class="d-flex justify-content-between flex-wrap gap-3 border-bottom pb-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">{{ $shop->name }}</h4>
            <div class="text-muted small">{{ $shop->address }}</div>
            <div class="text-muted small">{{ collect([$shop->phone, $shop->email])->filter()->implode(' · ') }}</div>
        </div>
        <div class="text-end">
            <div class="text-uppercase text-muted small fw-semibold">Invoice</div>
            <div class="fw-bold">{{ $order->order_number_format }}</div>
            <div class="text-muted small">{{ $order->created_at->format('j M Y, g:i A') }}</div>
        </div>
    </div>

    <div class="d-flex justify-content-between flex-wrap gap-3 mb-3 small">
        <div>
            <div class="text-muted">Bill to</div>
            <div class="fw-semibold">{{ $order->full_name ?: 'Walk-in customer' }}</div>
            @if ($order->phone_number)<div>{{ $order->phone_number }}</div>@endif
            @if ($order->address)<div>{{ $order->address }}</div>@endif
        </div>
        <div class="text-end">
            <div class="text-muted">Payment</div>
            <div class="fw-semibold">{{ $order->payment_method_label }}</div>
            @if (! empty($order->billing_details['transaction_code']))<div>Ref: {{ $order->billing_details['transaction_code'] }}</div>@endif
            @include('shop::admin.theme_one.order._payment-pill', ['order' => $order])
        </div>
    </div>

    <table class="table align-middle no-stack">
        <thead><tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Amount</th></tr></thead>
        <tbody>
            @foreach ($order->orderProducts as $line)
                <tr>
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td>{{ $line->product?->name ?? 'Deleted product' }}@include('shop::partials.line-options', ['attributes' => $line->attribute_description])</td>
                    <td class="text-center">{{ $line->quantity }} {{ $line->product?->unit }}</td>
                    <td class="text-end money">{{ $rs($line->unit_price) }}</td>
                    <td class="text-end money">{{ $rs($line->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ms-auto" style="max-width:300px;">
        <div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span class="money">{{ $rs($order->total_amount) }}</span></div>
        @if ($order->discount > 0)<div class="d-flex justify-content-between"><span class="text-muted">Discount</span><span class="money">- {{ $rs($order->discount) }}</span></div>@endif
        <div class="d-flex justify-content-between fw-bold fs-5 border-top mt-2 pt-2"><span>Total</span><span class="money">{{ $rs($order->grand_total) }}</span></div>
        <div class="d-flex justify-content-between"><span class="text-muted">Paid</span><span class="money">{{ $rs($order->paid_amount) }}</span></div>
        @if ($order->due_amount > 0)
            <div class="d-flex justify-content-between text-danger fw-semibold"><span>Credit (due)</span><span class="money">{{ $rs($order->due_amount) }}</span></div>
        @elseif ($order->change_amount > 0)
            <div class="d-flex justify-content-between text-success"><span>Change</span><span class="money">{{ $rs($order->change_amount) }}</span></div>
        @endif
    </div>

    <p class="text-center text-muted small mt-4 mb-0">Thank you for shopping with us!{{ $order->user ? ' · Served by '.$order->user->name : '' }}</p>
</div>
@endsection
