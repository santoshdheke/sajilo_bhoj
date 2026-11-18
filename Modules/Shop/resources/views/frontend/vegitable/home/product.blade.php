@extends($theme_path . 'common.layout.master_layout')
@section('title', $product->name.' - '.$shop->name)

@push('css')
<style>
    .pd-crumb { font-size: .82rem; }
    .pd-crumb a { color: var(--primary); text-decoration: none; font-weight: 500; }
    .pd-crumb .bi-chevron-right { font-size: .65rem; color: #94a3b8; }
    .pd-thumb { width: 74px; height: 74px; border: 2px solid #e5e7eb; border-radius: 12px; background: #fff; padding: 4px; overflow: hidden; cursor: pointer; transition: border-color .15s; }
    .pd-thumb img { width: 100%; height: 100%; object-fit: contain; }
    .pd-thumb.active, .pd-thumb:hover { border-color: var(--primary); }
    .pd-main { background: #f8fafc; border: 1px solid #eef0f3; border-radius: 16px; overflow: hidden; }
    .pd-main img { width: 100%; height: 100%; object-fit: contain; padding: 16px; }
    .pd-zoom { position: absolute; right: 12px; bottom: 12px; left: auto; top: auto; width: 38px !important; height: 38px !important; border-radius: 50%; border: 0; background: #fff; box-shadow: 0 2px 10px rgba(15, 23, 42, .15); color: #1f2937; }
    .pd-zoom:hover { color: var(--primary); }
    .pd-qty .btn { width: 38px; background: var(--primary-light); color: var(--primary); border: 0; font-weight: 700; }
    .pd-qty .btn:hover { background: var(--primary); color: var(--primary-text); }
    .pd-qty input { width: 64px; text-align: center; }
    .pd-qty input::-webkit-inner-spin-button { -webkit-appearance: none; }
    .pd-wish.on { background: var(--primary-light); }
    .pd-trust { background: var(--primary-light); border-radius: 14px; }
    .pd-trust i { color: var(--primary); font-size: 1.3rem; }
    .pd-tabs .nav-link { color: #475569; font-weight: 600; border: 0; border-bottom: 2px solid transparent; border-radius: 0; padding: .8rem 1.1rem; }
    .pd-tabs .nav-link.active { color: var(--primary); border-bottom-color: var(--primary); background: none; }
</style>
@endpush

@section('contant')
@php
    $images  = $product->productImages ?? collect();
    $stock   = $product->current_stock;
    $options = $product->category?->attributes ?? collect();
    $tiers   = $product->productPricePerOrders ?? collect();
    $related = Modules\Shop\Models\Product::with('productImage', 'category')
        ->where('category_id', $product->category_id)->where('id', '!=', $product->id)->latest()->take(12)->get();
    $details = $product->long_description ?: $product->description;
@endphp

<div class="container py-3">
    {{-- Breadcrumb --}}
    <nav class="pd-crumb d-flex flex-wrap align-items-center gap-2 mb-3" aria-label="breadcrumb">
        <a href="{{ route('shop.front.home') }}" aria-label="Home"><i class="bi bi-house-door-fill"></i></a>
        @if ($product->category)
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('shop.front.category', $product->category->slug) }}">{{ $product->category->title }}</a>
        @endif
        <i class="bi bi-chevron-right"></i>
        <span class="text-muted text-truncate">{{ $product->name }}</span>
    </nav>

    {{-- Gallery + buy box --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="d-flex flex-column-reverse flex-md-row gap-3">
                        @if ($images->count() > 1)
                            <div class="d-flex flex-md-column gap-2 overflow-auto">
                                @foreach ($images as $image)
                                    <button type="button" class="pd-thumb flex-shrink-0 {{ $loop->first ? 'active' : '' }}" data-src="{{ $image->image_path }}" aria-label="Show image {{ $loop->iteration }}">
                                        <img src="{{ $image->image_path }}" alt="">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="pd-main position-relative ratio ratio-4x3">
                                @if ($product->image_path)
                                    <img src="{{ $product->image_path }}" alt="{{ $product->name }}" id="pdMain">
                                @else
                                    <span class="d-flex align-items-center justify-content-center text-secondary display-4"><i class="bi bi-image"></i></span>
                                @endif
                            </div>
                            @if ($product->image_path)
                                <div class="position-relative">
                                    <button type="button" class="pd-zoom position-absolute" style="bottom:12px;" data-bs-toggle="modal" data-bs-target="#pdZoom" aria-label="Zoom image"><i class="bi bi-zoom-in"></i></button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    @if ($stock > 0)
                        <span class="badge rounded-pill bg-brand-soft text-brand mb-2"><i class="bi bi-check2 me-1"></i>In Stock</span>
                    @else
                        <span class="badge rounded-pill text-bg-danger mb-2"><i class="bi bi-x me-1"></i>Out of Stock</span>
                    @endif
                    <h1 class="h3 fw-bold mb-1">{{ $product->name }}</h1>
                    @if ($product->brand || $product->category)
                        <div class="small text-muted mb-2">{{ collect([$product->brand?->name, $product->category?->title])->filter()->implode(' · ') }}</div>
                    @endif
                    <div class="fs-2 fw-bold text-brand mb-3">Rs. {{ number_format((float) $product->price) }}</div>

                    @if ($tiers->isNotEmpty())
                        <div class="mb-3">
                            <div class="small fw-semibold mb-1">Buy more, pay less</div>
                            @foreach ($tiers as $tier)
                                <span class="badge text-bg-light border me-1 mb-1">{{ $tier->min_qty }}{{ $tier->max_qty ? '–'.$tier->max_qty : '+' }} pcs: Rs. {{ number_format((float) $tier->price) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <hr class="my-3">

                    <form action="{{ route('shop.front.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product" value="{{ json_encode(['id' => $product->id]) }}">
                        <input type="hidden" name="button_type" value="add_to_cart">

                        @if ($options->isNotEmpty())
                            <div class="row g-2 mb-3">
                                @foreach ($options as $attribute)
                                    @php $values = $attribute->productAttribute?->productAttributeValues?->pluck('value')->filter() ?? collect(); @endphp
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold mb-1">{{ $attribute->attribute_title }}</label>
                                        @if ($values->isNotEmpty())
                                            <select class="form-select form-select-sm" name="attribute[{{ $attribute->id }}][{{ $attribute->attribute_title }}]">
                                                <option value="">Choose…</option>
                                                @foreach ($values as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach
                                            </select>
                                        @else
                                            <input class="form-control form-control-sm" name="attribute[{{ $attribute->id }}][{{ $attribute->attribute_title }}]" maxlength="100" placeholder="Optional">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="small text-muted">Quantity:</span>
                            <div class="input-group input-group-sm pd-qty w-auto">
                                <button type="button" class="btn" data-step="-1" aria-label="Less">−</button>
                                <input type="number" name="quantity" id="pdQty" class="form-control" value="1" min="1" max="{{ max(1, $stock) }}" aria-label="Quantity">
                                <button type="button" class="btn" data-step="1" aria-label="More">+</button>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-brand btn-lg w-100 rounded-3 fs-6 py-2" @disabled($stock < 1)>
                                    @if ($stock > 0)
                                        <i class="bi bi-cart3 me-2"></i>Add to Cart
                                    @else
                                        <i class="bi bi-x-circle me-2"></i>Out of Stock
                                    @endif
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-outline-brand btn-lg w-100 rounded-3 fs-6 py-2 pd-wish" data-wish="{{ $product->id }}" data-name="{{ $product->name }}"><i class="bi bi-heart"></i><span class="ms-2">Add to Wishlist</span></button>
                            </div>
                        </div>
                    </form>

                    <div class="pd-trust row g-0 text-center small fw-semibold py-3">
                        <div class="col-4 d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2"><i class="bi bi-shield-check"></i>Secure Payment</div>
                        <div class="col-4 d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2"><i class="bi bi-truck"></i>Fast Delivery</div>
                        <div class="col-4 d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2"><i class="bi bi-arrow-repeat"></i>Easy Returns</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Description / shipping & returns --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <ul class="nav pd-tabs border-bottom px-2" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pdDesc" type="button">Description</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#pdShip" type="button">Shipping &amp; Returns</button></li>
        </ul>
        <div class="tab-content p-3 p-lg-4 small text-secondary">
            <div class="tab-pane fade show active" id="pdDesc">
                {!! $details ? nl2br(e($details)) : 'No description available for this product yet.' !!}
            </div>
            <div class="tab-pane fade" id="pdShip">
                <p class="mb-2">{{ $settings->policy('refund_policy', $shop->name) }}</p>
                <a href="{{ route('shop.front.page.refund') }}" class="text-brand fw-semibold text-decoration-none">Read the full refund policy <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Related products --}}
    @if ($related->isNotEmpty())
        <section>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 fw-bold mb-0 border-start border-4 border-brand ps-2">Related Products</h2>
                @if ($product->category)
                    <a href="{{ route('shop.front.category', $product->category->slug) }}" class="small fw-semibold text-brand text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
                @endif
            </div>
            <div class="row flex-nowrap g-3 overflow-auto pb-2 sf-row-scroll">
                @foreach ($related as $item)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">@include($view_path . 'component.product_card', ['product' => $item, 'outline' => true])</div>
                @endforeach
            </div>
        </section>
    @endif
</div>

{{-- Zoomed image --}}
@if ($product->image_path)
    <div class="modal fade" id="pdZoom" tabindex="-1" aria-label="{{ $product->name }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-1" data-bs-dismiss="modal" aria-label="Close"></button>
                <img src="{{ $product->image_path }}" alt="{{ $product->name }}" id="pdZoomImg" class="w-100 bg-white" style="max-height:80vh;object-fit:contain;">
            </div>
        </div>
    </div>
@endif
@endsection

@push('js')
<script>
(function () {
    // Thumbnails switch the main (and zoomed) image.
    document.querySelectorAll('.pd-thumb').forEach(function (t) {
        t.addEventListener('click', function () {
            document.querySelectorAll('.pd-thumb').forEach(function (x) { x.classList.toggle('active', x === t); });
            var main = document.getElementById('pdMain'), zoom = document.getElementById('pdZoomImg');
            if (main) main.src = t.dataset.src;
            if (zoom) zoom.src = t.dataset.src;
        });
    });
    // − / + quantity buttons.
    var qty = document.getElementById('pdQty');
    document.querySelectorAll('[data-step]').forEach(function (b) {
        b.addEventListener('click', function () {
            var v = (parseInt(qty.value, 10) || 1) + parseInt(b.dataset.step, 10);
            qty.value = Math.min(Math.max(v, 1), parseInt(qty.max, 10) || 1);
        });
    });
})();
</script>
@endpush
