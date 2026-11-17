@extends('shop::admin.theme_one.component.layout')
@section('title', $customer->full_name)

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="{{ route('shop.admin.customer.update', $customer) }}">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">@include('shop::admin.theme_one.customer._form-fields', ['customer' => $customer])</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>{{ $customer->full_name }}</h2>
        <p>{{ collect([$customer->phone_number, $customer->email, $customer->address_one])->filter()->implode(' · ') ?: 'No contact details' }}</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.customer.index') }}"><i class="bi bi-arrow-left me-1"></i>Customers</a>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil me-1"></i>Edit</button>
        <form method="POST" action="{{ route('shop.admin.customer.destroy', $customer) }}" data-confirm="Delete {{ $customer->full_name }}? Their past orders stay.">@csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button></form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft mb-4 text-center p-4">
            <div class="text-muted small">Balance</div>
            @if ($customer->is_in_debt)
                <div class="fs-2 fw-bold text-danger money">{{ $rs($customer->udhar_amount) }}</div><div class="pill pill-red">Owes the shop (udhar)</div>
            @elseif ($customer->balance > 0)
                <div class="fs-2 fw-bold text-success money">{{ $rs($customer->balance) }}</div><div class="pill pill-green">Advance</div>
            @else
                <div class="fs-2 fw-bold money">Rs. 0</div><div class="pill pill-gray">Settled</div>
            @endif
        </div>
        <div class="card-soft">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>Receive payment</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.customer.payment', $customer) }}">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Amount (Rs.)</label>
                        <input class="form-control" type="number" step="0.01" min="0.01" name="amount" value="{{ $customer->is_in_debt ? $customer->udhar_amount : '' }}" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Note</label>
                        <input class="form-control" name="description" maxlength="255" placeholder="Payment received"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Reference</label>
                        <input class="form-control" name="reference" maxlength="100" placeholder="Receipt / txn no. (optional)"></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Record payment</button>
                </form>
            </div>
        </div>
        @if ($customer->notes)<div class="card-soft mt-4 p-3 small"><div class="text-muted mb-1">Notes</div>{{ $customer->notes }}</div>@endif
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card mb-4">
            <div class="card-header"><h5 class="mb-0">Account history</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Date</th><th>Type</th><th>Details</th><th class="text-end">Amount</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            @forelse ($transactions as $t)
                                <tr>
                                    <td class="text-muted small">{{ $t->created_at->format('j M Y, g:i A') }}</td>
                                    <td><span class="pill {{ $t->amount < 0 ? 'pill-amber' : 'pill-green' }}">{{ ucfirst($t->type) }}</span></td>
                                    <td>{{ $t->description }}@if ($t->reference)<div class="text-muted small">Ref: {{ $t->reference }}</div>@endif</td>
                                    <td class="text-end money {{ $t->amount < 0 ? 'text-danger' : 'text-success' }}">{{ $t->amount < 0 ? '-' : '+' }} {{ $rs(abs($t->amount)) }}</td>
                                    <td class="text-end money">{{ $rs($t->balance_after) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-journal"></i>No credit sales or payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($transactions->hasPages())<div class="card-footer bg-transparent">{{ $transactions->links() }}</div>@endif
        </div>
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Recent orders</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Order</th><th>Date</th><th class="text-end">Total</th><th>Payment</th></tr></thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td><a class="fw-semibold text-decoration-none" href="{{ route('shop.admin.order.show', $order) }}">{{ $order->order_number_format }}</a></td>
                                    <td class="text-muted small">{{ $order->created_at->format('j M Y') }}</td>
                                    <td class="text-end money">{{ $rs($order->grand_total) }}</td>
                                    <td>@include('shop::admin.theme_one.order._payment-pill')</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty-row"><i class="bi bi-receipt"></i>No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
