<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{

    public function index()
    {
        $offers = Offer::with('product')->latest()->paginate(10);
        return view('pages.admin.offer.index', compact('offers'));
    }


    public function create()
    {
        $products = Product::where('status', 1)->orderBy('name', 'asc')->get();
        return view('pages.admin.offer.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'product_id'      => 'required|exists:products,id',
            'type'            => 'required|in:percentage,fixed',
            'discount_amount' => 'required|numeric|min:0',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'status'          => 'boolean',
        ]);

        Offer::create($validated);

        return redirect()->route('admin.offers.index')->with('success', 'Offer created successfully!');
    }

    public function show(Offer $offer)
    {
        return view('pages.admin.offer.show', compact('offer'));
    }



    public function edit(Offer $offer)
    {
        $products = Product::where('status', 1)->orderBy('name', 'asc')->get();
        return view('pages.admin.offer.edit', compact('offer', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'product_id'      => 'required|exists:products,id',
            'type'            => 'required|in:percentage,fixed',
            'discount_amount' => 'required|numeric|min:0',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'status'          => 'required|in:0,1',
        ]);

        $offer->update($validated);

        return redirect()->route('admin.offers.index')->with('success', 'Offer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('admin.offers.index')->with('success', 'Offer deleted successfully!');
    }
}
