<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    public const STATUSES = ['pending', 'ordered', 'received', 'cancelled'];

    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items.inventoryItem'])->latest()->get();
        $suppliers = Supplier::orderBy('name')->get();
        $inventoryItems = InventoryItem::with('category')->whereHas('category')->orderBy('name')->get();

        // Built here (rather than inline in the view) because Blade's @json() directive
        // truncates expressions with several array keys inside a closure.
        $inventoryItemsForJs = $inventoryItems->map(fn ($i) => [
            'id' => $i->id, 'name' => $i->name, 'unit' => $i->unit, 'cost_price' => $i->cost_price,
        ])->values();

        return view('admin.theme_one.purchase_order.index', compact('purchaseOrders', 'suppliers', 'inventoryItems', 'inventoryItemsForJs'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $purchaseOrder = DB::transaction(function () use ($data) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'],
                'invoice_no'  => $data['invoice_no'],
                'status'      => $data['status'],
            ]);

            foreach ($data['items'] as $item) {
                $purchaseOrder->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'subtotal'          => round($item['quantity'] * $item['unit_price'], 2),
                ]);
            }

            $purchaseOrder->recalcTotal();

            return $purchaseOrder;
        });

        $purchaseOrder->load(['supplier', 'items.inventoryItem']);

        return response()->json($purchaseOrder);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $wasReceived = $purchaseOrder->status === 'received';

        DB::transaction(function () use ($purchaseOrder, $data, $wasReceived) {
            $purchaseOrder->update(['status' => $data['status']]);

            if (! $wasReceived && $data['status'] === 'received') {
                foreach ($purchaseOrder->items as $item) {
                    $item->inventoryItem->increment('quantity', $item->quantity);
                }
            }
        });

        $purchaseOrder->load(['supplier', 'items.inventoryItem']);

        return response()->json($purchaseOrder);
    }

    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        if ($purchaseOrder->status === 'received') {
            return response()->json(['message' => 'Received purchase orders cannot be deleted.'], 422);
        }

        $purchaseOrder->delete();

        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        $branch = Tenancy::branch();

        return $request->validate([
            'supplier_id'                  => ['required', 'exists:suppliers,id'],
            'invoice_no'                   => ['required', 'string', 'max:100', Rule::unique('purchase_orders', 'invoice_no')->where('branch_id', $branch?->id)],
            'status'                       => ['required', Rule::in(self::STATUSES)],
            'items'                        => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id'    => ['required', 'distinct', 'exists:inventory_items,id'],
            'items.*.quantity'             => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'           => ['required', 'numeric', 'min:0'],
        ]);
    }
}
