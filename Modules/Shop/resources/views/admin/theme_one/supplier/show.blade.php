@extends('shop::admin.theme_one.component.layout')
@section('title', $supplier->name)

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="{{ route('shop.admin.supplier.update', $supplier) }}">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">@include('shop::admin.theme_one.supplier._form-fields', ['supplier' => $supplier])</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>{{ $supplier->name }}</h2>
        <p>{{ collect([$supplier->company_name, $supplier->phone, $supplier->email, $supplier->pan_vat ? 'PAN/VAT '.$supplier->pan_vat : null])->filter()->implode(' · ') ?: 'No contact details' }}</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.supplier.index') }}"><i class="bi bi-arrow-left me-1"></i>Suppliers</a>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil me-1"></i>Edit</button>
        <a class="btn btn-primary btn-sm" href="{{ route('shop.admin.supplier.purchase.create', ['supplier_id' => $supplier->id]) }}"><i class="bi bi-cart-plus me-1"></i>New purchase</a>
        <form method="POST" action="{{ route('shop.admin.supplier.destroy', $supplier) }}" data-confirm="Delete {{ $supplier->name }}? Their purchases stay.">@csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button></form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-bag', 'label' => 'Total purchased', 'value' => $rs($supplier->total_purchases), 'tone' => 'blue'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-cash-stack', 'label' => 'Paid', 'value' => $rs($supplier->total_paid), 'tone' => 'green'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-hourglass-split', 'label' => 'Outstanding', 'value' => $rs(max(0, $supplier->outstanding)), 'tone' => $supplier->outstanding > 0 ? 'red' : 'gray'])</div>
</div>

<div class="card-soft table-card">
    <div class="card-header"><h5 class="mb-0">Purchases</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>PO</th><th>Date</th><th class="text-center">Items</th><th class="text-end">Total</th><th class="text-end">Paid</th><th>Payment</th><th></th></tr></thead>
                <tbody>
                    @forelse ($supplier->purchaseOrders as $po)
                        <tr>
                            <td class="fw-semibold">{{ $po->po_number }}</td>
                            <td class="text-muted">{{ $po->order_date?->format('j M Y') }}</td>
                            <td class="text-center">{{ $po->items_count }}</td>
                            <td class="text-end money">{{ $rs($po->total) }}</td>
                            <td class="text-end money">{{ $rs($po->paid_amount) }}</td>
                            <td>@include('shop::admin.theme_one.supplier._payment-pill', ['po' => $po])</td>
                            <td><a class="tbl-action view" href="{{ route('shop.admin.supplier.purchase.show', $po) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-row"><i class="bi bi-cart"></i>No purchases from this supplier yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
