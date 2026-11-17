<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Product;
use Modules\Shop\Models\Section;
use Modules\Shop\Models\SectionItem;
use Modules\Shop\Support\ShopTenancy;

/** Storefront home page product sections and their products (multiple_pasal's SectionController). */
class SectionController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.section.index', [
            'sections' => Section::withCount('sectionItems')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $section = Section::create($this->validated($request));

        return redirect()->route('shop.admin.section.edit', $section)->with('success', 'Section added. Now pick its products.');
    }

    public function edit(Section $section): View
    {
        return view('shop::admin.theme_one.section.edit', [
            'section'    => $section,
            'products'   => Product::with('productImage', 'category')->orderBy('name')->get(),
            'productIds' => $section->productIds(),
        ]);
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $section->update($this->validated($request));

        $ids = $request->validate([
            'product_ids'   => 'nullable|array',
            'product_ids.*' => [Rule::exists('shop.products', 'id')->where('company_id', ShopTenancy::id())],
        ])['product_ids'] ?? [];

        DB::connection('shop')->transaction(function () use ($section, $ids) {
            $section->sectionItems()->where('sectionable_type', Product::class)->whereNotIn('sectionable_id', $ids)->delete();
            foreach ($ids as $id) {
                SectionItem::firstOrCreate(['section_id' => $section->id, 'sectionable_type' => Product::class, 'sectionable_id' => $id]);
            }
        });

        return redirect()->route('shop.admin.section.index')->with('success', 'Section saved.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', 'Section deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'type'       => ['required', Rule::in(array_keys(Section::TYPES))],
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
