<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deduction;
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
    $order = Order::with(['items.product.stock', 'customer'])->findOrFail($id);

    if ($order->sr->branch_id != auth()->user()->branch_id) {
      return redirect()->back()->with('error', 'Unauthorized access.');
    }

    $branchId = auth()->user()->branch_id;
    $customers = Customer::orderBy('shop_name', 'asc')->where('branch_id', $branchId)->get();

    // আপনার দেওয়া পদ্ধতি (নিরাপদ ভার্সন)
    $deductionSettings = Deduction::first();

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

    return view("pages." . auth()->user()->role . ".order.edit", compact('order', 'customers', 'products', 'deductionSettings'));
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
        // Get deduction percentage for audit
        $deductionSettings = DB::table('deductions')->where('type', 'main')->first();
        $globalRate = $request->has('apply_global') ? ($deductionSettings->customer_deduction ?? 0) : 0;
        $customRate = $request->applied_custom_deduction ?? 0;
        $totalDeductionPercent = $globalRate + $customRate;
        $order->update([
          'status'          => 'pending_sr',
          'special_discount' => $request->special_discount ?? 0,
          'discount_amount' => $request->total_discount,
          'net_total'       => $request->net_total,
          'applied_deduction_percent' => $totalDeductionPercent,
        ]);

        $order->items()->delete();
        foreach ($request->products as $item) {

          $basePrice = $item['price'];
          $deductionAmount = ($basePrice * $totalDeductionPercent / 100);
          $sellingRate = $basePrice - $deductionAmount;

          OrderItem::create([
            'order_id'              => $order->id,
            'product_id'            => $item['product_id'],
            'quantity'              => $item['qty'],
            'price'                 => $basePrice,
            'unit_deduction_amount' => $deductionAmount,
            'selling_rate'          => $sellingRate,
            'discount_amount'       => $item['discount'] ?? 0, // Offer discount
            'net_total'             => ($sellingRate - ($item['discount'] ?? 0)) * $item['qty']
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

    $hasDiscount = $items->contains(function ($item) {
      return (float) $item->discount_amount > 0;
    });

    // Current order transaction বের করো
    $transaction = Transaction::where('order_id', $order->id)
      ->where('type', 'buy')
      ->latest()
      ->first();

    $currentDue = $transaction?->due ?? $order->customer->due;
    $previousDue = max(0, $currentDue - $order->net_total);

    $customerData = [
      'details' => $order->customer,
      'previous_due' => $previousDue
    ];

    return view("pages.manager.order.invoice", compact(
      'order',
      'customerData',
      'items',
      'hasDiscount'
    ));
  }
}
