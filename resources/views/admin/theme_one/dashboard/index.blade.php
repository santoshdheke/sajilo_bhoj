@extends('admin.theme_one.component.layout')

@section('content')
<div class="page-title mb-4">
                <h2>Dashboard</h2>
                <p>Overview of sales, orders, tables, and kitchen performance.</p>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card-soft stat-card">
                        <div class="icon"><i class="bi bi-currency-dollar"></i></div>
                        <div class="value">{{ number_format($todaySales, 2) }}</div>
                        <div class="muted">Today's Sales</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card-soft stat-card">
                        <div class="icon"><i class="bi bi-receipt-cutoff"></i></div>
                        <div class="value">{{ $ordersToday }}</div>
                        <div class="muted">Orders Today</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card-soft stat-card">
                        <div class="icon"><i class="bi bi-grid-3x3-gap"></i></div>
                        <div class="value">{{ $tablesOccupied }}/{{ $tablesTotal }}</div>
                        <div class="muted">Occupied Tables</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card-soft stat-card">
                        <div class="icon"><i class="bi bi-fire"></i></div>
                        <div class="value">{{ $kitchenPending }}</div>
                        <div class="muted">Kitchen Pending</div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card-soft table-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Sales Overview</h5><small class="text-secondary">Last 7 days
                                    revenue snapshot</small>
                            </div><a href="{{ route('admin.report.index') }}" class="btn btn-primary">View Reports</a>
                        </div>
                        <div class="card-body">
                            <div class="mini-chart mb-4">
                                <div class="line" style="top:42px"></div>
                                <div class="line" style="top:92px"></div>
                                <div class="bars">
                                    @foreach($chartBars as $bar)
                                    <div class="bar" style="height:{{ $bar['pct'] }}%" title="{{ $bar['label'] }}"></div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Order No</th>
                                            <th>Table</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                        @php
                                            $statusMap = [
                                                'pending'   => ['status-pending',    'Pending'],
                                                'preparing' => ['status-progress',   'Preparing'],
                                                'ready'     => ['status-ready',      'Ready'],
                                                'served'    => ['status-ready',      'Served'],
                                                'cancelled' => ['status-cancelled',  'Cancelled'],
                                            ];
                                            [$cls, $lbl] = $statusMap[$order->status] ?? ['status-pending', ucfirst($order->status)];
                                        @endphp
                                        <tr>
                                            <td>{{ $order->order_no }}</td>
                                            <td>{{ $order->table->table_no ?? ucfirst(str_replace('_',' ',$order->order_type)) }}</td>
                                            <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                                            <td><span class="status-badge {{ $cls }}">{{ $lbl }}</span></td>
                                            <td>{{ number_format($order->grand_total, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center text-secondary py-4">No orders yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card-soft mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">Top Selling Items</h5>
                            <div class="widget-list">
                                @forelse($topItems as $item)
                                <div class="widget-item"><span>{{ $item->name }}</span><strong>{{ $item->qty }} sold</strong></div>
                                @empty
                                <div class="text-secondary">No sales data yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="card-soft">
                        <div class="card-body">
                            <h5 class="mb-3">Today's Goal</h5>
                            <div class="progress mb-3" style="height:12px">
                                <div class="progress-bar bg-success" style="width:{{ $goalPct }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between"><span class="text-secondary">Revenue
                                    Target</span><strong>{{ $goalPct }}%</strong></div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2"><span class="text-secondary">New
                                    Customers</span><strong>{{ $newCustomers }}</strong></div>
                            <div class="d-flex justify-content-between"><span class="text-secondary">Average
                                    Bill</span><strong>{{ number_format($avgBill, 2) }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
