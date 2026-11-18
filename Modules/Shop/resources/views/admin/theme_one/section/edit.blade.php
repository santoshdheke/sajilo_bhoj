@extends('shop::admin.theme_one.component.layout')
@section('title', $section->title)

@push('css')
<style>
    .sp-picker { position: relative; }
    .sp-picker-list { position: absolute; z-index: 30; left: 0; right: 0; top: calc(100% + 4px); max-height: 320px; overflow-y: auto;
                      background: #fff; border: 1px solid var(--line, #e5e7eb); border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, .12); display: none; }
    .sp-picker-list.open { display: block; }
    .sp-option { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border: 0; background: none; text-align: left; }
    .sp-option:hover, .sp-option.active { background: color-mix(in srgb, var(--primary) 8%, white); }
    .sp-option img, .sp-option .thumb { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; flex: 0 0 36px; }
    .sp-option-name { font-size: .88rem; font-weight: 600; color: #1f2937; line-height: 1.2; }
    .sp-option-meta { font-size: .75rem; color: #6b7280; }
    .sp-option-count { margin-left: auto; font-size: .7rem; font-weight: 700; color: var(--primary); background: color-mix(in srgb, var(--primary) 10%, white); border-radius: 999px; padding: 2px 8px; white-space: nowrap; }
    .sp-chosen { display: flex; align-items: center; gap: 10px; border: 1px dashed color-mix(in srgb, var(--primary) 40%, white); border-radius: 12px; padding: 8px 10px; background: color-mix(in srgb, var(--primary) 4%, white); }

    .sp-card { position: relative; background: #fff; border: 1px solid #eef0f3; border-radius: 14px; overflow: hidden; height: 100%; transition: box-shadow .15s, transform .15s; }
    .sp-card:hover { box-shadow: 0 8px 22px rgba(15, 23, 42, .1); transform: translateY(-2px); }
    .sp-card-img { aspect-ratio: 1 / 1; background: #f4f5f7; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 2rem; }
    .sp-card-img img { width: 100%; height: 100%; object-fit: cover; }
    .sp-order { position: absolute; top: 8px; left: 8px; background: rgba(17, 24, 39, .7); color: #fff; font-size: .7rem; font-weight: 700; border-radius: 6px; padding: 2px 7px; }
    .sp-dup { position: absolute; top: 8px; left: 46px; background: var(--primary); color: #fff; font-size: .68rem; font-weight: 700; border-radius: 6px; padding: 2px 7px; }
    .sp-remove { position: absolute; top: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; border: 0; background: rgba(220, 38, 38, .92); color: #fff;
                 display: flex; align-items: center; justify-content: center; font-size: .8rem; box-shadow: 0 2px 8px rgba(0, 0, 0, .2); }
    .sp-remove:hover { background: #b91c1c; }
    .sp-card-body { padding: 10px 12px 12px; }
    .sp-card-cat { font-size: .68rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; }
    .sp-card-name { font-size: .85rem; font-weight: 600; color: #1f2937; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .sp-card-price { font-size: .82rem; font-weight: 700; color: var(--primary); }
</style>
@endpush

@section('content')
@php
    $timesAdded = $items->countBy('sectionable_id');
    $seen = [];
@endphp
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>{{ $section->title }}</h2><p>Add products to this section — as many as you like, the same product more than once if you want.</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('shop.front.section', $section) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View on store</a>
        <a class="btn btn-outline-primary" href="{{ route('shop.admin.section.index') }}"><i class="bi bi-arrow-left me-1"></i>Sections</a>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: section settings + add product --}}
    <div class="col-lg-4">
        <div class="form-panel">
            <div class="card-soft mb-3">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Add product</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('shop.admin.section.item.store', $section) }}" id="addForm">
                        @csrf
                        <input type="hidden" name="product_id" id="productId">
                        <div class="sp-picker mb-2">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input class="form-control" id="productSearch" placeholder="Search product by name or SKU..." autocomplete="off">
                            </div>
                            <div class="sp-picker-list" id="pickerList">
                                @forelse ($products as $product)
                                    <button type="button" class="sp-option" data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-search="{{ strtolower($product->name.' '.$product->sku.' '.$product->category?->title) }}"
                                            data-img="{{ $product->image_url }}" data-meta="{{ \Modules\Shop\Support\Money::format($product->price) }}{{ $product->category ? ' • '.$product->category->title : '' }}">
                                        @if ($product->image_url)<img src="{{ $product->image_url }}" alt="">@else<span class="thumb"><i class="bi bi-image"></i></span>@endif
                                        <span><span class="sp-option-name d-block">{{ $product->name }}</span><span class="sp-option-meta">{{ \Modules\Shop\Support\Money::format($product->price) }}{{ $product->category ? ' • '.$product->category->title : '' }}</span></span>
                                        @if ($timesAdded[$product->id] ?? 0)<span class="sp-option-count">in section ×{{ $timesAdded[$product->id] }}</span>@endif
                                    </button>
                                @empty
                                    <div class="p-3 small text-muted">No products yet. <a href="{{ route('shop.admin.product.create') }}">Add a product</a> first.</div>
                                @endforelse
                                <div class="p-3 small text-muted d-none" id="pickerEmpty">No product matches.</div>
                            </div>
                        </div>
                        <div class="sp-chosen mb-2 d-none" id="chosen">
                            <span id="chosenImg"></span>
                            <span class="flex-grow-1 min-w-0"><span class="sp-option-name d-block text-truncate" id="chosenName"></span><span class="sp-option-meta" id="chosenMeta"></span></span>
                            <button type="button" class="btn btn-sm btn-link text-secondary p-0" id="chosenClear" title="Clear"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="d-flex gap-2">
                            <div style="width:96px;">
                                <label class="form-label small fw-semibold mb-1" for="qty">Times</label>
                                <input class="form-control" type="number" name="quantity" id="qty" value="1" min="1" max="50">
                            </div>
                            <div class="flex-grow-1 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="addBtn" disabled><i class="bi bi-plus-lg me-1"></i>Add to section</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-soft">
                <div class="card-header"><h5 class="mb-0">Section</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('shop.admin.section.update', $section) }}" class="row g-3">
                        @csrf @method('PUT')
                        <div class="col-12"><label class="form-label fw-semibold">Title</label><input class="form-control" name="title" value="{{ old('title', $section->title) }}" required maxlength="255"></div>
                        <div class="col-8"><label class="form-label fw-semibold">Layout</label>
                            <select class="form-select" name="type">@foreach (\Modules\Shop\Models\Section::TYPES as $k => $v)<option value="{{ $k }}" @selected($section->type === $k)>{{ $v }}</option>@endforeach</select></div>
                        <div class="col-4"><label class="form-label fw-semibold">Order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}"></div>
                        <div class="col-12"><button class="btn btn-outline-primary w-100"><i class="bi bi-check-lg me-1"></i>Save section</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: products in this section --}}
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0 fw-bold">Products in this section</h5>
            <span class="text-muted small">{{ $items->count() }} {{ \Illuminate\Support\Str::plural('item', $items->count()) }}</span>
        </div>

        @if ($items->isNotEmpty())
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                @foreach ($items->values() as $idx => $item)
                    @php
                        $product = $item->sectionable;
                        $seen[$product->id] = ($seen[$product->id] ?? 0) + 1;
                    @endphp
                    <div class="col">
                        <div class="sp-card">
                            <span class="sp-order">#{{ $idx + 1 }}</span>
                            @if (($timesAdded[$product->id] ?? 0) > 1)<span class="sp-dup" title="This product is in the section {{ $timesAdded[$product->id] }} times">copy {{ $seen[$product->id] }}/{{ $timesAdded[$product->id] }}</span>@endif
                            <form method="POST" action="{{ route('shop.admin.section.item.destroy', [$section, $item]) }}">
                                @csrf @method('DELETE')
                                <button class="sp-remove" title="Remove this item"><i class="bi bi-x-lg"></i></button>
                            </form>
                            <div class="sp-card-img">@if ($product->image_url)<img src="{{ $product->image_url }}" alt="">@else<i class="bi bi-image"></i>@endif</div>
                            <div class="sp-card-body">
                                <div class="sp-card-cat">{{ $product->category?->title }}</div>
                                <div class="sp-card-name">{{ $product->name }}</div>
                                <div class="sp-card-price money">{{ \Modules\Shop\Support\Money::format($product->price) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card-soft"><div class="card-body empty-row"><i class="bi bi-box-seam"></i>No products yet. Use <strong>Add product</strong> on the left.</div></div>
        @endif
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    var search = document.getElementById('productSearch'), list = document.getElementById('pickerList'),
        options = Array.prototype.slice.call(list.querySelectorAll('.sp-option')), empty = document.getElementById('pickerEmpty'),
        idInput = document.getElementById('productId'), addBtn = document.getElementById('addBtn'),
        chosen = document.getElementById('chosen'), active = -1;

    function visible() { return options.filter(function (o) { return o.style.display !== 'none'; }); }
    function filter() {
        var q = search.value.trim().toLowerCase();
        options.forEach(function (o) { o.style.display = !q || o.dataset.search.indexOf(q) !== -1 ? '' : 'none'; });
        if (empty) empty.classList.toggle('d-none', visible().length > 0 || !options.length);
        highlight(-1);
    }
    function highlight(i) {
        var v = visible(); active = i;
        options.forEach(function (o) { o.classList.remove('active'); });
        if (v[i]) { v[i].classList.add('active'); v[i].scrollIntoView({ block: 'nearest' }); }
    }
    function choose(o) {
        idInput.value = o.dataset.id;
        document.getElementById('chosenName').textContent = o.dataset.name;
        document.getElementById('chosenMeta').textContent = o.dataset.meta;
        document.getElementById('chosenImg').innerHTML = o.querySelector('img, .thumb').outerHTML;
        chosen.classList.remove('d-none');
        addBtn.disabled = false;
        search.value = ''; filter();
        list.classList.remove('open');
        addBtn.focus();
    }

    search.addEventListener('focus', function () { list.classList.add('open'); });
    search.addEventListener('input', function () { list.classList.add('open'); filter(); });
    search.addEventListener('keydown', function (e) {
        var v = visible();
        if (e.key === 'ArrowDown') { e.preventDefault(); list.classList.add('open'); highlight(Math.min(active + 1, v.length - 1)); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlight(Math.max(active - 1, 0)); }
        else if (e.key === 'Enter') { e.preventDefault(); if (v[active] || v.length === 1) choose(v[active] || v[0]); }
        else if (e.key === 'Escape') { list.classList.remove('open'); }
    });
    options.forEach(function (o) { o.addEventListener('click', function () { choose(o); }); });
    document.addEventListener('click', function (e) { if (!e.target.closest('.sp-picker')) list.classList.remove('open'); });
    document.getElementById('chosenClear').addEventListener('click', function () {
        idInput.value = ''; chosen.classList.add('d-none'); addBtn.disabled = true; search.focus();
    });
})();
</script>
@endpush
