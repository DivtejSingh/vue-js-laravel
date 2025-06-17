<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * List all orders (with their items).
     */
    public function index()
    {
        $orders = Order::with([
            'items.variant.product',
            'user:id,name,email',
            'userCompanyAddress',
            'deliveryMethod' 
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        $payload = $orders->map(function($order) {
            $units = $order->items->sum('quantity');
            $skus  = $order->items->count();

            $subtotal = $order->items->sum(function($itm) {
                return $itm->unit_price * $itm->quantity;
            });

            $walletDiscount = $order->wallet_discount ?? 0;
            $couponDiscount = $order->coupon_discount ?? 0;

            $deliveryId = optional($order->deliveryMethod)->delivery_method_id;
            $deliveryName = optional($order->deliveryMethod)->delivery_method_name;
            $deliveryCost = optional($order->deliveryMethod)->delivery_method_amount;

            $taxableAmount = max(0, $subtotal - $walletDiscount - $couponDiscount);
            $vat = round($taxableAmount * 0.20, 2);

            $paymentTotal = round(
                $subtotal 
                - $walletDiscount 
                - $couponDiscount 
                + $deliveryCost 
                + $vat,
                2
            );

            $addressId = optional($order->userCompanyAddress)->user_company_address_id;
            $address = optional($order->userCompanyAddress)->full_address;

            $items = $order->items->map(function($itm) {
                $variant = $itm->variant;
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

                return [
                    'order_item_id' => $itm->order_item_id,
                    'mvariant_id'   => $variant->mvariant_id,
                    'quantity'      => $itm->quantity,
                    'unit_price'    => (float) $itm->unit_price,
                    'variant' => [
                        'sku'           => $variant->sku,
                        'image'         => $variant->mvariant_image,
                        'price'         => (float) $variant->price,
                        'compare_price' => (float) $variant->compare_price,
                        'cost_price'    => (float) $variant->cost_price,
                        'options'        => $parsedOptions,
                        'option_value'   => $parsedOptionValue,
                    ],
                    'product' => [
                        'mproduct_id'    => $product->mproduct_id,
                        'mproduct_title' => $product->mproduct_title,
                        'mproduct_slug'  => $product->mproduct_slug,
                        'mproduct_image' => $product->mproduct_image,
                    ]
                ];
            });

            return [
                'order_id'   => $order->order_id,
                'user'       => [
                    'id'    => $order->user->id,
                    'name'  => $order->user->name,
                    'email' => $order->user->email,
                ],
                'order_date' => $order->created_at->toDateTimeString(),
                'units'      => $units,
                'payment_status'      => $order->status,
                'fulfillment_status'  => $order->fulfillment_status,
                'skus'       => $skus,

                'delivery' => [
                    'method_id'  => $deliveryId, 
                    'method'  => $deliveryName, 
                    'address_id'  => $addressId, 
                    'address' => $address,
                ],

                'summary' => [
                    'subtotal'        => round($subtotal, 2),
                    'wallet_discount' => round($walletDiscount, 2),
                    'coupon_discount' => round($couponDiscount, 2),
                    'delivery_cost'   => round($deliveryCost, 2),
                    'vat'             => round($vat, 2),
                    'payment_total'   => round($paymentTotal, 2),
                ],

                'items' => $items,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Fetch all Orders Successfully',
            'orders'  => $payload,
        ], 200);
    }

    /**
     * Create a new order (with nested items); calculate total_amount automatically.
     *
     * Expected JSON payload:
     * {
     *   "items": [
     *     { "mvariant_id": 123, "quantity": 2, "unit_price": 49.99 },
     *     { "mvariant_id": 57,  "quantity": 1, "unit_price": 19.50 }
     *   ]
     * }
     *
     * The authenticated user is automatically set as user.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.mvariant_id'       => ['required', 'integer'],
            'items.*.quantity'          => ['required', 'integer', 'min:1'],
            'items.*.unit_price'        => ['required', 'numeric', 'min:0.01'],
            'wallet_discount'           => ['nullable', 'numeric'],
            'coupon_discount'           => ['nullable', 'numeric'],
            'user_company_address_id'   => ['required', 'integer', 'exists:user_company_addresses,user_company_address_id'],
            'delivery_method_id'        => ['required', 'integer', 'exists:delivery_methods,delivery_method_id'],
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id'                    => $user->id,
                'total_amount'               => 0,
                'wallet_discount'            => $validated['wallet_discount'],
                'coupon_discount'            => $validated['coupon_discount'],
                'status'                     => 'pending',
                'fulfillment_status'         => 'unfulfilled',
                'user_company_address_id'    => $validated['user_company_address_id'],
                'delivery_method_id'         => $validated['delivery_method_id'],
            ]);

            $total = 0;
            foreach ($validated['items'] as $itemData) {
                $lineSubtotal = $itemData['quantity'] * $itemData['unit_price'];

                OrderItem::create([
                    'order_id'     => $order->order_id,        
                    'mvariant_id'  => $itemData['mvariant_id'],
                    'quantity'     => $itemData['quantity'],
                    'unit_price'   => $itemData['unit_price'],
                ]);

                $total += $lineSubtotal;
            }

            $order->total_amount = $total;
            $order->save();

            DB::commit();

            $order->load(['items', 'user:id,name,email']);
            return response()->json([
                'status'  => true,
                'message' => 'Order Placed Successfully',
                'order'    => $order,
            ], 201);
            
        }
        catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Retrieve a single order (with its items).
     */
    public function show($id)
    {
        $order = Order::with([
            'items.variant.product',
            'items.variant.mvariantDetail',    
            'items.variant.mstock',          
            'user:id,name,email',
            'userCompanyAddress',
            'deliveryMethod'
        ])->find($id);

        if (! $order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $units = $order->items->sum('quantity');
        $skus  = $order->items->count();

        $subtotal = $order->items->sum(function($itm) {
            return $itm->unit_price * $itm->quantity;
        });

        $walletDiscount = $order->wallet_discount ?? 0.00;
        $couponDiscount = $order->coupon_discount ?? 0.00;

        $deliveryId = optional($order->deliveryMethod)->delivery_method_id;
        $deliveryName = optional($order->deliveryMethod)->delivery_method_name;
        $deliveryCost = optional($order->deliveryMethod)->delivery_method_amount;

        $taxableAmount = max(0, $subtotal - $walletDiscount - $couponDiscount);
        $vat = round($taxableAmount * 0.20, 2);

        $paymentTotal = round(
            $subtotal 
            - $walletDiscount 
            - $couponDiscount 
            + $deliveryCost 
            + $vat,
            2
        );

        $addressId = optional($order->userCompanyAddress)->user_company_address_id;
        $address = optional($order->userCompanyAddress)->full_address;

        $items = $order->items->map(function($itm) {
            $variant = $itm->variant;
            $product = $variant->product;

            $rawOptions     = optional($variant->mvariantDetail)->options;
            $rawOptionValue = optional($variant->mvariantDetail)->option_value;

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

            return [
                'order_item_id' => $itm->order_item_id,
                'mvariant_id'   => $variant->mvariant_id,
                'quantity'      => $itm->quantity,
                'unit_price'    => (float) $itm->unit_price,

                'variant' => [
                    'sku'           => $variant->sku,
                    'image'         => $variant->mvariant_image,
                    'price'         => (float) $variant->price,
                    'compare_price' => (float) $variant->compare_price,
                    'cost_price'    => (float) $variant->cost_price,
                    'options'       => $parsedOptions,
                    'option_value'  => $parsedOptionValue,
                    'stock'         => optional($variant->mstock)->quantity ?? 0,
                    'mlocation_id'  => optional($variant->mstock)->mlocation_id,
                ],

                'product' => [
                    'mproduct_id'    => $product->mproduct_id,
                    'mproduct_title' => $product->mproduct_title,
                    'mproduct_slug'  => $product->mproduct_slug,
                    'mproduct_image' => $product->mproduct_image,
                ],
            ];
        })->values();

        $payload = [
            'order_id'            => $order->order_id,
            'user'                => [
                'id'    => $order->user->id,
                'name'  => $order->user->name,
                'email' => $order->user->email,
            ],
            'order_date'          => $order->created_at->toDateTimeString(),
            'units'               => $units,
            'payment_status'      => $order->status,
            'fulfillment_status'  => $order->fulfillment_status,
            'skus'                => $skus,

            'delivery' => [
                'method_id'  => $deliveryId, 
                'method'  => $deliveryName,
                'address_id' => $addressId,
                'address' => $address,
            ],

            'summary' => [
                'subtotal'        => round($subtotal, 2),
                'wallet_discount' => round($walletDiscount, 2),
                'coupon_discount' => round($couponDiscount, 2),
                'delivery_cost'   => round($deliveryCost, 2),
                'vat'             => round($vat, 2),
                'payment_total'   => round($paymentTotal, 2),
            ],

            'items' => $items,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Fetch Order Successfully',
            'order'   => $payload,
        ], 200);
    }

    /**
     * Update order status only (e.g. from 'pending' → 'paid' or 'shipped').
     *
     * Payload example:
     * {
     *   "status": "paid"
     * }
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['pending', 'paid', 'shipped', 'cancelled']),
            ],
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'status'  => true,
            'message' => 'Order Status Updated Successfully',
            'order'    => $order->load(['items', 'user:id,name,email']),
        ], 200);
    }

    /**
     * Delete an order (cascade deletes its items).
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(null, 204);
    }
}
