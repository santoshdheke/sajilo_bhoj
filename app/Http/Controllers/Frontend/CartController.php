<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Live data for the items in a visitor's cart, so the browser-stored cart always
 * shows current names, prices and photos and drops items that no longer exist.
 */
class CartController extends Controller
{
    public function items(Request $request): JsonResponse
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($id) => (int) $id)->filter()->unique()->take(100)->values();

        $items = self::lookup($ids->all())->map(fn (MenuItem $i) => [
            'id'    => $i->id,
            'name'  => $i->name,
            'price' => (float) $i->price,
            'img'   => $i->photoUrl() ?? '',
        ])->values();

        return response()->json(['items' => $items]);
    }

    /**
     * Available menu items of the current restaurant (categories are scoped to the tenant branch).
     */
    public static function lookup(array $ids)
    {
        if (! $ids) {
            return collect();
        }

        return MenuItem::with('category')
            ->whereIn('id', $ids)
            ->where('is_available', true)
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->get()
            ->keyBy('id');
    }
}
