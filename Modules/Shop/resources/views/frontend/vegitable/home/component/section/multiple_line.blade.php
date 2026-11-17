<section class="product">
    <div class="container">
        <div class="card_two">
            <div class="card-body">
                <div>
                    <h4 class="card-title float-left">{{ $section->title }}</h4>

                </div>
                <div class="clear"></div>
                <div class="row" style="margin: 0px 5px;">
                    @foreach ($section->sectionItems as $sectionItem)
                        @if ($sectionItem->sectionable_type == 'Modules\Shop\Models\Product')
                            @php($product = $sectionItem->sectionable)
                            <div class="col-lg-2" style="padding: 10px !important;">
                                <?php $categorySlug = $category->slug ?? ''; ?>
                                <?php $productSlug = $product->slug ?? ''; ?>
                                <a href="{{ route('shop.front.product', ['category' => $categorySlug, 'product' => $productSlug]) }}"
                                    style="text-decoration: none;">
                                    <div class="single_product">
                                        <img class="product_image" src="{{ $product->image_path }}" alt="image"
                                            style="height: 150px;width100%;" />
                                        <p class="product_name">{{ $product->name }}</p>
                                        <p class="product_price">Rs.{{ $product->price }}</p>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
