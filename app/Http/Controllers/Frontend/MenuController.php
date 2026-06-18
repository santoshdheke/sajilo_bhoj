<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FloorTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
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

        return view('frontend.theme_one.menu.index', compact('table', 'categories', 'items'));
    }
}
