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

    $orders = $query->paginate(20);

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
          ->paginate(10);

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

        // Get deduction percentage for audit
        $deductionSettings = DB::table('deductions')->where('type', 'main')->first();
        $globalRate = $request->has('apply_global') ? ($deductionSettings->customer_deduction ?? 0) : 0;
        $customRate = $request->applied_custom_deduction ?? 0;
        $totalDeductionPercent = $globalRate + $customRate;

        $order = Order::create([
          'customer_id'     => $request->customer_id,
          'sr_id'           => $user->id,
          'manager_id'      => $manager->id,
          'status'          => 'pending_sr',
          'special_discount' => $request->special_discount ?? 0,
          'discount_amount' => $request->total_discount,
          'net_total'       => $request->net_total,
          'applied_deduction_percent' => $totalDeductionPercent,
          'note'            => $request->note
        ]);

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
}
