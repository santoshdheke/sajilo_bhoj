@extends('admin.theme_one.component.layout')

@section('content')
@php
    $symbol = \App\Support\Money::symbol($currency);
    $total  = $categories->sum(fn ($c) => $c->menuItems->count());
@endphp
<style>
/* ── Printed menu-card boards (one per category) ── */
.am-board        { background:#fff;border-radius:18px;overflow:hidden;margin-bottom:22px;
                   border:1px solid color-mix(in srgb, var(--primary) 14%, white);box-shadow:0 8px 24px rgba(20,32,26,.05); }
.am-head         { display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 18px;color:#fff;
                   background:linear-gradient(90deg, var(--primary), color-mix(in srgb, var(--primary) 75%, black)); }
.am-head h3      { margin:0;font-size:1.05rem;font-weight:800;letter-spacing:.02em;color:#fff; }
.am-head .am-meta{ display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
.am-count        { font-size:.72rem;font-weight:600;padding:2px 9px;border-radius:999px;background:rgba(255,255,255,.18); }
.am-hidden       { font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:999px;background:#fff;color:#b02a37; }
.am-price-lbl    { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;opacity:.85; }
.am-body         { display:flex;gap:18px;padding:8px 18px 12px; }
.am-list         { flex:1;min-width:0;list-style:none;margin:0;padding:0; }
.am-item         { display:flex;align-items:center;gap:10px;padding:9px 0;
                   border-bottom:1px dashed color-mix(in srgb, var(--primary) 16%, white); }
.am-item:has(+ .am-empty-row) { border-bottom:0; }
.am-item.flash   { animation:amFlash 1.6s ease; }
@keyframes amFlash { 0%,40% { background:color-mix(in srgb, var(--primary) 12%, white); } 100% { background:transparent; } }
.am-name         { min-width:0;display:flex;flex-direction:column;font-weight:600;font-size:.93rem;color:#1c2620;line-height:1.3; }
.am-name small   { color:var(--muted);font-weight:400;font-size:.76rem;
                   display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden; }
.am-off-tag      { display:none;font-size:.68rem;font-weight:700;color:#b02a37;margin-left:6px; }
.am-item.is-off .am-name > span, .am-item.is-off .am-price { opacity:.45;text-decoration:line-through; }
.am-item.is-off .am-off-tag { display:inline; }
.am-dots         { flex:1;min-width:14px;align-self:flex-end;margin-bottom:7px;
                   border-bottom:2px dotted color-mix(in srgb, var(--primary) 30%, white); }
.am-price        { font-weight:800;color:color-mix(in srgb, var(--primary) 80%, black);white-space:nowrap;font-size:.93rem; }
.am-actions      { display:flex;align-items:center;gap:5px;flex-shrink:0; }
.am-empty-row    { padding:16px 0;color:var(--muted);font-size:.85rem; }
.am-photo        { width:150px;flex-shrink:0;align-self:flex-start;margin-top:8px; }
.am-photo img    { width:100%;aspect-ratio:1;object-fit:cover;border-radius:14px;display:block;
                   box-shadow:0 0 0 3px #fff, 0 0 0 5px color-mix(in srgb, var(--primary) 22%, white); }
.am-photo-empty  { width:100%;aspect-ratio:1;border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;
                   border:1.5px dashed color-mix(in srgb, var(--primary) 30%, white);color:var(--primary);font-size:.78rem;font-weight:600;
                   background:color-mix(in srgb, var(--primary) 5%, white);text-decoration:none; }
.am-photo-empty i { font-size:1.4rem; }
.am-photo-empty:hover { border-color:var(--primary);color:var(--primary); }
@media (max-width: 575px) {
    .am-body { flex-direction:column-reverse; }
    .am-photo { width:100%; }
    .am-photo img, .am-photo-empty { aspect-ratio:16/7; }
    .am-item { flex-wrap:wrap; }
    .am-actions { margin-left:auto; }
}

/* Availability toggle + icon buttons */
.avail-toggle    { width:38px;height:21px;border-radius:999px;border:none;cursor:pointer;position:relative;transition:.2s;flex-shrink:0; }
.avail-toggle::after { content:'';position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:#fff;transition:.2s; }
.avail-toggle.on { background:var(--primary); }
.avail-toggle.on::after { left:20px; }
.avail-toggle.off { background:#ced4da; }
.icon-btn        { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;cursor:pointer;
                   display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.icon-btn.edit   { background:#eef0ff;color:#3730a3; }
.icon-btn.edit:hover { background:#3730a3;color:#fff; }
.icon-btn.del    { background:#fff0f0;color:#dc3545; }
.icon-btn.del:hover { background:#dc3545;color:#fff; }

/* Toolbar */
.cat-tab         { display:inline-flex;align-items:center;gap:7px;padding:5px 14px;border-radius:999px;border:1px solid var(--line);
                   font-size:.82rem;font-weight:600;cursor:pointer;background:#fff;color:var(--muted);transition:.15s;white-space:nowrap; }
.cat-tab:hover   { border-color:var(--primary);color:var(--primary); }
.cat-tab.active  { background:var(--primary);color:#fff;border-color:var(--primary); }
.cat-tab img     { width:22px;height:22px;border-radius:50%;object-fit:cover;margin-left:-8px; }
.menu-empty      { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 24px;color:var(--muted);gap:10px;text-align:center; }
.menu-empty i    { font-size:3rem;opacity:.3; }
.form-panel      { position:sticky;top:24px; }
</style>

{{-- ── Edit Item Modal ── --}}
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Menu Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editName" maxlength="150">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="editCategory">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}{{ $cat->is_active ? '' : ' (hidden)' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius:14px 0 0 14px;">{{ $symbol }}</span>
                            <input type="number" class="form-control" id="editPrice" min="0" step="0.01" placeholder="0" style="border-radius:0 14px 14px 0;">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="editDesc" rows="3" maxlength="500" placeholder="Optional — shown under the name"></textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="editAvailable" checked>
                    <label class="form-check-label fw-semibold" for="editAvailable">Available</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn"><i class="bi bi-check-lg me-1"></i>Update Item</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Menu Items</h2>
    <p>Your menu, grouped by category. Photos belong to categories — change them under <a href="{{ route('admin.menu_category.index') }}" style="color:var(--primary);font-weight:600;">Categories</a>.</p>
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
                @if($categories->isEmpty())
                <div class="alert alert-warning py-2" style="font-size:.85rem;">
                    Create a <a href="{{ route('admin.menu_category.index') }}">category</a> first.
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addName" maxlength="150" placeholder="e.g. Veg. Momo">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-xl-12 col-xxl-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="addCategory">
                            <option value="">— Select —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}{{ $cat->is_active ? '' : ' (hidden)' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-xl-12 col-xxl-6">
                        <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius:14px 0 0 14px;">{{ $symbol }}</span>
                            <input type="number" class="form-control" id="addPrice" min="0" step="0.01" placeholder="0" style="border-radius:0 14px 14px 0;">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="addDesc" rows="3" maxlength="500" placeholder="Optional — shown under the name"></textarea>
                </div>
                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="addAvailable" checked>
                    <label class="form-check-label fw-semibold" for="addAvailable">Available</label>
                </div>
                <button class="btn btn-primary w-100" id="addSaveBtn"><i class="bi bi-plus-lg me-1"></i>Create Item</button>
            </div>
        </div>
    </div>

    {{-- ── Menu boards (right) ── --}}
    <div class="col-xl-8">
        <div class="card-soft mb-3" style="padding:12px 14px;">
            <div class="d-flex gap-2 align-items-center justify-content-between flex-wrap">
                <div class="d-flex gap-2 flex-wrap" id="catTabs">
                    <button type="button" class="cat-tab active" data-cat="all">All <span class="fw-normal opacity-75">({{ $total }})</span></button>
                    @foreach($categories as $cat)
                    <button type="button" class="cat-tab" data-cat="{{ $cat->id }}">
                        @if($cat->image)<img src="{{ $cat->imageUrl() }}" alt="">@endif{{ $cat->name }}
                    </button>
                    @endforeach
                </div>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.8rem;"></i>
                    <input class="form-control form-control-sm" id="itemSearch" placeholder="Search items…" style="padding-left:30px;border-radius:10px;width:180px;">
                </div>
            </div>
        </div>

        @if($categories->isEmpty())
        <div class="card-soft menu-empty">
            <i class="bi bi-journal-text"></i>
            <span class="fw-semibold">Your menu is empty</span>
            <span style="font-size:.85rem;">Start by creating a <a href="{{ route('admin.menu_category.index') }}">category</a>, then add dishes to it.</span>
        </div>
        @endif

        <div id="boards">
            @foreach($categories as $cat)
            <section class="am-board" data-cat="{{ $cat->id }}">
                <header class="am-head">
                    <div class="am-meta">
                        <h3>{{ $cat->name }}</h3>
                        <span class="am-count"></span>
                        @unless($cat->is_active)<span class="am-hidden" title="Hidden from the website">Hidden</span>@endunless
                    </div>
                    <span class="am-price-lbl">Price</span>
                </header>
                <div class="am-body">
                    <ul class="am-list">
                        @foreach($cat->menuItems as $item)
                        <li class="am-item {{ $item->is_available ? '' : 'is-off' }}" data-id="{{ $item->id }}" data-name="{{ Str::lower($item->name) }}">
                            <div class="am-name">
                                <span>{{ $item->name }}<em class="am-off-tag">Unavailable</em></span>
                                @if($item->description)<small>{{ $item->description }}</small>@endif
                            </div>
                            <span class="am-dots" aria-hidden="true"></span>
                            <span class="am-price">{{ \App\Support\Money::format($item->price, $currency) }}</span>
                            <div class="am-actions">
                                <button class="avail-toggle {{ $item->is_available ? 'on' : 'off' }}" title="{{ $item->is_available ? 'Available — click to mark unavailable' : 'Unavailable — click to make available' }}" onclick="toggleAvail({{ $item->id }}, this)"></button>
                                <button class="icon-btn edit" title="Edit" onclick="openEditItem({{ $item->id }})"><i class="bi bi-pencil"></i></button>
                                <button class="icon-btn del" title="Delete" onclick="deleteItem({{ $item->id }})"><i class="bi bi-trash"></i></button>
                            </div>
                        </li>
                        @endforeach
                        <li class="am-empty-row"><i class="bi bi-plus-circle me-1"></i>No dishes yet — add one with the form.</li>
                    </ul>
                    <div class="am-photo">
                        @if($cat->image)
                        <img src="{{ $cat->imageUrl() }}" alt="{{ $cat->name }}" loading="lazy">
                        @else
                        <a class="am-photo-empty" href="{{ route('admin.menu_category.index') }}" title="Add a photo to this category"><i class="bi bi-image"></i>Add photo</a>
                        @endif
                    </div>
                </div>
            </section>
            @endforeach
        </div>
        <div id="noMatch" class="card-soft menu-empty d-none">
            <i class="bi bi-search"></i>
            <span class="fw-semibold">No items match your search</span>
        </div>
    </div>
</div>

@push('js')
<script>
const CSRF   = '{{ csrf_token() }}';
const SYMBOL = @json($symbol);
const _editModal = new bootstrap.Modal(document.getElementById('editItemModal'));

const esc   = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const money = n => { n = Number(n) || 0; return SYMBOL + ' ' + n.toLocaleString('en-US', Number.isInteger(n) ? {} : {minimumFractionDigits: 2, maximumFractionDigits: 2}); };

// Read a JSON response safely (a 413 or server error returns HTML, not JSON).
async function readJson(res) { try { return await res.json(); } catch (e) { return {}; } }
function saveError(res, data) {
    if (res.status === 0)   return 'Network error. Please check your connection and try again.';
    if (res.status === 419) return 'Your session expired. Please refresh the page.';
    return Object.values(data.errors || {})[0]?.[0] || data.message || 'Something went wrong. Please try again.';
}

/* ════════ Rows & boards ════════ */
function rowHtml(d) {
    const on = !!d.is_available;
    return `<li class="am-item ${on ? '' : 'is-off'}" data-id="${d.id}" data-name="${esc(d.name.toLowerCase())}">
        <div class="am-name"><span>${esc(d.name)}<em class="am-off-tag">Unavailable</em></span>${d.description ? `<small>${esc(d.description)}</small>` : ''}</div>
        <span class="am-dots" aria-hidden="true"></span>
        <span class="am-price">${money(d.price)}</span>
        <div class="am-actions">
            <button class="avail-toggle ${on ? 'on' : 'off'}" title="${on ? 'Available — click to mark unavailable' : 'Unavailable — click to make available'}" onclick="toggleAvail(${d.id}, this)"></button>
            <button class="icon-btn edit" title="Edit" onclick="openEditItem(${d.id})"><i class="bi bi-pencil"></i></button>
            <button class="icon-btn del" title="Delete" onclick="deleteItem(${d.id})"><i class="bi bi-trash"></i></button>
        </div>
    </li>`;
}
const boardOf = catId => document.querySelector(`.am-board[data-cat="${catId}"]`);

function refreshBoard(board) {
    if (!board) return;
    const n = board.querySelectorAll('.am-item').length;
    board.querySelector('.am-count').textContent = n + (n === 1 ? ' item' : ' items');
    board.querySelector('.am-empty-row').style.display = n ? 'none' : '';
}
function placeRow(d) {
    const board = boardOf(d.menu_category_id);
    if (!board) { location.reload(); return null; }
    const tmp = document.createElement('ul'); tmp.innerHTML = rowHtml(d).trim();
    const li = tmp.firstElementChild;
    board.querySelector('.am-list').insertBefore(li, board.querySelector('.am-empty-row'));
    refreshBoard(board);
    return li;
}
function flash(li) {
    if (!li) return;
    li.classList.remove('flash'); void li.offsetWidth; li.classList.add('flash');
    li.scrollIntoView({behavior: 'smooth', block: 'center'});
}
document.querySelectorAll('.am-board').forEach(refreshBoard);

/* ════════ Filter: category tabs + search ════════ */
let activeCat = 'all';
function applyFilter() {
    const q = document.getElementById('itemSearch').value.trim().toLowerCase();
    let shown = 0;
    document.querySelectorAll('.am-board').forEach(board => {
        let visible = activeCat === 'all' || board.dataset.cat === activeCat;
        let hits = 0;
        board.querySelectorAll('.am-item').forEach(li => {
            const match = !q || li.dataset.name.includes(q);
            li.style.display = match ? '' : 'none';
            if (match) hits++;
        });
        if (q && !hits) visible = false;
        board.querySelector('.am-empty-row').style.display = (!q && !board.querySelector('.am-item')) ? '' : 'none';
        board.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });
    document.getElementById('noMatch').classList.toggle('d-none', shown > 0 || !document.querySelector('.am-board'));
}
document.querySelectorAll('.cat-tab').forEach(tab => tab.addEventListener('click', () => {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.toggle('active', t === tab));
    activeCat = tab.dataset.cat;
    applyFilter();
}));
document.getElementById('itemSearch').addEventListener('input', applyFilter);

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
    fd.append('name', name);
    fd.append('menu_category_id', catId);
    fd.append('price', price);
    fd.append('description', document.getElementById('addDesc').value.trim());
    fd.append('is_available', document.getElementById('addAvailable').checked ? '1' : '0');
    fd.append('_token', CSRF);

    let res, data = {};
    try {
        res  = await fetch('/admin/menu', {method: 'POST', body: fd, headers: {'Accept': 'application/json'}});
        data = await readJson(res);
    } catch (e) {
        res = {ok: false, status: 0};
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Create Item';
    }

    if (!res.ok) {
        err.textContent = saveError(res, data);
        err.classList.remove('d-none');
        return;
    }

    const li = placeRow(data);
    applyFilter();
    flash(li);

    // Keep the category selected so several dishes can be added in a row
    ['addName', 'addPrice', 'addDesc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('addAvailable').checked = true;
    document.getElementById('addName').focus();
});

/* ════════ Edit item ════════ */
async function openEditItem(id) {
    const res = await fetch(`/admin/menu/${id}`, {headers: {'Accept': 'application/json'}});
    if (!res.ok) { notify.error('This item could not be loaded.'); return; }
    const d = await res.json();
    document.getElementById('editId').value          = d.id;
    document.getElementById('editName').value        = d.name;
    document.getElementById('editCategory').value    = d.menu_category_id;
    document.getElementById('editPrice').value       = d.price;
    document.getElementById('editDesc').value        = d.description || '';
    document.getElementById('editAvailable').checked = d.is_available;
    _editModal.show();
}

document.getElementById('editSaveBtn').addEventListener('click', async () => {
    const id    = document.getElementById('editId').value;
    const name  = document.getElementById('editName').value.trim();
    const catId = document.getElementById('editCategory').value;
    const price = document.getElementById('editPrice').value;
    if (!name || !catId || price === '') { notify.error('Name, category and price are required.'); return; }

    const btn = document.getElementById('editSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const fd = new FormData();
    fd.append('name', name);
    fd.append('menu_category_id', catId);
    fd.append('price', price);
    fd.append('description', document.getElementById('editDesc').value.trim());
    fd.append('is_available', document.getElementById('editAvailable').checked ? '1' : '0');
    fd.append('_token', CSRF);

    let res, data = {};
    try {
        res  = await fetch(`/admin/menu/${id}`, {method: 'POST', body: fd, headers: {'Accept': 'application/json'}});
        data = await readJson(res);
    } catch (e) {
        res = {ok: false, status: 0};
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update Item';
    }

    if (!res.ok) { notify.error(saveError(res, data)); return; }

    _editModal.hide();
    const old = document.querySelector(`.am-item[data-id="${id}"]`);
    const oldBoard = old?.closest('.am-board');
    if (old && oldBoard && oldBoard.dataset.cat == data.menu_category_id) {
        // Same category: replace in place, keep its position
        const tmp = document.createElement('ul'); tmp.innerHTML = rowHtml(data).trim();
        old.replaceWith(tmp.firstElementChild);
        flash(document.querySelector(`.am-item[data-id="${id}"]`));
    } else {
        old?.remove();
        refreshBoard(oldBoard);
        flash(placeRow(data));
    }
    applyFilter();
});

/* ════════ Toggle availability ════════ */
async function toggleAvail(id, btn) {
    const res = await fetch(`/admin/menu/${id}/toggle`, {method: 'PATCH', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}});
    const data = await readJson(res);
    if (!res.ok) return;
    btn.classList.toggle('on', data.is_available);
    btn.classList.toggle('off', !data.is_available);
    btn.title = data.is_available ? 'Available — click to mark unavailable' : 'Unavailable — click to make available';
    btn.closest('.am-item').classList.toggle('is-off', !data.is_available);
}

/* ════════ Delete item ════════ */
function deleteItem(id) {
    const li = document.querySelector(`.am-item[data-id="${id}"]`);
    const name = li?.querySelector('.am-name > span')?.firstChild?.textContent || 'this item';
    if (!confirm(`Delete "${name}" from the menu?`)) return;
    fetch(`/admin/menu/${id}`, {method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}})
        .then(r => { if (!r.ok) throw r; return r.json(); })
        .then(() => {
            const board = li?.closest('.am-board');
            li?.remove();
            refreshBoard(board);
            applyFilter();
        })
        .catch(() => notify.error('The item could not be deleted.'));
}
</script>
@endpush
@endsection
