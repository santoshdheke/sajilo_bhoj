<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Shop\Models\Banner;
use Modules\Shop\Support\ShopTenancy;

/** Storefront home slider banners (multiple_pasal's BannerController). */
class BannerController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.banner.index', ['banners' => Banner::get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['image'] = $request->file('image')->store('shop/'.ShopTenancy::id().'/banners', 'public');

        Banner::create($data);

        return back()->with('success', 'Banner added.');
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('shop/'.ShopTenancy::id().'/banners', 'public');
        }

        $banner->update($data);

        return back()->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }

    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'sub_title'  => 'nullable|string|max:255',
            'url'        => 'nullable|string|max:255',
            'image'      => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'status'     => 'nullable|boolean',
        ], ['image.required' => 'Choose a banner image (wide, e.g. 1600×600).']);

        $data['status']     = $request->boolean('status', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['image']);

        return $data;
    }
}
