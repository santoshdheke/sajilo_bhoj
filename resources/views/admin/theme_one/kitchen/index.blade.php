@extends('admin.theme_one.component.layout')

@section('content')
<style>
/* ── KDS Layout ───────────────────────────────────────────────── */
.kds-board       { display:flex; gap:16px; overflow-x:auto; align-items:flex-start;
                   padding-bottom:12px; }
.kds-col-wrap    { flex:0 0 300px; display:flex; flex-direction:column; }

/* Column header */
.kds-col-head    { display:flex; align-items:center; justify-content:space-between;
                   padding:13px 16px 11px; border-bottom:1px solid var(--line);
                   border-radius:22px 22px 0 0; }
.kds-col-title   { display:flex; align-items:center; gap:8px; font-size:.93rem;
                   font-weight:700; }
.kds-count       { min-width:22px; height:22px; border-radius:999px; font-size:.73rem;
                   font-weight:700; display:inline-flex; align-items:center;
                   justify-content:center; padding:0 6px; }
.kds-col-actions { display:flex; gap:4px; opacity:.5; transition:.15s; }
.kds-col-wrap:hover .kds-col-actions { opacity:1; }
.col-act-btn     { width:26px; height:26px; border:none; border-radius:8px;
                   background:transparent; cursor:pointer; display:flex;
                   align-items:center; justify-content:center; font-size:.82rem;
                   transition:.12s; color:inherit; }
.col-act-btn:hover { background:rgba(0,0,0,.08); }

/* Color themes */
.color-green    .kds-col-head { background:linear-gradient(135deg,#f0fdf6,#e6f7ee); }
.color-green    .kds-col-title { color:#065f46; }
.color-green    .kds-count    { background:#d1fae5; color:#065f46; }
.color-green    .kds-card-strip { background:linear-gradient(90deg,#10b981,#34d399); }
.color-green    .kds-act-primary { background:#edfaf3; color:#198754; }
.color-green    .kds-act-primary:hover { background:#198754; color:#fff; }

.color-warning  .kds-col-head { background:linear-gradient(135deg,#fffbf0,#fff3d0); }
.color-warning  .kds-col-title { color:#92400e; }
.color-warning  .kds-count    { background:#fef3c7; color:#92400e; }
.color-warning  .kds-card-strip { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.color-warning  .kds-act-primary { background:#fff8e8; color:#9a6700; }
.color-warning  .kds-act-primary:hover { background:#f59e0b; color:#fff; }

.color-indigo   .kds-col-head { background:linear-gradient(135deg,#f0f0ff,#eae8ff); }
.color-indigo   .kds-col-title { color:#3730a3; }
.color-indigo   .kds-count    { background:#e0e7ff; color:#3730a3; }
.color-indigo   .kds-card-strip { background:linear-gradient(90deg,#6366f1,#818cf8); }
.color-indigo   .kds-act-primary { background:#eef0ff; color:#3730a3; }
.color-indigo   .kds-act-primary:hover { background:#6366f1; color:#fff; }

.color-blue     .kds-col-head { background:linear-gradient(135deg,#eff6ff,#dbeafe); }
.color-blue     .kds-col-title { color:#1e40af; }
.color-blue     .kds-count    { background:#dbeafe; color:#1e40af; }
.color-blue     .kds-card-strip { background:linear-gradient(90deg,#3b82f6,#60a5fa); }
.color-blue     .kds-act-primary { background:#eff6ff; color:#1e40af; }
.color-blue     .kds-act-primary:hover { background:#3b82f6; color:#fff; }

.color-pink     .kds-col-head { background:linear-gradient(135deg,#fdf2f8,#fce7f3); }
.color-pink     .kds-col-title { color:#9d174d; }
.color-pink     .kds-count    { background:#fce7f3; color:#9d174d; }
.color-pink     .kds-card-strip { background:linear-gradient(90deg,#ec4899,#f472b6); }
.color-pink     .kds-act-primary { background:#fdf2f8; color:#9d174d; }
.color-pink     .kds-act-primary:hover { background:#ec4899; color:#fff; }

.color-orange   .kds-col-head { background:linear-gradient(135deg,#fff7ed,#ffedd5); }
.color-orange   .kds-col-title { color:#9a3412; }
.color-orange   .kds-count    { background:#ffedd5; color:#9a3412; }
.color-orange   .kds-card-strip { background:linear-gradient(90deg,#f97316,#fb923c); }
.color-orange   .kds-act-primary { background:#fff7ed; color:#c2410c; }
.color-orange   .kds-act-primary:hover { background:#f97316; color:#fff; }

.color-red      .kds-col-head { background:linear-gradient(135deg,#fff1f2,#ffe4e6); }
.color-red      .kds-col-title { color:#9f1239; }
.color-red      .kds-count    { background:#ffe4e6; color:#9f1239; }
.color-red      .kds-card-strip { background:linear-gradient(90deg,#f43f5e,#fb7185); }
.color-red      .kds-act-primary { background:#fff1f2; color:#9f1239; }
.color-red      .kds-act-primary:hover { background:#f43f5e; color:#fff; }

/* Card body */
.kds-body        { padding:12px; display:flex; flex-direction:column; gap:10px;
                   min-height:180px; border-radius:0 0 22px 22px; }
.kds-empty       { display:flex; flex-direction:column; align-items:center;
                   justify-content:center; gap:6px; color:#5f6b64; min-height:140px;
                   font-size:.84rem; }
.kds-empty i     { font-size:1.8rem; opacity:.4; }

/* ── Order card ───────────────────────────────────────────────── */
.kds-card        { border-radius:14px; border:1px solid var(--line); background:#fff;
                   overflow:hidden; cursor:pointer; transition:.18s;
                   box-shadow:0 2px 8px rgba(0,0,0,.04); }
.kds-card:hover  { box-shadow:0 6px 20px rgba(0,0,0,.1); transform:translateY(-2px); }
.kds-card-strip  { height:4px; }
.kds-card-body   { padding:11px 13px 8px; }
.kds-order-no    { font-size:.76rem; font-weight:700; font-family:monospace; }
.kds-table-tag   { font-size:.76rem; font-weight:600; border-radius:7px; padding:2px 7px;
                   background:#f0fdf6; color:var(--primary);
                   border:1px solid rgba(25,135,84,.14); }
.kds-items-list  { list-style:none; padding:0; margin:7px 0 0; }
.kds-items-list li { display:flex; align-items:baseline; gap:5px; font-size:.83rem;
                     padding:3px 0; border-bottom:1px dotted rgba(20,40,20,.06); }
.kds-items-list li:last-child { border:none; }
.kds-qty         { font-weight:700; min-width:20px; color:var(--primary); }
.kds-item-note   { font-size:.71rem; color:#9a6700; margin-left:auto; white-space:nowrap; }
.kds-card-foot   { display:flex; align-items:center; justify-content:space-between;
                   padding:7px 12px; border-top:1px solid rgba(20,40,20,.05);
                   background:#fafcfa; gap:6px; }
.kds-timer       { font-size:.74rem; color:var(--muted); display:flex;
                   align-items:center; gap:3px; }
.kds-timer.urgent { color:#dc3545; font-weight:600; }
.kds-act         { font-size:.73rem; font-weight:600; padding:4px 9px; border-radius:8px;
                   border:none; cursor:pointer; transition:.12s;
                   display:inline-flex; align-items:center; gap:3px; }
.act-del         { width:26px; height:26px; padding:0; border:none; border-radius:8px;
                   background:#fff0f0; color:#dc3545; display:inline-flex;
                   align-items:center; justify-content:center; font-size:.82rem;
                   cursor:pointer; transition:.12s; flex-shrink:0; }
.act-del:hover   { background:#dc3545; color:#fff; }

/* Add column button */
.kds-add-col     { flex:0 0 220px; display:flex; align-items:center; justify-content:center; }
.btn-add-col     { display:flex; flex-direction:column; align-items:center; gap:8px;
                   width:100%; min-height:160px; border:2px dashed #ced4da;
                   border-radius:22px; background:transparent; cursor:pointer;
                   color:var(--muted); font-size:.85rem; font-weight:600;
                   transition:.18s; }
.btn-add-col i   { font-size:1.6rem; }
.btn-add-col:hover { border-color:var(--primary); color:var(--primary);
                     background:rgba(25,135,84,.04); }

/* Drag placeholder */
.drag-placeholder { border:2px dashed #ced4da; border-radius:14px;
                    background:rgba(25,135,84,.04); height:68px; }

/* Modal items */
.modal-item-row  { display:flex; align-items:center; gap:10px; padding:10px 0;
                   border-bottom:1px solid var(--line); }
.modal-item-row:last-child { border:none; }
.modal-item-qty  { width:30px; height:30px; border-radius:10px; background:#edfaf3;
                   color:var(--primary); font-weight:700; font-size:.85rem;
                   display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.modal-item-name { flex:1; font-weight:600; font-size:.9rem; }
.modal-item-note { font-size:.76rem; color:#9a6700; background:#fffbf0;
                   border-radius:6px; padding:2px 7px; }

/* Color picker */
.color-swatch    { width:28px; height:28px; border-radius:8px; cursor:pointer;
                   border:3px solid transparent; transition:.12s; display:inline-flex; }
.color-swatch.selected { border-color:#000; transform:scale(1.15); }
.col-colors      { display:flex; gap:8px; flex-wrap:wrap; }

/* Add item rows in new-order modal */
.item-row-new    { display:grid; grid-template-columns:1fr 60px 1fr auto;
                   gap:8px; align-items:center; }
</style>

{{-- ── Order Detail Modal ── --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i><span id="modalOrderNo"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3 mb-3 flex-wrap">
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Table</div>
                        <div class="fw-bold" id="modalTable"></div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Customer</div>
                        <div class="fw-bold" id="modalCustomer"></div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Column</div>
                        <div id="modalStatus"></div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Waiting</div>
                        <div class="fw-bold" id="modalWait"></div>
                    </div>
                </div>
                <div id="modalNotes" class="mb-3 p-3 rounded-3 d-none"
                     style="background:#fffbf0;border:1px solid rgba(245,158,11,.2);font-size:.86rem;">
                    <i class="bi bi-sticky me-1 text-warning"></i>
                    <span id="modalNotesText"></span>
                </div>
                <ul class="list-unstyled mb-0" id="modalItemsList"></ul>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex gap-2 flex-wrap" id="modalMoveActions">
                    <span class="text-muted" style="font-size:.82rem;align-self:center;">Move to:</span>
                </div>
                <button class="btn btn-outline-danger btn-sm" id="modalDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Add Column Modal ── --}}
<div class="modal fade" id="addColModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="colModalTitle">
                    <i class="bi bi-plus-square me-2"></i>New Column
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editColId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Column Name</label>
                    <input type="text" class="form-control" id="colName" placeholder="e.g. Plating, Delivery…" maxlength="60">
                </div>
                <div>
                    <label class="form-label fw-semibold d-block mb-2">Color</label>
                    <div class="col-colors">
                        @foreach([
                            'green'   => '#10b981',
                            'warning' => '#f59e0b',
                            'indigo'  => '#6366f1',
                            'blue'    => '#3b82f6',
                            'pink'    => '#ec4899',
                            'orange'  => '#f97316',
                            'red'     => '#f43f5e',
                        ] as $key => $hex)
                        <span class="color-swatch @if($key==='green') selected @endif"
                              data-color="{{ $key }}"
                              style="background:{{ $hex }};"
                              onclick="pickColor(this)"></span>
                        @endforeach
                    </div>
                    <input type="hidden" id="colColor" value="green">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveColBtn">
                    <i class="bi bi-check-lg me-1"></i>Save Column
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── New Order Modal ── --}}
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Kitchen Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Table <span class="text-danger">*</span></label>
                        <select class="form-select" id="newTable">
                            <option value="">Select table…</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->table_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Customer <small class="text-muted fw-normal">(optional)</small></label>
                        <input type="text" class="form-control" id="newCustomer" placeholder="Walk-in">
                    </div>
                </div>
                <label class="form-label fw-semibold">Items <span class="text-danger">*</span></label>
                <div id="newItemsWrap" class="d-flex flex-column gap-2 mb-2">
                    <div class="item-row-new">
                        <input type="text" class="form-control form-control-sm" placeholder="Item name…">
                        <input type="number" class="form-control form-control-sm" value="1" min="1" placeholder="Qty">
                        <input type="text" class="form-control form-control-sm" placeholder="Note (optional)">
                        <button type="button" class="act-del" onclick="this.closest('.item-row-new').remove()">&times;</button>
                    </div>
                </div>
                <button type="button" class="btn btn-soft btn-sm mb-3" onclick="addItemField()">
                    <i class="bi bi-plus-lg me-1"></i>Add Item
                </button>
                <div>
                    <label class="form-label fw-semibold">Notes <small class="text-muted fw-normal">(optional)</small></label>
                    <textarea class="form-control" id="newNotes" rows="2"
                              placeholder="Allergies, special requests…" style="resize:none;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNewOrder">
                    <i class="bi bi-send me-1"></i>Send to Kitchen
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Page header ── --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div class="page-title">
        <h2>Kitchen Display</h2>
        <p>Manage your preparation queue — add columns, drag orders, or click to act.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="text-muted" style="font-size:.8rem;">
            <i class="bi bi-arrow-repeat me-1"></i>Auto-refresh 30s
        </span>
        <button class="btn btn-soft btn-sm" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOrderModal">
            <i class="bi bi-plus-lg me-1"></i>New Order
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach($columns as $col)
    <div class="col-6 col-lg-3">
        <div class="card-soft" style="padding:14px 16px;">
            <div class="d-flex align-items-center gap-3">
                <div class="kds-count" style="width:40px;height:40px;border-radius:12px;font-size:1.2rem;flex-shrink:0;">
                    @if($col->color === 'warning') <i class="bi bi-hourglass-split" style="color:#9a6700;"></i>
                    @elseif($col->color === 'indigo') <i class="bi bi-fire" style="color:#6366f1;"></i>
                    @elseif($col->color === 'green') <i class="bi bi-check2-circle" style="color:#198754;"></i>
                    @elseif($col->color === 'blue') <i class="bi bi-truck" style="color:#3b82f6;"></i>
                    @elseif($col->color === 'pink') <i class="bi bi-heart" style="color:#ec4899;"></i>
                    @elseif($col->color === 'orange') <i class="bi bi-box" style="color:#f97316;"></i>
                    @else <i class="bi bi-list-ol" style="color:#f43f5e;"></i>
                    @endif
                </div>
                <div>
                    <div style="font-size:1.45rem;font-weight:800;line-height:1;">{{ $col->orders->count() }}</div>
                    <div style="font-size:.76rem;color:var(--muted);">{{ $col->name }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @if($columns->count() < 4)
    <div class="col-6 col-lg-3">
        <div class="card-soft" style="padding:14px 16px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:12px;background:#edfaf3;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                    <i class="bi bi-list-ol" style="color:var(--primary);"></i>
                </div>
                <div>
                    <div style="font-size:1.45rem;font-weight:800;line-height:1;">{{ $columns->sum(fn($c)=>$c->orders->count()) }}</div>
                    <div style="font-size:.76rem;color:var(--muted);">Total Active</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ── KDS Board ── --}}
<div class="kds-board" id="kds-board">

    @foreach($columns as $col)
    <div class="kds-col-wrap card-soft color-{{ $col->color }}"
         style="padding:0;"
         data-col-id="{{ $col->id }}"
         data-col-slug="{{ $col->slug }}">

        <div class="kds-col-head">
            <div class="kds-col-title">
                @if($col->color === 'warning') <i class="bi bi-hourglass-split"></i>
                @elseif($col->color === 'indigo') <i class="bi bi-fire"></i>
                @elseif($col->color === 'green') <i class="bi bi-check2-circle"></i>
                @elseif($col->color === 'blue') <i class="bi bi-truck"></i>
                @elseif($col->color === 'pink') <i class="bi bi-heart"></i>
                @elseif($col->color === 'orange') <i class="bi bi-box"></i>
                @else <i class="bi bi-flag"></i>
                @endif
                {{ $col->name }}
                <span class="kds-count">{{ $col->orders->count() }}</span>
            </div>
            <div class="kds-col-actions">
                <button class="col-act-btn" title="Rename column"
                        onclick="openEditCol({{ $col->id }},'{{ addslashes($col->name) }}','{{ $col->color }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="col-act-btn" title="Delete column"
                        onclick="deleteCol({{ $col->id }}, '{{ addslashes($col->name) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <div class="kds-body kds-column"
             data-col="{{ $col->slug }}"
             id="col-{{ $col->slug }}">
            @forelse($col->orders as $order)
                @include('admin.theme_one.kitchen._order_card', ['order' => $order, 'col' => $col])
            @empty
                <div class="kds-empty">
                    <i class="bi bi-inbox"></i>
                    <span>No orders here</span>
                </div>
            @endforelse
        </div>
    </div>
    @endforeach

    {{-- Add Column button --}}
    <div class="kds-add-col" style="align-self:stretch;">
        <button class="btn-add-col" data-bs-toggle="modal" data-bs-target="#addColModal"
                onclick="openAddCol()">
            <i class="bi bi-plus-square-dotted"></i>
            Add Column
        </button>
    </div>
</div>

@push('js')
<script>
const CSRF    = '{{ csrf_token() }}';
const COLUMNS = {!! json_encode($columns->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'slug'=>$c->slug,'color'=>$c->color])->values()) !!};

const _orderModal    = new bootstrap.Modal(document.getElementById('orderModal'));
const _addColModal   = new bootstrap.Modal(document.getElementById('addColModal'));
const _addOrderModal = new bootstrap.Modal(document.getElementById('addOrderModal'));

/* ════════════ Order detail modal ════════════ */
function openOrder(id) {
    const card = document.getElementById('kds-' + id);
    if (!card) return;
    const d = JSON.parse(card.dataset.order);

    document.getElementById('modalOrderNo').textContent  = d.order_no;
    document.getElementById('modalTable').textContent    = d.table_no;
    document.getElementById('modalCustomer').textContent = d.customer_name || 'Walk-in';
    document.getElementById('modalWait').textContent     = waitLabel(d.created_at);

    // Current column name
    const curCol = COLUMNS.find(c => c.slug === d.status);
    document.getElementById('modalStatus').innerHTML =
        `<span class="status-badge status-ready" style="font-size:.78rem;">${curCol ? curCol.name : d.status}</span>`;

    // Notes
    const notesWrap = document.getElementById('modalNotes');
    if (d.notes) {
        document.getElementById('modalNotesText').textContent = d.notes;
        notesWrap.classList.remove('d-none');
    } else {
        notesWrap.classList.add('d-none');
    }

    // Items
    document.getElementById('modalItemsList').innerHTML = d.items.map(item => `
        <div class="modal-item-row">
            <div class="modal-item-qty">${item.quantity}×</div>
            <div class="modal-item-name">${item.name}</div>
            ${item.note ? `<span class="modal-item-note">${item.note}</span>` : ''}
        </div>`).join('');

    // Move-to buttons (all columns except current)
    const moveWrap = document.getElementById('modalMoveActions');
    moveWrap.innerHTML = '<span class="text-muted me-1" style="font-size:.8rem;align-self:center;">Move to:</span>';
    COLUMNS.forEach(col => {
        if (col.slug === d.status) return;
        const btn = document.createElement('button');
        btn.className = 'btn btn-soft btn-sm';
        btn.innerHTML = col.name;
        btn.onclick   = () => setStatus(id, col.slug, btn);
        moveWrap.appendChild(btn);
    });
    // Served button
    const serveBtn = document.createElement('button');
    serveBtn.className = 'btn btn-success btn-sm';
    serveBtn.innerHTML = '<i class="bi bi-bag-check me-1"></i>Mark Served';
    serveBtn.onclick   = () => setStatus(id, 'served', serveBtn);
    moveWrap.appendChild(serveBtn);

    // Delete
    document.getElementById('modalDeleteBtn').onclick = () => deleteOrder(id);

    _orderModal.show();
}

/* ════════════ Set status ════════════ */
function setStatus(id, newStatus, btn) {
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    fetch(`/admin/kitchen/orders/${id}/status`, {
        method: 'PATCH',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({status: newStatus})
    })
    .then(r => r.json())
    .then(() => { _orderModal.hide(); location.reload(); })
    .catch(() => { btn.disabled = false; btn.innerHTML = orig; });
}

/* ════════════ Inline card action ════════════ */
function cardAction(e, id, status) {
    e.stopPropagation();
    fetch(`/admin/kitchen/orders/${id}/status`, {
        method: 'PATCH',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({status})
    }).then(() => location.reload());
}

/* ════════════ Delete order ════════════ */
function deleteOrder(id) {
    if (!confirm('Delete this order?')) return;
    fetch(`/admin/kitchen/orders/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
    }).then(() => { _orderModal.hide(); location.reload(); });
}

/* ════════════ New order ════════════ */
function addItemField() {
    const wrap = document.getElementById('newItemsWrap');
    const row  = document.createElement('div');
    row.className = 'item-row-new';
    row.innerHTML = `
        <input type="text" class="form-control form-control-sm" placeholder="Item name…">
        <input type="number" class="form-control form-control-sm" value="1" min="1">
        <input type="text" class="form-control form-control-sm" placeholder="Note (optional)">
        <button type="button" class="act-del" onclick="this.closest('.item-row-new').remove()">&times;</button>`;
    wrap.appendChild(row);
}

document.getElementById('saveNewOrder').addEventListener('click', async () => {
    const tableId = document.getElementById('newTable').value;
    if (!tableId) { document.getElementById('newTable').focus(); return; }
    const items = [];
    document.querySelectorAll('#newItemsWrap .item-row-new').forEach(row => {
        const inp = row.querySelectorAll('input');
        const name = inp[0].value.trim();
        if (name) items.push({name, qty: parseInt(inp[1].value)||1, note: inp[2].value.trim()||null});
    });
    if (!items.length) { alert('Add at least one item.'); return; }

    const btn = document.getElementById('saveNewOrder');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending…';

    const res = await fetch('/admin/kitchen/orders', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            table_id:      tableId,
            customer_name: document.getElementById('newCustomer').value.trim()||null,
            notes:         document.getElementById('newNotes').value.trim()||null,
            items
        })
    });
    if (res.ok) { _addOrderModal.hide(); location.reload(); }
    else { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send me-1"></i>Send to Kitchen'; }
});

/* ════════════ Column management ════════════ */
function pickColor(el) {
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('colColor').value = el.dataset.color;
}

function openAddCol() {
    document.getElementById('colModalTitle').innerHTML = '<i class="bi bi-plus-square me-2"></i>New Column';
    document.getElementById('editColId').value = '';
    document.getElementById('colName').value   = '';
    document.getElementById('colColor').value  = 'green';
    document.querySelectorAll('.color-swatch').forEach(s =>
        s.classList.toggle('selected', s.dataset.color === 'green'));
    document.getElementById('saveColBtn').innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Column';
}

function openEditCol(id, name, color) {
    document.getElementById('colModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Column';
    document.getElementById('editColId').value = id;
    document.getElementById('colName').value   = name;
    document.getElementById('colColor').value  = color;
    document.querySelectorAll('.color-swatch').forEach(s =>
        s.classList.toggle('selected', s.dataset.color === color));
    document.getElementById('saveColBtn').innerHTML = '<i class="bi bi-check-lg me-1"></i>Update Column';
    _addColModal.show();
}

document.getElementById('saveColBtn').addEventListener('click', async () => {
    const name  = document.getElementById('colName').value.trim();
    const color = document.getElementById('colColor').value;
    const id    = document.getElementById('editColId').value;
    if (!name) { document.getElementById('colName').focus(); return; }

    const btn = document.getElementById('saveColBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const url    = id ? `/admin/kitchen/columns/${id}` : '/admin/kitchen/columns';
    const method = id ? 'PUT' : 'POST';
    const res    = await fetch(url, {
        method,
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({name, color})
    });
    if (res.ok) { _addColModal.hide(); location.reload(); }
    else { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Column'; }
});

function deleteCol(id, name) {
    if (!confirm(`Delete column "${name}"? Orders inside will move to the first column.`)) return;
    fetch(`/admin/kitchen/columns/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
    }).then(() => location.reload());
}

/* ════════════ Drag-and-drop ════════════ */
$(function () {
    $('.kds-column').sortable({
        connectWith: '.kds-column',
        placeholder: 'drag-placeholder',
        cursor: 'move',
        cancel: 'button,a',
        receive: function (event, ui) {
            const newCol = $(this).data('col');
            const id     = ui.item.data('id');
            fetch(`/admin/kitchen/orders/${id}/status`, {
                method: 'PATCH',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                body: JSON.stringify({status: newCol})
            }).then(() => location.reload());
        }
    }).disableSelection();
});

/* ════════════ Live wait timers ════════════ */
function waitLabel(ts) {
    const mins = Math.floor((Date.now() - new Date(ts)) / 60000);
    if (mins < 1) return 'Just now';
    if (mins < 60) return mins + ' min ago';
    return Math.floor(mins/60) + 'h ' + (mins%60) + 'm ago';
}
function updateTimers() {
    document.querySelectorAll('.kds-timer[data-ts]').forEach(el => {
        const mins = Math.floor((Date.now() - new Date(el.dataset.ts)) / 60000);
        el.querySelector('.timer-val').textContent = mins < 1 ? 'Just now' : mins + ' min';
        el.classList.toggle('urgent', mins >= 15);
    });
}
updateTimers();
setInterval(updateTimers, 30000);
setTimeout(() => location.reload(), 30000);
</script>
@endpush
@endsection
