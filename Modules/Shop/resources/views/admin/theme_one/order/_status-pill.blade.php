@php($map = ['delivered' => 'pill-green', 'cancelled' => 'pill-red', 'pending' => 'pill-gray', 'processing' => 'pill-blue', 'shipped' => 'pill-purple'])
<span class="pill {{ $map[$order->order_status] ?? 'pill-gray' }}">{{ ucfirst($order->order_status) }}</span>
