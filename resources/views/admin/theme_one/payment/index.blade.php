@extends('admin.theme_one.component.layout')

@section('content')
<style>
.tbl-action { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
              cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit  { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover  { background:#3730a3;color:#fff; }
.stat-card{background:#fff;border-radius:16px;padding:18px;box-shadow:0 8px 24px rgba(0,0,0,.04);}
</style>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Record Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Order <span class="text-danger">*</span></label>
                    <select class="form-select" id="addOrder"><option value="">Select order…</option></select></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Method</label>
                        <select class="form-select" id="addMethod">
                            <option value="cash">Cash</option><option value="card">Card</option>
                            <option value="esewa">eSewa</option><option value="khalti">Khalti</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="addStatus">
                            <option value="completed">Completed</option><option value="pending">Pending</option>
                            <option value="failed">Failed</option><option value="refunded">Refunded</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="addAmount"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Transaction ID</label>
                        <input type="text" class="form-control" id="addTxn" maxlength="150" placeholder="(optional)"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="addSaveBtn"><i class="bi bi-check-lg me-1"></i>Save Payment</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h2>Payments</h2>
        <p>Track payments collected against customer orders.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" onclick="loadOrders()">
        <i class="bi bi-plus-lg me-1"></i>Record Payment
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="text-muted small">Collected Today</div><h4 class="fw-bold mb-0">NRS {{ number_format($stats['collected_today'], 2) }}</h4></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="text-muted small">Completed</div><h4 class="fw-bold mb-0">{{ $stats['completed'] }}</h4></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="text-muted small">Pending</div><h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="text-muted small">Failed</div><h4 class="fw-bold mb-0">{{ $stats['failed'] }}</h4></div></div>
</div>

<div class="card-soft table-card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0">Payment List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $payments->total() }})</span></h5>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:140px;">
                <option value="all">All statuses</option>
                @foreach(['pending','completed','failed','refunded'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="method" class="form-select form-select-sm" onchange="this.form.submit()" style="width:160px;">
                <option value="all">All methods</option>
                @foreach(['cash','card','esewa','khalti','bank_transfer'] as $m)
                    <option value="{{ $m }}" {{ request('method') === $m ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th style="width:36px;">#</th><th>Order</th><th>Method</th><th>Transaction ID</th><th class="text-end">Amount</th><th class="text-center">Status</th><th>Paid At</th><th></th></tr></thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="payment-row" data-id="{{ $payment->id }}" data-status="{{ $payment->status }}">
                        <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration + ($payments->currentPage()-1)*$payments->perPage() }}</td>
                        <td><span class="fw-semibold">{{ $payment->order->order_no ?? '—' }}</span></td>
                        <td>{{ ucfirst(str_replace('_',' ',$payment->payment_method)) }}</td>
                        <td class="text-muted" style="font-size:.85rem;">{{ $payment->transaction_id ?: '—' }}</td>
                        <td class="text-end fw-semibold">NRS {{ number_format($payment->amount, 2) }}</td>
                        <td class="text-center">
                            @php $badge = ['pending'=>'status-pending','completed'=>'status-ready','failed'=>'status-cancelled','refunded'=>'status-progress'][$payment->status] ?? 'status-pending'; @endphp
                            <span class="status-badge {{ $badge }} payment-status-badge">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.85rem;">{{ optional($payment->paid_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>
                            <select class="form-select form-select-sm status-select" style="width:120px;" onchange="updateStatus({{ $payment->id }}, this)">
                                @foreach(['pending','completed','failed','refunded'] as $st)
                                    <option value="{{ $st }}" {{ $payment->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-credit-card" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                        No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
    @endif
</div>

@push('js')
<script>
const CSRF = '{{ csrf_token() }}';
const _addModal = new bootstrap.Modal(document.getElementById('addModal'));
const STATUS_BADGE = {pending:'status-pending', completed:'status-ready', failed:'status-cancelled', refunded:'status-progress'};

async function loadOrders() {
    const sel = document.getElementById('addOrder');
    sel.innerHTML = '<option value="">Loading…</option>';
    const res = await fetch('{{ route('admin.payment.orders_without_payment') }}');
    const orders = await res.json();
    sel.innerHTML = '<option value="">Select order…</option>' + orders.map(o =>
        `<option value="${o.id}" data-amount="${o.grand_total}">${o.order_no} — ${o.customer_name || 'Walk-in'} (NRS ${Number(o.grand_total).toFixed(2)})</option>`
    ).join('');
    sel.onchange = () => {
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.amount) document.getElementById('addAmount').value = opt.dataset.amount;
    };
}

document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const orderId = document.getElementById('addOrder').value;
    const amount  = document.getElementById('addAmount').value;
    const err = document.getElementById('addError');
    err.classList.add('d-none');
    if (!orderId) { document.getElementById('addOrder').focus(); return; }
    if (!amount)  { document.getElementById('addAmount').focus(); return; }

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res = await fetch('{{ route('admin.payment.store') }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            order_id: orderId,
            payment_method: document.getElementById('addMethod').value,
            status: document.getElementById('addStatus').value,
            amount,
            transaction_id: document.getElementById('addTxn').value.trim() || null,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Payment';

    if (!res.ok) { err.textContent = data.errors?.order_id?.[0] || data.errors?.amount?.[0] || 'Something went wrong.'; err.classList.remove('d-none'); return; }

    _addModal.hide();
    location.reload();
});

function updateStatus(id, select) {
    const status = select.value;
    fetch(`/admin/payments/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ status })
    }).then(r => r.json()).then(data => {
        const row = document.querySelector(`.payment-row[data-id="${id}"]`);
        if (row) {
            const badge = row.querySelector('.payment-status-badge');
            badge.className = `status-badge payment-status-badge ${STATUS_BADGE[data.status] || 'status-pending'}`;
            badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
        }
    });
}
</script>
@endpush
@endsection
