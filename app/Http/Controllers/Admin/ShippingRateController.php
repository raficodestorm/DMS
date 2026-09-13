<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    /**
     * Display a listing of shipping rates.
     */
    public function index(Request $request): View
    {
        $totalRates = ShippingRate::count();
        $activeRates = ShippingRate::where('status', 1)->count();
        $countries = ShippingRate::distinct()->pluck('country')->filter()->values();
        $cities = ShippingRate::distinct()->pluck('city')->filter()->values();
        $countriesData = $this->getCountriesData();

        return view('pages.admin.shipping-rate.index', compact('totalRates', 'activeRates', 'countries', 'cities', 'countriesData'));
    }

    /**
     * Fetch shipping rate index data for dynamic AJAX table and pagination.
     */
    public function fetchShippingRatesIndexData(Request $request): JsonResponse
    {
        $query = ShippingRate::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('country', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $activeCount = (clone $query)->where('status', 1)->count();
        $totalCount  = (clone $query)->count();
        $shippingRates = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'       => view('pages.admin.shipping-rate.table', compact('shippingRates'))->render(),
            'mobile'      => view('pages.admin.shipping-rate.mtable', compact('shippingRates'))->render(),
            'pagination'  => (string) $shippingRates->links(),
            'total'       => $shippingRates->total(),
            'activeRates' => $activeCount,
            'totalRates'  => $totalCount,
        ]);
    }

    /**
     * Show the form for creating a new shipping rate.
     */
    public function create(): View
    {
        $countriesData = $this->getCountriesData();

        return view('pages.admin.shipping-rate.create', compact('countriesData'));
    }

    /**
     * Store a newly created shipping rate in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'city' => [
                'required',
                'string',
                'max:100',
                Rule::unique('shipping_rates')->where(function ($query) use ($request) {
                    return $query->where('country', $request->country)
                        ->where('city', $request->city);
                }),
            ],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ], [
            'city.unique' => 'A shipping rate already exists for the selected Country and City combination.',
        ]);

        ShippingRate::create($validated);

        return redirect()
            ->route('admin.shipping-rates.index')
            ->with('success', 'Shipping rate created successfully!');
    }

    /**
     * Show the form for editing the specified shipping rate.
     */
    public function edit(ShippingRate $shippingRate): View
    {
        $countriesData = $this->getCountriesData();

        return view('pages.admin.shipping-rate.edit', compact('shippingRate', 'countriesData'));
    }

    /**
     * Update the specified shipping rate in storage.
     */
    public function update(Request $request, ShippingRate $shippingRate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'city' => [
                'required',
                'string',
                'max:100',
                Rule::unique('shipping_rates')->where(function ($query) use ($request) {
                    return $query->where('country', $request->country)
                        ->where('city', $request->city);
                })->ignore($shippingRate->id),
            ],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ], [
            'city.unique' => 'A shipping rate already exists for the selected Country and City combination.',
        ]);

        $shippingRate->update($validated);

        return redirect()
            ->route('admin.shipping-rates.index')
            ->with('success', 'Shipping rate updated successfully!');
    }

    /**
     * Remove the specified shipping rate from storage.
     */
    public function destroy(ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->delete();

        return redirect()
            ->route('admin.shipping-rates.index')
            ->with('success', 'Shipping rate deleted successfully!');
    }

    /**
     * Toggle the status of the specified shipping rate.
     */
    public function toggleStatus(ShippingRate $shippingRate): RedirectResponse|JsonResponse
    {
        $shippingRate->update(['status' => ! $shippingRate->status]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $shippingRate->status,
                'message' => 'Status updated successfully!',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Shipping rate status updated successfully!');
    }

    /**
     * Load countries data from countries.json file.
     *
     * @return array<string, array<int, string>>
     */
    private function getCountriesData(): array
    {
        $filePath = resource_path('data/countries.json');

        if (! file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);

        return json_decode($content, true) ?? [];
    }
}
