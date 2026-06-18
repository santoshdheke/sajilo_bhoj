@extends('frontend.theme_one.component.layout')

@section('content')

<section class="page-banner">
    <div class="container text-center">
        <h1>Our Menu</h1>
        <p class="mb-0 text-white-50">
            {{ $items->count() }} dishes across {{ $categories->count() }} categories — fresh every day.
        </p>
    </div>
</section>

<section class="section-space">
    <div class="container">

        @if($table)
        <div class="alert alert-success rounded-4 mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-table fs-4"></i>
            <div>
                <strong>Ordering for {{ $table->name }}</strong> — add items and place your order below.
            </div>
        </div>
        @endif

        {{-- Category filter tabs --}}
        <div class="d-flex flex-wrap justify-content-center gap-2 menu-filter mb-5 fade-up">
            <button class="btn active" data-filter="all">All</button>
            @foreach($categories as $cat)
            <button class="btn" data-filter="cat-{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- Items grid --}}
        @if($items->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-basket3" style="font-size:3rem;opacity:.4;display:block;margin-bottom:12px;"></i>
            <p class="fs-5">No menu items available right now. Check back soon!</p>
        </div>
        @else
        <div class="row g-4" id="menuGrid">
            @foreach($items as $item)
            <div class="col-md-6 col-lg-4 fade-up menu-item-card" data-category="cat-{{ $item->menu_category_id }}">
                <div class="menu-card">
                    <div class="menu-image" style="position:relative;">
                        @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                             style="height:215px;width:100%;border-radius:22px;object-fit:cover;display:block;">
                        @else
                        <div style="height:215px;width:100%;border-radius:22px;background:var(--sb-green-pale);
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-egg-fried" style="font-size:3rem;color:var(--sb-muted);"></i>
                        </div>
                        @endif
                        <span style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,.9);
                                     padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:600;
                                     color:var(--sb-green-dark);">
                            {{ $item->category->name }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-1 mt-2">
                        <h5 class="fw-bold mb-0">{{ $item->name }}</h5>
                        <span class="price-tag">${{ number_format($item->price, 2) }}</span>
                    </div>
                    @if($item->description)
                    <p class="section-subtitle mb-3 order-item-content"
                       style="font-size:.83rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $item->description }}
                    </p>
                    @else
                    <p class="section-subtitle mb-3" style="font-size:.83rem;">{{ $item->category->name }}</p>
                    @endif
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-primary-o" href="javascript:void(0)"
                           onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->image ? Storage::url($item->image) : '' }}')">
                            <i class="bi bi-plus-circle me-1"></i>Add to order
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

@push('scripts')
<script>
/* ── Category filter ── */
document.querySelectorAll('.menu-filter .btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.menu-filter .btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('.menu-item-card').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
        });
    });
});

/* ── Simple cart (localStorage) ── */
function getCart() { try { return JSON.parse(localStorage.getItem('sb_cart') || '[]'); } catch { return []; } }
function saveCart(c) { localStorage.setItem('sb_cart', JSON.stringify(c)); updateCartUI(); }

function addToCart(id, name, price, img) {
    const cart = getCart();
    const idx  = cart.findIndex(i => i.id === id);
    if (idx >= 0) cart[idx].qty++;
    else cart.push({id, name, price, img, qty: 1});
    saveCart(cart);
    // Visual feedback
    const toastEl = document.getElementById('cartToast');
    if (toastEl) {
        document.getElementById('cartToastItem').textContent = name;
        bootstrap.Toast.getOrCreateInstance(toastEl).show();
    }
}

function updateCartUI() {
    const cart    = getCart();
    const total   = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const count   = cart.reduce((s, i) => s + i.qty, 0);
    const countEl = document.getElementById('cartCount');
    const totalEl = document.getElementById('cartTotal');
    const bodyEl  = document.getElementById('orderCardBody');
    if (countEl) countEl.textContent = count;
    if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
    if (bodyEl) {
        if (!cart.length) {
            bodyEl.innerHTML = '<p class="text-muted text-center py-3 mb-0" style="font-size:.85rem;">Your cart is empty.</p>';
        } else {
            bodyEl.innerHTML = cart.map(i => `
                <div class="order-item">
                    <div class="order-item-image">
                        ${i.img ? `<img src="${i.img}" alt="${i.name}">` : `<div style="width:100%;height:100%;background:#edfaf3;display:flex;align-items:center;justify-content:center;"><i class="bi bi-egg-fried" style="color:#198754;"></i></div>`}
                    </div>
                    <div class="order-item-content">
                        <h6>${i.name}</h6>
                        <p>Qty: ${i.qty}</p>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                        <div class="order-item-price">$${(i.price*i.qty).toFixed(2)}</div>
                        <button onclick="removeFromCart(${i.id})" style="border:none;background:none;color:#dc3545;font-size:.75rem;cursor:pointer;padding:0;">Remove</button>
                    </div>
                </div>`).join('');
        }
    }
    // Nav badge
    document.querySelectorAll('.cart-btn-label').forEach(el => {
        el.textContent = count > 0 ? `View Order (${count})` : 'View Order';
    });
}

function removeFromCart(id) {
    saveCart(getCart().filter(i => i.id !== id));
}

function clearCart() {
    saveCart([]);
}

document.addEventListener('DOMContentLoaded', updateCartUI);
</script>
@endpush

{{-- Toast notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100;">
    <div id="cartToast" class="toast align-items-center text-white border-0"
         role="alert" style="background:var(--sb-green-dark);border-radius:14px;">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>
                <span id="cartToastItem"></span> added to order!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection
