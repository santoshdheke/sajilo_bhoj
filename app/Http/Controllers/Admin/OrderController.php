<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Setting;
use App\Support\Money;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'table'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_no', 'like', "%$s%")
                  ->orWhere('customer_name', 'like', "%$s%")
                  ->orWhereHas('table', fn ($t) => $t->where('table_no', 'like', "%$s%"));
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('order_type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        $stats = [
            'today'     => Order::whereDate('created_at', today())->count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'served'    => Order::where('status', 'served')->count(),
            'revenue'   => Order::whereDate('created_at', today())->where('status', 'served')->sum('grand_total'),
        ];

        $tables = FloorTable::orderBy('table_no')->get(['id', 'table_no']);

        // Dishes for the "New Order" picker: name, price and their category's name + photo.
        $menuItems = MenuItem::with('category')->whereHas('category')->where('is_available', true)
            ->get()
            ->sortBy(fn ($i) => [$i->category->sort_order, $i->category->name, $i->sort_order, $i->name])
            ->map(fn ($i) => [
                'id'    => $i->id,
                'name'  => $i->name,
                'price' => (float) $i->price,
                'cat'   => $i->category->name,
                'img'   => $i->category->imageUrl(),
            ])->values();
        $companyId = Tenancy::company()?->id;
        $symbol    = Money::symbol(($companyId ? Setting::getValue($companyId, 'currency') : null) ?: 'NPR');

        return view('admin.theme_one.order.index', compact('orders', 'stats', 'tables', 'menuItems', 'symbol'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_id'             => 'nullable|exists:tables,id',
            'customer_name'        => 'nullable|string|max:100',
            'order_type'           => 'required|in:dine_in,takeaway,delivery',
            'notes'                => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|integer',
            'items.*.qty'          => 'required|integer|min:1|max:99',
            'items.*.note'         => 'nullable|string|max:200',
        ]);

        // Name and price always come from the menu, never from the browser.
        $dishes = MenuItem::whereHas('category')->where('is_available', true)
            ->whereIn('id', collect($data['items'])->pluck('menu_item_id'))->get()->keyBy('id');
        foreach ($data['items'] as $i => $item) {
            if (! $dishes->has($item['menu_item_id'])) {
                return response()->json(['message' => 'One of the dishes is no longer available. Please refresh and try again.',
                    'errors' => ["items.$i.menu_item_id" => ['Dish not available.']]], 422);
            }
        }

        $order = DB::transaction(function () use ($data, $dishes) {
            $order = Order::create([
                'table_id'      => $data['order_type'] === 'dine_in' ? ($data['table_id'] ?? null) : null,
                'customer_name' => $data['customer_name'] ?? null,
                'order_type'    => $data['order_type'],
                'status'        => 'pending',
                'notes'         => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $dish = $dishes[$item['menu_item_id']];
                $order->items()->create([
                    'menu_item_id' => $dish->id,
                    'name'         => $dish->name,
                    'quantity'     => $item['qty'],
                    'price'        => $dish->price,
                    'note'         => $item['note'] ?? null,
                ]);
            }

            $order->recalcTotals();

            return $order;
        });

        return response()->json($order->load(['items', 'table']));
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load(['items', 'table']));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate(['status' => 'required|in:pending,preparing,ready,served,cancelled']);
        $order->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $order->status]);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json(['success' => true]);
    }
}
