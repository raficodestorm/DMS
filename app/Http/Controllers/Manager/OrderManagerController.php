<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderManagerController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Order::with(['customer', 'sr'])
      ->where('manager_id', $user->id)
      ->whereNotNull('sr_id') // Exclude retail orders (manager acting as SR)
      ->latest();

    // Date Range Filter
    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    // Status Filter
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // Search Filter
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

    $orders = $query->paginate(15)->appends($request->query());

    if ($request->ajax()) {
      return response()->json([
        'table'      => view('pages.manager.order.table', compact('orders'))->render(),
        'mobile'     => view('pages.manager.order.mtable', compact('orders'))->render(),
        'pagination' => $orders->links()->render(),
      ]);
    }

    return view('pages.manager.order.index', compact('orders'));
  }

  public function showForManager($id)
  {
    $order = Order::with(['customer', 'sr', 'items.product'])->findOrFail($id);

    return view('pages.manager.order.show', compact('order'));
  }

  public function allCustomerOrders()
  {
    $customers = Customer::with(['orders' => function ($query) {
      $query->where('status', '!=', 'rejected')->where('manager_id', auth()->user()->id);
    }])->get()->map(function ($customer) {
      $customer->total_order_amount = $customer->orders->sum('net_total');
      return $customer;
    });

    return view('pages.manager.order.customer-based-orders', compact('customers'));
  }

  public function allSrOrders()
  {
    $manager = auth()->user();

    $srs = User::where('role', 'sr')
      ->where('branch_id', $manager->branch_id)
      ->with(['srOrders' => function ($query) use ($manager) {
        $query->where('manager_id', $manager->id)
          ->where('status', '!=', 'rejected');
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
      'pages.manager.order.sr-based-orders',
      compact('srs')
    );
  }

  public function specificCustomerOrders($id)
  {
    $customer = Customer::findOrFail($id);

    $orders = Order::where('customer_id', $id)
      ->where('status', '!=', 'rejected')
      ->latest()
      ->paginate(15);

    return view('pages.manager.order.specific-index', compact('customer', 'orders'));
  }


  public function specificSrOrders($id)
  {
    $sr = User::where('role', 'sr')->findOrFail($id);

    $orders = Order::where('sr_id', $id)
      ->where('status', '!=', 'rejected')
      ->latest()
      ->paginate(15);

    return view(
      'pages.manager.order.specific-index',
      compact('sr', 'orders')
    );
  }


  public function sendToAdmin($id)
  {
    $order = Order::findOrFail($id);

    try {
      DB::transaction(function () use ($order) {

        $order->update([
          'status' => 'pending_manager'
        ]);

        $notificationData = [
          'title'   => 'Order Approval Request',
          'message' => [
            'text' => 'A new order has been sent by',
            'from' => Auth::user()->branch->name ?? 'Unknown Branch'
          ],
          'url'  => route('admin.order.show', $order->id),
          'type' => 'order_request'
        ];

        $admins = \App\Models\User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
          $admin->notify(new \App\Notifications\SystemNotification($notificationData));
        }
      });

      return redirect()
        ->route('manager.order.index')
        ->with('success', 'Order sent to Admin for final approval.');
    } catch (\Exception $e) {
      // dd($e->getMessage());
      return back()->with('error', 'Something went wrong while sending order to admin. Please try again.');
    }
  }


  public function confirmAndInvoice(Order $order)
  {
    try {
      $customerData = DB::transaction(function () use ($order) {

        $branchId = auth()->user()->branch_id;

        $order->load(['items.product.category', 'customer', 'sr']);

        if ($order->status === 'complete') {
          throw new \Exception('Order already completed.');
        }

        foreach ($order->items as $item) {

          $stock = Stock::where([
            'product_id' => $item->product_id,
            'branch_id'  => $branchId
          ])->lockForUpdate()->first();

          if (!$stock) {
            throw new \Exception("Stock not found for {$item->product->name}");
          }

          if ($stock->quantity < $item->quantity) {
            throw new \Exception("Insufficient stock for {$item->product->name}");
          }

          $stock->decrement('quantity', $item->quantity);
        }

        $customer = Customer::lockForUpdate()->findOrFail($order->customer_id);

        $previous_due = $customer->due;

        $customer->increment('due', $order->net_total);
        $customer->refresh();

        Transaction::create([
          'customer_id' => $customer->id,
          'order_id'    => $order->id,
          'sr_id'    => $order->sr_id,
          'type'        => 'buy',
          'amount'      => $order->net_total,
          'due'         => $customer->due,
          'status'      => "complete",
          'note'        => 'Order confirmed #' . $order->id,
        ]);

        $order->update([
          'status' => 'complete'
        ]);

        return [
          'details' => $customer,
          'previous_due' => $previous_due,
          'current_due' => $customer->due
        ];
      });

      $order->refresh();
      $order->load(['items.product.category', 'customer', 'sr']);

      $items = $order->items->sortBy(function ($item) {
        return $item->product->category->name ?? 'General';
      });
      $hasDiscount = $items->contains(function ($item) {
        return (float) $item->discount_amount > 0;
      });

      return view('pages.manager.order.invoice', compact(
        'order',
        'customerData',
        'items',
        'hasDiscount'
      ));
    } catch (\Throwable $e) {
      return back()->with('error', $e->getMessage());
    }
  }

  public function destroy($id)
  {
    $order = Order::findOrFail($id);

    if ($order->status !== 'rejected') {
      return redirect()->back()->with('error', 'Only rejected orders can be deleted.');
    }

    $order->items()->delete();
    $order->delete();

    return redirect()->route('manager.order.index')->with('success', 'Order deleted successfully.');
  }
}
