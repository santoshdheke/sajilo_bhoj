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
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-3">
            @foreach($section->products() as $product)
                <div class="col">@include($view_path . 'component.product_card', ['product' => $product])</div>
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
