<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'name'        => ['required', 'string', 'max:100', $this->uniqueName()],
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request);
        }

        $cat = MenuCategory::create($data + ['sort_order' => MenuCategory::max('sort_order') + 1]);
        $cat->loadCount('menuItems');

        return response()->json($cat->toArray() + ['image_url' => $cat->imageUrl()]);
    }

    public function update(Request $request, MenuCategory $menuCategory): JsonResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100', $this->uniqueName()->ignore($menuCategory->id)],
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|max:3072',
            'remove_image' => 'boolean',
        ]);

        if ($request->hasFile('image') || $request->boolean('remove_image')) {
            if ($menuCategory->image) {
                Storage::disk('public')->delete($menuCategory->image);
            }
            $data['image'] = $request->hasFile('image') ? $this->storeImage($request) : null;
        }
        unset($data['remove_image']);

        $menuCategory->update($data);

        return response()->json($menuCategory->toArray() + ['image_url' => $menuCategory->imageUrl()]);
    }

    public function destroy(MenuCategory $menuCategory): JsonResponse
    {
        // Its dishes are removed by the database cascade, so clean up their photos too.
        $files = $menuCategory->menuItems()->whereNotNull('image')->pluck('image')
            ->push($menuCategory->image)->filter()->all();

        $menuCategory->delete();
        Storage::disk('public')->delete($files);

        return response()->json(['success' => true]);
    }

    /** Category names only need to be unique inside this restaurant branch. */
    private function uniqueName(): \Illuminate\Validation\Rules\Unique
    {
        return Rule::unique('menu_categories', 'name')->where('branch_id', Tenancy::branch()?->id);
    }

    private function storeImage(Request $request): string
    {
        $path = $request->file('image')->store('menu-categories', 'public');

        if (! $path) {
            throw ValidationException::withMessages([
                'image' => 'The photo could not be saved on the server. Please check the storage folder permissions.',
            ]);
        }

        return $path;
    }
}
