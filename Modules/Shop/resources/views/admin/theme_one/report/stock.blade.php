@extends('shop::admin.theme_one.component.layout')
@section('title', 'Stock report')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Stock report</h2><p>Stock value (at cost), low stock and expiry.</p></div>
    <button class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-safe', 'label' => 'Stock value', 'value' => $rs($totalValue), 'tone' => 'blue'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-exclamation-triangle', 'label' => 'Low stock', 'value' => $lowStock->count(), 'tone' => 'amber'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-x-octagon', 'label' => 'Out of stock', 'value' => $outOfStock->count(), 'tone' => 'red'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-calendar-x', 'label' => 'Expired / expiring', 'value' => $expired->count().' / '.$expiringSoon->count(), 'tone' => 'purple'])</div>
</div>

<div class="card-soft table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Product</th><th>Category</th><th class="text-center">In</th><th class="text-center">Out</th><th class="text-center">Available</th><th class="text-end">Unit cost</th><th class="text-end">Value</th><th>Expiry</th></tr></thead>
                <tbody>
                    @forelse ($products as $p)
                        <tr>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td class="text-muted">{{ $p->category?->title }}</td>
                            <td class="text-center">{{ $p->stock_in }}</td>
                            <td class="text-center">{{ $p->stock_out }}</td>
                            <td class="text-center"><span class="pill {{ $p->stock_in === 0 ? 'pill-gray' : ($p->current_stock === 0 ? 'pill-red' : ($p->is_low_stock ? 'pill-amber' : 'pill-green')) }}">{{ $p->current_stock }}</span></td>
                            <td class="text-end money text-muted">{{ $rs($p->cost_price ?? $p->price) }}</td>
                            <td class="text-end money fw-semibold">{{ $rs($p->stock_value) }}</td>
                            <td>
                                @if ($p->is_expired)<span class="pill pill-red">Expired {{ $p->expiry_date->format('j M Y') }}</span>
                                @elseif ($p->is_expiring_soon)<span class="pill pill-amber">{{ $p->expiry_date->format('j M Y') }}</span>
                                @elseif ($p->expiry_date)<span class="text-muted small">{{ $p->expiry_date->format('j M Y') }}</span>
                                @else<span class="text-muted">—</span>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-row"><i class="bi bi-clipboard-data"></i>No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
