{{-- Product card (home rows, store, category, section, related). Params: $product, optional $badge (text), $badgeClass, $outline (outlined button). --}}
@php
    $url   = route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]);
    $badge = $badge ?? ($product->created_at && $product->created_at->diffInDays() < 30 ? 'New' : null);
@endphp
<div class="sf-pcard card h-100 border-0 rounded-4">
    <div class="position-relative">
        <a href="{{ $url }}" class="sf-pcard-img d-block ratio ratio-1x1 rounded-4 overflow-hidden">
            @if ($product->image_path)
                <img src="{{ $product->image_path }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <span class="d-flex align-items-center justify-content-center text-secondary fs-1"><i class="bi bi-image"></i></span>
            @endif
        </a>
        @if ($badge)
            <span class="sf-pcard-badge badge rounded-pill {{ $badgeClass ?? 'bg-brand' }}">{{ $badge }}</span>
        @endif
        <button type="button" class="sf-pcard-heart" data-wish="{{ $product->id }}" data-name="{{ $product->name }}" aria-label="Save {{ $product->name }}" title="Save for later">
            <i class="bi bi-heart"></i>
        </button>
    </div>
    <div class="card-body px-3 pt-2 pb-3 d-flex flex-column">
        <a href="{{ $url }}" class="sf-pcard-name text-dark text-decoration-none">{{ $product->name }}</a>
        <div class="small text-muted text-truncate mb-1">{{ $product->category->title ?? '' }}</div>
        <div class="fw-bold text-dark mb-2 mt-auto">Rs. {{ number_format((float) $product->price) }}</div>
        <form action="{{ route('shop.front.cart.add') }}" method="POST" class="m-0">
            @csrf
            <input type="hidden" name="product" value="{{ json_encode(['id' => $product->id]) }}">
            <input type="hidden" name="quantity" value="1">
            <input type="hidden" name="button_type" value="add_to_cart">
            <button class="btn {{ ! empty($outline) ? 'btn-outline-brand' : 'btn-brand' }} btn-sm w-100 rounded-3 py-2"><i class="bi {{ ! empty($outline) ? 'bi-cart3' : 'bi-cart-plus-fill' }} me-1"></i>Add to Cart</button>
        </form>
    </div>
</div>
