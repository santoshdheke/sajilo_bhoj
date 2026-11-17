@extends('shop::admin.theme_one.component.layout')
@section('title', 'Inventory')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Inventory</h2><p>Stock in comes from purchases, stock out from sales.</p></div>
    <a class="btn btn-primary" href="{{ route('shop.admin.supplier.purchase.create') }}"><i class="bi bi-cart-plus me-1"></i>Add stock (purchase)</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-box', 'label' => 'Products', 'value' => number_format($totalProducts), 'tone' => 'blue'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-box-arrow-in-down', 'label' => 'Stock in', 'value' => number_format($totalStockIn), 'tone' => 'green'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-box-arrow-up', 'label' => 'Stock out (sold)', 'value' => number_format($totalStockOut), 'tone' => 'purple'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-exclamation-triangle', 'label' => 'Low stock', 'value' => number_format($lowStockItems->count()), 'tone' => $lowStockItems->isEmpty() ? 'gray' : 'amber', 'sub' => number_format($totalAvailable).' units available'])</div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Stock by product</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Product</th><th>Category</th><th class="text-center">In</th><th class="text-center">Out</th><th class="text-center">Available</th></tr></thead>
                        <tbody>
                            @forelse ($products as $p)
                                <tr>
                                    <td class="fw-semibold">{{ $p->name }}<div class="text-muted small">{{ $p->sku }}</div></td>
                                    <td class="text-muted">{{ $p->category?->title }}</td>
                                    <td class="text-center">{{ $p->stock_in }}</td>
                                    <td class="text-center">{{ $p->stock_out }}</td>
                                    <td class="text-center">
                                        <span class="pill {{ $p->stock_in === 0 ? 'pill-gray' : ($p->current_stock === 0 ? 'pill-red' : ($p->is_low_stock ? 'pill-amber' : 'pill-green')) }}">{{ $p->current_stock }} {{ $p->unit }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-box-seam"></i>No products yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-soft">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Recent purchases</h5><a class="small" href="{{ route('shop.admin.supplier.purchases') }}">All</a></div>
            <div class="card-body">
                @forelse ($recentPurchases as $po)
                    <a class="d-flex justify-content-between align-items-center py-2 text-decoration-none text-reset {{ ! $loop->last ? 'border-bottom' : '' }}" href="{{ route('shop.admin.supplier.purchase.show', $po) }}">
                        <span><span class="fw-semibold">{{ $po->po_number }}</span><div class="text-muted small">{{ $po->supplier?->name ?? 'No supplier' }} · {{ $po->items_count }} items</div></span>
                        <span class="money fw-semibold">{{ $rs($po->total) }}</span>
                    </a>
                @empty
                    <p class="text-muted small mb-0">No purchases yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
