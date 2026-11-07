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
    /* Brand-coloured stat cards: four shades of the theme colour (s = mix with white/black) */
    .db-stat { --c: var(--primary); --c-dark: color-mix(in srgb, var(--c) 72%, black);
               background: linear-gradient(135deg, color-mix(in srgb, var(--c) 7%, white), color-mix(in srgb, var(--c) 15%, white));
               border-color: color-mix(in srgb, var(--c) 30%, white); }
    .db-stat .ico   { background: linear-gradient(135deg, color-mix(in srgb, var(--c) 85%, white), var(--c-dark)); }
    .db-stat .ico svg { color: var(--c-dark); }
    .db-stat .ghost { color: var(--c); }
    .db-stat:hover  { transform: translateY(-3px); box-shadow: 0 16px 32px color-mix(in srgb, var(--c) 18%, transparent); }
    .db-stat        { transition: transform .2s, box-shadow .2s; }
    .db-stat.green  { --c: var(--primary); }
    .db-stat.blue   { --c: color-mix(in srgb, var(--primary) 70%, black); }
    .db-stat.orange { --c: color-mix(in srgb, var(--primary) 80%, #f59e0b); }
    .db-stat.purple { --c: color-mix(in srgb, var(--primary) 65%, white); }
    .db-stat.purple .ico { background: linear-gradient(135deg, color-mix(in srgb, var(--primary) 55%, white), color-mix(in srgb, var(--primary) 85%, black)); }

    .db-grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: 20px; align-items: stretch; }
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


    @media (max-width: 1199px) {
        .db-stats { grid-template-columns: repeat(2, 1fr); }
        .db-grid { grid-template-columns: 1fr; }
        .db-chef { width: 240px; }
    }
    @media (max-width: 767px) {
        .db-chef { display: none; }
        .db-stats { grid-template-columns: 1fr; }
    }

    /* ── Live orders / floor plan / bottom row ── */
    .db-row { display: grid; gap: 20px; margin-bottom: 20px; }
    .db-row-live { grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr); }
    .db-row-bottom { grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, .8fr); margin-top: 20px; }
    .db-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
    .db-card-head h5 { margin: 0; font-weight: 700; font-size: 1.2rem; color: #14201a; }
    .db-card-head .muted, .db-floor .muted { color: #4b5a51; font-size: .85rem; }
    .db-card-head .link { color: var(--primary); font-weight: 600; font-size: .9rem; }
    .db-live, .db-floor, .db-res, .db-kitchen { padding: 20px 22px; }

    .lo-tabs { display: flex; gap: 2px; background: color-mix(in srgb, var(--primary) 7%, white); border-radius: 999px; padding: 4px; flex-wrap: wrap; }
    .lo-tabs button { border: 0; background: none; border-radius: 999px; padding: 5px 12px; font-size: .85rem; font-weight: 600; color: #2f3b34; }
    .lo-tabs button span { opacity: .55; font-weight: 500; margin-left: 2px; }
    .lo-tabs button.active { background: var(--primary); color: #fff; }
    .lo-tabs button.active span { opacity: .8; }
    .lo-list { display: flex; flex-direction: column; }
    .lo-row { display: grid; grid-template-columns: auto minmax(0, 1fr) auto auto 112px; align-items: center; gap: 12px; padding: 13px 0; border-top: 1px solid #eef1ef; }
    .lo-row.hide { display: none; }
    .lo-no { font-weight: 700; font-size: .95rem; color: var(--primary); white-space: nowrap; min-width: 84px; }
    .lo-main { min-width: 0; }
    .lo-main strong { display: block; font-weight: 600; color: #14201a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lo-main small { display: block; color: #4b5a51; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lo-amt { font-weight: 700; color: #14201a; white-space: nowrap; }
    .lo-badge { padding: 4px 11px; border-radius: 999px; font-size: .78rem; font-weight: 600; white-space: nowrap; }
    .lo-new   { background: #fdebd3; color: #9a5b00; }
    .lo-prep  { background: #e6eefc; color: #2552a3; }
    .lo-ready { background: color-mix(in srgb, var(--primary) 12%, white); color: color-mix(in srgb, var(--primary) 70%, black); }
    .lo-done  { background: #eef0ef; color: #5f6b64; }
    .lo-btn { border: 1.5px solid var(--primary); color: var(--primary); background: #fff; border-radius: 999px; padding: 5px 12px; font-size: .82rem; font-weight: 600; white-space: nowrap; transition: .15s; }
    .lo-btn:hover { background: var(--primary); color: #fff; }
    .lo-btn:disabled { opacity: .5; }
    .lo-all { display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; color: var(--primary); font-weight: 600; font-size: .9rem; }

    .fp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(92px, 1fr)); gap: 10px; max-height: 420px; overflow-y: auto; padding: 2px; }
    .fp-cell { aspect-ratio: 1 / 1; border-radius: 12px; border: 1.5px solid transparent; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; transition: transform .15s; }
    .fp-cell:not(:disabled):hover { transform: translateY(-2px); }
    .fp-cell strong { font-size: 1.05rem; font-weight: 800; }
    .fp-cell small { font-size: .72rem; font-weight: 600; opacity: .85; }
    .fp-free     { background: color-mix(in srgb, var(--primary) 8%, white); border-color: color-mix(in srgb, var(--primary) 25%, white) !important; color: color-mix(in srgb, var(--primary) 75%, black); }
    .fp-seated   { background: var(--primary); color: #fff; }
    .fp-reserved { background: #fdebd3; border-color: #f6cf9a !important; color: #9a5b00; }
    .fp-cleaning { background: #eef0ef; border-color: #dde1df !important; color: #5f6b64; }
    .fp-foot { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin-top: 14px; font-size: .85rem; color: #2f3b34; }
    .fp-dot { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 6px; vertical-align: -1px; border: 1px solid transparent; }

    .res-row { display: flex; align-items: center; gap: 12px; padding: 9px 0; }
    .res-time { background: color-mix(in srgb, var(--primary) 10%, white); color: color-mix(in srgb, var(--primary) 70%, black); font-weight: 700; border-radius: 10px; padding: 8px 10px; font-size: .9rem; }
    .res-main { flex: 1; min-width: 0; }
    .res-main strong { display: block; color: #14201a; font-weight: 600; }
    .res-main small { color: #4b5a51; }
    .res-table { color: #4b5a51; font-weight: 600; font-size: .85rem; }

    .db-top { overflow: hidden; display: flex; flex-direction: column; }
    .top-img { height: 150px; background: color-mix(in srgb, var(--primary) 10%, white) center / cover no-repeat; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: color-mix(in srgb, var(--primary) 40%, white); }
    .top-body { flex: 1; background: color-mix(in srgb, var(--primary) 72%, black); color: #fff; padding: 16px 20px; }
    .eyebrow { font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; opacity: .75; margin-bottom: 8px; }
    .top-row { display: flex; align-items: baseline; gap: 10px; padding: 4px 0; }
    .top-row span { flex: 1; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .top-row small { opacity: .75; white-space: nowrap; }

    .db-kitchen { background: linear-gradient(160deg, color-mix(in srgb, var(--primary) 70%, black), color-mix(in srgb, var(--primary) 55%, black)); color: #fff; display: flex; flex-direction: column; }
    .db-kitchen .eyebrow { color: #fff; }
    .k-state { display: flex; align-items: center; gap: 8px; font-size: 1.15rem; font-weight: 700; margin-bottom: 12px; }
    .k-dot { width: 10px; height: 10px; border-radius: 50%; background: #4ade80; box-shadow: 0 0 0 4px rgba(74, 222, 128, .25); }
    .k-dot.busy { background: #fbbf24; box-shadow: 0 0 0 4px rgba(251, 191, 36, .25); }
    .k-bar { height: 6px; border-radius: 6px; background: rgba(255, 255, 255, .2); overflow: hidden; margin-bottom: 8px; }
    .k-bar span { display: block; height: 100%; background: #fff; border-radius: 6px; }
    .db-kitchen small { opacity: .8; }
    .db-kitchen .btn { align-self: flex-start; margin-top: auto !important; background: #fff; color: color-mix(in srgb, var(--primary) 70%, black); box-shadow: none; }
    .small-empty { padding: 14px 0; }
    .small-empty .ico { width: 56px; height: 56px; font-size: 1.5rem; }

    @media (max-width: 1199px) { .db-row-live, .db-row-bottom { grid-template-columns: 1fr; } }
    @media (max-width: 767px) {
        .lo-row { grid-template-columns: 1fr auto; row-gap: 6px; }
        .lo-no { grid-column: 1 / -1; }
        .lo-btn-space { display: none; }
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


@php
    $canOrders = auth()->user()?->hasPermission('orders.manage');
    $canTables = auth()->user()?->hasPermission('tables.manage');
    $orderFlow = [
        'pending'   => ['New',       'lo-new',  'preparing', 'Start cooking'],
        'preparing' => ['Preparing', 'lo-prep', 'ready',     'Mark ready'],
        'ready'     => ['Ready',     'lo-ready','served',    'Serve'],
        'served'    => ['Served',    'lo-done', null,        null],
    ];
    $tableState = ['available' => ['fp-free', 'Free'], 'occupied' => ['fp-seated', 'Seated'], 'reserved' => ['fp-reserved', 'Reserved'], 'cleaning' => ['fp-cleaning', 'Cleaning']];
    $freeTables = $floorTables->where('status', 'available')->count();
@endphp

<div class="db-row db-row-live">
    {{-- ── Live orders ──────────────────────────── --}}
    <div class="db-card db-live">
        <div class="db-card-head">
            <h5>Live orders</h5>
            <div class="lo-tabs" id="loTabs" role="tablist">
                @foreach (['all' => 'All', 'pending' => 'New', 'preparing' => 'Preparing', 'ready' => 'Ready', 'served' => 'Served'] as $key => $label)
                    <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-filter="{{ $key }}">{{ $label }} <span>{{ $orderCounts[$key] }}</span></button>
                @endforeach
            </div>
        </div>
        <div class="lo-list" id="loList">
            @forelse ($liveOrders as $o)
                @php [$lbl, $cls, $next, $action] = $orderFlow[$o->status] ?? [ucfirst($o->status), 'lo-done', null, null]; @endphp
                <div class="lo-row" data-status="{{ $o->status }}" data-id="{{ $o->id }}">
                    <div class="lo-no">{{ $o->order_no }}</div>
                    <div class="lo-main">
                        <strong>{{ $o->table->name ?? ucfirst(str_replace('_', ' ', $o->order_type)) }}@if ($o->customer_name) · {{ $o->customer_name }}@endif</strong>
                        <small>{{ $o->items->map(fn ($i) => $i->name . ($i->quantity > 1 ? ' ×' . $i->quantity : ''))->implode(', ') ?: 'No items' }}</small>
                    </div>
                    <div class="lo-amt">{{ number_format($o->grand_total, 2) }}</div>
                    <span class="lo-badge {{ $cls }}">{{ $lbl }}</span>
                    @if ($canOrders && $next)
                        <button type="button" class="lo-btn" data-next="{{ $next }}">{{ $action }}</button>
                    @else
                        <span class="lo-btn-space"></span>
                    @endif
                </div>
            @empty
                <div class="db-empty">
                    <div class="ico"><i class="bi bi-clipboard-data"></i></div>
                    <strong>No orders yet today.</strong>
                    <span>New orders will appear here as they come in.</span>
                </div>
            @endforelse
        </div>
        <a href="{{ route('admin.order.index') }}" class="lo-all">View all orders <i class="bi bi-arrow-right"></i></a>
    </div>

    {{-- ── Floor plan ───────────────────────────── --}}
    <div class="db-card db-floor">
        <div class="db-card-head">
            <h5>Floor plan</h5>
            <span class="muted"><span id="fpFree">{{ $freeTables }}</span> free</span>
        </div>
        @if ($floorTables->isEmpty())
            <div class="db-empty"><div class="ico"><i class="bi bi-grid-3x3-gap"></i></div><strong>No tables yet.</strong><span>Add tables from the Tables page.</span></div>
        @else
            <div class="fp-grid" id="fpGrid">
                @foreach ($floorTables as $t)
                    @php [$tcls, $tlbl] = $tableState[$t->status] ?? ['fp-free', ucfirst($t->status)]; @endphp
                    <button type="button" class="fp-cell {{ $tcls }}" data-id="{{ $t->id }}" data-status="{{ $t->status }}" title="{{ $t->room->name ?? '' }} · {{ $tlbl }}" @unless ($canTables) disabled @endunless>
                        <strong>{{ $t->name }}</strong>
                        <small>{{ $t->capacity ?: $t->chairs_count }} seats</small>
                    </button>
                @endforeach
            </div>
            <div class="fp-foot">
                <span><i class="fp-dot fp-free"></i>Free</span>
                <span><i class="fp-dot fp-seated"></i>Seated</span>
                <span><i class="fp-dot fp-reserved"></i>Reserved</span>
                @if ($canTables)<span class="ms-auto muted">Tap a table to change</span>@endif
            </div>
        @endif
    </div>
</div>

<div class="db-grid">
    {{-- ── Sales overview + orders ─────────────────── --}}
    <div class="db-card db-sales">
        <div class="head">
            <div class="t">
                <i class="bi bi-bar-chart-line-fill"></i>
                <div><h5>Sales Overview</h5><small>Last 7 days · <strong>{{ number_format($weekTotal, 2) }}</strong> total</small></div>
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
            <path d="{{ $linePath }}" fill="none" stroke="var(--primary)" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
            @foreach ($points as $p)
                <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5.5" fill="var(--primary)" stroke="#fff" stroke-width="2"><title>{{ $p['label'] }}: {{ number_format($p['total'], 2) }}</title></circle>
                <text x="{{ $p['x'] }}" y="{{ $top + $ch + 26 }}" text-anchor="middle">{{ $p['label'] }}</text>
            @endforeach
        </svg>
    </div>
</div>

<div class="db-row db-row-bottom">
    {{-- ── Reservations tonight ─────────────────── --}}
    <div class="db-card db-res">
        <div class="db-card-head">
            <h5>Reservations tonight</h5>
            @if ($canTables)<a href="{{ route('admin.table.index') }}" class="link">View all</a>@endif
        </div>
        @forelse ($reservations as $r)
            <div class="res-row">
                <span class="res-time">{{ \Carbon\Carbon::parse($r->booking_time)->format('H:i') }}</span>
                <div class="res-main">
                    <strong>{{ $r->name }}</strong>
                    <small>{{ $r->party_size }} {{ Str::plural('guest', $r->party_size) }}@if ($r->floorTable?->room) · {{ $r->floorTable->room->name }}@endif</small>
                </div>
                <span class="res-table">{{ $r->floorTable->name ?? '' }}</span>
            </div>
        @empty
            <div class="db-empty small-empty"><div class="ico"><i class="bi bi-calendar2-week"></i></div><strong>No more reservations today.</strong></div>
        @endforelse
    </div>

    {{-- ── Top sellers today ────────────────────── --}}
    @php $hero = $topToday->first(fn ($i) => $i->menuItem?->image); @endphp
    <div class="db-card db-top">
        <div class="top-img" style="{{ $hero ? "background-image:url('" . Storage::url($hero->menuItem->image) . "')" : '' }}">
            @unless ($hero)<i class="bi bi-egg-fried"></i>@endunless
        </div>
        <div class="top-body">
            <div class="eyebrow">Top sellers today</div>
            @forelse ($topToday as $item)
                <div class="top-row"><span>{{ $item->name }}</span><small>{{ $item->qty }} sold</small><strong>{{ number_format($item->revenue, 2) }}</strong></div>
            @empty
                <div class="top-row"><span>No sales yet today</span></div>
            @endforelse
        </div>
    </div>

    {{-- ── Kitchen status ───────────────────────── --}}
    <div class="db-card db-kitchen">
        <div class="eyebrow">Kitchen status</div>
        <div class="k-state"><span class="k-dot {{ $kitchenPending > 0 ? 'busy' : '' }}"></span>{{ $kitchenPending > 0 ? $kitchenPending . ' ' . Str::plural('order', $kitchenPending) . ' in queue' : 'All caught up' }}</div>
        <div class="k-bar"><span style="width: {{ $capacityPct }}%"></span></div>
        <small>{{ $capacityPct }}% of tables occupied ({{ $tablesOccupied }}/{{ $tablesTotal }})</small>
        <a href="{{ route('admin.kitchen.index') }}" class="btn btn-primary btn-sm mt-3"><i class="bi bi-fire me-1"></i>Open Kitchen Display</a>
    </div>
</div>
@endsection

@push('js')

<script>
(function () {
    var CSRF = @json(csrf_token());
    var orderUrl = @json(route('admin.order.status', ['order' => '__ID__']));
    var tableUrl = @json(route('admin.table.status', ['table' => '__ID__']));
    var flow = @json($orderFlow);
    var tableState = @json($tableState);

    function patch(url, body) {
        return fetch(url, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Silent-Toast': '1' }, body: JSON.stringify(body) })
            .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); });
    }

    // Live order filter tabs
    var tabs = document.querySelectorAll('#loTabs button');
    function applyFilter(f) {
        document.querySelectorAll('#loList .lo-row').forEach(function (row) { row.classList.toggle('hide', f !== 'all' && row.dataset.status !== f); });
    }
    function recount() {
        var rows = document.querySelectorAll('#loList .lo-row');
        tabs.forEach(function (t) {
            var f = t.dataset.filter, n = 0;
            rows.forEach(function (r) { if (f === 'all' || r.dataset.status === f) n++; });
            t.querySelector('span').textContent = n;
        });
    }
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.remove('active'); });
            t.classList.add('active');
            applyFilter(t.dataset.filter);
        });
    });

    // Live order actions: move the order to its next status
    document.getElementById('loList')?.addEventListener('click', function (e) {
        var btn = e.target.closest('.lo-btn'); if (!btn) return;
        var row = btn.closest('.lo-row'), next = btn.dataset.next;
        btn.disabled = true;
        patch(orderUrl.replace('__ID__', row.dataset.id), { status: next }).then(function () {
            var f = flow[next];
            row.dataset.status = next;
            var badge = row.querySelector('.lo-badge');
            badge.className = 'lo-badge ' + f[1]; badge.textContent = f[0];
            if (f[2]) { btn.dataset.next = f[2]; btn.textContent = f[3]; btn.disabled = false; }
            else { btn.replaceWith(Object.assign(document.createElement('span'), { className: 'lo-btn-space' })); }
            notify.success('Order ' + row.querySelector('.lo-no').textContent.trim() + ' marked as ' + f[0].toLowerCase() + '.');
            recount();
            applyFilter(document.querySelector('#loTabs button.active').dataset.filter);
        }).catch(function () { btn.disabled = false; notify.error('Could not update the order. Please try again.'); });
    });

    // Floor plan: tap to cycle Free → Seated → Reserved → Free
    var cycle = { available: 'occupied', occupied: 'reserved', reserved: 'available', cleaning: 'available' };
    document.getElementById('fpGrid')?.addEventListener('click', function (e) {
        var cell = e.target.closest('.fp-cell'); if (!cell || cell.disabled) return;
        var next = cycle[cell.dataset.status] || 'available';
        cell.disabled = true;
        patch(tableUrl.replace('__ID__', cell.dataset.id), { status: next }).then(function () {
            cell.classList.remove(tableState[cell.dataset.status][0]);
            cell.dataset.status = next;
            cell.classList.add(tableState[next][0]);
            cell.title = cell.title.replace(/·.*$/, '· ' + tableState[next][1]);
            notify.success(cell.querySelector('strong').textContent + ' is now ' + tableState[next][1].toLowerCase() + '.');
            document.getElementById('fpFree').textContent = document.querySelectorAll('#fpGrid .fp-cell[data-status="available"]').length;
        }).catch(function () { notify.error('Could not update the table. Please try again.'); })
          .finally(function () { cell.disabled = false; });
    });
})();
</script>
@endpush
