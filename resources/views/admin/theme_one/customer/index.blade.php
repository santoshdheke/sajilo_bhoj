@extends('admin.theme_one.component.layout')

@section('content')
<style>
.tbl-action { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
              cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit  { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover  { background:#3730a3;color:#fff; }
.tbl-action.del   { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover   { background:#dc3545;color:#fff; }
</style>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-lines-fill me-2"></i>Edit Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editName" maxlength="150"></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
                        <input type="text" class="form-control" id="editPhone" maxlength="30"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Loyalty Points</label>
                        <input type="number" min="0" class="form-control" id="editPoints"></div>
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
    <h2>Customers</h2>
    <p>Browse registered customers, their orders, and loyalty points.</p>
    @include('partials.plan-usage', ['key' => 'customers'])
</div>

<div class="card-soft table-card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0">Customer List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $customers->total() }})</span></h5>
        <form method="GET" class="d-flex" style="position:relative;">
            <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.8rem;"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                   placeholder="Search name, email, phone…" style="padding-left:30px;border-radius:10px;width:240px;">
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th style="width:36px;">#</th><th>Name</th><th>Email</th><th>Phone</th><th class="text-center">Orders</th><th class="text-center">Loyalty Points</th><th></th></tr></thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr class="customer-row" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                        data-phone="{{ $customer->phone }}" data-address="{{ $customer->address }}" data-points="{{ $customer->loyalty_points }}">
                        <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration + ($customers->currentPage()-1)*$customers->perPage() }}</td>
                        <td><span class="fw-semibold">{{ $customer->name }}</span></td>
                        <td class="text-muted">{{ $customer->email ?: '—' }}</td>
                        <td class="text-muted">{{ $customer->phone ?: '—' }}</td>
                        <td class="text-center">{{ $customer->orders_count }}</td>
                        <td class="text-center"><span class="status-badge status-ready">{{ $customer->loyalty_points }} pts</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                                <button class="tbl-action del" title="Delete" onclick="deleteCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                        No customers have registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>

@push('js')
<script>
const CSRF = '{{ csrf_token() }}';
const _editModal = new bootstrap.Modal(document.getElementById('editModal'));

function openEdit(row) {
    document.getElementById('editId').value = row.dataset.id;
    document.getElementById('editName').value = row.dataset.name;
    document.getElementById('editPhone').value = row.dataset.phone;
    document.getElementById('editAddress').value = row.dataset.address;
    document.getElementById('editPoints').value = row.dataset.points;
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id = document.getElementById('editId').value;
    const name = document.getElementById('editName').value.trim();
    if (!name) { document.getElementById('editName').focus(); return; }

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const res = await fetch(`/admin/customers/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            name,
            phone: document.getElementById('editPhone').value.trim() || null,
            address: document.getElementById('editAddress').value.trim() || null,
            loyalty_points: document.getElementById('editPoints').value || 0,
        })
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';

    if (res.ok) {
        _editModal.hide();
        const row = document.querySelector(`.customer-row[data-id="${id}"]`);
        if (row) {
            row.dataset.name = data.name; row.dataset.phone = data.phone || ''; row.dataset.address = data.address || ''; row.dataset.points = data.loyalty_points;
            row.cells[1].innerHTML = `<span class="fw-semibold">${data.name}</span>`;
            row.cells[3].textContent = data.phone || '—';
            row.cells[5].innerHTML = `<span class="status-badge status-ready">${data.loyalty_points} pts</span>`;
        }
    }
});

function deleteCustomer(id, name) {
    if (!confirm(`Delete customer "${name}"? Their order history will remain but be unlinked.`)) return;
    fetch(`/admin/customers/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF} })
        .then(r => r.json()).then(() => document.querySelector(`.customer-row[data-id="${id}"]`)?.closest('tr')?.remove());
}
</script>
@endpush
@endsection
