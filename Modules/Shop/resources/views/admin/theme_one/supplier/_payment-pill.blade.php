@php($map = ['paid' => 'pill-green', 'partial' => 'pill-amber', 'unpaid' => 'pill-red'])
<span class="pill {{ $map[$po->payment_status] ?? 'pill-gray' }}">{{ ucfirst($po->payment_status) }}</span>
