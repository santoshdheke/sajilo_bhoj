<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FloorTable;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $now   = Carbon::now();

        $todaySales     = Order::where('status', 'served')->whereDate('created_at', $today)->sum('grand_total');
        $ordersToday    = Order::whereDate('created_at', $today)->count();
        $tablesTotal    = FloorTable::count();
        $tablesOccupied = FloorTable::where('status', 'occupied')->count();
        $kitchenPending = Order::whereNotIn('status', ['served', 'cancelled'])->count();

        $recentOrders = Order::with('table')->latest()->limit(5)->get();

        $topItems = OrderItem::select('name', DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn ($q) => $q->where('status', 'served'))
            ->groupBy('name')
            ->orderByDesc('qty')
            ->limit(4)
            ->get();

        $monthRevenue = Order::where('status', 'served')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('grand_total');

        $avgBill = round(Order::where('status', 'served')->avg('grand_total') ?? 0, 2);

        $newCustomers = Customer::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $avgDailyRevenue = $now->day > 0 ? $monthRevenue / $now->day : 0;
        $goalPct = $avgDailyRevenue > 0 ? min(100, (int) round(($todaySales / $avgDailyRevenue) * 100)) : 0;

        // Last 7 days revenue, scaled to bar heights for the mini chart
        $weekDays = collect(range(6, 0))->map(fn ($d) => $now->copy()->subDays($d)->toDateString());

        $dailyRevenue = Order::where('status', 'served')
            ->where('created_at', '>=', $now->copy()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $maxRevenue = max(1.0, (float) ($dailyRevenue->max('total') ?? 0));

        $chartBars = $weekDays->map(fn ($d) => [
            'label' => Carbon::parse($d)->format('D'),
            'total' => (float) ($dailyRevenue[$d]->total ?? 0),
            'pct'   => max(6, (int) round(((float) ($dailyRevenue[$d]->total ?? 0) / $maxRevenue) * 100)),
        ]);

        return view('admin.theme_one.dashboard.index', compact(
            'todaySales', 'ordersToday', 'tablesTotal', 'tablesOccupied', 'kitchenPending',
            'recentOrders', 'topItems', 'monthRevenue', 'avgBill', 'newCustomers',
            'goalPct', 'chartBars'
        ));
    }
}
