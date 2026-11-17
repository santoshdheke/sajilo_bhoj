@extends($theme_path . 'common.layout.master_layout')

@push('css')
<style>
    .eh-pay-option {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px; border: 1px solid var(--border); border-radius: 3px;
        margin-bottom: 10px; cursor: pointer; transition: border-color .18s;
    }
    .eh-pay-option:hover { border-color: var(--primary); }
    .eh-pay-option input[type=radio] { margin-top: 3px; flex-shrink: 0; }
    .eh-pay-option-title { font-size: 14px; font-weight: 700; color: var(--dark); }
    .eh-pay-option-desc { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .eh-pay-qr-box {
        display: none; text-align: center; padding: 18px;
        background: var(--soft); border-radius: 3px; margin-top: 12px;
    }
    .eh-pay-qr-box.active { display: block; }
    .eh-pay-qr-box #khalti-qr-canvas,
    .eh-pay-qr-box #esewa-qr-canvas { display: inline-block; padding: 10px; background: #fff; border-radius: 4px; }
</style>
@endpush

@section('contant')
@php
    use Illuminate\Support\Facades\Session;
    $carts = json_decode(\Modules\Shop\Support\Cart::raw(), true);
    $total = 0;
    if (is_array($carts)) {
        foreach ($carts as $c) $total += ($c['product']['price'] ?? 0) * ($c['quantity'] ?? 1);
    }
@endphp

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">Checkout</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol></nav>
    </div>
</div>

<div class="container py-4">
    <form action="{{ route('shop.front.order.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="cart" value="{{ json_encode($carts) }}">
        <div class="row g-4">

            <!-- Left -->
            <div class="col-lg-8">
                <!-- Cart Items -->
                <div class="eh-summary-card mb-4">
                    <h5 style="font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:12px;border-bottom:2px solid var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-bag" style="color:var(--primary);"></i> Your Cart
                        @if(is_array($carts))
                            <span style="font-size:12px;font-weight:400;color:var(--muted);">({{ count($carts) }} item{{ count($carts)!=1?'s':'' }})</span>
                        @endif
                    </h5>

                    @if(is_array($carts) && count($carts) > 0)
                        @foreach($carts as $id => $cart)
                            <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);">
                                <img src="{{ $cart['product']['image'] ?? '' }}" alt="{{ $cart['product']['name'] }}"
                                     style="width:70px;height:70px;object-fit:cover;border-radius:3px;border:1px solid var(--border);flex-shrink:0;">
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:14px;font-weight:600;color:var(--dark);">{{ $cart['product']['name'] }}</div>
                                    <div style="font-size:12px;color:var(--muted);">Qty: {{ $cart['quantity'] }} &nbsp;·&nbsp; Rs. {{ number_format($cart['product']['price']) }}</div>
                                </div>
                                <div style="font-size:15px;font-weight:700;color:var(--dark);white-space:nowrap;">Rs. {{ number_format($cart['product']['price'] * $cart['quantity']) }}</div>
                                <a href="{{ route('shop.front.cart.remove', $id) }}"
                                   style="background:none;border:none;color:var(--muted);font-size:16px;cursor:pointer;padding:4px;text-decoration:none;">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="eh-empty-state">
                            <i class="bi bi-cart-x"></i>
                            <p>Your cart is empty.</p>
                            <a href="{{ route('shop.front.store') }}">Browse Products</a>
                        </div>
                    @endif
                </div>

                <!-- Shipping -->
                <div class="eh-summary-card">
                    <h5 style="font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:12px;border-bottom:2px solid var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-geo-alt" style="color:var(--primary);"></i> Shipping Address
                    </h5>

                    @if(auth('shop_customer')->check())
                        @php $user = auth('shop_customer')->user(); @endphp
                        <div style="padding:12px;background:var(--soft);border-radius:3px;border:1px solid var(--border);margin-bottom:12px;">
                            <strong>{{ $user->name }}</strong>
                            @if($user->phone_number) <span style="color:var(--muted);margin-left:8px;">{{ $user->phone_number }}</span> @endif
                            @if($user->address) <div style="color:var(--muted);font-size:13px;margin-top:4px;">{{ $user->address }}</div> @endif
                        </div>
                        <div class="eh-form-group">
                            <label>Address Hints (Optional)</label>
                            <textarea class="form-control" name="address_detail" rows="2" placeholder="Landmark, floor, instructions…"></textarea>
                        </div>
                    @else
                        <div class="row g-3">
                            <div class="col-sm-6 eh-form-group">
                                <label>Full Name *</label>
                                <input class="form-control" type="text" name="full_name" required>
                            </div>
                            <div class="col-sm-6 eh-form-group">
                                <label>Phone *</label>
                                <input class="form-control" type="text" name="phone_number" required>
                            </div>
                            <div class="col-12 eh-form-group">
                                <label>Email</label>
                                <input class="form-control" type="email" name="email">
                            </div>
                            <div class="col-12 eh-form-group">
                                <label>Address *</label>
                                <input class="form-control" type="text" name="address" required>
                            </div>
                            <div class="col-12 eh-form-group">
                                <label>Address Hints</label>
                                <textarea class="form-control" name="address_detail" rows="2"></textarea>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Payment Method -->
                <div class="eh-summary-card mt-4">
                    <h5 style="font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:12px;border-bottom:2px solid var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-wallet2" style="color:var(--primary);"></i> Payment Method
                    </h5>

                    <label class="eh-pay-option">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked onchange="ckTogglePayment(this.value)">
                        <div>
                            <div class="eh-pay-option-title">Cash on Delivery</div>
                            <div class="eh-pay-option-desc">Pay with cash when your order arrives</div>
                        </div>
                    </label>

                    @if($settings->khalti_secret_key)
                        <label class="eh-pay-option">
                            <input type="radio" name="payment_method" value="khalti" onchange="ckTogglePayment(this.value)">
                            <div>
                                <div class="eh-pay-option-title">Pay Online via Khalti</div>
                                <div class="eh-pay-option-desc">You'll be redirected to Khalti to complete payment</div>
                            </div>
                        </label>
                    @endif

                    @if($settings->khalti_id)
                        <label class="eh-pay-option">
                            <input type="radio" name="payment_method" value="khalti_qr" onchange="ckTogglePayment(this.value)">
                            <div>
                                <div class="eh-pay-option-title">Pay via QR Code (Khalti)</div>
                                <div class="eh-pay-option-desc">Scan the QR, pay manually, then upload your payment screenshot</div>
                            </div>
                        </label>

                        <div class="eh-pay-qr-box" id="ck-khalti-qr-box">
                            <div id="khalti-qr-canvas"></div>
                            <div style="font-size:13px;font-weight:700;color:var(--dark);margin-top:10px;">Khalti ID: {{ $settings->khalti_id }}</div>
                            @if($settings->khalti_name)
                                <div style="font-size:12px;color:var(--muted);">{{ $settings->khalti_name }}</div>
                            @endif
                            <div style="font-size:12px;color:var(--muted);margin-top:2px;">Amount: Rs. {{ number_format($total) }}</div>
                        </div>
                    @endif

                    @if($settings->esewa_id)
                        <label class="eh-pay-option">
                            <input type="radio" name="payment_method" value="esewa_qr" onchange="ckTogglePayment(this.value)">
                            <div>
                                <div class="eh-pay-option-title">Pay via QR Code (eSewa)</div>
                                <div class="eh-pay-option-desc">Scan the QR, pay manually, then upload your payment screenshot</div>
                            </div>
                        </label>

                        <div class="eh-pay-qr-box" id="ck-esewa-qr-box">
                            <div id="esewa-qr-canvas"></div>
                            <div style="font-size:13px;font-weight:700;color:var(--dark);margin-top:10px;">eSewa ID: {{ $settings->esewa_id }}</div>
                            @if($settings->esewa_name)
                                <div style="font-size:12px;color:var(--muted);">{{ $settings->esewa_name }}</div>
                            @endif
                            <div style="font-size:12px;color:var(--muted);margin-top:2px;">Amount: Rs. {{ number_format($total) }}</div>
                        </div>
                    @endif

                    @if($settings->khalti_id || $settings->esewa_id)
                        <div class="eh-form-group mt-3" id="ck-payment-screenshot-group" style="display:none;">
                            <label>Upload Payment Screenshot *</label>
                            <input class="form-control" type="file" name="payment_screenshot" id="ck-payment-screenshot" accept="image/*">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="col-lg-4">
                <div class="eh-summary-card" style="position:sticky;top:20px;">
                    <h5 style="font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:12px;border-bottom:2px solid var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-receipt" style="color:var(--primary);"></i> Order Summary
                    </h5>

                    @if(is_array($carts))
                        @foreach($carts as $cart)
                            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px;">
                                <span style="color:var(--dark);font-weight:500;">{{ $cart['product']['name'] }} <span style="color:var(--muted);font-weight:400;">×{{ $cart['quantity'] }}</span></span>
                                <span style="font-weight:600;">Rs. {{ number_format($cart['product']['price'] * $cart['quantity']) }}</span>
                            </div>
                        @endforeach
                    @endif

                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted);">
                        <span>Subtotal</span><span style="color:var(--dark);font-weight:600;">Rs. {{ number_format($total) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted);">
                        <span>Delivery</span><span style="color:#16a34a;font-weight:600;">Free</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:14px 0 8px;font-size:18px;font-weight:800;color:var(--dark);">
                        <span>Total</span><span style="color:var(--primary);">Rs. {{ number_format($total) }}</span>
                    </div>

                    @if(is_array($carts) && count($carts) > 0)
                        <button type="submit" class="eh-btn-primary w-100 justify-content-center mt-3" style="padding:14px;font-size:15px;">
                            <i class="bi bi-lock-fill" style="font-size:12px;"></i> Place Order
                        </button>
                    @else
                        <a href="{{ route('shop.front.store') }}" class="eh-btn-outline w-100 justify-content-center mt-3" style="padding:14px;font-size:15px;">
                            <i class="bi bi-bag"></i> Start Shopping
                        </a>
                    @endif

                    <div class="eh-trust-strip mt-3" style="padding:12px;">
                        <div class="eh-trust-item" style="font-size:12px;"><i class="bi bi-shield-check"></i> Secure</div>
                        <div class="eh-trust-item" style="font-size:12px;"><i class="bi bi-truck"></i> Free Delivery</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
