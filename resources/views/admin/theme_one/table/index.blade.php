@extends('admin.theme_one.component.layout')

@section('content')
<style>
/* ── Page tabs ─────────────────────────────────────────────────── */
.page-tab-bar   { display:flex; gap:6px; margin-bottom:20px; }
.page-tab       { padding:8px 20px; border-radius:12px; border:1px solid var(--line);
                  font-size:.88rem; font-weight:600; cursor:pointer; background:#f8f9fa;
                  color:var(--muted); transition:.15s; }
.page-tab.active { background:var(--primary); color:#fff; border-color:var(--primary); }
.page-tab:hover:not(.active) { background:#e9ecef; }

/* ── Table status cards ────────────────────────────────────────── */
.tbl-card       { border-radius:18px; overflow:hidden; transition:.18s; }
.tbl-card:hover { box-shadow:0 8px 24px rgba(0,0,0,.1); transform:translateY(-2px); }
.tbl-card-head  { padding:14px 16px 10px; display:flex; align-items:center;
                  justify-content:space-between; }
.tbl-name       { font-weight:800; font-size:1.05rem; }
.tbl-room-tag   { font-size:.72rem; color:var(--muted); font-weight:500; margin-top:1px; }
.tbl-card-body  { padding:0 16px 12px; }
.tbl-stat-row   { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.tbl-seats-ico  { display:flex; gap:3px; flex-wrap:wrap; max-width:90px; }
.seat-dot       { width:9px; height:9px; border-radius:50%; background:#adb5bd; flex-shrink:0; }
.tbl-card-foot  { padding:10px 16px; border-top:1px solid var(--line);
                  display:flex; align-items:center; justify-content:space-between; gap:8px; }
.tbl-booking-count { font-size:.75rem; font-weight:600; color:var(--muted);
                     display:flex; align-items:center; gap:4px; }

/* Status colors on card left border */
.status-available .tbl-card-head { border-left:4px solid var(--primary); }
.status-occupied  .tbl-card-head { border-left:4px solid #ef4444; }
.status-reserved  .tbl-card-head { border-left:4px solid #f59e0b; }
.status-cleaning  .tbl-card-head { border-left:4px solid #94a3b8; }

/* Status badge pills */
.st-badge       { display:inline-flex; align-items:center; gap:4px; padding:3px 10px;
                  border-radius:999px; font-size:.74rem; font-weight:700; }
.st-available   { background:color-mix(in srgb, var(--primary) 14%, white); color:color-mix(in srgb, var(--primary) 70%, black); }
.st-occupied    { background:#fee2e2; color:#991b1b; }
.st-reserved    { background:#fef3c7; color:#92400e; }
.st-cleaning    { background:#f1f5f9; color:#475569; }

/* Status select */
.status-sel     { border-radius:10px; padding:4px 8px; font-size:.78rem; font-weight:600;
                  border:1px solid var(--line); background:#fff; cursor:pointer; }

/* Room filter tabs */
.room-filter    { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px; }
.rm-ftab        { padding:5px 14px; border-radius:10px; border:1px solid var(--line);
                  font-size:.8rem; font-weight:600; cursor:pointer; background:#f8f9fa;
                  color:var(--muted); transition:.15s; }
.rm-ftab.active { background:var(--primary); color:#fff; border-color:var(--primary); }
.rm-ftab:hover:not(.active) { background:#e9ecef; }

/* Booking table actions */
.tbl-action { width:28px; height:28px; border:none; border-radius:8px; font-size:.8rem;
              cursor:pointer; display:inline-flex; align-items:center;
              justify-content:center; transition:.12s; }
.tbl-action.confirm { background:color-mix(in srgb, var(--primary) 10%, white); color:var(--primary); }
.tbl-action.confirm:hover { background:var(--primary); color:#fff; }
.tbl-action.cancel  { background:#fff8e8; color:#9a6700; }
.tbl-action.cancel:hover  { background:#f59e0b; color:#fff; }
.tbl-action.del     { background:#fff0f0; color:#dc3545; }
.tbl-action.del:hover     { background:#dc3545; color:#fff; }

/* ── Floor-plan (existing) ─────────────────────────────────────── */
.fp-page        { display:flex; flex-direction:column; }
.fp-toolbar     { display:flex; align-items:center; gap:12px; padding:14px 18px;
                  background:#fff; border-radius:16px; margin-bottom:14px;
                  border:1px solid rgba(20,40,20,.08); flex-wrap:wrap; }
.tool-item      { display:flex; flex-direction:column; align-items:center; gap:4px;
                  padding:10px 18px; border-radius:12px; cursor:grab;
                  user-select:none; font-size:11px; font-weight:600;
                  border:2px dashed; transition:.15s; }
.tool-item:hover { opacity:.85; }
.tool-item i    { font-size:22px; }
.tool-table     { color:#7c4a1e; border-color:#7c4a1e; background:#fdf5ee; }
.tool-chair     { color:var(--primary); border-color:var(--primary); background:color-mix(in srgb, var(--primary) 10%, white); }
.room-tabs      { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.room-tab       { padding:7px 14px; border-radius:10px; border:1px solid #dee2e6;
                  cursor:pointer; font-size:13px; font-weight:500; background:#f8f9fa;
                  display:flex; align-items:center; gap:6px; transition:.15s; }
.room-tab.active{ background:var(--primary); color:#fff; border-color:var(--primary); }
.room-tab:not(.active):hover { background:#e9ecef; }
.rm-x           { font-size:14px; line-height:1; opacity:.55; cursor:pointer; margin-left:2px; }
.rm-x:hover     { opacity:1; color:#dc3545; }
.btn-add-room   { padding:7px 14px; border-radius:10px; border:2px dashed var(--primary);
                  color:var(--primary); font-size:13px; font-weight:600; background:transparent;
                  cursor:pointer; transition:.15s; }
.btn-add-room:hover { background:color-mix(in srgb, var(--primary) 10%, white); }
.fp-canvas      { position:relative; min-height:560px; border-radius:16px;
                  background:#f4f6f8;
                  background-image: radial-gradient(circle, #c8cdd2 1px, transparent 1px);
                  background-size:28px 28px;
                  border:2px dashed #ced4da; overflow:hidden; transition:.2s; }
.fp-canvas.drop-over { border-color:var(--primary); background-color:color-mix(in srgb, var(--primary) 6%, white); }
.fp-canvas.d-none    { display:none !important; }
.fp-empty       { position:absolute; inset:0; display:flex; flex-direction:column;
                  align-items:center; justify-content:center; color:#5f6b64;
                  pointer-events:none; gap:6px; }
.fp-empty i     { font-size:40px; }
.floor-table    { position:absolute; cursor:move; user-select:none; z-index:10; }
.ft-wrap        { display:flex; flex-direction:column; align-items:center; gap:2px; }
.ft-mid         { display:flex; align-items:center; gap:2px; }
.ft-body        { width:88px; height:58px; border-radius:14px; position:relative;
                  display:flex; flex-direction:column; align-items:center;
                  justify-content:center; gap:2px;
                  background:repeating-linear-gradient(100deg,#8B5E3C 0 6px,#7e5535 6px 12px),
                              linear-gradient(135deg,#8B5E3C,#6c4a24);
                  color:#fff; border:2px solid #5a3920;
                  box-shadow:inset 0 1px 4px rgba(255,255,255,.25),
                             inset 0 -3px 6px rgba(0,0,0,.25),
                             0 4px 10px rgba(0,0,0,.22); }
.ft-name        { font-size:11px; font-weight:700; letter-spacing:.03em; }
.ft-seats       { font-size:10px; opacity:.8; }
.ft-del         { position:absolute; top:-9px; right:-9px; width:20px; height:20px;
                  background:#dc3545; color:#fff; border:none; border-radius:50%;
                  font-size:12px; font-weight:700; cursor:pointer; line-height:1;
                  display:flex; align-items:center; justify-content:center; z-index:20; }
.ft-del:hover   { background:#b02a37; }
.ft-edit        { position:absolute; bottom:-9px; left:-9px; width:20px; height:20px;
                  background:var(--primary); color:#fff; border:none; border-radius:50%;
                  font-size:9px; cursor:pointer; line-height:1;
                  display:flex; align-items:center; justify-content:center; z-index:20; }
.ft-edit:hover  { background:#157347; }
.rm-gear        { font-size:12px; opacity:.55; cursor:pointer; margin-left:2px;
                  line-height:1; transition:.12s; }
.rm-gear:hover  { opacity:1; color:#fff; }
.settings-photo-preview { width:100%; height:130px; object-fit:cover;
                           border-radius:10px; display:none; margin-top:8px; }
.chair-zone     { display:flex; align-items:center; justify-content:center; gap:3px;
                  min-width:20px; min-height:20px; border-radius:6px; transition:.15s; padding:2px; }
.chair-zone.top-zone,.chair-zone.bot-zone { flex-direction:row; }
.chair-zone.lft-zone,.chair-zone.rgt-zone { flex-direction:column; }
.chair-zone.ui-droppable-hover { background:rgba(25,135,84,.15);
                                  outline:2px dashed var(--primary); outline-offset:1px; }
.chair-tok      { width:18px; height:18px; background:linear-gradient(160deg,#e9d7b8,#cdab7c);
                  border:1.5px solid #a9824f; border-radius:5px; cursor:pointer;
                  display:flex; align-items:center; justify-content:center;
                  transition:.15s; flex-shrink:0; position:relative;
                  box-shadow:0 1px 3px rgba(0,0,0,.25); }
.chair-tok::after { content:''; position:absolute; background:#a9824f; border-radius:2px; }
.top-zone .chair-tok::after { top:-4px; left:50%; transform:translateX(-50%); width:12px; height:4px; }
.bot-zone .chair-tok::after { bottom:-4px; left:50%; transform:translateX(-50%); width:12px; height:4px; }
.lft-zone .chair-tok::after { left:-4px; top:50%; transform:translateY(-50%); width:4px; height:12px; }
.rgt-zone .chair-tok::after { right:-4px; top:50%; transform:translateY(-50%); width:4px; height:12px; }
.chair-tok:hover { background:#f8d7da; border-color:#dc3545; }
.chair-tok:hover::after { background:#dc3545; }
.chair-tok .ct-del   { display:none; color:#dc3545; font-size:10px; pointer-events:none;
                       position:relative; z-index:1; }
.chair-tok:hover .ct-del { display:block; }
.drop-hint      { width:22px; height:22px; border:2px dashed #adb5bd; border-radius:5px;
                  display:flex; align-items:center; justify-content:center;
                  color:#adb5bd; font-size:13px; flex-shrink:0; pointer-events:none; }
.chair-zone.ui-droppable-hover .drop-hint { border-color:var(--primary); color:var(--primary); }
.ui-draggable-dragging.tool-item { opacity:.75; box-shadow:0 6px 20px rgba(0,0,0,.2); }

/* Tables page — theme refresh overrides */
.page-tab-bar .page-tab { border-radius:999px; background:#fff; border:1px solid rgba(20,40,20,.06); box-shadow:0 4px 14px rgba(15,40,25,.05); font-weight:600; padding:9px 22px; }
.page-tab-bar .page-tab.active { background:linear-gradient(135deg,var(--primary),color-mix(in srgb,var(--primary) 75%,black)); color:#fff; box-shadow:0 8px 18px color-mix(in srgb,var(--primary) 25%,transparent); }
.room-filter .rm-ftab { border-radius:999px; background:#fff; color:#1f2937; padding:7px 18px; font-size:.85rem; border:1px solid rgba(20,40,20,.06); box-shadow:0 4px 14px rgba(15,40,25,.05); }
.room-filter .rm-ftab.active { background:var(--primary); color:#fff; }
.tbl-card { border-radius:16px; }
.tbl-card .tbl-name { font-size:1.35rem; }
.tbl-card .st-badge { padding:5px 12px; font-size:.8rem; }
.tbl-card .seat-dot { width:11px; height:11px; }
.tbl-card .tbl-seats-ico { max-width:150px; }
.stat-ico { width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
.bk-empty-ico { width:64px; height:64px; margin:0 auto 10px; border-radius:16px; background:color-mix(in srgb,var(--primary) 8%,white); display:flex; align-items:center; justify-content:center; font-size:1.8rem; color:color-mix(in srgb,var(--primary) 45%,white); }
</style>

{{-- ── Add Booking Modal ── --}}
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>New Booking — <span id="bookingTableName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bookingTableId">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Guest Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bkName" placeholder="Full name">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" class="form-control" id="bkPhone" placeholder="+977…">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="bkEmail" placeholder="guest@mail.com">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Party Size <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="bkParty" min="1" max="50" value="2">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="bkDate" value="{{ today()->toDateString() }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="bkTime" value="12:00">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" id="bkNotes" rows="2"
                                  placeholder="Allergies, special requests…" style="resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="bkSaveBtn">
                    <i class="bi bi-calendar-check me-1"></i>Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Confirm delete modal ── --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalOk">Delete</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Prompt modal (add room) ── --}}
<div class="modal fade" id="promptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Room</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Room name</label>
                <input type="text" class="form-control" id="promptInput" placeholder="e.g. Zone A, Rooftop…">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="promptModalOk">
                    <i class="bi bi-plus-lg me-1"></i>Add Room
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Table Settings Modal ── --}}
<div class="modal fade" id="tableSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><svg class="me-2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h20"/><path d="M5 7v10"/><path d="M19 7v10"/></svg>Table Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tblSettingsId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Table Name</label>
                    <input type="text" class="form-control" id="tblSettingsName" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="tblSettingsDesc" rows="3" maxlength="1000"
                              placeholder="Describe the table — location, view, ambience…"></textarea>
                </div>
                <div>
                    <label class="form-label fw-semibold d-block mb-2">Photos</label>
                    <div id="tblPhotoGallery" class="d-flex flex-wrap gap-2 mb-2" style="min-height:36px;"></div>
                    <label class="btn btn-outline-secondary btn-sm w-100 mb-0" for="tblSettingsPhotos" id="tblAddPhotoBtn">
                        <i class="bi bi-plus-lg me-1"></i>Add Photos
                        <input type="file" id="tblSettingsPhotos" accept="image/*" multiple class="d-none">
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="tblSettingsSave">
                    <i class="bi bi-check-lg me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Room Settings Modal ── --}}
<div class="modal fade" id="roomSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-door-open me-2"></i>Room Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="roomSettingsId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Room Name</label>
                    <input type="text" class="form-control" id="roomSettingsName" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="roomSettingsDesc" rows="3" maxlength="1000"
                              placeholder="Describe the room…"></textarea>
                </div>
                <div>
                    <label class="form-label fw-semibold">Area Photo</label>
                    <input type="file" class="form-control" id="roomSettingsPhoto" accept="image/*">
                    <img src="" alt="Area photo" id="roomSettingsPreview" class="settings-photo-preview">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="roomSettingsSave">
                    <i class="bi bi-check-lg me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Page header ── --}}
<div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-3">
    <div class="page-title">
        <h2>Table Management</h2>
        <p>Track table status, manage bookings, and design your floor plan.</p>
    </div>
    <button class="btn btn-primary" onclick="openBookingModal(null,'Any')"
            data-bs-toggle="modal" data-bs-target="#bookingModal">
        <i class="bi bi-calendar-plus me-1"></i>New Booking
    </button>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-4">
    @php $statItems = [
        ['Total Tables',     $stats['total'],          'bi-grid-3x3-gap',  'color-mix(in srgb, var(--primary) 14%, white)', 'var(--primary)'],
        ['Available',        $stats['available'],      'bi-check-circle',  'color-mix(in srgb, var(--primary) 10%, white)', 'color-mix(in srgb, var(--primary) 70%, black)'],
        ['Occupied',         $stats['occupied'],       'bi-people-fill',   'color-mix(in srgb, color-mix(in srgb, var(--primary) 80%, #f59e0b) 16%, white)', 'color-mix(in srgb, var(--primary) 80%, #f59e0b)'],
        ['Reserved',         $stats['reserved'],       'bi-bookmark-fill', 'color-mix(in srgb, var(--primary) 8%, white)', 'color-mix(in srgb, var(--primary) 65%, white)'],
        ['Today\'s Bookings', $stats['bookings_today'], 'bi-calendar-check','color-mix(in srgb, var(--primary) 18%, white)', 'color-mix(in srgb, var(--primary) 55%, black)'],
    ]; @endphp
    @foreach($statItems as [$lbl,$val,$ico,$bg,$clr])
    <div class="col-6 col-lg">
        <div class="card-soft" style="padding:16px 18px;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-ico" style="background:{{ $bg }};color:{{ $clr }};">
                    <i class="bi {{ $ico }}"></i>
                </div>
                <div>
                    <div style="font-size:1.6rem;font-weight:800;line-height:1.1;">{{ $val }}</div>
                    <div style="font-size:.85rem;color:#4b5a51;">{{ $lbl }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── View tabs ── --}}
<div class="page-tab-bar">
    <button class="page-tab active" onclick="switchTab('overview',this)">
        <i class="bi bi-grid me-1"></i>Overview
    </button>
    <button class="page-tab" onclick="switchTab('floorplan',this)">
        <i class="bi bi-arrows-move me-1"></i>Floor Plan
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- TAB: Overview                                             --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div id="tab-overview">

    {{-- Room filter --}}
    <div class="room-filter">
        <button class="rm-ftab active" data-room="all" onclick="filterRoom('all',this)">All Rooms</button>
        @foreach($rooms as $room)
        <button class="rm-ftab" data-room="{{ $room->id }}" onclick="filterRoom({{ $room->id }},this)">
            {{ $room->name }}
        </button>
        @endforeach
    </div>

    {{-- Table cards grid --}}
    <div class="row g-3 mb-4" id="tableGrid">
        @foreach($allTables as $t)
        @php
            $seats = $t->chairs->count();
            $bkCount = $t->todayBookings->count();
            $stClass = 'status-'.$t->status;
            $badgeClass = 'st-'.$t->status;
        @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3 table-card-col"
             data-room="{{ $t->room_id }}"
             data-id="{{ $t->id }}">
            <div class="card-soft tbl-card {{ $stClass }}">
                <div class="tbl-card-head">
                    <div>
                        <div class="tbl-name">{{ $t->name }}</div>
                        <div class="tbl-room-tag"><i class="bi bi-door-open me-1"></i>{{ $t->room->name }}</div>
                    </div>
                    <span class="st-badge {{ $badgeClass }}">
                        @if($t->status==='available') <i class="bi bi-check-circle"></i>
                        @elseif($t->status==='occupied') <i class="bi bi-people"></i>
                        @elseif($t->status==='reserved') <i class="bi bi-bookmark"></i>
                        @else <i class="bi bi-brush"></i>
                        @endif
                        {{ ucfirst($t->status) }}
                    </span>
                </div>
                <div class="tbl-card-body">
                    <div class="tbl-stat-row">
                        <div class="tbl-seats-ico">
                            @for($i=0;$i<min($seats,12);$i++)
                            <div class="seat-dot" style="{{ $t->status==='available' ? 'background:var(--primary)' : ($t->status==='occupied' ? 'background:#ef4444' : ($t->status==='reserved' ? 'background:#f59e0b' : '')) }}"></div>
                            @endfor
                            @if($seats>12)<span style="font-size:.7rem;color:var(--muted);">+{{ $seats-12 }}</span>@endif
                        </div>
                        <span class="text-muted" style="font-size:.8rem;">{{ $seats }} seat{{ $seats!=1?'s':'' }}</span>
                    </div>
                    @if($t->description)
                    <p class="text-muted mb-0" style="font-size:.78rem;line-height:1.4;">{{ Str::limit($t->description ?? '', 55) }}</p>
                    @endif
                </div>
                <div class="tbl-card-foot">
                    <div class="tbl-booking-count">
                        <i class="bi bi-calendar{{ $bkCount ? '-fill text-primary' : '' }}"></i>
                        {{ $bkCount }} booking{{ $bkCount!=1?'s':'' }} today
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <select class="status-sel" onchange="setTableStatus({{ $t->id }},this)">
                            @foreach(['available'=>'Available','occupied'=>'Occupied','reserved'=>'Reserved','cleaning'=>'Cleaning'] as $sv=>$sl)
                            <option value="{{ $sv }}" @selected($t->status===$sv)>{{ $sl }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-soft btn-sm" style="font-size:.75rem;padding:4px 8px;"
                                onclick="openBookingModal({{ $t->id }},'{{ addslashes($t->name) }}')"
                                data-bs-toggle="modal" data-bs-target="#bookingModal">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Today's Bookings ─────────────────────────────────── --}}
    <div class="card-soft table-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event" style="color:var(--primary);font-size:1.3rem;"></i>
                <h5 class="mb-0">Today's Bookings</h5>
                <span class="badge" style="background:var(--primary);">{{ $todayBookings->count() }}</span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openBookingModal(null,'Any')"
                    data-bs-toggle="modal" data-bs-target="#bookingModal">
                <i class="bi bi-plus-lg me-1"></i>Add Booking
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Guest</th>
                            <th>Contact</th>
                            <th>Time</th>
                            <th class="text-center">Party</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTbody">
                    @forelse($todayBookings as $bk)
                    <tr data-bid="{{ $bk->id }}">
                        <td class="fw-semibold">{{ $bk->floorTable->name ?? '—' }}</td>
                        <td>{{ $bk->name }}</td>
                        <td class="text-muted" style="font-size:.83rem;">
                            {{ $bk->phone ?? '' }}
                            @if($bk->email)<br><span style="font-size:.75rem;">{{ $bk->email }}</span>@endif
                        </td>
                        <td class="fw-semibold" style="font-size:.88rem;">
                            {{ \Carbon\Carbon::parse($bk->booking_time)->format('h:i A') }}
                        </td>
                        <td class="text-center"><strong>{{ $bk->party_size }}</strong></td>
                        <td class="text-muted" style="font-size:.82rem;">{{ Str::limit($bk->notes, 35) ?? '—' }}</td>
                        <td>
                            @if($bk->status==='confirmed')
                                <span class="status-badge status-ready">Confirmed</span>
                            @elseif($bk->status==='pending')
                                <span class="status-badge status-pending">Pending</span>
                            @else
                                <span class="status-badge status-cancelled">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($bk->status==='pending')
                                <button class="tbl-action confirm" title="Confirm"
                                        onclick="updateBooking({{ $bk->id }},'confirmed',this)">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                @endif
                                @if($bk->status!=='cancelled')
                                <button class="tbl-action cancel" title="Cancel"
                                        onclick="updateBooking({{ $bk->id }},'cancelled',this)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                                <button class="tbl-action del" title="Delete"
                                        onclick="deleteBooking({{ $bk->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="bkEmptyRow">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="bk-empty-ico"><i class="bi bi-calendar2-week"></i></div>
                            No bookings for today yet.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- TAB: Floor Plan                                           --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div id="tab-floorplan" style="display:none;">
<div class="fp-page">
    <div class="fp-toolbar">
        <div class="tool-item tool-table" id="drag-tool-table" title="Drag onto canvas to add a table">
            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h20"/><path d="M5 7v10"/><path d="M19 7v10"/></svg>Table
        </div>
        <div class="tool-item tool-chair" id="drag-tool-chair" title="Drag onto a table zone to add a chair">
            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v8"/><path d="M6 11h12"/><path d="M6 11v8"/><path d="M18 11v8"/></svg>Chair
        </div>
        <div class="vr mx-1"></div>
        <div class="room-tabs" id="room-tabs">
            @foreach($rooms as $room)
            <div class="room-tab @if($loop->first) active @endif"
                 data-room-id="{{ $room->id }}"
                 onclick="switchRoom({{ $room->id }})">
                {{ $room->name }}
                <span class="rm-gear" title="Room settings"
                      onclick="openRoomSettings(event,{{ $room->id }},'{{ addslashes($room->name) }}','{{ $room->photo ? Storage::url($room->photo) : '' }}','{{ addslashes($room->description ?? '') }}')">
                    <i class="bi bi-gear-fill"></i>
                </span>
                <span class="rm-x" onclick="removeRoom(event,{{ $room->id }})">&#x2715;</span>
            </div>
            @endforeach
            <button class="btn-add-room" onclick="addRoom()">
                <i class="bi bi-plus-lg"></i> Add Room
            </button>
        </div>
    </div>

    @forelse($rooms as $room)
    <div class="fp-canvas canvas-room @if(!$loop->first) d-none @endif"
         id="canvas-{{ $room->id }}"
         data-room-id="{{ $room->id }}">
        @if($room->floorTables->isEmpty())
        <div class="fp-empty">
            <i class="bi bi-arrows-move"></i>
            <span>Drag a <strong>Table</strong> here to start</span>
        </div>
        @endif
        @foreach($room->floorTables as $ft)
        @include('admin.theme_one.table._floor_table', ['ft' => $ft])
        @endforeach
    </div>
    @empty
    <div class="fp-canvas" id="canvas-empty">
        <div class="fp-empty">
            <i class="bi bi-door-open"></i>
            <span>Click <strong>+ Add Room</strong> to get started</span>
        </div>
    </div>
    @endforelse
</div>
</div>

@push('js')
<script>
const CSRF   = '{{ csrf_token() }}';
let tCount   = {{ $rooms->pluck('floorTables')->flatten()->count() + 1 }};

/* ════════ Tab switcher ════════ */
function switchTab(name, btn) {
    document.querySelectorAll('.page-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-overview').style.display  = name === 'overview'  ? '' : 'none';
    document.getElementById('tab-floorplan').style.display = name === 'floorplan' ? '' : 'none';
    if (name === 'floorplan') {
        // Re-init sortable after display:none → visible
        document.querySelectorAll('.canvas-room').forEach(c => makeCanvasDroppable(c));
        document.querySelectorAll('.floor-table').forEach(el => activateTable(el));
    }
}

/* ════════ Room filter (overview) ════════ */
function filterRoom(roomId, btn) {
    document.querySelectorAll('.rm-ftab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.table-card-col').forEach(col => {
        col.style.display = (roomId === 'all' || col.dataset.room == roomId) ? '' : 'none';
    });
}

/* ════════ Table status ════════ */
function setTableStatus(id, sel) {
    const status = sel.value;
    fetch(`/admin/tables/${id}/status`, {
        method: 'PATCH',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({status})
    }).then(r => r.json()).then(() => {
        const col  = document.querySelector(`.table-card-col[data-id="${id}"]`);
        if (!col) return;
        const card = col.querySelector('.tbl-card');
        // Update left border class
        card.className = card.className.replace(/status-\w+/, 'status-' + status);
        // Update badge
        const icons = {available:'check-circle',occupied:'people',reserved:'bookmark',cleaning:'brush'};
        const labels = {available:'Available',occupied:'Occupied',reserved:'Reserved',cleaning:'Cleaning'};
        const badge = card.querySelector('.st-badge');
        badge.className = `st-badge st-${status}`;
        badge.innerHTML = `<i class="bi bi-${icons[status]}"></i> ${labels[status]}`;
        // Update seat dots
        const colors = {available:'var(--primary)',occupied:'#ef4444',reserved:'#f59e0b',cleaning:'#94a3b8'};
        card.querySelectorAll('.seat-dot').forEach(d => d.style.background = colors[status]);
    });
}

/* ════════ Booking modal ════════ */
const _bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));

function openBookingModal(tableId, tableName) {
    document.getElementById('bookingTableId').value   = tableId || '';
    document.getElementById('bookingTableName').textContent = tableName || 'Any Table';
    document.getElementById('bkName').value  = '';
    document.getElementById('bkPhone').value = '';
    document.getElementById('bkEmail').value = '';
    document.getElementById('bkParty').value = '2';
    document.getElementById('bkDate').value  = '{{ today()->toDateString() }}';
    document.getElementById('bkTime').value  = '12:00';
    document.getElementById('bkNotes').value = '';
}

document.getElementById('bkSaveBtn').addEventListener('click', async () => {
    const tableId = document.getElementById('bookingTableId').value;
    const name    = document.getElementById('bkName').value.trim();
    const date    = document.getElementById('bkDate').value;
    const time    = document.getElementById('bkTime').value;
    const party   = document.getElementById('bkParty').value;
    if (!name || !date || !time || !party || !tableId) {
        alert('Please fill in all required fields and select a table.');
        return;
    }

    const btn = document.getElementById('bkSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    const res  = await fetch('/admin/bookings', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            table_id: tableId,
            name, phone: document.getElementById('bkPhone').value.trim()||null,
            email: document.getElementById('bkEmail').value.trim()||null,
            booking_date: date, booking_time: time, party_size: party,
            notes: document.getElementById('bkNotes').value.trim()||null,
        })
    });
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-calendar-check me-1"></i>Confirm Booking';

    if (res.ok) {
        _bookingModal.hide();
        // Add row to today's bookings if date is today
        if (date === '{{ today()->toDateString() }}') {
            document.getElementById('bkEmptyRow')?.remove();
            const tbody = document.getElementById('bookingsTbody');
            const timeFmt = new Date(`2000-01-01T${time}`).toLocaleTimeString('en',{hour:'2-digit',minute:'2-digit'});
            const tr = document.createElement('tr');
            tr.dataset.bid = data.id;
            tr.innerHTML = `
                <td class="fw-semibold">${data.floor_table?.name||'—'}</td>
                <td>${data.name}</td>
                <td class="text-muted" style="font-size:.83rem;">${data.phone||''}</td>
                <td class="fw-semibold" style="font-size:.88rem;">${timeFmt}</td>
                <td class="text-center"><strong>${data.party_size}</strong></td>
                <td class="text-muted" style="font-size:.82rem;">${(data.notes||'—').substring(0,35)}</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="tbl-action confirm" onclick="updateBooking(${data.id},'confirmed',this)" title="Confirm"><i class="bi bi-check-lg"></i></button>
                        <button class="tbl-action cancel"  onclick="updateBooking(${data.id},'cancelled',this)" title="Cancel"><i class="bi bi-x-lg"></i></button>
                        <button class="tbl-action del"     onclick="deleteBooking(${data.id})" title="Delete"><i class="bi bi-trash"></i></button>
                    </div>
                </td>`;
            tbody.insertBefore(tr, tbody.firstChild);
            // Update booking count on card
            const col = document.querySelector(`.table-card-col[data-id="${tableId}"]`);
            if (col) {
                const cnt = col.querySelector('.tbl-booking-count');
                if (cnt) {
                    const n = parseInt(cnt.textContent) + 1;
                    cnt.innerHTML = `<i class="bi bi-calendar-fill text-primary"></i> ${n} booking${n!==1?'s':''} today`;
                }
            }
        }
    }
});

/* ════════ Booking actions ════════ */
function updateBooking(id, status, btn) {
    const orig = btn.innerHTML;
    btn.disabled = true;
    fetch(`/admin/bookings/${id}/status`, {
        method: 'PATCH',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({status})
    }).then(r => r.json()).then(() => {
        const row    = document.querySelector(`tr[data-bid="${id}"]`);
        const badges = {confirmed:'<span class="status-badge status-ready">Confirmed</span>',
                        cancelled:'<span class="status-badge status-cancelled">Cancelled</span>'};
        if (row) {
            row.cells[6].innerHTML = badges[status] || '';
            row.cells[7].innerHTML = status === 'cancelled'
                ? `<div class="d-flex gap-1"><button class="tbl-action del" onclick="deleteBooking(${id})"><i class="bi bi-trash"></i></button></div>`
                : `<div class="d-flex gap-1"><button class="tbl-action cancel" onclick="updateBooking(${id},'cancelled',this)"><i class="bi bi-x-lg"></i></button><button class="tbl-action del" onclick="deleteBooking(${id})"><i class="bi bi-trash"></i></button></div>`;
        }
    }).catch(() => { btn.disabled = false; btn.innerHTML = orig; });
}

function deleteBooking(id) {
    if (!confirm('Delete this booking?')) return;
    fetch(`/admin/bookings/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': CSRF}
    }).then(() => {
        document.querySelector(`tr[data-bid="${id}"]`)?.remove();
    });
}

/* ════════ Floor-plan JS (unchanged) ════════ */
function addRoom() {
    showPrompt(name => {
        fetch('/admin/rooms', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({name})
        })
        .then(r=>r.json()).then(room => {
            const tabsEl = document.getElementById('room-tabs');
            const addBtn = tabsEl.querySelector('.btn-add-room');
            const tab = document.createElement('div');
            tab.className = 'room-tab'; tab.dataset.roomId = room.id;
            tab.innerHTML = `${room.name} <span class="rm-gear" onclick="openRoomSettings(event,${room.id},'${room.name.replace(/'/g,"\\'")}','')"><i class="bi bi-gear-fill"></i></span> <span class="rm-x" onclick="removeRoom(event,${room.id})">&#x2715;</span>`;
            tab.onclick = () => switchRoom(room.id);
            tabsEl.insertBefore(tab, addBtn);
            const canvas = document.createElement('div');
            canvas.className = 'fp-canvas canvas-room d-none';
            canvas.id = `canvas-${room.id}`; canvas.dataset.roomId = room.id;
            canvas.innerHTML = `<div class="fp-empty"><i class="bi bi-arrows-move"></i><span>Drag a <strong>Table</strong> here to start</span></div>`;
            document.querySelector('.fp-page').appendChild(canvas);
            makeCanvasDroppable(canvas); switchRoom(room.id);
        });
    });
}
function removeRoom(e, roomId) {
    e.stopPropagation();
    showConfirm('Delete this room and all its tables?', () => {
        fetch(`/admin/rooms/${roomId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}})
        .then(r=>r.json()).then(()=>{
            document.querySelector(`.room-tab[data-room-id="${roomId}"]`)?.remove();
            document.getElementById(`canvas-${roomId}`)?.remove();
            const first = document.querySelector('.room-tab');
            if(first) switchRoom(first.dataset.roomId);
        });
    },'Delete Room');
}
function switchRoom(roomId) {
    document.querySelectorAll('.room-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.canvas-room').forEach(c=>c.classList.add('d-none'));
    document.querySelector(`.room-tab[data-room-id="${roomId}"]`)?.classList.add('active');
    document.getElementById(`canvas-${roomId}`)?.classList.remove('d-none');
}
$('#drag-tool-table,#drag-tool-chair').draggable({helper:'clone',zIndex:1000,opacity:.82,cursor:'grabbing',revert:'invalid'});
function makeCanvasDroppable(el) {
    $(el).droppable({
        accept:'#drag-tool-table',tolerance:'pointer',
        over:function(){ $(this).addClass('drop-over'); },
        out:function(){ $(this).removeClass('drop-over'); },
        drop:function(evt,ui){
            $(this).removeClass('drop-over');
            const canvas=this, offset=$(canvas).offset();
            const x=Math.max(0,Math.round(evt.pageX-offset.left-56));
            const y=Math.max(0,Math.round(evt.pageY-offset.top-40));
            const roomId=canvas.dataset.roomId, name='T'+tCount++;
            fetch('/admin/tables',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                body:JSON.stringify({room_id:roomId,name,pos_x:x,pos_y:y})})
            .then(r=>r.json()).then(ft=>{
                canvas.querySelector('.fp-empty')?.remove();
                const el=buildTableEl(ft); canvas.appendChild(el); activateTable(el);
            });
        }
    });
}
function buildTableEl(ft) {
    const d=document.createElement('div');
    d.className='floor-table'; d.id=`ft-${ft.id}`;
    d.dataset.id=ft.id; d.dataset.name=ft.name;
    d.dataset.description=ft.description||'';
    d.dataset.photos=JSON.stringify((ft.photos||[]).map(p=>({id:p.id,url:'/storage/'+p.path})));
    d.style.left=ft.pos_x+'px'; d.style.top=ft.pos_y+'px';
    const top=(ft.chairs||[]).filter(c=>c.side==='top');
    const bot=(ft.chairs||[]).filter(c=>c.side==='bottom');
    const lft=(ft.chairs||[]).filter(c=>c.side==='left');
    const rgt=(ft.chairs||[]).filter(c=>c.side==='right');
    const total=(ft.chairs||[]).length;
    d.innerHTML=`<div class="ft-wrap">
      <div class="chair-zone top-zone" data-tid="${ft.id}" data-side="top">${top.map(chairToken).join('')}<div class="drop-hint"><i class="bi bi-plus"></i></div></div>
      <div class="ft-mid">
        <div class="chair-zone lft-zone" data-tid="${ft.id}" data-side="left">${lft.map(chairToken).join('')}<div class="drop-hint"><i class="bi bi-plus"></i></div></div>
        <div class="ft-body">
          <button class="ft-del" onclick="deleteTable(${ft.id})">&times;</button>
          <button class="ft-edit" onclick="openTableSettings(${ft.id},this.closest('.floor-table').dataset.name,this.closest('.floor-table').dataset.photos)"><i class="bi bi-pencil-fill"></i></button>
          <span class="ft-name">${ft.name}</span><span class="ft-seats">${total} seats</span>
        </div>
        <div class="chair-zone rgt-zone" data-tid="${ft.id}" data-side="right">${rgt.map(chairToken).join('')}<div class="drop-hint"><i class="bi bi-plus"></i></div></div>
      </div>
      <div class="chair-zone bot-zone" data-tid="${ft.id}" data-side="bottom">${bot.map(chairToken).join('')}<div class="drop-hint"><i class="bi bi-plus"></i></div></div>
    </div>`;
    return d;
}
const CHAIR_TOK_ICON = '<i class="bi bi-x ct-del"></i>';
function chairToken(c){ return `<div class="chair-tok" data-cid="${c.id}" onclick="removeChair(${c.id},this)">${CHAIR_TOK_ICON}</div>`; }
function activateTable(el) {
    $(el).draggable({containment:'parent',stack:'.floor-table',cancel:'.ft-del,.ft-edit,.chair-tok,.chair-zone',
        stop:function(evt,ui){ fetch(`/admin/tables/${this.dataset.id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({pos_x:Math.round(ui.position.left),pos_y:Math.round(ui.position.top)})}); }
    });
    $(el).find('.chair-zone').droppable({accept:'#drag-tool-chair',tolerance:'pointer',
        drop:function(evt,ui){
            const zone=this,tid=zone.dataset.tid,side=zone.dataset.side;
            fetch('/admin/chairs',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({table_id:tid,side})})
            .then(r=>r.json()).then(chair=>{
                const hint=zone.querySelector('.drop-hint'),tok=document.createElement('div');
                tok.className='chair-tok'; tok.dataset.cid=chair.id; tok.onclick=()=>removeChair(chair.id,tok);
                tok.innerHTML=CHAIR_TOK_ICON; zone.insertBefore(tok,hint); refreshSeats(tid);
            });
        }
    });
}
function deleteTable(tid){ showConfirm('Delete this table and its chairs?',()=>{ fetch(`/admin/tables/${tid}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}}).then(r=>r.json()).then(()=>document.getElementById(`ft-${tid}`)?.remove()); },'Delete Table'); }
function removeChair(cid,el){ fetch(`/admin/chairs/${cid}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}}).then(r=>r.json()).then(()=>{ const zone=el.closest('.chair-zone'),tid=zone.dataset.tid; el.remove(); refreshSeats(tid); }); }
function refreshSeats(tid){ const f=document.getElementById(`ft-${tid}`); if(!f)return; const n=f.querySelectorAll('.chair-tok').length; const s=f.querySelector('.ft-seats'); if(s)s.textContent=n+' seats'; }
let _confirmCb=null;
const _confirmModal=new bootstrap.Modal(document.getElementById('confirmModal'));
document.getElementById('confirmModalOk').addEventListener('click',()=>{ _confirmModal.hide(); if(_confirmCb)_confirmCb(); });
function showConfirm(msg,cb,title='Confirm'){ document.getElementById('confirmModalTitle').textContent=title; document.getElementById('confirmModalBody').textContent=msg; _confirmCb=cb; _confirmModal.show(); }
let _promptCb=null;
const _promptModal=new bootstrap.Modal(document.getElementById('promptModal'));
document.getElementById('promptModalOk').addEventListener('click',()=>{ const v=document.getElementById('promptInput').value.trim(); if(!v)return; _promptModal.hide(); if(_promptCb)_promptCb(v); });
document.getElementById('promptInput').addEventListener('keydown',e=>{ if(e.key==='Enter')document.getElementById('promptModalOk').click(); });
document.getElementById('promptModal').addEventListener('shown.bs.modal',()=>document.getElementById('promptInput').focus());
function showPrompt(cb){ document.getElementById('promptInput').value=''; _promptCb=cb; _promptModal.show(); }
const _tblSettingsModal=new bootstrap.Modal(document.getElementById('tableSettingsModal'));
function renderPhotoGallery(photos){ const g=document.getElementById('tblPhotoGallery'); if(!photos.length){g.innerHTML='<p class="text-muted small mb-0 fst-italic">No photos yet.</p>';return;} g.innerHTML=photos.map(p=>`<div class="position-relative" id="tbl-ph-${p.id}" style="flex-shrink:0;"><img src="${p.url}" style="width:80px;height:62px;object-fit:cover;border-radius:8px;display:block;"><button type="button" onclick="deleteTablePhoto(${p.id})" style="position:absolute;top:-7px;right:-7px;width:20px;height:20px;background:#dc3545;color:#fff;border:none;border-radius:50%;font-size:11px;line-height:1;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:5;">&times;</button></div>`).join(''); }
function openTableSettings(tid,name,photosRaw){ const photos=(()=>{try{return JSON.parse(photosRaw||'[]');}catch{return [];}})(); document.getElementById('tblSettingsId').value=tid; document.getElementById('tblSettingsName').value=name; document.getElementById('tblSettingsPhotos').value=''; const ftEl=document.getElementById(`ft-${tid}`); document.getElementById('tblSettingsDesc').value=ftEl?(ftEl.dataset.description||''):''; renderPhotoGallery(photos); _tblSettingsModal.show(); }
async function deleteTablePhoto(photoId){ await fetch(`/admin/table-photos/${photoId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}}); document.getElementById(`tbl-ph-${photoId}`)?.remove(); const tid=document.getElementById('tblSettingsId').value; updateFtPhotos(tid); const g=document.getElementById('tblPhotoGallery'); if(!g.querySelector('[id^="tbl-ph-"]'))g.innerHTML='<p class="text-muted small mb-0 fst-italic">No photos yet.</p>'; }
document.getElementById('tblSettingsPhotos').addEventListener('change',async function(){ const tid=document.getElementById('tblSettingsId').value; if(!this.files.length||!tid)return; const btn=document.getElementById('tblAddPhotoBtn'); btn.textContent='Uploading…'; const fd=new FormData(); [...this.files].forEach(f=>fd.append('photos[]',f)); fd.append('_token',CSRF); const res=await fetch(`/admin/tables/${tid}/photos`,{method:'POST',body:fd}); const saved=await res.json(); btn.innerHTML='<i class="bi bi-plus-lg me-1"></i>Add Photos'; this.value=''; const g=document.getElementById('tblPhotoGallery'); g.querySelector('p')?.remove(); saved.forEach(p=>{ const w=document.createElement('div'); w.className='position-relative'; w.id=`tbl-ph-${p.id}`; w.style.flexShrink='0'; w.innerHTML=`<img src="${p.url}" style="width:80px;height:62px;object-fit:cover;border-radius:8px;display:block;"><button type="button" onclick="deleteTablePhoto(${p.id})" style="position:absolute;top:-7px;right:-7px;width:20px;height:20px;background:#dc3545;color:#fff;border:none;border-radius:50%;font-size:11px;line-height:1;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:5;">&times;</button>`; g.appendChild(w); }); updateFtPhotos(tid); });
function updateFtPhotos(tid){ const g=document.getElementById('tblPhotoGallery'); const thumbs=[...g.querySelectorAll('[id^="tbl-ph-"]')]; const photos=thumbs.map(el=>({id:parseInt(el.id.replace('tbl-ph-','')),url:el.querySelector('img').src})); const ftEl=document.getElementById(`ft-${tid}`); if(ftEl)ftEl.dataset.photos=JSON.stringify(photos); }
document.getElementById('tblSettingsSave').addEventListener('click',async()=>{ const tid=document.getElementById('tblSettingsId').value; const name=document.getElementById('tblSettingsName').value.trim(); const description=document.getElementById('tblSettingsDesc').value.trim(); if(!name)return; await fetch(`/admin/tables/${tid}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({name,description})}); const ftEl=document.getElementById(`ft-${tid}`); if(ftEl){ftEl.dataset.name=name;ftEl.dataset.description=description;const s=ftEl.querySelector('.ft-name');if(s)s.textContent=name;} _tblSettingsModal.hide(); });
const _roomSettingsModal=new bootstrap.Modal(document.getElementById('roomSettingsModal'));
document.getElementById('roomSettingsPhoto').addEventListener('change',function(){ const p=document.getElementById('roomSettingsPreview'); if(this.files[0]){p.src=URL.createObjectURL(this.files[0]);p.style.display='block';} });
function openRoomSettings(e,roomId,name,photoUrl,description){ e.stopPropagation(); document.getElementById('roomSettingsId').value=roomId; document.getElementById('roomSettingsName').value=name; document.getElementById('roomSettingsDesc').value=description||''; document.getElementById('roomSettingsPhoto').value=''; const p=document.getElementById('roomSettingsPreview'); if(photoUrl){p.src=photoUrl;p.style.display='block';}else{p.src='';p.style.display='none';} _roomSettingsModal.show(); }
document.getElementById('roomSettingsSave').addEventListener('click',async()=>{ const roomId=document.getElementById('roomSettingsId').value; const name=document.getElementById('roomSettingsName').value.trim(); const description=document.getElementById('roomSettingsDesc').value.trim(); const file=document.getElementById('roomSettingsPhoto').files[0]; if(!name)return; await fetch(`/admin/rooms/${roomId}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({name,description})}); if(file){const fd=new FormData();fd.append('photo',file);fd.append('_token',CSRF);await fetch(`/admin/rooms/${roomId}/photo`,{method:'POST',body:fd});} const tab=document.querySelector(`.room-tab[data-room-id="${roomId}"]`); if(tab){const tn=[...tab.childNodes].find(n=>n.nodeType===Node.TEXT_NODE);if(tn)tn.textContent=name+' ';} _roomSettingsModal.hide(); });
$(function(){
    document.querySelectorAll('.canvas-room').forEach(c=>makeCanvasDroppable(c));
    document.querySelectorAll('.floor-table').forEach(el=>activateTable(el));
});
</script>
@endpush
@endsection
