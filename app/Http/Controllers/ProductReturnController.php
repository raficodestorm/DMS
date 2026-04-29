<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductReturnController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'order_id'    => 'nullable|exists:orders,id',
      'product_id'  => 'required|array|min:1',
      'product_id.*' => 'exists:products,id',
      'quantity'    => 'required|array|min:1',
      'quantity.*'  => 'required|integer|min:1',
      'unit_price'  => 'required|array|min:1',
      'unit_price.*' => 'required|numeric|min:0',
    ]);

    try {
      return DB::transaction(function () use ($request) {
        $totalAmount = 0;

        // ১. মাস্টার টেবিল এন্ট্রি
        $return = ProductReturn::create([
          'customer_id'  => $request->customer_id,
          'sr_id'        => auth()->id(),
          'order_id'     => $request->order_id,
          'total_amount' => 0, // পরে আপডেট হচ্ছে
          'reason'       => $request->reason,
          'status'       => 'pending',
        ]);

        // ২. আইটেম টেবিল এন্ট্রি
        foreach ($request->product_id as $key => $productId) {
          $subtotal = $request->quantity[$key] * $request->unit_price[$key];

          $return->items()->create([
            'product_id' => $productId,
            'quantity'   => $request->quantity[$key],
            'unit_price' => $request->unit_price[$key],
            'subtotal'   => $subtotal,
          ]);

          $totalAmount += $subtotal;
        }

        // ৩. টোটাল অ্যামাউন্ট আপডেট
        $return->update(['total_amount' => $totalAmount]);

        return redirect()->route('sr.returns.index')->with('success', 'Return request sent!');
      });
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }




  public function approve(ProductReturn $productReturn)
  {
    if ($productReturn->status === 'complete') {
      return back()->with('error', 'Already approved!');
    }

    try {
      return DB::transaction(function () use ($productReturn) {

        // কাস্টমারের ব্রাঞ্চ আইডি বের করা
        $branchId = $productReturn->customer->branch_id;

        // ১. ইনভেন্টরি এবং অর্ডার আইটেম আপডেট
        foreach ($productReturn->items as $item) {

          // নির্দিষ্ট ব্রাঞ্চের স্টক বাড়ানো (Increment Branch Stock)
          // এখানে ProductStock মডেল ব্যবহার করা হয়েছে (আপনার টেবিল নাম অনুযায়ী পরিবর্তন করে নিবেন)
          $branchStock = Stock::where('product_id', $item->product_id)
            ->where('branch_id', $branchId)
            ->first();

          if ($branchStock) {
            $branchStock->increment('stock', $item->quantity);
          } else {
            // যদি ওই ব্রাঞ্চে আগে এই প্রোডাক্টের কোনো এন্ট্রি না থাকে, তবে নতুন এন্ট্রি তৈরি হবে
            Stock::create([
              'product_id' => $item->product_id,
              'branch_id'  => $branchId,
              'stock'      => $item->quantity
            ]);
          }

          // মেইন প্রোডাক্ট টেবিলের টোটাল স্টকও আপডেট করা (অপশনাল, যদি আপনি গ্লোবাল স্টক রাখেন)
          $item->product->increment('stock', $item->quantity);

          // ২. যদি অর্ডার আইডি থাকে, তবে ওই অর্ডারের আইটেম লিস্ট আপডেট
          if ($productReturn->order_id) {
            $orderItem = OrderItem::where('order_id', $productReturn->order_id)
              ->where('product_id', $item->product_id)
              ->first();

            if ($orderItem) {
              $orderItem->decrement('quantity', $item->quantity);
              $orderItem->decrement('subtotal', $item->subtotal);
            }
          }
        }

        // ৩. কাস্টমার ডিউ আপডেট (Decrement Due)
        $productReturn->customer->decrement('due', $productReturn->total_amount);

        // ৪. যদি অর্ডার আইডি থাকে, তবে মেইন অর্ডার টেবিলের টোটাল আপডেট
        if ($productReturn->order_id) {
          $order = $productReturn->order;
          if ($order) {
            $order->decrement('total_amount', $productReturn->total_amount);
          }
        }

        // ৫. রিটার্ন স্ট্যাটাস কমপ্লিট করা
        $productReturn->update(['status' => 'complete']);

        return back()->with('success', 'Approved! Branch Stock, Order & Customer Due updated.');
      });
    } catch (\Exception $e) {
      return back()->with('error', 'Processing failed: ' . $e->getMessage());
    }
  }


















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

    return view("pages." . auth()->user()->role . ".order.edit", compact('order', 'customers', 'products'));
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
        ->route(auth()->user()->role . ".order.index")
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

    return back()->with('success', "Order BRS{$id} has been rejected.");
  }

  public function approve($id)
  {
    $order = Order::findOrFail($id);

    $order->update([
      'status' => 'approved'
    ]);

    return back()->with('success', "Order BRS{$id} has been approved successfully.");
  }



  public function viewInvoice(Order $order)
  {
    $order->load(['items.product.category', 'customer', 'sr']);

    $items = $order->items->sortBy(function ($item) {
      return $item->product->category->name ?? 'General';
    });

    $hasDiscount = $items->sum('discount_amount') > 0;

    $customerData = [
      'details' => $order->customer,
      'previous_due' => max(0, $order->customer->due - $order->net_total)
    ];

    return view("pages.manager.order.invoice", compact(
      'order',
      'customerData',
      'items',
      'hasDiscount'
    ));
  }
}
