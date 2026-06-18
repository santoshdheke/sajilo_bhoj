<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();

        return view('admin.theme_one.coupon.index', compact('coupons'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['code'] = strtoupper($data['code']);

        $coupon = Coupon::create($data);

        return response()->json($coupon);
    }

    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $data = $this->validated($request, $coupon->id);
        $data['code'] = strtoupper($data['code']);

        $coupon->update($data);

        return response()->json($coupon);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return response()->json(['success' => true]);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $company = \App\Support\Tenancy::company();

        return $request->validate([
            'code'           => [
                'required', 'string', 'max:40',
                \Illuminate\Validation\Rule::unique('coupons', 'code')
                    ->where('company_id', $company?->id)
                    ->ignore($ignoreId),
            ],
            'discount_type'  => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_to'       => 'nullable|date|after_or_equal:valid_from',
        ]);
    }
}
