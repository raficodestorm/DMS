@props(['product', 'customerDeduction' => null])

@php
    $isInStock = ($product->status == 1);
    $basePrice = (float) ($product->price ?? 0);

    if ($customerDeduction === null) {
        static $cachedDeductionPct = null;
        if ($cachedDeductionPct === null) {
            $cachedDeductionPct = (float) (\App\Models\Deduction::where('type', 'main')->value('customer_deduction') 
                ?? \App\Models\Deduction::value('customer_deduction') 
                ?? 0);
        }
        $deductionPct = $cachedDeductionPct;
    } else {
        $deductionPct = (float) $customerDeduction;
    }

    $hasDiscount = ($deductionPct > 0);
    $sellingPrice = $hasDiscount ? round($basePrice * (1 - ($deductionPct / 100))) : round($basePrice);
    $roundedBasePrice = round($basePrice);
@endphp

<div class="product-card-main">
    {{-- Top Badges & Wishlist --}}
    <div class="product-card-top">
        <span class="product-badge-new">New</span>
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
            @if($hasDiscount)
                <span class="product-card-price">৳{{ number_format($sellingPrice) }}</span>
                <del class="product-card-old-price">৳{{ number_format($roundedBasePrice) }}</del>
            @else
                <span class="product-card-price">৳{{ number_format($roundedBasePrice) }}</span>
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
            <button type="button" class="btn-card-cart">
                <i class="fas fa-shopping-bag"></i> Add to Cart
            </button>
            <a href="javascript:void(0)" class="btn-card-view">
                <i class="fas fa-eye"></i> View Product
            </a>
        </div>
    </div>
</div>
