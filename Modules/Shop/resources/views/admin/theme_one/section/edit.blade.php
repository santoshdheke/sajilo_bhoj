@extends('shop::admin.theme_one.component.layout')
@section('title', $section->title)

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>{{ $section->title }}</h2><p>Pick the products shown in this section (the storefront shows up to 8).</p></div>
    <a class="btn btn-outline-primary" href="{{ route('shop.admin.section.index') }}"><i class="bi bi-arrow-left me-1"></i>Sections</a>
</div>

<form method="POST" action="{{ route('shop.admin.section.update', $section) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-soft form-panel">
                <div class="card-header"><h5 class="mb-0">Section</h5></div>
                <div class="card-body row g-3">
                    <div class="col-12"><label class="form-label fw-semibold">Title</label><input class="form-control" name="title" value="{{ old('title', $section->title) }}" required maxlength="255"></div>
                    <div class="col-8"><label class="form-label fw-semibold">Layout</label>
                        <select class="form-select" name="type">@foreach (\Modules\Shop\Models\Section::TYPES as $k => $v)<option value="{{ $k }}" @selected($section->type === $k)>{{ $v }}</option>@endforeach</select></div>
                    <div class="col-4"><label class="form-label fw-semibold">Order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}"></div>
                    <div class="col-12"><span class="text-muted small"><span id="pickedCount">{{ count($productIds) }}</span> products picked</span></div>
                    <div class="col-12"><button class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Save section</button></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card-soft table-card">
                <div class="card-header"><input class="form-control form-control-sm" id="productFilter" placeholder="Filter products..." style="max-width:260px;"></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 no-stack">
                            <thead><tr><th style="width:40px;"></th><th style="width:56px;"></th><th>Product</th><th>Category</th><th class="text-end">Price</th></tr></thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr class="product-row" data-name="{{ strtolower($product->name.' '.$product->sku) }}">
                                        <td><input class="form-check-input pick" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="p{{ $product->id }}" @checked(in_array($product->id, $productIds))></td>
                                        <td>@if ($product->image_url)<img class="thumb" src="{{ $product->image_url }}" alt="">@else<span class="thumb"><i class="bi bi-image"></i></span>@endif</td>
                                        <td><label class="fw-semibold" for="p{{ $product->id }}">{{ $product->name }}</label></td>
                                        <td class="text-muted">{{ $product->category?->title }}</td>
                                        <td class="text-end money">{{ \Modules\Shop\Support\Money::format($product->price) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="empty-row"><i class="bi bi-box"></i>No products yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
document.getElementById('productFilter').addEventListener('input', function () {
    var q = this.value.trim().toLowerCase();
    document.querySelectorAll('.product-row').forEach(function (r) { r.style.display = !q || r.dataset.name.indexOf(q) !== -1 ? '' : 'none'; });
});
document.querySelectorAll('.pick').forEach(function (c) {
    c.addEventListener('change', function () { document.getElementById('pickedCount').textContent = document.querySelectorAll('.pick:checked').length; });
});
</script>
@endpush
