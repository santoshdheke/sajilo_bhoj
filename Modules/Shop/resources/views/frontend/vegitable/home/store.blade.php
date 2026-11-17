@extends($theme_path . 'common.layout.master_layout')

@section('contant')

<!-- Breadcrumb -->
<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">
            @if(request('search')) Search: "{{ request('search') }}"
            @elseif(request('category')) {{ ucwords(str_replace('-', ' ', request('category'))) }}
            @else All Products
            @endif
        </h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">Shop</li>
        </ol></nav>
    </div>
</div>

<div class="container py-3">
    <div class="row g-3">

        <!-- Sidebar -->
        <div class="col-lg-2 d-none d-lg-block">
            <div class="eh-sidebar-box">
                <div class="eh-sidebar-head" style="font-size:12px;padding:10px 14px;">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </div>
                <div style="padding:12px;">
                    @if(request()->hasAny(['category','brand','search']))
                        <a href="{{ route('shop.front.store') }}"
                           style="font-size:12px;color:var(--primary);display:inline-flex;align-items:center;gap:4px;margin-bottom:10px;">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    @endif

                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:8px;">Categories</div>
                    <ul class="eh-cat-tree">
                        <li>
                            <a href="{{ route('shop.front.store', request()->except('category')) }}"
                               style="{{ !request('category') ? 'color:var(--primary);font-weight:600;' : '' }}">
                                All Categories
                            </a>
                        </li>
                        @foreach(Modules\Shop\Models\Category::all() as $cat)
                            <li>
                                <a href="{{ route('shop.front.store', array_merge(request()->all(), ['category' => $cat->slug])) }}"
                                   style="{{ request('category') == $cat->slug ? 'color:var(--primary);font-weight:600;background:var(--primary-light);padding-left:20px;' : '' }}">
                                    {{ $cat->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @php $brands = Modules\Shop\Models\Brand::all(); @endphp
                    @if($brands->count())
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin:14px 0 8px;">Brands</div>
                        <ul class="eh-cat-tree">
                            @foreach($brands as $brand)
                                <li>
                                    <a href="{{ route('shop.front.store', array_merge(request()->all(), ['brand' => $brand->slug])) }}"
                                       style="{{ request('brand') == $brand->slug ? 'color:var(--primary);font-weight:600;' : '' }}">
                                        {{ $brand->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-lg-10">
            <!-- Toolbar -->
            <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:14px;border-bottom:2px solid var(--primary);margin-bottom:16px;flex-wrap:wrap;gap:8px;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="eh-btn-ghost d-lg-none" id="openSidebarBtn" style="font-size:12px;padding:6px 12px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(isset($products))
                        <span style="font-size:13px;color:var(--muted);">{{ $products->total() ?? $products->count() }} products</span>
                    @endif
                    @if(request('search'))
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:var(--primary-light);color:var(--primary);border-radius:3px;font-size:12px;font-weight:600;">
                            "{{ request('search') }}"
                            <a href="{{ route('shop.front.store', request()->except('search')) }}" style="color:var(--primary);">&times;</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:var(--primary-light);color:var(--primary);border-radius:3px;font-size:12px;font-weight:600;">
                            {{ ucwords(str_replace('-',' ',request('category'))) }}
                            <a href="{{ route('shop.front.store', request()->except('category')) }}" style="color:var(--primary);">&times;</a>
                        </span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13px;color:var(--muted);">Sort:</span>
                    <select style="padding:6px 12px;border:1px solid var(--border);border-radius:3px;font-size:13px;color:var(--text);background:#fff;outline:none;"
                            onchange="window.location=this.value">
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>''])) }}" {{ !request('sort') ? 'selected':'' }}>Default</option>
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>'price_asc'])) }}" {{ request('sort')=='price_asc'?'selected':'' }}>Price: Low → High</option>
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>'price_desc'])) }}" {{ request('sort')=='price_desc'?'selected':'' }}>Price: High → Low</option>
                        <option value="{{ route('shop.front.store', array_merge(request()->all(), ['sort'=>'newest'])) }}" {{ request('sort')=='newest'?'selected':'' }}>Newest</option>
                    </select>
                </div>
            </div>

            @if(isset($products) && $products->count() > 0)
                <div class="row g-3">
                    @foreach($products as $product)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="eh-product-card h-100">
                                <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                                   style="text-decoration:none;">
                                    <div class="eh-product-thumb">
                                        <img src="{{ $product->image_path }}" alt="{{ $product->name }}">
                                    </div>
                                </a>
                                <div class="eh-product-body">
                                    <h5>
                                        <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                                           style="color:inherit;">{{ $product->name }}</a>
                                    </h5>
                                    @if($product->category)
                                        <div class="eh-product-meta">{{ $product->category->title }}</div>
                                    @endif
                                    <div style="font-size:18px;font-weight:800;color:var(--primary);margin:8px 0;">
                                        Rs. {{ number_format($product->price) }}
                                    </div>
                                    <div class="eh-product-actions">
                                        <a href="{{ route('shop.front.product', ['category' => $product->category->slug ?? 'product', 'product' => $product->slug]) }}"
                                           class="eh-btn-ghost" style="font-size:12px;padding:6px 10px;">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form action="{{ route('shop.front.cart.add') }}" method="POST" style="flex:1;">
                                            @csrf
                                            <input type="hidden" name="button_type" value="add_to_cart">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="product" value="{{ json_encode(['id'=>$product->id,'name'=>$product->name,'price'=>$product->price,'image'=>$product->image_path]) }}">
                                            <button type="submit" class="eh-btn-primary w-100" style="font-size:12px;padding:6px 10px;justify-content:center;">
                                                <i class="bi bi-bag-plus"></i> Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(method_exists($products, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        <div class="eh-pagination">
                            {{ $products->appends(request()->all())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="eh-empty-state py-5">
                    <i class="bi bi-box-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px;"></i>
                    <p>No products found.</p>
                    <a href="{{ route('shop.front.store') }}">Browse All Products</a>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Mobile sidebar overlay -->
<div id="mobileSidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:4999;" onclick="closeSidebar()"></div>
<div id="mobileSidebarPanel"
     style="display:none;position:fixed;top:0;left:0;bottom:0;width:280px;background:#fff;z-index:5000;padding:20px;overflow-y:auto;box-shadow:4px 0 30px rgba(0,0,0,.15);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <strong style="font-size:15px;">Filters</strong>
        <button onclick="closeSidebar()" style="background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
    </div>
    @if(request()->hasAny(['category','brand','search']))
        <a href="{{ route('shop.front.store') }}" style="font-size:12px;color:var(--primary);display:flex;align-items:center;gap:4px;margin-bottom:12px;">
            <i class="bi bi-x-circle"></i> Clear Filters
        </a>
    @endif
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:8px;">Categories</div>
    <ul style="list-style:none;padding:0;margin:0 0 16px;">
        <li style="border-bottom:1px solid var(--border);">
            <a href="{{ route('shop.front.store', request()->except('category')) }}"
               style="display:block;padding:10px 4px;font-size:13px;color:{{ !request('category')?'var(--primary)':'var(--text)' }};">All Categories</a>
        </li>
        @foreach(Modules\Shop\Models\Category::all() as $cat)
            <li style="border-bottom:1px solid var(--border);">
                <a href="{{ route('shop.front.store', array_merge(request()->all(), ['category'=>$cat->slug])) }}"
                   style="display:block;padding:10px 4px;font-size:13px;color:{{ request('category')==$cat->slug?'var(--primary)':'var(--text)' }};">{{ $cat->title }}</a>
            </li>
        @endforeach
    </ul>
</div>

@push('js')
<script>
function closeSidebar() {
    document.getElementById('mobileSidebarPanel').style.display='none';
    document.getElementById('mobileSidebarOverlay').style.display='none';
}
var openBtn = document.getElementById('openSidebarBtn');
if(openBtn) openBtn.addEventListener('click', function(){
    document.getElementById('mobileSidebarPanel').style.display='block';
    document.getElementById('mobileSidebarOverlay').style.display='block';
});
</script>
@endpush
@endsection
