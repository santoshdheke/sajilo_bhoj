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
            'section'  => $section,
            'items'    => $section->sectionItems()->where('sectionable_type', Product::class)
                ->with(['sectionable' => fn ($q) => $q->with('productImage', 'category')])->get()
                ->filter(fn ($item) => $item->sectionable),
            'products' => Product::with('productImage', 'category')->orderBy('name')->get(),
        ]);
    }

    /** Title, layout and order of the section (its products are added / removed one at a time). */
    public function update(Request $request, Section $section): RedirectResponse
    {
        $section->update($this->validated($request));

        return redirect()->route('shop.admin.section.edit', $section)->with('success', 'Section saved.');
    }

    /** Add a product to the section. No limit, and the same product can be added again. */
    public function addItem(Request $request, Section $section): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', Rule::exists('shop.products', 'id')->where('company_id', ShopTenancy::id())],
            'quantity'   => 'nullable|integer|min:1|max:50',
        ], ['product_id.required' => 'Pick a product to add.']);

        $times = (int) ($data['quantity'] ?? 1);
        DB::connection('shop')->transaction(function () use ($section, $data, $times) {
            for ($i = 0; $i < $times; $i++) {
                SectionItem::create(['section_id' => $section->id, 'sectionable_type' => Product::class, 'sectionable_id' => $data['product_id']]);
            }
        });

        return redirect()->route('shop.admin.section.edit', $section)->with('success', $times > 1 ? "Product added {$times} times." : 'Product added.');
    }

    /** Remove one item (only that copy when the product was added more than once). */
    public function removeItem(Section $section, SectionItem $item): RedirectResponse
    {
        abort_unless((int) $item->section_id === (int) $section->id, 404);
        $item->delete();

        return redirect()->route('shop.admin.section.edit', $section)->with('success', 'Product removed.');
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
