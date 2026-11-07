@extends('frontend.theme_one.component.layout')

@section('content')
<style>
/* ── Table booking page ─────────────────────────────── */
.room-pills .nav-link {
    color: var(--sb-text);
    font-weight: 600;
    border-radius: 999px;
    padding: 9px 22px;
    border: 1.5px solid var(--sb-border);
    background: #fff;
    transition: .18s;
}
.room-pills .nav-link.active,
.room-pills .nav-link:hover {
    background: var(--sb-green);
    color: #fff;
    border-color: var(--sb-green);
}

/* Room banner */
.room-banner {
    height: 200px;
    border-radius: var(--sb-radius);
    background: center/cover no-repeat var(--sb-green-pale);
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
}
.room-banner-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.5) 0%, rgba(0,0,0,.1) 100%);
    display: flex;
    align-items: center;
    padding: 28px 32px;
}
.room-banner-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sb-muted);
    gap: 12px;
    font-size: 15px;
    font-weight: 600;
}
.room-banner-placeholder > div > i { font-size: 28px; color: var(--sb-green); }
.room-banner-empty { height: auto; background: #fff; border: 1px solid var(--sb-border); box-shadow: var(--sb-shadow); border-radius: 16px; }
.room-banner-empty .room-banner-placeholder { padding: 16px 22px !important; color: var(--sb-green-dark); font-size: 1rem; }

/* Table cards */
.tbl-card {
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    box-shadow: var(--sb-shadow);
    border: 1px solid var(--sb-border);
    transition: transform .2s, box-shadow .2s;
}
.tbl-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 24px 55px color-mix(in srgb, var(--primary) 18%, transparent);
}
.tbl-card-img {
    height: 180px;
    position: relative;
    overflow: hidden;
    background: var(--sb-green-pale);
    display: flex;
    align-items: center;
    justify-content: center;
}
.tbl-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.tbl-card-no-photo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: var(--sb-muted);
}
.tbl-card-no-photo i { font-size: 42px; }
.tbl-card-no-photo span { font-size: 12px; }
.view-photo-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,.55);
    color: #fff;
    border: none;
    border-radius: 999px;
    padding: 5px 13px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    gap: 4px;
    transition: .15s;
}
.view-photo-btn:hover { background: rgba(0,0,0,.78); }
.tbl-card-body { padding: 18px; }
.seats-badge {
    font-size: 12px;
    background: var(--sb-green-pale);
    color: var(--sb-green-dark);
    padding: 4px 11px;
    border-radius: 999px;
    font-weight: 600;
    white-space: nowrap;
}

/* Table status badge */
.tbl-status-badge { position:absolute;top:10px;left:10px;padding:3px 10px;border-radius:999px;
    font-size:.72rem;font-weight:700;display:inline-flex;align-items:center;gap:4px;
    backdrop-filter:blur(4px); }
.tbl-status-available { background:color-mix(in srgb, var(--primary) 90%, transparent);color:#fff; }
.tbl-status-occupied  { background:rgba(239,68,68,.9);color:#fff; }
.tbl-status-reserved  { background:rgba(245,158,11,.9);color:#fff; }
.tbl-status-cleaning  { background:rgba(100,116,139,.85);color:#fff; }
.tbl-card.unavailable { opacity:.75; }
.tbl-card.unavailable .book-btn { pointer-events:none; opacity:.5; }

/* Booking modal */
.booking-modal-header {
    background: linear-gradient(135deg, var(--sb-green), var(--sb-green-dark));
    border-radius: 20px 20px 0 0;
    padding: 20px 24px;
}
</style>

    @include('frontend.theme_one.component.page-banner', ['title' => 'Book a Table', 'subtitle' => 'Choose your preferred room and reserve your spot.'])

<section class="section-space">
    <div class="container">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($rooms->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-door-open" style="font-size:56px;opacity:.4;"></i>
            <p class="mt-3 fs-5">No rooms available yet. Please check back later.</p>
        </div>
        @else

        {{-- Room Tabs --}}
        <ul class="nav room-pills gap-2 mb-4 flex-wrap" id="roomTabs" role="tablist">
            @foreach($rooms as $room)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($loop->first) active @endif"
                        data-bs-toggle="pill"
                        data-bs-target="#room-pane-{{ $room->id }}"
                        type="button" role="tab">
                    <i class="bi bi-door-open me-1"></i>{{ $room->name }}
                </button>
            </li>
            @endforeach
        </ul>

        {{-- Room Panes --}}
        <div class="tab-content" id="roomTabContent">
            @foreach($rooms as $room)
            <div class="tab-pane fade @if($loop->first) show active @endif"
                 id="room-pane-{{ $room->id }}"
                 role="tabpanel">

                {{-- Room banner + optional description --}}
                <div class="room-banner @unless($room->photo) room-banner-empty @endunless"
                     @if($room->photo) style="background-image: url('{{ Storage::url($room->photo) }}')" @endif>
                    @if($room->photo)
                    <div class="room-banner-overlay">
                        <div class="text-white flex-grow-1">
                            <h3 class="fw-bold mb-1">{{ $room->name }}</h3>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-layout-wtf me-1"></i>{{ $room->floorTables->count() }} table(s)
                            </p>
                        </div>
                        <a href="{{ route('frontend.room.show', $room) }}"
                           class="btn btn-light btn-sm rounded-pill px-3 ms-3 flex-shrink-0">
                            <i class="bi bi-eye me-1"></i>View Room
                        </a>
                    </div>
                    @else
                    <div class="room-banner-placeholder h-100 justify-content-between px-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-door-open"></i>
                            <span>{{ $room->name }} &mdash; {{ $room->floorTables->count() }} table(s)</span>
                        </div>
                        <a href="{{ route('frontend.room.show', $room) }}"
                           class="btn btn-primary-o btn-sm px-3">
                            <i class="bi bi-eye me-1"></i>View Room
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Room description --}}
                @if($room->description)
                <p class="text-muted mb-4" style="font-size:15px;line-height:1.7;">
                    {{ $room->description }}
                </p>
                @endif

                {{-- Tables grid --}}
                @if($room->floorTables->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-layout-wtf" style="font-size:42px;opacity:.4;"></i>
                    <p class="mt-2">No tables set up in this room yet.</p>
                </div>
                @else
                <div class="row g-4">
                    @foreach($room->floorTables as $ft)
                    <div class="col-sm-6 col-lg-4">
                        <div class="tbl-card">
                            @php
                                $unavailable = in_array($ft->status ?? 'available', ['occupied','cleaning']);
                                $statusMap   = ['available'=>['available','tbl-status-available','check-circle'],
                                                'occupied' =>['occupied', 'tbl-status-occupied', 'people-fill'],
                                                'reserved' =>['reserved','tbl-status-reserved', 'bookmark-fill'],
                                                'cleaning' =>['cleaning','tbl-status-cleaning', 'brush']];
                                [$stLbl,$stCls,$stIco] = $statusMap[$ft->status ?? 'available'] ?? $statusMap['available'];
                            @endphp
                            <div class="tbl-card-img">
                                @if($ft->photos->isNotEmpty())
                                <img src="{{ Storage::url($ft->photos->first()->path) }}" alt="View from {{ $ft->name }}">
                                @php $photosJson = $ft->photos->map(fn($p) => ['url' => Storage::url($p->path)])->toJson(); @endphp
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
                                    <i class="bi bi-{{ $stIco }}"></i> {{ ucfirst($stLbl) }}
                                </span>
                            </div>
                            <div class="tbl-card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0">{{ $ft->name }}</h6>
                                    <span class="seats-badge">
                                        <i class="bi bi-person-seat-fill"></i> {{ $ft->chairs->count() }} seats
                                    </span>
                                </div>
                                @if($ft->description)
                                <p class="text-muted small mb-3" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ $ft->description }}
                                </p>
                                @else
                                <p class="text-muted small mb-3">{{ $room->name }}</p>
                                @endif
                                <div class="d-flex gap-2">
                                    <a href="{{ route('frontend.table.show', $ft) }}"
                                       class="btn btn-outline-secondary rounded-3 flex-shrink-0">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!$unavailable)
                                    <button class="btn btn-sb-primary flex-fill book-btn"
                                            onclick="openBookingModal({{ $ft->id }}, '{{ addslashes($ft->name) }}', '{{ addslashes($room->name) }}', {{ $ft->chairs->count() }})">
                                        <i class="bi bi-calendar-check me-1"></i>Book
                                    </button>
                                    @else
                                    <button class="btn btn-secondary flex-fill" disabled>
                                        <i class="bi bi-{{ $stIco }} me-1"></i>{{ ucfirst($stLbl) }}
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
