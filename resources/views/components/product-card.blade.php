@props(['product', 'customerDeduction' => null])

@php
    $isInStock = ($product->status == 1);
    $basePrice = (float) ($product->price ?? 0);

    if ($customerDeduction === null) {
        static $cachedDeductionPct = null;
        if ($cachedDeductionPct === null) {
            $cachedDeductionPct = (float) (\App\Models\Deduction::where('type', 'main')->value('retail_deduction') 
                ?? \App\Models\Deduction::value('retail_deduction') 
                ?? 30);
        }
        $deductionPct = $cachedDeductionPct;
    } else {
        $deductionPct = (float) $customerDeduction;
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

<div class="product-card-main">
    {{-- Top Badges & Wishlist --}}
    <div class="product-card-top">
        @if($offer && !empty($offerText))
            <span class="product-badge-offer">
                <i class="fas fa-bolt"></i> {{ $offerText }}
            </span>
        @else
            <span class="product-badge-new">R</span>
        @endif
        <button type="button" class="product-btn-wishlist" title="Add to Wishlist" onclick="event.preventDefault();">
            <i class="far fa-heart"></i>
        </button>
    </div>

    {{-- Product Image --}}
    <a href="javascript:void(0)" class="product-img-box">
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
        <a href="javascript:void(0)" class="product-card-title" title="{{ $product->name }}">
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
            <a href="javascript:void(0)" class="btn-card-view">
                <i class="fas fa-eye"></i> View Product
            </a>
        </div>
    </div>
</div>
