<?php

namespace App\Http\Controllers\Dso;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderDsoController extends Controller
{

  
  public function show($id)
  {
    $order = Order::with(['customer', 'sr', 'supplier', 'items.product'])->findOrFail($id);

    return view('pages.dso.order.show', compact('order'));
  }

  /**
   * Delivered Orders Index View for DSO.
   */
  public function deliveredOrdersIndex(Request $request)
  {
    return view('pages.dso.order.index');
  }

  /**
   * AJAX: Fetch Delivered Orders Data with filters & pagination (20 per page).
   */
  public function fetchDeliveredOrdersData(Request $request)
  {
    $user = auth()->user();

    $query = Order::with(['customer', 'branch', 'sr'])
      ->where('delivered_by', $user->id)
      ->where('status', 'delivered')
      ->latest('delivered_at');

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search)
          ->orWhere('customer_name', 'like', "%{$search}%")
          ->orWhere('customer_phone', 'like', "%{$search}%")
          ->orWhereHas('customer', function ($cq) use ($search) {
            $cq->where('shop_name', 'like', "%{$search}%")
              ->orWhere('manager', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('order_type')) {
      $query->where('order_type', $request->order_type);
    }

    if ($request->filled('payment_status')) {
      $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('delivered_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('delivered_at', '<=', $request->to_date);
    }

    $orders = $query->paginate(20)->withQueryString();

    return response()->json([
      'table'       => view('pages.dso.order.table', compact('orders'))->render(),
      'mobile'      => view('pages.dso.order.mtable', compact('orders'))->render(),
      'pagination'  => $orders->links()->toHtml(),
      'total_count' => $orders->total(),
    ]);
  }

}
