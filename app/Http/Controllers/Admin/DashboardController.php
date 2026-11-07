<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FloorTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableBooking;
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

        $weekTotal = (float) $chartBars->sum('total');

        // Live orders: today's orders that are still in play (newest first) + per-status counts
        $todayOrders = Order::with(['table', 'items'])
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();
        $liveOrders  = $todayOrders->take(8);
        $orderCounts = [
            'all'       => $todayOrders->count(),
            'pending'   => $todayOrders->where('status', 'pending')->count(),
            'preparing' => $todayOrders->where('status', 'preparing')->count(),
            'ready'     => $todayOrders->where('status', 'ready')->count(),
            'served'    => $todayOrders->where('status', 'served')->count(),
        ];

        // Floor plan
        $floorTables = FloorTable::with('room')->withCount('chairs')->orderBy('room_id')->orderBy('id')->get();

        // Reservations still to come today
        $reservations = TableBooking::with('floorTable.room')
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_time', '>=', $now->copy()->subHour()->format('H:i:s'))
            ->orderBy('booking_time')
            ->limit(5)
            ->get();

        // Top sellers today (with a menu photo when available)
        $topToday = OrderItem::with('menuItem')
            ->select('menu_item_id', 'name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(subtotal) as revenue'))
            ->whereHas('order', fn ($q) => $q->whereDate('created_at', $today)->where('status', '!=', 'cancelled'))
            ->groupBy('menu_item_id', 'name')
            ->orderByDesc('qty')
            ->limit(3)
            ->get();

        $capacityPct = $tablesTotal > 0 ? (int) round($tablesOccupied / $tablesTotal * 100) : 0;

        return view('admin.theme_one.dashboard.index', compact(
            'todaySales', 'ordersToday', 'tablesTotal', 'tablesOccupied', 'kitchenPending',
            'recentOrders', 'topItems', 'monthRevenue', 'avgBill', 'newCustomers',
            'goalPct', 'chartBars', 'weekTotal', 'liveOrders', 'orderCounts',
            'floorTables', 'reservations', 'topToday', 'capacityPct'
        ));
    }

    /**
     * Live numbers for the floating Chat Support widget (shown on every admin page).
     */
    public function chatFacts(): \Illuminate\Http\JsonResponse
    {
        $today = today();
        $now   = Carbon::now();

        $tablesTotal    = FloorTable::count();
        $tablesOccupied = FloorTable::where('status', 'occupied')->count();

        $top = OrderItem::select('name', DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn ($q) => $q->where('status', 'served'))
            ->groupBy('name')->orderByDesc('qty')->limit(4)->get()
            ->map(fn ($i) => $i->name . ' (' . $i->qty . ')')->implode(', ');

        return response()->json([
            'sales'     => number_format(Order::where('status', 'served')->whereDate('created_at', $today)->sum('grand_total'), 2),
            'orders'    => Order::whereDate('created_at', $today)->count(),
            'tables'    => $tablesOccupied . ' of ' . $tablesTotal,
            'kitchen'   => Order::whereNotIn('status', ['served', 'cancelled'])->count(),
            'month'     => number_format(Order::where('status', 'served')->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->sum('grand_total'), 2),
            'avgBill'   => number_format(round(Order::where('status', 'served')->avg('grand_total') ?? 0, 2), 2),
            'customers' => Customer::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
            'top'       => $top,
        ]);
    }
}
