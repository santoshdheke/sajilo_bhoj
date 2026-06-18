<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\FloorTable;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $tables = FloorTable::orderBy('table_no')->get(['id', 'table_no']);

        return view('frontend.theme_one.checkout.index', compact('tables'));
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code'     => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $data['code'])->first();

        if (! $coupon || ! $coupon->isValidNow()) {
            return response()->json(['message' => 'This coupon code is invalid or has expired.'], 422);
        }

        return response()->json([
            'code'     => $coupon->code,
            'discount' => $coupon->discountFor((float) $data['subtotal']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type'        => 'required|in:dine_in,takeaway,delivery',
            'table_id'          => 'nullable|exists:tables,id',
            'customer_name'     => 'required|string|max:100',
            'phone'             => 'nullable|string|max:20',
            'notes'             => 'nullable|string|max:500',
            'coupon_code'       => 'nullable|string|max:50',
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required|integer',
            'items.*.name'      => 'required|string|max:150',
            'items.*.price'     => 'required|numeric|min:0',
            'items.*.qty'       => 'required|integer|min:1',
        ]);

        $coupon = null;
        if (! empty($data['coupon_code'])) {
            $candidate = Coupon::where('code', $data['coupon_code'])->first();
            if ($candidate && $candidate->isValidNow()) {
                $coupon = $candidate;
            }
        }

        $order = DB::transaction(function () use ($data, $coupon) {
            $order = Order::create([
                'table_id'      => $data['order_type'] === 'dine_in' ? ($data['table_id'] ?? null) : null,
                'customer_id'   => auth('customer')->id(),
                'customer_name' => $data['customer_name'],
                'order_type'    => $data['order_type'],
                'notes'         => $data['notes'] ?? null,
                'status'        => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'menu_item_id' => $item['id'],
                    'name'         => $item['name'],
                    'quantity'     => $item['qty'],
                    'price'        => $item['price'],
                ]);
            }

            $subtotal = (float) $order->items()->sum(DB::raw('quantity * price'));
            $order->update(['discount' => $coupon ? $coupon->discountFor((float) $subtotal) : 0]);
            $order->recalcTotals();

            // Reward 1 loyalty point per 100 currency units spent.
            if ($customer = auth('customer')->user()) {
                $customer->increment('loyalty_points', (int) floor($order->grand_total / 100));
            }

            return $order;
        });

        return redirect()
            ->route('frontend.checkout.confirmation', $order)
            ->with('success', 'Your order has been placed! Order #' . $order->order_no);
    }

    public function confirmation(Order $order)
    {
        if (auth('customer')->check() && $order->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        $order->load(['items', 'table']);

        return view('frontend.theme_one.checkout.confirmation', compact('order'));
    }
}
