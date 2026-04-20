<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
  // ১. অর্ডার ইনডেক্স
  public function index()
  {
    $user = auth()->user();
    $query = Order::with(['customer', 'sr']);

    if ($user->role == 'sr') $query->where('sr_id', $user->id);
    if ($user->role == 'manager') $query->where('manager_id', $user->id);

    $orders = $query->latest()->paginate(15);
    return view("pages." . auth()->user()->role . ".order.index", compact('orders'));
  }

  public function indexForPendingAdmin()
  {
    $query = Order::with(['customer', 'sr']);

    $orders = $query->where('status', 'pending_manager')->latest()->paginate(15);
    return view("pages.admin.orders.index", compact('orders'));
  }


  public function create()
  {
    $branchId = auth()->user()->branch_id;
    $customers = Customer::orderBy('shop_name', 'asc')->where('branch_id', $branchId)->get();

    $products = Stock::with('product')
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->map(function ($stock) {
        return [
          'id' => $stock->product_id,
          'name' => $stock->product->name,
          'price' => $stock->product->price,
          'image' => $stock->product->image,
          'available_qty' => $stock->quantity
        ];
      });

    return view('pages.sr.order.create', compact('customers', 'products'));
  }

  // ২. প্রোডাক্ট ডাটা ও ডিসকাউন্ট চেক (AJAX)
  public function getProductData($id)
  {
    $product = Product::findOrFail($id);
    $today = Carbon::today();

    $offer = Offer::where('product_id', $id)
      ->where('status', 1)
      ->whereDate('start_date', '<=', $today)
      ->whereDate('end_date', '>=', $today)
      ->first();

    return response()->json([
      'price' => $product->price,
      'discount' => $offer ? $offer->discount_amount : 0,
      'discount_type' => $offer ? $offer->type : 'fixed'
    ]);
  }

  // ৩. অর্ডার স্টোর (SR)
  public function store(Request $request)
  {
    // ১. ভ্যালিডেশন (অবশ্যই করবেন)
    $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'products'    => 'required|array|min:1',
      'net_total'   => 'required|numeric'
    ]);

    try {
      return DB::transaction(function () use ($request) {
        $user = auth()->user();
        $branchId = $user->branch_id;

        $manager = User::where('branch_id', $branchId)
          ->where('role', 'manager')
          ->first();

        $order = Order::create([
          'customer_id'     => $request->customer_id,
          'sr_id'           => $user->id,
          'manager_id'      => $manager ? $manager->id : null, // ম্যানেজার না থাকলে নাল থাকবে
          'status'          => 'pending_sr',
          'discount_amount' => $request->total_discount ?? 0,
          'net_total'       => $request->net_total,
          'note'            => $request->note
        ]);

        foreach ($request->products as $item) {
          OrderItem::create([
            'order_id'        => $order->id,
            'product_id'      => $item['product_id'],
            'quantity'        => $item['qty'],
            'price'           => $item['price'],
            'total'           => $item['qty'] * $item['price'],
            'discount_amount' => $item['discount'] ?? 0,
            'net_total'       => ($item['price'] * $item['qty']) - (($item['discount'] ?? 0) * $item['qty'])
          ]);
        }

        if ($manager) {
          $notificationData = [
            'title'   => 'New Order Received',
            'message' => [
              'text' => 'A new order has been placed by',
              'from' => $user->username
            ],
            'url'     => route('manager.order.show', $order->id),
            'type'    => 'new_order'
          ];

          $manager->notify(new \App\Notifications\SystemNotification($notificationData));
        }

        return redirect()->route('dashboards')->with('success', 'Order requested successfully!');
      });
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
    }
  }


  public function showForManager($id)
  {
    $order = Order::with(['customer', 'sr', 'items.product'])->findOrFail($id);

    return view('pages.manager.order.show', compact('order'));
  }

  public function showForAdmin($id)
  {
    $order = Order::with(['customer', 'sr', 'items.product'])->findOrFail($id);

    return view('pages.admin.orders.show', compact('order'));
  }

  // ১. এডিট পেজ (যেখানে SR এর মতো একই ফর্ম থাকবে)
  public function edit($id)
  {
    $order = Order::with(['items.product', 'customer'])->findOrFail($id);

    if ($order->sr->branch_id != auth()->user()->branch_id) {
      return redirect()->back()->with('error', 'Unauthorized access.');
    }

    $branchId = auth()->user()->branch_id;
    $customers = Customer::orderBy('shop_name', 'asc')->where('branch_id', $branchId)->get();

    $products = Stock::with('product')
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->map(function ($stock) {
        return [
          'id' => $stock->product_id,
          'name' => $stock->product->name,
          'price' => $stock->product->price,
          'image' => $stock->product->image,
          'available_qty' => $stock->quantity
        ];
      });

    return view('pages.manager.order.edit', compact('order', 'customers', 'products'));
  }


  public function update(Request $request, $id)
  {
    $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'products'    => 'required|array|min:1',
      'net_total'   => 'required|numeric',
    ]);

    $order = Order::findOrFail($id);
    if ($order->sr->branch_id != auth()->user()->branch_id) {
      return redirect()->back()->with('error', 'Unauthorized access.');
    }

    try {
      DB::transaction(function () use ($request, $order) {

        $order->update([
          'customer_id'    => $request->customer_id,
          'total_discount' => $request->total_discount ?? 0,
          'net_total'      => $request->net_total,
          'status'      => 'pending_sr',
        ]);

        $order->items()->delete();
        foreach ($request->products as $item) {

          $order->items()->create([
            'product_id'      => $item['product_id'],
            'quantity'        => $item['qty'],
            'price'           => $item['price'],
            'total'           => $item['qty'] * $item['price'],
            'discount_amount' => $item['discount'] ?? 0,
            'net_total'       => ($item['price'] * $item['qty']) - (($item['discount'] ?? 0) * $item['qty'])
          ]);
        }
      });

      return redirect()
        ->route('manager.order.index')
        ->with('success', 'Order BRS' . $order->id . ' updated successfully.');
    } catch (\Exception $e) {
      return back()
        ->with('error', 'Something went wrong: ' . $e->getMessage())
        ->withInput();
    }
  }


  public function reject($id)
  {
    $order = Order::findOrFail($id);

    $order->update([
      'status' => 'rejected'
    ]);

    return redirect()->route('dashboards')->with('success', "Order BRS{$id} has been rejected.");
  }

  public function approve($id)
  {
    $order = Order::findOrFail($id);

    $order->update([
      'status' => 'approved'
    ]);

    return back()->with('success', "Order BRS{$id} has been approved successfully.");
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
          'type'        => 'buy',
          'amount'      => $order->net_total,
          'due'         => $customer->due,
          'note'        => 'Order confirmed #' . $order->id,
        ]);

        $order->update([
          'status' => 'complete'
        ]);

        return [
          'details' => $customer,
          'previous_due' => $previous_due
        ];
      });

      $order->refresh();
      $order->load(['items.product.category', 'customer', 'sr']);

      $items = $order->items->sortBy(function ($item) {
        return $item->product->category->name ?? 'General';
      });
      $hasDiscount = $items->sum('discount') > 0;

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



  public function viewInvoice(Order $order)
  {
    $order->load(['items.product.category', 'customer', 'sr']);

    $items = $order->items->sortBy(function ($item) {
      return $item->product->category->name ?? 'General';
    });

    $hasDiscount = $items->sum('discount') > 0;

    $customerData = [
      'details' => $order->customer,
      'previous_due' => max(0, $order->customer->due - $order->net_total)
    ];

    return view('pages.manager.order.invoice', compact(
      'order',
      'customerData',
      'items',
      'hasDiscount'
    ));
  }
}
