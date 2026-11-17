<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Shop\Models\Order;

/** Sales history (POS and online orders), status changes and payment verification. */
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::withCount('orderProducts')
            ->when($request->status, fn ($q, $s) => $q->where('order_status', $s))
            ->when($request->payment_status, fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->sale_type, fn ($q, $s) => $q->where('sale_type', $s))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q->where('order_number', 'like', "%{$s}%")
                ->orWhere('full_name', 'like', "%{$s}%")->orWhere('phone_number', 'like', "%{$s}%")))
            ->latest()->paginate(15)->withQueryString();

        return view('shop::admin.theme_one.order.index', ['orders' => $orders, 'statuses' => Order::STATUSES]);
    }

    public function show(Order $order): View
    {
        $order->load('orderProducts.product', 'customer', 'user');

        return view('shop::admin.theme_one.order.show', ['order' => $order]);
    }

    public function updateStatus(Order $order, string $status): RedirectResponse
    {
        $order->update(['order_status' => $status]);

        return back()->with('success', 'Order marked as '.$status.'.');
    }

    public function verifyPayment(Order $order): RedirectResponse
    {
        $order->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'paid_amount'    => $order->grand_total,
        ]);

        return back()->with('success', 'Payment verified and marked as paid.');
    }
}
