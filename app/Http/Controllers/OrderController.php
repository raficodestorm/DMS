<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
  /**
   * SR creates order
   */
  public function store(Request $request)
  {
    $request->validate([
      'customer_id' => 'required|exists:users,id',
      'manager_id' => 'required|exists:users,id',
      'products' => 'required|array'
    ]);

    DB::transaction(function () use ($request) {

      $order = Order::create([
        'customer_id' => $request->customer_id,
        'sr_id' => Auth::id(),
        'manager_id' => $request->manager_id,
        'status' => 'pending_manager',
        'total_amount' => 0,
        'discount_amount' => 0,
        'final_amount' => 0
      ]);

      $total = 0;
      $discount = 0;

      foreach ($request->products as $item) {

        $product = Product::with('offers')->findOrFail($item['product_id']);

        $qty = $item['qty'];
        $price = $product->price;
        $lineTotal = $price * $qty;

        // 🎁 Offer Apply
        $offer = $product->offers()
          ->whereDate('start_date', '<=', now())
          ->whereDate('end_date', '>=', now())
          ->first();

        if ($offer) {
          $discount += $offer->type === 'percentage'
            ? ($lineTotal * $offer->value / 100)
            : $offer->value;
        }

        OrderItem::create([
          'order_id' => $order->id,
          'product_id' => $product->id,
          'quantity' => $qty,
          'price' => $price,
          'total' => $lineTotal
        ]);

        $total += $lineTotal;
      }

      $order->update([
        'total_amount' => $total,
        'discount_amount' => $discount,
        'final_amount' => $total - $discount
      ]);

      // 🔔 Notify Manager
      $manager = \App\Models\User::find($request->manager_id);
      if ($manager) {
        $manager->notify(new \App\Notifications\OrderNotification($order));
      }
    });

    return back()->with('success', 'Order submitted successfully!');
  }

  /**
   * Manager confirms order
   */
  public function confirm($id)
  {
    $order = Order::findOrFail($id);

    // 🔐 Security: only assigned manager
    if ($order->manager_id !== Auth::id()) {
      abort(403);
    }

    $order->update(['status' => 'pending_admin']);

    return back()->with('success', 'Order sent to admin!');
  }

  /**
   * Admin approves order (stock out)
   */
  public function approve($id)
  {
    DB::transaction(function () use ($id) {

      $order = Order::with('items')->findOrFail($id);

      foreach ($order->items as $item) {

        $stock = Stock::where('product_id', $item->product_id)->first();

        if (!$stock || $stock->quantity < $item->quantity) {
          throw new \Exception('Insufficient stock for product ID: ' . $item->product_id);
        }

        $stock->decrement('quantity', $item->quantity);
      }

      $order->update([
        'status' => 'approved',
        'admin_id' => Auth::id()
      ]);
    });

    return back()->with('success', 'Order approved & stock updated!');
  }

  /**
   * Admin reject
   */
  public function reject($id)
  {
    $order = Order::findOrFail($id);
    $order->update(['status' => 'rejected']);

    return back()->with('error', 'Order rejected!');
  }
}
