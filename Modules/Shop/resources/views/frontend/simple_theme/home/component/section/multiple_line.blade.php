@php
    $mlItems = $section->sectionItems()
        ->where('sectionable_type', 'Modules\Shop\Models\Product')
        ->with(['sectionable' => function($q){ $q->with(['category', 'productImage']); }])
        ->get()
        ->pluck('sectionable')
        ->filter();
@endphp

<section class="product-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">{{ $section->title }}</h2>
            <a class="section-link" href="{{ route('shop.front.section', $section) }}">View All &rarr;</a>
        </div>

        <div class="row" style="margin:0 -6px;">
            @foreach ($mlItems as $product)
                <div class="col-lg-2 col-md-4 col-sm-6 col-6" style="padding:0 6px 16px;">
                    @include($view_path . 'component.single_product')
                </div>
            @endforeach

            @if ($mlItems->count() === 0)
                <div class="col-12 text-center" style="padding:40px 0;color:#bbb;font-size:14px;">
                    <i class="fas fa-box-open" style="font-size:32px;margin-bottom:12px;display:block;"></i>
                    No products in this section yet.
                </div>
            @endif
        </div>
    </div>
</section>
