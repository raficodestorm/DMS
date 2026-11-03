<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display Customer Ranking page.
     */
    public function ranking()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.admin.customer.ranking', compact('branches'));
    }

    /**
     * Fetch Customer Ranking data via AJAX based on last 30 days orders for wholesale customers.
     */
    public function fetchRankingData(Request $request)
    {
        $sinceDate = Carbon::now()->subDays(30);

        // 1. Calculate permanent Global Ranking for all wholesale customers (unaffected by filters)
        $globalRankedIds = Customer::where(function ($q) {
                $q->where('customer_type', 'wholesale')
                  ->orWhereNull('customer_type');
            })
            ->withSum(['orders as total_amount_30_days' => function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate)
                  ->where('status', '!=', 'rejected');
            }], 'net_total')
            ->withCount(['orders as total_orders_30_days' => function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate)
                  ->where('status', '!=', 'rejected');
            }])
            ->orderByRaw('COALESCE(total_amount_30_days, 0) DESC')
            ->orderByDesc('total_orders_30_days')
            ->orderBy('shop_name', 'asc')
            ->pluck('id')
            ->toArray();

        $rankMap = array_flip($globalRankedIds);

        // 2. Build filtered query for table display
        $query = Customer::with(['branch', 'sr'])
            ->where(function ($q) {
                $q->where('customer_type', 'wholesale')
                  ->orWhereNull('customer_type');
            })
            ->withSum(['orders as total_amount_30_days' => function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate)
                  ->where('status', '!=', 'rejected');
            }], 'net_total')
            ->withCount(['orders as total_orders_30_days' => function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate)
                  ->where('status', '!=', 'rejected');
            }]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            if (str_starts_with($search, 'BRC200')) {
                $id = str_replace('BRC200', '', $search);
                $query->where('id', $id);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('shop_name', 'LIKE', "%{$search}%")
                        ->orWhere('id', $search);
                });
            }
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $query->orderByRaw('COALESCE(total_amount_30_days, 0) DESC')
              ->orderByDesc('total_orders_30_days')
              ->orderBy('shop_name', 'asc');

        $limit = $request->input('limit', '10');
        if (strtolower($limit) === 'all') {
            $customers = $query->paginate(1000)->withQueryString();
        } else {
            $perPage = in_array((int) $limit, [10, 20, 30, 40, 50]) ? (int) $limit : 10;
            $customers = $query->paginate($perPage)->withQueryString();
        }

        // Attach fixed global rank to each paginated customer
        $customers->getCollection()->transform(function ($customer) use ($rankMap) {
            $customer->global_rank = isset($rankMap[$customer->id]) ? ($rankMap[$customer->id] + 1) : null;
            return $customer;
        });

        // Top Customer summary
        $topCustomer = Customer::where(function ($q) {
                $q->where('customer_type', 'wholesale')
                  ->orWhereNull('customer_type');
            })
            ->withSum(['orders as total_amount_30_days' => function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate)
                  ->where('status', '!=', 'rejected');
            }], 'net_total')
            ->orderByRaw('COALESCE(total_amount_30_days, 0) DESC')
            ->first();

        // Total 30-day wholesale sales
        $totalWholesaleSales = (float) Order::whereHas('customer', function ($q) {
                $q->where('customer_type', 'wholesale')
                  ->orWhereNull('customer_type');
            })
            ->where('created_at', '>=', $sinceDate)
            ->where('status', '!=', 'rejected')
            ->sum('net_total');

        // Total active ordering wholesale customers in last 30 days
        $activeCustomersCount = Customer::where(function ($q) {
                $q->where('customer_type', 'wholesale')
                  ->orWhereNull('customer_type');
            })
            ->whereHas('orders', function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate)
                  ->where('status', '!=', 'rejected');
            })
            ->count();

        return response()->json([
            'table'            => view('pages.admin.customer.ranking-table', compact('customers'))->render(),
            'mobile'           => view('pages.admin.customer.ranking-mtable', compact('customers'))->render(),
            'total'            => $customers->total(),
            'pagination'       => (string) $customers->links(),
            'top_customer'     => $topCustomer ? $topCustomer->shop_name : 'N/A',
            'top_amount'       => $topCustomer ? number_format($topCustomer->total_amount_30_days ?? 0, 2) : '0.00',
            'total_sales'      => number_format($totalWholesaleSales, 2),
            'active_customers' => $activeCustomersCount,
        ]);
    }

    /**
     * Get short ranking position info and 30-day stats for a wholesale customer (for popup modal).
     */
    public function getRankingPositionDetails(Request $request, Customer $customer)
    {
        $sinceDate = Carbon::now()->subDays(30);

        // Eager load relationships
        $customer->load(['branch', 'sr']);

        // 30-Day performance
        $orders30DaysQuery = $customer->orders()
            ->where('created_at', '>=', $sinceDate)
            ->where('status', '!=', 'rejected');

        $totalAmount30Days = (float) $orders30DaysQuery->sum('net_total');
        $totalOrders30Days = (int) $orders30DaysQuery->count();

        // Determine Rank position
        $rank = $request->input('rank');
        if (!$rank || !is_numeric($rank)) {
            $globalRankedIds = Customer::where(function ($q) {
                    $q->where('customer_type', 'wholesale')
                      ->orWhereNull('customer_type');
                })
                ->withSum(['orders as total_amount_30_days' => function ($q) use ($sinceDate) {
                    $q->where('created_at', '>=', $sinceDate)
                      ->where('status', '!=', 'rejected');
                }], 'net_total')
                ->withCount(['orders as total_orders_30_days' => function ($q) use ($sinceDate) {
                    $q->where('created_at', '>=', $sinceDate)
                      ->where('status', '!=', 'rejected');
                }])
                ->orderByRaw('COALESCE(total_amount_30_days, 0) DESC')
                ->orderByDesc('total_orders_30_days')
                ->orderBy('shop_name', 'asc')
                ->pluck('id')
                ->toArray();

            $rankMap = array_flip($globalRankedIds);
            $rank = isset($rankMap[$customer->id]) ? ($rankMap[$customer->id] + 1) : 1;
        }

        $html = view('pages.admin.customer.ranking-position-modal', compact(
            'customer',
            'rank',
            'totalAmount30Days',
            'totalOrders30Days',
            'sinceDate'
        ))->render();

        return response()->json([
            'success'   => true,
            'html'      => $html,
            'customer'  => [
                'id'        => $customer->id,
                'shop_name' => $customer->shop_name,
                'rank'      => $rank,
            ]
        ]);
    }

    /**
     * Load UI page only for Admin Customers (no heavy query on page load).
     */
    public function index()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.admin.customer.index', compact('branches'));
    }

    /**
     * Fetch Customer data via AJAX.
     */
    public function fetchCustomersIndexData(Request $request)
    {
        $query = Customer::with('branch')->orderBy('shop_name', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            if (str_starts_with($search, 'BRC200')) {
                $id = str_replace('BRC200', '', $search);
                $query->where('id', $id);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('shop_name', 'LIKE', "%{$search}%")
                        ->orWhere('id', $search);
                });
            }
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        $customers = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.customer.table', compact('customers'))->render(),
            'mobile'     => view('pages.admin.customer.mtable', compact('customers'))->render(),
            'total'      => $customers->total(),
            'pagination' => (string) $customers->links(),
        ]);
    }


    public function create()
    {
        $countriesData = [];
        $countriesPath = resource_path('data/countries.json');

        if (file_exists($countriesPath)) {
            $countriesData = json_decode(file_get_contents($countriesPath), true) ?: [];
        }

        $countries = array_keys($countriesData);
        $branches  = Branch::orderBy('name', 'asc')->get();
        // All SRs grouped — JS will filter by selected branch_id
        $allSrs = User::where('role', 'sr')->get(['id', 'fullname', 'username', 'branch_id']);
        return view('pages.admin.customer.create', compact('branches', 'countries', 'allSrs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:150',
            'manager'   => 'required|string|max:100',
            'phone'     => 'required|string|max:30',
            'country'   => 'required|string|max:100',
            'city'      => 'required|string|max:100',
            'address'   => 'required|string|max:255',
            'branch_id' => 'required|integer',
            'sr_id'     => 'nullable|exists:users,id',
        ]);
        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer added successfully!');
    }




    public function show(Customer $customer)
    {
        return view('pages.admin.customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $countriesData = [];
        $countriesPath = resource_path('data/countries.json');

        if (file_exists($countriesPath)) {
            $countriesData = json_decode(file_get_contents($countriesPath), true) ?: [];
        }

        $countries = array_keys($countriesData);
        $branches  = Branch::orderBy('name', 'asc')->get();
        $allSrs    = User::where('role', 'sr')->get(['id', 'fullname', 'username', 'branch_id']);
        return view('pages.admin.customer.edit', compact('customer', 'branches', 'countries', 'allSrs'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:150',
            'manager'   => 'required|string|max:100',
            'phone'     => 'required|string|max:30',
            'country'   => 'required|string|max:100',
            'city'      => 'required|string|max:100',
            'address'   => 'required|string|max:255',
            'branch_id' => 'nullable|integer',
            'due'       => 'nullable|numeric',
            'sr_id'     => 'nullable|exists:users,id',
        ]);
        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        // check if customer has user account
        if ($customer->users()->exists()) {
            return back()->with('error', 'This customer has user account. Please delete the user account first.');
        }

        $customer->delete();

        return redirect()->route('dashboards')
            ->with('success', 'Customer deleted successfully');
    }

    public function changeType(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_type' => 'required|string|max:100',
        ]);

        $oldType = $customer->customer_type;
        $customer->update([
            'customer_type' => $validated['customer_type']
        ]);

        return back()->with('success', 'Customer type changed from ' . ucfirst($oldType ?? 'N/A') . ' to ' . ucfirst($validated['customer_type']) . ' successfully.');
    }
}
