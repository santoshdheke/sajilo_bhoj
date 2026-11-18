@extends($theme_path . 'common.layout.master_layout')

@push('css')
<style>
    .sf-hero { background: radial-gradient(circle at 80% 30%, color-mix(in srgb, var(--primary) 45%, transparent), transparent 55%), linear-gradient(135deg, var(--dark), #0b0b1a); min-height: 360px; }
    .sf-hero .carousel-item { min-height: 360px; }
    .sf-hero-img { max-height: 280px; object-fit: cover; }
    .sf-hero-ctl { width: 42px; height: 42px; top: 50%; transform: translateY(-50%); bottom: auto; opacity: 1; }
    .sf-hero .carousel-indicators [data-bs-target] { width: 9px; height: 9px; border-radius: 50%; border: 0; }
    .sf-hero .carousel-indicators .active { background: var(--primary); }
    .sf-cat-icon { width: 64px; height: 64px; }
    .sf-offer .carousel-item img { height: 170px; object-fit: contain; }
    /* Category flyout: the panel opens to the right of the menu, over the banner. */
    .sf-cat-menu { z-index: 20; }
    .sf-cat-panel { display: none; position: absolute; top: 0; left: 100%; min-height: 100%; width: 560px; max-width: 70vw; margin-left: 8px; background: #fff; z-index: 30; }
    .sf-cat-panel::before { content: ""; position: absolute; top: 0; bottom: 0; left: -8px; width: 8px; } /* hover bridge across the gap */
    .sf-cat-item:hover > .sf-cat-panel, .sf-cat-item:focus-within > .sf-cat-panel { display: block; }
    .sf-cat-item:hover > .list-group-item { background: var(--bs-tertiary-bg); color: var(--primary); }
    .sf-cat-link:hover { color: var(--primary) !important; }
</style>
@endpush

@section('contant')
@php
    $banners        = Modules\Shop\Models\Banner::active()->get();
    $specialOffers  = Modules\Shop\Models\SpecialOffer::active()->get();
    $sections       = Modules\Shop\Models\Section::with('sectionItems')->get();
    $categories     = Modules\Shop\Models\Category::where('parent_id', 0)->with(['children' => fn ($q) => $q->orderBy('title'), 'children.children' => fn ($q) => $q->orderBy('title')])->orderBy('title')->get();
    $latestProducts = Modules\Shop\Models\Product::with('productImage', 'category')->latest()->take(12)->get();
    $offerProduct   = $latestProducts->first(); // shown when there are no special offers
@endphp

<div class="container py-4">
    {{-- ════ HERO ROW: categories | banner | special offer ════ --}}
    <div class="row g-3 align-items-stretch">

        {{-- All categories --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card h-100 border-0 shadow-sm rounded-4 sf-cat-menu">
                <div class="card-header bg-brand border-0 fw-semibold d-flex align-items-center py-3 rounded-top-4">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> All Categories <i class="bi bi-chevron-right ms-auto small"></i>
                </div>
                <div class="list-group list-group-flush flex-grow-1">
                    @forelse ($categories->take(7) as $cat)
                        <div class="sf-cat-item">
                            <a href="{{ route('shop.front.category', $cat->slug) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 small py-2">
                                <i class="bi {{ $cat->icon }} text-secondary"></i>
                                <span class="text-truncate">{{ $cat->title }}</span>
                                @if ($cat->children->isNotEmpty())<i class="bi bi-chevron-right ms-auto small text-muted"></i>@endif
                            </a>
                            @if ($cat->children->isNotEmpty())
                                <div class="sf-cat-panel shadow rounded-4 p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">{{ $cat->title }}</h6>
                                        <a href="{{ route('shop.front.category', $cat->slug) }}" class="ms-auto small fw-semibold text-brand text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                    <div class="row row-cols-2 g-3">
                                        @foreach ($cat->children as $sub)
                                            <div class="col">
                                                <a href="{{ route('shop.front.category', $sub->slug) }}" class="d-block small fw-semibold text-body text-decoration-none sf-cat-link mb-1">{{ $sub->title }}</a>
                                                @foreach ($sub->children as $leaf)
                                                    <a href="{{ route('shop.front.category', $leaf->slug) }}" class="d-block small text-muted text-decoration-none sf-cat-link py-1">{{ $leaf->title }}</a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item small text-muted">No categories yet.</div>
                    @endforelse
                </div>
                <a href="{{ route('shop.front.store') }}" class="card-footer bg-brand-soft border-0 text-center small fw-semibold text-brand text-decoration-none py-2 rounded-bottom-4">
                    <i class="bi bi-grid me-1"></i> View All Products
                </a>
            </div>
        </div>

        {{-- Banner slider --}}
        <div class="col-lg-7">
            @php
                $slides = $banners->isNotEmpty()
                    ? $banners->map(fn ($b) => ['title' => $b->title ?: 'Shop Smart, Live Better', 'text' => $b->sub_title ?: 'Discover the best products at unbeatable prices.', 'image' => $b->image_path, 'url' => $b->url ?: route('shop.front.store')])
                    : collect([['title' => 'Shop Smart, Live Better', 'text' => 'Discover the best products at unbeatable prices.', 'image' => null, 'url' => route('shop.front.store')]]);
            @endphp
            <div id="heroCarousel" class="carousel slide sf-hero rounded-4 overflow-hidden shadow-sm h-100" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner h-100">
                    @foreach ($slides as $i => $slide)
                        @php
                            $words = preg_split('/\s+/', trim($slide['title']));
                            $last  = count($words) > 1 ? array_pop($words) : null;
                        @endphp
                        <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                            <div class="row h-100 align-items-center g-4 px-4 px-md-5 py-5">
                                <div class="col-md-7 text-white">
                                    <div class="small text-uppercase fw-bold text-brand mb-2" style="letter-spacing:.08em;">Welcome to {{ $shop->name }}</div>
                                    <h1 class="display-5 fw-bold lh-sm mb-3 text-white">{{ implode(' ', $words) }} @if ($last)<span class="text-brand">{{ $last }}</span>@endif</h1>
                                    <p class="text-white-50 mb-4">{{ $slide['text'] }}</p>
                                    <a href="{{ $slide['url'] }}" class="btn btn-brand btn-lg rounded-pill px-4"><i class="bi bi-cart3 me-2"></i>Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                                </div>
                                <div class="col-md-5 d-none d-md-block text-center">
                                    @if ($slide['image'])
                                        <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="img-fluid rounded-4 shadow sf-hero-img w-100">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10" style="width:220px;height:220px;">
                                            <i class="bi bi-shop text-brand" style="font-size:6rem;"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($slides->count() > 1)
                    <button class="carousel-control-prev sf-hero-ctl start-0 ms-3 rounded-circle bg-dark bg-opacity-50" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left fs-5"></i></button>
                    <button class="carousel-control-next sf-hero-ctl end-0 me-3 rounded-circle bg-dark bg-opacity-50" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><i class="bi bi-chevron-right fs-5"></i></button>
                    <div class="carousel-indicators mb-3">
                        @foreach ($slides as $i => $slide)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Special offer --}}
        <div class="col-lg-2 d-none d-lg-block">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden sf-offer">
                <div class="card-header bg-brand border-0 small fw-semibold py-3"><i class="bi bi-star-fill me-2"></i>Special Offer</div>
                @if ($specialOffers->isNotEmpty())
                    <div id="offerCarousel" class="carousel slide flex-grow-1" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner h-100">
                            @foreach ($specialOffers as $i => $offer)
                                <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                                    <div class="card-body text-center d-flex flex-column h-100">
                                        <img src="{{ $offer->image_path }}" alt="{{ $offer->title }}" class="img-fluid w-100 mb-3">
                                        @if ($offer->title)<div class="fw-bold small">{{ $offer->title }}</div>@endif
                                        @if ($offer->sub_title)<div class="small text-muted mb-2">{{ $offer->sub_title }}</div>@endif
                                        <a href="{{ $offer->url ?: route('shop.front.store') }}" class="btn btn-sm btn-outline-brand rounded-pill mt-auto">Shop Now <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif ($offerProduct)
                    <div class="card-body text-center d-flex flex-column">
                        <div class="position-relative">
                            <span class="position-absolute top-0 start-0 badge rounded-pill bg-brand">New</span>
                            <img src="{{ $offerProduct->image_path }}" alt="{{ $offerProduct->name }}" class="img-fluid w-100 mb-3" style="height:150px;object-fit:contain;">
                        </div>
                        <div class="fw-bold small">{{ $offerProduct->name }}</div>
                        <div class="small text-muted mb-2">{{ $offerProduct->category->title ?? '' }}</div>
                        <div class="fw-bold text-brand fs-5 mb-3">Rs. {{ number_format((float) $offerProduct->price) }}</div>
                        <a href="{{ route('shop.front.product', ['category' => $offerProduct->category->slug ?? 'product', 'product' => $offerProduct->slug]) }}" class="btn btn-sm btn-outline-brand rounded-pill mt-auto">Shop Now <i class="bi bi-arrow-right"></i></a>
                    </div>
                @else
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center text-muted small">
                        <i class="bi bi-gift fs-1 mb-2"></i>No special offers right now.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════ BROWSE BY CATEGORY ════ --}}
    @if ($categories->isNotEmpty())
        <section class="mt-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 fw-bold mb-0 border-start border-4 border-brand ps-2">Browse by Category</h2>
                <a href="{{ route('shop.front.store') }}" class="small fw-semibold text-brand text-decoration-none">All Products <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                @foreach ($categories as $cat)
                    <div class="col">
                        <a href="{{ route('shop.front.category', $cat->slug) }}" class="card h-100 border-0 shadow-sm rounded-4 text-center text-decoration-none p-3 sf-lift">
                            <span class="sf-cat-icon rounded-circle bg-brand-soft text-brand d-inline-flex align-items-center justify-content-center fs-3 mx-auto mb-2"><i class="bi {{ $cat->icon }}"></i></span>
                            <span class="small fw-semibold text-dark">{{ $cat->title }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════ PRODUCT ROWS: home sections (Admin → Home sections), else new arrivals ════ --}}
    @php
        // Every product added to a section, in order (the same product can be in it more than once).
        $icons = ['bi-collection-fill', 'bi-arrow-repeat', 'bi-lightning-charge-fill', 'bi-fire', 'bi-gem', 'bi-heart-fill'];
        $rows = $sections->values()->map(fn ($section, $i) => [
            'title' => $section->title, 'icon' => $icons[$i % count($icons)], 'url' => route('shop.front.section', $section),
            'products' => $section->products(), 'scroll' => $section->type !== 'multiple_line',
        ])->filter(fn ($row) => $row['products']->isNotEmpty())->values();

        $rows->prepend(['title' => 'New Arrivals', 'icon' => 'bi-stars', 'url' => route('shop.front.store', ['sort' => 'newest']), 'products' => $latestProducts, 'scroll' => false]);
    @endphp
    @foreach ($rows as $row)
        @continue($row['products']->isEmpty())
        <section class="mt-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 fw-bold mb-0 border-start border-4 border-brand ps-2"><i class="bi {{ $row['icon'] }} text-brand me-1"></i> {{ $row['title'] }}</h2>
                <a href="{{ $row['url'] }}" class="small fw-semibold text-brand text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            @if ($row['scroll'])
                {{-- single line: one row that scrolls sideways --}}
                <div class="row flex-nowrap g-3 overflow-auto pb-2 sf-row-scroll">
                    @foreach ($row['products'] as $product)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">@include($view_path . 'component.product_card', ['product' => $product])</div>
                    @endforeach
                </div>
            @else
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                    @foreach ($row['products'] as $product)
                        <div class="col">@include($view_path . 'component.product_card', ['product' => $product])</div>
                    @endforeach
                </div>
            @endif
        </section>
    @endforeach

    @if ($latestProducts->isEmpty())
        <div class="text-center text-muted py-5 mt-4"><i class="bi bi-box-seam fs-1 d-block mb-2"></i>Products are coming soon.</div>
    @endif
</div>
@endsection
