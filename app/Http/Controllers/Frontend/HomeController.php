<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableBooking;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $featuredItems = MenuItem::with('category')
            ->whereHas('category')
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $categories = MenuCategory::where('is_active', true)
            ->withCount(['menuItems' => fn($q) => $q->where('is_available', true)])
            ->orderBy('sort_order')
            ->get();

        $topSelling = OrderItem::select('name', DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'served'))
            ->groupBy('name')
            ->orderByDesc('qty')
            ->first();

        $stats = [
            'menu_items'      => MenuItem::whereHas('category')->where('is_available', true)->count(),
            'tables_total'    => FloorTable::count(),
            'tables_occupied' => FloorTable::where('status', 'occupied')->count(),
            'today_bookings'  => TableBooking::whereDate('booking_date', today())
                                             ->whereIn('status', ['pending','confirmed'])->count(),
            'avg_bill'        => round(Order::where('status','served')->avg('grand_total') ?? 0, 2),
            'pending_orders'  => Order::whereIn('status', ['pending','preparing'])->count(),
        ];

        $monthRevenue = Order::where('status', 'served')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('grand_total');

        return view('frontend.theme_one.home.index', compact(
            'featuredItems', 'categories', 'topSelling', 'stats', 'monthRevenue'
        ));
    }
}
