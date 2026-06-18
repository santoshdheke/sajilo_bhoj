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
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editName" maxlength="150"></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
                        <input type="text" class="form-control" id="editPhone" maxlength="30"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="editEmail" maxlength="150"></div>
                </div>
                <div class="mt-3"><label class="form-label fw-semibold">Address</label>
                    <textarea class="form-control" id="editAddress" rows="2" maxlength="500"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Suppliers</h2>
    <p>Manage the suppliers your restaurant purchases stock from.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-truck text-success"></i>
                <h5 class="mb-0">Add Supplier</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addName" maxlength="150" placeholder="e.g. Himalayan Fresh Produce"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Phone</label>
                    <input type="text" class="form-control" id="addPhone" maxlength="30" placeholder="(optional)"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="addEmail" maxlength="150" placeholder="(optional)"></div>
                <div class="mb-4"><label class="form-label fw-semibold">Address</label>
                    <textarea class="form-control" id="addAddress" rows="3" maxlength="500" placeholder="(optional)"></textarea></div>
                <button class="btn btn-primary w-100" id="addSaveBtn"><i class="bi bi-plus-lg me-1"></i>Save Supplier</button>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Supplier List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $suppliers->count() }})</span></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th style="width:36px;">#</th><th>Name</th><th>Phone</th><th>Email</th><th class="text-center">Purchase Orders</th><th></th></tr>
                        </thead>
                        <tbody id="supplierTbody">
                            @forelse($suppliers as $supplier)
                            <tr class="supplier-row" data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}"
                                data-phone="{{ $supplier->phone }}" data-email="{{ $supplier->email }}" data-address="{{ $supplier->address }}">
                                <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                                <td><span class="fw-semibold">{{ $supplier->name }}</span></td>
                                <td class="text-muted">{{ $supplier->phone ?: '—' }}</td>
                                <td class="text-muted">{{ $supplier->email ?: '—' }}</td>
                                <td class="text-center"><span class="fw-bold">{{ $supplier->purchase_orders_count }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                                        <button class="tbl-action del" title="Delete" onclick="deleteSupplier({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-truck" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                                No suppliers yet. Add your first one!</td></tr>
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

function rowHtml(s) {
    return `<td class="text-muted" style="font-size:.8rem;"></td>
        <td><span class="fw-semibold">${s.name}</span></td>
        <td class="text-muted">${s.phone || '—'}</td>
        <td class="text-muted">${s.email || '—'}</td>
        <td class="text-center"><span class="fw-bold">${s.purchase_orders_count ?? 0}</span></td>
        <td><div class="d-flex gap-1">
            <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
            <button class="tbl-action del" title="Delete" onclick="deleteSupplier(${s.id},'${(s.name||'').replace(/'/g,"\\'")}')"><i class="bi bi-trash"></i></button>
        </div></td>`;
}

document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const name = document.getElementById('addName').value.trim();
    const err = document.getElementById('addError');
    err.classList.add('d-none');
    if (!name) { document.getElementById('addName').focus(); return; }

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res = await fetch('{{ route('admin.supplier.store') }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            name,
            phone: document.getElementById('addPhone').value.trim() || null,
            email: document.getElementById('addEmail').value.trim() || null,
            address: document.getElementById('addAddress').value.trim() || null,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Save Supplier';

    if (!res.ok) { err.textContent = data.errors?.name?.[0] || data.errors?.email?.[0] || 'Something went wrong.'; err.classList.remove('d-none'); return; }

    document.getElementById('emptyRow')?.remove();
    const tbody = document.getElementById('supplierTbody');
    const tr = document.createElement('tr');
    tr.className = 'supplier-row';
    tr.dataset.id = data.id; tr.dataset.name = data.name; tr.dataset.phone = data.phone || ''; tr.dataset.email = data.email || ''; tr.dataset.address = data.address || '';
    tr.innerHTML = rowHtml(data);
    tbody.insertBefore(tr, tbody.firstChild);
    renumber();

    ['addName','addPhone','addEmail','addAddress'].forEach(id => document.getElementById(id).value = '');
});

function openEdit(row) {
    document.getElementById('editId').value = row.dataset.id;
    document.getElementById('editName').value = row.dataset.name;
    document.getElementById('editPhone').value = row.dataset.phone;
    document.getElementById('editEmail').value = row.dataset.email;
    document.getElementById('editAddress').value = row.dataset.address;
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id = document.getElementById('editId').value;
    const name = document.getElementById('editName').value.trim();
    if (!name) { document.getElementById('editName').focus(); return; }

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const res = await fetch(`/admin/suppliers/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            name,
            phone: document.getElementById('editPhone').value.trim() || null,
            email: document.getElementById('editEmail').value.trim() || null,
            address: document.getElementById('editAddress').value.trim() || null,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';

    if (res.ok) {
        _editModal.hide();
        const row = document.querySelector(`.supplier-row[data-id="${id}"]`);
        if (row) {
            row.dataset.name = data.name; row.dataset.phone = data.phone || ''; row.dataset.email = data.email || ''; row.dataset.address = data.address || '';
            row.cells[1].innerHTML = `<span class="fw-semibold">${data.name}</span>`;
            row.cells[2].textContent = data.phone || '—';
            row.cells[3].textContent = data.email || '—';
        }
    }
});

function deleteSupplier(id, name) {
    if (!confirm(`Delete supplier "${name}"?`)) return;
    fetch(`/admin/suppliers/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF} })
        .then(r => r.json()).then(() => {
            document.querySelector(`.supplier-row[data-id="${id}"]`)?.remove();
            renumber();
            if (!document.querySelector('.supplier-row')) {
                document.getElementById('supplierTbody').innerHTML =
                    `<tr id="emptyRow"><td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-truck" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>No suppliers yet.</td></tr>`;
            }
        });
}

function renumber() {
    document.querySelectorAll('.supplier-row').forEach((row, i) => row.cells[0].textContent = i + 1);
}
</script>
@endpush
@endsection
