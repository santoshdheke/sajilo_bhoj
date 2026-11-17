@extends('shop::admin.theme_one.component.layout')
@section('title', 'Expenses')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-title mb-4"><h2>Expenses</h2><p>Rent, salaries, bills and other shop costs. Used in the profit report.</p></div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft mb-4 text-center p-4">
            <div class="text-muted small">This month</div>
            <div class="fs-2 fw-bold money">{{ $rs($monthTotal) }}</div>
        </div>
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-wallet2 text-primary"></i><h5 class="mb-0">Add expense</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.report.expenses.store') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <input class="form-control" name="category" list="expenseCategories" value="{{ old('category') }}" required maxlength="100" placeholder="e.g. Rent">
                        <datalist id="expenseCategories">@foreach ($categories as $c)<option value="{{ $c }}">@endforeach</datalist></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Amount (Rs.) <span class="text-danger">*</span></label>
                        <input class="form-control" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Description</label>
                        <input class="form-control" name="description" value="{{ old('description') }}" maxlength="255"></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Reference</label>
                        <input class="form-control" name="reference" value="{{ old('reference') }}" maxlength="100" placeholder="Bill no. (optional)"></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save expense</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Expense book</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Date</th><th>Category</th><th>Description</th><th class="text-end">Amount</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($expenses as $expense)
                                <tr>
                                    <td class="text-muted">{{ $expense->expense_date->format('j M Y') }}</td>
                                    <td><span class="pill pill-gray">{{ $expense->category }}</span></td>
                                    <td>{{ $expense->description ?: '—' }}@if ($expense->reference)<div class="text-muted small">Ref: {{ $expense->reference }}</div>@endif</td>
                                    <td class="text-end money fw-semibold">{{ $rs($expense->amount) }}</td>
                                    <td><form method="POST" action="{{ route('shop.admin.report.expenses.destroy', $expense) }}" data-confirm="Delete this expense?">@csrf @method('DELETE')
                                        <button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button></form></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-wallet2"></i>No expenses recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($expenses->hasPages())<div class="card-footer bg-transparent">{{ $expenses->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
