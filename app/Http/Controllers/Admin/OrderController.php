<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
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

        return view('admin.theme_one.order.index', compact('orders', 'stats', 'tables'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_id'      => 'nullable|exists:tables,id',
            'customer_name' => 'nullable|string|max:100',
            'order_type'    => 'required|in:dine_in,takeaway,delivery',
            'notes'         => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.name'  => 'required|string|max:100',
            'items.*.qty'   => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.note'  => 'nullable|string|max:200',
        ]);

        $order = Order::create([
            'table_id'      => $data['order_type'] === 'dine_in' ? ($data['table_id'] ?? null) : null,
            'customer_name' => $data['customer_name'] ?? null,
            'order_type'    => $data['order_type'],
            'status'        => 'pending',
            'notes'         => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            $order->items()->create([
                'name'     => $item['name'],
                'quantity' => $item['qty'],
                'price'    => $item['price'],
                'note'     => $item['note'] ?? null,
            ]);
        }

        $order->recalcTotals();

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
