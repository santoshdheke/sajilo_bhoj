@extends('shop::admin.theme_one.component.layout')
@section('title', 'Purchases')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Purchases</h2><p>Stock you bought. Each purchase adds its quantities to stock.</p></div>
    <a class="btn btn-primary" href="{{ route('shop.admin.supplier.purchase.create') }}"><i class="bi bi-cart-plus me-1"></i>New purchase</a>
</div>

<div class="card-soft table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>PO</th><th>Supplier</th><th>Date</th><th class="text-center">Items</th><th class="text-end">Total</th><th class="text-end">Due</th><th>Payment</th><th></th></tr></thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                        <tr>
                            <td class="fw-semibold">{{ $po->po_number }}</td>
                            <td>{{ $po->supplier?->name ?? '—' }}</td>
                            <td class="text-muted">{{ $po->order_date?->format('j M Y') }}</td>
                            <td class="text-center">{{ $po->items_count }}</td>
                            <td class="text-end money fw-semibold">{{ $rs($po->total) }}</td>
                            <td class="text-end money {{ $po->due_amount > 0 ? 'text-danger' : 'text-muted' }}">{{ $rs($po->due_amount) }}</td>
                            <td>@include('shop::admin.theme_one.supplier._payment-pill')</td>
                            <td><a class="tbl-action view" href="{{ route('shop.admin.supplier.purchase.show', $po) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-row"><i class="bi bi-cart-plus"></i>No purchases yet. <a href="{{ route('shop.admin.supplier.purchase.create') }}">Record your first stock purchase</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($purchaseOrders->hasPages())<div class="card-footer bg-transparent">{{ $purchaseOrders->links() }}</div>@endif
</div>
@endsection
