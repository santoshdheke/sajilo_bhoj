<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Shop\Models\SpecialOffer;
use Modules\Shop\Support\ShopTenancy;

/** Special offer slides beside the storefront home banner (theme "vegitable"). */
class SpecialOfferController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.offer.index', ['offers' => SpecialOffer::get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['image'] = $request->file('image')->store('shop/'.ShopTenancy::id().'/offers', 'public');

        SpecialOffer::create($data);

        return back()->with('success', 'Offer added.');
    }

    public function update(Request $request, SpecialOffer $specialOffer): RedirectResponse
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($specialOffer->image);
            $data['image'] = $request->file('image')->store('shop/'.ShopTenancy::id().'/offers', 'public');
        }

        $specialOffer->update($data);

        return back()->with('success', 'Offer updated.');
    }

    public function destroy(SpecialOffer $specialOffer): RedirectResponse
    {
        Storage::disk('public')->delete($specialOffer->image);
        $specialOffer->delete();

        return back()->with('success', 'Offer deleted.');
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
        ], ['image.required' => 'Choose an offer image (portrait or square works best).']);

        $data['status']     = $request->boolean('status', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['image']);

        return $data;
    }
}
