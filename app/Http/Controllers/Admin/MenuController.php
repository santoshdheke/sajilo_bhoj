<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Left panel tab: ?tab=menu (default), ?tab=category or ?tab=qr
        $tab = in_array($request->query('tab'), ['category', 'qr'], true) ? $request->query('tab') : 'menu';

        // Every category of this branch (hidden ones too, so their dishes stay manageable), with its dishes.
        $categories = MenuCategory::with('menuItems')->orderBy('sort_order')->orderBy('name')->get();
        $companyId  = Tenancy::company()?->id;
        $currency   = ($companyId ? Setting::getValue($companyId, 'currency') : null) ?: 'NPR';

        // Tables for the QR tab, so a code can open the menu for one table.
        $tables = FloorTable::orderBy('table_no')->get(['id', 'table_no']);

        return view('admin.theme_one.menu.index', compact('categories', 'currency', 'tab', 'tables'));
    }

    /**
     * Save the drag-and-drop order: categories in display order, and for each category its dishes
     * (a dish dropped into another category moves there). Ids from other restaurants are ignored.
     */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'categories'   => 'array',
            'categories.*' => 'integer',
            'items'        => 'array',
            'items.*'      => 'array',
            'items.*.*'    => 'integer',
        ]);

        $ownCategories = MenuCategory::pluck('id')->all(); // branch-scoped

        DB::transaction(function () use ($data, $ownCategories) {
            foreach (array_values($data['categories'] ?? []) as $position => $id) {
                if (in_array($id, $ownCategories, true)) {
                    MenuCategory::whereKey($id)->update(['sort_order' => $position]);
                }
            }

            foreach ($data['items'] ?? [] as $categoryId => $ids) {
                if (! in_array((int) $categoryId, $ownCategories, true)) {
                    continue;
                }
                $ownItems = MenuItem::whereIn('id', $ids)->whereIn('menu_category_id', $ownCategories)->pluck('id')->all();
                foreach (array_values($ids) as $position => $id) {
                    if (in_array($id, $ownItems, true)) {
                        MenuItem::whereKey($id)->update(['menu_category_id' => (int) $categoryId, 'sort_order' => $position]);
                    }
                }
            }
        });

        return response()->json(['success' => true]);
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
