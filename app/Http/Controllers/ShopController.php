<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display the public Shop page with search, category tabs, and product cards.
     */
    public function index(Request $request)
    {
        $selectedCategory = $request->query('category');
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'latest');
        $inStockOnly = $request->boolean('in_stock');
        $offersOnly = $request->boolean('offer') || $request->boolean('offers') || $request->boolean('special_offers') || $request->query('offer') == '1';

        // Fetch categories with active product counts
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('status', 1);
        }])
        ->having('products_count', '>', 0)
        ->orderBy('name', 'asc')
        ->get();

        $totalActiveProducts = Product::where('status', 1)->count();

        // Base query for active products
        $query = Product::where('status', 1)
            ->with(['category', 'supplier', 'activeRetailOffer'])
            ->withSum('stocks', 'quantity');

        // Filter Special Offers (Retail)
        if ($offersOnly) {
            $today = now()->toDateString();
            $query->whereHas('offers', function ($q) use ($today) {
                $q->where('status', 1)
                  ->where('customer_type', 'retail')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            });
        }

        // Filter by Category
        if ($selectedCategory && $selectedCategory !== 'all') {
            if (is_numeric($selectedCategory)) {
                $query->where('category_id', (int) $selectedCategory);
            } else {
                $query->whereHas('category', function ($q) use ($selectedCategory) {
                    $q->where('name', $selectedCategory);
                });
            }
        }

        // Filter by Search keyword
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('company_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter In Stock only
        if ($inStockOnly) {
            $query->having('stocks_sum_quantity', '>', 0);
        }

        // Sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
                $query->withSum('orderItems', 'quantity')
                      ->orderByDesc('order_items_sum_quantity')
                      ->latest('id');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $products = $query->paginate(20)->withQueryString();

        $currentCategory = ($selectedCategory && $selectedCategory !== 'all' && is_numeric($selectedCategory))
            ? Category::find($selectedCategory)
            : null;

        $pageTitle = $currentCategory
            ? $currentCategory->name
            : (!empty($search) ? '"' . $search . '"' : ($offersOnly ? 'Special Offers' : 'Shop'));

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
            $gridHtml = view('pages.shop.products_partial', compact('products', 'search', 'currentCategory', 'selectedCategory', 'inStockOnly', 'offersOnly', 'sort'))->render();
            $filtersHtml = view('pages.shop.filters_partial', compact('search', 'currentCategory', 'selectedCategory', 'inStockOnly', 'offersOnly', 'sort'))->render();
            return response()->json([
                'success'      => true,
                'grid_html'    => $gridHtml,
                'filters_html' => $filtersHtml,
                'total'        => $products->total(),
                'total_text'   => $products->total() . ' items',
                'title'        => $pageTitle,
            ]);
        }

        return view('pages.shop.index', compact(
            'products',
            'categories',
            'selectedCategory',
            'currentCategory',
            'search',
            'sort',
            'inStockOnly',
            'offersOnly',
            'totalActiveProducts'
        ));
    }
}
