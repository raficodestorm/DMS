<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetailOrderController extends Controller
{
  /**
   * Display all retail orders created by this manager directly.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Order::with(['customer'])
      ->where('manager_id', $user->id)
      ->whereNull('sr_id') // Identifying retail orders where manager is the creator and no SR is assigned
      ->latest();

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        if (str_starts_with($search, 'BRS')) {
          $q->where('id', str_replace('BRS', '', $search));
        } else {
          $q->where('id', $search)
            ->orWhereHas('customer', fn($c) => $c->where('shop_name', 'like', "%{$search}%"));
        }
      });
    }

    $orders = $query->paginate(20)->withQueryString();

    return view('pages.manager.retail.index', compact('orders'));
  }

  /**
   * Show the retail order creation form.
   */
  public function create()
  {
    $user     = auth()->user();
    $branchId = $user->branch_id;

    $customers = Customer::where('branch_id', $branchId)
      ->orderBy('shop_name', 'asc')
      ->get();

    $products = Stock::with('product')
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->map(fn($stock) => [
        'id'            => $stock->product_id,
        'name'          => $stock->product->name,
        'price'         => $stock->product->price,
        'image'         => $stock->product->image,
        'available_qty' => $stock->quantity,
      ]);

    return view('pages.manager.retail.create', compact('customers', 'products'));
  }

  /**
   * Store a new retail order.
   */
  public function store(Request $request)
  {
    $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'products'    => 'required|array|min:1',
      'net_total'   => 'required|numeric|min:0',
    ]);

    try {
      return DB::transaction(function () use ($request) {
        $user     = auth()->user();
        $managerId = $user->id;

        // Retail: only custom deduction
        $customRate = (float) ($request->applied_custom_deduction ?? 0);
        $totalDeductionPercent = min($customRate, 100);

        $order = Order::create([
          'customer_id'               => $request->customer_id,
          'sr_id'                     => null, // Retail orders don't have an SR
          'manager_id'                => $managerId,
          'status'                    => 'approved', // auto-approved
          'special_discount'          => $request->special_discount ?? 0,
          'discount_amount'           => $request->total_discount ?? 0,
          'net_total'                 => $request->net_total,
          'applied_deduction_percent' => $totalDeductionPercent,
          'note'                      => $request->note,
        ]);

        foreach ($request->products as $item) {
          $basePrice       = (float) $item['price'];
          $deductionAmount = $basePrice * $totalDeductionPercent / 100;
          $sellingRate     = $basePrice - $deductionAmount;

          OrderItem::create([
            'order_id'              => $order->id,
            'product_id'            => $item['product_id'],
            'quantity'              => $item['qty'],
            'price'                 => $basePrice,
            'unit_deduction_amount' => $deductionAmount,
            'selling_rate'          => $sellingRate,
            'discount_amount'       => $item['discount'] ?? 0,
            'net_total'             => ($sellingRate - ($item['discount'] ?? 0)) * $item['qty'],
          ]);
        }

        return redirect()
          ->route('manager.retail.index')
          ->with('success', "Retail Order #BRS{$order->id} created successfully!");
      });
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
    }
  }

  /**
   * Show a single retail order detail.
   */
  public function show(int $id)
  {
    $user  = auth()->user();
    $order = Order::with(['customer', 'items.product'])
      ->where('manager_id', $user->id)
      ->whereNull('sr_id')
      ->findOrFail($id);

    return view('pages.manager.retail.show', compact('order'));
  }

  /**
   * Show the edit form for a retail order.
   */
  public function edit(int $id)
  {
    $user     = auth()->user();
    $branchId = $user->branch_id;

    $order = Order::with('items.product')
      ->where('manager_id', $user->id)
      ->whereNull('sr_id')
      ->whereNotIn('status', ['complete', 'delivered'])
      ->findOrFail($id);

    $customers = Customer::where('branch_id', $branchId)
      ->orderBy('shop_name', 'asc')
      ->get();

    $products = Stock::with('product')
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->map(fn($stock) => [
        'id'            => $stock->product_id,
        'name'          => $stock->product->name,
        'price'         => $stock->product->price,
        'image'         => $stock->product->image,
        'available_qty' => $stock->quantity,
      ]);

    return view('pages.manager.retail.edit', compact('order', 'customers', 'products'));
  }

  /**
   * Update an existing retail order.
   */
  public function update(Request $request, int $id)
  {
    $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'products'    => 'required|array|min:1',
      'net_total'   => 'required|numeric|min:0',
    ]);

    try {
      return DB::transaction(function () use ($request, $id) {
        $user  = auth()->user();
        $order = Order::where('manager_id', $user->id)
          ->whereNull('sr_id')
          ->whereNotIn('status', ['complete', 'delivered'])
          ->findOrFail($id);

        $customRate            = (float) ($request->applied_custom_deduction ?? 0);
        $totalDeductionPercent = min($customRate, 100);

        $order->update([
          'customer_id'               => $request->customer_id,
          'special_discount'          => $request->special_discount ?? 0,
          'discount_amount'           => $request->total_discount ?? 0,
          'net_total'                 => $request->net_total,
          'applied_deduction_percent' => $totalDeductionPercent,
          'note'                      => $request->note,
        ]);

        $order->items()->delete();

        foreach ($request->products as $item) {
          $basePrice       = (float) $item['price'];
          $deductionAmount = $basePrice * $totalDeductionPercent / 100;
          $sellingRate     = $basePrice - $deductionAmount;

          OrderItem::create([
            'order_id'              => $order->id,
            'product_id'            => $item['product_id'],
            'quantity'              => $item['qty'],
            'price'                 => $basePrice,
            'unit_deduction_amount' => $deductionAmount,
            'selling_rate'          => $sellingRate,
            'discount_amount'       => $item['discount'] ?? 0,
            'net_total'             => ($sellingRate - ($item['discount'] ?? 0)) * $item['qty'],
          ]);
        }

        return redirect()
          ->route('manager.retail.index')
          ->with('success', "Retail Order #BRS{$order->id} updated successfully!");
      });
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
    }
  }

  /**
   * Delete a retail order.
   */
  public function destroy(int $id)
  {
    $user  = auth()->user();
    $order = Order::where('manager_id', $user->id)
      ->whereNull('sr_id')
      ->whereNotIn('status', ['complete', 'delivered'])
      ->findOrFail($id);

    $order->delete();

    return redirect()
      ->route('manager.retail.index')
      ->with('success', "Retail Order #BRS{$id} deleted.");
  }

  /**
   * Return product price + active offer data (JSON)
   */
  public function getProductData(int $id)
  {
    $product = Product::findOrFail($id);
    $today   = Carbon::today();

    $offer = Offer::where('product_id', $id)
      ->where('status', 1)
      ->whereDate('start_date', '<=', $today)
      ->whereDate('end_date', '>=', $today)
      ->first();

    return response()->json([
      'price'         => $product->price,
      'discount'      => $offer ? $offer->discount_amount : 0,
      'discount_type' => $offer ? $offer->type : 'fixed',
    ]);
  }
}
