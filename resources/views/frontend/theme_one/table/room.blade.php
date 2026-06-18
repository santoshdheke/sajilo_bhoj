@extends('frontend.theme_one.component.layout')

@section('content')
<style>
/* ── Room Detail page ───────────────────────────── */
.room-hero {
    position: relative;
    height: 380px;
    background: center/cover no-repeat var(--sb-green-pale);
    overflow: hidden;
}
.room-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,.15) 0%, rgba(0,0,0,.65) 100%);
    display: flex;
    align-items: flex-end;
    padding: 36px 48px;
}
.room-hero-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--sb-muted);
    font-size: 64px;
}
.room-hero-badge {
    background: rgba(255,255,255,.18);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 999px;
    padding: 5px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.breadcrumb-link { color: var(--sb-muted); text-decoration: none; font-size: 14px; }
.breadcrumb-link:hover { color: var(--sb-green); }

/* Table cards */
.tbl-card {
    border-radius: var(--sb-radius);
    overflow: hidden;
    background: #fff;
    box-shadow: var(--sb-shadow);
    border: 1px solid var(--sb-border);
    transition: transform .2s, box-shadow .2s;
}
.tbl-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 24px 55px rgba(15,77,47,.18);
}
.tbl-card-img {
    height: 200px;
    position: relative;
    overflow: hidden;
    background: var(--sb-green-pale);
    display: flex;
    align-items: center;
    justify-content: center;
}
.tbl-card-img img { width:100%; height:100%; object-fit:cover; }
.tbl-card-no-photo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: var(--sb-muted);
}
.tbl-card-no-photo i { font-size: 48px; }
.tbl-card-no-photo span { font-size: 12px; }
.view-photo-btn {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(0,0,0,.55);
    color: #fff;
    border: none;
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    gap: 5px;
    transition: .15s;
}
.view-photo-btn:hover { background: rgba(0,0,0,.78); }
.tbl-card-body { padding: 20px; }
.seats-badge {
    font-size: 12px;
    background: var(--sb-green-pale);
    color: var(--sb-green-dark);
    padding: 4px 12px;
    border-radius: 999px;
    font-weight: 600;
    white-space: nowrap;
}

/* Status badge */
.tbl-status-badge { position:absolute;top:10px;left:10px;padding:3px 10px;border-radius:999px;
    font-size:.72rem;font-weight:700;display:inline-flex;align-items:center;gap:4px;backdrop-filter:blur(4px); }
.tbl-status-available { background:rgba(16,185,129,.9);color:#fff; }
.tbl-status-occupied  { background:rgba(239,68,68,.9);color:#fff; }
.tbl-status-reserved  { background:rgba(245,158,11,.9);color:#fff; }
.tbl-status-cleaning  { background:rgba(100,116,139,.85);color:#fff; }

/* Booking modal */
.booking-modal-header {
    background: linear-gradient(135deg, var(--sb-green), var(--sb-green-dark));
    border-radius: 20px 20px 0 0;
    padding: 20px 24px;
}

/* Stats bar */
.room-stat {
    text-align: center;
    padding: 16px 24px;
    border-right: 1px solid var(--sb-border);
}
.room-stat:last-child { border-right: none; }
.room-stat-value { font-size: 26px; font-weight: 800; color: var(--sb-green-dark); }
.room-stat-label { font-size: 12px; color: var(--sb-muted); font-weight: 500; margin-top: 2px; }
</style>

{{-- Hero --}}
<div class="room-hero"
     @if($room->photo) style="background-image:url('{{ Storage::url($room->photo) }}')" @endif>
    @if($room->photo)
    <div class="room-hero-overlay">
        <div class="text-white">
            <div class="mb-3">
                <span class="room-hero-badge">
                    <i class="bi bi-layout-wtf"></i>
                    {{ $room->floorTables->count() }} Table(s)
                </span>
                <span class="room-hero-badge ms-2">
                    <i class="bi bi-person-seat-fill"></i>
                    {{ $room->floorTables->sum(fn($t) => $t->chairs->count()) }} Seat(s)
                </span>
            </div>
            <h1 class="fw-bold mb-0" style="font-size:2.2rem;">{{ $room->name }}</h1>
        </div>
    </div>
    @else
    <div class="room-hero-placeholder">
        <i class="bi bi-door-open"></i>
    </div>
    @endif
</div>

<section class="section-space">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="mb-4" style="font-size:14px;">
            <a href="{{ route('frontend.table.index') }}" class="breadcrumb-link">
                <i class="bi bi-arrow-left me-1"></i>Book a Table
            </a>
            <span class="text-muted mx-2">/</span>
            <span class="fw-semibold">{{ $room->name }}</span>
        </nav>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Stats bar --}}
        @php $totalSeats = $room->floorTables->sum(fn($t) => $t->chairs->count()); @endphp
        <div class="d-flex flex-wrap rounded-4 border mb-5" style="background:#fff;overflow:hidden;">
            <div class="room-stat flex-fill">
                <div class="room-stat-value">{{ $room->floorTables->count() }}</div>
                <div class="room-stat-label">Tables Available</div>
            </div>
            <div class="room-stat flex-fill">
                <div class="room-stat-value">{{ $totalSeats }}</div>
                <div class="room-stat-label">Total Seats</div>
            </div>
            <div class="room-stat flex-fill">
                <div class="room-stat-value">
                    {{ $room->floorTables->max(fn($t) => $t->chairs->count()) ?: 0 }}
                </div>
                <div class="room-stat-label">Max Seats / Table</div>
            </div>
        </div>

        {{-- Section heading --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Tables in {{ $room->name }}</h3>
            <a href="{{ route('frontend.table.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
                <i class="bi bi-grid me-1"></i>All Rooms
            </a>
        </div>

        {{-- Tables grid --}}
        @if($room->floorTables->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-layout-wtf" style="font-size:52px;opacity:.4;"></i>
            <p class="mt-3 fs-5">No tables set up in this room yet.</p>
        </div>
        @else
        <div class="row g-4">
            @foreach($room->floorTables as $ft)
            @php
                $photosJson  = $ft->photos->map(fn($p) => ['url' => Storage::url($p->path)])->toJson();
                $unavailable = in_array($ft->status ?? 'available', ['occupied','cleaning']);
                $statusMap   = ['available'=>['Available','tbl-status-available','check-circle'],
                                'occupied' =>['Occupied', 'tbl-status-occupied', 'people-fill'],
                                'reserved' =>['Reserved','tbl-status-reserved', 'bookmark-fill'],
                                'cleaning' =>['Cleaning','tbl-status-cleaning', 'brush']];
                [$stLbl,$stCls,$stIco] = $statusMap[$ft->status ?? 'available'] ?? $statusMap['available'];
            @endphp
            <div class="col-sm-6 col-lg-4">
                <div class="tbl-card">
                    <div class="tbl-card-img">
                        @if($ft->photos->isNotEmpty())
                        <img src="{{ Storage::url($ft->photos->first()->path) }}" alt="View from {{ $ft->name }}">
                        <button class="view-photo-btn"
                                onclick='openPhotoLightbox({{ $photosJson }}, "View from {{ addslashes($ft->name) }}")'>
                            <i class="bi bi-images"></i>
                            {{ $ft->photos->count() }} photo{{ $ft->photos->count() > 1 ? 's' : '' }}
                        </button>
                        @else
                        <div class="tbl-card-no-photo">
                            <i class="bi bi-layout-wtf"></i>
                            <span>No photo yet</span>
                        </div>
                        @endif
                        <span class="tbl-status-badge {{ $stCls }}">
                            <i class="bi bi-{{ $stIco }}"></i> {{ $stLbl }}
                        </span>
                    </div>
                    <div class="tbl-card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold mb-0">{{ $ft->name }}</h5>
                            <span class="seats-badge">
                                <i class="bi bi-person-seat-fill"></i> {{ $ft->chairs->count() }} seat(s)
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-door-open me-1"></i>{{ $room->name }}
                            @if($ft->photos->count() > 1)
                            &nbsp;·&nbsp; <i class="bi bi-images me-1"></i>{{ $ft->photos->count() }} photos
                            @endif
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('frontend.table.show', $ft) }}"
                               class="btn btn-outline-secondary rounded-3 flex-shrink-0">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$unavailable)
                            <button class="btn btn-sb-primary flex-fill"
                                    onclick="openBookingModal({{ $ft->id }}, '{{ addslashes($ft->name) }}', '{{ addslashes($room->name) }}', {{ $ft->chairs->count() }})">
                                <i class="bi bi-calendar-check me-1"></i>Book This Table
                            </button>
                            @else
                            <button class="btn btn-secondary flex-fill" disabled>
                                <i class="bi bi-{{ $stIco }} me-1"></i>{{ $stLbl }}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

{{-- ── Booking Modal ──────────────────────────────────── --}}
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:22px;overflow:hidden;">
            <div class="booking-modal-header">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Reserve a Table</h5>
                <p class="text-white mb-0 opacity-75 small mt-1" id="bookingSubtitle"></p>
            </div>
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal" style="filter:invert(1);z-index:10;"></button>
            <form action="{{ route('frontend.table.book') }}" method="POST">
                @csrf
                <input type="hidden" name="table_id" id="bookingTableId">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" required
                                   placeholder="Full name" value="{{ old('name') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control rounded-3"
                                   placeholder="+977-98XXXXXXXX" value="{{ old('phone') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="booking_date" class="form-control rounded-3" required
                                   min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Time <span class="text-danger">*</span></label>
                            <input type="time" name="booking_time" class="form-control rounded-3" required
                                   value="{{ old('booking_time') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Party Size <span class="text-danger">*</span></label>
                            <input type="number" name="party_size" class="form-control rounded-3"
                                   required min="1" max="50" value="{{ old('party_size', 2) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Special Requests</label>
                            <textarea name="notes" class="form-control rounded-3" rows="2"
                                      placeholder="Dietary needs, celebrations, seating preferences…">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3 flex-fill"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sb-primary rounded-3 flex-fill">
                        <i class="bi bi-check-lg me-1"></i>Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Photo Lightbox (carousel) ───────────────────────── --}}
<div class="modal fade" id="photoLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="position-relative text-center">
                <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-0 z-3"
                        data-bs-dismiss="modal" style="width:34px;height:34px;line-height:1;z-index:10;">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div id="lightboxCarousel" class="carousel slide">
                    <div class="carousel-inner" id="lightboxCarouselInner"></div>
                    <button class="carousel-control-prev" id="lightboxPrev"
                            type="button" data-bs-target="#lightboxCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" id="lightboxNext"
                            type="button" data-bs-target="#lightboxCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <p id="lightboxCaption" class="text-white mt-2 fw-semibold small"></p>
            </div>
        </div>
    </div>
</div>

<script>
const _bookingModal  = new bootstrap.Modal(document.getElementById('bookingModal'));
const _lightboxModal = new bootstrap.Modal(document.getElementById('photoLightbox'));

function openBookingModal(tableId, tableName, roomName, seats) {
    document.getElementById('bookingTableId').value = tableId;
    document.getElementById('bookingSubtitle').textContent =
        tableName + ' · ' + roomName + ' · ' + seats + ' seat(s)';
    _bookingModal.show();
}

function openPhotoLightbox(photos, caption) {
    const inner = document.getElementById('lightboxCarouselInner');
    inner.innerHTML = photos.map((p, i) => `
        <div class="carousel-item${i === 0 ? ' active' : ''}">
            <img src="${p.url}" alt=""
                 style="border-radius:18px;max-height:72vh;width:100%;object-fit:contain;">
        </div>`).join('');
    const multi = photos.length > 1;
    document.getElementById('lightboxPrev').style.display = multi ? '' : 'none';
    document.getElementById('lightboxNext').style.display = multi ? '' : 'none';
    document.getElementById('lightboxCaption').textContent = caption;
    bootstrap.Carousel.getOrCreateInstance(document.getElementById('lightboxCarousel')).to(0);
    _lightboxModal.show();
}
</script>
@endsection
