@extends('admin.theme_one.component.layout')

@section('content')
<style>
/* ── Order type badges ────────────────────────────────────────── */
.type-dine_in   { background:color-mix(in srgb, var(--primary) 12%, white); color:color-mix(in srgb, var(--primary) 65%, black); }
.type-takeaway  { background:#fff8e8; color:#92400e; }
.type-delivery  { background:#eff6ff; color:#1e40af; }
.type-badge     { display:inline-flex; align-items:center; gap:4px; padding:3px 10px;
                  border-radius:8px; font-size:.78rem; font-weight:600; }

/* ── Filter tabs ──────────────────────────────────────────────── */
.filter-tabs    { display:flex; gap:6px; flex-wrap:wrap; }
.ftab           { padding:6px 14px; border-radius:10px; border:1px solid var(--line);
                  font-size:.82rem; font-weight:600; cursor:pointer;
                  background:#f8f9fa; color:var(--muted); transition:.15s;
                  text-decoration:none; }
.ftab:hover     { background:#e9ecef; color:var(--text); }
.ftab.active    { background:var(--primary); color:#fff; border-color:var(--primary); }

/* ── Status filters ───────────────────────────────────────────── */
.sfab           { padding:5px 12px; border-radius:999px; font-size:.78rem; font-weight:600;
                  cursor:pointer; border:1px solid var(--line); background:#f8f9fa;
                  color:var(--muted); transition:.15s; text-decoration:none; }
.sfab:hover     { background:#e9ecef; }
.sfab.active    { border-color:currentColor; }
.sfab-all.active       { background:#1f2937; color:#fff; border-color:#1f2937; }
.sfab-pending.active   { background:#fff8e8; color:#9a6700; border-color:#f59e0b; }
.sfab-preparing.active { background:#eef0ff; color:#3730a3; border-color:#6366f1; }
.sfab-ready.active     { background:color-mix(in srgb, var(--primary) 10%, white); color:#065f46; border-color:#10b981; }
.sfab-served.active    { background:color-mix(in srgb, var(--primary) 10%, white); color:var(--primary); border-color:var(--primary); }
.sfab-cancelled.active { background:#fde7ea; color:#b42318; border-color:#dc3545; }

/* ── Action icon buttons ──────────────────────────────────────── */
.tbl-action     { width:30px; height:30px; border:none; border-radius:9px;
                  font-size:.82rem; cursor:pointer; display:inline-flex;
                  align-items:center; justify-content:center; transition:.12s; }
.tbl-action.view   { background:color-mix(in srgb, var(--primary) 9%, white); color:var(--primary); }
.tbl-action.view:hover { background:var(--primary); color:#fff; }
.tbl-action.del    { background:#fff0f0; color:#dc3545; }
.tbl-action.del:hover  { background:#dc3545; color:#fff; }

/* ── Order detail modal ───────────────────────────────────────── */
.detail-item-row { display:flex; align-items:center; gap:10px; padding:10px 0;
                   border-bottom:1px solid var(--line); }
.detail-item-row:last-child { border:none; }
.detail-qty      { width:30px; height:30px; border-radius:10px; background:color-mix(in srgb, var(--primary) 10%, white);
                   color:var(--primary); font-weight:700; font-size:.85rem;
                   display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.detail-name     { flex:1; font-weight:600; font-size:.9rem; }
.detail-price    { font-size:.85rem; color:var(--muted); white-space:nowrap; }
.detail-total    { font-weight:700; font-size:.9rem; min-width:54px; text-align:right; }
.detail-note     { font-size:.75rem; color:#9a6700; background:#fffbf0;
                   border-radius:6px; padding:2px 6px; }

/* ── Add order modal items ────────────────────────────────────── */
.item-row-new   { display:grid; grid-template-columns:1fr 56px 80px 1fr auto;
                  gap:8px; align-items:center; }
.act-del        { width:28px; height:28px; padding:0; border:none; border-radius:8px;
                  background:#fff0f0; color:#dc3545; display:inline-flex;
                  align-items:center; justify-content:center; font-size:.85rem;
                  cursor:pointer; transition:.12s; flex-shrink:0; }
.act-del:hover  { background:#dc3545; color:#fff; }

/* ── Status select in modal ───────────────────────────────────── */
.status-select  { border-radius:10px; padding:.5rem .8rem; font-size:.88rem;
                  font-weight:600; border:1px solid var(--line); }
</style>

{{-- ── Order Detail Modal ── --}}
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>
                    <span id="detailOrderNo"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Meta --}}
                <div class="d-flex gap-3 flex-wrap mb-3">
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Type</div>
                        <div id="detailType"></div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Table / Source</div>
                        <div class="fw-bold" id="detailTable"></div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Customer</div>
                        <div class="fw-bold" id="detailCustomer"></div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Placed</div>
                        <div class="fw-bold" id="detailTime"></div>
                    </div>
                </div>

                {{-- Notes --}}
                <div id="detailNotes" class="mb-3 p-3 rounded-3 d-none"
                     style="background:#fffbf0;border:1px solid rgba(245,158,11,.2);font-size:.86rem;">
                    <i class="bi bi-sticky me-1 text-warning"></i><span id="detailNotesText"></span>
                </div>

                {{-- Items --}}
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-semibold" style="font-size:.88rem;">Items</span>
                    <span class="text-muted" style="font-size:.78rem;">Qty &nbsp;&nbsp; Unit &nbsp;&nbsp; Total</span>
                </div>
                <div id="detailItemsList"></div>

                {{-- Totals --}}
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.88rem;">
                        <span class="text-muted">Subtotal</span>
                        <span id="detailSubtotal" class="fw-semibold"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:.88rem;">
                        <span class="text-muted">VAT (10%)</span>
                        <span id="detailVat" class="fw-semibold"></span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:1rem;">
                        <span class="fw-bold">Grand Total</span>
                        <span id="detailTotal" class="fw-bold text-brand" style="font-size:1.1rem;"></span>
                    </div>
                </div>

                {{-- Status change --}}
                <div class="mt-3 pt-3 border-top">
                    <label class="form-label fw-semibold mb-2">Update Status</label>
                    <div class="d-flex gap-2 flex-wrap" id="detailStatusBtns"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button class="btn btn-outline-danger btn-sm" id="detailDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete Order
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Add Order Modal ── --}}
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Order Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="newType" onchange="toggleTableLabel()">
                            <option value="dine_in">🍽 Dine In</option>
                            <option value="takeaway">🥡 Takeaway</option>
                            <option value="delivery">🚚 Delivery</option>
                        </select>
                    </div>
                    <div class="col-sm-4" id="tableField">
                        <label class="form-label fw-semibold" id="tableLbl">Table <span class="text-danger">*</span></label>
                        <select class="form-select" id="newTable">
                            <option value="">Select table…</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->table_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Customer <small class="text-muted fw-normal">(optional)</small></label>
                        <input type="text" class="form-control" id="newCustomer" placeholder="Walk-in">
                    </div>
                </div>

                <label class="form-label fw-semibold">Items <span class="text-danger">*</span></label>
                <div id="newItemsWrap" class="d-flex flex-column gap-2 mb-2">
                    <div class="item-row-new">
                        <input type="text"   class="form-control form-control-sm" placeholder="Item name…">
                        <input type="number" class="form-control form-control-sm" value="1" min="1" placeholder="Qty">
                        <input type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price $">
                        <input type="text"   class="form-control form-control-sm" placeholder="Note (opt.)">
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
            <div class="modal-footer justify-content-between align-items-center">
                <div class="text-muted" style="font-size:.82rem;">
                    Total: <strong id="newOrderTotal">$0.00</strong>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNewOrder">
                        <i class="bi bi-send me-1"></i>Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Page ── --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div class="page-title">
        <h2>Orders</h2>
        <p>Track dine-in, takeaway, and delivery orders.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
        <i class="bi bi-plus-lg me-1"></i>New Order
    </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card-soft" style="padding:18px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:color-mix(in srgb, var(--primary) 12%, white);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div style="font-size:1.55rem;font-weight:800;line-height:1.1;">{{ $stats['today'] }}</div>
                    <div style="font-size:.76rem;color:var(--muted);">Today's Orders</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-soft" style="padding:18px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:color-mix(in srgb, color-mix(in srgb, var(--primary) 80%, #f59e0b) 16%, white);color:color-mix(in srgb, var(--primary) 80%, #f59e0b);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div style="font-size:1.55rem;font-weight:800;line-height:1.1;">{{ $stats['pending'] }}</div>
                    <div style="font-size:.76rem;color:var(--muted);">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-soft" style="padding:18px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:color-mix(in srgb, var(--primary) 16%, white);color:color-mix(in srgb, var(--primary) 60%, black);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                    <i class="bi bi-fire"></i>
                </div>
                <div>
                    <div style="font-size:1.55rem;font-weight:800;line-height:1.1;">{{ $stats['preparing'] }}</div>
                    <div style="font-size:.76rem;color:var(--muted);">Preparing</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card-soft" style="padding:18px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:color-mix(in srgb, var(--primary) 12%, white);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div style="font-size:1.55rem;font-weight:800;line-height:1.1;">${{ number_format($stats['revenue'], 2) }}</div>
                    <div style="font-size:.76rem;color:var(--muted);">Today's Revenue</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Orders table --}}
<div class="card-soft table-card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h5 class="mb-0">All Orders</h5>
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.order.index') }}"
                  class="d-flex gap-2 align-items-center flex-wrap" id="filterForm">
                <input type="hidden" name="type"   value="{{ request('type','all') }}">
                <input type="hidden" name="status" value="{{ request('status','all') }}">
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.82rem;"></i>
                    <input class="form-control form-control-sm" name="search"
                           value="{{ request('search') }}"
                           placeholder="Search order, table, customer…"
                           style="padding-left:32px;border-radius:10px;width:220px;"
                           oninput="debounceSubmit()">
                </div>
                <button class="btn btn-soft btn-sm" type="submit">Search</button>
                @if(request()->hasAny(['search','type','status']))
                <a href="{{ route('admin.order.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>

        {{-- Type filter tabs --}}
        <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">
            <div class="filter-tabs">
                @foreach(['all'=>'All Types','dine_in'=>'Dine In','takeaway'=>'Takeaway','delivery'=>'Delivery'] as $val=>$lbl)
                <a href="#" class="ftab {{ request('type','all') === $val ? 'active' : '' }}"
                   onclick="setFilter('type','{{ $val }}')">
                    @if($val==='dine_in') <i class="bi bi-table me-1"></i>
                    @elseif($val==='takeaway') <i class="bi bi-bag me-1"></i>
                    @elseif($val==='delivery') <i class="bi bi-truck me-1"></i>
                    @endif
                    {{ $lbl }}
                </a>
                @endforeach
            </div>
            <div class="vr"></div>
            <div class="d-flex gap-2 flex-wrap">
                @foreach([
                    'all'       => ['All',       'sfab-all'],
                    'pending'   => ['Pending',   'sfab-pending'],
                    'preparing' => ['Preparing', 'sfab-preparing'],
                    'ready'     => ['Ready',     'sfab-ready'],
                    'served'    => ['Served',    'sfab-served'],
                    'cancelled' => ['Cancelled', 'sfab-cancelled'],
                ] as $val => [$lbl, $cls])
                <a href="#" class="sfab {{ $cls }} {{ request('status','all') === $val ? 'active' : '' }}"
                   onclick="setFilter('status','{{ $val }}')">{{ $lbl }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order No</th>
                        <th>Type</th>
                        <th>Table / Source</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-weight:700;color:var(--primary);font-size:.85rem;">
                                {{ $order->order_no }}
                            </span>
                        </td>
                        <td>
                            <span class="type-badge type-{{ $order->order_type }}">
                                @if($order->order_type === 'dine_in')   <i class="bi bi-table"></i>     Dine In
                                @elseif($order->order_type === 'takeaway') <i class="bi bi-bag"></i>    Takeaway
                                @elseif($order->order_type === 'delivery') <i class="bi bi-truck"></i>  Delivery
                                @endif
                            </span>
                        </td>
                        <td class="fw-semibold">{{ $order->table->table_no ?? ucfirst(str_replace('_',' ',$order->order_type)) }}</td>
                        <td class="text-muted" style="font-size:.9rem;">{{ $order->customer_name ?? 'Walk-in' }}</td>
                        <td>
                            <span class="text-muted" style="font-size:.85rem;">
                                {{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td class="fw-bold">${{ number_format($order->grand_total, 2) }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'pending'   => ['status-pending',    'Pending'],
                                    'preparing' => ['status-progress',   'Preparing'],
                                    'ready'     => ['status-ready',      'Ready'],
                                    'served'    => ['status-ready',      'Served'],
                                    'cancelled' => ['status-cancelled',  'Cancelled'],
                                ];
                                [$cls, $lbl] = $statusMap[$order->status] ?? ['status-pending', ucfirst($order->status)];
                            @endphp
                            <span class="status-badge {{ $cls }}">{{ $lbl }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                            {{ $order->created_at->format('h:i A') }}<br>
                            <span style="font-size:.74rem;">{{ $order->created_at->format('d M') }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="tbl-action view" title="View details"
                                        onclick="openOrder({{ $order->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="tbl-action del" title="Delete"
                                        onclick="deleteOrder({{ $order->id }}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4;"></i>
                            No orders found.
                            @if(request()->hasAny(['search','type','status']))
                            <a href="{{ route('admin.order.index') }}">Clear filters</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
    <div class="card-header border-top-0 d-flex align-items-center justify-content-between"
         style="border-top:1px solid var(--line) !important;">
        <span class="text-muted" style="font-size:.82rem;">
            Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
        </span>
        <div class="d-flex gap-1">
            @if($orders->onFirstPage())
            <button class="btn btn-soft btn-sm" disabled><i class="bi bi-chevron-left"></i></button>
            @else
            <a href="{{ $orders->previousPageUrl() }}" class="btn btn-soft btn-sm"><i class="bi bi-chevron-left"></i></a>
            @endif

            @foreach($orders->getUrlRange(max(1,$orders->currentPage()-2), min($orders->lastPage(),$orders->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="btn btn-sm {{ $page == $orders->currentPage() ? 'btn-primary' : 'btn-soft' }}"
               style="min-width:34px;">{{ $page }}</a>
            @endforeach

            @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}" class="btn btn-soft btn-sm"><i class="bi bi-chevron-right"></i></a>
            @else
            <button class="btn btn-soft btn-sm" disabled><i class="bi bi-chevron-right"></i></button>
            @endif
        </div>
    </div>
    @endif
</div>

@push('js')
<script>
const CSRF = '{{ csrf_token() }}';
const _detailModal   = new bootstrap.Modal(document.getElementById('orderDetailModal'));
const _addOrderModal = new bootstrap.Modal(document.getElementById('addOrderModal'));

const STATUS_MAP = {
    pending:   { cls: 'status-pending',   label: 'Pending'   },
    preparing: { cls: 'status-progress',  label: 'Preparing' },
    ready:     { cls: 'status-ready',     label: 'Ready'     },
    served:    { cls: 'status-ready',     label: 'Served'    },
    cancelled: { cls: 'status-cancelled', label: 'Cancelled' },
};

const TYPE_MAP = {
    dine_in:  { icon: 'bi-table',  label: 'Dine In',  cls: 'type-dine_in'  },
    takeaway: { icon: 'bi-bag',    label: 'Takeaway', cls: 'type-takeaway' },
    delivery: { icon: 'bi-truck',  label: 'Delivery', cls: 'type-delivery' },
};

/* ══════════ Order detail modal ══════════ */
function openOrder(id) {
    fetch(`/admin/orders/${id}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
    .then(r => r.json())
    .then(o => {
        document.getElementById('detailOrderNo').textContent  = o.order_no;
        document.getElementById('detailTable').textContent    = o.table ? o.table.table_no : (TYPE_MAP[o.order_type]?.label || o.order_type);
        document.getElementById('detailCustomer').textContent = o.customer_name || 'Walk-in';
        document.getElementById('detailTime').textContent     = new Date(o.created_at).toLocaleString();

        const type = TYPE_MAP[o.order_type] || {icon:'bi-receipt', label:o.order_type, cls:''};
        document.getElementById('detailType').innerHTML =
            `<span class="type-badge ${type.cls}"><i class="bi ${type.icon}"></i> ${type.label}</span>`;

        const notesWrap = document.getElementById('detailNotes');
        if (o.notes) {
            document.getElementById('detailNotesText').textContent = o.notes;
            notesWrap.classList.remove('d-none');
        } else {
            notesWrap.classList.add('d-none');
        }

        // Items
        let subtotal = 0;
        document.getElementById('detailItemsList').innerHTML = o.items.map(item => {
            const rowTotal = item.quantity * item.price;
            subtotal += rowTotal;
            return `<div class="detail-item-row">
                <div class="detail-qty">${item.quantity}×</div>
                <div class="detail-name">${item.name}${item.note ? ` <span class="detail-note">${item.note}</span>` : ''}</div>
                <div class="detail-price">$${parseFloat(item.price).toFixed(2)}</div>
                <div class="detail-total">$${rowTotal.toFixed(2)}</div>
            </div>`;
        }).join('');

        const vat   = subtotal * 0.10;
        const grand = subtotal + vat;
        document.getElementById('detailSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('detailVat').textContent      = '$' + vat.toFixed(2);
        document.getElementById('detailTotal').textContent    = '$' + grand.toFixed(2);

        // Status buttons
        const btnWrap = document.getElementById('detailStatusBtns');
        btnWrap.innerHTML = '';
        const statusFlow = ['pending','preparing','ready','served','cancelled'];
        statusFlow.forEach(s => {
            if (s === o.status) return;
            const m   = STATUS_MAP[s] || {};
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-soft';
            btn.textContent = m.label || s;
            btn.onclick = () => updateStatus(id, s, btn);
            btnWrap.appendChild(btn);
        });

        // Delete
        document.getElementById('detailDeleteBtn').onclick = () => deleteOrder(id);

        _detailModal.show();
    });
}

function updateStatus(id, status, btn) {
    const orig = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    fetch(`/admin/orders/${id}/status`, {
        method: 'PATCH',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({status})
    }).then(r => r.json()).then(() => { _detailModal.hide(); location.reload(); })
      .catch(() => { btn.disabled = false; btn.textContent = orig; });
}

function deleteOrder(id) {
    if (!confirm('Delete this order? This cannot be undone.')) return;
    fetch(`/admin/orders/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
    }).then(() => { _detailModal.hide(); location.reload(); });
}

/* ══════════ Add order modal ══════════ */
function toggleTableLabel() {
    const type = document.getElementById('newType').value;
    const field = document.getElementById('tableField');
    if (type === 'dine_in') {
        field.classList.remove('d-none');
        document.getElementById('newTable').required = true;
    } else {
        field.classList.add('d-none');
        document.getElementById('newTable').value = '';
        document.getElementById('newTable').required = false;
    }
}
toggleTableLabel();

function addItemField() {
    const wrap = document.getElementById('newItemsWrap');
    const row  = document.createElement('div');
    row.className = 'item-row-new';
    row.innerHTML = `
        <input type="text"   class="form-control form-control-sm" placeholder="Item name…"   oninput="calcNewTotal()">
        <input type="number" class="form-control form-control-sm" value="1" min="1"           oninput="calcNewTotal()">
        <input type="number" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price $" oninput="calcNewTotal()">
        <input type="text"   class="form-control form-control-sm" placeholder="Note (opt.)">
        <button type="button" class="act-del" onclick="this.closest('.item-row-new').remove();calcNewTotal()">&times;</button>`;
    wrap.appendChild(row);
}

function calcNewTotal() {
    let total = 0;
    document.querySelectorAll('#newItemsWrap .item-row-new').forEach(row => {
        const inp = row.querySelectorAll('input');
        total += (parseFloat(inp[2]?.value)||0) * (parseInt(inp[1]?.value)||1);
    });
    document.getElementById('newOrderTotal').textContent = '$' + total.toFixed(2);
}

// wire up first row inputs for live total
document.querySelectorAll('#newItemsWrap input[type=number]').forEach(el => el.addEventListener('input', calcNewTotal));

document.getElementById('saveNewOrder').addEventListener('click', async () => {
    const type = document.getElementById('newType').value;
    const tableId = document.getElementById('newTable').value;
    if (type === 'dine_in' && !tableId) { document.getElementById('newTable').focus(); return; }

    const items = [];
    document.querySelectorAll('#newItemsWrap .item-row-new').forEach(row => {
        const inp = row.querySelectorAll('input');
        const name = inp[0].value.trim();
        if (name) items.push({
            name,
            qty:   parseInt(inp[1].value) || 1,
            price: parseFloat(inp[2].value) || 0,
            note:  inp[3].value.trim() || null,
        });
    });
    if (!items.length) { notify.error('Add at least one item.'); return; }

    const btn = document.getElementById('saveNewOrder');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Placing…';

    const res = await fetch('/admin/orders', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            order_type:    type,
            table_id:      type === 'dine_in' ? tableId : null,
            customer_name: document.getElementById('newCustomer').value.trim() || null,
            notes:         document.getElementById('newNotes').value.trim() || null,
            items,
        })
    });
    if (res.ok) { _addOrderModal.hide(); location.reload(); }
    else {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i>Place Order';
        notify.error('Something went wrong. Please try again.');
    }
});

/* ══════════ Filters ══════════ */
function setFilter(key, val) {
    const form = document.getElementById('filterForm');
    form.querySelector(`input[name="${key}"]`).value = val;
    form.submit();
}

let _debounceTimer;
function debounceSubmit() {
    clearTimeout(_debounceTimer);
    _debounceTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
}
</script>
@endpush
@endsection
