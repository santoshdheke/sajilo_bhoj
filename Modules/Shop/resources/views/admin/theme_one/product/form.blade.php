@extends('shop::admin.theme_one.component.layout')
@section('title', $product->exists ? 'Edit Product' : 'Add Product')

@section('content')
@php
    $tiers = old('price_per_order', $product->exists ? $product->productPricePerOrders->map->only(['min_qty', 'max_qty', 'price'])->values()->all() : []);
    $tags  = old('tags', $product->exists ? $product->productTags->pluck('title')->implode(', ') : '');
@endphp

<div class="page-head mb-4">
    <div class="page-title mb-0">
        <h2>{{ $product->exists ? 'Edit Product' : 'Add Product' }}</h2>
        <p>{{ $product->exists ? $product->name : 'Fill in the details. Stock is added through Purchases.' }}</p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('shop.admin.product.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to products</a>
</div>

<form action="{{ $product->exists ? route('shop.admin.product.update', $product) : route('shop.admin.product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card-soft mb-4">
                <div class="card-header"><h5 class="mb-0">Details</h5></div>
                <div class="card-body row g-3">
                    <div class="col-md-8"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name', $product->name) }}" required maxlength="255" data-slug-source="#slug"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                        <input class="form-control" name="slug" id="slug" value="{{ old('slug', $product->slug) }}" required maxlength="255" pattern="[a-z0-9-]+"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">SKU</label>
                        <input class="form-control" name="sku" value="{{ old('sku', $product->sku) }}" maxlength="100"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Barcode</label>
                        <input class="form-control" name="barcode" value="{{ old('barcode', $product->barcode) }}" maxlength="100" placeholder="Scan or type"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Unit</label>
                        <input class="form-control" name="unit" value="{{ old('unit', $product->unit) }}" maxlength="30" placeholder="pcs, kg, ltr..."></div>
                    <div class="col-12"><label class="form-label fw-semibold">Short description</label>
                        <textarea class="form-control" name="description" rows="2">{{ old('description', $product->description) }}</textarea></div>
                    <div class="col-12"><label class="form-label fw-semibold">Long description</label>
                        <textarea class="form-control" name="long_description" rows="4">{{ old('long_description', $product->long_description) }}</textarea></div>
                    <div class="col-12"><label class="form-label fw-semibold">Tags</label>
                        <input class="form-control" name="tags" value="{{ $tags }}" placeholder="Comma separated, e.g. summer, cotton"></div>
                </div>
            </div>

            <div class="card-soft mb-4">
                <div class="card-header"><h5 class="mb-0">Category &amp; attributes</h5></div>
                <div class="card-body row g-3">
                    <div class="col-md-4"><label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="parent_category_id" id="parentCategory" required>
                            <option value="">Choose...</option>
                            @foreach ($roots as $id => $title)<option value="{{ $id }}" @selected($parentId == $id)>{{ $title }}</option>@endforeach
                        </select></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Sub-category</label>
                        <select class="form-select" name="category_id" id="childCategory">
                            <option value="">None</option>
                            @foreach ($children as $id => $title)<option value="{{ $id }}" @selected($childId == $id)>{{ $title }}</option>@endforeach
                        </select></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Brand <span class="text-danger">*</span></label>
                        <select class="form-select" name="brand_id" required>
                            <option value="">Choose...</option>
                            @foreach ($brands as $id => $name)<option value="{{ $id }}" @selected(old('brand_id', $product->brand_id) == $id)>{{ $name }}</option>@endforeach
                        </select></div>
                    <div class="col-12"><hr class="my-1"></div>
                    <div class="col-12 row g-3 m-0 p-0" id="attributeBox"><div class="col-12 text-muted small">Choose a category to see its attributes.</div></div>
                </div>
            </div>

            <div class="card-soft">
                <div class="card-header"><h5 class="mb-0">Images</h5></div>
                <div class="card-body">
                    @if ($product->exists && $product->productImages->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-3" id="oldImages">
                            @foreach ($product->productImages as $image)
                                <div class="position-relative">
                                    <img src="{{ $image->image_url }}" alt="" style="width:90px;height:90px;object-fit:cover;border-radius:12px;">
                                    <input type="hidden" name="old_image[]" value="{{ $image->image }}">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 py-0 px-1" onclick="this.parentElement.remove()" title="Remove"><i class="bi bi-x"></i></button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <input class="form-control" type="file" name="product_image[]" accept="image/*" multiple {{ $product->exists ? '' : 'required' }}>
                    <div class="form-text">{{ $product->exists ? 'Add more images, or remove existing ones above.' : 'At least one image. You can pick several.' }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card-soft mb-4 form-panel">
                <div class="card-header"><h5 class="mb-0">Pricing &amp; stock</h5></div>
                <div class="card-body row g-3">
                    <div class="col-6"><label class="form-label fw-semibold">Selling price</label>
                        <div class="input-group"><span class="input-group-text">Rs.</span><input class="form-control" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}"></div></div>
                    <div class="col-6"><label class="form-label fw-semibold">Cost price</label>
                        <div class="input-group"><span class="input-group-text">Rs.</span><input class="form-control" type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}"></div></div>
                    <div class="col-6"><label class="form-label fw-semibold">Tax rate</label>
                        <div class="input-group"><input class="form-control" type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate) }}"><span class="input-group-text">%</span></div></div>
                    <div class="col-6"><label class="form-label fw-semibold">Low-stock alert at</label>
                        <input class="form-control" type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Expiry date</label>
                        <input class="form-control" type="date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}"></div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="price_per_order_checkbox" value="1" id="tierToggle" @checked(old('price_per_order_checkbox', count($tiers) > 0))>
                            <label class="form-check-label fw-semibold" for="tierToggle">Quantity prices (wholesale)</label>
                        </div>
                        <div id="tierBox" class="mt-2 {{ old('price_per_order_checkbox', count($tiers) > 0) ? '' : 'd-none' }}">
                            <div id="tierRows"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="addTier"><i class="bi bi-plus"></i> Add tier</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-grid gap-2">
                    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $product->exists ? 'Update product' : 'Save product' }}</button>
                    @unless ($product->exists)
                        <button class="btn btn-outline-primary" name="back" value="1"><i class="bi bi-plus-lg me-1"></i>Save and add another</button>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
(function () {
    var parent = document.getElementById('parentCategory'), child = document.getElementById('childCategory'),
        box = document.getElementById('attributeBox'),
        childrenUrl = @json(route('shop.admin.category.children', ['parentId' => '__ID__'])),
        attributesUrl = @json(route('shop.admin.product.attributes', ['category' => '__ID__'])),
        productId = @json($product->id);

    function loadAttributes() {
        var categoryId = child.value || parent.value;
        if (!categoryId) { box.innerHTML = '<div class="col-12 text-muted small">Choose a category to see its attributes.</div>'; return; }
        fetch(attributesUrl.replace('__ID__', categoryId) + (productId ? '?product_id=' + productId : ''))
            .then(function (r) { return r.text(); }).then(function (html) { box.innerHTML = html; });
    }

    parent.addEventListener('change', function () {
        child.innerHTML = '<option value="">None</option>';
        if (parent.value) {
            fetch(childrenUrl.replace('__ID__', parent.value), { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (rows) { rows.forEach(function (c) { child.add(new Option(c.title, c.id)); }); });
        }
        loadAttributes();
    });
    child.addEventListener('change', loadAttributes);
    loadAttributes();

    // Quantity price tiers.
    var rows = document.getElementById('tierRows'), n = 0;
    function addTier(t) {
        t = t || {};
        var i = n++, div = document.createElement('div');
        div.className = 'row g-1 mb-1 align-items-center';
        div.innerHTML = '<div class="col-4"><input class="form-control form-control-sm" type="number" min="1" name="price_per_order[' + i + '][min_qty]" placeholder="Min qty" required></div>'
            + '<div class="col-3"><input class="form-control form-control-sm" type="number" min="1" name="price_per_order[' + i + '][max_qty]" placeholder="Max"></div>'
            + '<div class="col-4"><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="price_per_order[' + i + '][price]" placeholder="Price" required></div>'
            + '<div class="col-1"><button type="button" class="btn btn-sm btn-link text-danger p-0" title="Remove"><i class="bi bi-x-lg"></i></button></div>';
        div.querySelector('[name$="[min_qty]"]').value = t.min_qty || '';
        div.querySelector('[name$="[max_qty]"]').value = t.max_qty || '';
        div.querySelector('[name$="[price]"]').value = t.price || '';
        div.querySelector('button').addEventListener('click', function () { div.remove(); });
        rows.appendChild(div);
    }
    (@json(array_values($tiers))).forEach(addTier);
    document.getElementById('addTier').addEventListener('click', function () { addTier(); });
    document.getElementById('tierToggle').addEventListener('change', function () {
        document.getElementById('tierBox').classList.toggle('d-none', !this.checked);
        rows.querySelectorAll('input').forEach(function (inp) { inp.disabled = !document.getElementById('tierToggle').checked; });
        if (this.checked && !rows.children.length) addTier();
    });
    if (!document.getElementById('tierToggle').checked) rows.querySelectorAll('input').forEach(function (inp) { inp.disabled = true; });
})();
</script>
@endpush
