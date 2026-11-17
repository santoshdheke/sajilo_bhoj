@extends($theme_path . 'common.layout.master_layout')

@push('meta')
    <meta property="og:title"       content="{{ $product->name }}" />
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 160) }}" />
    <meta property="og:image"       content="{{ $product->image_path }}" />
    <meta property="og:url"         content="{{ route('shop.front.product', ['category' => $product->category->slug, 'product' => $product->slug]) }}" />
    <meta property="og:type"        content="website" />
@endpush

@push('css')
<style>
    /* ── Page wrapper ── */
    .pd-page { padding: 0 0 64px; }

    /* ── Breadcrumb ── */
    .pd-breadcrumb {
        padding: 14px 0;
        font-size: 12px;
        color: #999;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 36px;
    }
    .pd-breadcrumb a { color: #{{ $brand['primary'] }}; }
    .pd-breadcrumb a:hover { text-decoration: underline; color: #{{ $brand['primary'] }}; }
    .pd-breadcrumb span { margin: 0 7px; color: #ddd; }

    /* ── Main product grid ── */
    .pd-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 56px;
        align-items: start;
    }
    @media (max-width: 768px) {
        .pd-grid { grid-template-columns: 1fr; gap: 28px; }
    }

    /* ── Gallery ── */
    .pd-gallery { position: sticky; top: 90px; }

    .pd-main-img-wrap {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        background: #f4f4f4;
        aspect-ratio: 1 / 1;
        border: 1px solid #ebebeb;
    }
    .pd-main-img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .pd-main-img-wrap:hover .pd-main-img { transform: scale(1.04); }

    .pd-thumbs {
        display: flex;
        gap: 10px;
        margin-top: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
    }
    .pd-thumbs::-webkit-scrollbar { height: 3px; }
    .pd-thumbs::-webkit-scrollbar-thumb { background: #{{ $brand['primary'] }}55; border-radius: 3px; }

    .pd-thumb {
        flex-shrink: 0;
        width: 72px; height: 72px;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        transition: border-color 0.15s;
        background: #f4f4f4;
    }
    .pd-thumb img {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
    }
    .pd-thumb.active { border-color: #{{ $brand['primary'] }}; }
    .pd-thumb:hover { border-color: #{{ $brand['primary'] }}88; }

    /* ── Product info ── */
    .pd-info { }

    .pd-category-tag {
        display: inline-block;
        font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 1.5px;
        color: #{{ $brand['primary'] }};
        margin-bottom: 10px;
    }
    .pd-category-tag:hover { color: #{{ $brand['primary'] }}; text-decoration: underline; }

    .pd-name {
        font-size: 26px; font-weight: 700;
        color: #1a1a1a; line-height: 1.25;
        margin: 0 0 16px;
        letter-spacing: -0.3px;
    }

    .pd-price {
        font-size: 28px; font-weight: 800;
        color: #{{ $brand['primary'] }};
        margin-bottom: 20px;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .pd-price .currency {
        font-size: 16px; font-weight: 600;
        color: #{{ $brand['primary'] }};
    }

    .pd-divider { height: 1px; background: #f0f0f0; margin: 20px 0; }

    /* ── Attributes ── */
    .pd-attrs { margin-bottom: 20px; }
    .pd-attr-group { margin-bottom: 16px; }
    .pd-attr-label {
        font-size: 12px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.8px;
        color: #888; margin-bottom: 8px;
    }
    .pd-attr-select {
        width: 100%; height: 42px; padding: 0 14px;
        border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 14px; color: #1a1a1a; background: #fff;
        outline: none; cursor: pointer;
        transition: border-color 0.15s;
    }
    .pd-attr-select:focus { border-color: #{{ $brand['primary'] }}; }
    .pd-attr-input {
        width: 100%; height: 42px; padding: 0 14px;
        border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 14px; color: #1a1a1a; background: #fff;
        outline: none; transition: border-color 0.15s;
    }
    .pd-attr-input:focus { border-color: #{{ $brand['primary'] }}; }

    /* ── Quantity stepper ── */
    .pd-qty-label {
        font-size: 12px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.8px;
        color: #888; margin-bottom: 8px;
    }
    .pd-qty-wrap {
        display: inline-flex;
        align-items: center;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        height: 42px;
    }
    .pd-qty-btn {
        width: 42px; height: 42px;
        background: #f6f6f6; border: none;
        font-size: 18px; font-weight: 400;
        color: #444; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.12s;
    }
    .pd-qty-btn:hover { background: #ebebeb; }
    .pd-qty-input {
        width: 56px; text-align: center;
        border: none; border-left: 1.5px solid #e0e0e0; border-right: 1.5px solid #e0e0e0;
        font-size: 15px; font-weight: 600; color: #1a1a1a;
        outline: none; height: 42px;
        -moz-appearance: textfield;
    }
    .pd-qty-input::-webkit-outer-spin-button,
    .pd-qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }

    /* ── Action buttons ── */
    .pd-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 20px;
    }
    .btn-pd-cart {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 14px; border-radius: 8px;
        background: #1a1a1a; color: #fff;
        border: 2px solid #1a1a1a;
        font-size: 13px; font-weight: 700;
        letter-spacing: 0.5px; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-pd-cart:hover { background: #333; border-color: #333; color: #fff; }

    .btn-pd-buy {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 14px; border-radius: 8px;
        background: #{{ $brand['primary'] }}; color: #{{ $brand['text'] }};
        border: 2px solid #{{ $brand['primary'] }};
        font-size: 13px; font-weight: 700;
        letter-spacing: 0.5px; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-pd-buy:hover { opacity: 0.88; }

    /* ── Share row ── */
    .pd-share {
        display: flex; align-items: center; gap: 10px;
        margin-top: 20px; font-size: 12px; color: #999;
    }
    .pd-share a {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #f4f4f4; color: #555; font-size: 13px;
        transition: background 0.15s, color 0.15s;
        text-decoration: none;
    }
    .pd-share a.fb:hover { background: #1877f2; color: #fff; }
    .pd-share a.wa:hover { background: #25D366; color: #fff; }

    /* ── Description tabs ── */
    .pd-tabs { margin-top: 56px; }
    .pd-tab-nav {
        display: flex;
        border-bottom: 2px solid #ebebeb;
        gap: 0;
        margin-bottom: 28px;
    }
    .pd-tab-btn {
        padding: 10px 22px;
        font-size: 13px; font-weight: 600;
        color: #888; background: none; border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer; transition: color 0.15s, border-color 0.15s;
    }
    .pd-tab-btn.active { color: #{{ $brand['primary'] }}; border-bottom-color: #{{ $brand['primary'] }}; }
    .pd-tab-pane { display: none; font-size: 14px; line-height: 1.8; color: #444; }
    .pd-tab-pane.active { display: block; }

    /* ── Related products ── */
    .pd-related { margin-top: 64px; }
    .pd-related-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 24px; padding-bottom: 14px;
        border-bottom: 2px solid #{{ $brand['primary'] }};
    }
    .pd-related-title {
        font-size: 20px; font-weight: 700; color: #1a1a1a;
        font-style: italic;
    }
    .pd-related-link {
        font-size: 12px; font-weight: 600;
        color: #{{ $brand['primary'] }};
        text-transform: uppercase; letter-spacing: 0.8px;
    }
    .pd-related-link:hover { text-decoration: underline; color: #{{ $brand['primary'] }}; }

    /* ── Mobile ── */
    @media (max-width: 575px) {
        .pd-name  { font-size: 20px; }
        .pd-price { font-size: 22px; }
        .pd-actions { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('contant')

@php
    $pFallback       = asset('shop/frontend/simple_theme/img/product-' . (($product->id % 28) + 1) . '.jpg');
    $mainImg         = ($product->image_path && $product->image_path !== '') ? $product->image_path : $pFallback;
    $inputAbleAttrs  = $product->category->attributes;
    $relatedProducts = Modules\Shop\Models\Product::where('category_id', $product->category_id)
                          ->where('id', '!=', $product->id)
                          ->limit(6)->get();
@endphp

<div class="pd-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="pd-breadcrumb">
            <a href="{{ route('shop.front.home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('shop.front.store') }}">Shop</a>
            <span>/</span>
            <a href="{{ route('shop.front.category', $product->category->slug) }}">{{ $product->category->title }}</a>
            <span>/</span>
            <strong style="color:#1a1a1a;">{{ $product->name }}</strong>
        </nav>

        {{-- Product grid --}}
        <div class="pd-grid">

            {{-- ── Gallery ── --}}
            <div class="pd-gallery">
                <div class="pd-main-img-wrap">
                    <img class="pd-main-img" id="pd-selected-img"
                         src="{{ $mainImg }}"
                         alt="{{ $product->name }}"
                         onerror="this.onerror=null;this.src='{{ $pFallback }}'">
                </div>

                @if(isset($product->productImages) && $product->productImages->count() > 0)
                    <div class="pd-thumbs">
                        @foreach($product->productImages as $key => $pi)
                            @php $thumbFb = asset('shop/frontend/simple_theme/img/product-' . ((($product->id + $key) % 28) + 1) . '.jpg'); @endphp
                            <div class="pd-thumb {{ $key == 0 ? 'active' : '' }}"
                                 data-src="{{ $pi->image_path ?: $thumbFb }}">
                                <img src="{{ $pi->image_path ?: $thumbFb }}"
                                     alt="{{ $product->name }}"
                                     onerror="this.onerror=null;this.src='{{ $thumbFb }}'">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Info ── --}}
            <div class="pd-info">
                <a href="{{ route('shop.front.category', $product->category->slug) }}" class="pd-category-tag">
                    {{ $product->category->title }}
                </a>

                <h1 class="pd-name">{{ $product->name }}</h1>

                <div class="pd-price">
                    <span class="currency">Rs.</span>
                    {{ number_format($product->price) }}
                </div>

                @if($product->description)
                    <p style="font-size:14px;color:#666;line-height:1.7;margin-bottom:0">
                        {!! nl2br(e($product->description)) !!}
                    </p>
                @endif

                <div class="pd-divider"></div>

                <form action="{{ route('shop.front.cart.add') }}" method="post">
                    @csrf
                    <input name="product" type="hidden" value="{{ json_encode([
                        'id'    => $product->id,
                        'name'  => $product->name,
                        'price' => $product->price,
                        'image' => $mainImg,
                    ]) }}">

                    {{-- Attributes --}}
                    @if(isset($inputAbleAttrs) && $inputAbleAttrs->count() > 0)
                        <div class="pd-attrs">
                            @foreach($inputAbleAttrs as $attribute)
                                @if($attribute->input_type === 'select')
                                    @if(isset($attribute->productAttribute->productAttributeValues) && $attribute->productAttribute->productAttributeValues->count() > 0)
                                        <div class="pd-attr-group">
                                            <div class="pd-attr-label">{{ $attribute->attribute_title }}</div>
                                            <select class="pd-attr-select"
                                                name="attribute[{{ $attribute->id }}][{{ $attribute->attribute_title }}]">
                                                <option value="">Choose {{ $attribute->attribute_title }}</option>
                                                @foreach($attribute->productAttribute->productAttributeValues as $val)
                                                    @if($val->value !== '')
                                                        <option value="{{ $val->value }}">{{ $val->value }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                @else
                                    <div class="pd-attr-group">
                                        <div class="pd-attr-label">{{ $attribute->attribute_title }}</div>
                                        <input class="pd-attr-input"
                                               type="{{ $attribute->input_type }}"
                                               name="attribute[{{ $attribute->id }}][{{ $attribute->attribute_title }}]"
                                               placeholder="{{ $attribute->attribute_title }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Quantity --}}
                    <div style="margin-bottom:20px">
                        <div class="pd-qty-label">Quantity</div>
                        <div class="pd-qty-wrap">
                            <button type="button" class="pd-qty-btn" onclick="changeQty(-1)">−</button>
                            <input class="pd-qty-input" type="number" name="quantity" id="qty-input" value="1" min="1">
                            <button type="button" class="pd-qty-btn" onclick="changeQty(1)">+</button>
                        </div>
                    </div>

                    {{-- CTA buttons --}}
                    <div class="pd-actions">
                        <button type="submit" name="button_type" value="add_to_cart" class="btn-pd-cart">
                            <i class="fas fa-shopping-bag"></i> Add to Cart
                        </button>
                        <button type="submit" name="button_type" value="order_now" class="btn-pd-buy">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </div>
                </form>

                {{-- Share --}}
                <div class="pd-share">
                    <span>Share:</span>
                    <a class="fb" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('shop.front.product', ['category' => $product->category->slug, 'product' => $product->slug])) }}"
                       target="_blank" title="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a class="wa" href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('shop.front.product', ['category' => $product->category->slug, 'product' => $product->slug])) }}"
                       target="_blank" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Description tabs --}}
        @if($product->description || $product->long_description)
            <div class="pd-tabs">
                <div class="pd-tab-nav">
                    @if($product->description)
                        <button class="pd-tab-btn active" onclick="switchTab('desc', this)">Description</button>
                    @endif
                    @if($product->long_description)
                        <button class="pd-tab-btn {{ !$product->description ? 'active' : '' }}"
                            onclick="switchTab('long', this)">Details</button>
                    @endif
                </div>
                @if($product->description)
                    <div class="pd-tab-pane active" id="tab-desc">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif
                @if($product->long_description)
                    <div class="pd-tab-pane {{ !$product->description ? 'active' : '' }}" id="tab-long">
                        {!! nl2br(e($product->long_description)) !!}
                    </div>
                @endif
            </div>
        @endif

        {{-- Related products --}}
        @if($relatedProducts->count() > 0)
            <div class="pd-related">
                <div class="pd-related-header">
                    <span class="pd-related-title">You may also like</span>
                    <a href="{{ route('shop.front.category', $product->category->slug) }}" class="pd-related-link">
                        View all <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                    </a>
                </div>
                <div class="row" style="margin:0 -8px;">
                    @foreach($relatedProducts as $rp)
                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6" style="padding:0;">
                            @include($view_path . 'component.single_product', ['product' => $rp])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

@endsection

@push('js')
<script>
    // Thumbnail switcher
    $('.pd-thumb').on('click', function () {
        $('.pd-thumb').removeClass('active');
        $(this).addClass('active');
        $('#pd-selected-img').attr('src', $(this).data('src'));
    });

    // Quantity stepper
    function changeQty(delta) {
        const input = document.getElementById('qty-input');
        const val   = Math.max(1, parseInt(input.value || 1) + delta);
        input.value = val;
    }

    // Description tabs
    function switchTab(name, btn) {
        document.querySelectorAll('.pd-tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.pd-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }
</script>
@endpush
