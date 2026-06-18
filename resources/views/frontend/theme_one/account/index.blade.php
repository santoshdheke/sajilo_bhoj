@extends('frontend.theme_one.component.layout')

@section('content')
    <section class="page-banner">
        <div class="container text-center">
            <h1>My Account</h1>
            <p class="mb-0 text-white-50">Track your orders and loyalty rewards.</p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 fade-up">
                    <div class="contact-card">
                        <h3 class="fw-bold mb-3">{{ $customer->name }}</h3>
                        <div class="contact-info-item">
                            <div class="small text-muted">Email</div>
                            <div class="fw-semibold">{{ $customer->email }}</div>
                        </div>
                        @if($customer->phone)
                        <div class="contact-info-item">
                            <div class="small text-muted">Phone</div>
                            <div class="fw-semibold">{{ $customer->phone }}</div>
                        </div>
                        @endif
                        @if($customer->address)
                        <div class="contact-info-item">
                            <div class="small text-muted">Address</div>
                            <div class="fw-semibold">{{ $customer->address }}</div>
                        </div>
                        @endif
                        <div class="pt-3">
                            <span class="pill pill-green"><i class="bi bi-star-fill me-1"></i>{{ $customer->loyalty_points }} Loyalty Points</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 fade-up">
                    <div class="contact-card">
                        <h3 class="fw-bold mb-4">Order History</h3>

                        @forelse($orders as $order)
                        <a href="{{ route('frontend.account.order', $order) }}" class="text-decoration-none text-reset">
                            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $order->order_no }}</div>
                                    <div class="text-muted small">{{ $order->created_at->format('d M Y, h:i A') }} · {{ $order->order_type_label }} · {{ $order->items->count() }} item(s)</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ number_format($order->grand_total, 2) }}</div>
                                    <span class="pill pill-orange text-uppercase" style="font-size:.7rem;">{{ $order->status }}</span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>
                            You haven't placed any orders yet.
                            <div class="mt-3"><a href="{{ route('frontend.menu.index') }}" class="btn btn-sb-primary btn-sm">Browse Menu</a></div>
                        </div>
                        @endforelse

                        @if($orders->hasPages())
                        <div class="pt-4">{{ $orders->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
