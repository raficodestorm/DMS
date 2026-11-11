<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\ProductReturn;
use App\Models\Branch;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockInRequest;
use App\Models\StockInItem;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\StockCut;
use App\Models\StockCutItem;
use App\Models\Stock;
use App\Models\Category;
use App\Models\BranchCost;
use App\Models\CompanyCost;
use App\Models\Bonus;
use App\Models\AdminCash;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Reports Overview Dashboard
     */
    public function index(Request $request)
    {
        return view('pages.admin.report.index');
    }

    /**
     * Executive Sales & Revenue Report (Multi-Branch, Filterable & A4 Printable/PDF).
     */
    public function sales(Request $request)
    {
        // 1. Direct Date Range Handling (From Date & To Date)
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default on initial page load: Current Month to Today
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        // 2. Query Builder for Sales Orders
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Status Filter (Default: exclude rejected orders)
        $status = $request->input('status', 'valid');
        if ($status === 'valid') {
            $query->where('status', '!=', 'rejected');
        } elseif ($status !== 'all' && !empty($status)) {
            $query->where('status', $status);
        }

        // Branch Filter
        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        // SR Filter
        if ($request->filled('sr_id') && $request->sr_id !== 'all') {
            $query->where('sr_id', $request->sr_id);
        }

        // Order Type Filter (Channel)
        if ($request->filled('order_type') && $request->order_type !== 'all') {
            $query->where('order_type', $request->order_type);
        }

        // Payment Status Filter
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        $statsQuery = clone $query;

        // 3. Overall KPI Financial Metrics
        $totalOrdersCount  = (clone $statsQuery)->count();
        $totalNetSales     = (float) (clone $statsQuery)->sum('net_total');
        $totalDiscountSum  = (float) (clone $statsQuery)->sum(DB::raw('COALESCE(discount_amount, 0) + COALESCE(special_discount, 0)'));
        $totalGrossSales   = $totalNetSales + $totalDiscountSum;
        $totalPaidSum      = (float) (clone $statsQuery)->sum('payment_amount');
        $totalDueSum       = max(0, $totalNetSales - $totalPaidSum);

        // Total items sold & Total profit from order_items
        $orderIds = (clone $statsQuery)->pluck('id');
        $itemStats = OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(quantity) as total_qty, SUM(profit) as total_profit')
            ->first();

        $totalItemsSold = (int) ($itemStats->total_qty ?? 0);
        $totalProfit    = (float) ($itemStats->total_profit ?? 0);

        // 4. Returns in this date period
        $returnQuery = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved');
        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $returnQuery->where('branch_id', $request->branch_id);
        }
        $totalReturnsAmount = (float) $returnQuery->sum('total_amount');
        $totalReturnsCount  = (int) $returnQuery->count();
        $netRealizedRevenue = max(0, $totalNetSales - $totalReturnsAmount);

        // 5. Channel Breakdown (Wholesale, Retail, Online)
        $channelBreakdown = (clone $statsQuery)
            ->select(
                'order_type',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(net_total) as total_sales'),
                DB::raw('SUM(payment_amount) as total_paid')
            )
            ->groupBy('order_type')
            ->get()
            ->map(function ($row) use ($totalNetSales) {
                $sales = (float) $row->total_sales;
                $row->due = max(0, $sales - (float) $row->total_paid);
                $row->share_percent = $totalNetSales > 0 ? round(($sales / $totalNetSales) * 100, 1) : 0;
                return $row;
            })
            ->keyBy('order_type');

        // 6. Branch Breakdown
        $branchBreakdown = (clone $statsQuery)
            ->select(
                'branch_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(net_total) as branch_sales'),
                DB::raw('SUM(payment_amount) as branch_paid')
            )
            ->groupBy('branch_id')
            ->with('branch')
            ->get()
            ->map(function ($row) use ($totalNetSales) {
                $sales = (float) $row->branch_sales;
                $row->share_percent = $totalNetSales > 0 ? round(($sales / $totalNetSales) * 100, 1) : 0;
                $row->branch_due = max(0, $sales - (float) $row->branch_paid);
                return $row;
            })
            ->sortByDesc('branch_sales');

        // 7. Payment Status Breakdown
        $paymentBreakdown = (clone $statsQuery)
            ->select('payment_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(net_total) as amount'))
            ->groupBy('payment_status')
            ->get()
            ->map(function ($row) use ($totalNetSales) {
                $amt = (float) $row->amount;
                $row->share_percent = $totalNetSales > 0 ? round(($amt / $totalNetSales) * 100, 1) : 0;
                return $row;
            })
            ->keyBy('payment_status');

        // 8. Top 3 Revenue-Generating Products
        $topProducts = OrderItem::whereIn('order_id', $orderIds)
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(net_total) as total_revenue')
            )
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_revenue')
            ->limit(3)
            ->get()
            ->map(function ($row) use ($totalNetSales) {
                $rev = (float) $row->total_revenue;
                $row->share_percent = $totalNetSales > 0 ? round(($rev / $totalNetSales) * 100, 1) : 0;
                return $row;
            });

        // Filter auxiliary data
        $branches = Branch::orderBy('name', 'asc')->get();
        $srs      = User::where('role', 'sr')->orderBy('fullname', 'asc')->get();

        return view('pages.admin.report.sales', compact(
            'branches',
            'srs',
            'startDate',
            'endDate',
            'status',
            'totalOrdersCount',
            'totalGrossSales',
            'totalDiscountSum',
            'totalNetSales',
            'totalPaidSum',
            'totalDueSum',
            'totalItemsSold',
            'totalProfit',
            'totalReturnsAmount',
            'totalReturnsCount',
            'netRealizedRevenue',
            'channelBreakdown',
            'branchBreakdown',
            'paymentBreakdown',
            'topProducts'
        ));
    }

    /**
     * Executive Stock-In / Procurement Report (Multi-Branch, Filterable & A4 Printable/PDF).
     */
    public function stockIn(Request $request)
    {
        // 1. Direct Date Range Handling (From Date & To Date)
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default on initial page load: Current Month to Today
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        // 2. Query Builder for Stock-In Requests
        $query = StockInRequest::whereBetween('created_at', [$startDate, $endDate]);

        // Status Filter (Default: valid = exclude rejected)
        $status = $request->input('status', 'valid');
        if ($status === 'valid') {
            $query->where('status', '!=', 'rejected');
        } elseif ($status !== 'all' && !empty($status)) {
            $query->where('status', $status);
        }

        // Branch Filter
        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        // Supplier Filter
        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $query->where('supplier_id', $request->supplier_id);
        }

        $statsQuery = clone $query;

        // 3. Overall KPI Financial & Procurement Metrics
        $totalRequestsCount    = (clone $statsQuery)->count();
        $totalProcurementValue = (float) (clone $statsQuery)->sum('net_total');

        $requestIds = (clone $statsQuery)->pluck('id');

        // Total units procured from stock_in_items
        $itemStats = StockInItem::whereIn('stock_in_request_id', $requestIds)
            ->selectRaw('SUM(quantity) as total_qty, COUNT(*) as total_items')
            ->first();

        $totalUnitsProcured = (int) ($itemStats->total_qty ?? 0);
        $totalUniqueItems   = (int) ($itemStats->total_items ?? 0);

        // 4. Supplier Payments in this date period (from supplier_transactions type = 'pay')
        $paymentQuery = SupplierTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('type', 'pay');
        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $paymentQuery->where('branch_id', $request->branch_id);
        }
        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $paymentQuery->where('supplier_id', $request->supplier_id);
        }
        $totalSupplierPaid = (float) $paymentQuery->sum('amount');

        // 5. Stock Cuts / Purchase Returns in this period (from stock_cuts)
        $stockCutQuery = StockCut::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved');
        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $stockCutQuery->where('branch_id', $request->branch_id);
        }
        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $stockCutQuery->where('supplier_id', $request->supplier_id);
        }
        $totalStockCutAmount    = (float) $stockCutQuery->sum('net_total');
        $totalStockCutCount     = (int) $stockCutQuery->count();
        $netRealizedProcurement = max(0, $totalProcurementValue - $totalStockCutAmount);

        // 6. Outstanding Supplier Due
        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $totalSupplierDue = (float) (Supplier::where('id', $request->supplier_id)->value('due') ?? 0);
        } else {
            $totalSupplierDue = (float) Supplier::sum('due');
        }

        // 7. Section 1: Supplier Procurement & Volume Breakdown
        $supplierUnits = StockInItem::join('stock_in_requests', 'stock_in_items.stock_in_request_id', '=', 'stock_in_requests.id')
            ->whereIn('stock_in_requests.id', $requestIds)
            ->select('stock_in_requests.supplier_id', DB::raw('SUM(stock_in_items.quantity) as units'))
            ->groupBy('stock_in_requests.supplier_id')
            ->pluck('units', 'supplier_id');

        $supplierBreakdown = (clone $statsQuery)
            ->select(
                'supplier_id',
                DB::raw('COUNT(*) as request_count'),
                DB::raw('SUM(net_total) as total_purchase')
            )
            ->whereNotNull('supplier_id')
            ->groupBy('supplier_id')
            ->with('supplier')
            ->get()
            ->map(function ($row) use ($totalProcurementValue, $supplierUnits) {
                $purchase = (float) $row->total_purchase;
                $row->share_percent  = $totalProcurementValue > 0 ? round(($purchase / $totalProcurementValue) * 100, 1) : 0;
                $row->units_procured = (int) ($supplierUnits[$row->supplier_id] ?? 0);
                $row->supplier_due   = (float) ($row->supplier->due ?? 0);
                return $row;
            })
            ->sortByDesc('total_purchase');

        // 8. Section 2: Branch-Wise Stock-In & Inventory Additions
        $branchUnits = StockInItem::join('stock_in_requests', 'stock_in_items.stock_in_request_id', '=', 'stock_in_requests.id')
            ->whereIn('stock_in_requests.id', $requestIds)
            ->select('stock_in_requests.branch_id', DB::raw('SUM(stock_in_items.quantity) as units'))
            ->groupBy('stock_in_requests.branch_id')
            ->pluck('units', 'branch_id');

        $branchBreakdown = (clone $statsQuery)
            ->select(
                'branch_id',
                DB::raw('COUNT(*) as request_count'),
                DB::raw('SUM(net_total) as branch_purchase')
            )
            ->groupBy('branch_id')
            ->with('branch')
            ->get()
            ->map(function ($row) use ($totalProcurementValue, $branchUnits) {
                $val = (float) $row->branch_purchase;
                $row->share_percent = $totalProcurementValue > 0 ? round(($val / $totalProcurementValue) * 100, 1) : 0;
                $row->units_received = (int) ($branchUnits[$row->branch_id] ?? 0);
                return $row;
            })
            ->sortByDesc('branch_purchase');

        // 9. Section 3: Top 3 Procured Products (By Spend / Value)
        $topProducts = StockInItem::whereIn('stock_in_request_id', $requestIds)
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_cost')
            )
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_cost')
            ->limit(3)
            ->get()
            ->map(function ($row) use ($totalProcurementValue) {
                $cost = (float) $row->total_cost;
                $row->share_percent = $totalProcurementValue > 0 ? round(($cost / $totalProcurementValue) * 100, 1) : 0;
                return $row;
            });

        // 10. Section 4: Procurement Health & Status Breakdown
        $statusBreakdown = (clone $statsQuery)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(net_total) as amount'))
            ->groupBy('status')
            ->get()
            ->map(function ($row) use ($totalProcurementValue) {
                $amt = (float) $row->amount;
                $row->share_percent = $totalProcurementValue > 0 ? round(($amt / $totalProcurementValue) * 100, 1) : 0;
                return $row;
            })
            ->keyBy('status');

        // Filter auxiliary data
        $branches  = Branch::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('company_name', 'asc')->get();

        return view('pages.admin.report.stock-in', compact(
            'branches',
            'suppliers',
            'startDate',
            'endDate',
            'status',
            'totalRequestsCount',
            'totalProcurementValue',
            'totalUnitsProcured',
            'totalUniqueItems',
            'totalSupplierPaid',
            'totalStockCutAmount',
            'totalStockCutCount',
            'netRealizedProcurement',
            'totalSupplierDue',
            'supplierBreakdown',
            'branchBreakdown',
            'topProducts',
            'statusBreakdown'
        ));
    }

    /**
     * Executive Inventory & Valuation Report (Multi-Branch, Filterable & A4 Printable/PDF).
     */
    public function inventory(Request $request)
    {
        // 1. Build Base Query for Stocks joined with Products
        $query = Stock::query()
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->leftJoin('branches', 'stocks.branch_id', '=', 'branches.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id');

        // Branch Filter
        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $query->where('stocks.branch_id', $request->branch_id);
        }

        // Category Filter
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('products.category_id', $request->category_id);
        }

        // Supplier Filter
        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $query->where('products.supplier_id', $request->supplier_id);
        }

        // Stock Health / Status Filter
        $stockStatus = $request->input('stock_status', 'all');
        if ($stockStatus === 'available') {
            $query->whereColumn('stocks.quantity', '>', 'products.stock_alert');
        } elseif ($stockStatus === 'low_stock') {
            $query->where('stocks.quantity', '>', 0)
                  ->whereColumn('stocks.quantity', '<=', 'products.stock_alert');
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('stocks.quantity', '<=', 0);
        }

        $statsQuery = clone $query;

        // 2. Core KPI Valuation & Quantity Metrics
        $totalUnitsOnHand     = (int) (clone $statsQuery)->sum('stocks.quantity');
        $totalCostValuation   = (float) (clone $statsQuery)->sum(DB::raw('stocks.quantity * COALESCE(products.purchase_price, 0)'));
        $totalRetailValuation = (float) (clone $statsQuery)->sum(DB::raw('stocks.quantity * products.price'));
        $potentialGrossProfit = max(0, $totalRetailValuation - $totalCostValuation);
        $totalActiveSkus      = (int) (clone $statsQuery)->distinct('stocks.product_id')->count('stocks.product_id');

        // 3. Stock Health Categories Calculation (Contextual to selected Branch/Category/Supplier)
        $unfilteredHealthQuery = Stock::query()
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->leftJoin('branches', 'stocks.branch_id', '=', 'branches.id');

        if ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $unfilteredHealthQuery->where('stocks.branch_id', $request->branch_id);
        }
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $unfilteredHealthQuery->where('products.category_id', $request->category_id);
        }
        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $unfilteredHealthQuery->where('products.supplier_id', $request->supplier_id);
        }

        $healthyStats = (clone $unfilteredHealthQuery)
            ->whereColumn('stocks.quantity', '>', 'products.stock_alert')
            ->selectRaw('COUNT(DISTINCT stocks.product_id) as sku_count, SUM(stocks.quantity) as units, SUM(stocks.quantity * COALESCE(products.purchase_price, 0)) as cost_val')
            ->first();
        $healthyItemsCount = (int) ($healthyStats->sku_count ?? 0);
        $healthyUnits      = (int) ($healthyStats->units ?? 0);
        $healthyCostVal    = (float) ($healthyStats->cost_val ?? 0);

        $lowStockStats = (clone $unfilteredHealthQuery)
            ->where('stocks.quantity', '>', 0)
            ->whereColumn('stocks.quantity', '<=', 'products.stock_alert')
            ->selectRaw('COUNT(DISTINCT stocks.product_id) as sku_count, SUM(stocks.quantity) as units, SUM(stocks.quantity * COALESCE(products.purchase_price, 0)) as cost_val')
            ->first();
        $lowStockCount         = (int) ($lowStockStats->sku_count ?? 0);
        $lowStockUnits         = (int) ($lowStockStats->units ?? 0);
        $lowStockCostValuation = (float) ($lowStockStats->cost_val ?? 0);

        $outOfStockStats = (clone $unfilteredHealthQuery)
            ->where('stocks.quantity', '<=', 0)
            ->selectRaw('COUNT(DISTINCT stocks.product_id) as sku_count, SUM(stocks.quantity) as units')
            ->first();
        $outOfStockCount = (int) ($outOfStockStats->sku_count ?? 0);

        // 4. Section 1: Branch-Wise Inventory Holding & Valuation
        $branchHoldings = (clone $statsQuery)
            ->select(
                'stocks.branch_id',
                DB::raw('COUNT(DISTINCT stocks.product_id) as active_skus'),
                DB::raw('SUM(stocks.quantity) as total_units'),
                DB::raw('SUM(stocks.quantity * COALESCE(products.purchase_price, 0)) as cost_value'),
                DB::raw('SUM(stocks.quantity * products.price) as retail_value')
            )
            ->groupBy('stocks.branch_id')
            ->with('branch')
            ->get()
            ->map(function ($row) use ($totalCostValuation) {
                $cost = (float) $row->cost_value;
                $row->share_percent = $totalCostValuation > 0 ? round(($cost / $totalCostValuation) * 100, 1) : 0;
                return $row;
            })
            ->sortByDesc('cost_value');

        // 5. Section 2: Category-Wise Inventory Concentration
        $categoryHoldings = (clone $statsQuery)
            ->select(
                'products.category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT stocks.product_id) as active_skus'),
                DB::raw('SUM(stocks.quantity) as total_units'),
                DB::raw('SUM(stocks.quantity * COALESCE(products.purchase_price, 0)) as cost_value'),
                DB::raw('SUM(stocks.quantity * products.price) as retail_value')
            )
            ->groupBy('products.category_id', 'categories.name')
            ->get()
            ->map(function ($row) use ($totalCostValuation) {
                $cost = (float) $row->cost_value;
                $row->share_percent = $totalCostValuation > 0 ? round(($cost / $totalCostValuation) * 100, 1) : 0;
                return $row;
            })
            ->sortByDesc('cost_value');

        // 6. Section 3: Top 3 Highest Value Inventory Products (Capital Concentration)
        $topStockProducts = (clone $statsQuery)
            ->select(
                'stocks.product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'products.purchase_price as unit_cost',
                'products.price as unit_price',
                DB::raw('SUM(stocks.quantity) as total_units'),
                DB::raw('SUM(stocks.quantity * COALESCE(products.purchase_price, 0)) as total_cost_value')
            )
            ->groupBy('stocks.product_id', 'products.name', 'products.sku', 'products.purchase_price', 'products.price')
            ->orderByDesc('total_cost_value')
            ->limit(3)
            ->get()
            ->map(function ($row) use ($totalCostValuation) {
                $cost = (float) $row->total_cost_value;
                $row->share_percent = $totalCostValuation > 0 ? round(($cost / $totalCostValuation) * 100, 1) : 0;
                return $row;
            });

        // Auxiliary data for filter dropdowns
        $branches   = Branch::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $suppliers  = Supplier::orderBy('company_name', 'asc')->get();

        return view('pages.admin.report.inventory', compact(
            'branches',
            'categories',
            'suppliers',
            'stockStatus',
            'totalUnitsOnHand',
            'totalCostValuation',
            'totalRetailValuation',
            'potentialGrossProfit',
            'totalActiveSkus',
            'healthyItemsCount',
            'healthyUnits',
            'healthyCostVal',
            'lowStockCount',
            'lowStockUnits',
            'lowStockCostValuation',
            'outOfStockCount',
            'branchHoldings',
            'categoryHoldings',
            'topStockProducts'
        ));
    }

    /**
     * SR Performance — Leaderboard (ranked list of all SRs).
     */
    public function srPerformance(Request $request)
    {
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        // All active SRs with their branch
        $allSrs = User::where('role', 'sr')
            ->with('branch')
            ->orderBy('fullname')
            ->get();

        // ── Order-level aggregation per SR ──────────────────────────────
        $srOrderStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->whereNotNull('sr_id')
            ->select(
                'sr_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(net_total) as net_sales'),
                DB::raw('SUM(COALESCE(payment_amount,0)) as cash_collected'),
                DB::raw('SUM(COALESCE(special_discount,0)+COALESCE(discount_amount,0)) as total_discounts'),
                DB::raw('COUNT(DISTINCT customer_id) as unique_customers')
            )
            ->groupBy('sr_id')
            ->get()
            ->keyBy('sr_id');

        // ── Item-level profit per SR ─────────────────────────────────────
        $srItemStats = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'rejected')
            ->whereNotNull('orders.sr_id')
            ->select(
                'orders.sr_id',
                DB::raw('SUM(order_items.profit) as gross_profit'),
                DB::raw('SUM(order_items.quantity) as units_sold')
            )
            ->groupBy('orders.sr_id')
            ->get()
            ->keyBy('sr_id');

        // ── Returns per SR ───────────────────────────────────────────────
        $srReturns = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->whereNotNull('sr_id')
            ->select(
                'sr_id',
                DB::raw('SUM(total_amount) as return_amount'),
                DB::raw('COUNT(*) as return_count')
            )
            ->groupBy('sr_id')
            ->get()
            ->keyBy('sr_id');

        // ── Registered customers under each SR ─────────────────────────
        $srCustomerCounts = Customer::whereNotNull('sr_id')
            ->select('sr_id', DB::raw('COUNT(*) as total_customers'))
            ->groupBy('sr_id')
            ->get()
            ->keyBy('sr_id');

        // ── Build SR performance rows ────────────────────────────────────
        $srList = $allSrs->map(function ($sr) use (
            $srOrderStats, $srItemStats, $srReturns, $srCustomerCounts
        ) {
            $id = $sr->id;

            $oStat = $srOrderStats[$id]    ?? null;
            $iStat = $srItemStats[$id]     ?? null;
            $rStat = $srReturns[$id]       ?? null;
            $cStat = $srCustomerCounts[$id] ?? null;

            $orderCount      = (int)   ($oStat->order_count      ?? 0);
            $netSales        = (float) ($oStat->net_sales         ?? 0);
            $cashCollected   = (float) ($oStat->cash_collected    ?? 0);
            $totalDiscounts  = (float) ($oStat->total_discounts   ?? 0);
            $activeCustomers = (int)   ($oStat->unique_customers  ?? 0);
            $unitsSold       = (int)   ($iStat->units_sold        ?? 0);
            $grossProfit     = (float) ($iStat->gross_profit      ?? 0);
            $returnAmount    = (float) ($rStat->return_amount     ?? 0);
            $returnCount     = (int)   ($rStat->return_count      ?? 0);
            $totalCustomers  = (int)   ($cStat->total_customers   ?? 0);

            $netRevenue      = max(0, $netSales - $returnAmount);
            $customerDue     = max(0, $netSales - $cashCollected - $returnAmount);
            $netProfit       = max(0, $grossProfit);
            $grossMarginPct  = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0;
            $collectionPct   = $netSales   > 0 ? round(($cashCollected / $netSales) * 100, 1) : 0;
            $avgOrderValue   = $orderCount > 0 ? round($netRevenue / $orderCount, 2) : 0;

            return (object) [
                'sr'              => $sr,
                'order_count'     => $orderCount,
                'units_sold'      => $unitsSold,
                'net_sales'       => $netSales,
                'total_discounts' => $totalDiscounts,
                'return_amount'   => $returnAmount,
                'return_count'    => $returnCount,
                'net_revenue'     => $netRevenue,
                'cash_collected'  => $cashCollected,
                'customer_due'    => $customerDue,
                'gross_profit'    => $netProfit,
                'gross_margin_pct'=> $grossMarginPct,
                'collection_pct'  => $collectionPct,
                'avg_order_value' => $avgOrderValue,
                'active_customers'=> $activeCustomers,
                'total_customers' => $totalCustomers,
            ];
        })
        ->sortByDesc('order_count')
        ->values();

        // Company-wide totals
        $totalOrders    = $srList->sum('order_count');
        $totalRevenue   = $srList->sum('net_revenue');
        $totalProfit    = $srList->sum('gross_profit');
        $totalCollected = $srList->sum('cash_collected');
        $totalDue       = $srList->sum('customer_due');

        return view('pages.admin.report.sr-performance', compact(
            'startDate',
            'endDate',
            'srList',
            'totalOrders',
            'totalRevenue',
            'totalProfit',
            'totalCollected',
            'totalDue'
        ));
    }

    /**
     * SR Performance — Individual SR Detail Report.
     */
    public function srPerformanceDetail(Request $request, $srId)
    {
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } else {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        // Load SR
        $sr = User::where('role', 'sr')->with('branch')->findOrFail($srId);

        // ── Orders ──────────────────────────────────────────────────────
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->where('sr_id', $srId)
            ->with(['customer', 'branch'])
            ->orderByDesc('created_at')
            ->get();

        $orderIds = $orders->pluck('id');

        // ── Aggregations ────────────────────────────────────────────────
        $orderCount     = $orders->count();
        $netSales       = (float) $orders->sum('net_total');
        $cashCollected  = (float) $orders->sum('payment_amount');
        $totalDiscounts = (float) $orders->sum(fn($o) => ($o->discount_amount ?? 0) + ($o->special_discount ?? 0));
        $activeCustomers= $orders->pluck('customer_id')->filter()->unique()->count();

        $itemStats = OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(quantity) as total_qty, SUM(profit) as total_profit, SUM(net_total) as total_item_net')
            ->first();

        $unitsSold   = (int)   ($itemStats->total_qty    ?? 0);
        $grossProfit = (float) ($itemStats->total_profit ?? 0);

        // ── Returns ─────────────────────────────────────────────────────
        $returns = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->where('sr_id', $srId)
            ->get();

        $returnAmount = (float) $returns->sum('total_amount');
        $returnCount  = $returns->count();

        // ── Net metrics ─────────────────────────────────────────────────
        $netRevenue     = max(0, $netSales - $returnAmount);
        $customerDue    = max(0, $netSales - $cashCollected - $returnAmount);
        $netProfit      = max(0, $grossProfit);
        $grossMarginPct = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0;
        $collectionPct  = $netSales   > 0 ? round(($cashCollected / $netSales) * 100, 1)  : 0;
        $avgOrderValue  = $orderCount > 0 ? round($netRevenue / $orderCount, 2) : 0;

        // ── All registered customers under this SR ──────────────────────
        $allCustomers = Customer::where('sr_id', $srId)
            ->orderBy('shop_name')
            ->get();

        // ── Customer-wise revenue breakdown (from orders) ───────────────
        $customerBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->where('sr_id', $srId)
            ->whereNotNull('customer_id')
            ->select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(net_total) as net_sales'),
                DB::raw('SUM(COALESCE(payment_amount,0)) as cash_collected')
            )
            ->groupBy('customer_id')
            ->with('customer')
            ->orderByDesc('net_sales')
            ->get();

        // ── Monthly/daily order trend (order count per day) ─────────────
        $orderTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->where('sr_id', $srId)
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(net_total) as daily_sales')
            )
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get();

        // ── Top products sold by this SR ────────────────────────────────
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'rejected')
            ->where('orders.sr_id', $srId)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.net_total) as total_sales'),
                DB::raw('SUM(order_items.profit) as total_profit')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // ── Status breakdown of orders ───────────────────────────────────
        $statusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('sr_id', $srId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);

        return view('pages.admin.report.sr-performance-detail', compact(
            'sr',
            'startDate',
            'endDate',
            'periodDays',
            'orderCount',
            'unitsSold',
            'netSales',
            'totalDiscounts',
            'returnAmount',
            'returnCount',
            'netRevenue',
            'cashCollected',
            'customerDue',
            'grossProfit',
            'netProfit',
            'grossMarginPct',
            'collectionPct',
            'avgOrderValue',
            'activeCustomers',
            'allCustomers',
            'orders',
            'customerBreakdown',
            'orderTrend',
            'topProducts',
            'statusBreakdown'
        ));
    }

    /**
     * DSO Performance — Leaderboard (ranked list of all Delivery Sales Officers).
     */
    public function dsoPerformance(Request $request)
    {
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        // All active DSOs with branch
        $allDsos = User::where('role', 'dso')
            ->with('branch')
            ->orderBy('fullname')
            ->get();

        // Delivery aggregations per DSO
        $dsoOrderStats = Order::whereNotNull('delivered_by')
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('delivered_at', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->whereNull('delivered_at')
                          ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->select(
                'delivered_by',
                DB::raw("COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count"),
                DB::raw("COUNT(*) as total_assigned_count"),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN net_total ELSE 0 END) as delivered_amount"),
                DB::raw("SUM(COALESCE(payment_amount, 0)) as cash_collected"),
                DB::raw("COUNT(DISTINCT customer_id) as unique_customers")
            )
            ->groupBy('delivered_by')
            ->get()
            ->keyBy('delivered_by');

        // Delivered order items count
        $deliveredOrderIds = Order::whereNotNull('delivered_by')
            ->where('status', 'delivered')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('delivered_at', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->whereNull('delivered_at')
                          ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->select('id', 'delivered_by')
            ->get();

        $dsoUnitsDelivered = collect();
        if ($deliveredOrderIds->isNotEmpty()) {
            $dsoUnitsDelivered = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereIn('orders.id', $deliveredOrderIds->pluck('id'))
                ->select('orders.delivered_by', DB::raw('SUM(order_items.quantity) as units_delivered'))
                ->groupBy('orders.delivered_by')
                ->pluck('units_delivered', 'orders.delivered_by');
        }

        // Build DSO performance rows
        $dsoList = $allDsos->map(function ($dso) use ($dsoOrderStats, $dsoUnitsDelivered) {
            $id = $dso->id;
            $stat = $dsoOrderStats[$id] ?? null;

            $deliveredCount   = (int)   ($stat->delivered_count      ?? 0);
            $totalAssigned    = (int)   ($stat->total_assigned_count ?? 0);
            $deliveredAmount  = (float) ($stat->delivered_amount     ?? 0);
            $cashCollected    = (float) ($stat->cash_collected       ?? 0);
            $uniqueCustomers  = (int)   ($stat->unique_customers     ?? 0);
            $unitsDelivered   = (int)   ($dsoUnitsDelivered[$id]     ?? 0);

            $pendingDelivery  = max(0, $totalAssigned - $deliveredCount);
            $pendingDue       = max(0, $deliveredAmount - $cashCollected);
            $collectionPct    = $deliveredAmount > 0 ? round(($cashCollected / $deliveredAmount) * 100, 1) : 0;
            $avgDeliveryVal   = $deliveredCount > 0 ? round($deliveredAmount / $deliveredCount, 2) : 0;

            return (object) [
                'dso'               => $dso,
                'delivered_count'   => $deliveredCount,
                'total_assigned'    => $totalAssigned,
                'pending_delivery'  => $pendingDelivery,
                'delivered_amount'  => $deliveredAmount,
                'cash_collected'    => $cashCollected,
                'pending_due'       => $pendingDue,
                'collection_pct'    => $collectionPct,
                'unique_customers'  => $uniqueCustomers,
                'units_delivered'   => $unitsDelivered,
                'avg_delivery_val'  => $avgDeliveryVal,
            ];
        })
        ->sortByDesc('delivered_count')
        ->values();

        // Company-wide summary metrics
        $totalDeliveries     = $dsoList->sum('delivered_count');
        $totalDeliveredValue = $dsoList->sum('delivered_amount');
        $totalCollected      = $dsoList->sum('cash_collected');
        $totalUnitsDelivered = $dsoList->sum('units_delivered');

        return view('pages.admin.report.dso-performance', compact(
            'startDate',
            'endDate',
            'dsoList',
            'totalDeliveries',
            'totalDeliveredValue',
            'totalCollected',
            'totalUnitsDelivered'
        ));
    }

    /**
     * DSO Performance — Individual DSO Detail Report.
     */
    public function dsoPerformanceDetail(Request $request, $dsoId)
    {
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        // Load DSO
        $dso = User::where('role', 'dso')->with('branch')->findOrFail($dsoId);

        // Fetch delivered orders for this DSO
        $orders = Order::where('delivered_by', $dsoId)
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('delivered_at', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->whereNull('delivered_at')
                          ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->with(['customer', 'branch', 'sr'])
            ->orderByDesc('delivered_at')
            ->get();

        $deliveredOrders = $orders->where('status', 'delivered');
        $deliveredCount  = $deliveredOrders->count();
        $deliveredAmount = (float) $deliveredOrders->sum('net_total');
        $cashCollected   = (float) $orders->sum('payment_amount');
        $pendingDue      = max(0, $deliveredAmount - $cashCollected);
        $uniqueCustomers = $orders->pluck('customer_id')->filter()->unique()->count();
        $avgDeliveryVal  = $deliveredCount > 0 ? round($deliveredAmount / $deliveredCount, 2) : 0;
        $collectionPct   = $deliveredAmount > 0 ? round(($cashCollected / $deliveredAmount) * 100, 1) : 0;

        // Items delivered count
        $orderIds = $deliveredOrders->pluck('id');
        $unitsDelivered = (int) OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

        // Customer Delivery Breakdown (Top customers delivered to)
        $customerBreakdown = $orders->groupBy('customer_id')->map(function ($custOrders, $custId) {
            $first = $custOrders->first();
            $delCount = $custOrders->where('status', 'delivered')->count();
            $totalNet = (float) $custOrders->sum('net_total');
            $paidAmt  = (float) $custOrders->sum('payment_amount');
            return (object) [
                'customer'         => $first->customer,
                'customer_name'    => $first->customer->shop_name ?? $first->customer_name ?? 'Walk-in / Guest',
                'delivery_count'   => $delCount,
                'total_orders'     => $custOrders->count(),
                'total_amount'     => $totalNet,
                'cash_collected'   => $paidAmt,
                'due_amount'       => max(0, $totalNet - $paidAmt),
            ];
        })->sortByDesc('delivery_count')->values()->take(10);

        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);

        return view('pages.admin.report.dso-performance-detail', compact(
            'dso',
            'startDate',
            'endDate',
            'periodDays',
            'deliveredCount',
            'deliveredAmount',
            'cashCollected',
            'pendingDue',
            'uniqueCustomers',
            'unitsDelivered',
            'avgDeliveryVal',
            'collectionPct',
            'orders',
            'customerBreakdown'
        ));
    }

    /**
     * Branch Performance Comparison Report (Multi-Branch Side-by-Side Analytics).
     */
    public function branchPerformance(Request $request)
    {
        // 1. Date Range
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        $startDateStr = $startDate->toDateString();
        $endDateStr   = $endDate->toDateString();

        // 2. All Branches
        $allBranches = Branch::orderBy('name', 'asc')->get();

        // 2a. Order-level aggregations per branch
        $branchOrderStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->select(
                'branch_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(net_total) as net_sales'),
                DB::raw('SUM(COALESCE(payment_amount, 0)) as cash_collected'),
                DB::raw('SUM(COALESCE(special_discount, 0)) as special_discount'),
                DB::raw('SUM(COALESCE(discount_amount, 0) + COALESCE(special_discount, 0)) as total_discounts')
            )
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        // 2b. Item-level profit aggregation per branch
        $branchItemProfits = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'rejected')
            ->select(
                'orders.branch_id',
                DB::raw('SUM(order_items.profit) as gross_profit'),
                DB::raw('SUM(order_items.quantity) as units_sold')
            )
            ->groupBy('orders.branch_id')
            ->get()
            ->keyBy('branch_id');

        // 2c. Returns per branch
        $branchReturns = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->select('branch_id', DB::raw('SUM(total_amount) as return_amount'), DB::raw('COUNT(*) as return_count'))
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        // 2d. Branch costs (OPEX) per branch
        $branchCostStats = BranchCost::whereBetween('cost_date', [$startDateStr, $endDateStr])
            ->select('branch_id', DB::raw('SUM(amount) as opex'))
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        // 2e. Current stock value per branch
        $branchStockValues = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->select(
                'stocks.branch_id',
                DB::raw('COUNT(DISTINCT stocks.product_id) as sku_count'),
                DB::raw('SUM(stocks.quantity) as total_units'),
                DB::raw('SUM(stocks.quantity * COALESCE(products.purchase_price, 0)) as stock_cost_value'),
                DB::raw('SUM(stocks.quantity * products.price) as stock_retail_value')
            )
            ->groupBy('stocks.branch_id')
            ->get()
            ->keyBy('branch_id');

        // 2f. SR count per branch
        $branchSrCounts = User::where('role', 'sr')
            ->whereNotNull('branch_id')
            ->select('branch_id', DB::raw('COUNT(*) as sr_count'))
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        // 2g. Customer count per branch (unique customers in orders this period)
        $branchCustomerCounts = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->whereNotNull('customer_id')
            ->select('branch_id', DB::raw('COUNT(DISTINCT customer_id) as customer_count'))
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        // 3. Build branch performance rows
        $branchPerformance = $allBranches->map(function ($branch) use (
            $branchOrderStats, $branchItemProfits, $branchReturns,
            $branchCostStats, $branchStockValues, $branchSrCounts, $branchCustomerCounts
        ) {
            $bId = $branch->id;

            $orderStat   = $branchOrderStats[$bId]  ?? null;
            $itemStat    = $branchItemProfits[$bId]  ?? null;
            $returnStat  = $branchReturns[$bId]      ?? null;
            $costStat    = $branchCostStats[$bId]    ?? null;
            $stockStat   = $branchStockValues[$bId]  ?? null;
            $srStat      = $branchSrCounts[$bId]     ?? null;
            $custStat    = $branchCustomerCounts[$bId] ?? null;

            $orderCount       = (int)   ($orderStat->order_count      ?? 0);
            $grossNetSales    = (float) ($orderStat->net_sales         ?? 0);
            $cashCollected    = (float) ($orderStat->cash_collected    ?? 0);
            $totalDiscounts   = (float) ($orderStat->total_discounts   ?? 0);
            $specialDiscount  = (float) ($orderStat->special_discount  ?? 0);
            $unitsSold        = (int)   ($itemStat->units_sold         ?? 0);
            $grossItemProfit  = (float) ($itemStat->gross_profit       ?? 0);
            $returnAmount     = (float) ($returnStat->return_amount    ?? 0);
            $returnCount      = (int)   ($returnStat->return_count     ?? 0);
            $opex             = (float) ($costStat->opex               ?? 0);
            $skuCount         = (int)   ($stockStat->sku_count         ?? 0);
            $stockUnits       = (int)   ($stockStat->total_units       ?? 0);
            $stockCostValue   = (float) ($stockStat->stock_cost_value  ?? 0);
            $stockRetailValue = (float) ($stockStat->stock_retail_value ?? 0);
            $srCount          = (int)   ($srStat->sr_count             ?? 0);
            $customerCount    = (int)   ($custStat->customer_count     ?? 0);

            // Net Realized Revenue (after returns)
            $netRealizedRevenue = max(0, $grossNetSales - $returnAmount);

            // Net COGS & Gross Profit (order_items.profit already has all discounts deducted)
            $grossProfit        = max(0, $grossItemProfit);
            $realizedGrossProfit = $grossNetSales > 0
                ? max(0, $grossProfit * ($netRealizedRevenue / $grossNetSales))
                : 0;
            $cogs               = max(0, $netRealizedRevenue - $realizedGrossProfit);

            // Net Branch Contribution (after OPEX)
            $netContribution    = $realizedGrossProfit - $opex;

            // Remaining AR (not yet collected)
            $customerDue        = max(0, $grossNetSales - $cashCollected - $returnAmount);

            // Gross Margin %
            $grossMarginPct     = $netRealizedRevenue > 0
                ? round(($realizedGrossProfit / $netRealizedRevenue) * 100, 1)
                : 0;
            $netMarginPct       = $netRealizedRevenue > 0
                ? round(($netContribution / $netRealizedRevenue) * 100, 1)
                : 0;

            // Avg order value
            $avgOrderValue      = $orderCount > 0 ? round($netRealizedRevenue / $orderCount, 2) : 0;

            return (object) [
                'branch'              => $branch,
                'order_count'         => $orderCount,
                'units_sold'          => $unitsSold,
                'gross_net_sales'     => $grossNetSales,
                'total_discounts'     => $totalDiscounts,
                'return_amount'       => $returnAmount,
                'return_count'        => $returnCount,
                'net_realized_revenue'=> $netRealizedRevenue,
                'cogs'                => $cogs,
                'realized_gross_profit'=> $realizedGrossProfit,
                'gross_margin_pct'    => $grossMarginPct,
                'opex'                => $opex,
                'net_contribution'    => $netContribution,
                'net_margin_pct'      => $netMarginPct,
                'cash_collected'      => $cashCollected,
                'customer_due'        => $customerDue,
                'sku_count'           => $skuCount,
                'stock_units'         => $stockUnits,
                'stock_cost_value'    => $stockCostValue,
                'stock_retail_value'  => $stockRetailValue,
                'sr_count'            => $srCount,
                'customer_count'      => $customerCount,
                'avg_order_value'     => $avgOrderValue,
                'branch_cash'         => (float) $branch->cash,
            ];
        })->sortByDesc('net_realized_revenue')->values();

        // 4. Company-wide Totals (sum of all branches)
        $companyRevenue       = $branchPerformance->sum('net_realized_revenue');
        $companyCogs          = $branchPerformance->sum('cogs');
        $companyGrossProfit   = $branchPerformance->sum('realized_gross_profit');
        $companyOpex          = $branchPerformance->sum('opex');
        $companyNetProfit     = $branchPerformance->sum('net_contribution');
        $companyOrders        = $branchPerformance->sum('order_count');
        $companyUnits         = $branchPerformance->sum('units_sold');
        $companyDiscounts     = $branchPerformance->sum('total_discounts');
        $companyReturns       = $branchPerformance->sum('return_amount');
        $companyCash          = $branchPerformance->sum('branch_cash');
        $companyCustomerDue   = $branchPerformance->sum('customer_due');
        $companyStockCostVal  = $branchPerformance->sum('stock_cost_value');

        $companyGrossMarginPct = $companyRevenue > 0
            ? round(($companyGrossProfit / $companyRevenue) * 100, 1)
            : 0;
        $companyNetMarginPct   = $companyRevenue > 0
            ? round(($companyNetProfit / $companyRevenue) * 100, 1)
            : 0;

        // Add revenue-share % to each branch row
        $branchPerformance = $branchPerformance->map(function ($row) use ($companyRevenue, $companyGrossProfit) {
            $row->revenue_share_pct = $companyRevenue > 0
                ? round(($row->net_realized_revenue / $companyRevenue) * 100, 1)
                : 0;
            $row->profit_share_pct  = $companyGrossProfit > 0
                ? round(($row->realized_gross_profit / $companyGrossProfit) * 100, 1)
                : 0;
            return $row;
        });

        // 5. Top branch by different metrics
        $topByRevenue = $branchPerformance->first();
        $topByProfit  = $branchPerformance->sortByDesc('realized_gross_profit')->first();
        $topByOrders  = $branchPerformance->sortByDesc('order_count')->first();

        // 6. Monthly trend for the period: orders per branch per day (for sparkline)
        // We just pass the period days count for display
        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);

        return view('pages.admin.report.branch-performance', compact(
            'startDate',
            'endDate',
            'branchPerformance',
            'companyRevenue',
            'companyCogs',
            'companyGrossProfit',
            'companyGrossMarginPct',
            'companyOpex',
            'companyNetProfit',
            'companyNetMarginPct',
            'companyOrders',
            'companyUnits',
            'companyDiscounts',
            'companyReturns',
            'companyCash',
            'companyCustomerDue',
            'companyStockCostVal',
            'topByRevenue',
            'topByProfit',
            'topByOrders',
            'periodDays'
        ));
    }

    /**
     * Executive Financial Report (Profit & Loss / Income Statement & Cash Flow).
     */
    public function financial(Request $request)
    {
        // 1. Direct Date Range Handling (From Date & To Date)
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($request->filled('end_date')) {
            $startDate = Carbon::parse($request->end_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default on initial page load: Current Month to Today
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        $startDateStr = $startDate->toDateString();
        $endDateStr   = $endDate->toDateString();
        $branchId     = ($request->filled('branch_id') && $request->branch_id !== 'all') ? $request->branch_id : null;

        // 2. Revenue & Sales Queries
        $orderQuery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected');

        if ($branchId) {
            $orderQuery->where('branch_id', $branchId);
        }

        $totalOrdersCount = (clone $orderQuery)->count();
        $totalNetSales    = (float) (clone $orderQuery)->sum('net_total');
        $totalDiscounts   = (float) (clone $orderQuery)->sum(DB::raw('COALESCE(discount_amount, 0) + COALESCE(special_discount, 0)'));
        $totalGrossSales  = $totalNetSales + $totalDiscounts;

        // Order IDs for item-level aggregations
        $orderIds = (clone $orderQuery)->pluck('id');

        // Total Gross Profit and COGS from OrderItem
        // Note: order_items.profit is already calculated net of all item-level & order discounts!
        // Profit = itemNetTotal - totalPurchaseCost, so discounts are NOT subtracted again.
        $itemStats = OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(profit) as total_profit, SUM(net_total) as total_item_net')
            ->first();

        $grossProfitBeforeRet = (float) ($itemStats->total_profit ?? 0);
        $grossCogs            = max(0, $totalNetSales - $grossProfitBeforeRet);

        // 3. Customer Product Returns in this period
        $returnQuery = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved');

        if ($branchId) {
            $returnQuery->where('branch_id', $branchId);
        }

        $totalReturnsAmount = (float) $returnQuery->sum('total_amount');
        $totalReturnsCount  = (int) $returnQuery->count();
        $returnIds          = (clone $returnQuery)->pluck('id');

        // Profit loss and COGS recovered from returns
        $returnItemStats = ReturnItem::whereIn('product_return_id', $returnIds)
            ->selectRaw('SUM(profit) as total_return_profit')
            ->first();
        $returnProfitLoss = (float) ($returnItemStats->total_return_profit ?? 0);
        $returnedCogs     = max(0, $totalReturnsAmount - $returnProfitLoss);

        // Net Realized Trading Metrics (Revenue, COGS & Gross Profit)
        $netRealizedRevenue  = max(0, $totalNetSales - $totalReturnsAmount);
        $realizedGrossProfit = max(0, $grossProfitBeforeRet - $returnProfitLoss);
        $netRealizedCogs     = max(0, $netRealizedRevenue - $realizedGrossProfit);
        $grossMarginPercent  = $netRealizedRevenue > 0 ? round(($realizedGrossProfit / $netRealizedRevenue) * 100, 2) : 0;

        // 4. Operating Expenses (OPEX)
        // A. Branch Costs
        $branchCostQuery = BranchCost::whereBetween('cost_date', [$startDateStr, $endDateStr]);
        if ($branchId) {
            $branchCostQuery->where('branch_id', $branchId);
        }
        $totalBranchCosts = (float) (clone $branchCostQuery)->sum('amount');

        // B. Corporate / Central Company Costs
        $companyCostQuery = CompanyCost::whereBetween('cost_date', [$startDateStr, $endDateStr]);
        $totalCompanyCosts = $branchId ? 0 : (float) (clone $companyCostQuery)->sum('amount');

        // Total Operating Expenses (OPEX) — Excludes bonuses (bonus is company income)
        $totalOpex = $totalBranchCosts + $totalCompanyCosts;

        // Operating Trading Profit before other income
        $operatingProfit = $realizedGrossProfit - $totalOpex;

        // 5. Company Extra Income & Bonus (Supplier Incentives, Cashbacks, Special Gifts received)
        $bonusQuery = Bonus::whereBetween('bonus_date', [$startDateStr, $endDateStr]);
        $totalBonuses = $branchId ? 0 : (float) (clone $bonusQuery)->sum('amount');
        $bonusesCount = $branchId ? 0 : (int) (clone $bonusQuery)->count();

        // 6. Net Comprehensive Profit / Loss (Gross Profit - Total OPEX + Company Bonus)
        $netProfit        = $operatingProfit + $totalBonuses;
        $netMarginPercent = $netRealizedRevenue > 0 ? round(($netProfit / $netRealizedRevenue) * 100, 2) : 0;

        // 7. Section 2: Operating Expense Breakdown by Category
        $branchCostsByCat = (clone $branchCostQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $companyCostsByCat = $branchId ? collect() : (clone $companyCostQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $allCategories = collect(['salary', 'office', 'utility', 'transport', 'maintenance', 'marketing', 'staff', 'miscellaneous'])
            ->merge($branchCostsByCat->keys())
            ->merge($companyCostsByCat->keys())
            ->unique();

        $expenseCategories = $allCategories->map(function ($cat) use ($branchCostsByCat, $companyCostsByCat, $totalOpex) {
            $bAmt = (float) ($branchCostsByCat[$cat] ?? 0);
            $cAmt = (float) ($companyCostsByCat[$cat] ?? 0);
            $total = $bAmt + $cAmt;
            return (object) [
                'category'      => $cat,
                'name'          => ucfirst($cat),
                'branch_cost'   => $bAmt,
                'company_cost'  => $cAmt,
                'total_cost'    => $total,
                'share_percent' => $totalOpex > 0 ? round(($total / $totalOpex) * 100, 1) : 0,
            ];
        })->filter(function ($item) {
            return $item->total_cost > 0;
        })->sortByDesc('total_cost')->values();

        // 7. Section 3: Multi-Branch Profitability Contribution
        $allBranches = Branch::orderBy('name', 'asc')->get();

        $branchOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'rejected')
            ->select('branch_id', DB::raw('SUM(net_total) as net_sales'), DB::raw('SUM(special_discount) as special_discount'))
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $branchItemProfits = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'rejected')
            ->select('orders.branch_id', DB::raw('SUM(order_items.profit) as gross_profit'))
            ->groupBy('orders.branch_id')
            ->pluck('gross_profit', 'branch_id');

        $branchReturns = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->select('branch_id', DB::raw('SUM(total_amount) as return_amount'))
            ->groupBy('branch_id')
            ->pluck('return_amount', 'branch_id');

        $branchCostsByBranch = BranchCost::whereBetween('cost_date', [$startDateStr, $endDateStr])
            ->select('branch_id', DB::raw('SUM(amount) as cost_amount'))
            ->groupBy('branch_id')
            ->pluck('cost_amount', 'branch_id');

        $branchContributions = $allBranches->map(function ($br) use ($branchOrders, $branchItemProfits, $branchReturns, $branchCostsByBranch, $realizedGrossProfit) {
            $bOrder      = $branchOrders[$br->id] ?? null;
            $bNetSales   = (float) ($bOrder->net_sales ?? 0);
            $bSpecDisc   = (float) ($bOrder->special_discount ?? 0);
            $bItemProfit = (float) ($branchItemProfits[$br->id] ?? 0);
            $bReturnAmt  = (float) ($branchReturns[$br->id] ?? 0);

            $bRealizedRev     = max(0, $bNetSales - $bReturnAmt);
            $bGrossProfit     = max(0, $bItemProfit * ($bNetSales > 0 ? ($bRealizedRev / $bNetSales) : 1));
            $bCogs            = max(0, $bRealizedRev - $bGrossProfit);
            $bOpex            = (float) ($branchCostsByBranch[$br->id] ?? 0);
            $bNetContribution = $bGrossProfit - $bOpex;

            return (object) [
                'branch'           => $br,
                'realized_revenue' => $bRealizedRev,
                'cogs'             => $bCogs,
                'gross_profit'     => $bGrossProfit,
                'opex'             => $bOpex,
                'net_contribution' => $bNetContribution,
                'share_percent'    => $realizedGrossProfit > 0 ? round(($bGrossProfit / $realizedGrossProfit) * 100, 1) : 0,
            ];
        })->filter(function ($item) {
            return $item->realized_revenue > 0 || $item->opex > 0;
        })->sortByDesc('realized_revenue')->values();

        // 8. Section 4: Treasury Cash Flow & Liquidity
        $totalCashCollected = (float) (clone $orderQuery)->sum('payment_amount');
        $totalCustomerDue   = max(0, $totalNetSales - $totalCashCollected);

        $suppPayQuery = SupplierTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('type', 'pay');
        if ($branchId) {
            $suppPayQuery->where('branch_id', $branchId);
        }
        $totalSupplierPaid = (float) $suppPayQuery->sum('amount');
        $totalSupplierDue  = (float) Supplier::sum('due');

        $totalAdminCash   = (float) AdminCash::sum('cash');
        $totalBranchCash  = (float) Branch::sum('cash');
        $totalCompanyCash = $totalAdminCash + $totalBranchCash;

        // Filter auxiliary dropdowns
        $branches = Branch::orderBy('name', 'asc')->get();

        return view('pages.admin.report.financial', compact(
            'branches',
            'startDate',
            'endDate',
            'branchId',
            'totalOrdersCount',
            'totalGrossSales',
            'totalDiscounts',
            'totalNetSales',
            'totalReturnsAmount',
            'totalReturnsCount',
            'netRealizedRevenue',
            'grossCogs',
            'returnedCogs',
            'netRealizedCogs',
            'realizedGrossProfit',
            'grossMarginPercent',
            'totalBranchCosts',
            'totalCompanyCosts',
            'totalBonuses',
            'bonusesCount',
            'totalOpex',
            'operatingProfit',
            'netProfit',
            'netMarginPercent',
            'expenseCategories',
            'branchContributions',
            'totalCashCollected',
            'totalCustomerDue',
            'totalSupplierPaid',
            'totalSupplierDue',
            'totalAdminCash',
            'totalBranchCash',
            'totalCompanyCash'
        ));
    }
}
