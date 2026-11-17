@extends('shop::admin.theme_one.component.layout')
@section('title', 'Sales report')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Sales report</h2><p>{{ $from->format('j M Y') }} – {{ $to->format('j M Y') }} (cancelled orders excluded)</p></div>
    @include('shop::admin.theme_one.report._range')
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-cash-coin', 'label' => 'Total sales', 'value' => $rs($totalSales), 'tone' => 'blue'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-receipt', 'label' => 'Orders', 'value' => number_format($orderCount), 'tone' => 'green', 'sub' => $orderCount ? 'avg '.$rs($totalSales / $orderCount) : null])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-tag', 'label' => 'Discounts given', 'value' => $rs($totalDiscount), 'tone' => 'amber'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-percent', 'label' => 'Tax', 'value' => $rs($totalTax), 'tone' => 'gray'])</div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-soft h-100">
            <div class="card-header"><h5 class="mb-0">Daily sales</h5></div>
            <div class="card-body">
                @php($max = max(1, $dailySales->max() ?? 0))
                @forelse ($dailySales as $day => $amount)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="small text-muted" style="width:72px;">{{ \Carbon\Carbon::parse($day)->format('j M') }}</span>
                        <div class="flex-fill bg-light rounded" style="height:10px;"><div class="rounded" style="height:10px;background:var(--primary);width:{{ $amount / $max * 100 }}%"></div></div>
                        <span class="small money fw-semibold" style="width:100px;text-align:right;">{{ $rs($amount) }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">No sales in this period.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-soft h-100">
            <div class="card-header"><h5 class="mb-0">By payment method</h5></div>
            <div class="card-body">
                @forelse ($paymentBreakdown as $method => $amount)
                    <div class="d-flex justify-content-between py-2 {{ ! $loop->last ? 'border-bottom' : '' }}"><span>{{ $method ?: 'Other' }}</span><span class="money fw-semibold">{{ $rs($amount) }}</span></div>
                @empty
                    <p class="text-muted mb-0">No sales in this period.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card-soft table-card">
    <div class="card-header"><h5 class="mb-0">Orders</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th class="text-center">Items</th><th class="text-end">Total</th><th>Payment</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a class="fw-semibold text-decoration-none" href="{{ route('shop.admin.order.show', $order) }}">{{ $order->order_number_format }}</a></td>
                            <td class="text-muted small">{{ $order->created_at->format('j M Y, g:i A') }}</td>
                            <td>{{ $order->full_name ?: 'Walk-in' }}</td>
                            <td class="text-center">{{ $order->order_products_count }}</td>
                            <td class="text-end money fw-semibold">{{ $rs($order->grand_total) }}</td>
                            <td>@include('shop::admin.theme_one.order._payment-pill')</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row"><i class="bi bi-graph-up"></i>No sales in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
