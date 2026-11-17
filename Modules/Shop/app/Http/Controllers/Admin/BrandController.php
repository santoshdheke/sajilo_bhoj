<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Brand;
use Modules\Shop\Support\ShopTenancy;

class BrandController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.brand.index', [
            'brands' => Brand::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['logo'] = $request->hasFile('logo') ? $request->file('logo')->store('shop/'.ShopTenancy::id().'/brands', 'public') : null;

        Brand::create($data);

        return back()->with('success', 'Brand added.');
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $this->validated($request, $brand);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('shop/'.ShopTenancy::id().'/brands', 'public');
        }

        $brand->update($data);

        return back()->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->delete(); // its products keep working without a brand (brand_id set to null)

        return back()->with('success', 'Brand deleted.');
    }

    private function validated(Request $request, ?Brand $brand = null): array
    {
        $unique = fn (string $column) => Rule::unique('shop.brands', $column)
            ->where('company_id', ShopTenancy::id())->ignore($brand?->id);

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255', $unique('name')],
            'slug'   => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', $unique('slug')],
            'logo'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'status' => ['nullable', 'boolean'],
        ]);
        $data['status'] = $request->boolean('status', true);
        unset($data['logo']);

        return $data;
    }
}
