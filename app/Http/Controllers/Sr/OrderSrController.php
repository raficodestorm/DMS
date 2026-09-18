<?php

namespace App\Http\Controllers\Sr;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Services\OrderIdGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSrController extends Controller
{

  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Order::with(['customer', 'sr'])
      ->where('sr_id', $user->id)
      ->latest();

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    $orders = $query->paginate(20)->withQueryString();

    if ($request->ajax()) {
      return response()->json([
        'table'  => view('pages.sr.order.table', compact('orders'))->render(),
        'mobile' => view('pages.sr.order.mtable', compact('orders'))->render(),
      ]);
    }

    return view('pages.sr.order.index', compact('orders'));
  }

  public function fetchOrdersData(Request $request)
  {
    $user = auth()->user();

    $query = Order::with(['customer', 'sr'])
      ->where('sr_id', $user->id)
      ->latest();

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    $orders = $query->paginate(20)->withQueryString();

    return response()->json([
      'table'       => view('pages.sr.order.table', compact('orders'))->render(),
      'mobile'      => view('pages.sr.order.mtable', compact('orders'))->render(),
      'pagination'  => $orders->links()->toHtml(),
      'total_count' => $orders->total(),
    ]);
  }

  /**
   * Show the Online Order Search UI for SR.
   */
  public function onlineOrderIndex()
  {
    return view('pages.sr.order.onlineindex');
  }

  /**
   * AJAX: Search orders for SR.
   * - online orders: search across all branches
   * - field_order: only from SR's own branch
   */
  public function onlineOrderSearch(Request $request)
  {
    $user   = auth()->user();
    $search = trim($request->input('search', ''));

    if (empty($search)) {
      return response()->json(['orders' => [], 'message' => 'Enter an Order ID to search.']);
    }

    // Online orders: all branches — search by order_id only
    $onlineQuery = Order::with(['customer', 'branch'])
      ->where('order_type', 'online')
      ->where(function ($q) use ($search) {
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search);
      });

    // Field orders: only SR's branch — search by order_id only
    $fieldQuery = Order::with(['customer', 'branch'])
      ->where('order_type', 'field_order')
      ->where('branch_id', $user->branch_id)
      ->where(function ($q) use ($search) {
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search);
      });

    $onlineOrders = $onlineQuery->latest()->limit(15)->get();
    $fieldOrders  = $fieldQuery->latest()->limit(15)->get();

    $orders = $onlineOrders->merge($fieldOrders)->sortByDesc('created_at')->values();

    $result = $orders->map(function ($order) {
      return [
        'id'               => $order->id,
        'order_id'         => $order->order_id ?? ('BRS' . $order->id),
        'order_type'       => $order->order_type,
        'customer_id'      => $order->customer_id,
        'customer_name'    => $order->customer_name ?? ($order->customer->shop_name ?? ($order->customer->name ?? 'N/A')),
        'customer_phone'   => $order->customer_phone ?? ($order->customer->phone ?? 'N/A'),
        'country'          => $order->country ?? ($order->customer->country ?? 'N/A'),
        'city'             => $order->city ?? ($order->customer->city ?? 'N/A'),
        'address'          => $order->address ?? ($order->customer->address ?? 'N/A'),
        'note'             => $order->note,
        'branch'           => $order->branch->name ?? 'N/A',
        'net_total'        => number_format($order->net_total, 2),
        'payment_amount'   => number_format($order->payment_amount ?? 0, 2),
        'discount_amount'  => number_format($order->discount_amount ?? 0, 2),
        'special_discount' => number_format($order->special_discount ?? 0, 2),
        'payment_method'   => $order->payment_method ?? 'Cash on Delivery',
        'status'           => $order->status,
        'payment_status'   => $order->payment_status ?? 'unpaid',
        'date'             => $order->created_at->timezone(auth()->user()->timezone ?? 'Asia/Dhaka')->format('d M Y, h:i A'),
        'show_url'         => route('sr.order.show', $order->id),
        'delivered_url'    => route('sr.order.delivered', $order->id),
        'is_delivered'     => $order->status === 'delivered',
      ];
    });

    return response()->json([
      'orders'  => $result,
      'message' => $orders->isEmpty() ? 'No orders found for "' . $search . '".' : null,
    ]);
  }


  public function indexForCustomer()
  {
      $user = auth()->user();

      $orders = Order::with(['customer', 'sr'])
          ->where('customer_id', $user->customer_id)
          ->latest()
          ->paginate(15);

      return view('pages.customer.order.index', compact('orders'));
  }

  public function fetchCustomerOrdersData(Request $request)
  {
      $user = auth()->user();

      $query = Order::with(['customer', 'sr'])
          ->where('customer_id', $user->customer_id)
          ->latest();

      if ($request->filled('search')) {
          $search = trim($request->search);
          $query->where(function ($q) use ($search) {
              $q->where('order_id', 'like', "%{$search}%")
                ->orWhere('id', $search);
          });
      }

      if ($request->filled('status')) {
          $query->where('status', $request->status);
      }

      if ($request->filled('from_date')) {
          $query->whereDate('created_at', '>=', $request->from_date);
      }

      if ($request->filled('to_date')) {
          $query->whereDate('created_at', '<=', $request->to_date);
      }

      $orders = $query->paginate(15)->withQueryString();

      return response()->json([
          'table'       => view('pages.customer.order.table', compact('orders'))->render(),
          'mobile'      => view('pages.customer.order.mtable', compact('orders'))->render(),
          'pagination'  => $orders->links()->toHtml(),
          'total_count' => $orders->total(),
      ]);
  }

  public function showForCustomer($id)
  {
      $user = auth()->user();

      $order = Order::with(['customer', 'sr', 'items.product'])
          ->where('customer_id', $user->customer_id)
          ->findOrFail($id);

      return view('pages.customer.order.show', compact('order'));
  }

  public function allOrders()
  {
    $customers = Customer::with(['orders' => function ($query) {
      $query->where('status', '!=', 'rejected')->where('sr_id', auth()->user()->id);
    }])->get()->map(function ($customer) {
      $customer->total_order_amount = $customer->orders->sum('net_total');
      return $customer;
    });

    return view('pages.sr.order.all-orders', compact('customers'));
  }

  public function customerOrders($id)
  {
    $customer = Customer::findOrFail($id);

    $orders = Order::where('customer_id', $id)
      ->where('status', '!=', 'rejected')
      ->latest()
      ->paginate(15);

    return view('pages.sr.order.specific-index', compact('customer', 'orders'));
  }


  public function showForSr($id)
  {
    $order = Order::with(['customer', 'sr', 'supplier', 'items.product'])->findOrFail($id);

    return view('pages.sr.order.show', compact('order'));
  }

  public function delivered(Request $request, $id)
{
    $order = Order::with('customer')->findOrFail($id);

    $updateData = [
        'status'       => 'delivered',
        'delivered_by' => auth()->id(),
        'delivered_at' => now(),
    ];

    // Customer নেই OR customer group is retail
    if (
        is_null($order->customer_id) ||
        $order->customer?->customer_group === 'retail'
    ) {
        $updateData += [
            'payment_status' => 'paid',
            'payment_amount' => $order->net_total,
        ];
    }

    $order->update($updateData);

    $orderNumber = $order->order_id ?? "BRS{$id}";

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => "Order {$orderNumber} has been delivered successfully.",
            'status'  => 'delivered',
        ]);
    }

    return back()->with(
        'success',
        "Order {$orderNumber} has been delivered successfully."
    );
}



  public function create()
  {
    $branchId = auth()->user()->branch_id;
    $suppliers = \App\Models\Supplier::with('deductions')->orderBy('company_name', 'asc')->get();
    $deductionSettings = \App\Models\Deduction::where('type', 'main')->first() ?? \App\Models\Deduction::first();
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

    return view('pages.sr.order.create', compact('customers', 'suppliers', 'products', 'deductionSettings'));
  }

  public function searchProducts(Request $request)
  {
    $rawSearch = $_GET['search'] ?? $request->search ?? '';
    $search = trim($rawSearch);
    $supplierId = $request->supplier_id;
    $isAll = $request->boolean('all') || (strlen($rawSearch) >= 2 && $search === '');

    if (!$isAll) {
      if (strlen($rawSearch) < 2 || strlen($search) < 2) {
        return response()->json([]);
      }
    }

    $branchId = auth()->user()?->branch_id;

    $query = Stock::with('product')
      ->whereHas('product', function ($q) use ($supplierId) {
        if ($supplierId) {
          $q->where('supplier_id', $supplierId);
        }
      });

    if ($branchId) {
      $query->where('branch_id', $branchId);
    }

    if (!$isAll && $search !== '') {
      $query->whereHas('product', function ($q) use ($search, $supplierId) {
        $q->where('name', 'like', "%{$search}%");
        if ($supplierId) {
          $q->where('supplier_id', $supplierId);
        }
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
    if ($supplierId) {
      $prodQuery->where('supplier_id', $supplierId);
    }
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

    $branchId = auth()->user()->branch_id;

    $query = Customer::where('branch_id', $branchId);

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
        'manager'   => $c->manager,
        'phone'     => $c->phone,
        'due'       => (float) ($c->due ?: 0),
      ]);

    return response()->json($customers);
  }

  // discount check
  public function getProductData($id)
  {
    $product = Product::findOrFail($id);
    $today = Carbon::today();

    $offer = Offer::where('product_id', $id)
      ->where('status', 1)
      ->where('customer_type', 'wholesale')
      ->whereDate('start_date', '<=', $today)
      ->whereDate('end_date', '>=', $today)
      ->first();

    return response()->json([
      'price' => $product->price,
      'discount' => $offer ? $offer->discount_amount : 0,
      'discount_type' => $offer ? $offer->type : 'fixed'
    ]);
  }




  public function store(Request $request)
{
    $request->validate([
        'customer_id' => ['required', 'exists:customers,id'],
        'supplier_id' => ['nullable', 'exists:suppliers,id'],
        'products'    => ['required', 'array', 'min:1'],
        'net_total'   => ['required', 'numeric', 'min:0'],
    ]);

    try {
        return DB::transaction(function () use ($request) {

            $user = auth()->user();

            $branchId = $user->branch_id;

            /*
             * ---------------------------------------------------------
             * 1. Get manager
             * ---------------------------------------------------------
             */
            $manager = User::query()
                ->where('branch_id', $branchId)
                ->where('role', 'manager')
                ->first();

            $managers = User::query()
                ->where('branch_id', $branchId)
                ->where('role', 'manager')
                ->get();

            /*
             * ---------------------------------------------------------
             * 1-B. Get Customer (to snapshot contact info into order)
             * ---------------------------------------------------------
             */
            $customer = Customer::query()
                ->whereKey($request->customer_id)
                ->firstOrFail();

            /*
             * ---------------------------------------------------------
             * 2. Get deduction settings for selected supplier
             * ---------------------------------------------------------
             */
            $supplierId = $request->supplier_id;
            $deductionSettings = null;
            if ($supplierId) {
                $deductionSettings = \App\Models\Deduction::where('supplier_id', $supplierId)->where('type', 'main')->first()
                    ?? \App\Models\Deduction::where('supplier_id', $supplierId)->first();
            }
            if (!$deductionSettings) {
                $deductionSettings = \App\Models\Deduction::where('type', 'main')->first()
                    ?? \App\Models\Deduction::first();
            }

            $globalRate = $request->boolean('apply_global')
                ? ($deductionSettings->customer_deduction ?? 0)
                : 0;

            $customRate = (float) ($request->applied_custom_deduction ?? 0);

            $totalDeductionPercent = $globalRate + $customRate;

            /*
             * ---------------------------------------------------------
             * 3. Get all product IDs from request
             * ---------------------------------------------------------
             */
            $productIds = collect($request->products)
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->values();

            /*
             * ---------------------------------------------------------
             * 4. Fetch all products in ONE query.
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
             * 5. Create Order
             * ---------------------------------------------------------
             */
            $orderId = app(OrderIdGeneratorService::class)->generate();

            $order = Order::create([
                'order_id'                    => $orderId,
                'customer_id'                 => $request->customer_id,
                'supplier_id'                 => $supplierId,
                'sr_id'                       => $user->id,
                'manager_id'                  => $manager?->id,
                'branch_id'                   => $branchId,
                'status'                      => 'pending_sr',
                'special_discount'            => $request->special_discount ?? 0,
                'discount_amount'             => $request->total_discount ?? 0,
                'net_total'                   => (int) round($request->net_total),
                'applied_deduction_percent'   => $totalDeductionPercent,
                'note'                        => $request->note,
                'order_type'                  => 'field_order',
                // Snapshot customer info at time of order
                'customer_name'               => $customer->shop_name,
                'customer_phone'              => $customer->phone,
                'country'                     => $customer->country,
                'city'                        => $customer->city,
                'address'                     => $customer->address,
            ]);

            /*
             * ---------------------------------------------------------
             * 6. Create Order Items
             * ---------------------------------------------------------
             */
            foreach ($request->products as $item) {

                $productId = $item['product_id'];

                /** @var Product $product */
                $product = $products->get($productId);

                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    throw new \RuntimeException(
                        "Invalid quantity for product ID: {$productId}"
                    );
                }

                /*
                 * Base selling price
                 */
                $basePrice = (float) $item['price'];

                /*
                 * Deduction
                 */
                $deductionAmount = round(
                    $basePrice * $totalDeductionPercent / 100,
                    2
                );

                /*
                 * Selling rate after deduction
                 */
                $sellingRate = (int) round(
                    $basePrice - $deductionAmount
                );

                /*
                 * Offer discount
                 */
                $offerDiscount = (float) ($item['discount'] ?? 0);

                /*
                 * Final item net amount
                 */
                $itemNetTotal = round(
                    ($sellingRate - $offerDiscount) * $qty,
                    2
                );

                /*
                 * -----------------------------------------------------
                 * Purchase cost
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
                 * Standard formula:
                 *
                 * Profit = Revenue - Cost
                 * -----------------------------------------------------
                 */
                $profit = round(
                    $itemNetTotal - $totalPurchaseCost,
                    2
                );

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

            /*
             * ---------------------------------------------------------
             * 7. Manager notification
             * ---------------------------------------------------------
             */
            foreach ($managers as $manager) {

                $notificationData = [
                    'title'   => 'New Order Received',
                    'message' => [
                        'text' => 'A new order has been placed by',
                        'from' => $user->username,
                    ],
                    'url'     => route(
                        'manager.order.show',
                        $order->id
                    ),
                    'type'    => 'new_order',
                ];

                $manager->notify(
                    new \App\Notifications\SystemNotification(
                        $notificationData
                    )
                );
            }

            return redirect()
                ->route('sr.order.index')
                ->with('success', 'Order requested successfully!');
        });

    } catch (\Throwable $e) {

        report($e);

        return redirect()
            ->back()
            ->with(
                'error',
                'Something went wrong while creating the order.'
            );
    }
}

}
