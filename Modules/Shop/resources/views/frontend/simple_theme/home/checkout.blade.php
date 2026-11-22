@extends($theme_path . 'common.layout.master_layout')

@push('css')
<style>
    .checkout-page { padding: 36px 0 72px; }

    /* ── Breadcrumb ── */
    .ck-breadcrumb {
        font-size: 12px; color: #999;
        margin-bottom: 28px;
        display: flex; align-items: center; gap: 6px;
    }
    .ck-breadcrumb a { color: #{{ $brand['primary'] }}; }
    .ck-breadcrumb a:hover { text-decoration: underline; }
    .ck-breadcrumb i { font-size: 9px; color: #ccc; }

    /* ── Page title ── */
    .ck-page-title {
        font-size: 26px; font-weight: 800; color: #111;
        margin-bottom: 28px; letter-spacing: -.4px;
    }

    /* ── Layout ── */
    .ck-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
        align-items: start;
    }
    @media (max-width: 991px) { .ck-layout { grid-template-columns: 1fr; } }

    /* ── Section headings ── */
    .ck-section-title {
        font-size: 13px; font-weight: 700; color: #111;
        text-transform: uppercase; letter-spacing: .07em;
        margin-bottom: 16px; padding-bottom: 12px;
        border-bottom: 2px solid #{{ $brand['primary'] }};
        display: flex; align-items: center; gap: 8px;
    }
    .ck-section-title i { color: #{{ $brand['primary'] }}; }

    /* ── Cart item card ── */
    .ck-item {
        display: flex; align-items: center; gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .ck-item:last-child { border-bottom: none; }
    .ck-item-img {
        width: 72px; height: 72px; flex-shrink: 0;
        border-radius: 8px; object-fit: cover;
        border: 1px solid #f0f0f0; background: #f6f6f6;
    }
    .ck-item-name {
        font-size: 14px; font-weight: 600; color: #111; margin-bottom: 4px;
    }
    .ck-item-meta { font-size: 12px; color: #9ca3af; }
    .ck-item-price {
        margin-left: auto; flex-shrink: 0;
        text-align: right;
    }
    .ck-item-price-main { font-size: 15px; font-weight: 700; color: #111; }
    .ck-item-price-unit { font-size: 11px; color: #9ca3af; }
    .ck-remove-btn {
        background: none; border: none; color: #d1d5db;
        font-size: 14px; cursor: pointer; padding: 4px 6px;
        transition: color .15s; flex-shrink: 0;
    }
    .ck-remove-btn:hover { color: #ef4444; }

    .ck-empty {
        text-align: center; padding: 60px 20px; color: #9ca3af;
    }
    .ck-empty i { font-size: 48px; margin-bottom: 14px; display: block; color: #e5e7eb; }
    .ck-empty p { font-size: 14px; margin-bottom: 18px; }

    /* ── Right panel card ── */
    .ck-panel {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 12px;
        padding: 22px 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        margin-bottom: 20px;
    }

    /* ── Form fields ── */
    .ck-form-group { margin-bottom: 16px; }
    .ck-label {
        display: block; font-size: 11.5px; font-weight: 700;
        color: #6b7280; text-transform: uppercase; letter-spacing: .06em;
        margin-bottom: 6px;
    }
    .ck-input {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid #e8eaed; border-radius: 8px;
        font-size: 13.5px; color: #111; background: #fafbfc;
        outline: none; transition: border-color .15s, background .15s;
        font-family: inherit;
    }
    .ck-input:focus { border-color: #{{ $brand['primary'] }}; background: #fff; }
    .ck-input::placeholder { color: #c1c7d0; }
    textarea.ck-input { resize: vertical; min-height: 80px; }

    /* Prefilled field (logged-in user) */
    .ck-prefilled {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px;
        background: #f8f9fb; border-radius: 8px;
        border: 1px solid #e8eaed;
        margin-bottom: 12px;
    }
    .ck-prefilled i { color: #{{ $brand['primary'] }}; margin-top: 2px; flex-shrink: 0; }
    .ck-prefilled-label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; }
    .ck-prefilled-value { font-size: 13.5px; font-weight: 600; color: #111; }

    /* ── Order summary ── */
    .ck-summary-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 7px 0; font-size: 13.5px; color: #6b7280;
        border-bottom: 1px solid #f3f4f6;
    }
    .ck-summary-row:last-child { border-bottom: none; }
    .ck-summary-total {
        display: flex; justify-content: space-between; align-items: center;
        padding: 14px 0 4px;
        font-size: 16px; font-weight: 800; color: #111;
    }
    .ck-summary-total .amt { color: #{{ $brand['primary'] }}; font-size: 20px; }

    /* ── Place Order button ── */
    .ck-place-order {
        display: block; width: 100%; padding: 15px;
        background: #{{ $brand['primary'] }};
        color: #{{ $brand['text'] }};
        border: none; border-radius: 8px;
        font-size: 14px; font-weight: 700;
        letter-spacing: .5px; text-align: center;
        cursor: pointer; font-family: inherit;
        transition: opacity .15s, transform .12s;
        box-shadow: 0 3px 12px rgba(0,0,0,.15);
        margin-top: 14px;
    }
    .ck-place-order:hover { opacity: .88; transform: translateY(-1px); }
    .ck-place-order:active { transform: translateY(0); }

    /* ── Trust badges ── */
    .ck-trust {
        display: flex; align-items: center; justify-content: center; gap: 18px;
        padding-top: 14px; margin-top: 6px;
        border-top: 1px solid #f3f4f6;
    }
    .ck-trust-item {
        display: flex; align-items: center; gap: 5px;
        font-size: 11.5px; color: #9ca3af; font-weight: 500;
    }
    .ck-trust-item i { color: #10b981; font-size: 12px; }

    /* ── Payment method ── */
    .ck-pay-option {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px; border: 1.5px solid #e8eaed; border-radius: 8px;
        margin-bottom: 10px; cursor: pointer; transition: border-color .15s, background .15s;
    }
    .ck-pay-option:hover { border-color: #{{ $brand['primary'] }}; }
    .ck-pay-option input[type=radio] { margin-top: 3px; flex-shrink: 0; }
    .ck-pay-option-title { font-size: 13.5px; font-weight: 700; color: #111; }
    .ck-pay-option-desc { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    .ck-pay-qr-box {
        display: none; text-align: center; padding: 18px;
        background: #f8f9fb; border-radius: 8px; margin-top: 12px;
    }
    .ck-pay-qr-box.active { display: block; }
    .ck-pay-qr-box #khalti-qr-canvas,
    .ck-pay-qr-box #esewa-qr-canvas { display: inline-block; padding: 10px; background: #fff; border-radius: 8px; }
</style>
@endpush

@section('contant')
<?php
    $carts = \Modules\Shop\Support\Cart::raw();
    $carts = json_decode($carts, true);
    $total = 0;
    if (is_array($carts)) {
        foreach ($carts as $cart) {
            $total += $cart['product']['price'] * $cart['quantity'];
        }
    }
    $tax = 0;
    $grandTotal = $total + $tax;
?>

<div class="checkout-page">
<div class="container">

    {{-- Breadcrumb --}}
    <nav class="ck-breadcrumb">
        <a href="{{ route('shop.front.home') }}">Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('shop.front.store') }}">Shop</a>
        <i class="fas fa-chevron-right"></i>
        <span style="color:#111;">Checkout</span>
    </nav>

    <h1 class="ck-page-title">Checkout</h1>

    <form action="{{ route('shop.front.order.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="cart" value="{{ json_encode($carts) }}">

        <div class="ck-layout">

            {{-- ── LEFT: Cart items + Shipping ── --}}
            <div>

                {{-- Cart Items --}}
                <div class="ck-panel">
                    <div class="ck-section-title">
                        <i class="fas fa-shopping-bag"></i>
                        Your Cart
                        @if(is_array($carts))
                            <span style="font-size:12px;font-weight:500;color:#9ca3af;margin-left:4px;">({{ count($carts) }} item{{ count($carts) != 1 ? 's' : '' }})</span>
                        @endif
                    </div>

                    @if (is_array($carts) && count($carts) > 0)
                        @foreach ($carts as $id => $cart)
                            <div class="ck-item">
                                <img class="ck-item-img"
                                     src="{{ $cart['product']['image'] }}"
                                     alt="{{ $cart['product']['name'] }}"
                                     onerror="this.style.background='#f3f4f6'">
                                <div style="flex:1; min-width:0;">
                                    <div class="ck-item-name">{{ $cart['product']['name'] }}</div>
                                    @include('shop::partials.line-options', ['attributes' => $cart['attribute'] ?? []])
                                    <div class="ck-item-meta">
                                        Qty: {{ $cart['quantity'] }} &nbsp;·&nbsp;
                                        Rs. {{ number_format($cart['product']['price']) }} each
                                    </div>
                                </div>
                                <div class="ck-item-price">
                                    <div class="ck-item-price-main">
                                        Rs. {{ number_format($cart['product']['price'] * $cart['quantity']) }}
                                    </div>
                                </div>
                                <a href="{{ route('shop.front.cart.remove', $id) }}" class="ck-remove-btn" title="Remove">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="ck-empty">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Your cart is empty.</p>
                            <a href="{{ route('shop.front.store') }}"
                               style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#{{ $brand['primary'] }};color:#{{ $brand['text'] }};border-radius:8px;font-size:13px;font-weight:700;">
                                <i class="fas fa-store"></i> Browse Products
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Shipping Address --}}
                <div class="ck-panel">
                    <div class="ck-section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Shipping Address
                    </div>

                    @if (auth('shop_customer')->check())
                        @php $user = auth('shop_customer')->user(); @endphp
                        <div class="ck-prefilled">
                            <i class="fas fa-user-circle fa-lg"></i>
                            <div>
                                <div class="ck-prefilled-label">Delivering to</div>
                                <div class="ck-prefilled-value">{{ $user->name }}</div>
                                @if($user->phone_number ?? null)
                                    <div style="font-size:12.5px;color:#6b7280;">{{ $user->phone_number }}</div>
                                @endif
                                @if($user->address ?? null)
                                    <div style="font-size:12.5px;color:#6b7280;">{{ $user->address }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="ck-form-group">
                            <label class="ck-label">Address Hints (Optional)</label>
                            <textarea class="ck-input" name="address_detail"
                                      placeholder="Landmark, floor, instructions…"></textarea>
                        </div>
                    @else
                        <div class="row" style="margin:0 -8px;">
                            <div class="col-sm-6" style="padding:0 8px;">
                                <div class="ck-form-group">
                                    <label class="ck-label">Full Name *</label>
                                    <input class="ck-input" type="text" name="full_name"
                                           placeholder="Your full name" required>
                                </div>
                            </div>
                            <div class="col-sm-6" style="padding:0 8px;">
                                <div class="ck-form-group">
                                    <label class="ck-label">Phone Number *</label>
                                    <input class="ck-input" type="text" name="phone_number"
                                           placeholder="98XXXXXXXX" required>
                                </div>
                            </div>
                        </div>
                        <div class="ck-form-group">
                            <label class="ck-label">Email Address</label>
                            <input class="ck-input" type="email" name="email"
                                   placeholder="you@example.com">
                        </div>
                        <div class="ck-form-group">
                            <label class="ck-label">Address *</label>
                            <input class="ck-input" type="text" name="address"
                                   placeholder="City / Area / Street" required>
                        </div>
                        <div class="ck-form-group">
                            <label class="ck-label">Address Hints (Optional)</label>
                            <textarea class="ck-input" name="address_detail"
                                      placeholder="Landmark, floor, instructions…"></textarea>
                        </div>
                    @endif
                </div>

                {{-- Payment Method --}}
                <div class="ck-panel">
                    <div class="ck-section-title">
                        <i class="fas fa-wallet"></i>
                        Payment Method
                    </div>

                    <label class="ck-pay-option">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked onchange="ckTogglePayment(this.value)">
                        <div>
                            <div class="ck-pay-option-title">Cash on Delivery</div>
                            <div class="ck-pay-option-desc">Pay with cash when your order arrives</div>
                        </div>
                    </label>

                    @if($settings->khalti_secret_key)
                        <label class="ck-pay-option">
                            <input type="radio" name="payment_method" value="khalti" onchange="ckTogglePayment(this.value)">
                            <div>
                                <div class="ck-pay-option-title">Pay Online via Khalti</div>
                                <div class="ck-pay-option-desc">You'll be redirected to Khalti to complete payment</div>
                            </div>
                        </label>
                    @endif

                    @if($settings->khalti_id)
                        <label class="ck-pay-option">
                            <input type="radio" name="payment_method" value="khalti_qr" onchange="ckTogglePayment(this.value)">
                            <div>
                                <div class="ck-pay-option-title">Pay via QR Code (Khalti)</div>
                                <div class="ck-pay-option-desc">Scan the QR, pay manually, then upload your payment screenshot</div>
                            </div>
                        </label>

                        <div class="ck-pay-qr-box" id="ck-khalti-qr-box">
                            <div id="khalti-qr-canvas"></div>
                            <div style="font-size:13px;font-weight:700;color:#111;margin-top:10px;">Khalti ID: {{ $settings->khalti_id }}</div>
                            @if($settings->khalti_name)
                                <div style="font-size:12px;color:#6b7280;">{{ $settings->khalti_name }}</div>
                            @endif
                            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Amount: Rs. {{ number_format($grandTotal) }}</div>
                        </div>
                    @endif

                    @if($settings->esewa_id)
                        <label class="ck-pay-option">
                            <input type="radio" name="payment_method" value="esewa_qr" onchange="ckTogglePayment(this.value)">
                            <div>
                                <div class="ck-pay-option-title">Pay via QR Code (eSewa)</div>
                                <div class="ck-pay-option-desc">Scan the QR, pay manually, then upload your payment screenshot</div>
                            </div>
                        </label>

                        <div class="ck-pay-qr-box" id="ck-esewa-qr-box">
                            <div id="esewa-qr-canvas"></div>
                            <div style="font-size:13px;font-weight:700;color:#111;margin-top:10px;">eSewa ID: {{ $settings->esewa_id }}</div>
                            @if($settings->esewa_name)
                                <div style="font-size:12px;color:#6b7280;">{{ $settings->esewa_name }}</div>
                            @endif
                            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Amount: Rs. {{ number_format($grandTotal) }}</div>
                        </div>
                    @endif

                    @if($settings->khalti_id || $settings->esewa_id)
                        <div class="ck-form-group" id="ck-payment-screenshot-group" style="display:none;margin-top:14px;">
                            <label class="ck-label">Upload Payment Screenshot *</label>
                            <input class="ck-input" type="file" name="payment_screenshot" id="ck-payment-screenshot" accept="image/*">
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── RIGHT: Order Summary + CTA ── --}}
            <div>
                <div class="ck-panel" style="position:sticky; top:82px;">
                    <div class="ck-section-title">
                        <i class="fas fa-receipt"></i>
                        Order Summary
                    </div>

                    @if (is_array($carts) && count($carts) > 0)
                        @foreach ($carts as $cart)
                            <div class="ck-summary-row">
                                <span style="color:#111;font-weight:500;">
                                    {{ $cart['product']['name'] }}
                                    <span style="color:#9ca3af;font-weight:400;">×{{ $cart['quantity'] }}</span>
                                </span>
                                <span style="color:#111;font-weight:600;">
                                    Rs. {{ number_format($cart['product']['price'] * $cart['quantity']) }}
                                </span>
                            </div>
                        @endforeach
                    @endif

                    <div class="ck-summary-row" style="margin-top:8px;">
                        <span>Subtotal</span>
                        <span style="color:#111;font-weight:600;">Rs. {{ number_format($total) }}</span>
                    </div>
                    <div class="ck-summary-row">
                        <span>Delivery</span>
                        <span style="color:#10b981;font-weight:600;">Free</span>
                    </div>

                    <div class="ck-summary-total">
                        <span>Total</span>
                        <span class="amt">Rs. {{ number_format($grandTotal) }}</span>
                    </div>

                    @if (is_array($carts) && count($carts) > 0)
                        <button type="submit" class="ck-place-order">
                            <i class="fas fa-lock mr-2" style="font-size:12px;"></i>
                            Place Order
                        </button>
                    @else
                        <a href="{{ route('shop.front.store') }}" class="ck-place-order" style="display:block;text-align:center;text-decoration:none;">
                            <i class="fas fa-store mr-2" style="font-size:12px;"></i>
                            Start Shopping
                        </a>
                    @endif

                    <div class="ck-trust">
                        <div class="ck-trust-item"><i class="fas fa-shield-alt"></i> Secure</div>
                        <div class="ck-trust-item"><i class="fas fa-truck"></i> Free Delivery</div>
                        <div class="ck-trust-item"><i class="fas fa-undo-alt"></i> Easy Returns</div>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
</div>

@if($settings->khalti_id || $settings->esewa_id)
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    var ckKhaltiQrRendered = false;
    var ckEsewaQrRendered = false;

    function ckTogglePayment(method) {
        var khaltiBox = document.getElementById('ck-khalti-qr-box');
        var esewaBox = document.getElementById('ck-esewa-qr-box');
        var screenshotGroup = document.getElementById('ck-payment-screenshot-group');
        var screenshotInput = document.getElementById('ck-payment-screenshot');

        if (khaltiBox) khaltiBox.classList.remove('active');
        if (esewaBox) esewaBox.classList.remove('active');

        if (method === 'khalti_qr' || method === 'esewa_qr') {
            if (screenshotGroup) screenshotGroup.style.display = 'block';
            if (screenshotInput) screenshotInput.required = true;
        } else {
            if (screenshotGroup) screenshotGroup.style.display = 'none';
            if (screenshotInput) screenshotInput.required = false;
        }

        if (method === 'khalti_qr' && khaltiBox) {
            khaltiBox.classList.add('active');
            if (!ckKhaltiQrRendered) {
                new QRCode(document.getElementById('khalti-qr-canvas'), {
                    text: {!! json_encode(json_encode(['Khalti_ID' => $settings->khalti_id, 'name' => $settings->khalti_name ?: $shop->name])) !!},
                    width: 160,
                    height: 160,
                    colorDark: '#5c2d91',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                ckKhaltiQrRendered = true;
            }
        }

        if (method === 'esewa_qr' && esewaBox) {
            esewaBox.classList.add('active');
            if (!ckEsewaQrRendered) {
                new QRCode(document.getElementById('esewa-qr-canvas'), {
                    text: {!! json_encode(json_encode(['eSewa_ID' => $settings->esewa_id, 'name' => $settings->esewa_name ?: $shop->name])) !!},
                    width: 160,
                    height: 160,
                    colorDark: '#60bb46',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                ckEsewaQrRendered = true;
            }
        }
    }
</script>
@endpush
@endif

@endsection
