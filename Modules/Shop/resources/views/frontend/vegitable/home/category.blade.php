@extends($theme_path . 'common.layout.master_layout')

@section('contant')

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">{{ $category->title }}</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ $category->title }}</li>
        </ol></nav>
    </div>
</div>

<div class="container py-4">
    @if(isset($products) && $products->count())
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-3">
            @foreach($products as $product)
                <div class="col">@include($view_path . 'component.product_card', ['product' => $product])</div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="eh-empty-state py-5">
            <i class="bi bi-box-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px;"></i>
            <p>No products in this category yet.</p>
            <a href="{{ route('shop.front.store') }}">Browse All Products</a>
        </div>
    @endif
</div>
@endsection
