@extends('frontend.theme_one.component.layout')

@section('content')
    <section class="page-banner">
        <div class="container text-center">
            <h1>Checkout</h1>
            <p class="mb-0 text-white-50">Review your order and confirm your details.</p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div id="checkoutEmpty" class="text-center py-5 d-none">
                <i class="bi bi-bag-x" style="font-size:2.6rem;color:var(--sb-muted);"></i>
                <h4 class="mt-3">Your cart is empty</h4>
                <p class="section-subtitle">Add a few delicious items from our menu before checking out.</p>
                <a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-primary"><i class="bi bi-grid me-1"></i>Browse Menu</a>
            </div>

            <form id="checkoutForm" class="row g-4 d-none">
                @csrf
                <div class="col-lg-7 fade-up">
                    <div class="contact-card h-100">
                        <h3 class="fw-bold mb-4">Order Details</h3>

                        <div id="checkoutError" class="alert alert-danger d-none py-2"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Order Type</label>
                            <select class="form-select" id="orderType" name="order_type" required>
                                <option value="dine_in">Dine In</option>
                                <option value="takeaway">Takeaway</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>

                        <div class="mb-3" id="tableField">
                            <label class="form-label fw-semibold">Table</label>
                            <select class="form-select" id="tableId" name="table_id">
                                <option value="">Select a table…</option>
                                @foreach($tables as $t)
                                <option value="{{ $t->id }}">{{ $t->table_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerName" name="customer_name"
                                       value="{{ auth('customer')->user()->name ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" class="form-control" id="customerPhone" name="phone"
                                       value="{{ auth('customer')->user()->phone ?? '' }}" placeholder="+977…">
                            </div>
                        </div>

                        <div class="mb-1 mt-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" id="orderNotes" name="notes" rows="3" placeholder="Any special request…"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 fade-up">
                    <div class="contact-card form-panel" style="position:sticky;top:24px;">
                        <h3 class="fw-bold mb-3">Your Order</h3>
                        <div id="checkoutItems"></div>
                        <hr>
                        <div class="cart-total-row">
                            <span class="text-muted">Subtotal</span>
                            <span id="sumSubtotal" class="fw-semibold">NRS 0</span>
                        </div>
                        <div class="cart-total-row" style="margin-bottom:10px;">
                            <span class="fw-bold" style="font-size:1.05rem;">Total</span>
                            <strong id="sumTotal">NRS 0</strong>
                        </div>
                        <button type="submit" class="btn btn-sb-primary w-100 rounded-3" id="placeOrderBtn">
                            <i class="bi bi-bag-check me-1"></i>Place Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function toggleTableField() {
    const isDineIn = document.getElementById('orderType').value === 'dine_in';
    document.getElementById('tableField').style.display = isDineIn ? '' : 'none';
    document.getElementById('tableId').required = isDineIn;
}

function cartSubtotal(cart) {
    return cart.reduce((s, i) => s + i.price * i.qty, 0);
}

function renderCheckout() {
    const cart = getCart();
    const empty = document.getElementById('checkoutEmpty');
    const form  = document.getElementById('checkoutForm');

    if (!cart.length) {
        empty.classList.remove('d-none');
        form.classList.add('d-none');
        return;
    }
    empty.classList.add('d-none');
    form.classList.remove('d-none');

    document.getElementById('checkoutItems').innerHTML = cart.map(i => `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="fw-semibold" style="font-size:.88rem;">${escHtml(i.name)}</div>
                <div class="text-muted" style="font-size:.78rem;">${i.qty} × ${money(i.price)}</div>
            </div>
            <div class="fw-semibold">${money(i.price * i.qty)}</div>
        </div>`).join('');

    updateTotals();
}

function updateTotals() {
    const cart = getCart();
    const subtotal = cartSubtotal(cart);
    document.getElementById('sumSubtotal').textContent = money(subtotal);
    document.getElementById('sumTotal').textContent    = money(subtotal);
}

document.getElementById('orderType').addEventListener('change', toggleTableField);
toggleTableField();
renderCheckout();
document.addEventListener('cart:updated', renderCheckout);

document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const err = document.getElementById('checkoutError');
    err.classList.add('d-none');

    const cart = getCart();
    if (!cart.length) return;

    const payload = {
        order_type:    document.getElementById('orderType').value,
        table_id:      document.getElementById('tableId').value || null,
        customer_name: document.getElementById('customerName').value.trim(),
        phone:         document.getElementById('customerPhone').value.trim() || null,
        notes:         document.getElementById('orderNotes').value.trim() || null,
        items: cart.map(i => ({ id: i.id, name: i.name, price: i.price, qty: i.qty })),
    };

    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Placing order…';

    const res = await fetch('{{ route("frontend.checkout.store") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify(payload),
        redirect: 'follow',
    });

    if (res.redirected) {
        clearCart();
        window.location.href = res.url;
        return;
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-bag-check me-1"></i>Place Order';

    if (!res.ok) {
        let message = 'Something went wrong. Please check the form and try again.';
        try {
            const data = await res.json();
            message = Object.values(data.errors || {})[0]?.[0] || data.message || message;
        } catch (_) {}
        err.textContent = message;
        err.classList.remove('d-none');
    }
});
</script>
@endpush
