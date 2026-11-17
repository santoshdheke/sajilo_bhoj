@extends('shop::admin.theme_one.component.layout')
@section('title', 'Suppliers')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-title mb-4"><h2>Suppliers</h2><p>The dealers and wholesalers you buy stock from.</p></div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-truck text-primary"></i><h5 class="mb-0">Add Supplier</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.supplier.store') }}" class="row g-3">
                    @csrf
                    @include('shop::admin.theme_one.supplier._form-fields', ['supplier' => null])
                    <div class="col-12"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save Supplier</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Supplier List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $suppliers->count() }})</span></h5>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('shop.admin.supplier.purchase.create') }}"><i class="bi bi-cart-plus me-1"></i>New purchase</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Name</th><th>Phone</th><th class="text-center">Purchases</th><th class="text-end">Purchased</th><th class="text-end">Due</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                @php($due = max(0, (float) $supplier->purchased - (float) $supplier->paid))
                                <tr>
                                    <td class="fw-semibold">{{ $supplier->name }}<div class="text-muted small">{{ $supplier->company_name }}</div></td>
                                    <td class="text-muted">{{ $supplier->phone ?: '—' }}</td>
                                    <td class="text-center">{{ $supplier->purchase_orders_count }}</td>
                                    <td class="text-end money">{{ $rs($supplier->purchased ?? 0) }}</td>
                                    <td class="text-end money {{ $due > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $rs($due) }}</td>
                                    <td><a class="tbl-action view" title="Open" href="{{ route('shop.admin.supplier.show', $supplier) }}"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-row"><i class="bi bi-truck"></i>No suppliers yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
