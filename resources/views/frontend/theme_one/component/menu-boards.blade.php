{{--
    Printed menu-card layout: category filter + one board per category
    (brand header bar, dishes with dotted price leaders, category photo on the right).
    Expects $categories (active, ordered) and $items (available dishes with their category).
--}}
{{-- Category filter tabs --}}
<div class="d-flex flex-wrap justify-content-center gap-2 menu-filter mb-5">
    <button class="btn active" data-filter="all">All</button>
    @foreach($categories as $cat)
    <button class="btn" data-filter="cat-{{ $cat->id }}">
        @if($cat->image)<img class="mf-thumb" src="{{ $cat->imageUrl() }}" alt="" loading="lazy">@endif{{ $cat->name }}
    </button>
    @endforeach
</div>

{{-- Menu boards: one per category, like a printed menu card --}}
@php $byCategory = $items->groupBy('menu_category_id'); @endphp
@if($items->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-basket3" style="font-size:3rem;opacity:.4;display:block;margin-bottom:12px;"></i>
    <p class="fs-5">No menu items available right now. Check back soon!</p>
</div>
@else
<div class="menu-boards" id="menuGrid">
    @foreach($categories as $cat)
        @continue(! $byCategory->has($cat->id))
        <section class="menu-board menu-item-card" data-category="cat-{{ $cat->id }}">
            <header class="mb-head">
                <h3>{{ $cat->name }}</h3>
                <span>Price</span>
            </header>
            <div class="mb-body {{ $cat->image ? 'has-photo' : '' }}">
                <ul class="mb-list">
                    @foreach($byCategory[$cat->id] as $item)
                    <li class="mb-item">
                        <div class="mb-name">
                            <span>{{ $item->name }}</span>
                            @if($item->description)<small>{{ $item->description }}</small>@endif
                        </div>
                        <span class="mb-dots" aria-hidden="true"></span>
                        <span class="mb-price">{{ \App\Support\Money::format($item->price, $currencyCode) }}</span>
                        <button type="button" class="mb-add" title="Add {{ $item->name }} to order" aria-label="Add {{ $item->name }} to order"
                                onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->photoUrl() }}')">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </li>
                    @endforeach
                </ul>
                @if($cat->image)
                <div class="mb-photo"><img src="{{ $cat->imageUrl() }}" alt="{{ $cat->name }}" loading="lazy"></div>
                @endif
            </div>
            @if($cat->description)
            <p class="mb-note">{{ $cat->description }}</p>
            @endif
        </section>
    @endforeach
</div>
@endif

@push('scripts')
<script>
/* ── Category filter ── */
function applyFilter(btn) {
    document.querySelectorAll('.menu-filter .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    document.querySelectorAll('.menu-item-card').forEach(card => {
        card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
    });
}
document.querySelectorAll('.menu-filter .btn').forEach(btn => btn.addEventListener('click', () => applyFilter(btn)));

// Open on a category when linked from the home page (?category=ID)
const startCat = new URLSearchParams(location.search).get('category');
const startBtn = startCat && document.querySelector(`.menu-filter .btn[data-filter="cat-${CSS.escape(startCat)}"]`);
if (startBtn) applyFilter(startBtn);

</script>
@endpush
