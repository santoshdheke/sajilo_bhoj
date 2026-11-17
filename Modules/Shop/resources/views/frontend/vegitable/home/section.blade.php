@extends($theme_path . 'common.layout.master_layout')

@section('contant')

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">{{ $section->title }}</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ $section->title }}</li>
        </ol></nav>
    </div>
</div>

<div class="container py-4">
    @if(isset($section) && $section->sectionItems->isNotEmpty())
        <div class="row g-3">
            @foreach($section->sectionItems as $sectionItem)
                @if($sectionItem->sectionable_type == 'Modules\Shop\Models\Product')
                    @php $product = $sectionItem->sectionable; @endphp
                    @if($product)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="eh-product-card h-100">
                                <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}">
                                    <div class="eh-product-thumb">
                                        <img src="{{ $product->image_path }}" alt="{{ $product->name }}">
                                    </div>
                                </a>
                                <div class="eh-product-body">
                                    <h5>
                                        <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                                           style="color:inherit;">{{ $product->name }}</a>
                                    </h5>
                                    <div style="font-size:18px;font-weight:800;color:var(--primary);margin:8px 0;">
                                        Rs. {{ number_format($product->price) }}
                                    </div>
                                    <div class="eh-product-actions">
                                        <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                                           class="eh-btn-ghost" style="font-size:12px;padding:6px 10px;">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form action="{{ route('shop.front.cart.add') }}" method="POST" style="flex:1;">
                                            @csrf
                                            <input type="hidden" name="button_type" value="add_to_cart">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="product" value="{{ json_encode(['id'=>$product->id,'name'=>$product->name,'price'=>$product->price,'image'=>$product->image_path]) }}">
                                            <button type="submit" class="eh-btn-primary w-100" style="font-size:12px;padding:6px 10px;justify-content:center;">
                                                <i class="bi bi-bag-plus"></i> Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    @else
        <div class="eh-empty-state py-5">
            <i class="bi bi-box-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px;"></i>
            <p>No products in this section.</p>
            <a href="{{ route('shop.front.store') }}">Browse All Products</a>
        </div>
    @endif
</div>
@endsection
