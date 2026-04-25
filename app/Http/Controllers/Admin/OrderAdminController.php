<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderAdminController extends Controller
{

  public function indexForPendingAdmin()
  {
    $query = Order::with(['customer', 'sr']);

    $orders = $query->where('status', 'pending_manager')->latest()->paginate(15);
    return view("pages.admin.orders.index", compact('orders'));
  }


  public function indexForAllAdmin(Request $request)
  {
    $query = Order::with(['customer', 'sr'])
      ->latest();

    if ($request->filled('search')) {

      $search = trim($request->search);

      $query->where(function ($q) use ($search) {

        if (str_starts_with('BRS', strtoupper($search))) {
          return;
        }

        if (preg_match('/^BRS(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }

        $q->where('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          });
      });
    }

    $orders = $query->paginate(15);

    if ($request->ajax()) {
      return response()->json([
        'table'  => view('pages.admin.orders.table', compact('orders'))->render(),
        'mobile' => view('pages.admin.orders.mtable', compact('orders'))->render(),
      ]);
    }

    return view('pages.admin.orders.index', compact('orders'));
  }



  public function allCustomerOrders()
  {
    $customers = Customer::with(['orders' => function ($query) {
      $query->where('status', '!=', 'rejected');
    }])->get()->map(function ($customer) {
      $customer->total_order_amount = $customer->orders->sum('net_total');
      return $customer;
    });

    return view('pages.admin.orders.customer-based-orders', compact('customers'));
  }

  public function allSrOrders()
  {

    $srs = User::where('role', 'sr')
      ->with(['srOrders' => function ($query) {
        $query->where('status', '!=', 'rejected');
      }])
      ->get();

    foreach ($srs as $sr) {

      // Only delivered orders count
      $sr->total_orders = $sr->srOrders
        ->where('status', 'delivered')
        ->count();

      // Only delivered amount
      $sr->total_order_amount = $sr->srOrders
        ->where('status', 'delivered')
        ->sum('net_total');
    }

    return view(
      'pages.admin.orders.sr-based-orders',
      compact('srs')
    );
  }


  public function allBranchOrders()
  {
    $branches = Branch::with(['users' => function ($query) {
      $query->where('role', 'sr')
        ->with(['srOrders' => function ($order) {
          $order->where('status', '!=', 'rejected');
        }]);
    }])->get();

    foreach ($branches as $branch) {

      $totalOrders = 0;
      $totalAmount = 0;

      foreach ($branch->users as $sr) {

        $totalOrders += $sr->srOrders
          ->where('status', 'delivered')
          ->count();

        $totalAmount += $sr->srOrders
          ->where('status', 'delivered')
          ->sum('net_total');
      }

      $branch->total_orders = $totalOrders;
      $branch->total_order_amount = $totalAmount;
    }

    return view(
      'pages.admin.orders.branch-based-orders',
      compact('branches')
    );
  }


  public function specificSrOrders($id)
  {
    $sr = User::where('role', 'sr')->findOrFail($id);

    $orders = Order::where('sr_id', $id)
      ->where('status', '!=', 'rejected')
      ->latest()
      ->paginate(15);

    return view(
      'pages.admin.orders.specific-index',
      compact('sr', 'orders')
    );
  }


  public function specificCustomerOrders($id)
  {
    $customer = Customer::findOrFail($id);

    $orders = Order::where('customer_id', $id)
      ->where('status', '!=', 'rejected')
      ->latest()
      ->paginate(15);

    return view('pages.admin.orders.specific-index', compact('customer', 'orders'));
  }


  public function specificBranchOrders($id)
  {
    $branch = Branch::findOrFail($id);

    $orders = Order::with(['customer', 'sr'])
      ->whereHas('sr', function ($query) use ($id) {
        $query->where('branch_id', $id);
      })
      ->where('status', '!=', 'rejected')
      ->latest()
      ->paginate(15);

    return view(
      'pages.admin.orders.specific-index',
      compact('branch', 'orders')
    );
  }



  public function showForAdmin($id)
  {
    $order = Order::with(['customer', 'sr', 'items.product'])->findOrFail($id);

    return view('pages.admin.orders.show', compact('order'));
  }

  public function approve($id)
  {
    $order = Order::findOrFail($id);

    $order->update([
      'status' => 'approved'
    ]);

    return back()->with('success', "Order BRS{$id} has been approved successfully.");
  }
}
