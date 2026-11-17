@if (isset($product->category))
@php
    $fallbackImg  = asset('shop/frontend/simple_theme/img/product-' . (($product->id % 28) + 1) . '.jpg');
    $productImg   = ($product->image_path && $product->image_path !== '') ? $product->image_path : $fallbackImg;
    $isNew        = isset($product->created_at) && $product->created_at && $product->created_at->diffInDays() < 30;
@endphp

<div class="pc-wrap">
    <a href="{{ route('shop.front.product', ['category' => $product->category->slug, 'product' => $product->slug]) }}"
       class="pc-link">
        <div class="pc">

            {{-- ── Image area ── --}}
            <div class="pc-img-wrap">
                <img class="pc-img"
                     src="{{ $productImg }}"
                     alt="{{ $product->name }}"
                     onerror="this.onerror=null;this.src='{{ $fallbackImg }}'">

                {{-- Badge --}}
                @if($isNew)
                    <span class="pc-badge badge-new">New</span>
                @endif

                {{-- Wishlist --}}
                <button class="pc-wish-btn" onclick="event.preventDefault();" title="Save">
                    <i class="far fa-heart"></i>
                </button>

                {{-- Hover CTA overlay --}}
                <div class="pc-hover-overlay">
                    <button class="pc-pill-btn"
                            onclick="event.preventDefault(); document.getElementById('pc_atc_{{ $product->id }}').submit()">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                </div>
            </div>

            {{-- ── Below-image info (always visible) ── --}}
            <div class="pc-below">
                <div class="pc-below-cat">{{ $product->category->title }}</div>
                <p class="pc-below-name">{{ $product->name }}</p>
                <div class="pc-below-price">
                    <span class="pc-below-currency">Rs.</span>
                    {{ number_format($product->price) }}
                </div>
            </div>

        </div>
    </a>
</div>

<form action="{{ route('shop.front.cart.add') }}" method="post"
      id="pc_atc_{{ $product->id }}" style="display:none;">
    @csrf
    <input name="product" type="hidden" value="{{ json_encode([
        'id'    => $product->id,
        'name'  => $product->name,
        'price' => $product->price,
        'image' => $productImg,
    ]) }}">
    <input name="quantity" type="hidden" value="1">
    <input name="button_type" type="hidden" value="add_to_cart">
</form>
@endif
