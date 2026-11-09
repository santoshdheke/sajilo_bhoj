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
.cat-thumb { width:44px;height:44px;border-radius:12px;object-fit:cover;flex-shrink:0;
             background:color-mix(in srgb, var(--primary) 10%, white); }
div.cat-thumb { display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.1rem; }
.cat-photo { position:relative;display:block;aspect-ratio:4/3;border-radius:14px;overflow:hidden;cursor:pointer;
             border:1.5px dashed color-mix(in srgb, var(--primary) 35%, white);
             background:color-mix(in srgb, var(--primary) 6%, white);transition:border-color .15s; }
.cat-photo:hover { border-color:var(--primary); }
.cat-photo img { width:100%;height:100%;object-fit:cover;display:none; }
.cat-photo.has-img { border-style:solid; }
.cat-photo.has-img img { display:block; }
.cat-photo .cp-empty { position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;
                       gap:4px;color:var(--primary);font-size:.85rem;font-weight:600; }
.cat-photo .cp-empty i { font-size:1.6rem; }
.cat-photo .cp-empty small { color:var(--muted);font-weight:400; }
.cat-photo.has-img .cp-empty { display:none; }
.cat-photo .cp-remove { position:absolute;top:8px;right:8px;width:30px;height:30px;border-radius:50%;border:none;
                        background:rgba(255,255,255,.92);color:#dc3545;display:none;align-items:center;justify-content:center;
                        box-shadow:0 2px 8px rgba(0,0,0,.15); }
.cat-photo.has-img .cp-remove { display:flex; }
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
                <div id="editError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Photo</label>
                    <label class="cat-photo" id="editPhoto">
                        <img alt="">
                        <span class="cp-empty"><i class="bi bi-image"></i>Choose a photo<small>JPG, PNG or WEBP · max 3 MB</small></span>
                        <button type="button" class="cp-remove" title="Remove photo"><i class="bi bi-x-lg"></i></button>
                        <input type="file" accept="image/*" class="d-none">
                    </label>
                </div>
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
                    <label class="form-label fw-semibold">Photo</label>
                    <label class="cat-photo" id="addPhoto">
                        <img alt="">
                        <span class="cp-empty"><i class="bi bi-image"></i>Choose a photo<small>JPG, PNG or WEBP · max 3 MB</small></span>
                        <button type="button" class="cp-remove" title="Remove photo"><i class="bi bi-x-lg"></i></button>
                        <input type="file" accept="image/*" class="d-none">
                    </label>
                </div>

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
                                data-desc="{{ $cat->description }}" data-active="{{ $cat->is_active ? '1' : '0' }}"
                                data-img="{{ $cat->imageUrl() }}">
                                <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($cat->image)
                                        <img class="cat-thumb" src="{{ $cat->imageUrl() }}" alt="" loading="lazy">
                                        @else
                                        <div class="cat-thumb"><i class="bi bi-image"></i></div>
                                        @endif
                                        <span class="fw-semibold">{{ $cat->name }}</span>
                                    </div>
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
const MAX_PHOTO = 3 * 1024 * 1024;
const _editModal = new bootstrap.Modal(document.getElementById('editModal'));

const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const thumbHtml = url => url
    ? `<img class="cat-thumb" src="${esc(url)}" alt="">`
    : '<div class="cat-thumb"><i class="bi bi-image"></i></div>';
const nameCell = (name, url) =>
    `<div class="d-flex align-items-center gap-2">${thumbHtml(url)}<span class="fw-semibold">${esc(name)}</span></div>`;

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

/* ── Photo pickers: preview, size check, remove ── */
function photoPicker(id, onError) {
    const box = document.getElementById(id), img = box.querySelector('img'), input = box.querySelector('input');
    const state = { file: null, removed: false };
    const show = url => { img.src = url || ''; box.classList.toggle('has-img', !!url); };

    input.addEventListener('change', () => {
        const f = input.files[0]; input.value = '';
        if (!f) return;
        if (!f.type.startsWith('image/')) { onError('Please choose an image file.'); return; }
        if (f.size > MAX_PHOTO) { onError('This photo is too large. Please choose one under 3 MB.'); return; }
        onError('');
        state.file = f; state.removed = false;
        show(URL.createObjectURL(f));
    });
    box.querySelector('.cp-remove').addEventListener('click', e => {
        e.preventDefault(); e.stopPropagation();
        state.file = null; state.removed = true; show('');
    });

    return {
        get file() { return state.file; },
        get removed() { return state.removed; },
        reset(url) { state.file = null; state.removed = false; show(url); },
    };
}
function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg; el.classList.toggle('d-none', !msg);
}
const addPhoto  = photoPicker('addPhoto',  m => showError('addError', m));
const editPhoto = photoPicker('editPhoto', m => showError('editError', m));

async function readJson(res) {
    try { return await res.json(); } catch (e) { return {}; }
}
function errorText(res, data) {
    if (res.status === 413) return 'This photo is too large to upload. Please choose a smaller file.';
    const errs = data.errors || {};
    return (errs.name || errs.image || errs.description || [])[0] || data.message || 'Something went wrong.';
}

/* ── Add category ── */
document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const name = document.getElementById('addName').value.trim();
    const desc = document.getElementById('addDesc').value.trim();
    showError('addError', '');
    if (!name) { document.getElementById('addName').focus(); return; }

    const fd = new FormData();
    fd.append('name', name);
    if (desc) fd.append('description', desc);
    if (addPhoto.file) fd.append('image', addPhoto.file);

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
    let res, data;
    try {
        res  = await fetch('/admin/menu-category', {
            method: 'POST', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}, body: fd
        });
        data = await readJson(res);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Save Category';
    }

    if (!res.ok) { showError('addError', errorText(res, data)); return; }

    // Prepend row to table
    document.getElementById('emptyRow')?.remove();
    const tbody = document.getElementById('catTbody');
    const num   = tbody.querySelectorAll('tr.cat-row').length + 1;
    const tr    = document.createElement('tr');
    tr.className = 'cat-row';
    tr.dataset.id     = data.id;
    tr.dataset.name   = data.name;
    tr.dataset.desc   = data.description || '';
    tr.dataset.active = '1';
    tr.dataset.img    = data.image_url || '';
    tr.innerHTML = `
        <td class="text-muted" style="font-size:.8rem;">${num}</td>
        <td>${nameCell(data.name, data.image_url)}</td>
        <td class="text-muted" style="font-size:.85rem;max-width:200px;">${esc((data.description||'').substring(0,60)) || '—'}</td>
        <td class="text-center"><span class="fw-bold">0</span></td>
        <td class="text-center"><span class="status-badge status-ready">Active</span></td>
        <td>
            <div class="d-flex gap-1">
                <button class="tbl-action edit" title="Edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                <button class="tbl-action del"  title="Delete"><i class="bi bi-trash"></i></button>
            </div>
        </td>`;
    tr.querySelector('.del').addEventListener('click', () => deleteCat(data.id, data.name, 0));
    tbody.insertBefore(tr, tbody.firstChild);

    document.getElementById('addName').value = '';
    document.getElementById('addDesc').value = '';
    ['addNameCount','addDescCount'].forEach(id => document.getElementById(id).textContent = '0');
    addPhoto.reset('');
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
    showError('editError', '');
    editPhoto.reset(row.dataset.img || '');
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id     = document.getElementById('editId').value;
    const name   = document.getElementById('editName').value.trim();
    const desc   = document.getElementById('editDesc').value.trim();
    const active = document.getElementById('editActive').checked;
    showError('editError', '');
    if (!name) { document.getElementById('editName').focus(); return; }

    const fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('name', name);
    fd.append('description', desc);
    fd.append('is_active', active ? '1' : '0');
    if (editPhoto.file) fd.append('image', editPhoto.file);
    else if (editPhoto.removed) fd.append('remove_image', '1');

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
    let res, data;
    try {
        res  = await fetch(`/admin/menu-category/${id}`, {
            method: 'POST', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}, body: fd
        });
        data = await readJson(res);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';
    }

    if (!res.ok) { showError('editError', errorText(res, data)); return; }

    _editModal.hide();
    const row = document.querySelector(`.cat-row[data-id="${id}"]`);
    if (row) {
        row.dataset.name   = data.name;
        row.dataset.desc   = data.description || '';
        row.dataset.active = data.is_active ? '1' : '0';
        row.dataset.img    = data.image_url || '';
        row.cells[1].innerHTML = nameCell(data.name, data.image_url);
        row.cells[2].textContent = (data.description || '—').substring(0,60);
        row.cells[4].innerHTML = data.is_active
            ? '<span class="status-badge status-ready">Active</span>'
            : '<span class="status-badge status-cancelled">Inactive</span>';
    }
});

/* ── Delete category ── */
function deleteCat(id, name, count) {
    const msg = count > 0
        ? `Delete "${name}"? This will also delete ${count} menu item(s). This cannot be undone.`
        : `Delete category "${name}"?`;
    if (!confirm(msg)) return;

    fetch(`/admin/menu-category/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
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
