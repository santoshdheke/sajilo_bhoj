@extends('admin.theme_one.component.layout')

@section('content')
<style>
.nav-row:hover td { background:#f8fbf9; }
.tbl-action { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
              cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover { background:#3730a3;color:#fff; }
.tbl-action.del  { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover  { background:#dc3545;color:#fff; }
.tbl-action.move { background:#f1f4f3;color:#495057; }
.tbl-action.move:hover { background:#495057;color:#fff; }
.form-panel { position:sticky;top:24px; }
</style>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Menu Link</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editLabel" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editUrl" maxlength="255" placeholder="/about or https://...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Icon (bootstrap-icons class)</label>
                    <input type="text" class="form-control" id="editIcon" maxlength="60" placeholder="bi-house">
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
    <h2>Navigation Menu</h2>
    <p>Manage the links shown in the storefront header, mobile menu, and footer quick links.</p>
</div>

<div class="row g-4">
    {{-- ── Add Form ── --}}
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-link-45deg text-success"></i>
                <h5 class="mb-0">Add Menu Link</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addLabel" maxlength="100" placeholder="e.g. Gallery">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addUrl" maxlength="255" placeholder="/about or https://...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Icon (bootstrap-icons class)</label>
                    <input type="text" class="form-control" id="addIcon" maxlength="60" placeholder="bi-house">
                </div>
                <button class="btn btn-primary w-100" id="addSaveBtn">
                    <i class="bi bi-plus-lg me-1"></i>Save Link
                </button>
            </div>
        </div>
    </div>

    {{-- ── Link List ── --}}
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Menu Links <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $navItems->count() }})</span></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="navTable">
                        <thead>
                            <tr>
                                <th style="width:80px;">Order</th>
                                <th>Label</th>
                                <th>URL</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="navTbody">
                            @forelse($navItems as $item)
                            <tr class="nav-row" data-id="{{ $item->id }}" data-label="{{ $item->label }}"
                                data-url="{{ $item->url }}" data-icon="{{ $item->icon }}"
                                data-active="{{ $item->is_active ? '1' : '0' }}">
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action move" title="Move up" onclick="moveItem({{ $item->id }}, 'up')"><i class="bi bi-arrow-up"></i></button>
                                        <button class="tbl-action move" title="Move down" onclick="moveItem({{ $item->id }}, 'down')"><i class="bi bi-arrow-down"></i></button>
                                    </div>
                                </td>
                                <td>
                                    @if($item->icon)<i class="bi {{ $item->icon }} me-1"></i>@endif
                                    <span class="fw-semibold">{{ $item->label }}</span>
                                </td>
                                <td class="text-muted" style="font-size:.85rem;">{{ $item->url }}</td>
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="status-badge status-ready">Active</span>
                                    @else
                                        <span class="status-badge status-cancelled">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="tbl-action del" title="Delete" onclick="deleteNavItem({{ $item->id }}, '{{ addslashes($item->label) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-link-45deg" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                                    No menu links yet. Add your first one!
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

function statusBadge(active) {
    return active
        ? '<span class="status-badge status-ready">Active</span>'
        : '<span class="status-badge status-cancelled">Inactive</span>';
}

/* ── Add link ── */
document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const label       = document.getElementById('addLabel').value.trim();
    const url          = document.getElementById('addUrl').value.trim();
    const icon         = document.getElementById('addIcon').value.trim();
    const err          = document.getElementById('addError');
    err.classList.add('d-none');
    if (!label || !url) { document.getElementById(!label ? 'addLabel' : 'addUrl').focus(); return; }

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res  = await fetch('{{ route('admin.settings.navigation.store') }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({label, url, icon: icon || null, is_active: true})
    });
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Save Link';

    if (!res.ok) {
        err.textContent = data.errors?.label?.[0] || data.errors?.url?.[0] || 'Something went wrong.';
        err.classList.remove('d-none');
        return;
    }

    document.getElementById('emptyRow')?.remove();
    const tbody = document.getElementById('navTbody');
    const tr = document.createElement('tr');
    tr.className = 'nav-row';
    tr.dataset.id = data.id;
    tr.dataset.label = data.label;
    tr.dataset.url = data.url;
    tr.dataset.icon = data.icon || '';
    tr.dataset.active = '1';
    tr.innerHTML = `
        <td><div class="d-flex gap-1">
            <button class="tbl-action move" title="Move up" onclick="moveItem(${data.id},'up')"><i class="bi bi-arrow-up"></i></button>
            <button class="tbl-action move" title="Move down" onclick="moveItem(${data.id},'down')"><i class="bi bi-arrow-down"></i></button>
        </div></td>
        <td>${data.icon ? `<i class="bi ${data.icon} me-1"></i>` : ''}<span class="fw-semibold">${data.label}</span></td>
        <td class="text-muted" style="font-size:.85rem;">${data.url}</td>
        <td class="text-center">${statusBadge(true)}</td>
        <td><div class="d-flex gap-1">
            <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
            <button class="tbl-action del" title="Delete" onclick="deleteNavItem(${data.id},'${data.label.replace(/'/g,"\\'")}')"><i class="bi bi-trash"></i></button>
        </div></td>`;
    tbody.appendChild(tr);

    document.getElementById('addLabel').value = '';
    document.getElementById('addUrl').value = '';
    document.getElementById('addIcon').value = '';
});

/* ── Edit link ── */
function openEdit(row) {
    document.getElementById('editId').value = row.dataset.id;
    document.getElementById('editLabel').value = row.dataset.label;
    document.getElementById('editUrl').value = row.dataset.url;
    document.getElementById('editIcon').value = row.dataset.icon;
    document.getElementById('editActive').checked = row.dataset.active === '1';
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id          = document.getElementById('editId').value;
    const label       = document.getElementById('editLabel').value.trim();
    const url          = document.getElementById('editUrl').value.trim();
    const icon         = document.getElementById('editIcon').value.trim();
    const active       = document.getElementById('editActive').checked;
    if (!label || !url) return;

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const res = await fetch(`/admin/settings/navigation/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({label, url, icon: icon || null, is_active: active})
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';

    if (res.ok) {
        _editModal.hide();
        const row = document.querySelector(`.nav-row[data-id="${id}"]`);
        if (row) {
            row.dataset.label = label;
            row.dataset.url = url;
            row.dataset.icon = icon;
            row.dataset.active = active ? '1' : '0';
            row.cells[1].innerHTML = `${icon ? `<i class="bi ${icon} me-1"></i>` : ''}<span class="fw-semibold">${label}</span>`;
            row.cells[2].textContent = url;
            row.cells[3].innerHTML = statusBadge(active);
        }
    }
});

/* ── Delete link ── */
function deleteNavItem(id, label) {
    if (!confirm(`Delete "${label}"?`)) return;

    fetch(`/admin/settings/navigation/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
    }).then(r => r.json()).then(() => {
        document.querySelector(`.nav-row[data-id="${id}"]`)?.remove();
        if (!document.querySelector('.nav-row')) {
            document.getElementById('navTbody').innerHTML =
                `<tr id="emptyRow"><td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-link-45deg" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                    No menu links yet.</td></tr>`;
        }
    });
}

/* ── Reorder ── */
function moveItem(id, direction) {
    fetch('{{ route('admin.settings.navigation.reorder') }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({id, direction})
    }).then(() => location.reload());
}
</script>
@endpush
@endsection
