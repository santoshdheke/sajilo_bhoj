@extends('admin.theme_one.component.layout')

@section('content')
<style>
/* ── Billing page ─────────────────────────────────────────────── */

/* Stat cards */
.bill-stat        { padding:22px 24px; position:relative; overflow:hidden; }
.bill-stat .ico   { width:50px; height:50px; border-radius:16px; display:flex;
                    align-items:center; justify-content:center; font-size:1.3rem;
                    margin-bottom:14px; }
.bill-stat .val   { font-size:1.7rem; font-weight:800; line-height:1.1; }
.bill-stat .lbl   { font-size:.82rem; color:var(--muted); margin-top:4px; }
.bill-stat .trend { font-size:.78rem; font-weight:600; margin-top:6px; }
.bill-stat::after { content:''; position:absolute; right:-28px; bottom:-28px;
                    width:100px; height:100px; border-radius:50%;
                    background:color-mix(in srgb, var(--primary) 7%, transparent); pointer-events:none; }

/* Invoice header strip */
.inv-header       { display:flex; align-items:center; justify-content:space-between;
                    flex-wrap:wrap; gap:10px; padding:16px 20px;
                    background:linear-gradient(135deg,color-mix(in srgb, var(--primary) 9%, white),color-mix(in srgb, var(--primary) 14%, white));
                    border-radius:14px; margin-bottom:18px;
                    border:1px solid color-mix(in srgb, var(--primary) 12%, transparent); }
.inv-meta         { display:flex; gap:18px; flex-wrap:wrap; }
.inv-meta-item    { display:flex; flex-direction:column; }
.inv-meta-item .k { font-size:.74rem; color:var(--muted); text-transform:uppercase;
                    letter-spacing:.06em; }
.inv-meta-item .v { font-size:.93rem; font-weight:600; color:var(--text); }

/* Item table */
.inv-table        { width:100%; border-collapse:collapse; }
.inv-table thead th { font-size:.76rem; text-transform:uppercase; letter-spacing:.06em;
                      color:var(--muted); padding:8px 10px; border-bottom:1px solid var(--line);
                      font-weight:600; }
.inv-table tbody td { padding:10px 10px; border-bottom:1px solid rgba(20,40,20,.04);
                      vertical-align:middle; font-size:.93rem; }
.inv-table tbody tr:last-child td { border-bottom:none; }
.inv-table tbody tr:hover td     { background:#f8fbf9; }

/* Qty control */
.qty-ctrl         { display:inline-flex; align-items:center; gap:0;
                    border:1px solid rgba(20,40,20,.12); border-radius:10px;
                    overflow:hidden; }
.qty-btn          { width:28px; height:28px; border:none; background:color-mix(in srgb, var(--primary) 9%, white);
                    color:var(--primary); font-size:1rem; font-weight:700;
                    cursor:pointer; display:flex; align-items:center; justify-content:center;
                    transition:.12s; line-height:1; }
.qty-btn:hover    { background:var(--primary); color:#fff; }
.qty-val          { width:32px; text-align:center; font-weight:600; font-size:.88rem;
                    background:#fff; padding:0 2px; }

/* Remove item btn */
.btn-rm           { width:26px; height:26px; border:none; border-radius:8px;
                    background:#fff0f0; color:#dc3545; font-size:1rem; font-weight:700;
                    cursor:pointer; display:inline-flex; align-items:center;
                    justify-content:center; transition:.12s; }
.btn-rm:hover     { background:#dc3545; color:#fff; }

/* Add item row */
.add-item-row     { padding:10px; text-align:center; }

/* Payment method cards */
.pay-methods      { display:flex; gap:10px; margin-top:6px; }
.pay-card         { flex:1; position:relative; }
.pay-card input   { position:absolute; opacity:0; width:0; height:0; }
.pay-card label   { display:flex; flex-direction:column; align-items:center; gap:6px;
                    padding:12px 8px; border-radius:14px; border:2px solid var(--line);
                    cursor:pointer; font-size:.82rem; font-weight:600;
                    color:var(--muted); background:#f8fbf9; transition:.15s; }
.pay-card label i { font-size:1.35rem; }
.pay-card input:checked + label { border-color:var(--primary); background:color-mix(in srgb, var(--primary) 10%, white);
                                   color:var(--primary); }
.pay-card label:hover { border-color:color-mix(in srgb, var(--primary) 35%, transparent); background:color-mix(in srgb, var(--primary) 9%, white); }

/* Grand total highlight */
.grand-total-row  { background:linear-gradient(135deg,var(--primary),color-mix(in srgb, var(--primary) 75%, white)) !important;
                    border-radius:14px !important; padding:16px 18px !important; }
.grand-total-row span, .grand-total-row strong { color:#fff !important; font-size:1rem; }
.grand-total-row strong { font-size:1.25rem !important; }

/* Discount input */
.disc-wrap        { display:flex; gap:6px; align-items:center; }
.disc-wrap select { width:64px; padding:.45rem .5rem; font-size:.85rem; border-radius:10px; }
.disc-wrap input  { max-width:90px; padding:.45rem .7rem; font-size:.85rem; }

/* Recent bills table */
.bill-no          { font-family:monospace; font-size:.85rem; font-weight:700;
                    color:var(--primary); }
.method-badge     { display:inline-flex; align-items:center; gap:4px; padding:4px 10px;
                    border-radius:8px; font-size:.78rem; font-weight:600; }
.method-cash      { background:#fdf5ee; color:#7c4a1e; }
.method-card      { background:#eef0ff; color:#3730a3; }
.method-online    { background:#e0f2fe; color:#0369a1; }

/* Action icon btn */
.tbl-action       { width:30px; height:30px; border:none; border-radius:9px;
                    background:color-mix(in srgb, var(--primary) 9%, white); color:var(--primary); font-size:.85rem;
                    cursor:pointer; display:inline-flex; align-items:center;
                    justify-content:center; transition:.12s; }
.tbl-action:hover { background:var(--primary); color:#fff; }
.tbl-action.danger { background:#fff0f0; color:#dc3545; }
.tbl-action.danger:hover { background:#dc3545; color:#fff; }

/* Section divider label */
.section-sep      { display:flex; align-items:center; gap:10px; color:var(--muted);
                    font-size:.78rem; text-transform:uppercase; letter-spacing:.07em;
                    margin:18px 0 14px; }
.section-sep::before,.section-sep::after { content:''; flex:1; height:1px;
                    background:var(--line); }
</style>

<div class="page-title mb-4">
    <h2>Billing</h2>
    <p>Generate invoices, apply discounts, and capture payment details.</p>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card-soft bill-stat">
            <div class="ico" style="background:color-mix(in srgb, var(--primary) 10%, white); color:var(--primary);">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="val">$1,842.50</div>
            <div class="lbl">Today's Revenue</div>
            <div class="trend text-brand"><i class="bi bi-arrow-up-short"></i>12% vs yesterday</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-soft bill-stat">
            <div class="ico" style="background:color-mix(in srgb, var(--primary) 16%, white); color:color-mix(in srgb, var(--primary) 60%, black);">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div class="val">38</div>
            <div class="lbl">Bills Generated</div>
            <div class="trend text-brand"><i class="bi bi-arrow-up-short"></i>5 since morning</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-soft bill-stat">
            <div class="ico" style="background:color-mix(in srgb, color-mix(in srgb, var(--primary) 80%, #f59e0b) 16%, white); color:color-mix(in srgb, var(--primary) 80%, #f59e0b);">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="val">$48.49</div>
            <div class="lbl">Average Bill</div>
            <div class="trend text-warning"><i class="bi bi-dash"></i>Same as last week</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-soft bill-stat">
            <div class="ico" style="background:#fde7ea; color:#b42318;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="val">3</div>
            <div class="lbl">Pending Bills</div>
            <div class="trend text-danger"><i class="bi bi-exclamation-circle"></i>Needs attention</div>
        </div>
    </div>
</div>

{{-- ── Main: Invoice Builder + Payment Summary ── --}}
<div class="row g-4 mb-4">

    {{-- Invoice Builder --}}
    <div class="col-lg-7">
        <div class="card-soft h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text text-brand"></i>
                    <h5 class="mb-0">Invoice Builder</h5>
                </div>
                <span class="status-badge status-progress">Draft</span>
            </div>
            <div class="card-body">

                {{-- Order meta --}}
                <div class="inv-header">
                    <div class="inv-meta">
                        <div class="inv-meta-item">
                            <span class="k">Invoice</span>
                            <span class="v">#INV-0048</span>
                        </div>
                        <div class="inv-meta-item">
                            <span class="k">Table</span>
                            <span class="v">T-05</span>
                        </div>
                        <div class="inv-meta-item">
                            <span class="k">Covers</span>
                            <span class="v">4 guests</span>
                        </div>
                        <div class="inv-meta-item">
                            <span class="k">Waiter</span>
                            <span class="v">Alex K.</span>
                        </div>
                        <div class="inv-meta-item">
                            <span class="k">Date</span>
                            <span class="v">{{ now()->format('d M Y') }}</span>
                        </div>
                    </div>
                    <button class="btn btn-soft btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </button>
                </div>

                {{-- Items --}}
                <div class="table-responsive">
                    <table class="inv-table" id="inv-items">
                        <thead>
                            <tr>
                                <th style="width:40%">Item</th>
                                <th style="width:18%">Qty</th>
                                <th style="width:16%">Price</th>
                                <th style="width:16%">Total</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-price="12.50">
                                <td>
                                    <div class="fw-600" style="font-weight:600;">Chicken Burger</div>
                                    <div class="text-muted" style="font-size:.78rem;">Grilled · with fries</div>
                                </td>
                                <td>
                                    <div class="qty-ctrl">
                                        <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
                                        <span class="qty-val">2</span>
                                        <button class="qty-btn" onclick="changeQty(this,1)">+</button>
                                    </div>
                                </td>
                                <td>$12.50</td>
                                <td class="row-total fw-semibold">$25.00</td>
                                <td><button class="btn-rm" onclick="removeRow(this)" title="Remove">&times;</button></td>
                            </tr>
                            <tr data-price="4.50">
                                <td>
                                    <div class="fw-600" style="font-weight:600;">Cold Coffee</div>
                                    <div class="text-muted" style="font-size:.78rem;">Iced · large</div>
                                </td>
                                <td>
                                    <div class="qty-ctrl">
                                        <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
                                        <span class="qty-val">2</span>
                                        <button class="qty-btn" onclick="changeQty(this,1)">+</button>
                                    </div>
                                </td>
                                <td>$4.50</td>
                                <td class="row-total fw-semibold">$9.00</td>
                                <td><button class="btn-rm" onclick="removeRow(this)" title="Remove">&times;</button></td>
                            </tr>
                            <tr data-price="6.25">
                                <td>
                                    <div class="fw-600" style="font-weight:600;">Chocolate Cake</div>
                                    <div class="text-muted" style="font-size:.78rem;">Slice · warm</div>
                                </td>
                                <td>
                                    <div class="qty-ctrl">
                                        <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
                                        <span class="qty-val">1</span>
                                        <button class="qty-btn" onclick="changeQty(this,1)">+</button>
                                    </div>
                                </td>
                                <td>$6.25</td>
                                <td class="row-total fw-semibold">$6.25</td>
                                <td><button class="btn-rm" onclick="removeRow(this)" title="Remove">&times;</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Add item --}}
                <div class="add-item-row">
                    <button class="btn btn-soft btn-sm" onclick="addItemRow()">
                        <i class="bi bi-plus-lg me-1"></i>Add Item
                    </button>
                </div>

                <div class="section-sep">Customer Notes</div>
                <textarea class="form-control" rows="2"
                          placeholder="Allergies, special requests, birthday notes…"
                          style="font-size:.88rem; resize:none;"></textarea>
            </div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="col-lg-5">
        <div class="card-soft h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-brand"></i>
                <h5 class="mb-0">Payment Summary</h5>
            </div>
            <div class="card-body d-flex flex-column gap-0">

                {{-- Totals --}}
                <div class="widget-list mb-3">
                    <div class="widget-item">
                        <span class="text-muted">Subtotal</span>
                        <strong id="subtotal-display">$40.25</strong>
                    </div>
                    <div class="widget-item">
                        <span class="text-muted">Discount</span>
                        <div class="disc-wrap">
                            <select class="form-select" id="disc-type" onchange="recalc()">
                                <option value="pct">%</option>
                                <option value="flat">$</option>
                            </select>
                            <input type="number" class="form-control" id="disc-val"
                                   value="0" min="0" step="0.01" oninput="recalc()"
                                   placeholder="0">
                            <strong id="disc-display" class="text-danger ms-1" style="white-space:nowrap;">−$0.00</strong>
                        </div>
                    </div>
                    <div class="widget-item">
                        <span class="text-muted">VAT <small class="text-muted">(10%)</small></span>
                        <strong id="vat-display">$4.03</strong>
                    </div>
                    <div class="widget-item grand-total-row">
                        <span>Grand Total</span>
                        <strong id="grand-display">$44.28</strong>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2">Payment Method</label>
                    <div class="pay-methods">
                        <div class="pay-card">
                            <input type="radio" name="payment_method" id="pm-cash" value="cash" checked>
                            <label for="pm-cash">
                                <i class="bi bi-cash-stack"></i>
                                Cash
                            </label>
                        </div>
                        <div class="pay-card">
                            <input type="radio" name="payment_method" id="pm-card" value="card">
                            <label for="pm-card">
                                <i class="bi bi-credit-card-2-front"></i>
                                Card
                            </label>
                        </div>
                        <div class="pay-card">
                            <input type="radio" name="payment_method" id="pm-online" value="online">
                            <label for="pm-online">
                                <i class="bi bi-phone"></i>
                                Online
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Cash tendered (show when Cash selected) --}}
                <div class="mb-3" id="cash-tendered-wrap">
                    <label class="form-label fw-semibold">Cash Tendered</label>
                    <div class="d-flex gap-2">
                        <input type="number" class="form-control" id="cash-tendered"
                               placeholder="0.00" oninput="calcChange()">
                        <div class="widget-item px-3 flex-shrink-0" style="min-width:110px;">
                            <span style="font-size:.78rem;" class="text-muted">Change</span>
                            <strong id="change-display" class="text-brand" style="font-size:.95rem;">$0.00</strong>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-2 d-flex flex-column gap-2">
                    <button class="btn btn-soft w-100" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print Preview
                    </button>
                    <button class="btn btn-primary w-100" style="padding:.85rem 1rem; font-size:1rem;">
                        <i class="bi bi-receipt me-2"></i>Generate &amp; Settle Bill
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Recent Bills ── --}}
<div class="card-soft table-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-brand"></i>
            <h5 class="mb-0">Recent Bills</h5>
        </div>
        <div class="d-flex gap-2">
            <div style="position:relative;">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.85rem;"></i>
                <input class="form-control form-control-sm" placeholder="Search bills…"
                       style="padding-left:32px; border-radius:10px; width:180px;">
            </div>
            <button class="btn btn-soft btn-sm"><i class="bi bi-download me-1"></i>Export</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Table</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="bill-no">#INV-0047</span></td>
                        <td><span class="fw-semibold">T-03</span></td>
                        <td><span class="text-muted" style="font-size:.85rem;">5 items</span></td>
                        <td><strong>$62.40</strong></td>
                        <td><span class="method-badge method-card"><i class="bi bi-credit-card-2-front"></i>Card</span></td>
                        <td><span class="status-badge status-ready">Paid</span></td>
                        <td><span class="text-muted" style="font-size:.82rem;">12:34 PM</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action" title="Print"><i class="bi bi-printer"></i></button>
                                <button class="tbl-action" title="View"><i class="bi bi-eye"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bill-no">#INV-0046</span></td>
                        <td><span class="fw-semibold">T-08</span></td>
                        <td><span class="text-muted" style="font-size:.85rem;">3 items</span></td>
                        <td><strong>$28.75</strong></td>
                        <td><span class="method-badge method-cash"><i class="bi bi-cash"></i>Cash</span></td>
                        <td><span class="status-badge status-ready">Paid</span></td>
                        <td><span class="text-muted" style="font-size:.82rem;">12:11 PM</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action" title="Print"><i class="bi bi-printer"></i></button>
                                <button class="tbl-action" title="View"><i class="bi bi-eye"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bill-no">#INV-0045</span></td>
                        <td><span class="fw-semibold">T-12</span></td>
                        <td><span class="text-muted" style="font-size:.85rem;">7 items</span></td>
                        <td><strong>$91.20</strong></td>
                        <td><span class="method-badge method-online"><i class="bi bi-phone"></i>Online</span></td>
                        <td><span class="status-badge status-ready">Paid</span></td>
                        <td><span class="text-muted" style="font-size:.82rem;">11:58 AM</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action" title="Print"><i class="bi bi-printer"></i></button>
                                <button class="tbl-action" title="View"><i class="bi bi-eye"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bill-no">#INV-0044</span></td>
                        <td><span class="fw-semibold">T-01</span></td>
                        <td><span class="text-muted" style="font-size:.85rem;">4 items</span></td>
                        <td><strong>$44.50</strong></td>
                        <td><span class="method-badge method-card"><i class="bi bi-credit-card-2-front"></i>Card</span></td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                        <td><span class="text-muted" style="font-size:.82rem;">11:42 AM</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action" title="Print"><i class="bi bi-printer"></i></button>
                                <button class="tbl-action" title="View"><i class="bi bi-eye"></i></button>
                                <button class="tbl-action danger" title="Void"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bill-no">#INV-0043</span></td>
                        <td><span class="fw-semibold">T-06</span></td>
                        <td><span class="text-muted" style="font-size:.85rem;">2 items</span></td>
                        <td><strong>$19.00</strong></td>
                        <td><span class="method-badge method-cash"><i class="bi bi-cash"></i>Cash</span></td>
                        <td><span class="status-badge status-cancelled">Voided</span></td>
                        <td><span class="text-muted" style="font-size:.82rem;">11:20 AM</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action" title="View"><i class="bi bi-eye"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-header border-top-0 border-bottom-0 d-flex align-items-center justify-content-between"
         style="border-top:1px solid var(--line) !important;">
        <span class="text-muted" style="font-size:.83rem;">Showing 5 of 38 bills today</span>
        <div class="d-flex gap-1">
            <button class="btn btn-soft btn-sm"><i class="bi bi-chevron-left"></i></button>
            <button class="btn btn-soft btn-sm px-3">1</button>
            <button class="btn btn-soft btn-sm px-3" style="background:var(--primary);color:#fff;border-color:var(--primary);">2</button>
            <button class="btn btn-soft btn-sm px-3">3</button>
            <button class="btn btn-soft btn-sm"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>

@push('js')
<script>
const VAT_RATE = 0.10;

/* ── Qty +/– ── */
function changeQty(btn, delta) {
    const ctrl = btn.closest('.qty-ctrl');
    const valEl = ctrl.querySelector('.qty-val');
    let q = Math.max(1, parseInt(valEl.textContent) + delta);
    valEl.textContent = q;
    const row = btn.closest('tr');
    const price = parseFloat(row.dataset.price);
    row.querySelector('.row-total').textContent = '$' + (price * q).toFixed(2);
    recalc();
}

/* ── Remove row ── */
function removeRow(btn) {
    btn.closest('tr').remove();
    recalc();
}

/* ── Add item row ── */
function addItemRow() {
    const tbody = document.querySelector('#inv-items tbody');
    const tr = document.createElement('tr');
    tr.dataset.price = '0';
    tr.innerHTML = `
        <td><input class="form-control form-control-sm" placeholder="Item name…" oninput="this.closest('tr').dataset.price=parseFloat(this.closest('tr').querySelector('[data-role=price]').value)||0;recalc();" style="border-radius:8px;"></td>
        <td>
            <div class="qty-ctrl">
                <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
                <span class="qty-val">1</span>
                <button class="qty-btn" onclick="changeQty(this,1)">+</button>
            </div>
        </td>
        <td><input type="number" class="form-control form-control-sm" placeholder="0.00" min="0" step="0.01"
                   data-role="price" oninput="updateRowPrice(this)" style="border-radius:8px;max-width:80px;"></td>
        <td class="row-total fw-semibold">$0.00</td>
        <td><button class="btn-rm" onclick="removeRow(this)">&times;</button></td>`;
    tbody.appendChild(tr);
}

function updateRowPrice(input) {
    const row   = input.closest('tr');
    const price = parseFloat(input.value) || 0;
    row.dataset.price = price;
    const qty   = parseInt(row.querySelector('.qty-val').textContent);
    row.querySelector('.row-total').textContent = '$' + (price * qty).toFixed(2);
    recalc();
}

/* ── Recalculate totals ── */
function recalc() {
    let subtotal = 0;
    document.querySelectorAll('#inv-items tbody tr').forEach(row => {
        const qty   = parseInt(row.querySelector('.qty-val')?.textContent || 1);
        const price = parseFloat(row.dataset.price || 0);
        subtotal   += price * qty;
    });

    const discType = document.getElementById('disc-type').value;
    const discVal  = parseFloat(document.getElementById('disc-val').value) || 0;
    const discount = discType === 'pct'
        ? subtotal * (discVal / 100)
        : Math.min(discVal, subtotal);

    const vat   = (subtotal - discount) * VAT_RATE;
    const grand = subtotal - discount + vat;

    document.getElementById('subtotal-display').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('disc-display').textContent     = '−$' + discount.toFixed(2);
    document.getElementById('vat-display').textContent      = '$' + vat.toFixed(2);
    document.getElementById('grand-display').textContent    = '$' + grand.toFixed(2);

    calcChange();
}

/* ── Cash change ── */
function calcChange() {
    const grand     = parseFloat(document.getElementById('grand-display').textContent.replace('$', '')) || 0;
    const tendered  = parseFloat(document.getElementById('cash-tendered')?.value) || 0;
    const change    = Math.max(0, tendered - grand);
    const el = document.getElementById('change-display');
    if (el) el.textContent = '$' + change.toFixed(2);
}

/* ── Toggle cash tendered row ── */
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const wrap = document.getElementById('cash-tendered-wrap');
        if (wrap) wrap.style.display = this.value === 'cash' ? '' : 'none';
    });
});

document.addEventListener('DOMContentLoaded', () => {
    recalc();
});
</script>
@endpush
@endsection
