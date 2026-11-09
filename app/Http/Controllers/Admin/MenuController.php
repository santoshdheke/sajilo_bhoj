<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Left panel tab: ?tab=menu (default) or ?tab=category
        $tab = $request->query('tab') === 'category' ? 'category' : 'menu';

        // Every category of this branch (hidden ones too, so their dishes stay manageable), with its dishes.
        $categories = MenuCategory::with('menuItems')->orderBy('sort_order')->orderBy('name')->get();
        $companyId  = Tenancy::company()?->id;
        $currency   = ($companyId ? Setting::getValue($companyId, 'currency') : null) ?: 'NPR';

        return view('admin.theme_one.menu.index', compact('categories', 'currency', 'tab'));
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        return response()->json($menuItem->load('category'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_category_id' => ['required', $this->ownCategory()],
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'is_available'     => 'boolean',
        ]);

        $item = MenuItem::create($data + ['sort_order' => MenuItem::where('menu_category_id', $data['menu_category_id'])->max('sort_order') + 1]);
        return response()->json($item->load('category'));
    }

    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $data = $request->validate([
            'menu_category_id' => ['required', $this->ownCategory()],
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'is_available'     => 'boolean',
        ]);

        $menuItem->update($data);
        return response()->json($menuItem->load('category'));
    }

    public function toggleAvailability(MenuItem $menuItem): JsonResponse
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);
        return response()->json(['is_available' => $menuItem->is_available]);
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $menuItem->delete();
        return response()->json(['success' => true]);
    }

    /** The category must belong to this restaurant branch. */
    private function ownCategory(): \Illuminate\Validation\Rules\Exists
    {
        return \Illuminate\Validation\Rule::exists('menu_categories', 'id')->where('branch_id', Tenancy::branch()?->id);
    }
}
