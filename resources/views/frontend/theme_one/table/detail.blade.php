@extends('frontend.theme_one.component.layout')

@section('content')
<style>
/* ── Table detail page ───────────────────────────── */
.detail-section { padding: 52px 0; }

/* Photo gallery column */
.gallery-main {
    border-radius: var(--sb-radius);
    overflow: hidden;
    background: var(--sb-green-pale);
    aspect-ratio: 4/3;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.gallery-main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    transition: transform .3s;
}
.gallery-main img:hover { transform: scale(1.02); }
.gallery-main-placeholder { color: var(--sb-muted); font-size: 72px; }
.gallery-counter {
    position: absolute;
    bottom: 12px;
    right: 14px;
    background: rgba(0,0,0,.52);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 999px;
    backdrop-filter: blur(4px);
}
.gallery-thumbs {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
}
.gallery-thumb {
    width: 68px;
    height: 52px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color .15s;
    flex-shrink: 0;
}
.gallery-thumb.active { border-color: var(--sb-green); }
.gallery-thumb img { width:100%; height:100%; object-fit:cover; }

/* Info column */
.table-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--sb-green-pale);
    color: var(--sb-green-dark);
    font-size: 12px;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 999px;
}
.room-link {
    color: var(--sb-green);
    text-decoration: none;
    font-weight: 600;
}
.room-link:hover { text-decoration: underline; }
.info-divider { border-top: 1px solid var(--sb-border); margin: 20px 0; }
.detail-stat {
    text-align: center;
    padding: 14px 0;
}
.detail-stat-value {
    font-size: 22px;
    font-weight: 800;
    color: var(--sb-green-dark);
}
.detail-stat-label {
    font-size: 11px;
    color: var(--sb-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.desc-block {
    background: var(--sb-green-pale);
    border-left: 4px solid var(--sb-green);
    border-radius: 0 12px 12px 0;
    padding: 16px 18px;
    color: var(--sb-text);
    font-size: 15px;
    line-height: 1.7;
}

/* Related tables */
.tbl-card {
    border-radius: var(--sb-radius);
    overflow: hidden;
    background: #fff;
    box-shadow: var(--sb-shadow);
    border: 1px solid var(--sb-border);
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    color: inherit;
    display: block;
}
.tbl-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 50px color-mix(in srgb, var(--primary) 16%, transparent);
    color: inherit;
}
.tbl-card-img {
    height: 150px;
    overflow: hidden;
    background: var(--sb-green-pale);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sb-muted);
    font-size: 40px;
}
.tbl-card-img img { width:100%; height:100%; object-fit:cover; }
.tbl-card-body { padding: 14px 16px; }
.seats-badge {
    font-size: 11px;
    background: var(--sb-green-pale);
    color: var(--sb-green-dark);
    padding: 3px 10px;
    border-radius: 999px;
    font-weight: 600;
}

/* Status badge */
.tbl-status-badge-lg { display:inline-flex;align-items:center;gap:6px;padding:5px 14px;
    border-radius:999px;font-size:.82rem;font-weight:700; }
.tbl-status-available { background:color-mix(in srgb, var(--primary) 14%, white);color:color-mix(in srgb, var(--primary) 70%, black); }
.tbl-status-occupied  { background:#fee2e2;color:#991b1b; }
.tbl-status-reserved  { background:#fef3c7;color:#92400e; }
.tbl-status-cleaning  { background:#f1f5f9;color:#475569; }

/* Menu ordering section */
.menu-order-card { border-radius:18px;background:#fff;box-shadow:var(--sb-shadow);
    border:1px solid var(--sb-border);overflow:hidden;transition:.2s; }
.menu-order-card:hover { transform:translateY(-2px);box-shadow:0 20px 45px color-mix(in srgb, var(--primary) 14%, transparent); }
.menu-order-img { height:160px;overflow:hidden;background:var(--sb-green-pale);
    display:flex;align-items:center;justify-content:center; }
.menu-order-img img { width:100%;height:100%;object-fit:cover; }
.menu-order-body { padding:14px 16px; }
.menu-order-filter .btn { border-radius:999px;padding:6px 16px;font-size:.82rem;font-weight:600; }

/* Today's bookings */
.booking-slot { display:flex;align-items:center;gap:12px;padding:10px 14px;
    border-radius:12px;background:var(--sb-green-pale);margin-bottom:8px; }
.booking-slot:last-child { margin-bottom:0; }
.booking-slot-time { font-weight:800;font-size:.9rem;color:var(--sb-green-dark);min-width:60px; }

/* Booking modal */
.booking-modal-header {
    background: linear-gradient(135deg, var(--sb-green), var(--sb-green-dark));
    border-radius: 20px 20px 0 0;
    padding: 20px 24px;
}
</style>

<section class="detail-section">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="mb-4" style="font-size:14px;">
            <a href="{{ route('frontend.table.index') }}" class="text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>All Rooms
            </a>
            <span class="text-muted mx-2">/</span>
            <a href="{{ route('frontend.table.index') }}#room-pane-{{ $table->room->id }}"
               class="room-link">{{ $table->room->name }}</a>
            <span class="text-muted mx-2">/</span>
            <span class="fw-semibold">{{ $table->name }}</span>
        </nav>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-5 align-items-start">

            {{-- ── Left: Photo gallery ─────────────── --}}
            <div class="col-lg-6">
                <div class="gallery-main" id="galleryMain">
                    @if($table->photos->isNotEmpty())
                    <img src="{{ Storage::url($table->photos->first()->path) }}"
                         id="galleryMainImg" alt="{{ $table->name }}"
                         onclick="openLightbox(0)">
                    @if($table->photos->count() > 1)
                    <span class="gallery-counter">
                        <i class="bi bi-images me-1"></i>{{ $table->photos->count() }} photos
                    </span>
                    @endif
                    @else
                    <div class="gallery-main-placeholder">
                        <i class="bi bi-layout-wtf"></i>
                    </div>
                    @endif
                </div>

                @if($table->photos->count() > 1)
                <div class="gallery-thumbs">
                    @foreach($table->photos as $i => $photo)
                    <div class="gallery-thumb {{ $i === 0 ? 'active' : '' }}"
                         id="thumb-{{ $i }}"
                         onclick="switchPhoto({{ $i }}, '{{ Storage::url($photo->path) }}')">
                        <img src="{{ Storage::url($photo->path) }}" alt="">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── Right: Info ─────────────────────── --}}
            <div class="col-lg-6">
                @php
                    $statusMap = ['available'=>['Available','tbl-status-available','check-circle'],
                                  'occupied' =>['Occupied', 'tbl-status-occupied', 'people-fill'],
                                  'reserved' =>['Reserved','tbl-status-reserved', 'bookmark-fill'],
                                  'cleaning' =>['Cleaning','tbl-status-cleaning', 'brush']];
                    [$stLbl,$stCls,$stIco] = $statusMap[$table->status ?? 'available'] ?? $statusMap['available'];
                    $unavailable = in_array($table->status ?? 'available', ['occupied','cleaning']);
                @endphp
                {{-- Badges --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="table-badge">
                        <i class="bi bi-door-open"></i>
                        <a href="{{ route('frontend.table.index') }}" class="room-link text-decoration-none"
                           style="color:inherit;">{{ $table->room->name }}</a>
                    </span>
                    <span class="table-badge">
                        <i class="bi bi-person-seat-fill"></i>
                        {{ $table->chairs->count() }} Seat{{ $table->chairs->count() !== 1 ? 's' : '' }}
                    </span>
                    @if($table->photos->isNotEmpty())
                    <span class="table-badge">
                        <i class="bi bi-images"></i>
                        {{ $table->photos->count() }} Photo{{ $table->photos->count() !== 1 ? 's' : '' }}
                    </span>
                    @endif
                    <span class="tbl-status-badge-lg {{ $stCls }}">
                        <i class="bi bi-{{ $stIco }}"></i> {{ $stLbl }}
                    </span>
                </div>

                <h1 class="fw-bold mb-1" style="font-size:2rem;">{{ $table->name }}</h1>

                @if($table->room->description)
                <p class="text-muted small mb-0">
                    <i class="bi bi-door-open me-1"></i>
                    <strong>{{ $table->room->name }}:</strong> {{ $table->room->description }}
                </p>
                @endif

                <div class="info-divider"></div>

                {{-- Stats --}}
                <div class="row g-0 border rounded-4 overflow-hidden mb-4">
                    <div class="col-4 detail-stat border-end">
                        <div class="detail-stat-value">{{ $table->chairs->count() }}</div>
                        <div class="detail-stat-label">Seats</div>
                    </div>
                    <div class="col-4 detail-stat border-end">
                        <div class="detail-stat-value">{{ $table->photos->count() }}</div>
                        <div class="detail-stat-label">Photos</div>
                    </div>
                    <div class="col-4 detail-stat">
                        <div class="detail-stat-value">
                            {{ $table->room->floorTables->count() }}
                        </div>
                        <div class="detail-stat-label">In Room</div>
                    </div>
                </div>

                {{-- Table description --}}
                @if($table->description)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">About This Table</h6>
                    <div class="desc-block">{{ $table->description }}</div>
                </div>
                @endif

                {{-- Book button --}}
                @if(!$unavailable)
                <button class="btn btn-sb-primary w-100 py-3 fs-5 fw-semibold rounded-4"
                        onclick="openBookingModal({{ $table->id }}, '{{ addslashes($table->name) }}', '{{ addslashes($table->room->name) }}', {{ $table->chairs->count() }})">
                    <i class="bi bi-calendar-check me-2"></i>Book This Table
                </button>
                <p class="text-muted small text-center mt-2 mb-0">
                    <i class="bi bi-shield-check me-1"></i>Free reservation · No credit card required
                </p>
                @else
                <div class="alert rounded-4 mb-0" style="background:#fef3c7;border-color:#fde68a;color:#92400e;">
                    <i class="bi bi-clock me-2"></i>
                    This table is currently <strong>{{ $stLbl }}</strong>. Please check back later or choose another table.
                </div>
                @endif

                {{-- Today's bookings --}}
                @if($todayBookings->isNotEmpty())
                <div class="mt-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-event me-2" style="color:var(--primary);"></i>Today's Reservations</h6>
                    @foreach($todayBookings as $bk)
                    <div class="booking-slot">
                        <span class="booking-slot-time">{{ \Carbon\Carbon::parse($bk->booking_time)->format('h:i A') }}</span>
                        <div style="flex:1;">
                            <div class="fw-semibold" style="font-size:.88rem;">{{ $bk->name }}</div>
                            <div class="text-muted" style="font-size:.76rem;">
                                {{ $bk->party_size }} guest{{ $bk->party_size > 1 ? 's' : '' }}
                                @if($bk->notes) · {{ Str::limit($bk->notes, 30) }} @endif
                            </div>
                        </div>
                        <span style="font-size:.74rem;font-weight:600;padding:2px 8px;border-radius:999px;
                              background:{{ $bk->status==='confirmed' ? 'color-mix(in srgb, var(--primary) 14%, white)' : '#fef3c7' }};
                              color:{{ $bk->status==='confirmed' ? 'color-mix(in srgb, var(--primary) 70%, black)' : '#92400e' }};">
                            {{ ucfirst($bk->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ── Other tables in same room ─────────────── --}}
        @if($otherTables->isNotEmpty())
        <div class="mt-5 pt-4 border-top">
            <h4 class="fw-bold mb-4">Other Tables in {{ $table->room->name }}</h4>
            <div class="row g-4">
                @foreach($otherTables as $ot)
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('frontend.table.show', $ot) }}" class="tbl-card">
                        <div class="tbl-card-img">
                            @if($ot->photos->isNotEmpty())
                            <img src="{{ Storage::url($ot->photos->first()->path) }}" alt="{{ $ot->name }}">
                            @else
                            <i class="bi bi-layout-wtf"></i>
                            @endif
                        </div>
                        <div class="tbl-card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{ $ot->name }}</span>
                                <span class="seats-badge">
                                    <i class="bi bi-person-seat-fill"></i> {{ $ot->chairs->count() }}
                                </span>
                            </div>
                            @if($ot->description)
                            <p class="text-muted small mb-0 mt-1" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                {{ $ot->description }}
                            </p>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

{{-- ── Order from this table ──────────────────────────── --}}
<section style="padding:52px 0 0;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">Order from {{ $table->name }}</h3>
                <p class="text-muted mb-0" style="font-size:.9rem;">
                    Browse our menu and add items to your cart — we'll bring it to your table.
                </p>
            </div>
            <a href="{{ route('frontend.menu.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
                <i class="bi bi-grid me-1"></i>Full Menu
            </a>
        </div>

        {{-- Category filter --}}
        <div class="d-flex flex-wrap gap-2 menu-order-filter mb-4">
            <button class="btn active" data-ofilter="all">All</button>
            @foreach($categories as $cat)
            <button class="btn" data-ofilter="ocat-{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- Menu grid --}}
        <div class="row g-3">
            @foreach($menuItems as $item)
            <div class="col-sm-6 col-lg-3 order-item-col" data-ocategory="ocat-{{ $item->menu_category_id }}">
                <div class="menu-order-card">
                    <div class="menu-order-img">
                        @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
                        @else
                        <i class="bi bi-egg-fried" style="font-size:2.5rem;color:var(--sb-muted);"></i>
                        @endif
                    </div>
                    <div class="menu-order-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0" style="font-size:.92rem;line-height:1.3;">{{ $item->name }}</h6>
                            <span class="price-tag" style="white-space:nowrap;margin-left:8px;">${{ number_format($item->price,2) }}</span>
                        </div>
                        @if($item->description)
                        <p class="text-muted mb-2" style="font-size:.76rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $item->description }}
                        </p>
                        @endif
                        <button class="btn btn-primary-o w-100 btn-sm"
                                onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->image ? Storage::url($item->image) : '' }}')">
                            <i class="bi bi-plus-circle me-1"></i>Add to Order
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Photo Lightbox ────────────────────────────────── --}}
@php $photosJson = $table->photos->map(fn($p) => ['url' => Storage::url($p->path)])->toJson(); @endphp
<div class="modal fade" id="photoLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="position-relative text-center">
                <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 z-3"
                        data-bs-dismiss="modal" style="width:34px;height:34px;line-height:1;">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div id="lightboxCarousel" class="carousel slide">
                    <div class="carousel-inner" id="lightboxCarouselInner">
                        @foreach($table->photos as $i => $photo)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ Storage::url($photo->path) }}" alt=""
                                 style="border-radius:18px;max-height:80vh;width:100%;object-fit:contain;">
                        </div>
                        @endforeach
                    </div>
                    @if($table->photos->count() > 1)
                    <button class="carousel-control-prev" type="button"
                            data-bs-target="#lightboxCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button"
                            data-bs-target="#lightboxCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
                <p class="text-white mt-2 fw-semibold small">{{ $table->name }}</p>
            </div>
        </div>
    </div>
</div>

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

<script>
const _lightbox = new bootstrap.Modal(document.getElementById('photoLightbox'));
const _booking  = new bootstrap.Modal(document.getElementById('bookingModal'));
let _activeThumb = 0;

function switchPhoto(index, url) {
    document.getElementById('galleryMainImg').src = url;
    document.querySelectorAll('.gallery-thumb').forEach((t, i) => t.classList.toggle('active', i === index));
    _activeThumb = index;
}
function openLightbox(index) {
    bootstrap.Carousel.getOrCreateInstance(document.getElementById('lightboxCarousel')).to(index);
    _lightbox.show();
}
function openBookingModal(tableId, tableName, roomName, seats) {
    document.getElementById('bookingTableId').value = tableId;
    document.getElementById('bookingSubtitle').textContent = tableName + ' · ' + roomName + ' · ' + seats + ' seat(s)';
    _booking.show();
}

/* ── Menu category filter ── */
document.querySelectorAll('.menu-order-filter .btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.menu-order-filter .btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const f = this.dataset.ofilter;
        document.querySelectorAll('.order-item-col').forEach(col => {
            col.style.display = (f === 'all' || col.dataset.ocategory === f) ? '' : 'none';
        });
    });
});
</script>

{{-- Toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100;">
    <div id="cartToast" class="toast align-items-center text-white border-0"
         role="alert" style="background:var(--sb-green-dark);border-radius:14px;">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>
                <span id="cartToastItem"></span> added to order!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection
