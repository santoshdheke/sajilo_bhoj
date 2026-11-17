@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php $user = auth('shop_customer')->user(); @endphp

<div class="account-page">
<div class="container">
<div class="account-layout">

    {{-- Sidebar --}}
    @include($theme_path . 'user.sidebar')

    {{-- Main content --}}
    <div>
        <div class="account-content">
            <div class="acc-content-header">
                <h2 class="acc-content-title">
                    <i class="fas fa-user"></i>
                    My Profile
                </h2>
            </div>
            <div class="acc-content-body">

                {{-- Avatar + name row --}}
                <div style="display:flex;align-items:center;gap:20px;padding:6px 0 24px;border-bottom:1px solid #f3f4f6;margin-bottom:24px;flex-wrap:wrap;">
                    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#{{ $brand['primary'] }},#{{ $brand['secondary'] }});color:#fff;font-size:28px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 16px rgba(0,0,0,.12);">
                        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:20px;font-weight:800;color:#111;">{{ $user?->name ?? '—' }}</div>
                        <div style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $user?->email ?? '—' }}</div>
                    </div>
                </div>

                {{-- Profile fields --}}
                <div class="pf-row">
                    <div class="pf-icon"><i class="fas fa-user"></i></div>
                    <div style="flex:1;">
                        <div class="pf-label">Full Name</div>
                        <div class="pf-value">{{ $user?->name ?? '—' }}</div>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-icon"><i class="fas fa-envelope"></i></div>
                    <div style="flex:1;">
                        <div class="pf-label">Email Address</div>
                        <div class="pf-value">{{ $user?->email ?? '—' }}</div>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-icon"><i class="fas fa-phone"></i></div>
                    <div style="flex:1;">
                        <div class="pf-label">Phone Number</div>
                        <div class="pf-value">{{ $user?->phone_number ?? '—' }}</div>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div style="flex:1;">
                        <div class="pf-label">Address</div>
                        <div class="pf-value">{{ $user?->address ?? '—' }}</div>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-icon"><i class="fas fa-shield-alt"></i></div>
                    <div style="flex:1;">
                        <div class="pf-label">Account Type</div>
                        <div class="pf-value">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;border-radius:20px;font-size:12px;font-weight:700;">
                                <i class="fas fa-check-circle" style="font-size:10px;"></i>
                                Customer
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div style="flex:1;">
                        <div class="pf-label">Member Since</div>
                        <div class="pf-value">{{ optional($user?->created_at)->format('d F Y') ?? '—' }}</div>
                    </div>
                </div>

                {{-- Quick links --}}
                <div style="margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6;display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#{{ $brand['primary'] }};color:#{{ $brand['text'] }};border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="fas fa-shopping-bag"></i> My Orders
                    </a>
                    <a href="{{ route('shop.front.store') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#f3f4f6;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fas fa-store"></i> Continue Shopping
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>
</div>
</div>
@endsection
