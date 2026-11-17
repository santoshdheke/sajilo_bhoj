<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Customer;
use Modules\Shop\Models\CustomerTransaction;
use Modules\Shop\Models\Order;
use Modules\Shop\Models\OrderProduct;
use Modules\Shop\Models\Product;
use Modules\Shop\Support\Money;
use Modules\Shop\Support\ShopTenancy;

/**
 * Point of sale (multiple_pasal's ProductController::sell / storeSell / invoice).
 * A sale is a delivered "pos" order; whatever isn't paid is added to the customer's credit (udhar).
 */
class PosController extends Controller
{
    public function sell(): View
    {
        $products = Product::with('productImage', 'purchaseOrderItems', 'stockOutLines', 'category')->orderBy('name')->get()
            ->map(fn (Product $p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'sku'      => $p->sku,
                'barcode'  => $p->barcode,
                'price'    => (float) $p->price,
                'stock'    => $p->current_stock,
                'unit'     => $p->unit,
                'category' => $p->category?->title,
                'image'    => $p->image_url,
            ]);

        $customers = Customer::orderBy('first_name')->get()->map(fn (Customer $c) => [
            'id'      => $c->id,
            'name'    => $c->full_name,
            'phone'   => $c->phone_number,
            'address' => $c->address_one,
            'balance' => (float) $c->balance,
        ]);

        return view('shop::admin.theme_one.product.sell', [
            'products'       => $products,
            'customers'      => $customers,
            'paymentMethods' => Order::POS_PAYMENT_METHODS,
        ]);
    }

    public function storeSell(Request $request): RedirectResponse
    {
        $shop = ShopTenancy::id();

        $data = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('shop.products', 'id')->where('company_id', $shop)],
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method'     => ['required', Rule::in(array_keys(Order::POS_PAYMENT_METHODS))],
            'paid_amount'        => 'nullable|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'customer_id'        => ['nullable', Rule::exists('shop.customers', 'id')->where('company_id', $shop)],
            'customer_name'      => 'nullable|string|max:255',
            'customer_phone'     => 'nullable|string|max:20',
            'customer_address'   => 'nullable|string|max:255',
            'transaction_code'   => 'nullable|string|max:100',
        ], ['items.required' => 'Add at least one product to the cart.']);

        $subtotal   = collect($data['items'])->sum(fn ($i) => $i['unit_price'] * $i['quantity']);
        $discount   = min((float) ($data['discount'] ?? 0), $subtotal);
        $grandTotal = max(0, $subtotal - $discount);
        $paidAmount = (float) ($data['paid_amount'] ?? 0);

        // Credit sales need a customer, to know who owes the rest.
        if ($paidAmount < $grandTotal && empty($data['customer_id'])) {
            return back()->withInput()->with('error', 'Select a customer for a credit sale (paid less than the total).');
        }

        // Not more than what is in stock (same product in several lines counts together).
        $products = Product::with('purchaseOrderItems', 'stockOutLines')
            ->whereIn('id', collect($data['items'])->pluck('product_id'))->get()->keyBy('id');
        foreach (collect($data['items'])->groupBy('product_id') as $productId => $lines) {
            $product = $products[$productId];
            $wanted  = $lines->sum('quantity');
            if ($product->current_stock < $wanted) {
                return back()->withInput()->with('error', "Not enough stock for \"{$product->name}\": {$product->current_stock} available, {$wanted} in the cart.");
            }
        }

        $order = DB::connection('shop')->transaction(function () use ($data, $subtotal, $discount, $grandTotal, $paidAmount, $request) {
            $customer      = ! empty($data['customer_id']) ? Customer::lockForUpdate()->find($data['customer_id']) : null;
            $paymentStatus = $paidAmount >= $grandTotal ? 'paid' : 'credit';

            $order = Order::create([
                'customer_id'     => $customer?->id,
                'user_id'         => $request->user('shop')?->id,
                'order_number'    => Order::newNumber(),
                'full_name'       => $data['customer_name'] ?? $customer?->full_name,
                'phone_number'    => $data['customer_phone'] ?? $customer?->phone_number,
                'address'         => $data['customer_address'] ?? $customer?->address_one,
                'total_amount'    => $subtotal,
                'discount'        => $discount,
                'paid_amount'     => $paidAmount,
                'payment_method'  => $data['payment_method'],
                'billing_details' => ! empty($data['transaction_code']) ? ['transaction_code' => $data['transaction_code']] : null,
                'payment_status'  => $paymentStatus,
                'order_status'    => 'delivered',
                'sale_type'       => 'pos',
                'paid_at'         => now(),
            ]);

            foreach ($data['items'] as $item) {
                OrderProduct::create([
                    'order_id'              => $order->id,
                    'product_id'            => $item['product_id'],
                    'unit_price'            => $item['unit_price'],
                    'quantity'              => $item['quantity'],
                    'attribute_description' => [],
                ]);
            }

            // Unpaid part goes on the customer's account (negative balance = owes the shop).
            if ($paymentStatus === 'credit' && $customer) {
                $creditDue = $grandTotal - $paidAmount;
                $customer->balance = (float) $customer->balance - $creditDue;
                $customer->save();

                CustomerTransaction::create([
                    'customer_id'   => $customer->id,
                    'order_id'      => $order->id,
                    'type'          => 'credit',
                    'amount'        => -$creditDue,
                    'balance_after' => $customer->balance,
                    'description'   => 'Credit from POS sale #'.$order->order_number,
                ]);
            }

            return $order;
        });

        $message = $order->payment_status === 'credit'
            ? 'Sale completed. '.Money::format($order->due_amount).' added to the customer\'s credit.'
            : 'Sale completed.';

        return redirect()->route('shop.admin.product.invoice', $order)->with('success', $message);
    }

    public function invoice(Order $order): View
    {
        $order->load('orderProducts.product', 'customer', 'user');

        return view('shop::admin.theme_one.product.invoice', ['order' => $order, 'shop' => ShopTenancy::company()]);
    }
}
