<?php

namespace Modules\Restaurant\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Restaurant\Models\FloorTable;
use Modules\Restaurant\Models\MenuCategory;
use Modules\Restaurant\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request, $tableId = null)
    {
        $table = $tableId ? FloorTable::find($tableId) : null;

        $categories = MenuCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $items = MenuItem::with('category')
            ->whereHas('category')
            ->where('is_available', true)
            ->orderBy('menu_category_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('restaurant::frontend.theme_one.menu.index', compact('table', 'categories', 'items'));
    }
}
