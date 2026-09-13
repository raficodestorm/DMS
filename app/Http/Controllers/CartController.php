<?php

namespace App\Http\Controllers;

use App\Services\PricingEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected PricingEngineService $pricingEngine;

    public function __construct(PricingEngineService $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
    }

    /**
     * Calculate complete cart data live using the Pricing Engine.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculate(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $appliedCoupons = $request->input('applied_coupons', []);
        $country = $request->input('country');
        $city = $request->input('city');

        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }

        if (is_string($appliedCoupons)) {
            $appliedCoupons = json_decode($appliedCoupons, true) ?? [];
        }

        $result = $this->pricingEngine->calculateCart($items, $appliedCoupons, $country, $city);

        return response()->json($result);
    }

    /**
     * Validate and apply a coupon code for a product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'coupon_code' => 'required|string|max:50',
        ]);

        $productId = (int) $request->input('product_id');
        $couponCode = trim($request->input('coupon_code'));

        $validation = $this->pricingEngine->validateCoupon($productId, $couponCode);

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $validation['message'],
            'coupon_code' => $validation['coupon_code'],
            'offer_name' => $validation['offer_name'],
            'discount_amount' => $validation['discount_amount'],
            'discount_type' => $validation['discount_type'],
        ]);
    }
}
