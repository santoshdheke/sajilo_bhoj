@extends('frontend.theme_one.component.layout')

@section('content')

{{-- ── Hero ────────────────────────────────────────────────────────── --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="bi bi-stars"></i> {{ $settings['tagline'] ?: 'Fresh · Local · Delicious' }}</div>
                <h1>{{ $settings['hero_title'] ?: 'Delicious food, smooth service, and a premium dining experience.' }}</h1>
                <p>{{ $settings['hero_subtitle'] ?: ($settings['restaurant_name'] ?: 'SajiloBhoj') . ' brings you authentic flavours prepared fresh every day — from hearty mains to light bites and signature drinks. Reserve your table and enjoy a meal worth remembering.' }}</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-primary">Explore Menu</a>
                    <a href="{{ route('frontend.table.index') }}" class="btn btn-sb-light">Reserve Now</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-box">
                        <h4>{{ $stats['menu_items'] }}+</h4>
                        <span>Menu items</span>
                    </div>
                    <div class="stat-box">
                        <h4>{{ $stats['tables_total'] }}</h4>
                        <span>Tables</span>
                    </div>
                    <div class="stat-box">
                        <h4>{{ $stats['today_bookings'] }}</h4>
                        <span>Bookings today</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="floating-order">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <div class="small text-white-50">Live snapshot</div>
                            <h3 class="mb-0 fw-bold">Today's Highlights</h3>
                        </div>
                        <span class="pill pill-orange">Open Now</span>
                    </div>

                    <div class="mini-card mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small">Top Selling Item</div>
                                <div class="fw-semibold fs-5">{{ $topSelling->name ?? 'Chicken Momo' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="text-white-50 small">Sold</div>
                                <div class="fw-bold">{{ $topSelling->qty ?? '0' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mini-card mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small">Tables Occupied</div>
                                <div class="fw-semibold fs-5">{{ $stats['tables_occupied'] }} / {{ $stats['tables_total'] }}</div>
                            </div>
                            <span class="pill pill-green">
                                {{ $stats['tables_occupied'] < $stats['tables_total'] ? 'Seats Available' : 'Fully Booked' }}
                            </span>
                        </div>
                    </div>

                    <div class="mini-card">
                        <div class="text-white-50 small mb-2">Average Bill</div>
                        @php $goalPct = min(100, round(($stats['avg_bill'] / 60) * 100)); @endphp
                        <div class="progress mb-2" style="height:10px;border-radius:999px;">
                            <div class="progress-bar bg-success" style="width:{{ $goalPct }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>${{ number_format($stats['avg_bill'], 2) }}</span>
                            <span class="text-white-50">Revenue goal {{ $goalPct }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Why choose us ───────────────────────────────────────────────── --}}
<section class="section-space">
    <div class="container">
        <div class="text-center mb-5 fade-up">
            <h2 class="section-title">Why guests choose SajiloBhoj</h2>
            <p class="section-subtitle mx-auto" style="max-width:640px;">Every detail — from the kitchen to your table — is crafted for comfort, quality, and a smile.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-cup-hot',       'Fresh Ingredients',  'We prepare every dish using carefully selected vegetables, spices, and locally-sourced ingredients.'],
                ['bi-alarm',         'Quick Service',      'Fast order handling and a smooth dine-in experience. We respect your time as much as your appetite.'],
                ['bi-emoji-smile',   'Cozy Atmosphere',    'A warm interior, soft lighting, and elegant presentation make every meal an occasion worth revisiting.'],
                ['bi-calendar-check','Easy Reservation',   'Book your table online in seconds — choose your room, date, and party size without any hassle.'],
            ] as [$icon, $title, $desc])
            <div class="col-md-6 col-lg-3 fade-up">
                <div class="feature-card">
                    <div class="icon-wrap"><i class="bi {{ $icon }}"></i></div>
                    <h5 class="fw-bold">{{ $title }}</h5>
                    <p class="section-subtitle mb-0">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Featured menu items ─────────────────────────────────────────── --}}
<section class="section-space pt-0">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5 fade-up">
                <div class="bg-panel">
                    <span class="pill pill-green mb-3">Popular Picks</span>
                    <h2 class="section-title text-white">Best sellers from our kitchen</h2>
                    <p class="text-white-50 mb-2">
                        {{ $categories->count() }} categories ·
                        {{ $stats['menu_items'] }} available items
                    </p>
                    <p class="text-white-50 mb-4">
                        From spicy starters to refreshing drinks and indulgent desserts — there's something for everyone.
                    </p>
                    <a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-outline">View Full Menu</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-4">
                    @forelse($featuredItems as $item)
                    <div class="col-md-6 fade-up">
                        <div class="menu-card">
                            <div class="menu-image" style="overflow:hidden;border-radius:18px;margin-bottom:14px;height:130px;">
                                @if($item->image)
                                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                                     style="width:100%;height:130px;object-fit:cover;border-radius:18px;">
                                @else
                                <div style="width:100%;height:130px;background:var(--sb-green-pale);display:flex;align-items:center;justify-content:center;border-radius:18px;">
                                    <i class="bi bi-egg-fried" style="font-size:2.5rem;color:var(--sb-muted);"></i>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="fw-bold mb-0">{{ $item->name }}</h5>
                                <span class="price-tag">${{ number_format($item->price, 2) }}</span>
                            </div>
                            <p class="section-subtitle mb-0" style="font-size:.83rem;line-height:1.5;">
                                {{ Str::limit($item->description ?? $item->category->name, 70) }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-white-50 py-4">No menu items yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Categories quick-nav ────────────────────────────────────────── --}}
<section class="section-space pt-0">
    <div class="container">
        <div class="text-center mb-5 fade-up">
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-subtitle">Find exactly what you're craving.</p>
        </div>
        <div class="row g-3 justify-content-center">
            @foreach($categories as $cat)
            <div class="col-6 col-md-3 fade-up">
                <a href="{{ route('frontend.menu.index') }}" class="text-decoration-none">
                    <div class="feature-card text-center" style="padding:24px 16px;">
                        <div class="icon-wrap mx-auto mb-3">
                            @if(str_contains(strtolower($cat->name),'starter')) <i class="bi bi-egg-fried"></i>
                            @elseif(str_contains(strtolower($cat->name),'main'))   <i class="bi bi-basket2"></i>
                            @elseif(str_contains(strtolower($cat->name),'bever'))  <i class="bi bi-cup-straw"></i>
                            @elseif(str_contains(strtolower($cat->name),'dessert'))<i class="bi bi-cake2"></i>
                            @else <i class="bi bi-grid"></i>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1" style="font-size:.95rem;">{{ $cat->name }}</h5>
                        <p class="section-subtitle mb-0" style="font-size:.78rem;">{{ $cat->menu_items_count }} items</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Opening hours + CTA ─────────────────────────────────────────── --}}
<section class="section-space pt-0">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6 fade-up">
                <div class="testimonial-card">
                    <span class="pill pill-orange mb-3">Guest Review</span>
                    <h3 class="fw-bold mb-3">"Fresh food, warm service — SajiloBhoj never disappoints!"</h3>
                    <p class="section-subtitle">The ambience is calm and welcoming, the food arrives fast and tastes incredible. Definitely our go-to for family dinners.</p>
                    <div class="fw-bold">— Happy Guest ⭐⭐⭐⭐⭐</div>
                </div>
            </div>
            <div class="col-lg-6 fade-up">
                <div class="contact-card">
                    <h3 class="fw-bold mb-3">Opening Hours</h3>
                    @if($settings['opening_hours'])
                    <p class="section-subtitle mb-3" style="white-space:pre-line;">{{ $settings['opening_hours'] }}</p>
                    @else
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Sunday – Friday</span><strong>10:00 AM – 10:00 PM</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Saturday</span><strong>11:00 AM – 11:00 PM</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 mb-3">
                        <span>Online Booking</span><strong>24/7</strong>
                    </div>
                    @endif
                    <a href="{{ route('frontend.table.index') }}" class="btn btn-sb-primary w-100">
                        <i class="bi bi-calendar-check me-2"></i>Book a Table Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
