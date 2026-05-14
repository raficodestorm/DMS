<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\ProductReturn;
use App\Models\Bonus;
use App\Models\CompanyCost;
use App\Models\BranchCost;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. Date Filtering Logic ---
        $startDate = null;
        $endDate = null;
        $periodLabel = 'Current Month';

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $periodLabel = "From {$startDate->format('d M, Y')} to {$endDate->format('d M, Y')}";
        } elseif ($request->filled('day') && $request->filled('month') && $request->filled('year')) {
            $startDate = Carbon::create($request->year, $request->month, $request->day)->startOfDay();
            $endDate = Carbon::create($request->year, $request->month, $request->day)->endOfDay();
            $periodLabel = $startDate->format('d M, Y');
        } elseif ($request->filled('month') && $request->filled('year')) {
            $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
            $endDate = Carbon::create($request->year, $request->month, 1)->endOfMonth();
            $periodLabel = $startDate->format('F Y');
        } elseif ($request->filled('year')) {
            $startDate = Carbon::create($request->year, 1, 1)->startOfYear();
            $endDate = Carbon::create($request->year, 12, 31)->endOfYear();
            $periodLabel = "Year {$request->year}";
        } else {
            // Default to current month
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $periodLabel = now()->format('F Y');
        }

        $completedStatuses = ['complete', 'delivered'];

        // --- 2. Sales Report ---
        $ordersQuery = Order::whereIn('status', $completedStatuses)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalOrders = $ordersQuery->count();
        $totalSalesAmount = $ordersQuery->sum('net_total');
        
        $totalSpecialDiscount = $ordersQuery->sum('special_discount');
        $totalItemDiscount = Order::whereIn('status', $completedStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount_amount');
            
        $totalDiscount = $totalItemDiscount + $totalSpecialDiscount;

        $totalPaidAmount = Transaction::where('type', 'pay')
            ->where('status', 'complete')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalReturnedAmount = ProductReturn::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Total Due (Snapshot of current all customer dues)
        $totalDueAmount = Customer::sum('due');

        // --- 3. Profit Report ---
        // Gross Profit = (Selling Price - Purchase Price - Discount) - Special Discount
        $orderIds = Order::whereIn('status', $completedStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('id');

        $orderItemsProfit = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('order_id', $orderIds)
            ->sum(DB::raw('order_items.quantity * ((order_items.selling_rate - order_items.discount_amount) - products.purchase_price)'));

        $grossProfit = $orderItemsProfit - $totalSpecialDiscount;

        // Bonus increases profit
        $totalBonus = Bonus::whereBetween('bonus_date', [$startDate, $endDate])->sum('amount');
        
        $profit = $grossProfit + $totalBonus;

        // Costs reduce net profit
        $companyCost = CompanyCost::whereBetween('cost_date', [$startDate, $endDate])->sum('amount');
        $branchCost = BranchCost::whereBetween('cost_date', [$startDate, $endDate])->sum('amount');
        $totalCost = $companyCost + $branchCost;

        $netProfit = $profit - $totalCost;

        // Salary portion of the costs
        $companySalary = CompanyCost::whereBetween('cost_date', [$startDate, $endDate])
            ->where('category', 'salary')->sum('amount');
        $branchSalary = BranchCost::whereBetween('cost_date', [$startDate, $endDate])
            ->where('category', 'staff')->sum('amount');
        $totalSalary = $companySalary + $branchSalary;

        // --- 4. Product & Inventory Report ---
        $totalStockValue = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->sum(DB::raw('stocks.quantity * products.purchase_price'));

        $mostSoldProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereIn('order_id', $orderIds)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();

        // --- 5. Transaction Report ---
        $totalBuyTrans = Transaction::where('type', 'buy')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $totalPayTrans = Transaction::where('type', 'pay')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $totalReturnTrans = Transaction::where('type', 'return')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // --- 6. Employee/Branch Summary ---
        $branchSales = Order::select('branches.name as branch_name', DB::raw('SUM(net_total) as total_sales'))
            ->join('users', 'orders.manager_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->whereIn('orders.status', $completedStatuses)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total_sales')
            ->get();

        // Chart Data (Daily Sales for the selected period)
        $dailySalesRaw = Order::whereIn('status', $completedStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(net_total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $chartDates = [];
        $chartSales = [];
        
        $currentDate = $startDate->copy();
        while($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $chartDates[] = $currentDate->format('d M');
            $sale = $dailySalesRaw->firstWhere('date', $dateStr);
            $chartSales[] = $sale ? (float)$sale->total : 0;
            
            $currentDate->addDay();
            // Safety break if period is too long (e.g. year view)
            if (count($chartDates) > 31) {
                // For year view, aggregate by month instead
                if ($request->filled('year') && !$request->filled('month')) {
                    $monthlySalesRaw = Order::whereIn('status', $completedStatuses)
                        ->whereYear('created_at', $request->year)
                        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(net_total) as total'))
                        ->groupBy('month')
                        ->orderBy('month')
                        ->pluck('total', 'month')->toArray();
                        
                    $chartDates = [];
                    $chartSales = [];
                    $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
                    foreach($months as $m => $name) {
                        $chartDates[] = $name;
                        $chartSales[] = $monthlySalesRaw[$m] ?? 0;
                    }
                }
                break;
            }
        }

        return view('pages.admin.report.index', compact(
            'periodLabel', 'totalOrders', 'totalSalesAmount', 'totalPaidAmount', 
            'totalDueAmount', 'totalDiscount', 'totalReturnedAmount',
            'grossProfit', 'totalBonus', 'profit', 'totalCost', 'netProfit', 'totalSalary',
            'totalStockValue', 'mostSoldProducts',
            'totalBuyTrans', 'totalPayTrans', 'totalReturnTrans',
            'branchSales', 'chartDates', 'chartSales'
        ));
    }
}
