@extends('shop::admin.theme_one.component.layout')
@section('title', 'Customers')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="{{ route('shop.admin.customer.store') }}">
            @csrf
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">@include('shop::admin.theme_one.customer._form-fields', ['customer' => null])</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save customer</button>
            </div>
        </form>
    </div>
</div>

<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Customers</h2><p>Your regular customers and their credit (udhar) balance.</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('shop.admin.customer.credit') }}"><i class="bi bi-journal-text me-1"></i>Credit list</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg me-1"></i>Add Customer</button>
    </div>
</div>

<div class="card-soft table-card">
    <div class="card-header">
        <form class="filter-bar d-flex flex-wrap gap-2" method="get">
            <input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Name or phone" style="max-width:240px;">
            <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Search</button>
            @if (request('search'))<a class="btn btn-sm btn-link" href="{{ route('shop.admin.customer.index') }}">Clear</a>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Name</th><th>Phone</th><th>Address</th><th class="text-center">Orders</th><th class="text-end">Balance</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-semibold">{{ $customer->full_name }}</td>
                            <td class="text-muted">{{ $customer->phone_number ?: '—' }}</td>
                            <td class="text-muted">{{ $customer->address_one ?: '—' }}</td>
                            <td class="text-center">{{ $customer->orders_count }}</td>
                            <td class="text-end">
                                @if ($customer->is_in_debt)<span class="pill pill-red money">Owes {{ $rs($customer->udhar_amount) }}</span>
                                @elseif ($customer->balance > 0)<span class="pill pill-green money">Advance {{ $rs($customer->balance) }}</span>
                                @else<span class="text-muted">—</span>@endif
                            </td>
                            <td><a class="tbl-action view" title="Open" href="{{ route('shop.admin.customer.show', $customer) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row"><i class="bi bi-person-hearts"></i>No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($customers->hasPages())<div class="card-footer bg-transparent">{{ $customers->links() }}</div>@endif
</div>
@endsection
