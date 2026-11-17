<section class="product">
    <div class="container">
        <div class="card_two">
            <div class="card-body">
                <div>
                    <h4 class="card-title float-left">{{ $section->title }}</h4>

                    <div class="float-right">
                        <a class="btn-own" href="{{ route('shop.front.section', $section->page) }}">View more</a>
                    </div>

                </div>
                <div class="clear"></div>
                <div class="owl-carousel-four">
                    @foreach ($section->sectionItems as $sectionItem)
                        @if ($sectionItem->sectionable_type == 'Modules\Shop\Models\Product')
                            @php($product = $sectionItem->sectionable)
                            <div class="item">

                                <div style="padding: 7px;">
                                    <a href="{{ route('shop.front.product', ['category' => $category->slug, 'product' => $product->slug]) }}"
                                        style="text-decoration: none;">
                                        <div class="single_product">
                                            <img class="product_image" src="{{ $product->image_path }}" alt="image"
                                                style="height: 150px;width100%;" />
                                            <p class="product_name">{{ $product->name }}</p>
                                            <p class="product_price">Rs.{{ $product->price }}</p>
                                        </div>
                                    </a>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
