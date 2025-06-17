<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Coupons fetched successfully',
            'data'    => $coupons,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'              => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'discount_type'     => ['required', Rule::in(['fixed','percent'])],
            'discount_value'    => ['required', 'numeric', 'min:0'],
            'expires_at'        => ['nullable', 'date', 'after:now'],
            'usage_limit'       => ['nullable', 'integer', 'min:1'],
            'per_user_limit'    => ['nullable', 'integer', 'min:1'],
            'min_cart_value'    => ['nullable', 'numeric', 'min:0'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        $coupon = Coupon::create([
            'code'              => strtoupper($validated['code']),
            'discount_type'     => $validated['discount_type'],
            'discount_value'    => $validated['discount_value'],
            'expires_at'        => $validated['expires_at'] ?? null,
            'usage_limit'       => $validated['usage_limit'] ?? null,
            'per_user_limit'    => $validated['per_user_limit'] ?? 1,
            'min_cart_value'    => $validated['min_cart_value'] ?? 0,
            'is_active'         => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Coupon created successfully',
            'data'    => $coupon,
        ], 201);
    }

    public function show($coupon_id)
    {
        $coupon = Coupon::find($coupon_id);
        if (! $coupon) {
            return response()->json(['status'=>false,'message'=>'Coupon not found'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Coupon details fetched',
            'data'    => $coupon,
        ], 200);
    }

    public function update(Request $request, $coupon_id)
    {
        $coupon = Coupon::find($coupon_id);
        if (! $coupon) {
            return response()->json(['status'=>false,'message'=>'Coupon not found'], 404);
        }

        $validated = $request->validate([
            'code'              => ['sometimes','required','string','max:50', Rule::unique('coupons','code')->ignore($coupon->coupon_id,'coupon_id')],
            'discount_type'     => ['sometimes','required', Rule::in(['fixed','percent'])],
            'discount_value'    => ['sometimes','required','numeric','min:0'],
            'expires_at'        => ['nullable','date','after:now'],
            'usage_limit'       => ['nullable','integer','min:1'],
            'per_user_limit'    => ['nullable','integer','min:1'],
            'min_cart_value'    => ['nullable','numeric','min:0'],
            'is_active'         => ['nullable','boolean'],
        ]);

        $coupon->update([
            'code'              => strtoupper($validated['code'] ?? $coupon->code),
            'discount_type'     => $validated['discount_type'] ?? $coupon->discount_type,
            'discount_value'    => $validated['discount_value'] ?? $coupon->discount_value,
            'expires_at'        => $validated['expires_at'] ?? $coupon->expires_at,
            'usage_limit'       => $validated['usage_limit'] ?? $coupon->usage_limit,
            'per_user_limit'    => $validated['per_user_limit'] ?? $coupon->per_user_limit,
            'min_cart_value'    => $validated['min_cart_value'] ?? $coupon->min_cart_value,
            'is_active'         => $validated['is_active'] ?? $coupon->is_active,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Coupon updated successfully',
            'data'    => $coupon,
        ], 200);
    }

    public function destroy($coupon_id)
    {
        $coupon = Coupon::find($coupon_id);
        if (! $coupon) {
            return response()->json(['status'=>false,'message'=>'Coupon not found'], 404);
        }
        $coupon->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Coupon deleted successfully',
        ], 200);
    }
}
