@extends('shop::admin.theme_one.component.layout')
@section('title', 'POS / Sell')

@push('css')
<style>
.pos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; max-height: calc(100vh - 290px); overflow-y: auto; padding: 2px; }
.pos-item { border: 1px solid #e5e7eb; border-radius: 14px; background: #fff; padding: 10px; text-align: left; cursor: pointer; transition: .12s; display: flex; flex-direction: column; gap: 6px; }
.pos-item:hover { border-color: var(--primary); box-shadow: 0 6px 18px rgba(37,99,235,.12); }
.pos-item.out { opacity: .45; cursor: not-allowed; }
.pos-item .img { height: 86px; border-radius: 10px; background: #f1f5f9 center/cover no-repeat; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.6rem; }
.pos-item .nm { font-weight: 600; font-size: .86rem; line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.cart-line { display: grid; grid-template-columns: 1fr auto auto auto; gap: 8px; align-items: center; padding: 8px 0; border-bottom: 1px dashed #e5e7eb; }
.cart-line input { width: 64px; }
.cart-line .price { width: 90px; }
.pm-pill + label { border: 1px solid #e5e7eb; border-radius: 10px; padding: 6px 10px; cursor: pointer; font-size: .82rem; font-weight: 600; }
.pm-pill:checked + label { border-color: var(--primary); background: color-mix(in srgb, var(--primary) 10%, white); color: var(--primary); }
.totals div { display: flex; justify-content: space-between; padding: 3px 0; }
.totals .grand { font-size: 1.25rem; font-weight: 800; border-top: 1px solid #e5e7eb; margin-top: 4px; padding-top: 8px; }
</style>
@endpush

@section('content')
<div class="modal fade" id="quickCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>New customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-6"><label class="form-label fw-semibold">First name *</label><input class="form-control" id="qcFirst" maxlength="100"></div>
                <div class="col-6"><label class="form-label fw-semibold">Last name</label><input class="form-control" id="qcLast" maxlength="100"></div>
                <div class="col-6"><label class="form-label fw-semibold">Phone</label><input class="form-control" id="qcPhone" maxlength="20"></div>
                <div class="col-6"><label class="form-label fw-semibold">Address</label><input class="form-control" id="qcAddress" maxlength="255"></div>
                <div class="col-12 text-danger small d-none" id="qcError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="qcSave"><i class="bi bi-check-lg me-1"></i>Add customer</button>
            </div>
        </div>
    </div>
</div>

<div class="page-head mb-3">
    <div class="page-title mb-0"><h2>Point of Sale</h2><p>Click products or scan a barcode to add them to the bill.</p></div>
    <a class="btn btn-outline-primary btn-sm" href="{{ route('shop.admin.order.index') }}"><i class="bi bi-clock-history me-1"></i>Sales history</a>
</div>

<form action="{{ route('shop.admin.product.sell.store') }}" method="POST" id="posForm">
    @csrf
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card-soft">
                <div class="card-header">
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                        <input class="form-control" id="productSearch" placeholder="Search by name, SKU or scan a barcode and press Enter" autofocus autocomplete="off"></div>
                </div>
                <div class="card-body">
                    <div class="pos-grid" id="productGrid"></div>
                    <p class="text-muted text-center my-4 d-none" id="noProducts">No products match. <a href="{{ route('shop.admin.product.create') }}">Add a product</a>.</p>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card-soft form-panel">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cart3 me-2 text-primary"></i>Bill</h5>
                    <button type="button" class="btn btn-sm btn-link text-danger" id="clearCart">Clear</button>
                </div>
                <div class="card-body">
                    <div id="cartLines"><p class="text-muted small text-center my-3" id="cartEmpty">The bill is empty.</p></div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold mb-1">Customer</label>
                        <div class="input-group">
                            <select class="form-select" name="customer_id" id="customerSelect">
                                <option value="">Walk-in customer</option>
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickCustomer" title="New customer"><i class="bi bi-person-plus"></i></button>
                        </div>
                        <div class="small mt-1" id="customerBalance"></div>
                        <div class="row g-2 mt-1" id="walkinFields">
                            <div class="col-6"><input class="form-control form-control-sm" name="customer_name" placeholder="Name (optional)" value="{{ old('customer_name') }}"></div>
                            <div class="col-6"><input class="form-control form-control-sm" name="customer_phone" placeholder="Phone (optional)" value="{{ old('customer_phone') }}"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold mb-1">Payment</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($paymentMethods as $value => $label)
                                <input type="radio" class="btn-check pm-pill" name="payment_method" id="pm_{{ $value }}" value="{{ $value }}" @checked(old('payment_method', 'cash') === $value)>
                                <label for="pm_{{ $value }}">{{ $label }}</label>
                            @endforeach
                        </div>
                        <input class="form-control form-control-sm mt-2 d-none" name="transaction_code" id="txnCode" placeholder="Transaction / reference code" value="{{ old('transaction_code') }}" maxlength="100">
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-6"><label class="form-label small fw-semibold mb-1">Discount (Rs.)</label>
                            <input class="form-control" type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', 0) }}"></div>
                        <div class="col-6"><label class="form-label small fw-semibold mb-1">Paid (Rs.)</label>
                            <input class="form-control" type="number" step="0.01" min="0" name="paid_amount" id="paid" value="{{ old('paid_amount') }}"></div>
                    </div>

                    <div class="totals mt-3">
                        <div><span class="text-muted">Subtotal</span><span class="money" id="tSubtotal">Rs. 0</span></div>
                        <div><span class="text-muted">Discount</span><span class="money" id="tDiscount">Rs. 0</span></div>
                        <div class="grand"><span>Total</span><span class="money" id="tTotal">Rs. 0</span></div>
                        <div id="tChangeRow"><span class="text-muted" id="tChangeLabel">Change</span><span class="money fw-semibold" id="tChange">Rs. 0</span></div>
                    </div>

                    <button class="btn btn-primary w-100 mt-3 py-2" id="completeBtn" disabled><i class="bi bi-check2-circle me-1"></i>Complete sale</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
(function () {
    var PRODUCTS = @json($products), CUSTOMERS = @json($customers), CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var cart = [], grid = document.getElementById('productGrid'), search = document.getElementById('productSearch');
    var fmt = function (n) { n = Math.round((+n || 0) * 100) / 100; return 'Rs. ' + n.toLocaleString('en-IN', { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 }); };
    var esc = function (t) { var d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; };

    // ── Products ──
    function renderProducts() {
        var q = search.value.trim().toLowerCase();
        var list = PRODUCTS.filter(function (p) {
            return !q || p.name.toLowerCase().indexOf(q) !== -1 || (p.sku || '').toLowerCase() === q || (p.barcode || '').toLowerCase() === q;
        });
        grid.innerHTML = list.map(function (p) {
            return '<button type="button" class="pos-item' + (p.stock <= 0 ? ' out' : '') + '" data-id="' + p.id + '">'
                + '<span class="img"' + (p.image ? ' style="background-image:url(\'' + encodeURI(p.image) + '\')"' : '') + '>' + (p.image ? '' : '<i class="bi bi-box"></i>') + '</span>'
                + '<span class="nm">' + esc(p.name) + '</span>'
                + '<span class="d-flex justify-content-between small"><b class="money">' + fmt(p.price) + '</b><span class="' + (p.stock <= 0 ? 'text-danger' : 'text-muted') + '">' + (p.stock <= 0 ? 'Out' : p.stock + ' ' + esc(p.unit || 'left')) + '</span></span>'
                + '</button>';
        }).join('');
        document.getElementById('noProducts').classList.toggle('d-none', list.length > 0);
    }
    grid.addEventListener('click', function (e) { var b = e.target.closest('.pos-item'); if (b) add(+b.dataset.id); });
    search.addEventListener('input', renderProducts);
    // A barcode scanner types the code and presses Enter: add the exact match.
    search.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        var q = search.value.trim().toLowerCase();
        var hit = PRODUCTS.find(function (p) { return (p.barcode || '').toLowerCase() === q || (p.sku || '').toLowerCase() === q; })
               || (PRODUCTS.filter(function (p) { return p.name.toLowerCase().indexOf(q) !== -1; }).length === 1 ? PRODUCTS.find(function (p) { return p.name.toLowerCase().indexOf(q) !== -1; }) : null);
        if (hit) { add(hit.id); search.value = ''; renderProducts(); }
    });

    // ── Cart ──
    function add(id) {
        var p = PRODUCTS.find(function (x) { return x.id === id; }); if (!p) return;
        var line = cart.find(function (l) { return l.id === id; });
        var inCart = line ? line.qty : 0;
        if (inCart + 1 > p.stock) { notify.error('Only ' + p.stock + ' of "' + p.name + '" in stock.'); return; }
        if (line) line.qty++; else cart.push({ id: p.id, name: p.name, price: p.price, qty: 1, stock: p.stock });
        renderCart();
    }
    function renderCart() {
        var box = document.getElementById('cartLines');
        box.innerHTML = cart.length ? '' : '<p class="text-muted small text-center my-3">The bill is empty.</p>';
        cart.forEach(function (l, i) {
            var row = document.createElement('div');
            row.className = 'cart-line';
            row.innerHTML = '<div><div class="fw-semibold small">' + esc(l.name) + '</div><div class="text-muted" style="font-size:.75rem;">' + l.stock + ' in stock</div>'
                + '<input type="hidden" name="items[' + i + '][product_id]" value="' + l.id + '"></div>'
                + '<input class="form-control form-control-sm" type="number" min="1" max="' + l.stock + '" name="items[' + i + '][quantity]" value="' + l.qty + '">'
                + '<input class="form-control form-control-sm price" type="number" step="0.01" min="0" name="items[' + i + '][unit_price]" value="' + l.price + '" title="Unit price">'
                + '<button type="button" class="btn btn-sm btn-link text-danger p-0" title="Remove"><i class="bi bi-x-lg"></i></button>';
            row.querySelector('[name$="[quantity]"]').addEventListener('input', function () { l.qty = Math.max(1, Math.min(l.stock, +this.value || 1)); totals(); });
            row.querySelector('[name$="[quantity]"]').addEventListener('change', function () { this.value = l.qty; });
            row.querySelector('.price').addEventListener('input', function () { l.price = Math.max(0, +this.value || 0); totals(); });
            row.querySelector('button').addEventListener('click', function () { cart.splice(i, 1); renderCart(); });
            box.appendChild(row);
        });
        totals();
    }
    document.getElementById('clearCart').addEventListener('click', function () { cart = []; renderCart(); });

    // ── Totals: paid less than the total becomes the customer's credit ──
    var paidInput = document.getElementById('paid'), paidTouched = paidInput.value !== '';
    paidInput.addEventListener('input', function () { paidTouched = true; totals(); });
    document.getElementById('discount').addEventListener('input', totals);
    function totals() {
        var sub = cart.reduce(function (s, l) { return s + l.price * l.qty; }, 0);
        var disc = Math.min(sub, Math.max(0, +document.getElementById('discount').value || 0));
        var total = Math.max(0, sub - disc);
        if (!paidTouched) paidInput.value = total ? total : '';
        var paid = +paidInput.value || 0, diff = paid - total;
        document.getElementById('tSubtotal').textContent = fmt(sub);
        document.getElementById('tDiscount').textContent = '- ' + fmt(disc);
        document.getElementById('tTotal').textContent = fmt(total);
        document.getElementById('tChangeLabel').textContent = diff >= 0 ? 'Change' : 'Credit (udhar)';
        document.getElementById('tChange').textContent = fmt(Math.abs(diff));
        document.getElementById('tChange').className = 'money fw-semibold ' + (diff < 0 ? 'text-danger' : 'text-success');
        document.getElementById('completeBtn').disabled = !cart.length;
    }

    // ── Customers ──
    var select = document.getElementById('customerSelect');
    function fillCustomers(selected) {
        select.innerHTML = '<option value="">Walk-in customer</option>' + CUSTOMERS.map(function (c) {
            return '<option value="' + c.id + '">' + esc(c.name) + (c.phone ? ' · ' + esc(c.phone) : '') + '</option>';
        }).join('');
        if (selected) select.value = selected;
        onCustomer();
    }
    function onCustomer() {
        var c = CUSTOMERS.find(function (x) { return String(x.id) === select.value; });
        document.getElementById('walkinFields').classList.toggle('d-none', !!c);
        document.getElementById('customerBalance').innerHTML = c && c.balance < 0
            ? '<span class="pill pill-amber">Already owes ' + fmt(-c.balance) + '</span>' : '';
    }
    select.addEventListener('change', onCustomer);

    var val = function (id) { return document.getElementById(id).value.trim(); };
    document.getElementById('qcSave').addEventListener('click', function () {
        var err = document.getElementById('qcError'); err.classList.add('d-none');
        fetch(@json(route('shop.admin.customer.quick_store')), {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ first_name: val('qcFirst'), last_name: val('qcLast'), phone_number: val('qcPhone'), address_one: val('qcAddress') })
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); }).then(function (res) {
            if (!res.ok) { err.textContent = res.d.message || 'Could not add the customer.'; err.classList.remove('d-none'); return; }
            CUSTOMERS.push(res.d); fillCustomers(res.d.id);
            bootstrap.Modal.getInstance(document.getElementById('quickCustomer')).hide();
            ['qcFirst', 'qcLast', 'qcPhone', 'qcAddress'].forEach(function (id) { document.getElementById(id).value = ''; });
            notify.success('Customer added.');
        });
    });

    // ── Payment method: digital payments ask for a reference code ──
    function onMethod() {
        var m = (document.querySelector('[name="payment_method"]:checked') || {}).value;
        document.getElementById('txnCode').classList.toggle('d-none', ['esewa', 'khalti', 'bank', 'card'].indexOf(m) === -1);
    }
    document.querySelectorAll('[name="payment_method"]').forEach(function (r) { r.addEventListener('change', onMethod); });

    document.getElementById('posForm').addEventListener('submit', function (e) {
        var total = cart.reduce(function (s, l) { return s + l.price * l.qty; }, 0) - (+document.getElementById('discount').value || 0);
        if ((+paidInput.value || 0) < total && !select.value) {
            e.preventDefault(); notify.error('Choose a customer for a credit sale, or enter the full amount paid.');
        }
    });

    fillCustomers(@json(old('customer_id')));
    onMethod();
    renderProducts();
    renderCart();
})();
</script>
@endpush
