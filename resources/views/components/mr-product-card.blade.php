@props(['product', 'customerDeduction' => null])

@php
    $isInStock = ($product->status == 1);
    $basePrice = (float) ($product->price ?? 0);

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

    $sellingPrice = round($priceAfterDeduction);
    $roundedBasePrice = round($basePrice);
    $hasDiscount = ($sellingPrice < $roundedBasePrice);
@endphp

<div class="product-card-main">
    {{-- Top Badges & Wishlist --}}
    <div class="product-card-top">
        <span class="product-badge-new">R</span>
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
                @if($hasDiscount)
                    <span class="product-card-price">৳{{ number_format($sellingPrice) }}</span>
                    <del class="product-card-old-price">৳{{ number_format($roundedBasePrice) }}</del>
                @else
                    <span class="product-card-price">৳{{ number_format($roundedBasePrice) }}</span>
                @endif
            </div>
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
