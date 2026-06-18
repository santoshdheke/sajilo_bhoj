@extends('admin.theme_one.component.layout')

@section('content')
<style>
.tbl-action { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
              cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.view  { background:#eef0ff;color:#3730a3; }
.tbl-action.view:hover  { background:#3730a3;color:#fff; }
.tbl-action.del   { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover   { background:#dc3545;color:#fff; }
.po-line-row select, .po-line-row input { font-size:.85rem; }
.po-line-row .line-subtotal { font-weight:600; }
</style>

{{-- Add / View modal --}}
<div class="modal fade" id="poModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="poModalTitle"><i class="bi bi-receipt me-2"></i>New Purchase Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div id="poError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>

                <div class="row g-3 mb-3">
                    <div class="col-md-5"><label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select" id="poSupplier">
                            <option value="">Select supplier…</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Invoice No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="poInvoice" maxlength="100"></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="poStatus">
                            @foreach(\App\Http\Controllers\Admin\PurchaseOrderController::STATUSES as $st)
                                <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                            @endforeach
                        </select></div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label fw-semibold mb-0">Line Items <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="poAddLineBtn"><i class="bi bi-plus-lg me-1"></i>Add Line</button>
                </div>
                <div id="poLines"></div>

                <div class="text-end mt-3 pt-2 border-top">
                    <span class="text-muted me-2">Total:</span>
                    <span class="fw-bold fs-5" id="poTotal">NRS 0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="poSaveBtn"><i class="bi bi-check-lg me-1"></i>Save Purchase Order</button>
            </div>
        </div>
    </div>
</div>

{{-- View-only modal --}}
<div class="modal fade" id="poViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Purchase Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="poViewBody"></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<div class="page-title mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h2>Purchase Orders</h2>
        <p>Order stock from suppliers and receive shipments into inventory.</p>
    </div>
    <button class="btn btn-primary" onclick="openPoAdd()" {{ $suppliers->isEmpty() || $inventoryItems->isEmpty() ? 'disabled title=Add suppliers and inventory items first' : '' }}>
        <i class="bi bi-plus-lg me-1"></i>New Purchase Order
    </button>
</div>

<div class="card-soft table-card">
    <div class="card-header"><h5 class="mb-0">Order List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $purchaseOrders->count() }})</span></h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th style="width:36px;">#</th><th>Invoice No.</th><th>Supplier</th><th class="text-center">Items</th><th class="text-end">Total</th><th class="text-center">Status</th><th>Created</th><th></th></tr></thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                    <tr class="po-row" data-id="{{ $po->id }}" data-status="{{ $po->status }}">
                        <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                        <td><span class="fw-semibold">{{ $po->invoice_no }}</span></td>
                        <td class="text-muted">{{ $po->supplier->name ?? '—' }}</td>
                        <td class="text-center">{{ $po->items->count() }}</td>
                        <td class="text-end fw-semibold">NRS {{ number_format($po->total_amount, 2) }}</td>
                        <td class="text-center">
                            @php $badge = ['pending'=>'status-pending','ordered'=>'status-progress','received'=>'status-ready','cancelled'=>'status-cancelled'][$po->status] ?? 'status-pending'; @endphp
                            @if($po->status === 'received' || $po->status === 'cancelled')
                                <span class="status-badge {{ $badge }} po-status-badge">{{ ucfirst($po->status) }}</span>
                            @else
                                <select class="form-select form-select-sm status-select" style="width:120px;" onchange="updatePoStatus({{ $po->id }}, this)">
                                    @foreach(\App\Http\Controllers\Admin\PurchaseOrderController::STATUSES as $st)
                                        <option value="{{ $st }}" {{ $po->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.85rem;">{{ $po->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action view" title="View" onclick='viewPo(@json($po->load(["supplier","items.inventoryItem"])))'><i class="bi bi-eye"></i></button>
                                @if($po->status !== 'received')
                                <button class="tbl-action del" title="Delete" onclick="deletePo({{ $po->id }}, '{{ addslashes($po->invoice_no) }}')"><i class="bi bi-trash"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                        No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
const CSRF = '{{ csrf_token() }}';
const _poModal = new bootstrap.Modal(document.getElementById('poModal'));
const _poViewModal = new bootstrap.Modal(document.getElementById('poViewModal'));
const STATUS_BADGE = {pending:'status-pending', ordered:'status-progress', received:'status-ready', cancelled:'status-cancelled'};

const INVENTORY_ITEMS = @json($inventoryItemsForJs);

let lineSeq = 0;

function lineRowHtml(seq) {
    const options = INVENTORY_ITEMS.map(i => `<option value="${i.id}" data-cost="${i.cost_price}" data-unit="${i.unit}">${i.name} (${i.unit})</option>`).join('');
    return `
    <div class="row g-2 align-items-center po-line-row mb-2" data-seq="${seq}">
        <div class="col-md-5">
            <select class="form-select form-select-sm line-item">
                <option value="">Select item…</option>
                ${options}
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm line-qty" placeholder="Qty">
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" min="0" class="form-control form-control-sm line-price" placeholder="Unit price">
        </div>
        <div class="col-md-2 text-end line-subtotal">NRS 0.00</div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLine(this)"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>`;
}

function addLine() {
    lineSeq++;
    const wrap = document.createElement('div');
    wrap.innerHTML = lineRowHtml(lineSeq);
    const row = wrap.firstElementChild;
    document.getElementById('poLines').appendChild(row);

    const itemSel = row.querySelector('.line-item');
    const qtyInp  = row.querySelector('.line-qty');
    const priceInp = row.querySelector('.line-price');

    itemSel.addEventListener('change', () => {
        const opt = itemSel.options[itemSel.selectedIndex];
        if (opt && opt.dataset.cost) priceInp.value = opt.dataset.cost;
        recalcLine(row);
    });
    qtyInp.addEventListener('input', () => recalcLine(row));
    priceInp.addEventListener('input', () => recalcLine(row));
}

function removeLine(btn) {
    btn.closest('.po-line-row').remove();
    recalcTotal();
}

function recalcLine(row) {
    const qty = parseFloat(row.querySelector('.line-qty').value) || 0;
    const price = parseFloat(row.querySelector('.line-price').value) || 0;
    row.querySelector('.line-subtotal').textContent = 'NRS ' + (qty * price).toFixed(2);
    recalcTotal();
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('#poLines .po-line-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.line-qty').value) || 0;
        const price = parseFloat(row.querySelector('.line-price').value) || 0;
        total += qty * price;
    });
    document.getElementById('poTotal').textContent = 'NRS ' + total.toFixed(2);
}

document.getElementById('poAddLineBtn').addEventListener('click', addLine);

function openPoAdd() {
    document.getElementById('poModalTitle').innerHTML = '<i class="bi bi-receipt me-2"></i>New Purchase Order';
    document.getElementById('poError').classList.add('d-none');
    document.getElementById('poSupplier').selectedIndex = 0;
    document.getElementById('poInvoice').value = '';
    document.getElementById('poStatus').selectedIndex = 0;
    document.getElementById('poLines').innerHTML = '';
    document.getElementById('poTotal').textContent = 'NRS 0.00';
    lineSeq = 0;
    addLine();
    _poModal.show();
}

document.getElementById('poSaveBtn').addEventListener('click', async () => {
    const err = document.getElementById('poError');
    err.classList.add('d-none');

    const supplierId = document.getElementById('poSupplier').value;
    const invoiceNo  = document.getElementById('poInvoice').value.trim();
    if (!supplierId) { err.textContent = 'Please select a supplier.'; err.classList.remove('d-none'); return; }
    if (!invoiceNo)  { err.textContent = 'Please enter an invoice number.'; err.classList.remove('d-none'); return; }

    const items = [];
    let valid = true;
    document.querySelectorAll('#poLines .po-line-row').forEach(row => {
        const itemId = row.querySelector('.line-item').value;
        const qty = row.querySelector('.line-qty').value;
        const price = row.querySelector('.line-price').value;
        if (!itemId || !qty || price === '') { valid = false; return; }
        items.push({ inventory_item_id: itemId, quantity: qty, unit_price: price });
    });
    if (!valid || items.length === 0) { err.textContent = 'Please complete all line items (item, quantity, unit price).'; err.classList.remove('d-none'); return; }

    const ids = items.map(i => i.inventory_item_id);
    if (new Set(ids).size !== ids.length) { err.textContent = 'Each inventory item can only appear once per purchase order.'; err.classList.remove('d-none'); return; }

    const btn = document.getElementById('poSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res = await fetch('{{ route('admin.purchase_order.store') }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            supplier_id: supplierId,
            invoice_no: invoiceNo,
            status: document.getElementById('poStatus').value,
            items,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Purchase Order';

    if (!res.ok) {
        const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
        err.textContent = data.message || firstError || 'Something went wrong.';
        err.classList.remove('d-none');
        return;
    }

    _poModal.hide();
    location.reload();
});

function viewPo(po) {
    const badge = STATUS_BADGE[po.status] || 'status-pending';
    const rows = (po.items || []).map(it => `
        <tr>
            <td>${it.inventory_item?.name ?? '—'}</td>
            <td class="text-muted">${it.inventory_item?.unit ?? ''}</td>
            <td class="text-end">${Number(it.quantity).toFixed(2)}</td>
            <td class="text-end">NRS ${Number(it.unit_price).toFixed(2)}</td>
            <td class="text-end fw-semibold">NRS ${Number(it.subtotal).toFixed(2)}</td>
        </tr>`).join('');

    document.getElementById('poViewBody').innerHTML = `
        <div class="row g-3 mb-3">
            <div class="col-md-4"><div class="text-muted small">Invoice No.</div><div class="fw-semibold">${po.invoice_no}</div></div>
            <div class="col-md-4"><div class="text-muted small">Supplier</div><div class="fw-semibold">${po.supplier?.name ?? '—'}</div></div>
            <div class="col-md-4"><div class="text-muted small">Status</div><span class="status-badge ${badge}">${po.status.charAt(0).toUpperCase()+po.status.slice(1)}</span></div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead><tr><th>Item</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>${rows || '<tr><td colspan="5" class="text-center text-muted py-3">No line items.</td></tr>'}</tbody>
            </table>
        </div>
        <div class="text-end pt-2 border-top">
            <span class="text-muted me-2">Total:</span>
            <span class="fw-bold fs-5">NRS ${Number(po.total_amount).toFixed(2)}</span>
        </div>`;
    _poViewModal.show();
}

function updatePoStatus(id, select) {
    const status = select.value;
    if (status === 'received' && !confirm('Mark as received? This will add the ordered quantities into inventory stock.')) {
        select.value = select.dataset.prev || 'pending';
        return;
    }
    fetch(`/admin/purchase-orders/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ status })
    }).then(r => r.json()).then(() => location.reload());
}

document.querySelectorAll('.status-select').forEach(s => s.dataset.prev = s.value);

function deletePo(id, invoice) {
    if (!confirm(`Delete purchase order "${invoice}"?`)) return;
    fetch(`/admin/purchase-orders/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF} })
        .then(async r => {
            const data = await r.json();
            if (!r.ok) { alert(data.message || 'Could not delete this purchase order.'); return; }
            document.querySelector(`.po-row[data-id="${id}"]`)?.remove();
        });
}
</script>
@endpush
@endsection
