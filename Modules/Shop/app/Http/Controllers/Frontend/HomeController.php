<?php

namespace Modules\Shop\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Category;
use Modules\Shop\Models\Customer;
use Modules\Shop\Models\Order;
use Modules\Shop\Models\OrderProduct;
use Modules\Shop\Models\Product;
use Modules\Shop\Models\ProductAttribute;
use Modules\Shop\Models\Section;
use Modules\Shop\Models\ShopSetting;
use Modules\Shop\Support\Storefront;
use Modules\Shop\Support\Cart;
use Modules\Shop\Support\ShopTenancy;

/**
 * Storefront (multiple_pasal's Frontend\HomeController, theme "simple_theme"):
 * home, store / search, category, product, section, cart, checkout and orders.
 */
class HomeController extends Controller
{
    private const VIEW = 'home.';

    public function index(): View
    {
        return view(Storefront::view(self::VIEW.'index'));
    }

    public function store(Request $request): View
    {
        $products = $this->filtered(Product::query(), $request)->paginate(12)->withQueryString();

        return view(Storefront::view(self::VIEW.'store'), compact('products'));
    }

    public function category(Request $request, string $category): View
    {
        $category = Category::where('slug', $category)->firstOrFail();
        $products = $this->sorted(Product::with('productImage', 'category')->whereIn('category_id', $this->categoryIds($category)), $request)
            ->paginate(12)->withQueryString();

        return view(Storefront::view(self::VIEW.'category'), compact('products', 'category'));
    }

    public function section(Section $section): View
    {
        return view(Storefront::view(self::VIEW.'section'), compact('section'));
    }

    public function product(string $category, string $product): View
    {
        $product = Product::with('productImages', 'productImage', 'category.attributes', 'productPricePerOrders')
            ->where('slug', $product)->firstOrFail();

        // The theme reads $attribute->productAttribute->productAttributeValues for this product.
        $values = ProductAttribute::with('productAttributeValues')->where('product_id', $product->id)->get()->keyBy('attribute_id');
        $product->category->attributes->each(fn ($attribute) => $attribute->setRelation('productAttribute', $values[$attribute->id] ?? null));

        return view(Storefront::view(self::VIEW.'product'), compact('product'));
    }

    public function checkout(): View
    {
        return view(Storefront::view(self::VIEW.'checkout'));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product'     => 'required|string',
            'quantity'    => 'nullable|integer|min:1|max:1000',
            'attribute'   => 'nullable|array',
            'button_type' => 'nullable|in:add_to_cart,order_now',
        ]);

        // The form sends the product as JSON; only its id is used (price etc. come from the database).
        $productId = (int) (json_decode($data['product'], true)['id'] ?? 0);
        $product   = Product::with('purchaseOrderItems', 'stockOutLines')->find($productId);
        if (! $product) {
            return back()->with('error', 'This product is no longer available.');
        }

        $quantity = (int) ($data['quantity'] ?? 1);
        if ($product->current_stock < Cart::quantityOf($product->id) + $quantity) {
            return back()->with('error', $product->current_stock > 0
                ? "Only {$product->current_stock} of \"{$product->name}\" in stock."
                : "\"{$product->name}\" is out of stock.");
        }

        // attribute[id][title] = value  →  [{id, title, value}]
        $attributes = [];
        foreach ((array) ($data['attribute'] ?? []) as $id => $pairs) {
            foreach ((array) $pairs as $title => $value) {
                if (filled($value)) {
                    $attributes[] = ['id' => (int) $id, 'title' => (string) $title, 'value' => mb_substr((string) $value, 0, 100)];
                }
            }
        }

        Cart::add($product, $quantity, $attributes);

        return ($data['button_type'] ?? null) === 'order_now'
            ? redirect()->route('shop.front.checkout')
            : back()->with('success', "\"{$product->name}\" added to your cart.");
    }

    public function removeFromCart(string $id): RedirectResponse
    {
        Cart::remove($id);

        return back()->with('success', 'Removed from your cart.');
    }

    public function order(Request $request): RedirectResponse
    {
        $settings = ShopSetting::current();
        $customer = Auth::guard('shop_customer')->user();

        $methods = array_keys(array_filter([
            'cash_on_delivery' => true,
            'khalti'           => filled($settings->khalti_secret_key),
            'khalti_qr'        => filled($settings->khalti_id),
            'esewa_qr'         => filled($settings->esewa_id),
        ]));

        $data = $request->validate([
            'payment_method'     => ['nullable', Rule::in($methods)],
            'payment_screenshot' => ['required_if:payment_method,khalti_qr,esewa_qr', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'full_name'          => [$customer ? 'nullable' : 'required', 'string', 'max:255'],
            'phone_number'       => [$customer ? 'nullable' : 'required', 'string', 'max:20'],
            'email'              => ['nullable', 'email', 'max:255'],
            'address'            => [$customer ? 'nullable' : 'required', 'string', 'max:255'],
            'address_detail'     => ['nullable', 'string', 'max:1000'],
        ], [
            'payment_screenshot.required_if' => 'Upload the screenshot of your QR payment.',
        ]);

        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            return redirect()->route('shop.front.store')->with('error', 'Your cart is empty.');
        }

        // Not more than what is in stock (same product in several lines counts together).
        $products = Product::with('purchaseOrderItems', 'stockOutLines')->whereIn('id', $lines->pluck('product_id'))->get()->keyBy('id');
        foreach ($lines->groupBy('product_id') as $productId => $group) {
            $product = $products[$productId];
            if ($product->current_stock < $group->sum('quantity')) {
                return back()->withInput()->with('error', "Sorry, only {$product->current_stock} of \"{$product->name}\" left in stock. Please update your cart.");
            }
        }

        $paymentMethod = $data['payment_method'] ?? 'cash_on_delivery';
        $screenshot    = $request->file('payment_screenshot')?->store('shop/'.ShopTenancy::id().'/payment-proofs', 'public');

        $order = DB::connection('shop')->transaction(function () use ($data, $customer, $lines, $paymentMethod, $screenshot) {
            // Guests become (or are matched to) a customer of this shop by phone number.
            $customer ??= Customer::where('phone_number', $data['phone_number'])->first()
                ?? Customer::create([
                    'first_name'   => $data['full_name'],
                    'email'        => $data['email'] ?? null,
                    'phone_number' => $data['phone_number'],
                    'address_one'  => $data['address'],
                ]);

            $order = Order::create([
                'customer_id'     => $customer->id,
                'order_number'    => Order::newNumber(),
                'full_name'       => $data['full_name'] ?? $customer->full_name,
                'email'           => $data['email'] ?? $customer->email,
                'phone_number'    => $data['phone_number'] ?? $customer->phone_number,
                'address'         => $data['address'] ?? $customer->address_one,
                'address_detail'  => $data['address_detail'] ?? null,
                'total_amount'    => $lines->sum(fn ($line) => (float) $line['product']->price * $line['quantity']),
                'payment_method'  => $paymentMethod,
                'payment_status'  => 'pending',
                'order_status'    => 'pending',
                'sale_type'       => 'online',
                'billing_details' => $screenshot ? ['screenshot' => $screenshot] : null,
            ]);

            foreach ($lines as $line) {
                OrderProduct::create([
                    'order_id'              => $order->id,
                    'product_id'            => $line['product']->id,
                    'unit_price'            => $line['product']->price ?? 0,
                    'quantity'              => $line['quantity'],
                    'attribute_description' => $line['attribute'],
                ]);
            }

            return $order;
        });

        Cart::clear();
        session()->push('shop_orders', $order->order_number); // lets a guest open their order page

        return $paymentMethod === 'khalti'
            ? redirect()->route('shop.front.order.pay_khalti', $order->order_number)
            : redirect()->route('shop.front.order.show', $order->order_number)->with('success', 'Order placed successfully.');
    }

    public function orderDetail(string $orderNumber): View
    {
        $order = self::visibleOrder($orderNumber);
        $order->load('orderProducts.product.productImage');

        return view(Storefront::view(self::VIEW.'order_detail'), compact('order'));
    }

    /** An order this visitor may see: placed in this session, or belonging to the signed-in customer. */
    public static function visibleOrder(string $orderNumber): Order
    {
        $order    = Order::where('order_number', $orderNumber)->firstOrFail();
        $customer = Auth::guard('shop_customer')->user();

        abort_unless(in_array($orderNumber, session('shop_orders', []), true)
            || ($customer && $order->customer_id === $customer->id), 404);

        return $order;
    }

    /** Store filters: ?category=slug, ?brand=slug, ?search=words, ?sort=price_asc|price_desc|newest. */
    private function filtered(Builder $query, Request $request): Builder
    {
        $query->with('productImage', 'category');

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            $category ? $query->whereIn('category_id', $this->categoryIds($category)) : $query->whereRaw('1 = 0');
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
        }

        if ($request->filled('search')) {
            $words = preg_split('/\s+/', trim((string) $request->search));
            $query->where(function ($q) use ($words, $request) {
                $q->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->where('name', 'like', '%'.$word.'%');
                    }
                })->orWhere('sku', $request->search)->orWhere('barcode', $request->search)
                  ->orWhereHas('productTags', fn ($t) => $t->whereIn('title', $words));
            });
        }

        return $this->sorted($query, $request);
    }

    private function sorted(Builder $query, Request $request): Builder
    {
        return match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };
    }

    /** A root category also shows its sub-categories' products (sub-category is optional on products). */
    private function categoryIds(Category $category): array
    {
        $ids = [$category->id];
        if ((int) $category->parent_id === 0) {
            $ids = array_merge($ids, $category->children()->pluck('id')->all());
        }

        return $ids;
    }
}
