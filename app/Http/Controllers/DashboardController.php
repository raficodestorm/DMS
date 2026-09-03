<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\BranchCost;
use App\Models\CompanyCost;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index(Request $request)
  {
    $user = $request->user();
    $role = $user->role;
    $data = [];

    $currentMonth = now()->month;
    $currentYear  = now()->year;
    $data['monthName'] = now()->format('F');

    // Statuses that count as real completed sales
    $completedStatuses = ['complete', 'delivered'];

    if ($role === 'admin') {

      // 1. Total Company Stock Value across all branches
      $data['totalStockValue'] = Stock::join('products', 'stocks.product_id', '=', 'products.id')
        ->sum(DB::raw('stocks.quantity * products.purchase_price'));

      // 2. Current Month Total Sales (completed orders only)
      $data['currentMonthSales'] = Order::whereIn('status', $completedStatuses)
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('net_total');

      // 3. Current Month Profit (completed orders only)
      $orderItemsProfit = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->whereIn('orders.status', $completedStatuses)
        ->whereMonth('orders.created_at', $currentMonth)
        ->whereYear('orders.created_at', $currentYear)
        ->sum(DB::raw('order_items.quantity * ((order_items.selling_rate - order_items.discount_amount) - products.purchase_price)'));

      $specialDiscounts = Order::whereIn('status', $completedStatuses)
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('special_discount');

      $data['currentMonthProfit'] = $orderItemsProfit - $specialDiscounts;

      // 4. Current Month Cost = company_costs + all branch_costs
      $companyCost = CompanyCost::whereMonth('cost_date', $currentMonth)
        ->whereYear('cost_date', $currentYear)
        ->sum('amount');

      $branchCostTotal = BranchCost::whereMonth('cost_date', $currentMonth)
        ->whereYear('cost_date', $currentYear)
        ->sum('amount');

      $data['currentMonthCost'] = $companyCost + $branchCostTotal;

      // 5. Yearly Sales Data (Monthly breakdown)
      $yearlySales = Order::whereIn('status', $completedStatuses)
        ->whereYear('created_at', $currentYear)
        ->select(
          DB::raw('MONTH(created_at) as month'),
          DB::raw('SUM(net_total) as total_sales')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total_sales', 'month')
        ->toArray();

      $months = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Aug',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec'
      ];

      $chartData = [];
      foreach ($months as $num => $name) {
        $chartData[] = [
          'month' => $name,
          'sales' => $yearlySales[$num] ?? 0
        ];
      }
      $data['yearlySalesChart'] = $chartData;
    } elseif ($role === 'manager') {

      $branchId = $user->branch_id;

      // 1. Branch Stock Value only
      $data['totalStockValue'] = Stock::where('stocks.branch_id', $branchId)
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->sum(DB::raw('stocks.quantity * products.purchase_price'));

      // 2. Branch Monthly Sales (completed orders for this manager)
      $data['currentMonthSales'] = Order::where('manager_id', $user->id)
        ->whereIn('status', $completedStatuses)
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('net_total');

      // 3. Branch Total Customer Due
      $data['branchTotalDue'] = Customer::where('branch_id', $branchId)->sum('due');

      // 4. Branch Monthly Cost
      $data['currentMonthCost'] = BranchCost::where('branch_id', $branchId)
        ->whereMonth('cost_date', $currentMonth)
        ->whereYear('cost_date', $currentYear)
        ->sum('amount');

      // 5. Yearly Branch Sales Data
      $yearlySales = Order::where('manager_id', $user->id)
        ->whereIn('status', $completedStatuses)
        ->whereYear('created_at', $currentYear)
        ->select(
          DB::raw('MONTH(created_at) as month'),
          DB::raw('SUM(net_total) as total_sales')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total_sales', 'month')
        ->toArray();

      $months = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Aug',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec'
      ];

      $chartData = [];
      foreach ($months as $num => $name) {
        $chartData[] = [
          'month' => $name,
          'sales' => $yearlySales[$num] ?? 0
        ];
      }
      $data['yearlySalesChart'] = $chartData;
    } elseif ($role === 'sr') {

      $srId     = $user->id;
      $branchId = $user->branch_id;

      // 1. SR's own current-month order amount
      $data['srMonthlyOrderAmount'] = Order::where('sr_id', $srId)
        ->where('status', '!=', 'rejected')
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('net_total');

      // 2. Total customers in the SR's branch
      $data['branchCustomerCount'] = Customer::where('branch_id', $branchId)->count();

      // 3. Daily sales chart for current month
      $dailySalesRaw = Order::where('sr_id', $srId)
        ->where('status', '!=', 'rejected')
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->select(
          DB::raw('DAY(created_at) as day'),
          DB::raw('SUM(net_total) as total_sales')
        )
        ->groupBy('day')
        ->orderBy('day')
        ->get()
        ->pluck('total_sales', 'day')
        ->toArray();

      $daysInMonth = now()->daysInMonth;
      $dailyChartData = [];
      for ($d = 1; $d <= $daysInMonth; $d++) {
        $dailyChartData[] = [
          'day'   => $d,
          'sales' => $dailySalesRaw[$d] ?? 0,
        ];
      }
      $data['srDailyChart'] = $dailyChartData;
    } elseif ($role === 'customer') {

      $customerId = $user->customer_id;

      // 1. Current-month total order amount
      $data['customerMonthlyOrders'] = Order::where('customer_id', $customerId)
        ->where('status', '!=', 'rejected')
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('net_total');

      // 2. Current-month total payments
      $data['customerMonthlyPayments'] = Transaction::where('customer_id', $customerId)
        ->where('type', 'pay')
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('amount');

      // 3. Latest due
      $latestTransaction = Transaction::where('customer_id', $customerId)
        ->latest()
        ->first();
      $data['customerCurrentDue'] = Customer::find($customerId)?->due ?? 0;

      // 4. Running order
      $runningStatuses = ['pending_sr', 'pending_manager', 'approved'];
      $data['runningOrder'] = Order::where('customer_id', $customerId)
        ->whereIn('status', $runningStatuses)
        ->latest()
        ->first();

      // 5. Daily order chart
      $dailyOrdersRaw = Order::where('customer_id', $customerId)
        ->where('status', '!=', 'rejected')
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->select(
          DB::raw('DAY(created_at) as day'),
          DB::raw('SUM(net_total) as total_amount')
        )
        ->groupBy('day')
        ->orderBy('day')
        ->get()
        ->pluck('total_amount', 'day')
        ->toArray();

      $daysInMonth = now()->daysInMonth;
      $customerDailyChart = [];
      for ($d = 1; $d <= $daysInMonth; $d++) {
        $customerDailyChart[] = [
          'day'    => $d,
          'amount' => $dailyOrdersRaw[$d] ?? 0,
        ];
      }
      $data['customerDailyChart'] = $customerDailyChart;
    }

    // Role-based dashboard view
    $view = match ($role) {
      'admin'    => 'pages.dashboard.admin',
      'manager'  => 'pages.dashboard.manager',
      'sr'       => 'pages.dashboard.sr',
      'customer' => 'pages.dashboard.customer',
      default    => 'dashboards',
    };

    return view($view, $data);
  }
}
