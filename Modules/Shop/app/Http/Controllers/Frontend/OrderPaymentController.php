<?php

namespace Modules\Shop\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Shop\Models\ShopSetting;
use Modules\Shop\Support\Khalti;

/**
 * Khalti online payment of a storefront order (multiple_pasal's OrderPaymentController),
 * with the shop's own Khalti merchant key. The order is marked paid only after Khalti's
 * lookup confirms it is Completed for the full amount.
 */
class OrderPaymentController extends Controller
{
    public function payKhalti(string $orderNumber): RedirectResponse
    {
        $order  = HomeController::visibleOrder($orderNumber);
        $khalti = new Khalti(ShopSetting::current());

        if (! $khalti->enabled() || $order->payment_status === 'paid' || $order->order_status === 'cancelled') {
            return redirect()->route('shop.front.order.show', $order->order_number)
                ->with('error', $order->payment_status === 'paid' ? 'This order is already paid.' : 'Khalti payment is not available for this order.');
        }

        try {
            $response = $khalti->initiate([
                'return_url'          => route('shop.front.order.pay_khalti.callback', $order->order_number),
                'website_url'         => url('/'),
                'amount'              => (int) round($order->grand_total * 100),
                'purchase_order_id'   => $order->order_number,
                'purchase_order_name' => $order->order_number_format,
                'customer_info'       => array_filter(['name' => $order->full_name, 'email' => $order->email, 'phone' => $order->phone_number]),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Khalti initiate failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            $response = null;
        }

        if (! $response?->ok() || ! $response->json('payment_url')) {
            return redirect()->route('shop.front.order.show', $order->order_number)
                ->with('error', 'Could not start the Khalti payment. Please try again, or contact the shop.');
        }

        $order->update(['billing_details' => array_merge((array) $order->billing_details, ['pidx' => $response->json('pidx')])]);

        return redirect()->away($response->json('payment_url'));
    }

    public function khaltiCallback(Request $request, string $orderNumber): RedirectResponse
    {
        $order  = HomeController::visibleOrder($orderNumber);
        $khalti = new Khalti(ShopSetting::current());
        $pidx   = (string) $request->query('pidx');
        $back   = redirect()->route('shop.front.order.show', $order->order_number);

        // Only the payment we started for this order counts.
        if (! $khalti->enabled() || $pidx === '' || $pidx !== ($order->billing_details['pidx'] ?? null)) {
            return $back->with('error', 'Invalid Khalti payment response.');
        }
        if ($order->payment_status === 'paid') {
            return $back->with('success', 'Payment received via Khalti.');
        }

        try {
            $lookup = $khalti->lookup($pidx);
        } catch (\Throwable $e) {
            Log::warning('Khalti lookup failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            $lookup = null;
        }

        $expected = (int) round($order->grand_total * 100);
        if (! $lookup?->ok() || $lookup->json('status') !== 'Completed' || (int) $lookup->json('total_amount') !== $expected) {
            return $back->with('error', 'The Khalti payment was not completed. You can try again below.');
        }

        $order->update([
            'payment_status'  => 'paid',
            'paid_at'         => now(),
            'paid_amount'     => $order->grand_total,
            'billing_details' => array_merge((array) $order->billing_details, ['transaction_id' => $lookup->json('transaction_id')]),
        ]);

        return $back->with('success', 'Payment received via Khalti. Thank you!');
    }
}
