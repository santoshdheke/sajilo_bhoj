@extends('admin.theme_one.component.layout')

@section('content')
<style>
.report-stat    { padding:22px 24px; position:relative; overflow:hidden; }
.report-stat .ico { width:48px;height:48px;border-radius:15px;display:flex;align-items:center;
                  justify-content:center;font-size:1.25rem;margin-bottom:14px; }
.report-stat .val { font-size:1.75rem;font-weight:800;line-height:1.1; }
.report-stat .lbl { font-size:.78rem;color:var(--muted);margin-top:3px; }
.report-stat .sub { font-size:.76rem;margin-top:6px;font-weight:600; }
.report-stat::after { content:'';position:absolute;right:-28px;bottom:-28px;
                  width:100px;height:100px;border-radius:50%;
                  background:rgba(25,135,84,.06);pointer-events:none; }
.bar-chart      { display:flex;align-items:flex-end;gap:4px;height:160px;padding:0 4px; }
.bar-col        { flex:1;display:flex;flex-direction:column;align-items:center;gap:3px; }
.bar-fill       { width:100%;border-radius:6px 6px 2px 2px;min-height:4px;
                  background:linear-gradient(180deg,#34d399,#198754);transition:.3s; }
.bar-label      { font-size:.62rem;color:var(--muted);white-space:nowrap; }
.type-row       { display:flex;align-items:center;gap:10px;margin-bottom:10px; }
.type-dot       { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
.type-bar-bg    { flex:1;height:8px;border-radius:999px;background:#f0f0f0;overflow:hidden; }
.type-bar-fill  { height:100%;border-radius:999px;transition:.5s; }
.type-pct       { font-size:.78rem;font-weight:700;min-width:36px;text-align:right; }
.rank-no        { width:28px;height:28px;border-radius:8px;background:#edfaf3;color:#198754;
                  font-weight:700;font-size:.8rem;display:flex;align-items:center;
                  justify-content:center;flex-shrink:0; }
.item-bar-bg    { flex:1;height:6px;border-radius:999px;background:#f0f0f0;overflow:hidden;max-width:100px; }
.item-bar-fill  { height:100%;border-radius:999px;background:linear-gradient(90deg,#34d399,#198754); }
</style>

<div class="page-title mb-4">
    <h2>Reports</h2>
    <p>{{ now()->format('F Y') }} performance — revenue, orders, and top items.</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
    $statRows = [
        ['Monthly Revenue',  '$'.number_format($monthRevenue,2),  'bi-graph-up-arrow',  '#edfaf3','#198754', 'This month'],
        ['Today\'s Revenue', '$'.number_format($todayRevenue,2),  'bi-currency-dollar', '#e0f2fe','#0369a1', today()->format('d M Y')],
        ['Orders Served',    $totalOrders,                         'bi-bag-check-fill',  '#fef3c7','#92400e', 'This month'],
        ['Avg Order Value',  '$'.number_format($avgOrderValue,2), 'bi-receipt',         '#eef0ff','#3730a3', 'Per order'],
        ['Bookings',         $totalBookings,                       'bi-calendar-check',  '#fde7ea','#9f1239', 'This month'],
    ];
    @endphp
    @foreach($statRows as [$lbl,$val,$ico,$bg,$clr,$sub])
    <div class="col-6 col-lg">
        <div class="card-soft report-stat">
            <div class="ico" style="background:{{ $bg }};color:{{ $clr }};"><i class="bi {{ $ico }}"></i></div>
            <div class="val">{{ $val }}</div>
            <div class="lbl">{{ $lbl }}</div>
            <div class="sub" style="color:{{ $clr }};">{{ $sub }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">

    {{-- Revenue chart --}}
    <div class="col-lg-8">
        <div class="card-soft h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2 text-success"></i>Daily Revenue — Last 14 Days</h5>
                <span class="text-muted" style="font-size:.8rem;">Served orders only</span>
            </div>
            <div class="card-body">
                @php $maxRev = max((float)$chartRevenue->max(), 1); @endphp
                <div class="bar-chart">
                    @foreach($chartLabels as $i => $label)
                    @php $h = round(($chartRevenue[$i] / $maxRev) * 140); @endphp
                    <div class="bar-col">
                        <div class="bar-fill" style="height:{{ max($h,4) }}px;"
                             title="{{ $label }}: ${{ number_format($chartRevenue[$i],2) }}"></div>
                        <div class="bar-label">{{ $i % 2 === 0 ? $label : '' }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between mt-2" style="font-size:.74rem;color:var(--muted);">
                    <span>$0</span>
                    <span>${{ number_format($maxRev/2,0) }}</span>
                    <span>${{ number_format($maxRev,0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order type breakdown --}}
    <div class="col-lg-4">
        <div class="card-soft h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-success"></i>Order Types</h5>
            </div>
            <div class="card-body">
                @php
                    $typeTotal  = $orderTypes->sum('cnt') ?: 1;
                    $typeColors = ['dine_in'=>'#10b981','takeaway'=>'#f59e0b','delivery'=>'#6366f1'];
                    $typeLabels = ['dine_in'=>'Dine In','takeaway'=>'Takeaway','delivery'=>'Delivery'];
                @endphp
                @forelse($orderTypes as $t)
                @php $pct = round(($t->cnt / $typeTotal) * 100); $clr = $typeColors[$t->order_type] ?? '#adb5bd'; @endphp
                <div class="type-row">
                    <div class="type-dot" style="background:{{ $clr }};"></div>
                    <div style="min-width:72px;font-size:.83rem;font-weight:600;">{{ $typeLabels[$t->order_type] ?? ucfirst($t->order_type) }}</div>
                    <div class="type-bar-bg"><div class="type-bar-fill" style="width:{{ $pct }}%;background:{{ $clr }};"></div></div>
                    <div class="type-pct">{{ $pct }}%</div>
                </div>
                <div style="font-size:.75rem;color:var(--muted);margin:-6px 0 12px 20px;">
                    {{ $t->cnt }} orders · ${{ number_format($t->revenue,2) }}
                </div>
                @empty
                <div class="text-muted text-center py-4" style="font-size:.85rem;">No order data yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Category performance --}}
    <div class="col-lg-7">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bi bi-grid me-2 text-success"></i>Category Performance</h5>
                <span class="text-muted" style="font-size:.8rem;">{{ now()->format('M Y') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Items on Menu</th>
                                <th>Revenue</th>
                                <th class="text-center">Qty Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryStats as $cat)
                            <tr>
                                <td class="fw-semibold">{{ $cat->name }}</td>
                                <td class="text-center text-muted">{{ $cat->items }}</td>
                                <td><span class="fw-bold">${{ number_format($cat->revenue,2) }}</span></td>
                                <td class="text-center fw-semibold">{{ $cat->orders }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">No data for this month.</td></tr>
                            @endforelse
                        </tbody>
                        @if($categoryStats->isNotEmpty())
                        <tfoot>
                            <tr style="background:#f8fbf9;">
                                <td class="fw-bold">Total</td>
                                <td class="text-center fw-bold">{{ $categoryStats->sum('items') }}</td>
                                <td class="fw-bold text-success">${{ number_format($categoryStats->sum('revenue'),2) }}</td>
                                <td class="text-center fw-bold">{{ $categoryStats->sum('orders') }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top selling items --}}
    <div class="col-lg-5">
        <div class="card-soft h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Top Selling Items</h5>
            </div>
            <div class="card-body">
                @php $maxQty = max((int)$topItems->max('qty'), 1); @endphp
                @forelse($topItems as $i => $item)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rank-no">{{ $i+1 }}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="fw-semibold" style="font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->name }}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="item-bar-bg"><div class="item-bar-fill" style="width:{{ round(($item->qty/$maxQty)*100) }}%;"></div></div>
                            <span style="font-size:.75rem;color:var(--muted);white-space:nowrap;">{{ $item->qty }} sold</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success" style="font-size:.85rem;">${{ number_format($item->revenue,2) }}</div>
                    </div>
                </div>
                @empty
                <div class="text-muted text-center py-5">
                    <i class="bi bi-trophy" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3;"></i>No sales data yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
