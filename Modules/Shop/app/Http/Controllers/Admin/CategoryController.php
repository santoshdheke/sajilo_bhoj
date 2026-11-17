<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Attribute;
use Modules\Shop\Models\Category;
use Modules\Shop\Support\ShopTenancy;

/** Product categories, up to three levels, each with the attributes its products offer. */
class CategoryController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.category.index', [
            'categories'    => Category::with('parent', 'attributes')->withCount('products')->orderBy('parent_id')->orderBy('title')->get(),
            'parentOptions' => Category::parentOptions(),
            'attributes'    => Attribute::orderBy('attribute_title')->pluck('attribute_title', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = Category::create($this->validated($request));
        $category->attributes()->sync($request->input('attribute_ids', []));

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        if ((int) $data['parent_id'] === $category->id) {
            return back()->with('error', 'A category cannot be its own parent.');
        }

        $category->update($data);
        $category->attributes()->sync($request->input('attribute_ids', []));

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->with('error', 'Move its products and sub-categories first, then delete the category.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    /** Sub-categories of a category (product form's second category select). */
    public function children(int $parentId): JsonResponse
    {
        return response()->json(Category::where('parent_id', $parentId)->orderBy('title')->get(['id', 'title']));
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $unique = fn (string $column) => Rule::unique('shop.categories', $column)
            ->where('company_id', ShopTenancy::id())->ignore($category?->id);

        $data = $request->validate([
            'title'           => ['required', 'string', 'max:255', $unique('title')],
            'slug'            => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', $unique('slug')],
            'parent_id'       => ['nullable', 'integer', 'min:0'],
            'attribute_ids'   => ['nullable', 'array'],
            'attribute_ids.*' => [Rule::exists('shop.attributes', 'id')->where('company_id', ShopTenancy::id())],
        ]);

        $data['parent_id'] = (int) ($data['parent_id'] ?? 0);
        if ($data['parent_id'] && ! Category::whereKey($data['parent_id'])->exists()) {
            $data['parent_id'] = 0;
        }
        unset($data['attribute_ids']);

        return $data;
    }
}
