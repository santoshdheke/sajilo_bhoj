@php($map = ['paid' => 'pill-green', 'credit' => 'pill-amber', 'pending' => 'pill-gray', 'failed' => 'pill-red', 'refunded' => 'pill-purple'])
<span class="pill {{ $map[$order->payment_status] ?? 'pill-gray' }}">{{ ucfirst($order->payment_status) }}</span>
