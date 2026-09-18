<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified product details page.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'images', 'activeRetailOffer']);

        $today = now()->toDateString();

        // Pricing
        $basePrice    = (float) ($product->price ?? 0);
        $deductionPct = (float) (\App\Models\Deduction::where('type', 'main')->value('retail_deduction') ?? 0);
        $priceAfterDeduction = $deductionPct > 0 ? $basePrice * (1 - $deductionPct / 100) : $basePrice;

        $offer = $product->activeRetailOffer;
        $offerDiscountVal = 0;
        $offerText        = null;
        $offerEndDate     = null;

        if ($offer) {
            $offerEndDate = $offer->end_date;
            if ($offer->type === 'free_shipping') {
                $offerText = 'Free Shipping';
            } elseif ($offer->type === 'percentage') {
                $offerDiscountVal = $priceAfterDeduction * (float) $offer->discount_amount / 100;
                $offerText = ((int)$offer->discount_amount == (float)$offer->discount_amount
                    ? (int)$offer->discount_amount
                    : (float)$offer->discount_amount) . '% OFF';
            } else {
                $offerDiscountVal = (float) $offer->discount_amount;
                $offerText = '৳' . ((int)$offer->discount_amount == (float)$offer->discount_amount
                    ? (int)$offer->discount_amount
                    : number_format($offer->discount_amount, 2)) . ' OFF';
            }
        }

        $sellingPrice  = round(max(0, $priceAfterDeduction - $offerDiscountVal));
        $originalPrice = round($basePrice);
        $hasDiscount   = $sellingPrice < $originalPrice;

        // Wishlist state
        if (auth()->check()) {
            $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)->exists();
        } else {
            $raw        = request()->cookie('guest_wishlist', '[]');
            $guestIds   = json_decode($raw, true);
            $inWishlist = is_array($guestIds) && in_array($product->id, $guestIds);
        }

        // Related products — same category, exclude current
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->with(['activeRetailOffer'])
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('pages.product.show', compact(
            'product',
            'basePrice',
            'priceAfterDeduction',
            'sellingPrice',
            'originalPrice',
            'hasDiscount',
            'offer',
            'offerText',
            'offerEndDate',
            'deductionPct',
            'inWishlist',
            'relatedProducts'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
