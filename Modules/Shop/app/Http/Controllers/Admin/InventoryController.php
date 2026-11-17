<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Shop\Models\Product;
use Modules\Shop\Models\PurchaseOrder;

/** Stock overview: purchased (in), sold (out) and available per product. */
class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::with('purchaseOrderItems', 'stockOutLines', 'category')->orderBy('name')->get();

        return view('shop::admin.theme_one.inventory.index', [
            'products'        => $products,
            'totalProducts'   => $products->count(),
            'totalStockIn'    => $products->sum('stock_in'),
            'totalStockOut'   => $products->sum('stock_out'),
            'totalAvailable'  => $products->sum('current_stock'),
            'lowStockItems'   => $products->filter(fn ($p) => $p->is_low_stock),
            'recentPurchases' => PurchaseOrder::with('supplier')->withCount('items')->latest()->take(5)->get(),
        ]);
    }
}
