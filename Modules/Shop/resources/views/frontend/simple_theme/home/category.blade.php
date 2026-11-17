@extends($theme_path . 'common.layout.master_layout')

@section('contant')
    <section class="product">
        <div class="container">
            <div class="card_two">
                <div class="card-body">
                    <div>
                        <h4 class="card-title"
                            style="width: fit-content;border-bottom: 4px solid #{{ $brand['primary'] }};padding:5px 10px;">
                            {{ $category->title }}</h4>
                    </div>
                    <div class="row">
                        @if (isset($products))
                            @foreach ($products as $product)
                            <div class="col-lg-2 col-sm-4 col-xs-6 col-md-3 col-6" style="padding: 0px !important;">
                                    @include($view_path . 'component.single_product')
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div style="height: 30px;"></div>
@endsection

@push('js')
@endpush
