<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Shop\Models\Customer;
use Modules\Shop\Models\Expense;
use Modules\Shop\Models\Order;
use Modules\Shop\Models\OrderProduct;
use Modules\Shop\Models\Product;
use Modules\Shop\Models\PurchaseOrder;

/** multiple_pasal's admin DashboardController::index. All queries are scoped to the current shop. */
class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $month = Carbon::now();
        $net   = DB::raw('total_amount - discount');

        $sales = fn () => Order::where('order_status', '!=', 'cancelled');

        $todaySales    = $sales()->whereDate('created_at', $today)->sum($net);
        $monthlySales  = $sales()->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->sum($net);
        $todayOrders   = Order::whereDate('created_at', $today)->count();
        $monthlyOrders = Order::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();

        // Profit this month: revenue - cost of goods sold - expenses.
        $orderIdsThisMonth = $sales()->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->pluck('id');
        $monthlyCogs = OrderProduct::with('product')->whereIn('order_id', $orderIdsThisMonth)->get()
            ->sum(fn ($op) => (float) ($op->product->cost_price ?? 0) * $op->quantity);
        $monthlyExpenses = Expense::whereMonth('expense_date', $month->month)->whereYear('expense_date', $month->year)->sum('amount');
        $monthlyProfit   = $monthlySales - $monthlyCogs - $monthlyExpenses;

        $products      = Product::with('purchaseOrderItems', 'stockOutLines')->get();
        $totalProducts = $products->count();
        $lowStockItems = $products->filter(fn ($p) => $p->is_low_stock);
        $expiringSoon  = $products->filter(fn ($p) => $p->is_expiring_soon);

        $topProducts = OrderProduct::selectRaw('product_id, SUM(quantity) as total_sold')
            ->whereIn('order_id', $orderIdsThisMonth)
            ->with('product')->groupBy('product_id')->orderByDesc('total_sold')->limit(5)->get();

        $recentOrders = Order::withCount('orderProducts')->latest()->limit(8)->get();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $reportSales = [
            'daily'   => $todaySales,
            'weekly'  => $sales()->whereBetween('created_at', [$weekStart, $weekEnd])->sum($net),
            'monthly' => $monthlySales,
        ];
        $reportPurchase = [
            'daily'   => PurchaseOrder::whereDate('order_date', $today)->sum('total'),
            'weekly'  => PurchaseOrder::whereBetween('order_date', [$weekStart->toDateString(), $weekEnd->toDateString()])->sum('total'),
            'monthly' => PurchaseOrder::whereMonth('order_date', $month->month)->whereYear('order_date', $month->year)->sum('total'),
        ];
        $reportExpenses = [
            'daily'   => Expense::whereDate('expense_date', $today)->sum('amount'),
            'weekly'  => Expense::whereBetween('expense_date', [$weekStart->toDateString(), $weekEnd->toDateString()])->sum('amount'),
            'monthly' => $monthlyExpenses,
        ];

        // Last 7 days.
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $chart[] = [
                'label'    => $day->format('D'),
                'sales'    => (float) $sales()->whereDate('created_at', $day)->sum($net),
                'purchase' => (float) PurchaseOrder::whereDate('order_date', $day)->sum('total'),
                'expense'  => (float) Expense::whereDate('expense_date', $day)->sum('amount'),
            ];
        }

        $totalCustomers = Customer::count();
        $udharCustomers = Customer::where('balance', '<', 0)->count();

        return view('shop::admin.theme_one.dashboard.index', compact(
            'todaySales', 'monthlySales', 'todayOrders', 'monthlyOrders', 'monthlyProfit', 'monthlyExpenses',
            'totalProducts', 'lowStockItems', 'expiringSoon', 'topProducts', 'recentOrders', 'chart',
            'totalCustomers', 'udharCustomers', 'reportSales', 'reportPurchase', 'reportExpenses'
        ));
    }
}
