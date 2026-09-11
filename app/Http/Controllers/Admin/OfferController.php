<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{

    public function index(Request $request)
    {
        $totalOffers = Offer::count();
        $activeOffers = Offer::where('status', 1)->count();
        return view('pages.admin.offer.index', compact('totalOffers', 'activeOffers'));
    }

    public function fetchOffersIndexData(Request $request)
    {
        $query = Offer::with(['product', 'product.category'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        

        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        $activeCount = (clone $query)->where('status', 1)->count();
        $totalCount  = (clone $query)->count();
        $offers      = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'        => view('pages.admin.offer.table', compact('offers'))->render(),
            'mobile'       => view('pages.admin.offer.mtable', compact('offers'))->render(),
            'pagination'   => (string) $offers->links(),
            'total'        => $offers->total(),
            'activeOffers' => $activeCount,
            'totalOffers'  => $totalCount,
        ]);
    }


    public function create()
    {
        $products = Product::where('status', 1)->with('category')->orderBy('name', 'asc')->get();
        return view('pages.admin.offer.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'customer_type'   => 'required|string|max:255',
            'product_id'      => 'required|exists:products,id',
            'coupon_code'     => 'nullable|string|max:255',
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
        $products = Product::where('status', 1)->with('category')->orderBy('name', 'asc')->get();
        return view('pages.admin.offer.edit', compact('offer', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'customer_type'   => 'required|string|max:255',
            'product_id'      => 'required|exists:products,id',
            'coupon_code'     => 'nullable|string|max:255',
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
