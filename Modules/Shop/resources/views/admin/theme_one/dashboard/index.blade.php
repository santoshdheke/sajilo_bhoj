@extends('shop::admin.theme_one.component.layout')
@section('title', 'Dashboard')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0">
        <h2>Dashboard</h2>
        <p>{{ now()->format('l, j F Y') }} — here is how your shop is doing.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.supplier.purchase.create') }}"><i class="bi bi-cart-plus me-1"></i>Add stock</a>
        <a class="btn btn-primary btn-sm" href="{{ route('shop.admin.product.sell') }}"><i class="bi bi-upc-scan me-1"></i>New sale</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-cash-coin', 'label' => "Today's sales", 'value' => $rs($todaySales), 'tone' => 'blue', 'sub' => $todayOrders.' orders today'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-calendar3', 'label' => 'This month', 'value' => $rs($monthlySales), 'tone' => 'green', 'sub' => $monthlyOrders.' orders'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-graph-up-arrow', 'label' => 'Profit this month', 'value' => $rs($monthlyProfit), 'tone' => $monthlyProfit < 0 ? 'red' : 'purple', 'sub' => 'after '.$rs($monthlyExpenses).' expenses'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-people', 'label' => 'Customers', 'value' => number_format($totalCustomers), 'tone' => 'amber', 'sub' => $udharCustomers.' with credit (udhar)', 'href' => route('shop.admin.customer.credit')])</div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card-soft h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Last 7 days</h5>
                <span class="small text-muted"><span class="pill pill-blue">Sales</span> <span class="pill pill-amber">Purchases</span> <span class="pill pill-red">Expenses</span></span>
            </div>
            <div class="card-body">
                @php($max = max(1, collect($chart)->flatMap(fn ($d) => [$d['sales'], $d['purchase'], $d['expense']])->max()))
                <div class="d-flex align-items-end justify-content-between gap-2" style="height:220px;">
                    @foreach ($chart as $day)
                        <div class="flex-fill d-flex flex-column align-items-center gap-1 h-100 justify-content-end">
                            <div class="d-flex align-items-end gap-1 w-100 justify-content-center" style="height:190px;">
                                <span title="Sales {{ $rs($day['sales']) }}" style="width:28%;max-width:18px;border-radius:6px 6px 0 0;background:#3b82f6;height:{{ max(2, $day['sales'] / $max * 100) }}%"></span>
                                <span title="Purchases {{ $rs($day['purchase']) }}" style="width:28%;max-width:18px;border-radius:6px 6px 0 0;background:#f59e0b;height:{{ max(2, $day['purchase'] / $max * 100) }}%"></span>
                                <span title="Expenses {{ $rs($day['expense']) }}" style="width:28%;max-width:18px;border-radius:6px 6px 0 0;background:#ef4444;height:{{ max(2, $day['expense'] / $max * 100) }}%"></span>
                            </div>
                            <small class="text-muted">{{ $day['label'] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-soft h-100">
            <div class="card-header"><h5 class="mb-0">Summary</h5></div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0 no-stack">
                    <thead><tr><th></th><th class="text-end">Today</th><th class="text-end">Week</th><th class="text-end">Month</th></tr></thead>
                    <tbody>
                        @foreach (['Sales' => $reportSales, 'Purchases' => $reportPurchase, 'Expenses' => $reportExpenses] as $label => $row)
                            <tr>
                                <td class="fw-semibold">{{ $label }}</td>
                                <td class="text-end money small">{{ $rs($row['daily']) }}</td>
                                <td class="text-end money small">{{ $rs($row['weekly']) }}</td>
                                <td class="text-end money small">{{ $rs($row['monthly']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 border-top d-flex justify-content-between small">
                    <span class="text-muted">Products</span><span class="fw-semibold">{{ number_format($totalProducts) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card-soft table-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Recent orders</h5>
                <a class="small" href="{{ route('shop.admin.order.index') }}">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Order</th><th>Customer</th><th class="text-center">Items</th><th class="text-end">Total</th><th>Payment</th></tr></thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td><a class="fw-semibold text-decoration-none" href="{{ route('shop.admin.order.show', $order) }}">{{ $order->order_number_format }}</a><div class="text-muted small">{{ $order->created_at->diffForHumans() }}</div></td>
                                    <td>{{ $order->full_name ?: 'Walk-in' }}</td>
                                    <td class="text-center">{{ $order->order_products_count }}</td>
                                    <td class="text-end money fw-semibold">{{ $rs($order->grand_total) }}</td>
                                    <td>@include('shop::admin.theme_one.order._payment-pill', ['order' => $order])</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-receipt"></i>No sales yet. Make your first sale from the POS.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card-soft mb-4">
            <div class="card-header"><h5 class="mb-0">Top products this month</h5></div>
            <div class="card-body">
                @forelse ($topProducts as $top)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <span class="text-truncate">{{ $top->product?->name ?? 'Deleted product' }}</span>
                        <span class="pill pill-blue">{{ $top->total_sold }} sold</span>
                    </div>
                @empty
                    <p class="text-muted mb-0 small">No sales this month yet.</p>
                @endforelse
            </div>
        </div>
        <div class="card-soft">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Needs attention</h5>
                <a class="small" href="{{ route('shop.admin.report.stock') }}">Stock report</a>
            </div>
            <div class="card-body">
                @forelse ($lowStockItems->take(5) as $p)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-truncate">{{ $p->name }}</span><span class="pill pill-amber">{{ $p->current_stock }} left</span>
                    </div>
                @empty
                @endforelse
                @foreach ($expiringSoon->take(5) as $p)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-truncate">{{ $p->name }}</span><span class="pill pill-red">expires {{ $p->expiry_date->format('j M') }}</span>
                    </div>
                @endforeach
                @if ($lowStockItems->isEmpty() && $expiringSoon->isEmpty())
                    <p class="text-muted mb-0 small"><i class="bi bi-check-circle text-success me-1"></i>No low stock or expiring products.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
