@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php
    $images = $product->productImages ?? collect();
    $mainImage = $images->first();
    $relatedProducts = Modules\Shop\Models\Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)->take(8)->get();
@endphp

<!-- Breadcrumb -->
<div class="eh-breadcrumb-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop.front.category', ['category' => $product->category->slug]) }}">{{ $product->category->title }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Images -->
        <div class="col-lg-5">
            <div class="eh-detail-img-wrap" id="mainImgWrap">
                <img src="{{ $product->image_path }}" alt="{{ $product->name }}" id="mainImg"
                     style="max-height:340px;object-fit:contain;width:100%;">
            </div>
            @if($images->count() > 1)
                <div class="eh-thumb-strip mt-2">
                    @foreach($images as $img)
                        <div class="eh-thumb {{ $loop->first ? 'active' : '' }}"
                             onclick="document.getElementById('mainImg').src='{{ asset('storage/'.$img->path) }}';this.closest('.eh-thumb-strip').querySelectorAll('.eh-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active')">
                            <img src="{{ asset('storage/'.$img->path) }}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Info -->
        <div class="col-lg-7">
            <div class="eh-summary-card">
                @if($product->category)
                    <div class="mb-2">
                        <span class="eh-badge eh-badge-orange">{{ $product->category->title }}</span>
                    </div>
                @endif
                <h2>{{ $product->name }}</h2>

                <div class="my-3" style="font-size:28px;font-weight:800;color:var(--primary);">
                    Rs. {{ number_format($product->price) }}
                </div>

                @if($product->description)
                    <p style="color:var(--muted);font-size:14px;line-height:1.7;margin-bottom:20px;">
                        {{ $product->description }}
                    </p>
                @endif

                <!-- Add to cart -->
                <form action="{{ route('shop.front.cart.add') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="button_type" value="add_to_cart">
                    <input type="hidden" name="product" value="{{ json_encode(['id' => $product->id, 'name' => $product->name, 'price' => $product->price, 'image' => $product->image_path]) }}">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <label style="font-size:13px;font-weight:600;color:var(--muted);">Qty:</label>
                        <input type="number" name="quantity" value="1" min="1"
                               style="width:70px;padding:8px;border:1px solid var(--border);border-radius:3px;font-size:14px;">
                    </div>
                    <div class="eh-cta-stack">
                        <button type="submit" class="eh-btn-primary">
                            <i class="bi bi-bag-plus"></i> Add to Cart
                        </button>
                        <a href="{{ route('shop.front.checkout') }}" class="eh-btn-outline">
                            <i class="bi bi-bag-check"></i> View Cart & Checkout
                        </a>
                    </div>
                </form>

                <div class="eh-trust-strip mt-3">
                    <div class="eh-trust-item"><i class="bi bi-shield-check"></i> Secure Payment</div>
                    <div class="eh-trust-item"><i class="bi bi-truck"></i> Fast Delivery</div>
                    <div class="eh-trust-item"><i class="bi bi-arrow-return-left"></i> Easy Returns</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="eh-extra-section mt-4">
        <div class="eh-extra-head">
            <h3><i class="bi bi-grid"></i> Related Products</h3>
            @if($product->category)
                <a href="{{ route('shop.front.category', ['category' => $product->category->slug]) }}">View All <i class="bi bi-arrow-right"></i></a>
            @endif
        </div>
        <div class="eh-extra-body">
            <div class="eh-scroll-row">
                @foreach($relatedProducts as $rp)
                    <a href="{{ route('shop.front.product', ['category' => $rp->category->slug ?? 'product', 'product' => $rp->slug]) }}"
                       class="eh-card-sm">
                        <div class="eh-card-sm-img">
                            <img src="{{ $rp->image_path }}" alt="{{ $rp->name }}">
                        </div>
                        <div class="eh-card-sm-info">
                            <div class="eh-card-sm-name">{{ $rp->name }}</div>
                            <div class="eh-card-sm-meta">Rs. {{ number_format($rp->price) }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
