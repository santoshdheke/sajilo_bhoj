@extends('admin.theme_one.component.layout')

@section('content')
<style>
/* ── Menu item card ───────────────────────────────────────────── */
.menu-card          { border-radius:18px;overflow:hidden;transition:.2s;cursor:default; }
.menu-card:hover    { box-shadow:0 8px 28px rgba(0,0,0,.1);transform:translateY(-2px); }
.menu-card-img      { width:100%;height:150px;object-fit:cover;background:#f0fdf6;
                      display:flex;align-items:center;justify-content:center; }
.menu-card-img img  { width:100%;height:150px;object-fit:cover;display:block; }
.menu-card-img.placeholder { font-size:3rem;color:#adb5bd; }
.menu-card-body     { padding:14px 16px; }
.menu-cat-tag       { font-size:.72rem;font-weight:600;padding:2px 8px;border-radius:6px;
                      background:#edfaf3;color:var(--primary);display:inline-block;margin-bottom:6px; }
.menu-card-name     { font-weight:700;font-size:.97rem;margin-bottom:3px;
                      white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.menu-card-desc     { font-size:.78rem;color:var(--muted);margin-bottom:10px;
                      display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
.menu-card-foot     { display:flex;align-items:center;justify-content:space-between;gap:8px; }
.menu-price         { font-size:1.05rem;font-weight:800;color:var(--primary); }
.menu-actions       { display:flex;gap:5px; }

/* Toggle pill */
.avail-toggle       { width:38px;height:21px;border-radius:999px;border:none;cursor:pointer;
                      position:relative;transition:.2s;flex-shrink:0; }
.avail-toggle::after { content:'';position:absolute;top:3px;left:3px;width:15px;height:15px;
                        border-radius:50%;background:#fff;transition:.2s; }
.avail-toggle.on    { background:var(--primary); }
.avail-toggle.on::after { left:20px; }
.avail-toggle.off   { background:#ced4da; }

/* Icon buttons */
.icon-btn           { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;
                      cursor:pointer;display:inline-flex;align-items:center;
                      justify-content:center;transition:.12s; }
.icon-btn.edit      { background:#eef0ff;color:#3730a3; }
.icon-btn.edit:hover { background:#3730a3;color:#fff; }
.icon-btn.del       { background:#fff0f0;color:#dc3545; }
.icon-btn.del:hover  { background:#dc3545;color:#fff; }

/* Category filter tabs */
.cat-tab            { padding:6px 16px;border-radius:10px;border:1px solid var(--line);
                      font-size:.82rem;font-weight:600;cursor:pointer;
                      background:#f8f9fa;color:var(--muted);transition:.15s;
                      text-decoration:none;white-space:nowrap; }
.cat-tab:hover      { background:#e9ecef;color:var(--text); }
.cat-tab.active     { background:var(--primary);color:#fff;border-color:var(--primary); }

/* Image upload preview */
.img-upload-zone    { border:2px dashed #ced4da;border-radius:12px;min-height:110px;
                      display:flex;align-items:center;justify-content:center;
                      flex-direction:column;gap:6px;cursor:pointer;background:#f8f9fa;
                      transition:.18s;overflow:hidden;position:relative; }
.img-upload-zone:hover { border-color:var(--primary);background:#f0fdf6; }
.img-upload-zone img   { position:absolute;inset:0;width:100%;height:100%;object-fit:cover; }
.img-upload-zone .overlay { position:absolute;inset:0;background:rgba(0,0,0,.4);
                             display:flex;align-items:center;justify-content:center;
                             color:#fff;font-size:.85rem;font-weight:600;opacity:0;transition:.18s; }
.img-upload-zone:hover .overlay { opacity:1; }
.img-upload-hint    { color:var(--muted);font-size:.8rem;display:flex;flex-direction:column;
                      align-items:center;gap:4px;pointer-events:none; }
.img-upload-hint i  { font-size:1.6rem; }

/* Empty state */
.menu-empty         { display:flex;flex-direction:column;align-items:center;justify-content:center;
                      padding:60px 24px;color:var(--muted);gap:10px; }
.menu-empty i       { font-size:3rem;opacity:.3; }

/* Sticky form panel */
.form-panel         { position:sticky;top:24px; }
</style>

{{-- ── Edit Item Modal ── --}}
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Menu Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editName" maxlength="150" placeholder="Chicken Burger">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="editCategory">
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius:14px 0 0 14px;">$</span>
                                    <input type="number" class="form-control" id="editPrice" min="0" step="0.01" placeholder="0.00" style="border-radius:0 14px 14px 0;">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="editDesc" rows="3" maxlength="500"
                                      placeholder="Describe the item…"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editAvailable" checked>
                            <label class="form-check-label fw-semibold" for="editAvailable">Available</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Item Photo</label>
                        <div class="img-upload-zone" id="editImgZone"
                             onclick="document.getElementById('editImage').click()">
                            <img id="editImgPreview" src="" alt="" style="display:none;">
                            <div class="img-upload-hint" id="editImgHint">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>Click to change</span>
                            </div>
                            <div class="overlay"><i class="bi bi-pencil me-1"></i>Change</div>
                            <input type="file" id="editImage" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn">
                    <i class="bi bi-check-lg me-1"></i>Update Item
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Menu Items</h2>
    <p>Add, edit, and manage your restaurant's full menu.</p>
</div>

<div class="row g-4">

    {{-- ── Add Form (left panel) ── --}}
    <div class="col-xl-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-basket-fill text-success"></i>
                <h5 class="mb-0">Add Menu Item</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addName" maxlength="150" placeholder="e.g. Chicken Burger">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="addCategory">
                            <option value="">— Select —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius:14px 0 0 14px;">$</span>
                            <input type="number" class="form-control" id="addPrice" min="0" step="0.01" placeholder="0.00" style="border-radius:0 14px 14px 0;">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="addDesc" rows="3" maxlength="500"
                              placeholder="Describe the item…"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold d-block mb-2">Item Photo</label>
                    <div class="img-upload-zone" id="addImgZone"
                         onclick="document.getElementById('addImage').click()">
                        <img id="addImgPreview" src="" alt="" style="display:none;">
                        <div class="img-upload-hint" id="addImgHint">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span>Click to upload</span>
                            <span style="font-size:.72rem;">JPG, PNG — max 3MB</span>
                        </div>
                        <div class="overlay"><i class="bi bi-pencil me-1"></i>Change</div>
                        <input type="file" id="addImage" accept="image/*" class="d-none">
                    </div>
                </div>
                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="addAvailable" checked>
                    <label class="form-check-label fw-semibold" for="addAvailable">Available</label>
                </div>
                <button class="btn btn-primary w-100" id="addSaveBtn">
                    <i class="bi bi-plus-lg me-1"></i>Create Item
                </button>
            </div>
        </div>
    </div>

    {{-- ── Menu Items Grid (right) ── --}}
    <div class="col-xl-8">

        {{-- Filters & search --}}
        <div class="card-soft mb-3" style="padding:14px 16px;">
            <div class="d-flex gap-2 align-items-center justify-content-between flex-wrap">
                <div class="d-flex gap-2 flex-wrap" id="catTabs">
                    <a href="{{ route('admin.menu.index') }}"
                       class="cat-tab {{ !request('category') || request('category')==='all' ? 'active' : '' }}">
                        All <span class="text-muted fw-normal">({{ $items->count() }})</span>
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('admin.menu.index', ['category'=>$cat->id, 'search'=>request('search')]) }}"
                       class="cat-tab {{ request('category') == $cat->id ? 'active' : '' }}">
                        {{ $cat->name }}
                        <span class="text-muted fw-normal">({{ $items->where('menu_category_id',$cat->id)->count() }})</span>
                    </a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('admin.menu.index') }}" class="d-flex gap-2">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <div style="position:relative;">
                        <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.8rem;"></i>
                        <input class="form-control form-control-sm" name="search" value="{{ request('search') }}"
                               placeholder="Search items…"
                               style="padding-left:30px;border-radius:10px;width:180px;">
                    </div>
                </form>
            </div>
        </div>

        {{-- Grid --}}
        @if($items->isEmpty())
        <div class="card-soft menu-empty">
            <i class="bi bi-basket3"></i>
            <span class="fw-semibold">No items found</span>
            <span style="font-size:.85rem;">{{ request('search') ? 'Try a different search.' : 'Add your first menu item using the form.' }}</span>
        </div>
        @else
        <div class="row g-3" id="menuGrid">
            @foreach($items as $item)
            <div class="col-sm-6 col-lg-4 menu-item-col" data-id="{{ $item->id }}">
                <div class="card-soft menu-card">
                    {{-- Image --}}
                    <div class="menu-card-img {{ $item->image ? '' : 'placeholder' }}">
                        @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
                        @else
                        <i class="bi bi-egg-fried"></i>
                        @endif
                    </div>
                    <div class="menu-card-body">
                        <div class="menu-cat-tag">{{ $item->category->name }}</div>
                        <div class="menu-card-name" title="{{ $item->name }}">{{ $item->name }}</div>
                        @if($item->description)
                        <div class="menu-card-desc">{{ $item->description }}</div>
                        @endif
                        <div class="menu-card-foot">
                            <span class="menu-price">${{ number_format($item->price, 2) }}</span>
                            <div class="menu-actions">
                                <button class="avail-toggle {{ $item->is_available ? 'on' : 'off' }}"
                                        title="{{ $item->is_available ? 'Available — click to mark unavailable' : 'Unavailable — click to make available' }}"
                                        onclick="toggleAvail({{ $item->id }}, this)"></button>
                                <button class="icon-btn edit" title="Edit"
                                        onclick="openEditItem({{ $item->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="icon-btn del" title="Delete"
                                        onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('js')
<script>
const CSRF       = '{{ csrf_token() }}';
const CATEGORIES = {!! json_encode($categories->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values()) !!};
const _editModal = new bootstrap.Modal(document.getElementById('editItemModal'));

/* ════════ Image preview helpers ════════ */
function wireImgPreview(inputId, previewId, hintId) {
    document.getElementById(inputId).addEventListener('change', function () {
        if (!this.files[0]) return;
        const prev = document.getElementById(previewId);
        const hint = document.getElementById(hintId);
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
        hint.style.display = 'none';
    });
}
wireImgPreview('addImage','addImgPreview','addImgHint');
wireImgPreview('editImage','editImgPreview','editImgHint');

/* ════════ Add item ════════ */
document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const name  = document.getElementById('addName').value.trim();
    const catId = document.getElementById('addCategory').value;
    const price = document.getElementById('addPrice').value;
    const err   = document.getElementById('addError');
    err.classList.add('d-none');

    if (!name || !catId || price === '') {
        err.textContent = 'Name, category and price are required.';
        err.classList.remove('d-none');
        return;
    }

    const btn = document.getElementById('addSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const fd = new FormData();
    fd.append('name',             name);
    fd.append('menu_category_id', catId);
    fd.append('price',            price);
    fd.append('description',      document.getElementById('addDesc').value.trim());
    fd.append('is_available',     document.getElementById('addAvailable').checked ? '1' : '0');
    fd.append('_token',           CSRF);
    const imgFile = document.getElementById('addImage').files[0];
    if (imgFile) fd.append('image', imgFile);

    const res  = await fetch('/admin/menu', {method:'POST', body:fd});
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Create Item';

    if (!res.ok) {
        const firstErr = Object.values(data.errors || {})[0]?.[0] || 'Something went wrong.';
        err.textContent = firstErr;
        err.classList.remove('d-none');
        return;
    }

    // Add card to grid
    const grid = document.getElementById('menuGrid');
    if (!grid) { location.reload(); return; }
    const imgHtml = data.image
        ? `<img src="/storage/${data.image}" alt="${data.name}">`
        : `<i class="bi bi-egg-fried"></i>`;
    const col = document.createElement('div');
    col.className = 'col-sm-6 col-lg-4 menu-item-col';
    col.dataset.id = data.id;
    col.innerHTML = `
        <div class="card-soft menu-card">
            <div class="menu-card-img ${data.image ? '' : 'placeholder'}">${imgHtml}</div>
            <div class="menu-card-body">
                <div class="menu-cat-tag">${data.category.name}</div>
                <div class="menu-card-name">${data.name}</div>
                ${data.description ? `<div class="menu-card-desc">${data.description}</div>` : ''}
                <div class="menu-card-foot">
                    <span class="menu-price">$${parseFloat(data.price).toFixed(2)}</span>
                    <div class="menu-actions">
                        <button class="avail-toggle ${data.is_available?'on':'off'}"
                                onclick="toggleAvail(${data.id},this)"></button>
                        <button class="icon-btn edit" onclick="openEditItem(${data.id})"><i class="bi bi-pencil"></i></button>
                        <button class="icon-btn del"  onclick="deleteItem(${data.id},'${data.name.replace(/'/g,"\\'")}')"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>`;
    grid.insertBefore(col, grid.firstChild);

    // Reset form
    document.getElementById('addName').value  = '';
    document.getElementById('addPrice').value = '';
    document.getElementById('addDesc').value  = '';
    document.getElementById('addCategory').value = '';
    document.getElementById('addAvailable').checked = true;
    document.getElementById('addImage').value = '';
    document.getElementById('addImgPreview').style.display = 'none';
    document.getElementById('addImgHint').style.display = '';
});

/* ════════ Edit item ════════ */
const _itemCache = {};

async function openEditItem(id) {
    if (!_itemCache[id]) {
        const res = await fetch(`/admin/menu/${id}`, {headers:{'Accept':'application/json'}});
        if (!res.ok) return;
        _itemCache[id] = await res.json();
    }
    const d = _itemCache[id];
    document.getElementById('editId').value              = d.id;
    document.getElementById('editName').value            = d.name;
    document.getElementById('editCategory').value        = d.menu_category_id;
    document.getElementById('editPrice').value           = d.price;
    document.getElementById('editDesc').value            = d.description || '';
    document.getElementById('editAvailable').checked     = d.is_available;
    document.getElementById('editImage').value           = '';

    const prev = document.getElementById('editImgPreview');
    const hint = document.getElementById('editImgHint');
    if (d.image) {
        prev.src = '/storage/' + d.image;
        prev.style.display = 'block';
        hint.style.display = 'none';
    } else {
        prev.style.display = 'none';
        hint.style.display = '';
    }
    _editModal.show();
}

// Expose show endpoint (reuse admin.menu.show if needed — just fetch the item)
// We'll add a quick GET route for it
document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id    = document.getElementById('editId').value;
    const name  = document.getElementById('editName').value.trim();
    const catId = document.getElementById('editCategory').value;
    const price = document.getElementById('editPrice').value;
    if (!name || !catId || price === '') return;

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const fd = new FormData();
    fd.append('_method',          'POST');   // route accepts POST
    fd.append('name',             name);
    fd.append('menu_category_id', catId);
    fd.append('price',            price);
    fd.append('description',      document.getElementById('editDesc').value.trim());
    fd.append('is_available',     document.getElementById('editAvailable').checked ? '1' : '0');
    fd.append('_token',           CSRF);
    const imgFile = document.getElementById('editImage').files[0];
    if (imgFile) fd.append('image', imgFile);

    const res  = await fetch(`/admin/menu/${id}`, {method:'POST', body:fd});
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update Item';

    if (res.ok) {
        delete _itemCache[id]; // bust cache
        _editModal.hide();
        location.reload();     // simple reload to reflect all changes
    }
});

/* ════════ Toggle availability ════════ */
async function toggleAvail(id, btn) {
    const res  = await fetch(`/admin/menu/${id}/toggle`, {
        method: 'PATCH', headers: {'X-CSRF-TOKEN': CSRF}
    });
    const data = await res.json();
    if (res.ok) {
        btn.classList.toggle('on',  data.is_available);
        btn.classList.toggle('off', !data.is_available);
        btn.title = data.is_available
            ? 'Available — click to mark unavailable'
            : 'Unavailable — click to make available';
        if (_itemCache[id]) _itemCache[id].is_available = data.is_available;
    }
}

/* ════════ Delete item ════════ */
function deleteItem(id, name) {
    if (!confirm(`Delete "${name}" from the menu?`)) return;
    fetch(`/admin/menu/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF}})
    .then(r => r.json())
    .then(() => {
        document.querySelector(`.menu-item-col[data-id="${id}"]`)?.remove();
        delete _itemCache[id];
    });
}
</script>
@endpush
@endsection
