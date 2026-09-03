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
        if (str_starts_with($search, 'BRS')) {
          $id = str_replace('BRS', '', $search);
          $q->where('id', $id);
        } else {
          $q->where('id', $search)
            ->orWhereHas('customer', function ($customer) use ($search) {
              $customer->where('shop_name', 'like', "%{$search}%");
            });
        }
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


  public function indexForCustomer()
  {
      $user = auth()->user();

      $orders = Order::with(['customer', 'sr'])
          ->where('customer_id', $user->customer_id)
          ->latest()
          ->paginate(15);

      return view('pages.customer.order.index', compact('orders'));
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
    $order = Order::with(['customer', 'sr', 'items.product'])->findOrFail($id);

    return view('pages.sr.order.show', compact('order'));
  }

  public function delivered($id)
  {
    $order = Order::findOrFail($id);

    $order->update([
      'status' => 'delivered'
    ]);

    return back()->with('success', "Order BRS{$id} has been delivered successfully.");
  }

  public function create()
  {
    $branchId = auth()->user()->branch_id;
    $deductionSettings = \DB::table('deductions')->where('type', 'main')->first();
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

    return view('pages.sr.order.create', compact('customers', 'products', 'deductionSettings'));
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
             * 2. Get deduction settings
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
            $order = Order::create([
                'customer_id'                 => $request->customer_id,
                'sr_id'                       => $user->id,
                'manager_id'                  => $manager?->id,
                'branch_id'                   => $branchId,
                'status'                      => 'pending_sr',
                'special_discount'            => $request->special_discount ?? 0,
                'discount_amount'             => $request->total_discount ?? 0,
                'net_total'                   => $request->net_total,
                'applied_deduction_percent'  => $totalDeductionPercent,
                'note'                        => $request->note,
                'order_type'                  => 'field_order'
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
                $sellingRate = round(
                    $basePrice - $deductionAmount,
                    2
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



  // public function store(Request $request)
  // {

  //   $request->validate([
  //     'customer_id' => 'required|exists:customers,id',
  //     'products'    => 'required|array|min:1',
  //     'net_total'   => 'required|numeric'
  //   ]);

  //   try {
  //     return DB::transaction(function () use ($request) {
  //       $user = auth()->user();
  //       $branchId = $user->branch_id;

  //       $manager = User::where('branch_id', $branchId)
  //         ->where('role', 'manager')
  //         ->first();

        
  //       $deductionSettings = DB::table('deductions')->where('type', 'main')->first();
  //       $globalRate = $request->has('apply_global') ? ($deductionSettings->customer_deduction ?? 0) : 0;
  //       $customRate = $request->applied_custom_deduction ?? 0;
  //       $totalDeductionPercent = $globalRate + $customRate;

  //       $order = Order::create([
  //         'customer_id'     => $request->customer_id,
  //         'sr_id'           => $user->id,
  //         'manager_id'      => $manager->id,
  //         'branch_id'      => $user->branch_id,
  //         'status'          => 'pending_sr',
  //         'special_discount' => $request->special_discount ?? 0,
  //         'discount_amount' => $request->total_discount,
  //         'net_total'       => $request->net_total,
  //         'applied_deduction_percent' => $totalDeductionPercent,
  //         'note'            => $request->note
  //       ]);

  //       foreach ($request->products as $item) {

  //         $basePrice = $item['price'];
  //         $deductionAmount = ($basePrice * $totalDeductionPercent / 100);
  //         $sellingRate = $basePrice - $deductionAmount;

  //         OrderItem::create([
  //           'order_id'              => $order->id,
  //           'product_id'            => $item['product_id'],
  //           'quantity'              => $item['qty'],
  //           'price'                 => $basePrice,
  //           'unit_deduction_amount' => $deductionAmount,
  //           'selling_rate'          => $sellingRate,
  //           'discount_amount'       => $item['discount'] ?? 0, 
  //           'net_total'             => ($sellingRate - ($item['discount'] ?? 0)) * $item['qty']
  //         ]);
  //       }

  //       if ($manager) {
  //         $notificationData = [
  //           'title'   => 'New Order Received',
  //           'message' => [
  //             'text' => 'A new order has been placed by',
  //             'from' => $user->username
  //           ],
  //           'url'     => route('manager.order.show', $order->id),
  //           'type'    => 'new_order'
  //         ];

  //         $manager->notify(new \App\Notifications\SystemNotification($notificationData));
  //       }

  //       return redirect()->route('dashboards')->with('success', 'Order requested successfully!');
  //     });
  //   } catch (\Exception $e) {
  //     return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
  //   }
  // }
}
