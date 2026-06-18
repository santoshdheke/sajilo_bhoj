@extends('frontend.theme_one.component.layout')

@section('content')
    <section class="page-banner">
        <div class="container text-center">
            <h1>Order {{ $order->order_no }}</h1>
            <p class="mb-0 text-white-50">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 fade-up">
                    <div class="contact-card">
                        <a href="{{ route('frontend.account.index') }}" class="text-muted text-decoration-none small d-inline-block mb-3">
                            <i class="bi bi-arrow-left me-1"></i>Back to My Account
                        </a>

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
                                        <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="cart-total-row">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="cart-total-row">
                            <span class="text-muted">Discount</span>
                            <span class="fw-semibold text-success">- {{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        @if($order->tax > 0)
                        <div class="cart-total-row">
                            <span class="text-muted">Tax</span>
                            <span class="fw-semibold">{{ number_format($order->tax, 2) }}</span>
                        </div>
                        @endif
                        <div class="cart-total-row">
                            <span class="fw-bold" style="font-size:1.05rem;">Grand Total</span>
                            <strong>{{ number_format($order->grand_total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
