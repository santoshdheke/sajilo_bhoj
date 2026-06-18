<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $month = $now->month;
        $year  = $now->year;

        // ── Top stats ───────────────────────────────────────────
        $monthRevenue  = Order::where('status', 'served')
                              ->whereYear('created_at', $year)
                              ->whereMonth('created_at', $month)
                              ->sum('grand_total');

        $todayRevenue  = Order::where('status', 'served')
                              ->whereDate('created_at', today())
                              ->sum('grand_total');

        $totalOrders   = Order::where('status', 'served')
                              ->whereYear('created_at', $year)
                              ->whereMonth('created_at', $month)
                              ->count();

        $avgOrderValue = $totalOrders > 0 ? round($monthRevenue / $totalOrders, 2) : 0;

        $totalBookings = TableBooking::whereYear('created_at', $year)
                                     ->whereMonth('created_at', $month)
                                     ->count();

        // ── Daily revenue for last 14 days ──────────────────────
        $dailyRevenue = Order::where('status', 'served')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartDays    = collect(range(13, 0))->map(fn($d) => now()->subDays($d)->toDateString());
        $chartRevenue = $chartDays->map(fn($d) => (float)($dailyRevenue[$d]->total ?? 0));
        $chartLabels  = $chartDays->map(fn($d) => Carbon::parse($d)->format('d M'));

        // ── Category performance ─────────────────────────────────
        $categoryStats = MenuCategory::withCount('menuItems')
            ->with('menuItems')
            ->get()
            ->map(function ($cat) use ($year, $month) {
                $revenue = (float) OrderItem::whereIn('name', $cat->menuItems->pluck('name'))
                    ->whereHas('order', fn($q) => $q->where('status', 'served')
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month))
                    ->sum(DB::raw('quantity * price'));

                $orders = OrderItem::whereIn('name', $cat->menuItems->pluck('name'))
                    ->whereHas('order', fn($q) => $q->where('status', 'served')
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month))
                    ->sum('quantity');

                return (object)[
                    'name'    => $cat->name,
                    'items'   => $cat->menu_items_count,
                    'revenue' => $revenue,
                    'orders'  => $orders,
                ];
            })->sortByDesc('revenue')->values();

        // ── Top menu items ───────────────────────────────────────
        $topItems = OrderItem::select('name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(quantity*price) as revenue'))
            ->whereHas('order', fn($q) => $q->where('status', 'served')
                ->whereYear('created_at', $year)->whereMonth('created_at', $month))
            ->groupBy('name')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        // ── Order type breakdown ─────────────────────────────────
        $orderTypes = Order::where('status', 'served')
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->selectRaw('order_type, COUNT(*) as cnt, SUM(grand_total) as revenue')
            ->groupBy('order_type')
            ->get();

        return view('admin.theme_one.report.index', compact(
            'monthRevenue', 'todayRevenue', 'totalOrders', 'avgOrderValue',
            'totalBookings', 'chartLabels', 'chartRevenue',
            'categoryStats', 'topItems', 'orderTypes'
        ));
    }
}
