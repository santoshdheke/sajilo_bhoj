@extends('shop::admin.theme_one.component.layout')
@section('title', 'Products')

@php($rs = fn ($v) => \Modules\Shop\Support\Money::format($v))

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0">
        <h2>Products</h2>
        <p>Everything you sell. Stock comes from your purchases.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('shop.admin.product.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
</div>

<div class="card-soft table-card">
    <div class="card-header">
        <form class="filter-bar d-flex flex-wrap gap-2" method="get">
            <input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Name, SKU or barcode" style="max-width:240px;">
            <select class="form-select form-select-sm" name="category_id" style="max-width:220px;">
                <option value="">All categories</option>
                @foreach ($categories as $id => $title)<option value="{{ $id }}" @selected(request('category_id') == $id)>{{ $title }}</option>@endforeach
            </select>
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            @if (request()->hasAny(['search', 'category_id']))<a class="btn btn-sm btn-link" href="{{ route('shop.admin.product.index') }}">Clear</a>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th style="width:56px;"></th><th>Product</th><th>Category</th><th class="text-end">Price</th><th class="text-end">Cost</th><th class="text-center">Stock</th><th></th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>@if ($product->image_url)<img class="thumb" src="{{ $product->image_url }}" alt="">@else<span class="thumb"><i class="bi bi-image"></i></span>@endif</td>
                            <td>
                                <span class="fw-semibold">{{ $product->name }}</span>
                                <div class="text-muted small">{{ collect([$product->sku ? 'SKU '.$product->sku : null, $product->brand?->name])->filter()->implode(' · ') ?: '—' }}</div>
                            </td>
                            <td class="text-muted">{{ $product->category?->title }}</td>
                            <td class="text-end money fw-semibold">{{ $product->price !== null ? $rs($product->price) : '—' }}</td>
                            <td class="text-end money text-muted">{{ $product->cost_price !== null ? $rs($product->cost_price) : '—' }}</td>
                            <td class="text-center">
                                @if ($product->stock_in === 0)
                                    <span class="pill pill-gray">No stock yet</span>
                                @elseif ($product->current_stock === 0)
                                    <span class="pill pill-red">Out of stock</span>
                                @elseif ($product->is_low_stock)
                                    <span class="pill pill-amber">{{ $product->current_stock }} {{ $product->unit }}</span>
                                @else
                                    <span class="pill pill-green">{{ $product->current_stock }} {{ $product->unit }}</span>
                                @endif
                                @if ($product->is_expired)<div><span class="pill pill-red mt-1">Expired</span></div>@elseif ($product->is_expiring_soon)<div><span class="pill pill-amber mt-1">Expires {{ $product->expiry_date->format('j M') }}</span></div>@endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a class="tbl-action edit" title="Edit" href="{{ route('shop.admin.product.edit', $product) }}"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('shop.admin.product.destroy', $product) }}" method="POST" data-confirm="Delete product &quot;{{ $product->name }}&quot;?">
                                        @csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-row"><i class="bi bi-box"></i>No products found. <a href="{{ route('shop.admin.product.create') }}">Add your first product</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($products->hasPages())<div class="card-footer bg-transparent">{{ $products->links() }}</div>@endif
</div>
@endsection
