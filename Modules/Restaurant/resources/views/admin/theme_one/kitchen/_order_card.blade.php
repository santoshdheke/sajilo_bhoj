@php
    $waitMins  = (int) $order->created_at->diffInMinutes(now());
    $isUrgent  = $waitMins >= 15;
    $orderData = json_encode([
        'id'            => $order->id,
        'order_no'      => $order->order_no,
        'table_no'      => $order->table->table_no ?? ucfirst(str_replace('_',' ',$order->order_type)),
        'customer_name' => $order->customer_name,
        'status'        => $order->status,
        'notes'         => $order->notes,
        'created_at'    => $order->created_at->toIso8601String(),
        'items'         => $order->items->map(fn($i) => [
            'name'     => $i->name,
            'quantity' => $i->quantity,
            'note'     => $i->note,
        ])->toArray(),
    ]);
@endphp

<div class="kds-card"
     id="kds-{{ $order->id }}"
     data-id="{{ $order->id }}"
     data-order="{{ htmlspecialchars($orderData, ENT_QUOTES) }}"
     onclick="openOrder({{ $order->id }})">

    <div class="kds-card-strip"></div>

    <div class="kds-card-body">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="kds-order-no">{{ $order->order_no }}</span>
            <span class="kds-table-tag">{{ $order->table->table_no ?? ucfirst(str_replace('_',' ',$order->order_type)) }}</span>
        </div>

        @if($order->customer_name)
        <div style="font-size:.75rem;color:var(--muted);margin-bottom:2px;">
            <i class="bi bi-person me-1"></i>{{ $order->customer_name }}
        </div>
        @endif

        <ul class="kds-items-list">
            @foreach($order->items->take(3) as $item)
            <li>
                <span class="kds-qty">{{ $item->quantity }}×</span>
                <span>{{ $item->name }}</span>
                @if($item->note)
                <span class="kds-item-note">{{ $item->note }}</span>
                @endif
            </li>
            @endforeach
            @if($order->items->count() > 3)
            <li style="color:var(--muted);font-size:.75rem;font-style:italic;">
                +{{ $order->items->count() - 3 }} more…
            </li>
            @endif
        </ul>

        @if($order->notes)
        <div style="margin-top:6px;font-size:.73rem;color:#9a6700;background:#fffbf0;
                    border-radius:6px;padding:3px 7px;">
            <i class="bi bi-sticky me-1"></i>{{ Str::limit($order->notes, 48) }}
        </div>
        @endif
    </div>

    <div class="kds-card-foot" onclick="event.stopPropagation()">
        <span class="kds-timer {{ $isUrgent ? 'urgent' : '' }}"
              data-ts="{{ $order->created_at->toIso8601String() }}">
            <i class="bi bi-clock{{ $isUrgent ? '-fill' : '' }} me-1"></i>
            <span class="timer-val">{{ $waitMins < 1 ? 'Just now' : $waitMins . ' min' }}</span>
        </span>

        <div class="d-flex gap-1 align-items-center">
            {{-- Next-column shortcut button --}}
            @php
                $colIndex   = $columns->search(fn($c) => $c->slug === $order->status);
                $nextCol    = $colIndex !== false ? $columns->get($colIndex + 1) : null;
            @endphp

            @if($nextCol)
            <button class="kds-act kds-act-primary"
                    onclick="cardAction(event, {{ $order->id }}, '{{ $nextCol->slug }}')">
                <i class="bi bi-arrow-right"></i>{{ Str::words($nextCol->name, 1, '') }}
            </button>
            @else
            <button class="kds-act" style="background:#edfaf3;color:#198754;"
                    onclick="cardAction(event, {{ $order->id }}, 'served')">
                <i class="bi bi-bag-check"></i>Serve
            </button>
            @endif

            <button class="act-del"
                    onclick="event.stopPropagation();if(confirm('Delete?'))fetch('/admin/kitchen/orders/{{ $order->id }}',{method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())"
                    title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>
