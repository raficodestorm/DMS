<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\BranchCost;
use App\Models\CompanyCost;
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

      // 3. Branch Monthly Profit (completed orders for this manager)
      $orderItemsProfit = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('orders.manager_id', $user->id)
        ->whereIn('orders.status', $completedStatuses)
        ->whereMonth('orders.created_at', $currentMonth)
        ->whereYear('orders.created_at', $currentYear)
        ->sum(DB::raw('order_items.quantity * ((order_items.selling_rate - order_items.discount_amount) - products.purchase_price)'));

      $specialDiscounts = Order::where('manager_id', $user->id)
        ->whereIn('status', $completedStatuses)
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('special_discount');

      $data['currentMonthProfit'] = $orderItemsProfit - $specialDiscounts;

      // 4. Branch Monthly Cost from branch_costs table (correct table/column)
      $data['currentMonthCost'] = BranchCost::where('branch_id', $branchId)
        ->whereMonth('cost_date', $currentMonth)
        ->whereYear('cost_date', $currentYear)
        ->sum('amount');
    }

    // Role-based dashboard view
    $view = "pages.dashboard.{$role}";
    if (!view()->exists($view)) {
      $view = "pages.dashboard.user";
    }

    return view($view, $data);
  }
}
