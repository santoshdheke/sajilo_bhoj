<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\KitchenColumn;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KitchenController extends Controller
{
    public function index()
    {
        $columns = KitchenColumn::orderBy('sort_order')->get()->each(function ($col) {
            $col->setRelation('orders', Order::with(['items', 'table'])
                ->where('status', $col->slug)
                ->latest()
                ->get());
        });

        // Share $columns with the _order_card partial via View::share
        view()->share('columns', $columns);

        $tables = FloorTable::orderBy('table_no')->get(['id', 'table_no']);

        return view('admin.theme_one.kitchen.index', compact('columns', 'tables'));
    }

    // ── Orders ──────────────────────────────────────────────────

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $slugs = KitchenColumn::pluck('slug')->toArray();
        $request->validate(['status' => 'required|string|in:' . implode(',', array_merge($slugs, ['served']))]);
        $order->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_id'      => 'required|exists:tables,id',
            'customer_name' => 'nullable|string|max:100',
            'notes'         => 'nullable|string|max:500',
            'items'         => 'required|array|min:1',
            'items.*.name'  => 'required|string|max:100',
            'items.*.qty'   => 'required|integer|min:1',
            'items.*.note'  => 'nullable|string|max:200',
        ]);

        $firstCol = KitchenColumn::orderBy('sort_order')->value('slug') ?? 'pending';

        $order = Order::create([
            'table_id'      => $data['table_id'],
            'customer_name' => $data['customer_name'] ?? null,
            'order_type'    => 'dine_in',
            'notes'         => $data['notes'] ?? null,
            'status'        => $firstCol,
        ]);

        foreach ($data['items'] as $item) {
            $order->items()->create([
                'name'     => $item['name'],
                'quantity' => $item['qty'],
                'price'    => 0,
                'note'     => $item['note'] ?? null,
            ]);
        }

        $order->recalcTotals();

        return response()->json($order->load(['items', 'table']));
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json(['success' => true]);
    }

    // ── Columns ─────────────────────────────────────────────────

    public function storeColumn(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:60',
            'color' => 'required|in:green,warning,indigo,blue,pink,orange,red',
        ]);

        $slug = Str::slug($data['name'], '-');
        $slug = KitchenColumn::where('slug', $slug)->exists()
            ? $slug . '-' . time()
            : $slug;

        $col = KitchenColumn::create([
            'name'       => $data['name'],
            'slug'       => $slug,
            'color'      => $data['color'],
            'sort_order' => KitchenColumn::max('sort_order') + 1,
        ]);

        return response()->json($col);
    }

    public function updateColumn(Request $request, KitchenColumn $column): JsonResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:60',
            'color' => 'required|in:green,warning,indigo,blue,pink,orange,red',
        ]);

        $column->update($data);
        return response()->json($column);
    }

    public function destroyColumn(KitchenColumn $column): JsonResponse
    {
        // Move orders in this column to the first remaining column
        $first = KitchenColumn::where('id', '!=', $column->id)->orderBy('sort_order')->first();
        if ($first) {
            Order::where('status', $column->slug)->update(['status' => $first->slug]);
        }
        $column->delete();
        return response()->json(['success' => true]);
    }

    public function reorderColumns(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);
        foreach ($request->order as $sortOrder => $id) {
            KitchenColumn::where('id', $id)->update(['sort_order' => $sortOrder + 1]);
        }
        return response()->json(['success' => true]);
    }
}
