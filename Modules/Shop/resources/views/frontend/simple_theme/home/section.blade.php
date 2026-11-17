@extends($theme_path . 'common.layout.master_layout')

@push('css')
<style>
    /* ── Section hero ── */
    .sec-hero {
        background: #faf9f7;
        border-bottom: 1px solid #ebebeb;
        padding: 28px 0 22px;
    }
    .sec-breadcrumb {
        font-size: 12px; color: #bbb; margin-bottom: 6px;
    }
    .sec-breadcrumb a { color: #{{ $brand['primary'] }}; }
    .sec-breadcrumb a:hover { text-decoration: underline; color: #{{ $brand['primary'] }}; }
    .sec-breadcrumb span { margin: 0 6px; }

    .sec-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 26px; font-weight: 700; font-style: italic;
        color: #1a1a1a; margin: 0 0 4px;
    }
    .sec-count { font-size: 13px; color: #aaa; }

    /* ── Layout ── */
    .sec-layout {
        display: flex;
        gap: 32px;
        align-items: flex-start;
        padding: 32px 0 60px;
    }

    /* ── Sidebar ── */
    .sec-sidebar {
        flex: 0 0 220px;
        position: sticky;
        top: 80px;
    }

    .sec-sidebar-card {
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .sec-sidebar-head {
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        padding: 11px 16px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1.2px;
    }

    .sec-sidebar-list {
        list-style: none; padding: 8px 0; margin: 0;
    }
    .sec-sidebar-list li a {
        display: block;
        padding: 8px 16px;
        font-size: 13px; color: #555;
        border-left: 3px solid transparent;
        transition: all 0.15s;
    }
    .sec-sidebar-list li a:hover,
    .sec-sidebar-list li.active a {
        color: #{{ $brand['primary'] }};
        background: #{{ $brand['primary'] }}0d;
        border-left-color: #{{ $brand['primary'] }};
    }

    /* ── Main content ── */
    .sec-main { flex: 1; min-width: 0; }

    /* Sort bar */
    .sec-sortbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #ebebeb;
        flex-wrap: wrap; gap: 10px;
    }
    .sec-sortbar-left { font-size: 13px; color: #888; }
    .sec-sortbar-left strong { color: #222; }

    /* ── Empty state ── */
    .sec-empty {
        text-align: center;
        padding: 80px 20px;
        color: #ccc;
    }
    .sec-empty i { font-size: 48px; margin-bottom: 16px; display: block; }
    .sec-empty p { font-size: 15px; color: #bbb; }

    @media (max-width: 767px) {
        .sec-sidebar { display: none; }
        .sec-layout { padding: 20px 0 40px; }
    }
</style>
@endpush

@section('contant')

    {{-- Hero / breadcrumb --}}
    <div class="sec-hero">
        <div class="container">
            <div class="sec-breadcrumb">
                <a href="{{ route('shop.front.home') }}">Home</a>
                <span>&rsaquo;</span>
                <span>{{ $section->title }}</span>
            </div>
            <h1 class="sec-title">{{ $section->title }}</h1>
            @php
                $secProducts = $section->sectionItems()
                    ->where('sectionable_type', 'Modules\Shop\Models\Product')
                    ->with(['sectionable.category', 'sectionable.productImage'])
                    ->get()
                    ->filter(fn($i) => isset($i->sectionable) && isset($i->sectionable->category));
            @endphp
            <p class="sec-count">{{ $secProducts->count() }} product{{ $secProducts->count() !== 1 ? 's' : '' }}</p>
        </div>
    </div>

    <div class="container">
        <div class="sec-layout">

            {{-- Sidebar --}}
            <aside class="sec-sidebar">
                <div class="sec-sidebar-card">
                    <div class="sec-sidebar-head">Categories</div>
                    <ul class="sec-sidebar-list">
                        <li><a href="{{ route('shop.front.store') }}">All Products</a></li>
                        @foreach (Modules\Shop\Models\Category::take(8)->get() as $cat)
                            <li>
                                <a href="{{ route('shop.front.category', $cat->slug) }}">{{ $cat->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @php $allSections = Modules\Shop\Models\Section::take(6)->get(); @endphp
                @if ($allSections->count() > 1)
                    <div class="sec-sidebar-card">
                        <div class="sec-sidebar-head">Collections</div>
                        <ul class="sec-sidebar-list">
                            @foreach ($allSections as $s)
                                <li class="{{ $s->id === $section->id ? 'active' : '' }}">
                                    <a href="{{ route('shop.front.section', $s->page) }}">{{ $s->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>

            {{-- Products --}}
            <div class="sec-main">
                <div class="sec-sortbar">
                    <div class="sec-sortbar-left">
                        Showing <strong>{{ $secProducts->count() }}</strong> product{{ $secProducts->count() !== 1 ? 's' : '' }}
                    </div>
                </div>

                @if ($secProducts->count() > 0)
                    <div class="row" style="margin:0 -8px;">
                        @foreach ($secProducts as $sectionItem)
                            @php $product = $sectionItem->sectionable; @endphp
                            <div class="col-lg-3 col-sm-6 col-6" style="padding:0 8px 20px;">
                                @include($view_path . 'component.single_product')
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="sec-empty">
                        <i class="fas fa-box-open"></i>
                        <p>No products found in this collection.</p>
                        <a href="{{ route('shop.front.store') }}" class="btn-primary-own" style="margin-top:20px;display:inline-flex;">
                            Browse All Products
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection

@push('js')
@endpush
