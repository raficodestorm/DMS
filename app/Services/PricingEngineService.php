<?php

namespace App\Services;

use App\Models\Deduction;
use App\Models\Offer;
use App\Models\Product;
use App\Models\ShippingRate;
use Illuminate\Support\Collection;

class PricingEngineService
{
    /**
     * Cache the retail deduction percentage per request.
    /**
     * Cache supplier deductions map and default deduction.
     */
    protected ?array $supplierDeductions = null;
    protected ?float $defaultRetailDeduction = null;

    /**
     * Get the retail deduction percentage for a specific supplier, or global fallback.
     */
    public function getRetailDeductionPercent(?int $supplierId = null): float
    {
        if ($this->supplierDeductions === null) {
            $this->supplierDeductions = [];
            $allDeductions = Deduction::orderByRaw("CASE WHEN type = 'main' THEN 1 ELSE 2 END")->get();
            foreach ($allDeductions as $d) {
                if ($d->supplier_id && !isset($this->supplierDeductions[$d->supplier_id])) {
                    $this->supplierDeductions[$d->supplier_id] = (float) $d->retail_deduction;
                }
            }
            $this->defaultRetailDeduction = (float) (
                Deduction::where('type', 'main')->value('retail_deduction')
                ?? Deduction::value('retail_deduction')
                ?? 0.0
            );
        }

        if ($supplierId && isset($this->supplierDeductions[$supplierId])) {
            return $this->supplierDeductions[$supplierId];
        }

        return $this->defaultRetailDeduction ?? 0.0;
    }

    /**
     * Calculate price for a single product.
     *
     * @param Product|int $product
     * @param int $quantity
     * @param string|null $couponCode
     * @return array
     */
    public function calculateItemPrice(Product|int $product, int $quantity = 1, ?string $couponCode = null): array
    {
        if (is_numeric($product)) {
            $product = Product::with(['category', 'supplier', 'activeRetailOffer'])->find($product);
        }

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        $quantity = max(1, (int) $quantity);
        $basePrice = (float) ($product->price ?? 0);
        $deductionPercent = $this->getRetailDeductionPercent($product->supplier_id ?? null);

        // 1. Initial price after retail deduction
        $deductionAmount = ($basePrice * ($deductionPercent / 100));
        $priceAfterDeduction = max(0, $basePrice - $deductionAmount);

        // 2. Active retail offer check
        $today = now()->toDateString();
        $offer = $product->relationLoaded('activeRetailOffer')
            ? $product->activeRetailOffer
            : Offer::where('product_id', $product->id)
                ->where('status', 1)
                ->where('customer_type', 'retail')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();

        $offerDiscountUnit = 0.0;
        $couponApplied = false;
        $hasCouponRequirement = false;
        $offerName = null;
        $offerText = null;
        $availableCouponCode = null;
        $hasFreeShipping = false;

        if ($offer) {
            $offerName = $offer->name;
            $hasCouponRequirement = !empty($offer->coupon_code);
            $availableCouponCode = $offer->coupon_code;

            // Format offer display text
            if ($offer->type === 'percentage') {
                $pctVal = ((float) $offer->discount_amount == (int) $offer->discount_amount)
                    ? (int) $offer->discount_amount
                    : (float) $offer->discount_amount;
                $offerText = $pctVal . '% OFF';
            } elseif ($offer->type === 'free_shipping') {
                $offerText = '🚚 Free Shipping';
            } else {
                $offerText = '৳' . number_format(round($offer->discount_amount), 0) . ' OFF';
            }

            // Check if coupon is required vs auto-applied
            if ($hasCouponRequirement) {
                // Requires valid matching coupon code
                if ($couponCode && strcasecmp(trim($couponCode), trim($offer->coupon_code)) === 0) {
                    if ($offer->type === 'free_shipping') {
                        $hasFreeShipping = true;
                        $couponApplied = true;
                    } else {
                        $offerDiscountUnit = $this->calculateOfferDiscountValue($priceAfterDeduction, $offer);
                        $couponApplied = true;
                    }
                }
            } else {
                // Auto-applied offer without coupon code
                if ($offer->type === 'free_shipping') {
                    $hasFreeShipping = true;
                } else {
                    $offerDiscountUnit = $this->calculateOfferDiscountValue($priceAfterDeduction, $offer);
                }
            }
        }

        // 3. Final unit price and subtotals
        $unitPrice = round($priceAfterDeduction, 2); // Unit price = price after retail deduction
        $finalUnitPrice = max(0, round($priceAfterDeduction - $offerDiscountUnit, 2)); // Final price after offer discount
        $subtotal = round($finalUnitPrice * $quantity, 2); // Final total for this item
        $originalSubtotal = round($basePrice * $quantity, 2);
        $itemOfferSavings = round($offerDiscountUnit * $quantity, 2); // Savings = only offer discount amount

        // Offer snapshot string (e.g. "5%" or "5TK")
        $offerSnapshot = null;
        if ($offer && $offerDiscountUnit > 0) {
            if ($offer->type === 'percentage') {
                $pct = ((float) $offer->discount_amount == (int) $offer->discount_amount)
                    ? (int) $offer->discount_amount
                    : (float) $offer->discount_amount;
                $offerSnapshot = $pct . '%';
            } elseif ($offer->type === 'fixed') {
                $fixedVal = ((float) $offer->discount_amount == (int) $offer->discount_amount)
                    ? (int) $offer->discount_amount
                    : (float) $offer->discount_amount;
                $offerSnapshot = $fixedVal . 'TK';
            }
        }

        return [
            'success' => true,
            'product_id' => $product->id,
            'name' => $product->name,
            'category' => $product->category->name ?? 'General',
            'image' => !empty($product->image) ? asset($product->image) : '',
            'in_stock' => (bool) ($product->status == 1),
            'quantity' => $quantity,
            'base_price' => $basePrice,
            'base_price_formatted' => '৳' . number_format(round($basePrice), 0),
            'deduction_percent' => $deductionPercent,
            'deduction_amount' => round($deductionAmount),
            'price_after_deduction' => $unitPrice,
            'price_after_deduction_formatted' => '৳' . number_format(round($unitPrice), 0),
            'has_offer' => ($offer !== null),
            'offer_name' => $offerName,
            'offer_text' => $offerText,
            'offer_snapshot' => $offerSnapshot,
            'has_free_shipping' => $hasFreeShipping,
            'has_coupon_requirement' => $hasCouponRequirement,
            'available_coupon_code' => $availableCouponCode,
            'coupon_applied' => $couponApplied,
            'applied_coupon_code' => $couponApplied ? $offer->coupon_code : null,
            'offer_discount_unit' => round($offerDiscountUnit),
            'unit_price' => $unitPrice,
            'unit_price_formatted' => '৳' . number_format(round($unitPrice), 0),
            'final_unit_price' => $finalUnitPrice,
            'final_unit_price_formatted' => '৳' . number_format(round($finalUnitPrice), 0),
            'subtotal' => $subtotal,
            'subtotal_formatted' => '৳' . number_format(round($subtotal), 0),
            'original_subtotal' => $originalSubtotal,
            'total_savings' => $itemOfferSavings,
            'total_savings_formatted' => '৳' . number_format(round($itemOfferSavings), 0),
        ];
    }

    /**
     * Calculate complete cart pricing given items, coupons, country, and city.
     *
     * @param array $cartItems [['product_id' => int, 'quantity' => int, 'coupon_code' => string|null], ...]
     * @param array $appliedCoupons [product_id => coupon_code, ...]
     * @param string|null $country
     * @param string|null $city
     * @return array
     */
    public function calculateCart(
        array $cartItems,
        array $appliedCoupons = [],
        ?string $country = null,
        ?string $city = null
    ): array {
        if (empty($cartItems)) {
            return [
                'success' => true,
                'items' => [],
                'total_items' => 0,
                'subtotal' => 0.0,
                'subtotal_formatted' => '৳0',
                'original_subtotal' => 0.0,
                'total_savings' => 0.0,
                'total_savings_formatted' => '৳0',
                'shipping_fee' => 0.0,
                'shipping_formatted' => '৳0',
                'shipping_name' => null,
                'is_free_shipping' => false,
                'all_items_free_shipping' => false,
                'final_total' => 0.0,
                'final_total_formatted' => '৳0',
                'grand_total' => 0.0,
                'grand_total_formatted' => '৳0',
                'retail_deduction_percent' => $this->getRetailDeductionPercent(),
            ];
        }

        // Collect product IDs and quantity mapping
        $itemMap = [];
        $productIds = [];

        foreach ($cartItems as $item) {
            $pId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            if ($pId <= 0) continue;

            $qty = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
            $coupon = $item['coupon_code'] ?? ($appliedCoupons[$pId] ?? null);

            $itemMap[$pId] = [
                'quantity' => $qty,
                'coupon_code' => $coupon,
            ];
            $productIds[] = $pId;
        }

        if (empty($productIds)) {
            return [
                'success' => true,
                'items' => [],
                'total_items' => 0,
                'subtotal' => 0.0,
                'subtotal_formatted' => '৳0',
                'original_subtotal' => 0.0,
                'total_savings' => 0.0,
                'total_savings_formatted' => '৳0',
                'shipping_fee' => 0.0,
                'shipping_formatted' => '৳0',
                'shipping_name' => null,
                'is_free_shipping' => false,
                'all_items_free_shipping' => false,
                'final_total' => 0.0,
                'final_total_formatted' => '৳0',
                'grand_total' => 0.0,
                'grand_total_formatted' => '৳0',
                'retail_deduction_percent' => $this->getRetailDeductionPercent(),
            ];
        }

        // Batch query all products with active offers to eliminate N+1
        $today = now()->toDateString();
        $products = Product::whereIn('id', $productIds)
            ->with([
                'category',
                'supplier',
                'activeRetailOffer',
            ])
            ->get()
            ->keyBy('id');

        $calculatedItems = [];
        $totalItems = 0;
        $cartSubtotal = 0.0;
        $originalSubtotal = 0.0;
        $totalSavings = 0.0;
        $cartHasAnyFreeShipping = false;

        foreach ($itemMap as $pId => $data) {
            $product = $products->get($pId);
            if (!$product) continue;

            $calculated = $this->calculateItemPrice(
                $product,
                $data['quantity'],
                $data['coupon_code']
            );

            if ($calculated['success']) {
                $calculatedItems[] = $calculated;
                $totalItems += $calculated['quantity'];
                $cartSubtotal += $calculated['subtotal'];
                $originalSubtotal += $calculated['original_subtotal'];
                $totalSavings += $calculated['total_savings'];
                if (!empty($calculated['has_free_shipping'])) {
                    $cartHasAnyFreeShipping = true;
                }
            }
        }

        // Free Shipping Rule:
        // Free shipping applies ONLY if ALL products in the cart have an active free shipping offer/coupon
        $allFreeShipping = !empty($calculatedItems) && collect($calculatedItems)->every(fn($item) => !empty($item['has_free_shipping']));

        $shippingFee = 0.0;
        $shippingRateName = null;
        $isFreeShipping = false;

        if ($allFreeShipping) {
            $shippingFee = 0.0;
            $isFreeShipping = true;
            $shippingFormatted = 'FREE 🎉';
            $shippingRateName = 'Free Shipping Offer';
        } else {
            $shippingRate = $this->getShippingRateForLocation($country, $city);
            if ($shippingRate) {
                $shippingFee = (float) $shippingRate->base_rate;
                $shippingRateName = $shippingRate->name;
            } else {
                // Fallback default shipping rate if location is not found in shipping_rates table
                $shippingFee = 3000.0;
                $shippingRateName = 'Standard Delivery';
            }
            $shippingFormatted = '৳' . number_format(round($shippingFee), 0);
        }

        $grandTotal = round($cartSubtotal + $shippingFee);

        return [
            'success' => true,
            'items' => $calculatedItems,
            'total_items' => $totalItems,
            'subtotal' => round($cartSubtotal),
            'subtotal_formatted' => '৳' . number_format(round($cartSubtotal), 0),
            'original_subtotal' => round($originalSubtotal),
            'original_subtotal_formatted' => '৳' . number_format(round($originalSubtotal), 0),
            'total_savings' => round($totalSavings),
            'total_savings_formatted' => '৳' . number_format(round($totalSavings), 0),
            'shipping_fee' => round($shippingFee),
            'shipping_formatted' => $shippingFormatted,
            'shipping_name' => $shippingRateName,
            'is_free_shipping' => $isFreeShipping,
            'cart_has_free_shipping' => $cartHasAnyFreeShipping,
            'all_items_free_shipping' => $allFreeShipping,
            'final_total' => round($cartSubtotal),
            'final_total_formatted' => '৳' . number_format(round($cartSubtotal), 0),
            'grand_total' => $grandTotal,
            'grand_total_formatted' => '৳' . number_format($grandTotal, 0),
            'retail_deduction_percent' => $this->getRetailDeductionPercent(),
        ];
    }

    /**
     * Look up active shipping rate for a country and city.
     *
     * @param string|null $country
     * @param string|null $city
     * @return ShippingRate|null
     */
    public function getShippingRateForLocation(?string $country, ?string $city): ?ShippingRate
    {
        if (empty($country) && empty($city)) {
            return null;
        }

        $country = trim($country ?? '');
        $city = trim($city ?? '');

        // 1. Exact match country + city
        if ($country && $city) {
            $rate = ShippingRate::where('status', 1)
                ->where('country', $country)
                ->where('city', $city)
                ->first();

            if ($rate) return $rate;

            // Case-insensitive match
            $rate = ShippingRate::where('status', 1)
                ->whereRaw('LOWER(country) = ?', [strtolower($country)])
                ->whereRaw('LOWER(city) = ?', [strtolower($city)])
                ->first();

            if ($rate) return $rate;
        }

        // 2. City match only (case-insensitive)
        if ($city) {
            $rate = ShippingRate::where('status', 1)
                ->whereRaw('LOWER(city) = ?', [strtolower($city)])
                ->first();

            if ($rate) return $rate;
        }

        return null;
    }

    /**
     * Validate a coupon code for a product.
     *
     * @param int $productId
     * @param string $couponCode
     * @return array
     */
    public function validateCoupon(int $productId, string $couponCode): array
    {
        $today = now()->toDateString();
        $offer = Offer::where('product_id', $productId)
            ->where('status', 1)
            ->where('customer_type', 'retail')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$offer) {
            return [
                'valid' => false,
                'message' => 'No active offers found for this product.',
            ];
        }

        if (empty($offer->coupon_code)) {
            return [
                'valid' => false,
                'message' => 'This product already has an automatic discount applied.',
            ];
        }

        if (strcasecmp(trim($couponCode), trim($offer->coupon_code)) !== 0) {
            return [
                'valid' => false,
                'message' => 'Invalid coupon code.',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Coupon code applied successfully!',
            'coupon_code' => $offer->coupon_code,
            'offer_name' => $offer->name,
            'discount_amount' => (float) $offer->discount_amount,
            'discount_type' => $offer->type,
        ];
    }

    /**
     * Internal calculation of offer discount value against pre-deduction price.
     */
    protected function calculateOfferDiscountValue(float $priceAfterDeduction, Offer $offer): float
    {
        if ($offer->type === 'free_shipping') {
            return 0.0; // No price change — shipping benefit only
        }

        if ($offer->type === 'percentage') {
            return ($priceAfterDeduction * ((float) $offer->discount_amount / 100));
        }

        return min($priceAfterDeduction, (float) $offer->discount_amount);
    }
}
