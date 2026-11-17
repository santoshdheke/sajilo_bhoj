<?php

namespace Modules\Restaurant\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Restaurant\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerAccountController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $orders = $customer->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('restaurant::frontend.theme_one.account.index', compact('customer', 'orders'));
    }

    public function show(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items', 'table']);

        return view('restaurant::frontend.theme_one.account.order', compact('order'));
    }
}
