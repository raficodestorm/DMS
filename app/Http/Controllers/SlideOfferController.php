<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class SlideOfferController extends Controller
{
    /**
     * Display the campaign page for a specific slide with its connected offer products.
     */
    public function show(Slide $slide)
    {
        // If inactive and user is not admin, abort 404
        if (!$slide->status && (!auth()->check() || auth()->user()->role !== 'admin')) {
            abort(404, 'Campaign banner is not active.');
        }

        $slide->load(['offers' => function ($q) {
            $q->where('status', 1)->with('product');
        }]);

        $productIds = $slide->offers->pluck('product_id')->filter()->unique()->toArray();

        // Query all products belonging to this slide's offers
        $products = Product::where('status', 1)
            ->whereIn('id', $productIds)
            ->withSum('stocks', 'quantity')
            ->with(['category', 'supplier', 'activeRetailOffer'])
            ->latest()
            ->get();

        return view('pages.campaign.show', compact('slide', 'products', 'productIds'));
    }
}
