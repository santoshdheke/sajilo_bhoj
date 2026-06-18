<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::withCount('menuItems')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.theme_one.menu_category.index', compact('categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:menu_categories,name',
            'description' => 'nullable|string|max:500',
        ]);
        $cat = MenuCategory::create($data + ['sort_order' => MenuCategory::max('sort_order') + 1]);
        $cat->loadCount('menuItems');
        return response()->json($cat);
    }

    public function update(Request $request, MenuCategory $menuCategory): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:menu_categories,name,' . $menuCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);
        $menuCategory->update($data);
        return response()->json($menuCategory);
    }

    public function destroy(MenuCategory $menuCategory): JsonResponse
    {
        $menuCategory->delete();
        return response()->json(['success' => true]);
    }
}
