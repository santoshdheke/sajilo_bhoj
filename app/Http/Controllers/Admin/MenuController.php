<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = MenuCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $query      = MenuItem::with('category')->whereHas('category')->orderBy('sort_order')->orderBy('name');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('menu_category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->get();
        return view('admin.theme_one.menu.index', compact('categories', 'items'));
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        return response()->json($menuItem->load('category'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'is_available'     => 'boolean',
            'image'            => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $item = MenuItem::create($data + ['sort_order' => MenuItem::max('sort_order') + 1]);
        return response()->json($item->load('category'));
    }

    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $data = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:0',
            'is_available'     => 'boolean',
            'image'            => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('image')) {
            if ($menuItem->image) Storage::disk('public')->delete($menuItem->image);
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

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
        if ($menuItem->image) Storage::disk('public')->delete($menuItem->image);
        $menuItem->delete();
        return response()->json(['success' => true]);
    }
}
