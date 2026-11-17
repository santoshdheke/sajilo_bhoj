@extends('shop::admin.theme_one.component.layout')
@section('title', 'New purchase')

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>New purchase</h2><p>Record stock you received. The quantities are added to stock right away.</p></div>
    <a class="btn btn-outline-primary" href="{{ route('shop.admin.supplier.purchases') }}"><i class="bi bi-arrow-left me-1"></i>Purchases</a>
</div>

@if ($products->isEmpty())
    <div class="alert alert-info">Add your products first, then record purchases. <a href="{{ route('shop.admin.product.create') }}">Add a product</a></div>
@endif

<form method="POST" action="{{ route('shop.admin.supplier.purchase.store') }}" id="poForm">
    @csrf
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card-soft table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Items</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addLine"><i class="bi bi-plus"></i> Add line</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 no-stack">
                            <thead><tr><th style="min-width:220px;">Product</th><th style="width:110px;">Qty</th><th style="width:150px;">Unit cost</th><th class="text-end" style="width:130px;">Amount</th><th style="width:40px;"></th></tr></thead>
                            <tbody id="lines"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card-soft form-panel">
                <div class="card-header"><h5 class="mb-0">Purchase details</h5></div>
                <div class="card-body row g-3">
                    <div class="col-12"><label class="form-label fw-semibold">Supplier</label>
                        <select class="form-select" name="supplier_id">
                            <option value="">— None —</option>
                            @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $supplierId) == $s->id)>{{ $s->name }}</option>@endforeach
                        </select>
                        <div class="form-text"><a href="{{ route('shop.admin.supplier.index') }}">Add a supplier</a></div></div>
                    <div class="col-12"><label class="form-label fw-semibold">Date</label>
                        <input class="form-control" type="date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}"></div>
                    <div class="col-6"><label class="form-label fw-semibold">Tax (Rs.)</label>
                        <input class="form-control" type="number" step="0.01" min="0" name="tax_amount" id="tax" value="{{ old('tax_amount', 0) }}"></div>
                    <div class="col-6"><label class="form-label fw-semibold">Discount (Rs.)</label>
                        <input class="form-control" type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', 0) }}"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Paid now (Rs.)</label>
                        <input class="form-control" type="number" step="0.01" min="0" name="paid_amount" id="paid" value="{{ old('paid_amount', 0) }}"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" maxlength="1000">{{ old('notes') }}</textarea></div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span class="money" id="tSub">Rs. 0</span></div>
                        <div class="d-flex justify-content-between fw-bold fs-5 border-top mt-2 pt-2"><span>Total</span><span class="money" id="tTotal">Rs. 0</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Due to supplier</span><span class="money text-danger" id="tDue">Rs. 0</span></div>
                    </div>
                    <div class="col-12"><button class="btn btn-primary w-100" @disabled($products->isEmpty())><i class="bi bi-check-lg me-1"></i>Save purchase</button></div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
(function () {
    var PRODUCTS = @json($products), OLD = @json(old('items', []));
    var tbody = document.getElementById('lines'), n = 0;
    var fmt = function (v) { v = Math.round((+v || 0) * 100) / 100; return 'Rs. ' + v.toLocaleString('en-IN', { maximumFractionDigits: 2 }); };
    var options = '<option value="">Choose product...</option>' + PRODUCTS.map(function (p) {
        var d = document.createElement('div'); d.textContent = p.name + (p.sku ? ' (' + p.sku + ')' : '');
        return '<option value="' + p.id + '" data-cost="' + (p.cost_price || p.price || 0) + '">' + d.innerHTML + '</option>';
    }).join('');

    function addLine(item) {
        item = item || {};
        var i = n++, tr = document.createElement('tr');
        tr.innerHTML = '<td><select class="form-select form-select-sm" name="items[' + i + '][product_id]" required>' + options + '</select></td>'
            + '<td><input class="form-control form-control-sm" type="number" min="1" name="items[' + i + '][quantity]" value="' + (item.quantity || 1) + '" required></td>'
            + '<td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[' + i + '][unit_price]" value="' + (item.unit_price || '') + '" required></td>'
            + '<td class="text-end money amount">Rs. 0</td>'
            + '<td><button type="button" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-lg"></i></button></td>';
        var sel = tr.querySelector('select'), cost = tr.querySelector('[name$="[unit_price]"]');
        sel.value = item.product_id || '';
        sel.addEventListener('change', function () { if (!cost.value) cost.value = sel.selectedOptions[0].dataset.cost || ''; totals(); });
        tr.querySelectorAll('input').forEach(function (inp) { inp.addEventListener('input', totals); });
        tr.querySelector('button').addEventListener('click', function () { tr.remove(); totals(); });
        tbody.appendChild(tr);
        totals();
    }
    function totals() {
        var sub = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var amt = (+tr.querySelector('[name$="[quantity]"]').value || 0) * (+tr.querySelector('[name$="[unit_price]"]').value || 0);
            tr.querySelector('.amount').textContent = fmt(amt); sub += amt;
        });
        var total = Math.max(0, sub + (+document.getElementById('tax').value || 0) - (+document.getElementById('discount').value || 0));
        document.getElementById('tSub').textContent = fmt(sub);
        document.getElementById('tTotal').textContent = fmt(total);
        document.getElementById('tDue').textContent = fmt(Math.max(0, total - (+document.getElementById('paid').value || 0)));
    }
    ['tax', 'discount', 'paid'].forEach(function (id) { document.getElementById(id).addEventListener('input', totals); });
    document.getElementById('addLine').addEventListener('click', function () { addLine(); });
    (OLD.length ? OLD : [{}]).forEach(addLine);
})();
</script>
@endpush
