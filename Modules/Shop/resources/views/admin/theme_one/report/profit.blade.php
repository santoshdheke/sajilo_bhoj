@extends('shop::admin.theme_one.component.layout')
@section('title', 'Profit report')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Profit &amp; loss</h2><p>{{ $from->format('j M Y') }} – {{ $to->format('j M Y') }}</p></div>
    @include('shop::admin.theme_one.report._range')
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-cash-coin', 'label' => 'Revenue', 'value' => $rs($revenue), 'tone' => 'blue'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-box', 'label' => 'Cost of goods sold', 'value' => $rs($cogs), 'tone' => 'amber'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-wallet2', 'label' => 'Expenses', 'value' => $rs($expenses), 'tone' => 'red'])</div>
    <div class="col-6 col-xl-3">@include('shop::admin.theme_one.component.kpi', ['icon' => 'bi-graph-up-arrow', 'label' => 'Net profit', 'value' => $rs($netProfit), 'tone' => $netProfit < 0 ? 'red' : 'green'])</div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-soft">
            <div class="card-header"><h5 class="mb-0">Statement</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Revenue (after discounts)</span><span class="money">{{ $rs($revenue) }}</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>– Cost of goods sold</span><span class="money">{{ $rs($cogs) }}</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom fw-semibold"><span>Gross profit</span><span class="money">{{ $rs($grossProfit) }}</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>– Expenses</span><span class="money">{{ $rs($expenses) }}</span></div>
                <div class="d-flex justify-content-between py-2 fw-bold fs-5 {{ $netProfit < 0 ? 'text-danger' : 'text-success' }}"><span>Net profit</span><span class="money">{{ $rs($netProfit) }}</span></div>
                <p class="text-muted small mb-0 mt-2">Cost of goods uses each product's current cost price.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-soft">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Expenses by category</h5><a class="small" href="{{ route('shop.admin.report.expenses') }}">Expense book</a></div>
            <div class="card-body">
                @forelse ($expenseByCategory as $row)
                    <div class="d-flex justify-content-between py-2 {{ ! $loop->last ? 'border-bottom' : '' }}"><span>{{ $row->category }}</span><span class="money fw-semibold">{{ $rs($row->total) }}</span></div>
                @empty
                    <p class="text-muted mb-0">No expenses in this period.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
