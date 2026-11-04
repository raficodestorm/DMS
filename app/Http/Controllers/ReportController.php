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
}
