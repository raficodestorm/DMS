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
    $query = Order::with(['customer', 'sr.branch', 'manager.branch'])
      ->latest();

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
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

    if ($request->filled('branch_id')) {
      $branchId = $request->branch_id;
      $query->where(function ($q) use ($branchId) {
        $q->where('branch_id', $branchId)
          ->orWhereHas('sr', function ($srQ) use ($branchId) {
            $srQ->where('branch_id', $branchId);
          })
          ->orWhereHas('manager', function ($mgrQ) use ($branchId) {
            $mgrQ->where('branch_id', $branchId);
          });
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    $orders = $query->paginate(15)->withQueryString();
    $branches = Branch::orderBy('name', 'asc')->get();

    if ($request->ajax()) {
      return response()->json([
        'table'      => view('pages.admin.orders.table', compact('orders'))->render(),
        'mobile'     => view('pages.admin.orders.mtable', compact('orders'))->render(),
        'total'      => $orders->total(),
        'pagination' => (string) $orders->links(),
      ]);
    }

    return view('pages.admin.orders.index', compact('orders', 'branches'));
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
    $branches = Branch::with([
        'orders' => function ($query) {
            $query->where('status', 'delivered');
        }
    ])->get();

    foreach ($branches as $branch) {

        $branch->total_orders = $branch->orders->count();

        $branch->total_order_amount = $branch->orders->sum('net_total');
    }

    return view(
        'pages.admin.orders.branch-based-orders',
        compact('branches')
    );
}


  private function applySpecificOrderFilters(Request $request, $query)
  {
    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        if (preg_match('/^BRS(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }
        $q->where('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          })
          ->orWhereHas('sr', function ($sr) use ($search) {
            $sr->where('fullname', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    return $query;
  }

  public function specificSrOrders(Request $request, $id)
  {
    $sr = User::where('role', 'sr')->findOrFail($id);

    $query = Order::with(['customer', 'sr'])
      ->where('sr_id', $id)
      ->latest();

    $query = $this->applySpecificOrderFilters($request, $query);
    $orders = $query->paginate(15)->withQueryString();

    if ($request->ajax()) {
      return response()->json([
        'table'      => view('pages.admin.orders.specific-table', compact('orders'))->render(),
        'mobile'     => view('pages.admin.orders.specific-mtable', compact('orders'))->render(),
        'total'      => $orders->total(),
        'pagination' => (string) $orders->links(),
      ]);
    }

    return view('pages.admin.orders.specific-index', compact('sr', 'orders'));
  }


  public function specificCustomerOrders(Request $request, $id)
  {
    $customer = Customer::findOrFail($id);

    $query = Order::with(['customer', 'sr'])
      ->where('customer_id', $id)
      ->latest();

    $query = $this->applySpecificOrderFilters($request, $query);
    $orders = $query->paginate(15)->withQueryString();

    if ($request->ajax()) {
      return response()->json([
        'table'      => view('pages.admin.orders.specific-table', compact('orders'))->render(),
        'mobile'     => view('pages.admin.orders.specific-mtable', compact('orders'))->render(),
        'total'      => $orders->total(),
        'pagination' => (string) $orders->links(),
      ]);
    }

    return view('pages.admin.orders.specific-index', compact('customer', 'orders'));
  }


  public function specificBranchOrders(Request $request, $id)
  {
    $branch = Branch::findOrFail($id);

    $query = Order::with(['customer', 'sr'])
      ->where(function ($q) use ($id) {
        $q->where('branch_id', $id)
          ->orWhereHas('sr', function ($srQ) use ($id) {
            $srQ->where('branch_id', $id);
          });
      })
      ->latest();

    $query = $this->applySpecificOrderFilters($request, $query);
    $orders = $query->paginate(15)->withQueryString();

    if ($request->ajax()) {
      return response()->json([
        'table'      => view('pages.admin.orders.specific-table', compact('orders'))->render(),
        'mobile'     => view('pages.admin.orders.specific-mtable', compact('orders'))->render(),
        'total'      => $orders->total(),
        'pagination' => (string) $orders->links(),
      ]);
    }

    return view('pages.admin.orders.specific-index', compact('branch', 'orders'));
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
