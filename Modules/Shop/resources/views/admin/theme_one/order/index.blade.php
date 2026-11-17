@extends('shop::admin.theme_one.component.layout')
@section('title', 'Orders')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Orders</h2><p>Every sale: counter (POS) and online orders.</p></div>
    <a class="btn btn-primary" href="{{ route('shop.admin.product.sell') }}"><i class="bi bi-upc-scan me-1"></i>New sale</a>
</div>

<div class="card-soft table-card">
    <div class="card-header">
        <form class="filter-bar d-flex flex-wrap gap-2" method="get">
            <input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Order no., name or phone" style="max-width:220px;">
            <select class="form-select form-select-sm" name="status" style="max-width:160px;">
                <option value="">Any status</option>
                @foreach ($statuses as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <select class="form-select form-select-sm" name="payment_status" style="max-width:160px;">
                <option value="">Any payment</option>
                @foreach (['paid', 'credit', 'pending', 'failed', 'refunded'] as $s)<option value="{{ $s }}" @selected(request('payment_status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <select class="form-select form-select-sm" name="sale_type" style="max-width:140px;">
                <option value="">POS &amp; online</option>
                <option value="pos" @selected(request('sale_type') === 'pos')>POS</option>
                <option value="online" @selected(request('sale_type') === 'online')>Online</option>
            </select>
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            @if (request()->hasAny(['search', 'status', 'payment_status', 'sale_type']))<a class="btn btn-sm btn-link" href="{{ route('shop.admin.order.index') }}">Clear</a>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th class="text-center">Items</th><th class="text-end">Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><span class="fw-semibold">{{ $order->order_number_format }}</span>
                                <div class="text-muted small">{{ $order->created_at->format('j M Y, g:i A') }} · {{ strtoupper($order->sale_type) }}</div></td>
                            <td>{{ $order->full_name ?: 'Walk-in' }}<div class="text-muted small">{{ $order->phone_number }}</div></td>
                            <td class="text-center">{{ $order->order_products_count }}</td>
                            <td class="text-end money fw-semibold">{{ $rs($order->grand_total) }}</td>
                            <td>@include('shop::admin.theme_one.order._payment-pill')<div class="text-muted small">{{ $order->payment_method_label }}</div></td>
                            <td>@include('shop::admin.theme_one.order._status-pill')</td>
                            <td><div class="d-flex gap-1">
                                <a class="tbl-action view" title="Details" href="{{ route('shop.admin.order.show', $order) }}"><i class="bi bi-eye"></i></a>
                                <a class="tbl-action edit" title="Invoice" href="{{ route('shop.admin.product.invoice', $order) }}"><i class="bi bi-printer"></i></a>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-row"><i class="bi bi-receipt"></i>No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($orders->hasPages())<div class="card-footer bg-transparent">{{ $orders->links() }}</div>@endif
</div>
@endsection
