@extends('shop::admin.theme_one.component.layout')
@section('title', $po->po_number)

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>{{ $po->po_number }}</h2>
        <p>{{ $po->order_date?->format('j M Y') }} · {{ $po->supplier?->name ?? 'No supplier' }} · <span class="pill pill-green">{{ ucfirst($po->status) }}</span></p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.supplier.purchases') }}"><i class="bi bi-arrow-left me-1"></i>Purchases</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Items</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Unit cost</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @foreach ($po->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->product?->name ?? 'Deleted product' }}</td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->product?->unit }}</td>
                                    <td class="text-end money">{{ $rs($item->unit_price) }}</td>
                                    <td class="text-end money fw-semibold">{{ $rs($item->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-soft">
            <div class="card-header"><h5 class="mb-0">Summary</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span class="money">{{ $rs($po->subtotal) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Tax</span><span class="money">{{ $rs($po->tax_amount) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Discount</span><span class="money">- {{ $rs($po->discount) }}</span></div>
                <div class="d-flex justify-content-between fw-bold border-top mt-2 pt-2"><span>Total</span><span class="money">{{ $rs($po->total) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Paid</span><span class="money">{{ $rs($po->paid_amount) }}</span></div>
                <div class="d-flex justify-content-between {{ $po->due_amount > 0 ? 'text-danger fw-semibold' : '' }}"><span>Due</span><span class="money">{{ $rs($po->due_amount) }}</span></div>
                <div class="mt-3">@include('shop::admin.theme_one.supplier._payment-pill')</div>
                @if ($po->notes)<div class="mt-3 small text-muted">{{ $po->notes }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection
