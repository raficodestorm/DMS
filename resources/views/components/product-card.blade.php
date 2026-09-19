@props(['product', 'customerDeduction' => null])

@php
    $isInStock = (bool) $product->is_in_stock;
    $basePrice = (float) ($product->price ?? 0);

    // Wishlist state check
    if (auth()->check()) {
        $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)->exists();
    } else {
        $raw = request()->cookie('guest_wishlist', '[]');
        $guestIds = json_decode($raw, true);
        $inWishlist = is_array($guestIds) && in_array($product->id, $guestIds);
    }

    if ($customerDeduction !== null) {
        $deductionPct = (float) $customerDeduction;
    } else {
        // Cache all supplier deductions to prevent multiple queries across repeated cards
        static $supplierDeductionsMap = null;
        static $defaultDeductionPct = null;

        if ($supplierDeductionsMap === null) {
            $supplierDeductionsMap = [];
            $allDeductions = \App\Models\Deduction::orderByRaw("CASE WHEN type = 'main' THEN 1 ELSE 2 END")->get();
            foreach ($allDeductions as $d) {
                if ($d->supplier_id && !isset($supplierDeductionsMap[$d->supplier_id])) {
                    $supplierDeductionsMap[$d->supplier_id] = (float) $d->retail_deduction;
                }
            }
            $defaultDeductionPct = (float) (
                \App\Models\Deduction::where('type', 'main')->value('retail_deduction') 
                ?? \App\Models\Deduction::value('retail_deduction') 
                ?? 0
            );
        }

        $supplierId = $product->supplier_id ?? ($product->supplier?->id ?? null);
        if ($supplierId && isset($supplierDeductionsMap[$supplierId])) {
            $deductionPct = $supplierDeductionsMap[$supplierId];
        } else {
            $deductionPct = $defaultDeductionPct;
        }
    }

    $hasDeduction = ($deductionPct > 0);
    $priceAfterDeduction = $hasDeduction ? ($basePrice * (1 - ($deductionPct / 100))) : $basePrice;

    // Check for active retail offer
    $today = now()->toDateString();
    $offer = $product->relationLoaded('activeRetailOffer') 
        ? $product->activeRetailOffer 
        : ($product->relationLoaded('offers')
            ? $product->offers->first(fn($o) => $o->status == 1 && $o->customer_type === 'retail' && $o->start_date <= $today && $o->end_date >= $today)
            : \App\Models\Offer::where('product_id', $product->id)
                ->where('status', 1)
                ->where('customer_type', 'retail')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first()
          );

    $offerDiscountVal = 0;
    $offerText = null;
    if ($offer) {
        if ($offer->type === 'free_shipping') {
            $offerDiscountVal = 0;
            $offerText = 'Free Shipping';
        } elseif ($offer->type === 'percentage') {
            $offerDiscountVal = ($priceAfterDeduction * (float)$offer->discount_amount / 100);
            $offerAmountFormatted = ((float)$offer->discount_amount == (int)$offer->discount_amount) ? (int)$offer->discount_amount : (float)$offer->discount_amount;
            $offerText = $offerAmountFormatted . '% OFF';
        } else {
            $offerDiscountVal = (float)$offer->discount_amount;
            $offerAmountFormatted = ((float)$offer->discount_amount == (int)$offer->discount_amount) ? (int)$offer->discount_amount : number_format($offer->discount_amount, 2);
            $offerText = '৳' . $offerAmountFormatted . ' OFF';
        }
    }

    $finalPrice = max(0, $priceAfterDeduction - $offerDiscountVal);
    $sellingPrice = round($finalPrice);
    $roundedBasePrice = round($basePrice);
    $hasDiscount = ($sellingPrice < $roundedBasePrice);
@endphp

<div class="product-card-main" onclick="if (!event.target.closest('button, a, .product-btn-wishlist, .btn-card-cart')) { window.location.href = '{{ route('products.show', $product) }}'; }" style="cursor: pointer;">
    {{-- Top Badges & Wishlist --}}
    <div class="product-card-top">
        @if($offer && !empty($offerText))
            <span class="product-badge-offer">
                <i class="fas fa-bolt"></i> {{ $offerText }}
            </span>
        @else
            <span class="product-badge-new">R</span>
        @endif
        <button type="button"
            class="product-btn-wishlist {{ $inWishlist ? 'wishlisted' : '' }}"
            title="{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
            data-product-id="{{ $product->id }}"
            onclick="toggleWishlist(this, event)">
            <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
        </button>
    </div>

    {{-- Product Image --}}
    <a href="{{ route('products.show', $product) }}" class="product-img-box">
        @if(!empty($product->image))
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 38px; background: var(--primary-soft); border-radius: 8px;">
                <i class="fas fa-box-open"></i>
            </div>
        @endif
    </a>

    {{-- Product Body --}}
    <div class="product-card-body">
        <a href="{{ route('products.show', $product) }}" class="product-card-title" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <div class="product-card-price-wrap">
            <div class="product-card-price-group">
                @if($offer && !empty($offerText) && round($priceAfterDeduction) < $roundedBasePrice && $sellingPrice < round($priceAfterDeduction))
                    <span class="product-card-price">৳{{ number_format($sellingPrice) }}</span>
                    <del class="product-card-mid-price">৳{{ number_format(round($priceAfterDeduction)) }}</del>
                    <del class="product-card-base-price">৳{{ number_format($roundedBasePrice) }}</del>
                @elseif($hasDiscount)
                    <span class="product-card-price">৳{{ number_format($sellingPrice) }}</span>
                    <del class="product-card-old-price">৳{{ number_format($roundedBasePrice) }}</del>
                @else
                    <span class="product-card-price">৳{{ number_format($roundedBasePrice) }}</span>
                @endif
            </div>

            @if($offer && !empty($offer->coupon_code))
                <span class="product-coupon-pill" title="Coupon Code: {{ $offer->coupon_code }}">
                    Coupon: {{ $offer->coupon_code }}
                </span>
            @endif
        </div>

        <div class="product-card-stock {{ $isInStock ? '' : 'out-of-stock' }}">
            @if($isInStock)
                <i class="fas fa-check-circle"></i> In Stock
            @else
                <i class="fas fa-times-circle"></i> Out of Stock
            @endif
        </div>

        <div class="product-card-actions">
            @if($isInStock)
            <button type="button" class="btn-card-cart"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                onclick="addToCart(this)">
                <i class="fas fa-shopping-bag"></i> Add to Cart
            </button>
            @else
            <button type="button" class="btn-card-cart" style="opacity: 0.6; cursor: not-allowed;" disabled>
                <i class="fas fa-ban"></i> Out of Stock
            </button>
            @endif
        </div>
    </div>
</div>
