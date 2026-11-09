@extends('frontend.theme_one.component.layout')

@section('content')
    <section class="page-banner">
        <div class="container text-center">
            <h1>Order Confirmed</h1>
            <p class="mb-0 text-white-50">Thank you — your order has been received.</p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 fade-up">
                    <div class="contact-card">
                        @if(session('success'))
                            <div class="alert alert-success py-2">{{ session('success') }}</div>
                        @endif

                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <div class="text-muted small">Order Number</div>
                                <h3 class="fw-bold mb-0">{{ $order->order_no }}</h3>
                            </div>
                            <span class="pill pill-orange text-uppercase">{{ $order->status }}</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="text-muted small">Order Type</div>
                                <div class="fw-semibold">{{ $order->order_type_label }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Table</div>
                                <div class="fw-semibold">{{ $order->table->table_no ?? '—' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Customer</div>
                                <div class="fw-semibold">{{ $order->customer_name }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Placed</div>
                                <div class="fw-semibold">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3">Items</h5>
                        <div class="table-responsive mb-3">
                            <table class="table align-middle">
                                <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr></thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->name }}@if($item->note)<div class="text-muted small">{{ $item->note }}</div>@endif</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ \App\Support\Money::format($item->price, $currencyCode) }}</td>
                                        <td class="text-end">{{ \App\Support\Money::format($item->subtotal, $currencyCode) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="cart-total-row">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">{{ \App\Support\Money::format($order->subtotal, $currencyCode) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="cart-total-row">
                            <span class="text-muted">Discount</span>
                            <span class="fw-semibold text-success">- {{ \App\Support\Money::format($order->discount, $currencyCode) }}</span>
                        </div>
                        @endif
                        @if($order->tax > 0)
                        <div class="cart-total-row">
                            <span class="text-muted">Tax</span>
                            <span class="fw-semibold">{{ \App\Support\Money::format($order->tax, $currencyCode) }}</span>
                        </div>
                        @endif
                        <div class="cart-total-row" style="margin-bottom:16px;">
                            <span class="fw-bold" style="font-size:1.05rem;">Grand Total</span>
                            <strong>{{ \App\Support\Money::format($order->grand_total, $currencyCode) }}</strong>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-light flex-fill"><i class="bi bi-grid me-1"></i>Order More</a>
                            @auth('customer')
                            <a href="{{ route('frontend.account.index') }}" class="btn btn-sb-primary flex-fill"><i class="bi bi-receipt me-1"></i>My Orders</a>
                            @else
                            <a href="{{ route('frontend.home.index') }}" class="btn btn-sb-primary flex-fill"><i class="bi bi-house me-1"></i>Back Home</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
