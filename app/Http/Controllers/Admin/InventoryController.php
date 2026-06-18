<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index()
    {
        $categories = InventoryCategory::withCount('items')->orderBy('name')->get();
        $items = InventoryItem::with('category')
            ->whereHas('category')
            ->orderBy('name')
            ->get();

        return view('admin.theme_one.inventory.index', compact('categories', 'items'));
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $branch = Tenancy::branch();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('inventory_categories', 'name')->where('branch_id', $branch?->id)],
        ]);

        $category = InventoryCategory::create($data);
        $category->loadCount('items');

        return response()->json($category);
    }

    public function updateCategory(Request $request, InventoryCategory $inventoryCategory): JsonResponse
    {
        $branch = Tenancy::branch();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('inventory_categories', 'name')->where('branch_id', $branch?->id)->ignore($inventoryCategory->id)],
        ]);

        $inventoryCategory->update($data);

        return response()->json($inventoryCategory);
    }

    public function destroyCategory(InventoryCategory $inventoryCategory): JsonResponse
    {
        $inventoryCategory->delete();

        return response()->json(['success' => true]);
    }

    public function storeItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:inventory_categories,id',
            'name'          => 'required|string|max:150',
            'unit'          => 'required|string|max:30',
            'quantity'      => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'cost_price'    => 'required|numeric|min:0',
        ]);

        $item = InventoryItem::create($data);
        $item->load('category');

        return response()->json(array_merge($item->toArray(), ['is_low_stock' => $item->is_low_stock]));
    }

    public function updateItem(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:inventory_categories,id',
            'name'          => 'required|string|max:150',
            'unit'          => 'required|string|max:30',
            'quantity'      => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'cost_price'    => 'required|numeric|min:0',
        ]);

        $inventoryItem->update($data);
        $inventoryItem->load('category');

        return response()->json(array_merge($inventoryItem->toArray(), ['is_low_stock' => $inventoryItem->is_low_stock]));
    }

    public function destroyItem(InventoryItem $inventoryItem): JsonResponse
    {
        $inventoryItem->delete();

        return response()->json(['success' => true]);
    }
}
