@extends('admin.theme_one.component.layout')

@section('content')
@php
    $user = auth()->user();

    // Line chart geometry (SVG units)
    $totals  = collect($chartBars)->pluck('total')->map(fn ($v) => (float) $v);
    $rawMax  = max(1, $totals->max() ?? 0);
    $step    = $rawMax <= 100 ? 20 : (int) (ceil($rawMax / 5 / 10 ** (strlen((int) ($rawMax / 5)) - 1)) * 10 ** (strlen((int) ($rawMax / 5)) - 1));
    $yMax    = $step * 5;
    $cw = 760; $ch = 170; $left = 40; $top = 10;
    $count   = max(1, count($chartBars) - 1);
    $points  = collect($chartBars)->values()->map(fn ($b, $i) => [
        'x' => $left + ($cw - $left) * $i / $count,
        'y' => $top + $ch - ($ch * ((float) $b['total'] / $yMax)),
        'label' => $b['label'],
        'total' => (float) $b['total'],
    ]);
    $linePath = $points->map(fn ($p, $i) => ($i ? 'L' : 'M') . round($p['x'], 1) . ' ' . round($p['y'], 1))->implode(' ');
    $areaPath = $linePath . ' L' . round($points->last()['x'], 1) . ' ' . ($top + $ch) . ' L' . $left . ' ' . ($top + $ch) . ' Z';

    $statusMap = [
        'pending'   => ['status-pending',   'Pending'],
        'preparing' => ['status-progress',  'Preparing'],
        'ready'     => ['status-ready',     'Ready'],
        'served'    => ['status-ready',     'Served'],
        'cancelled' => ['status-cancelled', 'Cancelled'],
    ];

    // Stat card icons (white glyph in the circle + faded glyph on the right), drawn to match the design.
    $ico = [
        'dollar'    => '<span class="glyph-text">$</span>',
        'clipboard' => '<svg viewBox="0 0 24 24" fill="#fff"><path d="M8 3h8v3H8z" opacity=".9"/><path d="M6 4.5h1.5V7h9V4.5H18a1.5 1.5 0 0 1 1.5 1.5v14A1.5 1.5 0 0 1 18 21.5H6A1.5 1.5 0 0 1 4.5 20V6A1.5 1.5 0 0 1 6 4.5z"/><g stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M8 11h8M8 14h8M8 17h5"/></g></svg>',
        'table'     => '<svg viewBox="0 0 24 24" fill="#fff"><rect x="5" y="8" width="14" height="2.6" rx="1"/><rect x="7.2" y="10.4" width="1.8" height="8" rx=".6"/><rect x="15" y="10.4" width="1.8" height="8" rx=".6"/><path d="M2 6.5h1.8v6H6v1.8H3.8v4H2zM22 6.5h-1.8v6H18v1.8h2.2v4H22z"/></svg>',
        'flame'     => '<svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2c1 3.5-1.5 5-1.5 8 0 1 .6 2 1.6 2 1.3 0 1.9-1.3 1.6-3 2.6 1.7 4.3 4.3 4.3 7.2A6 6 0 0 1 12 22a6 6 0 0 1-6-5.8C6 11 12 8.5 12 2z"/></svg>',
    ];
    $ghostIco = [
        'growth' => '<svg viewBox="0 0 48 48" fill="currentColor"><rect x="6" y="30" width="7" height="12" rx="1.5"/><rect x="16" y="24" width="7" height="18" rx="1.5"/><rect x="26" y="18" width="7" height="24" rx="1.5"/><rect x="36" y="10" width="7" height="32" rx="1.5"/><path d="M6 24 18 14l8 5L40 6" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M34 5h7v7" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'cart'   => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h6l5 24h24l5-17H12"/><circle cx="17" cy="39" r="3"/><circle cx="35" cy="39" r="3"/></svg>',
        'table'  => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M10 18h28M14 18v20M34 18v20M12 28h24"/><path d="M4 12v26M4 28h6M44 12v26M44 28h-6"/></svg>',
        'clock'  => '<svg viewBox="0 0 48 48"><circle cx="24" cy="24" r="19" fill="currentColor" opacity=".55"/><path d="M24 13v12l8 5" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];

    $stats = [
        ['green',  $ico['dollar'],    number_format($todaySales, 2),        "Today's Sales",   $ghostIco['growth']],
        ['blue',   $ico['clipboard'], $ordersToday,                         'Orders Today',    $ghostIco['cart']],
        ['orange', $ico['table'],     $tablesOccupied . '/' . $tablesTotal, 'Occupied Tables', $ghostIco['table']],
        ['purple', $ico['flame'],     $kitchenPending,                      'Kitchen Pending', $ghostIco['clock']],
    ];

    // Facts the chat assistant can answer from
    $chatFacts = [
        'sales'     => number_format($todaySales, 2),
        'orders'    => $ordersToday,
        'tables'    => $tablesOccupied . ' of ' . $tablesTotal,
        'kitchen'   => $kitchenPending,
        'month'     => number_format($monthRevenue, 2),
        'avgBill'   => number_format($avgBill, 2),
        'customers' => $newCustomers,
        'top'       => $topItems->map(fn ($i) => $i->name . ' (' . $i->qty . ')')->implode(', '),
    ];
@endphp

<style>
    .db-hero { position: relative; display: flex; justify-content: space-between; align-items: flex-end; min-height: 150px; margin-bottom: 4px; }
    .db-hero .hello { font-size: 1.6rem; font-weight: 500; color: #1c2620; margin: 0; }
    .db-hero h1 { font-size: clamp(1.9rem, 3.2vw, 2.6rem); font-weight: 800; letter-spacing: -.02em; color: #14201a; margin: 0 0 6px; }
    .db-hero p { color: #4b5a51; margin: 0 0 18px; }
    .db-chef { position: absolute; right: 6%; bottom: -30px; width: 330px; max-width: 45%; pointer-events: none; z-index: 0; }

    .db-stats { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 22px; }
    .db-stat { position: relative; overflow: hidden; display: flex; align-items: center; gap: 16px; padding: 22px 20px; border-radius: 18px; border: 1.5px solid; box-shadow: 0 10px 26px rgba(15, 40, 25, .06); }
    .db-stat .ico { width: 58px; height: 58px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 8px 16px rgba(0,0,0,.12); }
    .db-stat .val { font-size: 1.9rem; font-weight: 800; line-height: 1; color: #14201a; }
    .db-stat .lbl { font-size: .92rem; color: #2f3b34; margin-top: 4px; }
    .db-stat .ghost { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); width: 52px; height: 52px; opacity: .35; }
    .db-stat .ghost svg { width: 100%; height: 100%; display: block; }
    .db-stat .ico svg { width: 30px; height: 30px; }
    .db-stat .ico .glyph-text { font-size: 2rem; font-weight: 800; line-height: 1; }
    .db-stat.blue .ico svg { color: #2563eb; }
    .db-stat.green  { background: linear-gradient(135deg, #e6f7ec, #d4f0de); border-color: #9fdcb3; }
    .db-stat.green .ico { background: linear-gradient(135deg, #22c55e, #15803d); } .db-stat.green .ghost { color: #16a34a; }
    .db-stat.blue   { background: linear-gradient(135deg, #e8f0ff, #d9e6ff); border-color: #a9c3f5; }
    .db-stat.blue .ico { background: linear-gradient(135deg, #3b82f6, #1d4ed8); } .db-stat.blue .ghost { color: #2563eb; }
    .db-stat.orange { background: linear-gradient(135deg, #fff1e4, #ffe3cc); border-color: #f8c597; }
    .db-stat.orange .ico { background: linear-gradient(135deg, #fb923c, #ea580c); } .db-stat.orange .ghost { color: #ea580c; }
    .db-stat.purple { background: linear-gradient(135deg, #f3ecff, #e9defd); border-color: #cdb6f5; }
    .db-stat.purple .ico { background: linear-gradient(135deg, #8b5cf6, #6d28d9); } .db-stat.purple .ghost { color: #7c3aed; }

    .db-grid { display: grid; grid-template-columns: minmax(0, 1.85fr) minmax(0, 1fr); gap: 20px; align-items: stretch; }
    .db-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(15, 40, 25, .07); }
    .db-sales { padding: 22px 22px 18px; }
    .db-sales .head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .db-sales .head .t { display: flex; gap: 12px; align-items: center; }
    .db-sales .head .t .bi { width: 40px; height: 40px; border-radius: 50%; background: color-mix(in srgb, var(--primary) 12%, white); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .db-sales h5 { font-weight: 700; margin: 0; font-size: 1.3rem; }
    .db-sales small { color: #4b5a51; }
    .db-chart { width: 100%; height: auto; display: block; }
    .db-chart text { font-size: 12px; fill: #2f3b34; font-family: inherit; }
    .db-orders { margin-top: 12px; border: 1px solid #eef1ef; border-radius: 14px; overflow: hidden; }
    .db-orders table { margin: 0; }
    .db-orders thead th { background: color-mix(in srgb, var(--primary) 5%, white); font-size: .8rem; font-weight: 700; color: #1c2620; text-transform: uppercase; padding: 12px 14px; border: 0; white-space: nowrap; }
    .db-orders thead th .bi { color: var(--primary); margin-right: 6px; }
    .db-orders tbody td { padding: 12px 14px; font-size: .9rem; }
    .db-empty { text-align: center; padding: 26px 12px 22px; }
    .db-empty .ico { width: 82px; height: 82px; border-radius: 50%; margin: 0 auto 10px; background: color-mix(in srgb, var(--primary) 12%, white); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; }
    .db-empty strong { display: block; color: #14201a; }
    .db-empty span { font-size: .88rem; color: #4b5a51; }

    .db-chat { display: flex; flex-direction: column; overflow: hidden; min-height: 520px; }
    .db-chat .chead { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: linear-gradient(135deg, color-mix(in srgb, var(--primary) 70%, black), color-mix(in srgb, var(--primary) 55%, black)); color: #fff; }
    .db-chat .chead h5 { margin: 0; font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; color: #fff; }
    .db-chat .chead h5 .bi { width: 34px; height: 34px; border-radius: 50%; background: #fff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .db-chat .chead button { background: none; border: 0; color: #fff; font-size: 1.3rem; line-height: 1; }
    .db-chat .cbody { flex: 1; padding: 18px 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; max-height: 420px; }
    .db-chat.min .cbody, .db-chat.min .cfoot, .db-chat.min .chips { display: none; }
    .db-chat.min { min-height: 0; }
    .msg { display: flex; gap: 10px; align-items: flex-end; max-width: 92%; }
    .msg .av { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; }
    .msg.bot .av { background: #e8f0ff; color: #2563eb; }
    .msg.me { align-self: flex-end; flex-direction: row-reverse; }
    .msg.me .av { background: linear-gradient(135deg, var(--primary), color-mix(in srgb, var(--primary) 70%, black)); color: #fff; font-size: .78rem; font-weight: 700; }
    .msg .bubble { padding: 10px 14px 6px; border-radius: 14px; font-size: .92rem; line-height: 1.45; }
    .msg.bot .bubble { background: color-mix(in srgb, var(--primary) 9%, white); color: #14201a; border-bottom-left-radius: 4px; }
    .msg.me .bubble { background: linear-gradient(135deg, color-mix(in srgb, var(--primary) 70%, black), color-mix(in srgb, var(--primary) 55%, black)); color: #fff; border-bottom-right-radius: 4px; }
    .msg .bubble .big { display: block; font-size: 1.45rem; font-weight: 800; color: var(--primary); }
    .msg .bubble time { display: block; text-align: right; font-size: .68rem; opacity: .6; margin-top: 2px; }
    .chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 0 16px 10px; }
    .chips button { border: 1px solid color-mix(in srgb, var(--primary) 25%, white); background: #fff; color: color-mix(in srgb, var(--primary) 70%, black); border-radius: 999px; padding: 4px 12px; font-size: .78rem; font-weight: 600; }
    .chips button:hover { background: color-mix(in srgb, var(--primary) 8%, white); }
    .db-chat .cfoot { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-top: 1px solid #eef1ef; background: #fafcfb; }
    .db-chat .cfoot .bi-paperclip { font-size: 1.2rem; color: #4b5a51; }
    .db-chat .cfoot input { flex: 1; border: 1px solid #e5e9e7; border-radius: 12px; padding: 10px 14px; font-size: .9rem; background: #fff; }
    .db-chat .cfoot input:focus { outline: none; border-color: var(--primary); }
    .db-chat .cfoot button { width: 46px; height: 46px; border: 0; border-radius: 12px; background: linear-gradient(135deg, #22c55e, #15803d); color: #fff; font-size: 1.1rem; }

    @media (max-width: 1199px) {
        .db-stats { grid-template-columns: repeat(2, 1fr); }
        .db-grid { grid-template-columns: 1fr; }
        .db-chef { width: 240px; }
    }
    @media (max-width: 767px) {
        .db-chef { display: none; }
        .db-stats { grid-template-columns: 1fr; }
    }
</style>

{{-- ── Welcome ─────────────────────────────────────── --}}
<div class="db-hero">
    <div class="position-relative" style="z-index:1;">
        <p class="hello">Welcome Back,</p>
        <h1>{{ $user?->name ?? 'there' }} <span aria-hidden="true">👋</span></h1>
        <p>Here's what's happening with your restaurant today.</p>
    </div>

    {{-- Chef illustration --}}
    <svg class="db-chef" viewBox="0 0 330 200" aria-hidden="true">
        <g fill="color-mix(in srgb, var(--primary) 45%, white)" opacity=".55">
            <path d="M18 196c-10-40 8-80 46-96 8 40-10 80-46 96z"/>
            <path d="M300 190c14-44 0-86-34-104-10 42 4 84 34 104z"/>
            <path d="M262 120c22-16 50-14 64 2-20 16-46 16-64-2z"/>
        </g>
        {{-- body --}}
        <path d="M112 200c0-40 22-66 54-66s54 26 54 66z" fill="#fff" stroke="#dfe6e2" stroke-width="2"/>
        <path d="M150 140l16 18 16-18" fill="none" stroke="var(--primary)" stroke-width="7" stroke-linejoin="round"/>
        <circle cx="152" cy="172" r="3.5" fill="#1c2620"/><circle cx="152" cy="188" r="3.5" fill="#1c2620"/>
        <circle cx="180" cy="172" r="3.5" fill="#1c2620"/><circle cx="180" cy="188" r="3.5" fill="#1c2620"/>
        {{-- head --}}
        <circle cx="166" cy="104" r="30" fill="#f6c9a0"/>
        <path d="M137 98c4-14 16-20 29-20s25 6 29 20c-8-6-18-8-29-8s-21 2-29 8z" fill="#3b2a20"/>
        <circle cx="155" cy="104" r="3" fill="#1c2620"/><circle cx="177" cy="104" r="3" fill="#1c2620"/>
        <path d="M156 116c6 6 14 6 20 0" fill="none" stroke="#a0522d" stroke-width="3" stroke-linecap="round"/>
        <circle cx="148" cy="113" r="4" fill="#f4a3a3" opacity=".6"/><circle cx="184" cy="113" r="4" fill="#f4a3a3" opacity=".6"/>
        {{-- hat --}}
        <rect x="140" y="66" width="52" height="16" rx="4" fill="#fff" stroke="#dfe6e2" stroke-width="2"/>
        <path d="M140 70c-16-2-22-18-12-28 6-8 18-8 22-2 4-12 28-12 32 0 4-6 16-6 22 2 10 10 4 26-12 28z" fill="#fff" stroke="#dfe6e2" stroke-width="2"/>
        {{-- ok hand --}}
        <path d="M112 150c-10-6-14-20-8-30" fill="none" stroke="#fff" stroke-width="14" stroke-linecap="round"/>
        <circle cx="100" cy="112" r="12" fill="#f6c9a0"/>
        <circle cx="100" cy="106" r="5" fill="none" stroke="#e0a57a" stroke-width="3"/>
        {{-- cloche arm --}}
        <path d="M220 150c12-6 20-18 22-30" fill="none" stroke="#fff" stroke-width="14" stroke-linecap="round"/>
        <circle cx="244" cy="118" r="10" fill="#f6c9a0"/>
        <path d="M206 112a42 32 0 0 1 84 0z" fill="#cfd8d4"/>
        <path d="M216 104a32 24 0 0 1 22-18" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" opacity=".8"/>
        <circle cx="248" cy="78" r="5" fill="#aab6b1"/>
        <rect x="200" y="110" width="96" height="7" rx="3.5" fill="#9aa8a2"/>
    </svg>
</div>

{{-- ── Stat cards ──────────────────────────────────── --}}
<div class="db-stats">
    @foreach ($stats as [$color, $icon, $value, $label, $ghost])
    <div class="db-stat {{ $color }}">
        <div class="ico">{!! $icon !!}</div>
        <div>
            <div class="val">{{ $value }}</div>
            <div class="lbl">{{ $label }}</div>
        </div>
        <span class="ghost" aria-hidden="true">{!! $ghost !!}</span>
    </div>
    @endforeach
</div>

<div class="db-grid">
    {{-- ── Sales overview + orders ─────────────────── --}}
    <div class="db-card db-sales">
        <div class="head">
            <div class="t">
                <i class="bi bi-bar-chart-line-fill"></i>
                <div><h5>Sales Overview</h5><small>Last 7 days revenue snapshot</small></div>
            </div>
            <a href="{{ route('admin.report.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-bar-chart me-1"></i>View Reports <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <svg class="db-chart" viewBox="0 0 {{ $cw + 20 }} {{ $ch + 44 }}" role="img" aria-label="Revenue for the last 7 days">
            @for ($i = 0; $i <= 5; $i++)
                @php $gy = $top + $ch - ($ch * $i / 5); @endphp
                <line x1="{{ $left }}" x2="{{ $cw }}" y1="{{ $gy }}" y2="{{ $gy }}" stroke="#e6ebe8" stroke-width="1"/>
                <text x="{{ $left - 10 }}" y="{{ $gy + 4 }}" text-anchor="end">{{ number_format($step * $i) }}</text>
            @endfor
            @foreach ($points as $p)
                <line x1="{{ $p['x'] }}" x2="{{ $p['x'] }}" y1="{{ $top }}" y2="{{ $top + $ch }}" stroke="#f0f3f1" stroke-width="1"/>
            @endforeach
            <path d="{{ $areaPath }}" fill="color-mix(in srgb, var(--primary) 12%, transparent)"/>
            <path d="{{ $linePath }}" fill="none" stroke="#16a34a" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
            @foreach ($points as $p)
                <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5.5" fill="#16a34a" stroke="#fff" stroke-width="2"><title>{{ $p['label'] }}: {{ number_format($p['total'], 2) }}</title></circle>
                <text x="{{ $p['x'] }}" y="{{ $top + $ch + 26 }}" text-anchor="middle">{{ $p['label'] }}</text>
            @endforeach
        </svg>

        <div class="db-orders">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i>Order No</th>
                            <th><i class="bi bi-shop-window"></i>Table</th>
                            <th><i class="bi bi-person-fill"></i>Customer</th>
                            <th><i class="bi bi-activity"></i>Status</th>
                            <th class="text-end"><i class="bi bi-cash-stack"></i>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            @php [$cls, $lbl] = $statusMap[$order->status] ?? ['status-pending', ucfirst($order->status)]; @endphp
                            <tr>
                                <td class="fw-semibold">{{ $order->order_no }}</td>
                                <td>{{ $order->table->table_no ?? ucfirst(str_replace('_', ' ', $order->order_type)) }}</td>
                                <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                                <td><span class="status-badge {{ $cls }}">{{ $lbl }}</span></td>
                                <td class="text-end fw-semibold">{{ number_format($order->grand_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-0">
                                <div class="db-empty">
                                    <div class="ico"><i class="bi bi-clipboard-data"></i></div>
                                    <strong>No orders yet.</strong>
                                    <span>Start taking orders to see data here.</span>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Chat support ────────────────────────────── --}}
    <div class="db-card db-chat" id="dbChat">
        <div class="chead">
            <h5><i class="bi bi-chat-dots-fill"></i>Chat Support</h5>
            <button type="button" id="chatMin" aria-label="Minimise chat">&minus;</button>
        </div>
        <div class="cbody" id="chatBody">
            <div class="msg bot">
                <span class="av"><i class="bi bi-robot"></i></span>
                <div class="bubble">Hello! 👋<br>How can I help you today?<time id="chatHelloTime"></time></div>
            </div>
        </div>
        <div class="chips" id="chatChips">
            <button type="button">Today's sales</button>
            <button type="button">Orders today</button>
            <button type="button">Tables</button>
            <button type="button">Top items</button>
        </div>
        <form class="cfoot" id="chatForm" autocomplete="off">
            <i class="bi bi-paperclip" aria-hidden="true"></i>
            <input type="text" id="chatInput" placeholder="Type a message..." aria-label="Message">
            <button type="submit" aria-label="Send"><i class="bi bi-send-fill"></i></button>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    var facts    = @json($chatFacts);
    var initials = @json($user?->initials ?: 'U');
    var body     = document.getElementById('chatBody');


    function esc(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
    function time() { return new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }); }
    document.getElementById('chatHelloTime').textContent = time();

    function add(who, html) {
        var el = document.createElement('div');
        el.className = 'msg ' + who;
        el.innerHTML = '<span class="av">' + (who === 'bot' ? '<i class="bi bi-robot"></i>' : esc(initials)) + '</span>'
                     + '<div class="bubble">' + html + '<time>' + time() + '</time></div>';
        body.appendChild(el);
        body.scrollTop = body.scrollHeight;
    }

    // Answers simple questions from today's dashboard numbers.
    function reply(q) {
        q = q.toLowerCase();
        if (/sale|revenue|earn|income/.test(q)) {
            if (/month/.test(q)) return "This month's revenue is:<span class=\"big\">" + facts.month + '</span>';
            return "Today's sales is:<span class=\"big\">" + facts.sales + '</span>';
        }
        if (/order/.test(q))            return 'Orders today:<span class="big">' + facts.orders + '</span>';
        if (/table|occup|seat/.test(q)) return 'Occupied tables:<span class="big">' + facts.tables + '</span>';
        if (/kitchen|pending|cook/.test(q)) return 'Orders pending in the kitchen:<span class="big">' + facts.kitchen + '</span>';
        if (/top|best|popular|item/.test(q)) return facts.top ? 'Top selling items: ' + esc(facts.top) : 'No sales data yet.';
        if (/average|avg|bill/.test(q)) return 'Average bill:<span class="big">' + facts.avgBill + '</span>';
        if (/customer/.test(q))         return 'New customers this month:<span class="big">' + facts.customers + '</span>';
        if (/hi|hello|hey|namaste/.test(q)) return 'Hi! Ask me about sales, orders, tables, kitchen or top items.';
        return "Sorry, I can only answer about today's sales, orders, tables, kitchen, top items, average bill and customers.";
    }

    function ask(text) {
        text = text.trim();
        if (!text) return;
        add('me', esc(text));
        setTimeout(function () { add('bot', reply(text)); }, 350);
    }

    document.getElementById('chatForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var input = document.getElementById('chatInput');
        ask(input.value);
        input.value = '';
    });
    document.querySelectorAll('#chatChips button').forEach(function (b) {
        b.addEventListener('click', function () { ask(b.textContent); });
    });
    document.getElementById('chatMin').addEventListener('click', function () {
        var chat = document.getElementById('dbChat');
        chat.classList.toggle('min');
        this.innerHTML = chat.classList.contains('min') ? '+' : '&minus;';
    });
})();
</script>
@endpush
