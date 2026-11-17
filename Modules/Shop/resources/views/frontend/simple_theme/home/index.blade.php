@extends($theme_path . 'common.layout.master_layout')

@push('meta')
@endpush

@push('css')
<style>
    /* ══ HERO — full-width overlay ══ */
    .hero-section {
        position: relative;
        overflow: hidden;
        background: #1a1a1a;
    }

    .hero-slide {
        position: relative;
        height: 580px;
        display: flex;
        align-items: center;
    }

    .hero-bg-img {
        position: absolute;
        inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        object-position: center 20%;
        display: block;
    }

    .hero-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(
            to right,
            rgba(0,0,0,0.65) 0%,
            rgba(0,0,0,0.32) 50%,
            rgba(0,0,0,0.05) 100%
        );
    }

    .hero-content {
        position: relative; z-index: 2;
        max-width: 560px; padding: 0 20px;
    }

    .hero-eyebrow {
        font-size: 11px; font-weight: 600;
        color: rgba(255,255,255,0.65);
        text-transform: uppercase; letter-spacing: 3px;
        margin-bottom: 16px; display: block;
    }

    .hero-heading {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(38px, 5vw, 64px);
        font-weight: 700; font-style: italic;
        color: #ffffff;
        line-height: 1.08;
        margin-bottom: 16px;
        letter-spacing: -1px;
    }

    .hero-desc {
        font-size: 14px; color: rgba(255,255,255,0.68);
        line-height: 1.75; margin-bottom: 32px;
        max-width: 400px;
    }

    .hero-cta-wrap { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }

    .hero-cta {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 34px;
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        font-size: 11.5px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 2px;
        border: 2px solid #{{ $brand['primary'] }};
        transition: all 0.22s;
    }
    .hero-cta:hover {
        background: transparent;
        color: #ffffff; border-color: #ffffff;
    }
    .hero-cta i { font-size: 10px; }

    .hero-cta-ghost {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.8);
        text-transform: uppercase; letter-spacing: 1.5px;
        border-bottom: 1px solid rgba(255,255,255,0.4);
        padding-bottom: 2px; transition: color 0.2s, border-color 0.2s;
    }
    .hero-cta-ghost:hover { color: #fff; border-color: #fff; }

    /* Custom carousel controls */
    .hero-ctl {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.2);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 14px; cursor: pointer; z-index: 10;
        transition: background 0.2s; text-decoration: none;
        opacity: 1 !important;
    }
    .hero-ctl:hover { background: rgba(255,255,255,0.3); color: #fff; }
    .hero-ctl-prev { left: 22px; }
    .hero-ctl-next { right: 22px; }

    /* Dot indicators */
    .hero-dots {
        position: absolute; bottom: 22px; left: 50%;
        transform: translateX(-50%);
        display: flex; gap: 8px; z-index: 10;
    }
    .hero-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: rgba(255,255,255,0.45);
        cursor: pointer; transition: all 0.22s;
        border: none; padding: 0;
    }
    .hero-dot.active { background: #fff; width: 26px; border-radius: 50px; }

    .hero-section .carousel-control-prev,
    .hero-section .carousel-control-next,
    .hero-section .carousel-indicators { display: none; }

    /* ══ FEATURE STRIP ══ */
    .feature-strip {
        background: #fff;
        border-top: 1px solid #ebebeb;
        border-bottom: 1px solid #ebebeb;
        padding: 22px 0;
    }
    .feature-item { display: flex; align-items: center; gap: 14px; padding: 0 8px; }
    .feature-icon {
        width: 44px; height: 44px; border-radius: 50%;
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }
    .feature-text h6 { font-size: 13px; font-weight: 700; margin: 0; color: #222; }
    .feature-text p  { font-size: 11px; color: #999; margin: 0; }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 767px) {
        .hero-slide { height: 420px; }
        .hero-heading { font-size: 32px; }
        .hero-desc { display: none; }
        .feature-item { margin-bottom: 16px; }
    }
    @media (max-width: 480px) {
        .hero-slide { height: 340px; }
        .hero-heading { font-size: 26px; }
        .hero-eyebrow { display: none; }
    }
</style>
@endpush

@section('contant')

    <!-- ════ HERO ════ -->
    <section class="hero-section">
        @php
            $banners  = Modules\Shop\Models\Banner::active()->get();
            $fallback = [
                asset('shop/frontend/simple_theme/img/carousel-1.jpg'),
                asset('shop/frontend/simple_theme/img/carousel-2.jpg'),
            ];
            $slides = [];
            if ($banners->count() > 0) {
                foreach ($banners as $bi => $bn) {
                    $slides[] = [
                        'eyebrow' => 'New Collection',
                        'heading' => ($bn->title    ?: 'Timeless Beauty'),
                        'desc'    => ($bn->sub_title ?: 'Discover our curated collection crafted for the modern woman.'),
                        'img'     => ($bn->image_path && $bn->image_path !== '')
                                        ? $bn->image_path
                                        : $fallback[$bi % 2],
                        'url'     => ($bn->url ?: route('shop.front.store')),
                    ];
                }
            } else {
                $slides = [
                    ['eyebrow' => 'New Arrivals', 'heading' => 'Timeless Beauty',     'desc' => 'Discover premium collections crafted for the modern woman.', 'img' => $fallback[0], 'url' => route('shop.front.store')],
                    ['eyebrow' => 'New Season',   'heading' => 'Effortless Elegance', 'desc' => 'Explore styles that blend comfort with contemporary design.', 'img' => $fallback[1], 'url' => route('shop.front.store')],
                ];
            }
        @endphp

        <div id="heroCarousel" class="carousel slide" data-ride="carousel" data-interval="5000">
            <div class="carousel-inner">
                @foreach ($slides as $key => $slide)
                    <div class="carousel-item{{ $key == 0 ? ' active' : '' }}">
                        <div class="hero-slide">
                            <img class="hero-bg-img" src="{{ $slide['img'] }}" alt="{{ $slide['heading'] }}">
                            <div class="hero-overlay"></div>
                            <div class="container">
                                <div class="hero-content">
                                    <span class="hero-eyebrow">{{ $slide['eyebrow'] }}</span>
                                    <h1 class="hero-heading">{{ $slide['heading'] }}</h1>
                                    <p class="hero-desc">{{ $slide['desc'] }}</p>
                                    <div class="hero-cta-wrap">
                                        <a href="{{ $slide['url'] }}" class="hero-cta">
                                            Shop Now <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <a href="{{ route('shop.front.store') }}" class="hero-cta-ghost">
                                            View All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Custom controls -->
        <a href="#heroCarousel" class="hero-ctl hero-ctl-prev" data-slide="prev">
            <i class="fas fa-chevron-left"></i>
        </a>
        <a href="#heroCarousel" class="hero-ctl hero-ctl-next" data-slide="next">
            <i class="fas fa-chevron-right"></i>
        </a>

        <!-- Dots -->
        <div class="hero-dots" id="heroDots">
            @foreach ($slides as $key => $slide)
                <button class="hero-dot{{ $key == 0 ? ' active' : '' }}"
                        data-slide-to="{{ $key }}" data-target="#heroCarousel"></button>
            @endforeach
        </div>
    </section>

    <!-- ════ FEATURE STRIP ════ -->
    <section class="feature-strip">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-truck"></i></div>
                        <div class="feature-text">
                            <h6>Free Delivery</h6>
                            <p>On orders over Rs.1000</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-undo-alt"></i></div>
                        <div class="feature-text">
                            <h6>Easy Returns</h6>
                            <p>7 days return policy</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="feature-text">
                            <h6>Secure Payment</h6>
                            <p>100% secure checkout</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-headset"></i></div>
                        <div class="feature-text">
                            <h6>24/7 Support</h6>
                            <p>Dedicated customer care</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ════ PRODUCT SECTIONS ════ -->
    @php $counter = 0; @endphp
    @foreach (Modules\Shop\Models\Section::get() as $section)
        @if (count($section->sectionItems) > 0)
            @php
                $counter++;
                $path = $section->type ?? 'single_line';
            @endphp
            @include($view_path . 'component.section.' . $path)
        @endif
    @endforeach

    @if ($counter === 0)
        @php
            $featuredProducts = Modules\Shop\Models\Product::where('company_id', $shop->id)
                ->with(['productImage', 'category'])
                ->whereHas('category')
                ->latest()
                ->take(8)
                ->get();
        @endphp

        @if ($featuredProducts->count() > 0)
            <section class="product-section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">New Arrivals</h2>
                        <a class="section-link" href="{{ route('shop.front.store') }}">View All &rarr;</a>
                    </div>
                    <div class="row" style="margin:0 -8px;">
                        @foreach ($featuredProducts as $product)
                            <div class="col-lg-3 col-sm-6 col-6" style="padding:0 8px 20px;">
                                @include($view_path . 'component.single_product')
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endif

    <div style="height:20px;"></div>

@endsection

@push('js')
<script>
    $(document).ready(function () {

        $('#heroCarousel').on('slid.bs.carousel', function (e) {
            var idx = $(e.relatedTarget).index();
            $('#heroDots .hero-dot').removeClass('active');
            $('#heroDots .hero-dot').eq(idx).addClass('active');
        });

        $(".owl-carousel-four").owlCarousel({
            autoPlay: 3000, items: 6, nav: true, loop: true,
            responsive: { 0: { items: 2 }, 600: { items: 4 }, 992: { items: 6 } }
        });
        $(".owl-carousel-three").owlCarousel({
            autoPlay: 4000, items: 5, nav: true, loop: true,
            responsive: { 0: { items: 2 }, 600: { items: 3 }, 992: { items: 5 } }
        });
        $(".owl-carousel-five").owlCarousel({
            autoPlay: 3200, items: 5, nav: true, loop: true,
            responsive: { 0: { items: 2 }, 600: { items: 3 }, 992: { items: 5 } }
        });
    });
</script>
@endpush
