@extends($theme_path . 'common.layout.master_layout')

@push('css')
<style>
    /* ═══════════════════════════
       STORE PAGE
    ═══════════════════════════ */
    .store-hero {
        background: #{{ $brand['secondary'] }}18;
        border-bottom: 1px solid #{{ $brand['primary'] }}22;
        padding: 28px 0 22px;
    }

    .store-breadcrumb {
        font-size: 12px;
        color: #999;
        margin-bottom: 6px;
    }

    .store-breadcrumb a { color: #{{ $brand['primary'] }}; }
    .store-breadcrumb a:hover { color: #{{ $brand['primary'] }}; text-decoration: underline; }
    .store-breadcrumb span { margin: 0 6px; color: #ccc; }

    .store-title {
        font-size: 26px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .store-count {
        font-size: 13px;
        color: #999;
        margin-top: 4px;
    }

    /* Layout */
    .store-layout {
        display: flex;
        gap: 32px;
        align-items: flex-start;
        padding: 32px 0 60px;
    }

    /* ── Sidebar ── */
    .store-sidebar {
        flex: 0 0 240px;
        width: 240px;
        position: sticky;
        top: 80px;
    }

    .sf-block {
        border-bottom: 1px solid #ebebeb;
        padding: 18px 0;
    }

    .sf-block:last-child { border-bottom: none; }

    .sf-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #1a1a1a;
        margin: 0 0 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .sf-title i { font-size: 10px; color: #aaa; transition: transform 0.2s; }
    .sf-title.open i { transform: rotate(180deg); }

    .sf-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sf-list li { margin-bottom: 2px; }

    .sf-list li a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        font-size: 13px;
        color: #444;
        border-radius: 6px;
        transition: all 0.15s;
        border-left: 3px solid transparent;
    }

    .sf-list li a:hover,
    .sf-list li.active a {
        background: #{{ $brand['primary'] }}12;
        color: #{{ $brand['primary'] }};
        border-left-color: #{{ $brand['primary'] }};
        padding-left: 14px;
    }

    .sf-count {
        font-size: 11px;
        color: #bbb;
        font-weight: 400;
    }

    .sf-list li.active .sf-count { color: #{{ $brand['primary'] }}88; }

    /* Clear filters link */
    .sf-clear {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #{{ $brand['primary'] }};
        margin-top: 4px;
        font-weight: 500;
    }

    .sf-clear:hover { text-decoration: underline; color: #{{ $brand['primary'] }}; }

    /* Mobile filter toggle */
    .filter-toggle-btn {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        background: #1a1a1a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }

    /* ── Toolbar ── */
    .store-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 18px;
        border-bottom: 2px solid #{{ $brand['primary'] }};
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .store-toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sort-label { font-size: 13px; color: #888; white-space: nowrap; }

    .sort-select {
        padding: 8px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        color: #1a1a1a;
        cursor: pointer;
        background: #fff;
        outline: none;
        transition: border-color 0.2s;
    }

    .sort-select:focus { border-color: #{{ $brand['primary'] }}; }

    /* Search pill */
    .active-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: #{{ $brand['primary'] }}15;
        color: #{{ $brand['primary'] }};
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #{{ $brand['primary'] }}40;
    }

    .active-filter-pill a {
        color: #{{ $brand['primary'] }};
        font-size: 14px;
        line-height: 1;
        margin-left: 2px;
    }

    /* ── Product grid ── */
    .store-grid { flex: 1; min-width: 0; }

    /* ── Toolbar count ── */
    .store-result-count {
        font-size: 13px; color: #888;
        white-space: nowrap;
    }

    /* ── Empty state ── */
    .store-empty {
        text-align: center;
        padding: 80px 20px;
        color: #bbb;
    }

    .store-empty i { font-size: 60px; margin-bottom: 20px; color: #e0e0e0; }
    .store-empty h3 { font-size: 20px; font-weight: 700; color: #444; margin-bottom: 10px; }
    .store-empty p { font-size: 14px; color: #999; margin-bottom: 24px; }

    .btn-browse {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        transition: opacity 0.2s;
    }

    .btn-browse:hover { opacity: 0.85; color: #{{ $brand['text'] }}; }

    /* Pagination */
    .store-pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .store-pagination .page-item .page-link {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        color: #444;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        font-family: inherit;
    }

    .store-pagination .page-item.active .page-link,
    .store-pagination .page-item .page-link:hover {
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        border-color: #{{ $brand['primary'] }};
    }

    /* ── Responsive ── */
    @media (max-width: 991px) {
        .store-sidebar {
            display: none;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 280px;
            background: #fff;
            z-index: 5000;
            padding: 24px 20px;
            overflow-y: auto;
            box-shadow: 4px 0 30px rgba(0,0,0,0.15);
        }

        .store-sidebar.open { display: block; }
        .filter-toggle-btn { display: inline-flex; }
        .store-layout { flex-direction: column; gap: 0; }
        .sidebar-overlay { display: none; position: fixed; inset:0; background:rgba(0,0,0,0.4); z-index:4999; }
        .sidebar-overlay.open { display: block; }
    }

    @media (max-width: 575px) {
        .store-grid .row > div { padding: 0; }
    }
</style>
@endpush

@section('contant')

<?php $attribute = request()->all(); ?>

<!-- Store Hero / Breadcrumb -->
<section class="store-hero">
    <div class="container">
        <div class="store-breadcrumb">
            <a href="{{ route('shop.front.home') }}">Home</a>
            <span>/</span>
            @if (request('category'))
                <a href="{{ route('shop.front.store') }}">Shop</a>
                <span>/</span>
                <strong style="color:#1a1a1a;">{{ request('category') }}</strong>
            @elseif (request('search'))
                <a href="{{ route('shop.front.store') }}">Shop</a>
                <span>/</span>
                <strong style="color:#1a1a1a;">Search: "{{ request('search') }}"</strong>
            @else
                <strong style="color:#1a1a1a;">Shop</strong>
            @endif
        </div>
        <h1 class="store-title">
            @if (request('search'))
                Results for "{{ request('search') }}"
            @elseif (request('category'))
                {{ ucwords(str_replace('-', ' ', request('category'))) }}
            @else
                All Products
            @endif
        </h1>
        @if (isset($products))
            <p class="store-count">{{ $products->count() }} product{{ $products->count() != 1 ? 's' : '' }} found</p>
        @endif
    </div>
</section>

<div class="container">
    <!-- Mobile filter overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="store-layout">

        <!-- ════════ SIDEBAR ════════ -->
        <aside class="store-sidebar" id="storeSidebar">

            <!-- Sidebar close (mobile) -->
            <div class="d-flex d-lg-none justify-content-between align-items-center mb-3">
                <span style="font-size:15px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">Filters</span>
                <button id="closeSidebar" style="background:none;border:none;font-size:20px;cursor:pointer;color:#444;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @if (request('category') || request('brand') || request('search'))
                <div class="sf-block" style="padding-top:0;">
                    <a href="{{ route('shop.front.store') }}" class="sf-clear">
                        <i class="fas fa-times-circle"></i> Clear all filters
                    </a>
                </div>
            @endif

            <!-- Categories -->
            <div class="sf-block">
                <div class="sf-title open" data-target="cat-list">
                    Categories <i class="fas fa-chevron-down"></i>
                </div>
                <ul class="sf-list" id="cat-list">
                    <li class="{{ !request('category') ? 'active' : '' }}">
                        <?php $allAttr = request()->except('category'); ?>
                        <a href="{{ route('shop.front.store', $allAttr) }}">
                            All Categories
                        </a>
                    </li>
                    @foreach (Modules\Shop\Models\Category::get() as $cat)
                        <?php $catAttr = request()->all(); $catAttr['category'] = $cat->slug; ?>
                        <li class="{{ $cat->slug == request('category') ? 'active' : '' }}">
                            <a href="{{ route('shop.front.store', $catAttr) }}">
                                {{ $cat->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Brands -->
            @php($brands = Modules\Shop\Models\Brand::all())
            @if ($brands->count() > 0)
                <div class="sf-block">
                    <div class="sf-title open" data-target="brand-list">
                        Brands <i class="fas fa-chevron-down"></i>
                    </div>
                    <ul class="sf-list" id="brand-list">
                        @foreach ($brands as $brand)
                            <?php $brandAttr = request()->all(); $brandAttr['brand'] = $brand->slug; ?>
                            <li class="{{ $brand->slug == request('brand') ? 'active' : '' }}">
                                <a href="{{ route('shop.front.store', $brandAttr) }}">
                                    {{ $brand->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </aside>
        <!-- /.store-sidebar -->

        <!-- ════════ MAIN GRID ════════ -->
        <div class="store-grid">

            <!-- Toolbar -->
            <div class="store-toolbar">
                <div class="store-toolbar-left">
                    <button class="filter-toggle-btn" id="openSidebar">
                        <i class="fas fa-sliders-h"></i> Filter
                    </button>

                    @if (isset($products))
                        <span class="store-result-count d-none d-lg-inline">
                            {{ $products->total() ?? $products->count() }} product{{ ($products->total() ?? $products->count()) != 1 ? 's' : '' }}
                        </span>
                    @endif

                    @if (request('search'))
                        <div class="active-filter-pill">
                            <i class="fas fa-search" style="font-size:10px;"></i>
                            "{{ request('search') }}"
                            <?php $clearSearch = request()->except('search'); ?>
                            <a href="{{ route('shop.front.store', $clearSearch) }}">&times;</a>
                        </div>
                    @endif

                    @if (request('category'))
                        <div class="active-filter-pill">
                            <i class="fas fa-tag" style="font-size:10px;"></i>
                            {{ ucwords(str_replace('-',' ', request('category'))) }}
                            <?php $clearCat = request()->except('category'); ?>
                            <a href="{{ route('shop.front.store', $clearCat) }}">&times;</a>
                        </div>
                    @endif

                    @if (request('brand'))
                        <div class="active-filter-pill">
                            <i class="fas fa-copyright" style="font-size:10px;"></i>
                            {{ request('brand') }}
                            <?php $clearBrand = request()->except('brand'); ?>
                            <a href="{{ route('shop.front.store', $clearBrand) }}">&times;</a>
                        </div>
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="sort-label">Sort:</span>
                    <select class="sort-select" onchange="window.location=this.value">
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>''])) }}"
                            {{ !request('sort') ? 'selected' : '' }}>Default</option>
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>'price_asc'])) }}"
                            {{ request('sort')=='price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>'price_desc'])) }}"
                            {{ request('sort')=='price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>'newest'])) }}"
                            {{ request('sort')=='newest' ? 'selected' : '' }}>Newest First</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            @if (isset($products) && $products->count() > 0)
                <div class="row" style="margin:0 -8px;">
                    @foreach ($products as $product)
                        <div class="col-xl-3 col-lg-4 col-sm-4 col-6" style="padding:0;">
                            @include($view_path . 'component.single_product')
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if (method_exists($products, 'links'))
                    <div class="store-pagination">
                        {{ $products->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </div>
                @endif

            @else
                <!-- Empty state -->
                <div class="store-empty">
                    <i class="fas fa-box-open"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your search or filter to find what you're looking for.</p>
                    <a href="{{ route('shop.front.store') }}" class="btn-browse">
                        <i class="fas fa-store"></i> Browse All Products
                    </a>
                </div>
            @endif

        </div>
        <!-- /.store-grid -->

    </div><!-- /.store-layout -->
</div>

@endsection

@push('js')
<script>
    // Sidebar filter accordion
    $('.sf-title').on('click', function () {
        const target = $('#' + $(this).data('target'));
        $(this).toggleClass('open');
        target.slideToggle(200);
    });

    // Mobile sidebar open/close
    $('#openSidebar').on('click', function () {
        $('#storeSidebar, #sidebarOverlay').addClass('open');
        $('body').css('overflow','hidden');
    });

    function closeSidebar() {
        $('#storeSidebar, #sidebarOverlay').removeClass('open');
        $('body').css('overflow','');
    }

    $('#closeSidebar, #sidebarOverlay').on('click', closeSidebar);
</script>
@endpush
