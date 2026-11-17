@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php
    $banners = Modules\Shop\Models\Banner::active()->get();
    $specialOffers = Modules\Shop\Models\SpecialOffer::active()->get();
    $sections = Modules\Shop\Models\Section::with(['sectionItems'])->get();
    $latestProducts  = Modules\Shop\Models\Product::latest()->take(8)->get();
    $featuredProducts = Modules\Shop\Models\Product::inRandomOrder()->take(8)->get();
    $popularProducts  = Modules\Shop\Models\Product::orderBy('id','desc')->skip(4)->take(8)->get();
    $categories = Modules\Shop\Models\Category::all();
@endphp

<div class="container py-3">
    <div class="row g-2" style="--bs-gutter-x: 15px; --bs-gutter-y: 10px;">

        <!-- ── Left sidebar: flyout categories ── -->
        <div class="col-lg-2 d-none d-lg-block">
            <div class="eh-sidebar-box eh-flyout-sidebar" id="ehCatSidebar">
                <div class="eh-sidebar-head" style="font-size:12px;padding:10px 14px;flex-shrink:0;">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> All Categories
                </div>
                <ul class="eh-flyout-list">
                    @foreach($categories as $cat)
                        <li class="eh-flyout-item">
                            <button class="eh-flyout-trigger">
                                <span class="trigger-label">{{ $cat->title }}</span>
                                <i class="bi bi-chevron-right trigger-arrow"></i>
                            </button>
                            <div class="eh-flyout-panel">
                                <div class="eh-flyout-links">
                                    @foreach(Modules\Shop\Models\Product::where('category_id', $cat->id)->take(12)->get() as $p)
                                        <a href="{{ route('shop.front.product', ['category' => $cat->slug, 'product' => $p->slug]) }}">{{ $p->name }}</a>
                                    @endforeach
                                </div>
                                <a href="{{ route('shop.front.category', ['category' => $cat->slug]) }}"
                                   class="eh-flyout-more">View All {{ $cat->title }} →</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="eh-flyout-footer">
                    <a href="{{ route('shop.front.store') }}">
                        <i class="bi bi-grid-3x3-gap"></i> View All Products
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Hero carousel ── -->
        <div class="col-12 col-lg-7">
            @if($banners->isNotEmpty())
                <div id="ehHero" class="carousel slide eh-hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        @foreach($banners as $i => $banner)
                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                <div class="eh-hero">
                                    <img src="{{ $banner->image_path }}" class="eh-hero-bg" alt="{{ $banner->title ?? $shop->name }}">
                                    <div class="eh-hero-overlay"></div>
                                    <div class="eh-hero-body">
                                        @if($banner->title)
                                            <div class="eh-hero-badge"><i class="bi bi-stars"></i> {{ $banner->title }}</div>
                                        @endif
                                        @if($banner->sub_title)
                                            <h1>{{ $banner->sub_title }}</h1>
                                        @else
                                            <h1>{{ $shop->name }}</h1>
                                        @endif
                                        <div class="eh-hero-actions">
                                            <a href="{{ route('shop.front.store') }}" class="eh-btn-primary">
                                                <i class="bi bi-bag"></i> Shop Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#ehHero" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#ehHero" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    <div class="carousel-indicators eh-hero-indicators">
                        @foreach($banners as $i => $banner)
                            <button type="button" data-bs-target="#ehHero" data-bs-slide-to="{{ $i }}"
                                    class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="eh-hero eh-hero--empty" style="background:var(--dark);border-radius:var(--radius);min-height:380px;">
                    <div class="eh-hero-body" style="width:100%;text-align:center;align-items:center;">
                        <h1 style="color:#fff;">{{ $shop->name }}</h1>
                        <p style="color:rgba(255,255,255,.7);">Discover our amazing products</p>
                        <div class="eh-hero-actions" style="justify-content:center;">
                            <a href="{{ route('shop.front.store') }}" class="eh-btn-primary"><i class="bi bi-bag"></i> Shop Now</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- ── Right: special offer slider ── -->
        <div class="col-12 col-lg-3 d-flex flex-column">
            <div class="eh-sidebar-box" id="ehOfferBox" style="flex:1;display:flex;flex-direction:column;margin-bottom:0;overflow:hidden;">
                <div class="eh-sidebar-head">
                    <i class="bi bi-gift-fill me-1"></i> Special Offer
                </div>
                @if($specialOffers->isNotEmpty())
                    <div id="ehOfferSlider" class="carousel slide eh-offer-slider" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner h-100">
                            @foreach($specialOffers as $i => $offer)
                                <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                                    @if($offer->url)
                                        <a href="{{ $offer->url }}" class="eh-offer-slide">
                                            <img src="{{ $offer->image_path }}" alt="{{ $offer->title ?? 'Special Offer' }}">
                                        </a>
                                    @else
                                        <div class="eh-offer-slide">
                                            <img src="{{ $offer->image_path }}" alt="{{ $offer->title ?? 'Special Offer' }}">
                                        </div>
                                    @endif
                                    @if($offer->title || $offer->sub_title)
                                        <div class="eh-offer-caption">
                                            @if($offer->title)<strong>{{ $offer->title }}</strong>@endif
                                            @if($offer->sub_title)<span>{{ $offer->sub_title }}</span>@endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($specialOffers->count() > 1)
                            <div class="carousel-indicators eh-hero-indicators">
                                @foreach($specialOffers as $i => $offer)
                                    <button type="button" data-bs-target="#ehOfferSlider" data-bs-slide-to="{{ $i }}"
                                            class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i+1 }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="eh-offer-empty">
                        <i class="bi bi-gift"></i>
                        <p>No special offers right now.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- ── New Arrivals ── -->
    @if($latestProducts->isNotEmpty())
    <div class="eh-extra-section">
        <div class="eh-extra-head">
            <h3><i class="bi bi-stars"></i> New Arrivals</h3>
            <a href="{{ route('shop.front.store') }}">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="eh-extra-body">
            <div class="eh-sc-wrap">
                <button class="eh-sc-btn eh-sc-prev" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <div class="eh-scroll-row eh-sc-track">
                    @foreach($latestProducts as $product)
                        <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                           class="eh-card-sm">
                            <span class="eh-card-sm-badge badge-new">NEW</span>
                            <div class="eh-card-sm-img">
                                <img src="{{ $product->image_path }}" alt="{{ $product->name }}">
                                <div class="eh-card-sm-overlay">
                                    <button class="eh-card-sm-atc" type="button"
                                            onclick="event.preventDefault(); document.getElementById('veg_atc_{{ $product->id }}').submit()">
                                        <i class="bi bi-bag-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                            <div class="eh-card-sm-info">
                                <div class="eh-card-sm-name">{{ $product->name }}</div>
                                <div class="eh-card-sm-meta">Rs. {{ number_format($product->price) }}</div>
                            </div>
                        </a>
                        <form action="{{ route('shop.front.cart.add') }}" method="POST"
                              id="veg_atc_{{ $product->id }}" style="display:none;">
                            @csrf
                            <input type="hidden" name="button_type" value="add_to_cart">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="product" value="{{ json_encode(['id'=>$product->id,'name'=>$product->name,'price'=>$product->price,'image'=>$product->image_path]) }}">
                        </form>
                    @endforeach
                </div>
                <button class="eh-sc-btn eh-sc-next" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
    @endif

    <!-- ── Section-based product rows ── -->
    @foreach($sections as $section)
        @php
            $sectionProducts = collect();
            foreach ($section->sectionItems as $item) {
                if ($item->sectionable_type === 'Modules\Shop\Models\Product') {
                    $p = Modules\Shop\Models\Product::find($item->sectionable_id);
                    if ($p) $sectionProducts->push($p);
                }
            }
        @endphp
        @if($sectionProducts->isNotEmpty())
        <div class="eh-extra-section">
            <div class="eh-extra-head">
                <h3><i class="bi bi-patch-check-fill"></i> {{ $section->title }}</h3>
                <a href="{{ route('shop.front.store') }}">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="eh-extra-body">
                <div class="eh-sc-wrap">
                    <button class="eh-sc-btn eh-sc-prev" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="eh-scroll-row eh-sc-track">
                        @foreach($sectionProducts as $product)
                            <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                               class="eh-card-sm">
                                <span class="eh-card-sm-badge badge-top">TOP</span>
                                <div class="eh-card-sm-img">
                                    <img src="{{ $product->image_path }}" alt="{{ $product->name }}">
                                    <div class="eh-card-sm-overlay">
                                        <button class="eh-card-sm-atc" type="button"
                                                onclick="event.preventDefault(); document.getElementById('veg_atc_{{ $product->id }}').submit()">
                                            <i class="bi bi-bag-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                                <div class="eh-card-sm-info">
                                    <div class="eh-card-sm-name">{{ $product->name }}</div>
                                    <div class="eh-card-sm-meta">Rs. {{ number_format($product->price) }}</div>
                                </div>
                            </a>
                            <form action="{{ route('shop.front.cart.add') }}" method="POST"
                                  id="veg_atc_{{ $product->id }}" style="display:none;">
                                @csrf
                                <input type="hidden" name="button_type" value="add_to_cart">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="product" value="{{ json_encode(['id'=>$product->id,'name'=>$product->name,'price'=>$product->price,'image'=>$product->image_path]) }}">
                            </form>
                        @endforeach
                    </div>
                    <button class="eh-sc-btn eh-sc-next" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <!-- ── Featured Products (fallback row if no sections) ── -->
    @if($sections->isEmpty() && $featuredProducts->isNotEmpty())
    <div class="eh-extra-section">
        <div class="eh-extra-head">
            <h3><i class="bi bi-fire"></i> Featured Products</h3>
            <a href="{{ route('shop.front.store') }}">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="eh-extra-body">
            <div class="eh-sc-wrap">
                <button class="eh-sc-btn eh-sc-prev" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <div class="eh-scroll-row eh-sc-track">
                    @foreach($featuredProducts as $product)
                        <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                           class="eh-card-sm">
                            <span class="eh-card-sm-badge badge-hot">HOT</span>
                            <div class="eh-card-sm-img">
                                <img src="{{ $product->image_path }}" alt="{{ $product->name }}">
                                <div class="eh-card-sm-overlay">
                                    <button class="eh-card-sm-atc" type="button"
                                            onclick="event.preventDefault(); document.getElementById('veg_atc_{{ $product->id }}').submit()">
                                        <i class="bi bi-bag-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                            <div class="eh-card-sm-info">
                                <div class="eh-card-sm-name">{{ $product->name }}</div>
                                <div class="eh-card-sm-meta">Rs. {{ number_format($product->price) }}</div>
                            </div>
                        </a>
                        <form action="{{ route('shop.front.cart.add') }}" method="POST"
                              id="veg_atc_{{ $product->id }}" style="display:none;">
                            @csrf
                            <input type="hidden" name="button_type" value="add_to_cart">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="product" value="{{ json_encode(['id'=>$product->id,'name'=>$product->name,'price'=>$product->price,'image'=>$product->image_path]) }}">
                        </form>
                    @endforeach
                </div>
                <button class="eh-sc-btn eh-sc-next" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
    @endif

    <!-- ── Category grid ── -->
    @if($categories->isNotEmpty())
    <div class="eh-extra-section">
        <div class="eh-extra-head">
            <h3><i class="bi bi-grid-3x3-gap-fill"></i> Browse by Category</h3>
            <a href="{{ route('shop.front.store') }}">All Products <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="eh-extra-body">
            <div class="row g-2">
                @foreach($categories as $cat)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('shop.front.category', ['category' => $cat->slug]) }}" class="eh-cat-card d-block">
                            <div class="eh-cat-icon">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                            <h6>{{ $cat->title }}</h6>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
