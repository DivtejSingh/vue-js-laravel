<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart_item;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Mvariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Auth;

class CartitemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
    {
        try {
            $userId = auth()->id();

            // 0) Gather all mvariant_id’s in this user’s wishlist:
            $wishlistVariantIds = Wishlist::where('user_id', $userId)
                                                    ->pluck('mvariant_id')
                                                    ->toArray();

            // 1) Pull all cart‐item rows for this user
            $cartItems = Cart_item::where('user_id', $userId)
                                ->where('status', 'active')
                                ->get();

            // 2) Map each cart‐item → combine variant + product + wishlist flag
            //    NOTE how we add "use ($wishlistVariantIds)" below.
            $cartWithProduct = $cartItems->map(function ($item) use ($wishlistVariantIds) {
                $variant = Mvariant::with([
                    'product:mproduct_id,mproduct_title,mproduct_image,mproduct_slug,mproduct_desc,status,saleschannel,mproduct_type_id,mbrand_id',
                    'product.type:mproduct_type_id,mproduct_type_name',
                    'product.brand:mbrand_id,mbrand_name',
                    'mstock:mvariant_id,quantity,mlocation_id',
                    'mvariantDetail:mvariant_id,options,option_value'
                ])->find($item->mvariant_id);

                if (! $variant) {
                    return null; // skip if variant not found
                }

                $product = $variant->product;

                $rawOptions      = optional($variant->mvariantDetail)->options;
                $rawOptionValue  = optional($variant->mvariantDetail)->option_value;

                $parsedOptions = null;
                if (is_string($rawOptions)) {
                    $parsedOptions = json_decode($rawOptions, true);
                } elseif (is_array($rawOptions)) {
                    $parsedOptions = $rawOptions;
                }

                $parsedOptionValue = null;
                if (is_string($rawOptionValue)) {
                    $parsedOptionValue = json_decode($rawOptionValue, true);
                } elseif (is_array($rawOptionValue)) {
                    $parsedOptionValue = $rawOptionValue;
                }

                // 3) Check if this variant is in the user’s wishlist array:
                $inWishlist = in_array($variant->mvariant_id, $wishlistVariantIds);

                return [
                    'cart_item_id' => $item->cart_item_id,
                    'mvariant_id'  => $variant->mvariant_id,
                    'quantity'     => $item->quantity,
                    'status'       => $item->status,

                    'product' => [
                        'mproduct_id'    => $product->mproduct_id,
                        'mproduct_title' => $product->mproduct_title,
                        'mproduct_image' => $product->mproduct_image,
                        'mproduct_slug'  => $product->mproduct_slug,
                        'mproduct_desc'  => $product->mproduct_desc,
                        'status'         => $product->status,
                        'saleschannel'   => $product->saleschannel,
                        'product_type'   => optional($product->type)->mproduct_type_name,
                        'brand_name'     => optional($product->brand)->mbrand_name,

                        // Here’s your wishlist flag:
                        'user_info_wishlist'  => $inWishlist,

                        'mvariant_id'    => $variant->mvariant_id,
                        'sku'            => $variant->sku,
                        'image'          => $variant->mvariant_image,
                        'price'          => $variant->price,
                        'compare_price'  => $variant->compare_price,
                        'cost_price'     => $variant->cost_price,
                        'taxable'        => $variant->taxable,
                        'barcode'        => $variant->barcode,
                        'options'        => $parsedOptions,
                        'option_value'   => $parsedOptionValue,
                        'stock'         => optional($variant->mstock)->quantity ?? 0,
                        'mlocation_id'  => optional($variant->mstock)->mlocation_id,
                    ],
                ];
            })
            ->filter() // remove any nulls
            ->values();

            return response()->json([
                'status'    => true,
                'message'   => 'Fetched all Cart Items successfully',
                'cdnURL'    => config('cdn.url'),
                'cart_item' => $cartWithProduct,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'message' => 'Token not found'], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $request->validate([
                'cart' => 'nullable|array'
            ]);

            $incomingCart = collect($request->cart);

            if ($incomingCart->isEmpty()) {
                Cart_item::where('user_id', $user->id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Cart cleared successfully.'
                ], 200);
            }

            $request->validate([
                'cart.*.mvariant_id' => 'required|integer|distinct',
                'cart.*.quantity'    => 'required|integer|min:1'
            ]);

            $existingCart = Cart_item::where('user_id', $user->id)->get();
            $incomingProductIds = $incomingCart->pluck('mvariant_id')->all();
            $existingProductIds = $existingCart->pluck('mvariant_id')->all();

            // Delete removed items
            $productsToDelete = array_diff($existingProductIds, $incomingProductIds);
            if (!empty($productsToDelete)) {
                Cart_item::where('user_id', $user->id)
                    ->whereIn('mvariant_id', $productsToDelete)
                    ->delete();
            }

            // Insert or update
            foreach ($incomingCart as $item) {
                $cartItem = $existingCart->firstWhere('mvariant_id', $item['mvariant_id']);

                if ($cartItem) {
                    if ($cartItem->quantity != $item['quantity']) {
                        $cartItem->quantity = $item['quantity'];
                        $cartItem->save();
                    }
                } else {
                    Cart_item::create([
                        'user_id'     => $user->id,
                        'mvariant_id' => $item['mvariant_id'],
                        'quantity'    => $item['quantity'],
                        'status'      => 'active'
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        try {
            $userId = auth()->id();

            $data = $request->validate([
                'code' => ['required', 'string', 'exists:coupons,code'],
            ]);

            $code = strtoupper($data['code']);

            $coupon = Coupon::where('code', $code)->first();
            if (! $coupon) {
                return response()->json(['status'=>false,'message'=>'Coupon not found'], 404);
            }

            if (! $coupon->isValid()) {
                return response()->json(['status'=>false,'message'=>'Coupon is expired or inactive'], 400);
            }

            if ($coupon->usage_limit !== null && $coupon->totalUsed() >= $coupon->usage_limit) {
                return response()->json(['status'=>false,'message'=>'Coupon usage limit reached'], 400);
            }

            $usage = CouponUsage::firstOrCreate(
                ['coupon_id' => $coupon->coupon_id, 'user_id' => $userId],
                ['used_count' => 0]
            );

            if ($coupon->per_user_limit !== null && $usage->used_count >= $coupon->per_user_limit) {
                return response()->json(['status'=>false,'message'=>'You have already used this coupon the maximum allowed times'], 400);
            }

            $cartItems = Cart_item::where('user_id', $userId)->where('status', 'active')->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['status'=>false,'message'=>'Cart is empty'], 400);
            }

            $subtotal = 0;
            foreach ($cartItems as $item) {
                $variant = Mvariant::find($item->mvariant_id);
                if (! $variant) continue;

                $subtotal += ($variant->price * $item->quantity);
            }

            if ($subtotal < $coupon->min_cart_value) {
                return response()->json([
                    'status'  => false,
                    'message' => "Cart total must be at least {$coupon->min_cart_value} to apply this coupon"
                ], 400);
            }

            if ($coupon->discount_type === 'fixed') {
                $discountAmount = min($coupon->discount_value, $subtotal);
            } else {
                $discountAmount = ($coupon->discount_value / 100) * $subtotal;
            }

            $newTotal = round($subtotal - $discountAmount, 2);

            DB::transaction(function () use ($coupon, $usage) {
                $usage->increment('used_count', 1);
            });

            return response()->json([
                'status'         => true,
                'message'        => 'Coupon applied successfully',
                'original_total' => $subtotal,
                'discount'       => round($discountAmount, 2),
                'new_total'      => $newTotal,
                'coupon_code'    => $coupon->code,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'message' => 'Token not found'], 401);
        }
    }

   
}
