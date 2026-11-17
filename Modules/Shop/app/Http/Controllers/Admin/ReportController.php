<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Shop\Models\Expense;
use Modules\Shop\Models\Order;
use Modules\Shop\Models\Product;

/** Sales, stock and profit reports, and the expense book (multiple_pasal's ReportController). */
class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        [$from, $to] = $this->range($request);

        $orders = Order::whereBetween('created_at', [$from, $to])->where('order_status', '!=', 'cancelled')
            ->withCount('orderProducts')->latest()->get();

        return view('shop::admin.theme_one.report.sales', [
            'orders'           => $orders,
            'totalSales'       => $orders->sum('grand_total'),
            'totalTax'         => $orders->sum('tax_amount'),
            'totalDiscount'    => $orders->sum('discount'),
            'orderCount'       => $orders->count(),
            'dailySales'       => $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))->map(fn ($g) => $g->sum('grand_total'))->sortKeys(),
            'paymentBreakdown' => $orders->groupBy('payment_method_label')->map(fn ($g) => $g->sum('grand_total')),
            'from'             => $from,
            'to'               => $to,
        ]);
    }

    public function stock(): View
    {
        $products = Product::with('purchaseOrderItems', 'stockOutLines', 'category')->orderBy('name')->get()
            ->each(fn ($p) => $p->stock_value = $p->current_stock * (float) ($p->cost_price ?? $p->price));

        return view('shop::admin.theme_one.report.stock', [
            'products'     => $products,
            'lowStock'     => $products->filter(fn ($p) => $p->is_low_stock),
            'outOfStock'   => $products->filter(fn ($p) => $p->stock_in > 0 && $p->current_stock == 0),
            'expiringSoon' => $products->filter(fn ($p) => $p->is_expiring_soon),
            'expired'      => $products->filter(fn ($p) => $p->is_expired),
            'totalValue'   => $products->sum('stock_value'),
        ]);
    }

    public function profit(Request $request): View
    {
        [$from, $to] = $this->range($request);

        $orders = Order::whereBetween('created_at', [$from, $to])->where('order_status', '!=', 'cancelled')
            ->with('orderProducts.product')->get();

        $revenue = $orders->sum('grand_total');
        $cogs    = $orders->sum(fn ($o) => $o->orderProducts->sum(fn ($op) => (float) ($op->product->cost_price ?? 0) * $op->quantity));

        $expenseQuery = fn () => Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
        $expenses     = (float) $expenseQuery()->sum('amount');
        $grossProfit  = $revenue - $cogs;

        return view('shop::admin.theme_one.report.profit', [
            'revenue'           => $revenue,
            'cogs'              => $cogs,
            'expenses'          => $expenses,
            'grossProfit'       => $grossProfit,
            'netProfit'         => $grossProfit - $expenses,
            'expenseByCategory' => $expenseQuery()->selectRaw('category, SUM(amount) as total')->groupBy('category')->orderByDesc('total')->get(),
            'from'              => $from,
            'to'                => $to,
        ]);
    }

    public function expenseIndex(): View
    {
        return view('shop::admin.theme_one.report.expenses', [
            'expenses'   => Expense::latest('expense_date')->latest('id')->paginate(20),
            'monthTotal' => Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'categories' => Expense::CATEGORIES,
        ]);
    }

    public function expenseStore(Request $request): RedirectResponse
    {
        Expense::create($request->validate([
            'category'     => 'required|string|max:100',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string|max:255',
            'reference'    => 'nullable|string|max:100',
        ]));

        return back()->with('success', 'Expense recorded.');
    }

    public function expenseDestroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }

    /** ?from=&to= (default: this month so far). */
    private function range(Request $request): array
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        return [$from, $to];
    }
}
