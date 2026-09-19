<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DealController extends Controller
{
    /**
     * Display the Today's Deals page with 4 daily rotating in-stock products.
     */
    public function index()
    {
        $todayStr     = now()->toDateString();
        $todayKey     = 'todays_deals_' . $todayStr;
        $yesterdayKey = 'todays_deals_' . now()->subDay()->toDateString();
        $yesterdayIds = Cache::get($yesterdayKey, []);

        // Cache the 4 daily deal products until midnight
        $dealProducts = Cache::remember($todayKey, now()->endOfDay(), function () use ($yesterdayIds) {
            $baseQuery = Product::where('status', 1)
                ->whereHas('stocks', function ($q) {
                    $q->where('quantity', '>', 0);
                })
                ->withSum('stocks', 'quantity')
                ->with(['category', 'supplier', 'activeRetailOffer']);

            // Attempt to pick 4 products excluding yesterday's deals
            if (!empty($yesterdayIds) && is_array($yesterdayIds)) {
                $fresh = (clone $baseQuery)->whereNotIn('id', $yesterdayIds)->inRandomOrder()->take(4)->get();
                if ($fresh->count() >= 4) {
                    return $fresh;
                }
            }

            // Fallback if catalog size is small
            return $baseQuery->inRandomOrder()->take(4)->get();
        });

        // Store current deal product IDs for tomorrow's exclusion
        Cache::put($todayKey . '_ids', $dealProducts->pluck('id')->toArray(), now()->addDays(2));

        // Midnight countdown timestamp
        $midnightTimestamp = now()->endOfDay()->timestamp;

        return view('pages.deals.index', compact('dealProducts', 'midnightTimestamp'));
    }
}
