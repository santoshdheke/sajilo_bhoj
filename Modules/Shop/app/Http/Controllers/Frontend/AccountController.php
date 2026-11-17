<?php

namespace Modules\Shop\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Order;
use Modules\Shop\Support\ShopTenancy;
use Modules\Shop\Support\Storefront;

/** Signed-in storefront customer: dashboard, profile and orders (multiple_pasal's ProfileController). */
class AccountController extends Controller
{
    private const VIEW = 'user.';

    public function dashboard(): View
    {
        return view(Storefront::view(self::VIEW.'dashboard'));
    }

    public function profile(): View
    {
        return view(Storefront::view(self::VIEW.'profile'));
    }

    /** Update name, contact details and (optionally) the password. */
    public function update(Request $request): RedirectResponse
    {
        $customer = $request->user('shop_customer');

        $data = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'email'        => ['required', 'email', 'max:255', Rule::unique('shop.customers', 'email')->where('company_id', ShopTenancy::id())->ignore($customer->id)],
            'phone_number' => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:255',
            'password'     => 'nullable|string|min:6|confirmed',
        ]);

        $customer->fill([
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'] ?? null,
            'email'        => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'address_one'  => $data['address'] ?? null,
        ]);
        if (filled($data['password'] ?? null)) {
            $customer->password = $data['password'];
        }
        $customer->save();

        return redirect()->route('shop.front.account.profile')->with('success', 'Profile updated.');
    }

    public function orders(Request $request): View
    {
        $orders = Order::with('orderProducts.product.productImage')
            ->where('customer_id', $request->user('shop_customer')->id)
            ->when($request->query('order-status'), fn ($q, $status) => $q->where('order_status', $status))
            ->latest()->get();

        return view(Storefront::view(self::VIEW.'order'), compact('orders'));
    }
}
