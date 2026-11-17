@extends($theme_path . 'common.layout.master_layout')

@section('contant')
@php
    $status       = request('order-status', 'pending');
    $pageTitle    = match($status) {
        'delivered' => 'Purchase History',
        'cancelled' => 'Cancelled Orders',
        default     => 'My Orders',
    };
    $pageIcon = match($status) {
        'delivered' => 'fas fa-check-circle',
        'cancelled' => 'fas fa-times-circle',
        default     => 'fas fa-clock',
    };
    $emptyMsg = match($status) {
        'delivered' => 'No delivered orders yet.',
        'cancelled' => 'No cancelled orders.',
        default     => 'No pending orders right now.',
    };
@endphp

<div class="account-page">
<div class="container">
<div class="account-layout">

    {{-- Sidebar --}}
    @include($theme_path . 'user.sidebar')

    {{-- Main content --}}
    <div>

        {{-- Status tabs --}}
        <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
            @foreach([
                ['pending',   'fas fa-clock',        'Pending'],
                ['delivered', 'fas fa-check-circle', 'Delivered'],
                ['cancelled', 'fas fa-times-circle', 'Cancelled'],
            ] as [$s, $ic, $lbl])
            @php $isActive = $status === $s; @endphp
            <a href="{{ route('shop.front.account.orders', ['order-status' => $s]) }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid;transition:all .15s;
                      {{ $isActive ? 'background:#'.$brand['primary'].';color:#'.$brand['text'].';border-color:#'.$brand['primary'].';box-shadow:0 3px 10px rgba(0,0,0,.12);' : 'background:#fff;color:#6b7280;border-color:#e8eaed;' }}">
                <i class="{{ $ic }}" style="font-size:12px;"></i> {{ $lbl }}
            </a>
            @endforeach
        </div>

        <div class="account-content">
            <div class="acc-content-header">
                <h2 class="acc-content-title">
                    <i class="{{ $pageIcon }}"></i>
                    {{ $pageTitle }}
                </h2>
                @if(isset($orders) && count($orders) > 0)
                    <span style="font-size:13px;font-weight:600;color:#9ca3af;">{{ count($orders) }} order{{ count($orders) != 1 ? 's' : '' }}</span>
                @endif
            </div>

            <div style="padding:0;">
                @if(isset($orders) && count($orders) > 0)
                    @foreach($orders as $order)
                    @php
                        $os = strtolower($order->order_status ?? 'pending');
                        $ps = strtolower($order->payment_status ?? 'unpaid');
                        $osBadge = match($os) {
                            'delivered'=>'badge-delivered','cancelled'=>'badge-cancelled',
                            'shipped'=>'badge-shipped','processing'=>'badge-processing',
                            default=>'badge-pending',
                        };
                        $psBadge = $ps === 'paid' ? 'badge-paid' : 'badge-unpaid';
                    @endphp
                    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;transition:background .1s;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">

                        {{-- Order header row --}}
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                            <div>
                                <div style="font-size:15px;font-weight:800;color:#111;margin-bottom:4px;">
                                    {{ $order->order_number_format }}
                                </div>
                                <div style="font-size:12px;color:#9ca3af;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                    <span><i class="fas fa-calendar-alt" style="margin-right:4px;"></i>{{ optional($order->created_at)->format('d M Y') ?? '—' }}</span>
                                    <span><i class="fas fa-box" style="margin-right:4px;"></i>{{ count($order->orderProduces) }} item{{ count($order->orderProduces) != 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span class="order-badge {{ $osBadge }}">{{ ucfirst($os) }}</span>
                                <span class="order-badge {{ $psBadge }}">{{ ucfirst($ps) }}</span>
                            </div>
                        </div>

                        {{-- Products preview --}}
                        @if(count($order->orderProduces) > 0)
                        <div style="display:flex;gap:8px;margin:14px 0;flex-wrap:wrap;">
                            @foreach($order->orderProduces->take(4) as $op)
                                @if(isset($op->product))
                                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#f8f9fb;border:1px solid #e8eaed;border-radius:8px;font-size:12.5px;">
                                    @if($op->product->image_path)
                                        <img src="{{ $op->product->image_path }}"
                                             style="width:36px;height:36px;object-fit:cover;border-radius:6px;flex-shrink:0;"
                                             alt="{{ $op->product->name }}"
                                             onerror="this.style.display='none'">
                                    @endif
                                    <div>
                                        <div style="font-weight:600;color:#111;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $op->product->name }}</div>
                                        <div style="color:#9ca3af;font-size:11.5px;">×{{ $op->quantity }}</div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            @if(count($order->orderProduces) > 4)
                                <div style="display:flex;align-items:center;padding:8px 12px;background:#f3f4f6;border-radius:8px;font-size:12px;color:#6b7280;font-weight:600;">
                                    +{{ count($order->orderProduces) - 4 }} more
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- Footer row --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                            <div style="font-size:15px;font-weight:800;color:#111;">
                                Total: <span style="color:#{{ $brand['primary'] }};">Rs. {{ number_format($order->total_amount) }}</span>
                            </div>
                            <a href="{{ route('shop.front.order.show', $order->order_number) }}"
                               style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#{{ $brand['primary'] }};color:#{{ $brand['text'] }};border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;transition:opacity .15s;"
                               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-eye" style="font-size:12px;"></i> View Details
                            </a>
                        </div>

                    </div>
                    @endforeach
                @else
                    <div class="acc-empty">
                        <div class="acc-empty-icon">
                            <i class="{{ $pageIcon }}"></i>
                        </div>
                        <h4>{{ $emptyMsg }}</h4>
                        <p>When you have {{ $status === 'cancelled' ? 'cancelled' : ($status === 'delivered' ? 'delivered' : 'pending') }} orders, they'll appear here.</p>
                        <a href="{{ route('shop.front.store') }}"
                           style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#{{ $brand['primary'] }};color:#{{ $brand['text'] }};border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                            <i class="fas fa-store"></i> Shop Now
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
</div>
</div>
@endsection
