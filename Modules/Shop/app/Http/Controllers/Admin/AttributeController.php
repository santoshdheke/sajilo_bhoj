<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Attribute;
use Modules\Shop\Support\ShopTenancy;

class AttributeController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.attribute.index', [
            'attributes' => Attribute::withCount('categories')->orderBy('attribute_title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Attribute::create($this->validated($request));

        return back()->with('success', 'Attribute added.');
    }

    public function update(Request $request, Attribute $attribute): RedirectResponse
    {
        $attribute->update($this->validated($request, $attribute));

        return back()->with('success', 'Attribute updated.');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return back()->with('success', 'Attribute deleted.');
    }

    private function validated(Request $request, ?Attribute $attribute = null): array
    {
        return $request->validate([
            'attribute_title' => ['required', 'string', 'max:255',
                Rule::unique('shop.attributes', 'attribute_title')->where('company_id', ShopTenancy::id())->ignore($attribute?->id)],
            'input_type'      => ['required', Rule::in(array_keys(Attribute::INPUT_TYPES))],
        ]);
    }
}
