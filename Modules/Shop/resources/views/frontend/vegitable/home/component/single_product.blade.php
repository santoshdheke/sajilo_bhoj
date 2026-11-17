@if (isset($product->category))
@php
    $productImg = $product->image_path ?? '';
    $isNew = isset($product->created_at) && $product->created_at && $product->created_at->diffInDays() < 30;
@endphp

<a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
   class="eh-card-sm d-block">
    @if($isNew)
        <span class="eh-card-sm-badge badge-new">NEW</span>
    @endif
    <div class="eh-card-sm-img">
        <img src="{{ $productImg }}" alt="{{ $product->name }}">
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

<form action="{{ route('shop.front.cart.add') }}" method="post"
      id="veg_atc_{{ $product->id }}" style="display:none;">
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
