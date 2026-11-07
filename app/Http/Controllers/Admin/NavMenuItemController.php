<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavMenuItem;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NavMenuItemController extends Controller
{
    public function index()
    {
        $company = Tenancy::company();

        $items = $company
            ? NavMenuItem::withoutGlobalScope('company')->where('company_id', $company->id)->orderBy('sort_order')->get()
            : collect();

        if ($items->isEmpty() && $company) {
            NavMenuItem::forCompany($company->id);
            $items = NavMenuItem::withoutGlobalScope('company')->where('company_id', $company->id)->orderBy('sort_order')->get();
        }

        return view('admin.theme_one.setting.navigation', ['navItems' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $company = Tenancy::company();
        abort_unless($company, 404);

        $data = $request->validate([
            'label'          => 'required|string|max:100',
            'url'            => 'required|string|max:255',
            'icon'           => 'nullable|string|max:60',
            'is_active'      => 'boolean',
        ]);

        $item = NavMenuItem::create($data + [
            'company_id' => $company->id,
            'sort_order' => NavMenuItem::withoutGlobalScope('company')->where('company_id', $company->id)->max('sort_order') + 1,
        ]);

        return response()->json($item);
    }

    public function update(Request $request, NavMenuItem $navMenuItem): JsonResponse
    {
        $data = $request->validate([
            'label'          => 'required|string|max:100',
            'url'            => 'required|string|max:255',
            'icon'           => 'nullable|string|max:60',
            'is_active'      => 'boolean',
        ]);

        $navMenuItem->update($data);

        return response()->json($navMenuItem);
    }

    public function destroy(NavMenuItem $navMenuItem): JsonResponse
    {
        $navMenuItem->delete();

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id'        => 'required|integer',
            'direction' => 'required|in:up,down',
        ]);

        $company = Tenancy::company();
        abort_unless($company, 404);

        $items = NavMenuItem::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->orderBy('sort_order')
            ->get();

        $index = $items->search(fn ($item) => $item->id === $data['id']);
        if ($index === false) {
            return response()->json(['success' => false], 404);
        }

        $swapWith = $data['direction'] === 'up' ? $index - 1 : $index + 1;
        if (! $items->has($swapWith)) {
            return response()->json(['success' => true]);
        }

        $a = $items[$index];
        $b = $items[$swapWith];
        [$a->sort_order, $b->sort_order] = [$b->sort_order, $a->sort_order];
        $a->save();
        $b->save();

        return response()->json(['success' => true]);
    }
}
