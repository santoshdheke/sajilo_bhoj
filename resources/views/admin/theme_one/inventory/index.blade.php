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

{{-- Category modal --}}
<div class="modal fade" id="catModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-tags me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="catEditId">
                <label class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control" id="catEditName" maxlength="100">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="catEditSaveBtn">Update</button>
            </div>
        </div>
    </div>
</div>

{{-- Item modal --}}
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="itemModalTitle"><i class="bi bi-box-seam me-2"></i>Add Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div id="itemError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <input type="hidden" id="itemId">
                <div class="mb-3"><label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="itemCategory">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select></div>
                <div class="row g-3">
                    <div class="col-md-7"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="itemName" maxlength="150"></div>
                    <div class="col-md-5"><label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="itemUnit" maxlength="30" placeholder="kg, ltr, pcs…"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Quantity</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="itemQty"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Reorder Level</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="itemReorder"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Cost Price</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="itemCost"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="itemSaveBtn">Save Item</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Inventory</h2>
    <p>Track stock categories, item levels, and low-stock alerts.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft" style="position:sticky;top:24px;">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-tags text-success"></i><h5 class="mb-0">Categories</h5>
            </div>
            <div class="card-body">
                <div id="catAddError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="catAddName" maxlength="100" placeholder="New category name…">
                    <button class="btn btn-primary" id="catAddBtn"><i class="bi bi-plus-lg"></i></button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="catTable">
                        <tbody>
                            @forelse($categories as $cat)
                            <tr class="cat-row" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}">
                                <td><span class="fw-semibold">{{ $cat->name }}</span></td>
                                <td class="text-center"><span class="text-muted" style="font-size:.8rem;">{{ $cat->items_count }} items</span></td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="tbl-action edit" title="Edit" onclick="openCatEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                                        <button class="tbl-action del" title="Delete" onclick="deleteCat({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->items_count }})"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="catEmptyRow"><td colspan="3" class="text-center py-4 text-muted">No categories yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Stock Items <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $items->count() }})</span></h5>
                <button class="btn btn-primary btn-sm" onclick="openItemAdd()" {{ $categories->isEmpty() ? 'disabled title=Add a category first' : '' }}>
                    <i class="bi bi-plus-lg me-1"></i>Add Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th style="width:36px;">#</th><th>Item</th><th>Category</th><th>Unit</th><th class="text-end">Quantity</th><th class="text-end">Reorder Level</th><th class="text-end">Cost Price</th><th class="text-center">Status</th><th></th></tr></thead>
                        <tbody id="itemTbody">
                            @forelse($items as $item)
                            <tr class="item-row" data-id="{{ $item->id }}" data-category="{{ $item->category_id }}" data-name="{{ $item->name }}"
                                data-unit="{{ $item->unit }}" data-qty="{{ $item->quantity }}" data-reorder="{{ $item->reorder_level }}" data-cost="{{ $item->cost_price }}">
                                <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                                <td><span class="fw-semibold">{{ $item->name }}</span></td>
                                <td class="text-muted">{{ $item->category->name }}</td>
                                <td class="text-muted">{{ $item->unit }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format($item->reorder_level, 2), '0'), '.') }}</td>
                                <td class="text-end">NRS {{ number_format($item->cost_price, 2) }}</td>
                                <td class="text-center">
                                    @if($item->is_low_stock)
                                        <span class="status-badge status-cancelled">Low Stock</span>
                                    @else
                                        <span class="status-badge status-ready">OK</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action edit" title="Edit" onclick="openItemEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                                        <button class="tbl-action del" title="Delete" onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="itemEmptyRow"><td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                                No stock items yet.</td></tr>
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
const _catModal  = new bootstrap.Modal(document.getElementById('catModal'));
const _itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
const fmt = n => { const s = Number(n).toFixed(2); return s.replace(/\.?0+$/, '') || '0'; };

/* ── Categories ── */
document.getElementById('catAddBtn').addEventListener('click', async () => {
    const name = document.getElementById('catAddName').value.trim();
    const err = document.getElementById('catAddError');
    err.classList.add('d-none');
    if (!name) return;

    const res = await fetch('{{ route('admin.inventory_category.store') }}', {
        method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body: JSON.stringify({name})
    });
    const data = await res.json();
    if (!res.ok) { err.textContent = data.errors?.name?.[0] || 'Something went wrong.'; err.classList.remove('d-none'); return; }

    document.getElementById('catEmptyRow')?.remove();
    const tbody = document.querySelector('#catTable tbody');
    const tr = document.createElement('tr');
    tr.className = 'cat-row'; tr.dataset.id = data.id; tr.dataset.name = data.name;
    tr.innerHTML = `<td><span class="fw-semibold">${data.name}</span></td>
        <td class="text-center"><span class="text-muted" style="font-size:.8rem;">0 items</span></td>
        <td class="text-end"><div class="d-flex gap-1 justify-content-end">
            <button class="tbl-action edit" title="Edit" onclick="openCatEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
            <button class="tbl-action del" title="Delete" onclick="deleteCat(${data.id},'${(data.name||'').replace(/'/g,"\\'")}',0)"><i class="bi bi-trash"></i></button>
        </div></td>`;
    tbody.appendChild(tr);
    document.getElementById('catAddName').value = '';

    const sel = document.getElementById('itemCategory');
    const opt = document.createElement('option');
    opt.value = data.id; opt.textContent = data.name;
    sel.appendChild(opt);
    document.querySelector('button[onclick="openItemAdd()"]')?.removeAttribute('disabled');
});

function openCatEdit(row) {
    document.getElementById('catEditId').value = row.dataset.id;
    document.getElementById('catEditName').value = row.dataset.name;
    _catModal.show();
}

document.getElementById('catEditSaveBtn').addEventListener('click', async () => {
    const id = document.getElementById('catEditId').value;
    const name = document.getElementById('catEditName').value.trim();
    if (!name) return;

    const res = await fetch(`/admin/inventory-categories/${id}`, {
        method: 'PUT', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body: JSON.stringify({name})
    });
    if (res.ok) {
        const data = await res.json();
        _catModal.hide();
        const row = document.querySelector(`.cat-row[data-id="${id}"]`);
        row.dataset.name = data.name;
        row.cells[0].innerHTML = `<span class="fw-semibold">${data.name}</span>`;
        document.querySelector(`#itemCategory option[value="${id}"]`).textContent = data.name;
    }
});

function deleteCat(id, name, count) {
    const msg = count > 0 ? `Delete "${name}"? This will also delete ${count} item(s).` : `Delete category "${name}"?`;
    if (!confirm(msg)) return;
    fetch(`/admin/inventory-categories/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF} })
        .then(r => r.json()).then(() => location.reload());
}

/* ── Items ── */
function openItemAdd() {
    document.getElementById('itemModalTitle').innerHTML = '<i class="bi bi-box-seam me-2"></i>Add Item';
    document.getElementById('itemError').classList.add('d-none');
    document.getElementById('itemId').value = '';
    document.getElementById('itemCategory').selectedIndex = 0;
    ['itemName','itemUnit','itemQty','itemReorder','itemCost'].forEach(id => document.getElementById(id).value = '');
    _itemModal.show();
}

function openItemEdit(row) {
    document.getElementById('itemModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Item';
    document.getElementById('itemError').classList.add('d-none');
    document.getElementById('itemId').value = row.dataset.id;
    document.getElementById('itemCategory').value = row.dataset.category;
    document.getElementById('itemName').value = row.dataset.name;
    document.getElementById('itemUnit').value = row.dataset.unit;
    document.getElementById('itemQty').value = row.dataset.qty;
    document.getElementById('itemReorder').value = row.dataset.reorder;
    document.getElementById('itemCost').value = row.dataset.cost;
    _itemModal.show();
}

document.getElementById('itemSaveBtn').addEventListener('click', async () => {
    const id = document.getElementById('itemId').value;
    const err = document.getElementById('itemError');
    err.classList.add('d-none');

    const payload = {
        category_id: document.getElementById('itemCategory').value,
        name: document.getElementById('itemName').value.trim(),
        unit: document.getElementById('itemUnit').value.trim(),
        quantity: document.getElementById('itemQty').value || 0,
        reorder_level: document.getElementById('itemReorder').value || 0,
        cost_price: document.getElementById('itemCost').value || 0,
    };
    if (!payload.name || !payload.unit) { err.textContent = 'Name and unit are required.'; err.classList.remove('d-none'); return; }

    const btn = document.getElementById('itemSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const url = id ? `/admin/inventory-items/${id}` : '{{ route('admin.inventory_item.store') }}';
    const res = await fetch(url, {
        method: id ? 'PUT' : 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    btn.disabled = false; btn.innerHTML = 'Save Item';

    if (!res.ok) { err.textContent = data.errors?.name?.[0] || data.errors?.unit?.[0] || 'Something went wrong.'; err.classList.remove('d-none'); return; }

    _itemModal.hide();
    location.reload();
});

function deleteItem(id, name) {
    if (!confirm(`Delete item "${name}"?`)) return;
    fetch(`/admin/inventory-items/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF} })
        .then(r => r.json()).then(() => document.querySelector(`.item-row[data-id="${id}"]`)?.remove());
}
</script>
@endpush
@endsection
