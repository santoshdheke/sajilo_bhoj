@extends('admin.theme_one.component.layout')

@section('content')
<style>
.cat-row:hover td { background:#f8fbf9; }
.tbl-action { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
              cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit  { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover  { background:#3730a3;color:#fff; }
.tbl-action.del   { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover   { background:#dc3545;color:#fff; }
.form-panel { position:sticky;top:24px; }
.char-counter { font-size:.75rem;color:var(--muted);text-align:right;margin-top:3px; }
</style>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editName" maxlength="100">
                    <div class="char-counter"><span id="editNameCount">0</span>/100</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="editDesc" rows="3" maxlength="500"
                              placeholder="Short description…"></textarea>
                    <div class="char-counter"><span id="editDescCount">0</span>/500</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="editActive" checked>
                    <label class="form-check-label fw-semibold" for="editActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn">
                    <i class="bi bi-check-lg me-1"></i>Update
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Categories</h2>
    <p>Manage food and beverage categories for the restaurant menu.</p>
</div>

<div class="row g-4">

    {{-- ── Add Form ── --}}
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-folder-plus text-success"></i>
                <h5 class="mb-0">Add Category</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addName" maxlength="100"
                           placeholder="e.g. Main Course">
                    <div class="char-counter"><span id="addNameCount">0</span>/100</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="addDesc" rows="4" maxlength="500"
                              placeholder="Short description of this category…"></textarea>
                    <div class="char-counter"><span id="addDescCount">0</span>/500</div>
                </div>
                <button class="btn btn-primary w-100" id="addSaveBtn">
                    <i class="bi bi-plus-lg me-1"></i>Save Category
                </button>
            </div>
        </div>
    </div>

    {{-- ── Category List ── --}}
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Category List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $categories->count() }})</span></h5>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.8rem;"></i>
                    <input class="form-control form-control-sm" id="catSearch"
                           placeholder="Search…"
                           style="padding-left:30px;border-radius:10px;width:180px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="catTable">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th class="text-center">Items</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="catTbody">
                            @forelse($categories as $cat)
                            <tr class="cat-row" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}"
                                data-desc="{{ $cat->description }}" data-active="{{ $cat->is_active ? '1' : '0' }}">
                                <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $cat->name }}</span>
                                </td>
                                <td class="text-muted" style="font-size:.85rem;max-width:200px;">
                                    {{ Str::limit($cat->description, 60) ?? '—' }}
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $cat->menu_items_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if($cat->is_active)
                                        <span class="status-badge status-ready">Active</span>
                                    @else
                                        <span class="status-badge status-cancelled">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action edit" title="Edit"
                                                onclick="openEdit(this.closest('tr'))">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="tbl-action del" title="Delete"
                                                onclick="deleteCat({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->menu_items_count }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                                    No categories yet. Add your first one!
                                </td>
                            </tr>
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

/* ── Char counters ── */
function wireCounter(inputId, countId) {
    const el = document.getElementById(inputId);
    const ct = document.getElementById(countId);
    const update = () => ct.textContent = el.value.length;
    el.addEventListener('input', update);
    update();
}
wireCounter('addName','addNameCount');
wireCounter('addDesc','addDescCount');
wireCounter('editName','editNameCount');
wireCounter('editDesc','editDescCount');

/* ── Add category ── */
document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const name = document.getElementById('addName').value.trim();
    const desc = document.getElementById('addDesc').value.trim();
    const err  = document.getElementById('addError');
    err.classList.add('d-none');
    if (!name) { document.getElementById('addName').focus(); return; }

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res  = await fetch('/admin/menu-category', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({name, description: desc||null})
    });
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Save Category';

    if (!res.ok) {
        err.textContent = data.errors?.name?.[0] || 'Something went wrong.';
        err.classList.remove('d-none');
        return;
    }

    // Prepend row to table
    document.getElementById('emptyRow')?.remove();
    const tbody = document.getElementById('catTbody');
    const rows  = tbody.querySelectorAll('tr.cat-row');
    const num   = rows.length + 1;
    const tr    = document.createElement('tr');
    tr.className = 'cat-row';
    tr.dataset.id     = data.id;
    tr.dataset.name   = data.name;
    tr.dataset.desc   = data.description || '';
    tr.dataset.active = '1';
    tr.innerHTML = `
        <td class="text-muted" style="font-size:.8rem;">${num}</td>
        <td><span class="fw-semibold">${data.name}</span></td>
        <td class="text-muted" style="font-size:.85rem;max-width:200px;">${(data.description||'').substring(0,60)||'—'}</td>
        <td class="text-center"><span class="fw-bold">0</span></td>
        <td class="text-center"><span class="status-badge status-ready">Active</span></td>
        <td>
            <div class="d-flex gap-1">
                <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                <button class="tbl-action del"  title="Delete" onclick="deleteCat(${data.id},'${data.name.replace(/'/g,"\\'")}',0)"><i class="bi bi-trash"></i></button>
            </div>
        </td>`;
    tbody.insertBefore(tr, tbody.firstChild);

    document.getElementById('addName').value = '';
    document.getElementById('addDesc').value = '';
    ['addNameCount','addDescCount'].forEach(id => document.getElementById(id).textContent = '0');
});

/* ── Edit category ── */
function openEdit(row) {
    document.getElementById('editId').value       = row.dataset.id;
    document.getElementById('editName').value     = row.dataset.name;
    document.getElementById('editDesc').value     = row.dataset.desc;
    document.getElementById('editActive').checked = row.dataset.active === '1';
    ['editNameCount','editDescCount'].forEach(id => {
        const field = document.getElementById(id.replace('Count',''));
        document.getElementById(id).textContent = field.value.length;
    });
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id     = document.getElementById('editId').value;
    const name   = document.getElementById('editName').value.trim();
    const desc   = document.getElementById('editDesc').value.trim();
    const active = document.getElementById('editActive').checked;
    if (!name) { document.getElementById('editName').focus(); return; }

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const res  = await fetch(`/admin/menu-category/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({name, description: desc||null, is_active: active})
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';

    if (res.ok) {
        _editModal.hide();
        // Update row in table
        const row = document.querySelector(`.cat-row[data-id="${id}"]`);
        if (row) {
            row.dataset.name   = name;
            row.dataset.desc   = desc;
            row.dataset.active = active ? '1' : '0';
            row.cells[1].innerHTML = `<span class="fw-semibold">${name}</span>`;
            row.cells[2].textContent = (desc||'—').substring(0,60);
            row.cells[4].innerHTML = active
                ? '<span class="status-badge status-ready">Active</span>'
                : '<span class="status-badge status-cancelled">Inactive</span>';
        }
    }
});

/* ── Delete category ── */
function deleteCat(id, name, count) {
    const msg = count > 0
        ? `Delete "${name}"? This will also delete ${count} menu item(s). This cannot be undone.`
        : `Delete category "${name}"?`;
    if (!confirm(msg)) return;

    fetch(`/admin/menu-category/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
    }).then(r => r.json()).then(() => {
        document.querySelector(`.cat-row[data-id="${id}"]`)?.remove();
        if (!document.querySelector('.cat-row')) {
            document.getElementById('catTbody').innerHTML =
                `<tr id="emptyRow"><td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-folder" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                    No categories yet.</td></tr>`;
        }
    });
}

/* ── Client-side search ── */
document.getElementById('catSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.cat-row').forEach(row => {
        row.style.display = row.dataset.name.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
