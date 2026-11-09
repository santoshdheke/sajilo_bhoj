@extends('frontend.theme_one.component.layout')

@section('content')
@php
    $brand        = $settings['restaurant_name'] ?: 'SajiloBhoj';
    $fallbackImg  = asset('assets/frontend/img/menu/momo.png');
    $withImage    = $featuredItems->first(fn ($i) => $i->photoUrl());
    $heroImg      = $withImage?->photoUrl() ?? $fallbackImg;
    $featured     = ($topSelling ? $featuredItems->firstWhere('name', $topSelling->name) : null) ?? $featuredItems->first();
    $catIcon = function ($name) {
        $n = strtolower($name);
        return match (true) {
            str_contains($n, 'momo')                                   => 'bi-egg-fried',
            str_contains($n, 'thali') || str_contains($n, 'main')      => 'bi-basket2',
            str_contains($n, 'chinese') || str_contains($n, 'noodle')  => 'bi-bowl-hot',
            str_contains($n, 'drink') || str_contains($n, 'bever')     => 'bi-cup-straw',
            str_contains($n, 'dessert') || str_contains($n, 'cake')    => 'bi-cake2',
            str_contains($n, 'pizza') || str_contains($n, 'burger')    => 'bi-fire',
            str_contains($n, 'tea') || str_contains($n, 'coffee')      => 'bi-cup-hot',
            default                                                    => 'bi-grid',
        };
    };
@endphp

<style>
    .hm { background: #fff; flex: 1 0 auto; }
    .hm h1, .hm h2, .hm h3 { color: var(--sb-text); letter-spacing: -.02em; }
    .hm-hero h1 { color: #1c2620 !important; }
    .hm-hero h1 .hl { color: var(--sb-green) !important; }
    .hm-mint { background: var(--sb-green-soft); color: var(--sb-green-dark); }

    /* Hero */
    .hm-hero { padding: 48px 0 56px; overflow: hidden; }
    .hm-badge { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 5px 14px; font-size: .85rem; font-weight: 600; }
    .hm-hero h1 { font-size: clamp(2.2rem, 4.8vw, 3.5rem); font-weight: 800; line-height: 1.08; margin: 14px 0 18px; }
    .hm-hero h1 .hl { color: var(--sb-green); }
    .hm-hero .lead-text { color: var(--sb-muted); font-size: 1rem; line-height: 1.65; max-width: 500px; }
    .hm-btn { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; padding: 12px 26px; font-weight: 600; transition: .2s; border: 0; }
    .hm-btn-fill { background: var(--sb-green); color: #fff; box-shadow: 0 10px 24px color-mix(in srgb, var(--primary) 30%, transparent); }
    .hm-btn-fill:hover { background: var(--sb-green-dark); color: #fff; }
    .hm-btn-line { border: 1.5px solid var(--sb-green); color: var(--sb-green-dark); background: #fff; }
    .hm-btn-line:hover { background: var(--sb-green-pale); color: var(--sb-green-dark); }
    .hm-btn-white { background: #fff; color: var(--sb-green-dark); padding: 8px 20px; font-size: .9rem; }
    .hm-btn-white:hover { color: var(--sb-green-dark); transform: translateX(2px); }

    .hm-stats { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 30px; }
    .hm-stat { display: flex; align-items: center; gap: 14px; background: #fff; border-radius: 14px; padding: 14px 22px 14px 18px; box-shadow: 0 8px 26px rgba(15, 40, 25, .08); min-width: 150px; }
    .hm-stat .bi { font-size: 1.6rem; color: var(--sb-green); }
    .hm-stat .ico-svg { width: 28px; height: 28px; color: var(--sb-green); flex-shrink: 0; }
    .hm-stat strong { display: block; font-size: 1.05rem; line-height: 1.1; }
    .hm-stat span { font-size: .8rem; color: var(--sb-muted); }

    .hm-visual { position: relative; padding: 30px 10px 40px 30px; }
    .hm-blob { position: absolute; inset: 0 -60px -10px 10px; background: var(--sb-green-pale); border-radius: 46% 54% 42% 58% / 38% 40% 60% 62%; }
    .hm-photo { position: relative; border-radius: 26px; overflow: hidden; aspect-ratio: 5 / 4; transform: rotate(-4deg); box-shadow: 0 24px 50px rgba(15, 40, 25, .18); }
    .hm-photo img { width: 100%; height: 100%; object-fit: cover; }
    .hm-spark { position: absolute; left: 0; top: 18px; width: 60px; color: var(--sb-green); }

    /* Section heading */
    .hm-head { text-align: center; margin-bottom: 30px; }
    .hm-head h2 { font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 800; margin: 0; }
    .hm-head .bar { display: block; width: 64px; height: 3px; border-radius: 3px; background: var(--sb-green); margin: 12px auto 0; }
    .hm-head-lines { display: flex; align-items: center; justify-content: center; gap: 30px; }
    .hm-head-lines::before, .hm-head-lines::after { content: ''; width: 100px; height: 2px; background: var(--sb-green); opacity: .6; }
    .hm-head-lines h2 { color: var(--sb-green-dark); }

    /* Cards */
    .hm-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 28px rgba(15, 40, 25, .07); border: 1px solid #f0f3f1; height: 100%; }
    .hm-feature { padding: 22px 20px; transition: transform .2s; }
    .hm-feature:hover { transform: translateY(-4px); }
    .hm-feature .ico { width: 54px; height: 54px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 16px; }
    .hm-feature h3 { font-size: 1.05rem; font-weight: 700; color: var(--sb-green-dark); margin-bottom: 8px; }
    .hm-feature p { font-size: .88rem; color: var(--sb-muted); line-height: 1.55; margin: 0; }

    .hm-promo { background: linear-gradient(135deg, var(--sb-green), var(--sb-green-dark)); color: #fff; border-radius: 18px; padding: 30px 34px; height: 100%; display: flex; align-items: center; gap: 30px; box-shadow: 0 16px 36px color-mix(in srgb, var(--primary) 25%, transparent); }
    .hm-promo svg { width: 96px; flex-shrink: 0; }
    .hm-promo h3 { color: #fff; font-size: 1.45rem; font-weight: 700; margin: 0 0 6px; }
    .hm-promo p { opacity: .85; margin: 0 0 14px; font-size: .92rem; }

    .hm-dish { position: relative; display: flex; overflow: hidden; height: 100%; min-height: 180px; background: linear-gradient(90deg, var(--sb-green-pale) 0 45%, #fff 100%); }
    .hm-dish .info { padding: 26px 18px 22px; z-index: 1; display: flex; flex-direction: column; justify-content: center; min-width: 0; }
    .hm-dish .tag { font-size: 1.6rem; color: var(--sb-green); margin-bottom: 12px; }
    .hm-dish h3 { font-size: 1.2rem; font-weight: 700; margin: 0; }
    .hm-dish .price { font-size: 1.9rem; font-weight: 800; color: var(--sb-green-dark); margin: 4px 0 10px; }
    .hm-dish .add { font-size: .85rem; font-weight: 600; color: var(--sb-green); align-self: flex-start; }
    .hm-dish .img { position: absolute; right: 0; top: 0; bottom: 0; width: 58%; }
    .hm-dish .img img { width: 100%; height: 100%; object-fit: cover; -webkit-mask-image: linear-gradient(90deg, transparent 0, #000 30%); mask-image: linear-gradient(90deg, transparent 0, #000 30%); }

    .hm-cats { display: flex; flex-wrap: wrap; justify-content: center; gap: 18px; }
    .hm-cats .hm-cat { flex: 0 1 200px; height: auto; }
    .hm-cat { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 24px 12px 20px; color: var(--sb-text); transition: transform .2s, box-shadow .2s; }
    .hm-cat:hover { transform: translateY(-4px); color: var(--sb-text); box-shadow: 0 14px 32px rgba(15, 40, 25, .12); }
    .hm-cat .bi { font-size: 2.2rem; color: var(--sb-green); }
    .hm-cat-img { width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;
                  box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary) 35%, white), 0 8px 18px rgba(15, 40, 25, .12); }
    .hm-cat strong { font-weight: 500; }
    .hm-cat small { color: var(--sb-muted); font-size: .75rem; margin-top: -6px; }

    .hm-section { padding: 28px 0; }
    .hm-section:last-child { padding-bottom: 72px; }

    @media (max-width: 991px) {
        .hm-hero { padding: 32px 0 24px; }
        .hm-visual { margin-top: 10px; padding: 20px 10px 30px 20px; }
        .hm-blob { right: -20px; }
        .hm-head-lines::before, .hm-head-lines::after { width: 40px; }
    }
    @media (max-width: 575px) {
        .hm-stat { flex: 1 1 100%; }
        .hm-promo { flex-direction: column; align-items: flex-start; gap: 14px; padding: 24px; }
        .hm-promo svg { width: 64px; }
        .hm-cats .hm-cat { flex: 1 1 calc(50% - 9px); }
    }
</style>

<div class="hm">

{{-- ── Hero ─────────────────────────────────────────────── --}}
<section class="hm-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="hm-badge hm-mint"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 3C10 3 4 8 4 15c0 2 .6 3.7 1.6 5L3 22.6 4.4 24l2.6-2.6C8.3 22.4 10 23 12 23c7 0 11-6 9-20zM8 18c2-5 5-8 9-10-3 3-6 6-9 10z"/></svg>{{ $settings['tagline'] ?: 'Fresh · Local · Delicious' }}</span>

                @if ($settings['hero_title'])
                    <h1>{{ $settings['hero_title'] }}</h1>
                @else
                    <h1>Delicious food, smooth service, and a <span class="hl">premium dining experience.</span></h1>
                @endif

                <p class="lead-text">{{ $settings['hero_subtitle'] ?: $brand . ' brings you authentic flavours prepared fresh every day — from hearty mains to light bites and signature drinks.' }}</p>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('frontend.menu.index') }}" class="hm-btn hm-btn-fill">Explore Menu <i class="bi bi-arrow-right"></i></a>
                    <a href="{{ route('frontend.table.index') }}" class="hm-btn hm-btn-line">Reserve Now</a>
                </div>

                <div class="hm-stats">
                    <div class="hm-stat"><svg class="ico-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3v7a2 2 0 0 0 2 2v9"/><path d="M10 3v7a2 2 0 0 1-2 2"/><path d="M8 3v6"/><path d="M17 21V13"/><path d="M17 13c-2 0-3-2.5-3-5s1-5 3-5 3 2.5 3 5-1 5-3 5z" fill="currentColor"/></svg><div><strong>{{ $stats['menu_items'] }}+</strong><span>Menu Items</span></div></div>
                    <div class="hm-stat"><i class="bi bi-people"></i><div><strong>{{ $stats['tables_total'] }}</strong><span>Tables</span></div></div>
                    <div class="hm-stat"><i class="bi bi-calendar-week"></i><div><strong>{{ $stats['today_bookings'] }}</strong><span>Bookings today</span></div></div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hm-visual">
                    <span class="hm-blob"></span>
                    <svg class="hm-spark" viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" aria-hidden="true">
                        <path d="M30 8l4 16"/><path d="M50 6l-8 18"/><path d="M6 30l18 2"/>
                    </svg>
                    <div class="hm-photo">
                        <img src="{{ $heroImg }}" alt="{{ $withImage->name ?? $brand }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Why guests choose us ─────────────────────────────── --}}
<section class="hm-section">
    <div class="container">
        <div class="hm-head">
            <h2>{{ $settings['home_why_title'] ?: 'Why guests choose ' . $brand }}</h2>
            <span class="bar"></span>
        </div>
        <div class="row g-3">
            @for ($i = 1; $i <= 4; $i++)
            <div class="col-sm-6 col-lg-3">
                <div class="hm-card hm-feature">
                    <div class="ico hm-mint"><i class="bi {{ $settings["home_feature_{$i}_icon"] }}"></i></div>
                    <h3>{{ $settings["home_feature_{$i}_title"] }}</h3>
                    <p>{{ $settings["home_feature_{$i}_desc"] }}</p>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

{{-- ── Best sellers + featured dish ─────────────────────── --}}
<section class="hm-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="hm-promo">
                    <svg viewBox="0 0 96 64" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="48" cy="10" r="4"/>
                        <path d="M10 48a38 34 0 0 1 76 0z"/>
                        <path d="M24 36a24 20 0 0 1 14-12"/>
                        <path d="M4 54h88"/>
                    </svg>
                    <div>
                        <h3>{{ $settings['home_featured_title'] ?: 'Best sellers from our kitchen' }}</h3>
                        <p>{{ $settings['home_featured_text'] ?: 'A selection of our most loved dishes.' }}</p>
                        <a href="{{ route('frontend.menu.index') }}" class="hm-btn hm-btn-white">View Full Menu <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                @if ($featured)
                <div class="hm-card hm-dish">
                    <div class="info">
                        <i class="bi bi-tag tag"></i>
                        <h3>{{ $featured->name }}</h3>
                        <div class="price">{{ \App\Support\Money::format($featured->price, $currencyCode) }}</div>
                        <a href="javascript:void(0)" class="add"
                           onclick="addToCart({{ $featured->id }}, '{{ addslashes($featured->name) }}', {{ $featured->price }}, '{{ $featured->photoUrl() }}')">
                            <i class="bi bi-plus-circle me-1"></i>Add to order
                        </a>
                    </div>
                    <div class="img"><img src="{{ $featured->photoUrl() ?? $fallbackImg }}" alt="{{ $featured->name }}" loading="lazy"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ── Browse by category ───────────────────────────────── --}}
@if ($categories->isNotEmpty())
<section class="hm-section">
    <div class="container">
        <div class="hm-head hm-head-lines"><h2>Browse by Category</h2></div>
        <div class="hm-cats">
            @foreach ($categories as $cat)
            <a href="{{ route('frontend.menu.index', ['category' => $cat->id]) }}" class="hm-card hm-cat">
                @if ($cat->image)
                <img class="hm-cat-img" src="{{ $cat->imageUrl() }}" alt="{{ $cat->name }}" loading="lazy">
                @else
                <i class="bi {{ $catIcon($cat->name) }}"></i>
                @endif
                <strong>{{ $cat->name }}</strong>
                <small>{{ $cat->menu_items_count }} {{ Str::plural('item', $cat->menu_items_count) }}</small>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

</div>
@endsection
