@extends('admin.theme_one.component.layout')

@section('content')
@php
    $symbol = \App\Support\Money::symbol($currency);
    $total  = $categories->sum(fn ($c) => $c->menuItems->count());
@endphp
<style>
/* ── Page title ── */
.mi-title        { display:flex;align-items:center;gap:12px; }
.mi-title svg    { width:34px;height:34px;color:var(--primary);flex-shrink:0; }

/* ── Left panel: tabs + forms ── */
.mi-panel        { position:sticky;top:24px;overflow:hidden; }
.mi-panel .card-body { position:relative; }
.mi-pane-head   { display:flex;align-items:flex-end;justify-content:space-between;gap:10px;margin-bottom:14px; }
.mi-pane-head h5 { margin:0 0 4px !important; }
.mi-art          { width:84px;height:58px;color:var(--primary);flex-shrink:0;pointer-events:none; }
.mi-tabs         { display:flex;gap:6px;padding:5px;margin-bottom:18px;border-radius:14px;
                   background:color-mix(in srgb, var(--primary) 8%, white); }
.mi-tab          { flex:1;justify-content:center;display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:10px;border:0;background:transparent;
                   font-weight:700;font-size:.88rem;color:color-mix(in srgb, var(--primary) 70%, #333);text-decoration:none;white-space:nowrap;transition:.15s; }
.mi-tab:hover    { color:var(--primary); }
.mi-tab.active   { background:#fff;color:var(--primary);box-shadow:0 2px 10px color-mix(in srgb, var(--primary) 18%, transparent); }
.mi-tab .plus    { width:22px;height:22px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;
                   background:color-mix(in srgb, var(--primary) 14%, white); }
.mi-tab.active .plus { background:var(--primary);color:#fff; }
.mi-tabs .mi-tab { padding:8px 8px; }
.mi-pane         { display:none; }

/* QR tab */
.qr-box          { display:flex;justify-content:center;padding:18px;border-radius:16px;margin-bottom:14px;
                   background:color-mix(in srgb, var(--primary) 6%, white); }
.qr-box canvas   { width:220px;height:220px;max-width:100%;background:#fff;border-radius:12px;padding:10px;box-shadow:0 4px 18px rgba(0,0,0,.06); }
.qr-url          { font-size:.8rem;word-break:break-all;color:var(--muted);text-align:center;margin-bottom:14px; }
.qr-url a        { color:var(--primary);font-weight:600; }
.mi-pane.active  { display:block; }
.mi-pane h5      { font-weight:800;margin-bottom:16px; }

/* Category photo picker */
.cat-photo       { position:relative;display:block;aspect-ratio:16/9;border-radius:14px;overflow:hidden;cursor:pointer;margin:0;
                   border:1.5px dashed color-mix(in srgb, var(--primary) 35%, white);background:color-mix(in srgb, var(--primary) 6%, white); }
.cat-photo:hover { border-color:var(--primary); }
.cat-photo img   { width:100%;height:100%;object-fit:cover;display:none; }
.cat-photo.has-img { border-style:solid; }
.cat-photo.has-img img { display:block; }
.cat-photo .cp-empty { position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;
                       color:var(--primary);font-size:.85rem;font-weight:600; }
.cat-photo .cp-empty i { font-size:1.5rem; }
.cat-photo .cp-empty small { color:var(--muted);font-weight:400; }
.cat-photo.has-img .cp-empty { display:none; }
.cat-photo .cp-remove { position:absolute;top:8px;right:8px;width:30px;height:30px;border-radius:50%;border:none;display:none;
                        align-items:center;justify-content:center;background:rgba(255,255,255,.92);color:#dc3545;box-shadow:0 2px 8px rgba(0,0,0,.15); }
.cat-photo.has-img .cp-remove { display:flex; }

/* ── Toolbar ── */
.mi-toolbar      { display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:12px 14px; }
.mi-chips        { display:flex;gap:8px;flex-wrap:wrap; }
.mi-chip         { display:inline-flex;align-items:center;gap:7px;padding:6px 14px 6px 8px;border-radius:999px;border:0;cursor:pointer;
                   font-size:.82rem;font-weight:600;color:#2a2a35;background:color-mix(in srgb, var(--primary) 8%, white);transition:.15s; }
.mi-chip:hover   { background:color-mix(in srgb, var(--primary) 16%, white); }
.mi-chip .ci     { width:24px;height:24px;border-radius:50%;object-fit:cover;display:inline-flex;align-items:center;justify-content:center;
                   font-size:.8rem;color:var(--primary);background:#fff; }
.mi-chip .n      { color:var(--muted);font-weight:500; }
.mi-chip.active  { background:var(--primary);color:#fff; }
.mi-chip.active .n { color:rgba(255,255,255,.8); }
.mi-chip.active .ci { background:rgba(255,255,255,.2);color:#fff; }
.mi-search       { position:relative;margin-left:auto; }
.mi-search i     { position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.85rem; }
.mi-search input { padding-left:36px;border-radius:12px;width:220px; }

/* ── Category sections ── */
.mi-sec          { background:#fff;border-radius:16px;overflow:hidden;margin-top:16px;
                   border:1px solid color-mix(in srgb, var(--primary) 10%, white);box-shadow:0 6px 20px rgba(20,32,26,.04); }
.mi-head         { display:flex;align-items:center;gap:10px;padding:10px 14px;background:color-mix(in srgb, var(--primary) 12%, white); }
.mi-head .mi-ico { width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                   background:#fff;color:var(--primary);box-shadow:0 0 0 2px #fff; }
.mi-head h3      { margin:0;font-size:1rem;font-weight:800;color:#1c1c28; }
.mi-count        { font-size:.78rem;font-weight:700;color:var(--primary); }
.mi-hidden       { font-size:.7rem;font-weight:700;padding:2px 8px;border-radius:999px;background:#fff;color:#b02a37; }
.mi-add-btn      { margin-left:auto;display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;border:0;
                   background:color-mix(in srgb, var(--primary) 85%, black);color:#fff;font-size:.8rem;font-weight:600;transition:.15s; }
.mi-add-btn:hover { background:var(--primary); }
.mi-list         { list-style:none;margin:0;padding:0; }
.mi-row          { display:grid;grid-template-columns:18px minmax(0,1fr) 100px 150px auto;align-items:center;gap:14px;padding:10px 16px 10px 10px;background:#fff;
                   border-top:1px solid color-mix(in srgb, var(--primary) 8%, white); }
.mi-row:first-child { border-top:0; }
.mi-row.flash    { animation:miFlash 1.6s ease; }
@keyframes miFlash { 0%,40% { background:color-mix(in srgb, var(--primary) 10%, white); } 100% { background:transparent; } }
.mi-name         { font-weight:700;font-size:.92rem;color:#1c1c28;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.mi-desc         { font-size:.78rem;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.mi-price        { font-weight:800;font-size:.92rem;color:#1c1c28;white-space:nowrap; }
.mi-row.is-off .mi-name, .mi-row.is-off .mi-price { opacity:.5; }
.mi-avail        { display:flex;align-items:center;gap:9px;font-size:.85rem;font-weight:500;color:#333; }
.mi-row.is-off .mi-avail span { color:#b02a37; }
.mi-btns         { display:flex;gap:8px; }
.mi-btn          { display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:999px;border:0;font-size:.85rem;font-weight:700;transition:.15s; }
.mi-btn.del      { background:#fde7ea;color:#d6304a; }
.mi-btn.del:hover { background:#d6304a;color:#fff; }
.mi-empty-row    { padding:14px 16px;color:var(--muted);font-size:.85rem; }
.menu-empty      { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 24px;color:var(--muted);gap:10px;text-align:center; }
.menu-empty i    { font-size:3rem;opacity:.3; }

/* Drag and drop */
.mi-grip, .mi-sec-grip { cursor:grab;color:color-mix(in srgb, var(--primary) 45%, #9aa); font-size:1.05rem;line-height:1;touch-action:none;
                   display:flex;align-items:center;justify-content:center;border-radius:6px;transition:.15s; }
.mi-grip:hover, .mi-sec-grip:hover { color:var(--primary);background:color-mix(in srgb, var(--primary) 10%, white); }
.mi-sec-grip     { width:22px;height:34px;margin-right:-4px; }
.mi-grip:active, .mi-sec-grip:active { cursor:grabbing; }
.mi-ghost        { opacity:.45;background:color-mix(in srgb, var(--primary) 10%, white) !important;outline:2px dashed color-mix(in srgb, var(--primary) 45%, white);outline-offset:-2px; }
.mi-chosen       { box-shadow:0 10px 28px rgba(20,32,26,.16); }
.no-drag-items .mi-grip, .no-drag-cats .mi-sec-grip { visibility:hidden; }
/* Double-click inline editing */
.mi-row .mi-info, .mi-row .mi-price, .mi-cat-name { cursor:text; }
.mi-row:not(.editing) .mi-info, .mi-row:not(.editing) .mi-price, .mi-cat-name { user-select:none; }
.mi-row.editing  { background:color-mix(in srgb, var(--primary) 5%, white); }
.mi-row.editing .form-control { border-radius:10px;padding:5px 10px;font-size:.88rem; }
.mi-row.editing .mi-in-name { font-weight:700; }
.mi-row.saving   { opacity:.6;pointer-events:none; }
.mi-cat-name     { border-radius:6px;padding:1px 4px;margin-left:-4px; }
.mi-cat-name:hover { background:rgba(255,255,255,.65); }
.mi-cat-input    { max-width:240px;height:32px;padding:3px 10px;font-weight:800;border-radius:10px; }
.mi-cat-del      { width:32px;height:32px;border-radius:50%;border:0;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;
                   background:#fff;color:#d6304a;transition:.15s; }
.mi-cat-del:hover { background:#d6304a;color:#fff; }
.mi-hint         { font-size:.8rem;color:var(--muted);margin:14px 2px 0;display:flex;align-items:center;gap:6px; }
.mi-hint i       { color:var(--primary); }

.avail-toggle    { width:38px;height:21px;border-radius:999px;border:none;cursor:pointer;position:relative;transition:.2s;flex-shrink:0; }
.avail-toggle::after { content:'';position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:#fff;transition:.2s; }
.avail-toggle.on { background:var(--primary); }
.avail-toggle.on::after { left:20px; }
.avail-toggle.off { background:#ced4da; }

@media (max-width: 767px) {
    .mi-row { grid-template-columns:18px minmax(0,1fr) auto; }
    .mi-grip { grid-row:span 2; }
    .mi-avail { grid-column:2; }
    .mi-btns { grid-column:3; }
    .mi-btn span { display:none; }
    .mi-btn { padding:8px 11px; }
    .mi-search, .mi-search input { width:100%; }
}
</style>

<div class="page-title mb-4">
    <h2 class="mi-title">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M6 2a1 1 0 0 1 1 1v5h1V3a1 1 0 1 1 2 0v5h1V3a1 1 0 1 1 2 0v6a4 4 0 0 1-3 3.87V21a1.5 1.5 0 0 1-3 0v-8.13A4 4 0 0 1 4 9V3a1 1 0 0 1 2-1z"/>
            <path d="M18.5 2c.83 0 1.5.67 1.5 1.5V21a1.5 1.5 0 0 1-3 0v-6h-1.5a1 1 0 0 1-1-1V7.5C14.5 4.46 16.29 2 18.5 2z"/>
        </svg>
        Menu Items
    </h2>
    <p>Manage your menu items and categories from here.</p>
</div>

<div class="row g-4">

    {{-- ── Left panel: Add Menu Item / Add Category ── --}}
    <div class="col-xl-4">
        <div class="card-soft mi-panel">
            <div class="card-body">

                <div class="mi-tabs" role="tablist">
                    <a href="{{ route('admin.menu.index', ['tab' => 'menu']) }}" class="mi-tab {{ $tab === 'menu' ? 'active' : '' }}" data-tab="menu" role="tab">
                        <span class="plus"><i class="bi bi-plus-lg"></i></span>Menu Item
                    </a>
                    <a href="{{ route('admin.menu.index', ['tab' => 'category']) }}" class="mi-tab {{ $tab === 'category' ? 'active' : '' }}" data-tab="category" role="tab">
                        <span class="plus"><i class="bi bi-plus-lg"></i></span>Category
                    </a>
                    <a href="{{ route('admin.menu.index', ['tab' => 'qr']) }}" class="mi-tab {{ $tab === 'qr' ? 'active' : '' }}" data-tab="qr" role="tab">
                        <span class="plus"><i class="bi bi-qr-code"></i></span>QR Code
                    </a>
                </div>

                {{-- Add Menu Item --}}
                <div class="mi-pane {{ $tab === 'menu' ? 'active' : '' }}" data-pane="menu">
                    <div class="mi-pane-head"><h5>Add Menu Item</h5><svg class="mi-art" viewBox="0 0 104 72" aria-hidden="true">
                    <g stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity=".55">
                        <path d="M28 14 20 6"/><path d="M40 8 38 0"/><path d="M76 14l8-8"/><path d="M64 8l2-8"/><path d="M18 26H8"/><path d="M96 26H86"/>
                    </g>
                    <path d="M22 54a30 30 0 0 1 60 0z" fill="currentColor" opacity=".35"/>
                    <circle cx="52" cy="22" r="4" fill="currentColor" opacity=".45"/>
                    <rect x="14" y="54" width="76" height="7" rx="3.5" fill="currentColor" opacity=".55"/>
                    <path d="M34 42a20 20 0 0 1 12-12" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round" opacity=".8"/>
                </svg></div>
                    <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                    @if($categories->isEmpty())
                    <div class="alert alert-warning py-2" style="font-size:.85rem;">
                        Add a category first — use the <a href="{{ route('admin.menu.index', ['tab' => 'category']) }}">Category</a> tab.
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addName" maxlength="150" placeholder="e.g. Veg. Momo">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-7 col-xl-12 col-xxl-7">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="addCategory">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}{{ $cat->is_active ? '' : ' (hidden)' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-5 col-xl-12 col-xxl-5">
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
                    <label class="form-label fw-semibold d-block">Availability</label>
                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="addAvailable" checked>
                        <label class="form-check-label" for="addAvailable">Available</label>
                    </div>
                    <button class="btn btn-primary w-100" id="addSaveBtn"><i class="bi bi-plus-lg me-1"></i>Create Item</button>
                </div>

                {{-- Add Category --}}
                <div class="mi-pane {{ $tab === 'category' ? 'active' : '' }}" data-pane="category">
                    <div class="mi-pane-head"><h5>Add Category</h5><svg class="mi-art" viewBox="0 0 104 72" aria-hidden="true">
                    <g stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity=".55">
                        <path d="M28 14 20 6"/><path d="M40 8 38 0"/><path d="M76 14l8-8"/><path d="M64 8l2-8"/><path d="M18 26H8"/><path d="M96 26H86"/>
                    </g>
                    <path d="M22 54a30 30 0 0 1 60 0z" fill="currentColor" opacity=".35"/>
                    <circle cx="52" cy="22" r="4" fill="currentColor" opacity=".45"/>
                    <rect x="14" y="54" width="76" height="7" rx="3.5" fill="currentColor" opacity=".55"/>
                    <path d="M34 42a20 20 0 0 1 12-12" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round" opacity=".8"/>
                </svg></div>
                    <div id="catError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="catName" maxlength="100" placeholder="e.g. Thukpa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Photo</label>
                        <label class="cat-photo" id="catPhoto">
                            <input type="file" accept="image/*" class="d-none">
                            <img alt="">
                            <span class="cp-empty"><i class="bi bi-image"></i>Choose a photo<small>JPG, PNG or WEBP · max 3 MB</small></span>
                            <button type="button" class="cp-remove" title="Remove photo"><i class="bi bi-x-lg"></i></button>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="catDesc" rows="3" maxlength="500" placeholder="Optional — shown under the category on the website"></textarea>
                    </div>
                    <button class="btn btn-primary w-100" id="catSaveBtn"><i class="bi bi-plus-lg me-1"></i>Create Category</button>
                    <p class="text-secondary small text-center mt-3 mb-0">
                        Double-click a category name in the list to rename it. To change a photo or hide a category, use the <a href="{{ route('admin.menu_category.index') }}" style="color:var(--primary);font-weight:600;">Categories</a> page.
                    </p>
                </div>

                {{-- Menu QR code --}}
                <div class="mi-pane {{ $tab === 'qr' ? 'active' : '' }}" data-pane="qr">
                    <div class="mi-pane-head"><div><h5>Menu QR Code</h5><div class="text-secondary small">Guests scan it to open your menu.</div></div></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="qrTable">Link to</label>
                        <select class="form-select" id="qrTable">
                            <option value="{{ route('frontend.menu.index') }}" data-label="menu">Whole menu (no table)</option>
                            @foreach($tables as $t)
                            <option value="{{ route('frontend.menu.index', $t->id) }}" data-label="table-{{ $t->table_no ?: $t->id }}">
                                Table {{ $t->table_no }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="qr-box"><canvas id="qrCanvas" width="600" height="600" aria-label="Menu QR code"></canvas></div>
                    <div class="qr-url"><a id="qrLink" href="#" target="_blank" rel="noopener"></a></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="qrDownload"><i class="bi bi-download me-1"></i>Download</button>
                        <button type="button" class="btn btn-outline-secondary flex-fill" id="qrPrint"><i class="bi bi-printer me-1"></i>Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right: toolbar + category sections ── --}}
    <div class="col-xl-8">
        <div class="card-soft mi-toolbar">
            <div class="mi-chips" id="catTabs">
                <button type="button" class="mi-chip active" data-cat="all">
                    <span class="ci"><i class="bi bi-grid-3x3-gap-fill"></i></span>All <span class="n">(<span data-count="all">{{ $total }}</span>)</span>
                </button>
                @foreach($categories as $cat)
                <button type="button" class="mi-chip" data-cat="{{ $cat->id }}">
                    @if($cat->image)<img class="ci" src="{{ $cat->imageUrl() }}" alt="">@else<span class="ci"><i class="bi bi-egg-fried"></i></span>@endif
                    <span class="cn">{{ $cat->name }}</span> <span class="n">(<span data-count="{{ $cat->id }}">{{ $cat->menuItems->count() }}</span>)</span>
                </button>
                @endforeach
            </div>
            <div class="mi-search">
                <i class="bi bi-search"></i>
                <input class="form-control" id="itemSearch" placeholder="Search items…">
            </div>
        </div>

        @if($categories->isEmpty())
        <div class="card-soft menu-empty mt-3">
            <i class="bi bi-journal-text"></i>
            <span class="fw-semibold">Your menu is empty</span>
            <span style="font-size:.85rem;">Create a category with the <strong>Category</strong> tab, then add dishes to it.</span>
        </div>
        @endif

        @if($total)
        <p class="mi-hint" id="dragHint"><i class="bi bi-hand-index-thumb"></i><span><strong>Double-click</strong> a dish or category name to edit it (Enter saves, Esc cancels). <strong>Drag</strong> <i class="bi bi-grip-vertical"></i> to reorder or move dishes.</span></p>
        @endif
        <div id="sections">
            @foreach($categories as $cat)
            <section class="mi-sec" data-cat="{{ $cat->id }}">
                <header class="mi-head">
                    <span class="mi-sec-grip" title="Drag to reorder categories"><i class="bi bi-grip-vertical"></i></span>
                    @if($cat->image)
                    <img class="mi-ico" src="{{ $cat->imageUrl() }}" alt="" loading="lazy">
                    @else
                    <span class="mi-ico"><i class="bi bi-egg-fried"></i></span>
                    @endif
                    <h3 class="mi-cat-name" title="Double-click to rename">{{ $cat->name }}</h3>
                    <span class="mi-count"></span>
                    @unless($cat->is_active)<span class="mi-hidden" title="Hidden from the website">Hidden</span>@endunless
                    <button type="button" class="mi-add-btn" onclick="addInto({{ $cat->id }})"><i class="bi bi-plus-lg"></i>Add Item</button>
                    <button type="button" class="mi-cat-del" title="Delete category" onclick="deleteCategory({{ $cat->id }})"><i class="bi bi-trash3"></i></button>
                </header>
                <ul class="mi-list">
                    @foreach($cat->menuItems as $item)
                    <li class="mi-row {{ $item->is_available ? '' : 'is-off' }}" data-id="{{ $item->id }}" data-name="{{ Str::lower($item->name) }}"
                        data-title="{{ $item->name }}" data-desc="{{ $item->description }}" data-price="{{ (float) $item->price }}">
                        <span class="mi-grip" title="Drag to reorder"><i class="bi bi-grip-vertical"></i></span>
                        <div class="mi-info" style="min-width:0;" title="Double-click to edit">
                            <div class="mi-name">{{ $item->name }}</div>
                            @if($item->description)<div class="mi-desc">{{ $item->description }}</div>@endif
                        </div>
                        <div class="mi-price" title="Double-click to edit">{{ \App\Support\Money::format($item->price, $currency) }}</div>
                        <div class="mi-avail">
                            <button class="avail-toggle {{ $item->is_available ? 'on' : 'off' }}" title="Click to change availability" onclick="toggleAvail({{ $item->id }}, this)"></button>
                            <span>{{ $item->is_available ? 'Available' : 'Unavailable' }}</span>
                        </div>
                        <div class="mi-btns">
                            <button class="mi-btn del" onclick="deleteItem({{ $item->id }})"><i class="bi bi-trash3"></i><span>Delete</span></button>
                        </div>
                    </li>
                    @endforeach
                    <li class="mi-empty-row"><i class="bi bi-info-circle me-1"></i>No dishes yet — click <strong>Add Item</strong>.</li>
                </ul>
            </section>
            @endforeach
        </div>
        <div id="noMatch" class="card-soft menu-empty mt-3 d-none">
            <i class="bi bi-search"></i>
            <span class="fw-semibold">No items match your search</span>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>
<script>
const CSRF   = '{{ csrf_token() }}';
const SYMBOL = @json($symbol);
const MAX_PHOTO = 3 * 1024 * 1024;

const esc   = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const money = n => { n = Number(n) || 0; return SYMBOL + ' ' + n.toLocaleString('en-US', Number.isInteger(n) ? {} : {minimumFractionDigits: 2, maximumFractionDigits: 2}); };

async function readJson(res) { try { return await res.json(); } catch (e) { return {}; } }
function saveError(res, data) {
    if (res.status === 0)   return 'Network error. Please check your connection and try again.';
    if (res.status === 413) return 'This photo is too large to upload. Please choose a smaller file.';
    if (res.status === 419) return 'Your session expired. Please refresh the page.';
    return Object.values(data.errors || {})[0]?.[0] || data.message || 'Something went wrong. Please try again.';
}
function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg; el.classList.toggle('d-none', !msg);
}

/* ════════ Tabs (?tab=menu | ?tab=category | ?tab=qr) ════════ */
function setTab(tab) {
    document.querySelectorAll('.mi-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
    document.querySelectorAll('.mi-pane').forEach(p => p.classList.toggle('active', p.dataset.pane === tab));
    const url = new URL(location.href);
    url.searchParams.set('tab', tab);
    history.replaceState(null, '', url);
}
document.querySelectorAll('.mi-tab').forEach(t => t.addEventListener('click', e => { e.preventDefault(); setTab(t.dataset.tab); }));

/* ════════ Menu QR code ════════ */
(function () {
    const select = document.getElementById('qrTable'), canvas = document.getElementById('qrCanvas'), link = document.getElementById('qrLink');
    if (!select || typeof qrcode === 'undefined') return;

    // Draw the QR for the chosen link onto the canvas (white quiet zone around it).
    function draw() {
        const url = select.value, qr = qrcode(0, 'M');
        qr.addData(url); qr.make();
        const n = qr.getModuleCount(), quiet = 4, size = canvas.width, cell = size / (n + quiet * 2);
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, size, size);
        ctx.fillStyle = '#111';
        for (let r = 0; r < n; r++) for (let c = 0; c < n; c++) {
            if (qr.isDark(r, c)) ctx.fillRect(Math.floor((c + quiet) * cell), Math.floor((r + quiet) * cell), Math.ceil(cell), Math.ceil(cell));
        }
        link.href = url; link.textContent = url;
    }

    document.getElementById('qrDownload').addEventListener('click', () => {
        const a = document.createElement('a');
        a.download = 'qr-' + select.selectedOptions[0].dataset.label + '.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
    });

    document.getElementById('qrPrint').addEventListener('click', () => {
        const label = select.selectedOptions[0].textContent.trim();
        const w = window.open('', '_blank');
        if (!w) return;
        w.document.write(`<!doctype html><title>Menu QR</title><body style="font-family:sans-serif;text-align:center;padding:40px">
            <h2 style="margin:0 0 6px">Scan to see our menu</h2><p style="margin:0 0 20px;color:#555">${esc(label)}</p>
            <img src="${canvas.toDataURL('image/png')}" style="width:320px;height:320px" onload="print()"></body>`);
        w.document.close();
    });

    select.addEventListener('change', draw);
    draw();
})();

/* ════════ Rows & sections ════════ */
function rowHtml(d) {
    const on = !!d.is_available;
    return `<li class="mi-row ${on ? '' : 'is-off'}" data-id="${d.id}" data-name="${esc(d.name.toLowerCase())}"
            data-title="${esc(d.name)}" data-desc="${esc(d.description || '')}" data-price="${Number(d.price)}">
        <span class="mi-grip" title="Drag to reorder"><i class="bi bi-grip-vertical"></i></span>
        <div class="mi-info" style="min-width:0;" title="Double-click to edit"><div class="mi-name">${esc(d.name)}</div>${d.description ? `<div class="mi-desc">${esc(d.description)}</div>` : ''}</div>
        <div class="mi-price" title="Double-click to edit">${money(d.price)}</div>
        <div class="mi-avail"><button class="avail-toggle ${on ? 'on' : 'off'}" title="Click to change availability" onclick="toggleAvail(${d.id}, this)"></button><span>${on ? 'Available' : 'Unavailable'}</span></div>
        <div class="mi-btns">
            <button class="mi-btn del" onclick="deleteItem(${d.id})"><i class="bi bi-trash3"></i><span>Delete</span></button>
        </div>
    </li>`;
}
const sectionOf = catId => document.querySelector(`.mi-sec[data-cat="${catId}"]`);

function refreshCounts() {
    let total = 0;
    document.querySelectorAll('.mi-sec').forEach(sec => {
        const n = sec.querySelectorAll('.mi-row').length;
        total += n;
        sec.querySelector('.mi-count').textContent = `(${n} ${n === 1 ? 'item' : 'items'})`;
        const chip = document.querySelector(`[data-count="${sec.dataset.cat}"]`);
        if (chip) chip.textContent = n;
    });
    const all = document.querySelector('[data-count="all"]');
    if (all) all.textContent = total;
}
function placeRow(d) {
    const sec = sectionOf(d.menu_category_id);
    if (!sec) { location.reload(); return null; }
    const tmp = document.createElement('ul'); tmp.innerHTML = rowHtml(d).trim();
    const li = tmp.firstElementChild;
    sec.querySelector('.mi-list').insertBefore(li, sec.querySelector('.mi-empty-row'));
    return li;
}
function flash(li) {
    if (!li) return;
    li.classList.remove('flash'); void li.offsetWidth; li.classList.add('flash');
    li.scrollIntoView({behavior: 'smooth', block: 'center'});
}

/* ════════ Filter: category chips + search ════════ */
let activeCat = 'all';
function applyFilter() {
    const q = document.getElementById('itemSearch').value.trim().toLowerCase();
    let shown = 0;
    document.querySelectorAll('.mi-sec').forEach(sec => {
        let visible = activeCat === 'all' || sec.dataset.cat === activeCat, hits = 0;
        sec.querySelectorAll('.mi-row').forEach(li => {
            const match = !q || li.dataset.name.includes(q);
            li.style.display = match ? '' : 'none';
            if (match) hits++;
        });
        if (q && !hits) visible = false;
        sec.querySelector('.mi-empty-row').style.display = (!q && !sec.querySelector('.mi-row')) ? '' : 'none';
        sec.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });
    document.getElementById('noMatch').classList.toggle('d-none', shown > 0 || !document.querySelector('.mi-sec'));
    refreshCounts();
    setDragEnabled(!q, !q && activeCat === 'all');
}
document.querySelectorAll('.mi-chip').forEach(chip => chip.addEventListener('click', () => {
    document.querySelectorAll('.mi-chip').forEach(c => c.classList.toggle('active', c === chip));
    activeCat = chip.dataset.cat;
    applyFilter();
}));
document.getElementById('itemSearch').addEventListener('input', applyFilter);

/* ════════ Drag and drop ════════ */
// Dishes: reorder inside a category or drop into another one. Categories: reorder by their header handle.
// Dragging is paused while searching (hidden rows would make the order ambiguous).
const sortables = { items: [], cats: null };
function setDragEnabled(items, cats) {
    if (typeof Sortable === 'undefined') return;
    sortables.items.forEach(s => s.option('disabled', !items));
    sortables.cats?.option('disabled', !cats);
    document.body.classList.toggle('no-drag-items', !items);
    document.body.classList.toggle('no-drag-cats', !cats);
    const hint = document.getElementById('dragHint');
    if (hint) hint.style.visibility = items ? '' : 'hidden';
}
async function saveOrder(evt) {
    if (evt.from === evt.to && evt.oldIndex === evt.newIndex) return;
    const payload = { categories: [], items: {} };
    document.querySelectorAll('.mi-sec').forEach(sec => {
        payload.categories.push(+sec.dataset.cat);
        payload.items[sec.dataset.cat] = [...sec.querySelectorAll('.mi-row')].map(li => +li.dataset.id);
    });
    applyFilter(); // counts + empty rows
    try {
        const res = await fetch('/admin/menu/reorder', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF},
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw res;
    } catch (e) {
        notify.error('The new order could not be saved. Reloading…');
        setTimeout(() => location.reload(), 1200);
    }
}
if (typeof Sortable !== 'undefined') {
    const opts = { animation: 160, ghostClass: 'mi-ghost', chosenClass: 'mi-chosen', onEnd: saveOrder };
    document.querySelectorAll('.mi-list').forEach(ul => sortables.items.push(
        Sortable.create(ul, { ...opts, group: 'menu-items', handle: '.mi-grip', draggable: '.mi-row' })
    ));
    const secs = document.getElementById('sections');
    if (secs) sortables.cats = Sortable.create(secs, { ...opts, handle: '.mi-sec-grip', draggable: '.mi-sec' });
}
applyFilter();

/* "Add Item" on a category: open the menu form with that category picked */
function addInto(catId) {
    setTab('menu');
    document.getElementById('addCategory').value = catId;
    const name = document.getElementById('addName');
    name.scrollIntoView({behavior: 'smooth', block: 'center'});
    name.focus({preventScroll: true});
}

/* ════════ Add item ════════ */
document.getElementById('addSaveBtn').addEventListener('click', async () => {
    const name  = document.getElementById('addName').value.trim();
    const catId = document.getElementById('addCategory').value;
    const price = document.getElementById('addPrice').value;
    showError('addError', '');
    if (!name || !catId || price === '') { showError('addError', 'Name, category and price are required.'); return; }

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
    if (!res.ok) { showError('addError', saveError(res, data)); return; }

    const li = placeRow(data);
    applyFilter();
    flash(li);
    // Keep the category picked so several dishes can be added in a row
    ['addName', 'addPrice', 'addDesc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('addAvailable').checked = true;
    document.getElementById('addName').focus({preventScroll: true});
});

/* ════════ Add category ════════ */
const catPhoto = (function () {
    const box = document.getElementById('catPhoto'), img = box.querySelector('img'), input = box.querySelector('input');
    let file = null;
    const show = url => { img.src = url || ''; box.classList.toggle('has-img', !!url); };
    input.addEventListener('change', () => {
        const f = input.files[0]; input.value = '';
        if (!f) return;
        if (!f.type.startsWith('image/')) { showError('catError', 'Please choose an image file.'); return; }
        if (f.size > MAX_PHOTO) { showError('catError', 'This photo is too large. Please choose one under 3 MB.'); return; }
        showError('catError', '');
        file = f; show(URL.createObjectURL(f));
    });
    box.querySelector('.cp-remove').addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); file = null; show(''); });
    return { get file() { return file; } };
})();

document.getElementById('catSaveBtn').addEventListener('click', async () => {
    const name = document.getElementById('catName').value.trim();
    showError('catError', '');
    if (!name) { document.getElementById('catName').focus(); return; }

    const fd = new FormData();
    fd.append('name', name);
    const desc = document.getElementById('catDesc').value.trim();
    if (desc) fd.append('description', desc);
    if (catPhoto.file) fd.append('image', catPhoto.file);

    const btn = document.getElementById('catSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
    let res, data = {};
    try {
        res  = await fetch('/admin/menu-category', {method: 'POST', body: fd, headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}});
        data = await readJson(res);
    } catch (e) {
        res = {ok: false, status: 0};
    }
    if (!res.ok) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Create Category';
        showError('catError', saveError(res, data));
        return;
    }
    // Reload so the new category appears in the list, chips and dropdowns (tab stays on Category).
    location.reload();
});

/* ════════ Double-click: edit a dish in its row ════════ */
function replaceRow(old, d) {
    const tmp = document.createElement('ul'); tmp.innerHTML = rowHtml(d).trim();
    const li = tmp.firstElementChild;
    old.replaceWith(li);
    return li;
}
function restoreRow(li) {
    replaceRow(li, {
        id: +li.dataset.id, name: li.dataset.title, description: li.dataset.desc,
        price: +li.dataset.price, is_available: !li.classList.contains('is-off'),
    });
}
function startEdit(li) {
    if (li.classList.contains('editing')) return;
    document.querySelectorAll('.mi-row.editing').forEach(saveRow); // one row at a time
    li.classList.add('editing');
    li.querySelector('.mi-info').innerHTML =
        `<input class="form-control mi-in-name" maxlength="150" value="${esc(li.dataset.title)}" aria-label="Name">
         <input class="form-control mi-in-desc mt-1" maxlength="500" value="${esc(li.dataset.desc)}" placeholder="Description (optional)" aria-label="Description">`;
    li.querySelector('.mi-price').innerHTML =
        `<input type="number" class="form-control mi-in-price" min="0" step="0.01" value="${esc(li.dataset.price)}" aria-label="Price">`;
    const name = li.querySelector('.mi-in-name');
    name.focus(); name.select();
}
async function saveRow(li) {
    if (!li.classList.contains('editing') || li.classList.contains('saving')) return;
    const name  = li.querySelector('.mi-in-name').value.trim();
    const desc  = li.querySelector('.mi-in-desc').value.trim();
    const price = li.querySelector('.mi-in-price').value;

    if (name === li.dataset.title && desc === li.dataset.desc && Number(price) === Number(li.dataset.price)) { restoreRow(li); return; }
    if (!name || price === '' || Number(price) < 0) { notify.error('Name and a valid price are required.'); li.querySelector(name ? '.mi-in-price' : '.mi-in-name').focus(); return; }

    const fd = new FormData();
    fd.append('name', name);
    fd.append('description', desc);
    fd.append('price', price);
    fd.append('menu_category_id', li.closest('.mi-sec').dataset.cat);
    fd.append('is_available', li.classList.contains('is-off') ? '0' : '1');
    fd.append('_token', CSRF);

    li.classList.add('saving');
    let res, data = {};
    try {
        res  = await fetch(`/admin/menu/${li.dataset.id}`, {method: 'POST', body: fd, headers: {'Accept': 'application/json'}});
        data = await readJson(res);
    } catch (e) {
        res = {ok: false, status: 0};
    }
    li.classList.remove('saving');
    if (!res.ok) { notify.error(saveError(res, data)); li.querySelector('.mi-in-name').focus(); return; }
    flash(replaceRow(li, data));
    applyFilter();
}
const sectionsEl = document.getElementById('sections');
sectionsEl?.addEventListener('dblclick', e => {
    if (e.target.closest('.mi-avail, .mi-btns, .mi-grip, input')) return;
    const li = e.target.closest('.mi-row');
    if (li) { window.getSelection()?.removeAllRanges(); startEdit(li); return; }
    const title = e.target.closest('.mi-cat-name');
    if (title) startRename(title);
});
sectionsEl?.addEventListener('keydown', e => {
    const li = e.target.closest('.mi-row.editing');
    if (!li) return;
    if (e.key === 'Enter')  { e.preventDefault(); saveRow(li); }
    if (e.key === 'Escape') { e.preventDefault(); restoreRow(li); }
});
// Clicking outside the row saves it
sectionsEl?.addEventListener('focusout', e => {
    const li = e.target.closest('.mi-row.editing');
    if (!li) return;
    setTimeout(() => { if (li.isConnected && !li.contains(document.activeElement)) saveRow(li); }, 120);
});

/* ════════ Double-click: rename a category ════════ */
function startRename(h3) {
    if (h3.querySelector('input')) return;
    const sec = h3.closest('.mi-sec'), old = h3.textContent.trim();
    h3.innerHTML = `<input class="form-control mi-cat-input" maxlength="100" value="${esc(old)}" aria-label="Category name">`;
    const input = h3.querySelector('input');
    input.focus(); input.select();
    let done = false;
    const finish = async (save) => {
        if (done) return; done = true;
        const name = input.value.trim();
        if (!save || !name || name === old) { h3.textContent = old; return; }
        const fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('name', name);
        input.disabled = true;
        let res, data = {};
        try {
            res  = await fetch(`/admin/menu-category/${sec.dataset.cat}`, {method: 'POST', body: fd, headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}});
            data = await readJson(res);
        } catch (e) {
            res = {ok: false, status: 0};
        }
        if (!res.ok) { notify.error(saveError(res, data)); h3.textContent = old; return; }
        h3.textContent = data.name;
        const chipName = document.querySelector(`.mi-chip[data-cat="${sec.dataset.cat}"] .cn`);
        if (chipName) chipName.textContent = data.name;
        const opt = document.querySelector(`#addCategory option[value="${sec.dataset.cat}"]`);
        if (opt) opt.textContent = data.name + (data.is_active ? '' : ' (hidden)');
    };
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter')  { e.preventDefault(); finish(true); }
        if (e.key === 'Escape') { e.preventDefault(); finish(false); }
    });
    input.addEventListener('blur', () => finish(true));
}

/* ════════ Delete a category (and its dishes) ════════ */
async function deleteCategory(id) {
    const sec = sectionOf(id);
    const name = sec?.querySelector('.mi-cat-name')?.textContent.trim() || 'this category';
    const n = sec ? sec.querySelectorAll('.mi-row').length : 0;
    const msg = n ? `Delete "${name}"? Its ${n} ${n === 1 ? 'dish' : 'dishes'} will be deleted too. This cannot be undone.` : `Delete category "${name}"?`;
    if (!confirm(msg)) return;
    let res;
    try {
        res = await fetch(`/admin/menu-category/${id}`, {method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}});
    } catch (e) {
        res = {ok: false};
    }
    if (!res.ok) { notify.error('The category could not be deleted.'); return; }
    sec?.remove();
    document.querySelector(`.mi-chip[data-cat="${id}"]`)?.remove();
    document.querySelector(`#addCategory option[value="${id}"]`)?.remove();
    if (activeCat === String(id)) document.querySelector('.mi-chip[data-cat="all"]')?.click();
    applyFilter();
}

/* ════════ Toggle availability ════════ */
async function toggleAvail(id, btn) {
    const res = await fetch(`/admin/menu/${id}/toggle`, {method: 'PATCH', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}});
    const data = await readJson(res);
    if (!res.ok) return;
    const on = !!data.is_available, row = btn.closest('.mi-row');
    btn.classList.toggle('on', on);
    btn.classList.toggle('off', !on);
    row.classList.toggle('is-off', !on);
    row.querySelector('.mi-avail span').textContent = on ? 'Available' : 'Unavailable';
}

/* ════════ Delete item ════════ */
function deleteItem(id) {
    const li = document.querySelector(`.mi-row[data-id="${id}"]`);
    const name = li?.querySelector('.mi-name')?.textContent || 'this item';
    if (!confirm(`Delete "${name}" from the menu?`)) return;
    fetch(`/admin/menu/${id}`, {method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}})
        .then(r => { if (!r.ok) throw r; return r.json(); })
        .then(() => { li?.remove(); applyFilter(); })
        .catch(() => notify.error('The item could not be deleted.'));
}
</script>
@endpush
@endsection
