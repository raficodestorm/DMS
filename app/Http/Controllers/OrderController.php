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

  public function searchProducts(Request $request)
  {
    $rawSearch = $_GET['search'] ?? $request->search ?? '';
    $search = trim($rawSearch);
    $isAll = $request->boolean('all') || (strlen($rawSearch) >= 2 && $search === '');

    if (!$isAll) {
      if (strlen($rawSearch) < 2 || strlen($search) < 2) {
        return response()->json([]);
      }
    }

    $branchId = auth()->user()?->branch_id;

    $query = Stock::with('product')
      ->whereHas('product');

    if ($branchId) {
      $query->where('branch_id', $branchId);
    }

    if (!$isAll && $search !== '') {
      $query->whereHas('product', function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%");
      });
    }

    $stocks = $query->limit(20)->get();

    if ($stocks->count() > 0) {
      $products = $stocks->map(fn($s) => [
        'id'            => $s->product_id,
        'name'          => $s->product?->name ?? '',
        'image'         => $s->product?->image,
        'available_qty' => $s->quantity ?? 0,
      ]);
      return response()->json($products);
    }

    $prodQuery = Product::query();
    if (!$isAll && $search !== '') {
      $prodQuery->where('name', 'like', "%{$search}%");
    }

    $products = $prodQuery->limit(20)->get()->map(fn($p) => [
      'id'            => $p->id,
      'name'          => $p->name ?? '',
      'image'         => $p->image,
      'available_qty' => 0,
    ]);

    return response()->json($products);
  }

  public function searchCustomers(Request $request)
  {
    $rawSearch = $_GET['search'] ?? $request->search ?? '';
    $search = trim($rawSearch);
    $isAll = $request->boolean('all') || (strlen($rawSearch) >= 2 && $search === '');

    if (!$isAll) {
      if (strlen($rawSearch) < 2 || strlen($search) < 2) {
        return response()->json([]);
      }
    }

    $branchId = auth()->user()?->branch_id;

    $query = Customer::query();

    if ($branchId) {
      $query->where('branch_id', $branchId);
    }

    if (!$isAll && $search !== '') {
      $query->where(function ($q) use ($search) {
        $q->where('shop_name', 'like', "%{$search}%")
          ->orWhere('phone', 'like', "%{$search}%")
          ->orWhere('manager', 'like', "%{$search}%");
      });
    }

    $customers = $query->orderBy('shop_name', 'asc')
      ->limit(50)
      ->get()
      ->map(fn($c) => [
        'id'        => $c->id,
        'shop_name' => $c->shop_name,
        'due'       => (float) ($c->due ?: 0),
      ]);

    return response()->json($customers);
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
        'customer_id' => ['required', 'exists:customers,id'],
        'products'    => ['required', 'array', 'min:1'],
        'net_total'   => ['required', 'numeric', 'min:0'],
    ]);

    $order = Order::with('sr')->findOrFail($id);

    /*
     * ---------------------------------------------------------
     * Authorization
     * ---------------------------------------------------------
     */
    if ($order->branch_id !== auth()->user()->branch_id) 
      {
        return redirect()
            ->back()
            ->with('error', 'Unauthorized access.');
    }

    try {

        DB::transaction(function () use ($request, $order) {

            /*
             * ---------------------------------------------------------
             * 1. Get deduction settings
             * ---------------------------------------------------------
             */
            $deductionSettings = DB::table('deductions')
                ->where('type', 'main')
                ->first();

            $globalRate = $request->boolean('apply_global')
                ? ($deductionSettings->customer_deduction ?? 0)
                : 0;

            $customRate = (float) ($request->applied_custom_deduction ?? 0);

            $totalDeductionPercent = $globalRate + $customRate;


            /*
             * ---------------------------------------------------------
             * 2. Get all product IDs
             * ---------------------------------------------------------
             */
            $productIds = collect($request->products)
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->values();


            /*
             * ---------------------------------------------------------
             * 3. Fetch products in ONE query
             * ---------------------------------------------------------
             */
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');


            /*
             * Make sure every requested product exists.
             */
            if ($products->count() !== $productIds->count()) {
                throw new \RuntimeException(
                    'One or more selected products no longer exist.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 4. Update Order
             * ---------------------------------------------------------
             */
            $order->update([
                'status'                     => 'pending_sr',
                'special_discount'           => $request->special_discount ?? 0,
                'discount_amount'            => $request->total_discount ?? 0,
                'net_total'                  => $request->net_total,
                'applied_deduction_percent' => $totalDeductionPercent,
                'note'                       => $request->note,
            ]);


            /*
             * ---------------------------------------------------------
             * 5. Delete old Order Items
             *
             * Since the update form represents the complete
             * current order, rebuilding the items is safe.
             * ---------------------------------------------------------
             */
            $order->items()->delete();


            /*
             * ---------------------------------------------------------
             * 6. Create new Order Items
             * ---------------------------------------------------------
             */
            foreach ($request->products as $item) {

                $productId = $item['product_id'];

                /** @var Product $product */
                $product = $products->get($productId);

                /*
                 * Quantity validation
                 */
                $qty = (int) ($item['qty'] ?? 0);

                if ($qty <= 0) {
                    throw new \RuntimeException(
                        "Invalid quantity for product ID: {$productId}"
                    );
                }


                /*
                 * -----------------------------------------------------
                 * Base selling price
                 * -----------------------------------------------------
                 */
                $basePrice = (float) ($item['price'] ?? 0);

                if ($basePrice < 0) {
                    throw new \RuntimeException(
                        "Invalid price for product ID: {$productId}"
                    );
                }


                /*
                 * -----------------------------------------------------
                 * Deduction
                 * -----------------------------------------------------
                 */
                $deductionAmount = round(
                    $basePrice * $totalDeductionPercent / 100,
                    2
                );


                /*
                 * Selling rate after deduction
                 * -----------------------------------------------------
                 */
                $sellingRate = round(
                    $basePrice - $deductionAmount,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Offer discount
                 * -----------------------------------------------------
                 */
                $offerDiscount = (float) ($item['discount'] ?? 0);

                if ($offerDiscount < 0) {
                    throw new \RuntimeException(
                        "Invalid discount for product ID: {$productId}"
                    );
                }


                /*
                 * -----------------------------------------------------
                 * Final item net amount
                 * -----------------------------------------------------
                 */
                $itemNetTotal = round(
                    ($sellingRate - $offerDiscount) * $qty,
                    2
                );


                /*
                 * Prevent negative item total
                 */
                if ($itemNetTotal < 0) {
                    throw new \RuntimeException(
                        "Discount cannot exceed selling price for product ID: {$productId}"
                    );
                }


                /*
                 * -----------------------------------------------------
                 * Purchase Cost
                 * -----------------------------------------------------
                 */
                $purchasePrice = (float) $product->purchase_price;

                $totalPurchaseCost = round(
                    $purchasePrice * $qty,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * PROFIT
                 *
                 * Profit = Revenue - Purchase Cost
                 * -----------------------------------------------------
                 */
                $profit = round(
                    $itemNetTotal - $totalPurchaseCost,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Create Order Item
                 * -----------------------------------------------------
                 */
                OrderItem::create([
                    'order_id'              => $order->id,
                    'product_id'            => $product->id,
                    'quantity'              => $qty,

                    'price'                 => $basePrice,

                    'unit_deduction_amount' => $deductionAmount,

                    'selling_rate'          => $sellingRate,

                    'discount_amount'       => $offerDiscount,

                    'net_total'             => $itemNetTotal,

                    'profit'                => $profit,
                ]);
            }
        });

        return redirect()
            ->route(auth()->user()->role . '.order.index')
            ->with(
                'success',
                'Order BRS' . $order->id . ' updated successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->with(
                'error',
                'Something went wrong while updating the order.'
            )
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

    $transactionOrder = Transaction::where('order_id', $order->id)
      ->where('type', 'buy')
      ->latest('id')
      ->first();

    if ($transactionOrder) {
      $previousDue = round((float) ($transactionOrder->due_before_transaction ?? 0), 2);
      $currentDue = round((float) ($transactionOrder->due_after_transaction ?? 0), 2);
    } else {
      $currentDue = round(
            (float) ($order->customer->due ?? 0),
            2
        );

        $previousDue = round(
            $currentDue - (float) $order->net_total,
            2
        );
    }

    $customerData = [
      'details' => $order->customer,
      'previous_due' => $previousDue,
      'current_due' => $currentDue
    ];

    return view("pages.manager.order.invoice", compact(
      'order',
      'customerData',
      'items',
      'hasDiscount'
    ));
  }
}
