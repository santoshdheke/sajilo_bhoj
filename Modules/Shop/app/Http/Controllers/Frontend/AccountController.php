<?php

namespace Modules\Shop\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Shop\Models\Order;

/** Signed-in storefront customer: dashboard, profile and orders (multiple_pasal's ProfileController). */
class AccountController extends Controller
{
    private const VIEW = 'shop::frontend.simple_theme.user.';

    public function dashboard(): View
    {
        return view(self::VIEW.'dashboard');
    }

    public function profile(): View
    {
        return view(self::VIEW.'profile');
    }

    public function orders(Request $request): View
    {
        $orders = Order::with('orderProducts.product.productImage')
            ->where('customer_id', $request->user('shop_customer')->id)
            ->when($request->query('order-status'), fn ($q, $status) => $q->where('order_status', $status))
            ->latest()->get();

        return view(self::VIEW.'order', compact('orders'));
    }
}
