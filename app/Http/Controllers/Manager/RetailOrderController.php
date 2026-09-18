<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Deduction;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use App\Services\OrderIdGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SystemNotification;

class RetailOrderController extends Controller
{
  /**
   * Display the retail orders page UI (no data loaded initially).
   */
  public function index(Request $request)
  {
    return view('pages.manager.retail.index');
  }

  /**
   * Fetch retail orders data via AJAX with filters.
   */
  public function fetchData(Request $request)
  {
    $user = auth()->user();

    $query = Order::with(['customer'])
      ->where('branch_id', $user->branch_id)
      ->where('order_type', 'retail')
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
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search)
          ->orWhereHas('customer', fn($c) => $c->where('shop_name', 'like', "%{$search}%"));
      });
    }

    $orders = $query->paginate(20)->withQueryString();

    return response()->json([
      'table'      => view('pages.manager.retail.table',  compact('orders'))->render(),
      'mobile'     => view('pages.manager.retail.mtable', compact('orders'))->render(),
      'total'      => $orders->total(),
      'pagination' => (string) $orders->links(),
    ]);
  }

  /**
   * Show the POS terminal interface for retail sales.
   */
  public function pos()
  {
    $user     = auth()->user();
    $branchId = $user->branch_id;

    $deductions = Deduction::pluck('retail_deduction', 'supplier_id');
    $today      = Carbon::today();

    $stocks = Stock::with(['product.category', 'product.supplier', 'product.activeRetailOffer'])
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->filter(fn($stock) => !is_null($stock->product));

    $categories = collect();

    $products = $stocks->map(function ($stock) use ($deductions, &$categories) {
      $product         = $stock->product;
      $basePrice       = (float) $product->price;
      $supplierId      = $product->supplier_id;
      $retailDeduction = isset($deductions[$supplierId]) ? (float) $deductions[$supplierId] : 0.0;
      $deductionAmount = ($basePrice * $retailDeduction) / 100;
      $sellingRate     = max(0, $basePrice - $deductionAmount);

      // Offer check
      $offer = $product->activeRetailOffer;
      $offerDiscount = 0.0;
      $offerText = null;

      if ($offer) {
        if ($offer->type === 'percentage') {
          $offerDiscount = round($sellingRate * ((float) $offer->discount_amount / 100), 2);
          $formattedAmt  = ((float) $offer->discount_amount == (int) $offer->discount_amount) ? (int) $offer->discount_amount : (float) $offer->discount_amount;
          $offerText     = $formattedAmt . '% OFF';
        } elseif ($offer->type === 'fixed') {
          $offerDiscount = (float) $offer->discount_amount;
          $formattedAmt  = ((float) $offer->discount_amount == (int) $offer->discount_amount) ? (int) $offer->discount_amount : number_format($offer->discount_amount, 2);
          $offerText     = '৳' . $formattedAmt . ' OFF';
        } elseif ($offer->type === 'free_shipping') {
          $offerText     = 'Free Shipping';
        }
      }

      $catId   = $product->category_id ?? 0;
      $catName = $product->category->name ?? 'General';
      if ($catId && !$categories->has($catId)) {
        $categories->put($catId, $catName);
      }

      return [
        'id'               => $product->id,
        'name'             => $product->name,
        'category_id'      => $catId,
        'category_name'    => $catName,
        'price'            => $basePrice,
        'retail_deduction' => $retailDeduction,
        'selling_rate'     => round($sellingRate, 2),
        'has_offer'        => !is_null($offer),
        'offer_text'       => $offerText,
        'offer_discount'   => $offerDiscount,
        'offer_type'       => $offer ? $offer->type : 'fixed',
        'image'            => $product->image,
        'available_qty'    => $stock->quantity,
      ];
    })->values();

    return view('pages.manager.retail.pos', compact('products', 'categories'));
  }

  /**
   * Show the retail order creation form.
   */
  public function create()
  {
    $user     = auth()->user();
    $branchId = $user->branch_id;

    $deductions = Deduction::pluck('retail_deduction', 'supplier_id');

    $products = Stock::with(['product.supplier'])
      ->where('branch_id', $branchId)
      ->where('quantity', '>', 0)
      ->get()
      ->filter(fn($stock) => !is_null($stock->product))
      ->map(function ($stock) use ($deductions) {
        $product         = $stock->product;
        $basePrice       = (float) $product->price;
        $supplierId      = $product->supplier_id;
        $retailDeduction = isset($deductions[$supplierId]) ? (float) $deductions[$supplierId] : 0.0;
        $deductionAmount = ($basePrice * $retailDeduction) / 100;
        $sellingRate     = max(0, $basePrice - $deductionAmount);

        return [
          'id'               => $product->id,
          'name'             => $product->name,
          'price'            => $basePrice,
          'retail_deduction' => $retailDeduction,
          'selling_rate'     => round($sellingRate, 2),
          'image'            => $product->image,
          'available_qty'    => $stock->quantity,
        ];
      })
      ->values();

    return view('pages.manager.retail.create', compact('products'));
  }

  /**
   * Store a new retail order.
   */
  public function store(Request $request)
  {
    $request->validate([
      'products'        => 'required|array|min:1',
      'net_total'       => 'required|numeric|min:0',
      'customer_name'   => 'nullable|string|max:255',
      'customer_phone'  => 'nullable|string|max:50',
      'special_discount'=> 'nullable|numeric|min:0',
      'note'            => 'nullable|string',
    ]);

    try {
      return DB::transaction(function () use ($request) {
        $user      = auth()->user();
        $managerId = $user->id;
        $branchId  = $user->branch_id;

        $orderId = app(OrderIdGeneratorService::class)->generate();

        $customerName  = $request->filled('customer_name') ? trim($request->customer_name) : 'Retail Customer';
        $customerPhone = $request->filled('customer_phone') ? trim($request->customer_phone) : null;

        $order = Order::create([
          'customer_id'               => null,
          'order_id'                  => $orderId,
          'sr_id'                     => null,
          'manager_id'                => $managerId,
          'branch_id'                 => $branchId,
          'status'                    => 'delivered',
          'special_discount'          => (float) ($request->special_discount ?? 0),
          'discount_amount'           => (float) ($request->total_discount ?? 0),
          'net_total'                 => 0,
          'applied_deduction_percent' => null,
          'note'                      => $request->note,
          'order_type'                => 'retail',
          'payment_status'            => 'paid',
          'payment_amount'            => 0,
          'payment_method'            => $request->payment_method ?? 'Cash',
          'customer_name'             => $customerName,
          'customer_phone'            => $customerPhone,
        ]);

        // Pre-fetch all products with supplier
        $productIds = collect($request->products)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $supplierIds = $products->pluck('supplier_id')->filter()->unique()->values();
        $deductions = Deduction::whereIn('supplier_id', $supplierIds)->pluck('retail_deduction', 'supplier_id');

        $totalNetTotal = 0;
        $totalOfferDiscount = 0;

        foreach ($request->products as $item) {
          $product = $products->get($item['product_id']);
          if (!$product) {
              throw new \Exception("Product ID {$item['product_id']} not found.");
          }

          // Stock Check & Update
          $stock = Stock::where([
              'product_id' => $item['product_id'],
              'branch_id'  => $branchId,
          ])->lockForUpdate()->first();

          if (!$stock) {
              throw new \Exception("Stock not found for product {$product->name}.");
          }

          if ($stock->quantity < $item['qty']) {
              throw new \Exception("Insufficient stock for product {$product->name}.");
          }

          $stock->decrement('quantity', $item['qty']);

          $qty              = (int) $item['qty'];
          $basePrice        = (float) $product->price;
          $deductionPercent = isset($deductions[$product->supplier_id]) ? (float) $deductions[$product->supplier_id] : 0.0;
          $deductionAmount  = round($basePrice * ($deductionPercent / 100), 2);
          $sellingRate      = round(max(0, $basePrice - $deductionAmount), 2);

          $offerDiscount    = (float) ($item['discount'] ?? 0);
          $itemNetTotal     = round(max(0, ($sellingRate - $offerDiscount) * $qty), 2);

          // Profit = Revenue - Cost
          $purchasePrice     = (float) ($product->purchase_price ?? 0);
          $totalPurchaseCost = round($purchasePrice * $qty, 2);
          $profit            = round($itemNetTotal - $totalPurchaseCost, 2);

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

          $totalNetTotal += $itemNetTotal;
          $totalOfferDiscount += ($offerDiscount * $qty);
        }

        $specialDiscount = (float) ($request->special_discount ?? 0);
        $finalNetTotal   = (int) round(max(0, $totalNetTotal - $specialDiscount));
        $totalDiscount   = (int) round($totalOfferDiscount + $specialDiscount);

        $order->update([
            'discount_amount'  => $totalDiscount,
            'special_discount' => $specialDiscount,
            'net_total'        => $finalNetTotal,
            'payment_amount'   => $finalNetTotal,
        ]);

        // Notify admins
        $notificationData = [
            'title'   => 'New Retail Order Done',
            'message' => [
                'text' => 'A new retail order has been created by',
                'from' => Auth::user()->branch->name ?? 'Unknown Branch',
            ],
            'url'  => route('admin.order.show', $order->id),
            'type' => 'retail_order',
        ];

        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification($notificationData));
        }

        return redirect()
          ->route('manager.order.view_retail_invoice', $order->id)
          ->with('success', "Retail Order #{$orderId} created successfully!");
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
    $product = Product::with('supplier')->findOrFail($id);
    $deduction = Deduction::where('supplier_id', $product->supplier_id)->first();
    $retailDeduction = $deduction ? (float) $deduction->retail_deduction : 0.0;
    $basePrice = (float) $product->price;
    $deductionAmount = ($basePrice * $retailDeduction) / 100;
    $sellingRate = max(0, $basePrice - $deductionAmount);

    $today = Carbon::today();
    $offer = Offer::where('product_id', $id)
      ->where('status', 1)
      ->where('customer_type', 'retail')
      ->whereDate('start_date', '<=', $today)
      ->whereDate('end_date', '>=', $today)
      ->first();

    return response()->json([
      'price'             => $product->price,
      'retail_deduction'  => $retailDeduction,
      'selling_rate'      => round($sellingRate, 2),
      'discount'          => $offer ? $offer->discount_amount : 0,
      'discount_type'     => $offer ? $offer->type : 'fixed',
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
