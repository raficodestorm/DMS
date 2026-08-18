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
use Illuminate\Support\Facades\Auth;
use App\Notifications\SystemNotification;

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

    $products = Stock::with('product')
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->filter(fn($stock) => !is_null($stock->product))
      ->map(fn($stock) => [
        'id'            => $stock->product_id,
        'name'          => $stock->product->name,
        'price'         => $stock->product->price,
        'image'         => $stock->product->image,
        'available_qty' => $stock->quantity,
      ])
      ->values();

    return view('pages.manager.retail.create', compact( 'products'));
  }

  /**
   * Store a new retail order.
   */
  public function store(Request $request)
  {
    $request->validate([
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
          'sr_id'                     => null, // Retail orders don't have an SR
          'manager_id'                => $managerId,
          'branch_id'                 => $user->branch_id,
          'status'                    => 'delivered', // auto-approved
          'special_discount'          => $request->special_discount ?? 0,
          'discount_amount'           => $request->total_discount ?? 0,
          'net_total'                 => $request->net_total,
          'applied_deduction_percent' => $totalDeductionPercent,
          'note'                      => $request->note,
        ]);
        $branchId = auth()->user()->branch_id;
        foreach ($request->products as $item) {
          // Stock Check & Update
            $stock = Stock::where([
                'product_id' => $item['product_id'],
                'branch_id'  => $branchId
            ])->lockForUpdate()->first();

            if (!$stock) {
                throw new \Exception("Stock not found.");
            }

            if ($stock->quantity < $item['qty']) {
                throw new \Exception("Insufficient stock.");
            }

            $stock->decrement('quantity', $item['qty']);

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

        // Send Notification To Admin After Everything Stored
        $notificationData = [
            'title'   => 'New Retail Order Done',
            'message' => [
                'text' => 'A new retail order has been created by',
                'from' => Auth::user()->branch->name ?? 'Unknown Branch'
            ],
            'url'  => route('admin.order.show', $order->id),
            'type' => 'retail_order'
        ];

        $admins = \App\Models\User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification($notificationData));
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

    return view('pages.manager.retail.edit', compact('order', 'products'));
  }

  /**
   * Update an existing retail order.
   */
  public function update(Request $request, int $id)
{
    $request->validate([
        'products'    => 'required|array|min:1',
        'net_total'   => 'required|numeric|min:0',
    ]);

    try {

        return DB::transaction(function () use ($request, $id) {

            $user  = auth()->user();

            $order = Order::where('manager_id', $user->id)
                ->whereNull('sr_id')
                ->findOrFail($id);

            $customRate            = (float) ($request->applied_custom_deduction ?? 0);
            $totalDeductionPercent = min($customRate, 100);

            $order->update([
                'special_discount'          => $request->special_discount ?? 0,
                'discount_amount'           => $request->total_discount ?? 0,
                'net_total'                 => $request->net_total,
                'applied_deduction_percent' => $totalDeductionPercent,
                'note'                      => $request->note,
            ]);

            $branchId = auth()->user()->branch_id;

            // Restore Previous Stock
            foreach ($order->items as $oldItem) {

                $stock = Stock::where([
                    'product_id' => $oldItem->product_id,
                    'branch_id'  => $branchId
                ])->lockForUpdate()->first();

                if ($stock) {
                    $stock->increment('quantity', $oldItem->quantity);
                }
            }

            $order->items()->delete();

            foreach ($request->products as $item) {

                // Stock Check
                $stock = Stock::where([
                    'product_id' => $item['product_id'],
                    'branch_id'  => $branchId
                ])->lockForUpdate()->first();

                if (!$stock) {
                    throw new \Exception("Stock not found.");
                }

                if ($stock->quantity < $item['qty']) {
                    throw new \Exception("Insufficient stock.");
                }

                // Deduct New Stock
                $stock->decrement('quantity', $item['qty']);

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

        return redirect()->back()
            ->with('error', 'Something went wrong! ' . $e->getMessage());
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




  public function viewRetailInvoice(Order $order)
  {
    $order->load(['items.product.category', 'customer', 'manager']);

    $items = $order->items->sortBy(function ($item) {
      return $item->product->category->name ?? 'General';
    });

    $hasDiscount = $items->contains(function ($item) {
      return (float) $item->discount_amount > 0;
    });

    return view("pages.manager.order.retail-invoice", compact(
      'order',
      'items',
      'hasDiscount'
    ));
  }
}
