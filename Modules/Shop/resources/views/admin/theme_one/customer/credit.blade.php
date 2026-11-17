@extends('shop::admin.theme_one.component.layout')
@section('title', 'Credit (Udhar)')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Credit (Udhar)</h2><p>Customers who owe the shop money.</p></div>
    <a class="btn btn-outline-primary" href="{{ route('shop.admin.customer.index') }}"><i class="bi bi-people me-1"></i>All customers</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-journal-text', 'label' => 'Total to collect', 'value' => $rs($totalCredit), 'tone' => 'red'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-people', 'label' => 'Customers with credit', 'value' => $customers->count(), 'tone' => 'amber'])</div>
</div>

<div class="card-soft table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Customer</th><th>Phone</th><th class="text-end">Owes</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-semibold">{{ $customer->full_name }}</td>
                            <td class="text-muted">{{ $customer->phone_number ?: '—' }}</td>
                            <td class="text-end money fw-bold text-danger">{{ $rs($customer->udhar_amount) }}</td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('shop.admin.customer.show', $customer) }}">Receive payment</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-row"><i class="bi bi-emoji-smile"></i>Nobody owes the shop anything.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
