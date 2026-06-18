@extends('admin.theme_one.component.layout')

@section('content')
<style>
.tbl-action { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
              cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit  { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover  { background:#3730a3;color:#fff; }
.tbl-action.del   { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover   { background:#dc3545;color:#fff; }
.form-panel { position:sticky;top:24px; }
</style>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Coupon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3"><label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase" id="editCode" maxlength="40"></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Type</label>
                        <select class="form-select" id="editType"><option value="percent">Percent (%)</option><option value="fixed">Fixed amount</option></select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Value</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="editValue"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Valid From</label>
                        <input type="datetime-local" class="form-control" id="editFrom"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Valid To</label>
                        <input type="datetime-local" class="form-control" id="editTo"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Coupons</h2>
    <p>Create discount codes customers can apply at checkout.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-ticket-perforated text-success"></i>
                <h5 class="mb-0">Add Coupon</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase" id="addCode" maxlength="40" placeholder="e.g. WELCOME10"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Type</label>
                    <select class="form-select" id="addType"><option value="percent">Percent (%)</option><option value="fixed">Fixed amount</option></select></div>
                <div class="mb-3"><label class="form-label fw-semibold">Value <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="addValue" placeholder="e.g. 10"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Valid From</label>
                    <input type="datetime-local" class="form-control" id="addFrom"></div>
                <div class="mb-4"><label class="form-label fw-semibold">Valid To</label>
                    <input type="datetime-local" class="form-control" id="addTo"></div>
                <button class="btn btn-primary w-100" id="addSaveBtn"><i class="bi bi-plus-lg me-1"></i>Save Coupon</button>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Coupon List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $coupons->count() }})</span></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th style="width:36px;">#</th><th>Code</th><th>Discount</th><th>Valid From</th><th>Valid To</th><th class="text-center">Status</th><th></th></tr></thead>
                        <tbody id="couponTbody">
                            @forelse($coupons as $coupon)
                            <tr class="coupon-row" data-id="{{ $coupon->id }}" data-code="{{ $coupon->code }}"
                                data-type="{{ $coupon->discount_type }}" data-value="{{ $coupon->discount_value }}"
                                data-from="{{ optional($coupon->valid_from)->format('Y-m-d\TH:i') }}"
                                data-to="{{ optional($coupon->valid_to)->format('Y-m-d\TH:i') }}">
                                <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                                <td><span class="fw-semibold">{{ $coupon->code }}</span></td>
                                <td>{{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : 'NRS '.number_format($coupon->discount_value, 2) }}</td>
                                <td class="text-muted" style="font-size:.85rem;">{{ optional($coupon->valid_from)->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="text-muted" style="font-size:.85rem;">{{ optional($coupon->valid_to)->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="text-center">
                                    @if($coupon->isValidNow())
                                        <span class="status-badge status-ready">Active</span>
                                    @else
                                        <span class="status-badge status-cancelled">Expired</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                                        <button class="tbl-action del" title="Delete" onclick="deleteCoupon({{ $coupon->id }}, '{{ addslashes($coupon->code) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-ticket-perforated" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                                No coupons yet. Add your first one!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
const CSRF = '{{ csrf_token() }}';
const _editModal = new bootstrap.Modal(document.getElementById('editModal'));

function fmtDiscount(type, value) {
    return type === 'percent' ? `${value}%` : `NRS ${Number(value).toFixed(2)}`;
}
function fmtDate(v) { return v ? v.replace('T', ' ') : '—'; }

document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const code = document.getElementById('addCode').value.trim().toUpperCase();
    const err = document.getElementById('addError');
    err.classList.add('d-none');
    if (!code) { document.getElementById('addCode').focus(); return; }

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res = await fetch('{{ route('admin.coupon.store') }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            code,
            discount_type: document.getElementById('addType').value,
            discount_value: document.getElementById('addValue').value,
            valid_from: document.getElementById('addFrom').value || null,
            valid_to: document.getElementById('addTo').value || null,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Save Coupon';

    if (!res.ok) { err.textContent = data.errors?.code?.[0] || data.errors?.discount_value?.[0] || data.errors?.valid_to?.[0] || 'Something went wrong.'; err.classList.remove('d-none'); return; }

    document.getElementById('emptyRow')?.remove();
    const tbody = document.getElementById('couponTbody');
    const tr = document.createElement('tr');
    tr.className = 'coupon-row';
    tr.dataset.id = data.id; tr.dataset.code = data.code; tr.dataset.type = data.discount_type;
    tr.dataset.value = data.discount_value; tr.dataset.from = data.valid_from || ''; tr.dataset.to = data.valid_to || '';
    tr.innerHTML = `
        <td class="text-muted" style="font-size:.8rem;"></td>
        <td><span class="fw-semibold">${data.code}</span></td>
        <td>${fmtDiscount(data.discount_type, data.discount_value)}</td>
        <td class="text-muted" style="font-size:.85rem;">${fmtDate(data.valid_from)}</td>
        <td class="text-muted" style="font-size:.85rem;">${fmtDate(data.valid_to)}</td>
        <td class="text-center"><span class="status-badge status-ready">Active</span></td>
        <td><div class="d-flex gap-1">
            <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
            <button class="tbl-action del" title="Delete" onclick="deleteCoupon(${data.id},'${(data.code||'').replace(/'/g,"\\'")}')"><i class="bi bi-trash"></i></button>
        </div></td>`;
    tbody.insertBefore(tr, tbody.firstChild);
    renumber();

    ['addCode','addValue','addFrom','addTo'].forEach(id => document.getElementById(id).value = '');
});

function openEdit(row) {
    document.getElementById('editId').value = row.dataset.id;
    document.getElementById('editCode').value = row.dataset.code;
    document.getElementById('editType').value = row.dataset.type;
    document.getElementById('editValue').value = row.dataset.value;
    document.getElementById('editFrom').value = row.dataset.from;
    document.getElementById('editTo').value = row.dataset.to;
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id = document.getElementById('editId').value;
    const code = document.getElementById('editCode').value.trim().toUpperCase();
    if (!code) { document.getElementById('editCode').focus(); return; }

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const res = await fetch(`/admin/coupons/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            code,
            discount_type: document.getElementById('editType').value,
            discount_value: document.getElementById('editValue').value,
            valid_from: document.getElementById('editFrom').value || null,
            valid_to: document.getElementById('editTo').value || null,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';

    if (res.ok) {
        _editModal.hide();
        const row = document.querySelector(`.coupon-row[data-id="${id}"]`);
        if (row) {
            row.dataset.code = data.code; row.dataset.type = data.discount_type; row.dataset.value = data.discount_value;
            row.dataset.from = data.valid_from || ''; row.dataset.to = data.valid_to || '';
            row.cells[1].innerHTML = `<span class="fw-semibold">${data.code}</span>`;
            row.cells[2].textContent = fmtDiscount(data.discount_type, data.discount_value);
            row.cells[3].textContent = fmtDate(data.valid_from);
            row.cells[4].textContent = fmtDate(data.valid_to);
        }
    }
});

function deleteCoupon(id, code) {
    if (!confirm(`Delete coupon "${code}"?`)) return;
    fetch(`/admin/coupons/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF} })
        .then(r => r.json()).then(() => {
            document.querySelector(`.coupon-row[data-id="${id}"]`)?.remove();
            renumber();
            if (!document.querySelector('.coupon-row')) {
                document.getElementById('couponTbody').innerHTML =
                    `<tr id="emptyRow"><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-ticket-perforated" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>No coupons yet.</td></tr>`;
            }
        });
}

function renumber() {
    document.querySelectorAll('.coupon-row').forEach((row, i) => row.cells[0].textContent = i + 1);
}
</script>
@endpush
@endsection
