<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Deduction;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\OrderIdGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display wholesale product catalog for logged-in customer's branch.
     */
    public function shop()
    {
        $user = auth()->user();
        $customer = $user->customer;
        if (!$customer && $user->customer_id) {
            $customer = Customer::find($user->customer_id);
        }

        $branchId = $customer?->branch_id ?? $user->branch_id;
        $today = Carbon::today();

        $suppliers = Supplier::with('deductions')->orderBy('company_name', 'asc')->get();
        $categories = Category::pluck('name', 'id');
        $branchStocks = Stock::where('branch_id', $branchId)->pluck('quantity', 'product_id');

        $activeOffers = Offer::where('status', 1)
            ->where('customer_type', 'wholesale')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->keyBy('product_id');

        $products = Product::with(['supplier.deductions', 'category'])
            ->get()
            ->map(function ($product) use ($branchStocks, $activeOffers) {
                $basePrice = (float) $product->price;

                $supplier = $product->supplier;
                $customerDeduction = 0;
                if ($supplier) {
                    $deduction = $supplier->deductions->first();
                    $customerDeduction = (float) ($deduction->customer_deduction ?? 0);
                }

                $deductionAmount = round($basePrice * $customerDeduction / 100, 2);
                $sellingRate = (int) round($basePrice - $deductionAmount);
                if ($sellingRate < 0) {
                    $sellingRate = 0;
                }

                $offer = $activeOffers->get($product->id);
                $offerDisc = 0;
                $offerText = null;
                $offerType = 'fixed';
                if ($offer) {
                    $offerType = $offer->type;
                    if ($offer->type === 'percentage') {
                        $offerDisc = round($sellingRate * $offer->discount_amount / 100, 2);
                        $offerText = ((float) $offer->discount_amount) . '% OFF';
                    } else {
                        $offerDisc = (float) $offer->discount_amount;
                        $offerText = '৳' . ((float) $offer->discount_amount) . ' OFF';
                    }
                }

                return [
                    'id'                 => $product->id,
                    'name'               => $product->name,
                    'image'              => $product->image,
                    'price'              => $basePrice,
                    'customer_deduction' => $customerDeduction,
                    'selling_rate'       => $sellingRate,
                    'category_id'        => $product->category_id,
                    'category_name'      => $product->category?->name ?? 'General',
                    'supplier_id'        => $product->supplier_id,
                    'supplier_name'      => $supplier?->company_name ?? ($supplier?->name ?? 'N/A'),
                    'available_qty'      => (int) ($branchStocks->get($product->id) ?? 0),
                    'has_offer'          => $offer ? true : false,
                    'offer_text'         => $offerText,
                    'offer_discount'     => $offerDisc,
                    'offer_type'         => $offerType,
                ];
            });

        return view('pages.customer.order.shop', compact('suppliers', 'categories', 'products', 'customer'));
    }

    /**
     * Display cart checkout page for wholesale customer.
     */
    public function cart()
    {
        $user = auth()->user();
        $customer = $user->customer;
        if (!$customer && $user->customer_id) {
            $customer = Customer::with(['branch', 'sr'])->find($user->customer_id);
        } else if ($customer) {
            $customer->load(['branch', 'sr']);
        }

        if (!$customer) {
            return redirect()->route('customer.orders.shop')->with('error', 'Customer profile not found.');
        }

        $suppliers = Supplier::with('deductions')->orderBy('company_name', 'asc')->get();
        $deductionSettings = Deduction::where('type', 'main')->first() ?? Deduction::first();

        return view('pages.customer.order.cart', compact('customer', 'suppliers', 'deductionSettings'));
    }

    /**
     * Store new customer wholesale order.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;
        if (!$customer && $user->customer_id) {
            $customer = Customer::find($user->customer_id);
        }

        if (!$customer) {
            return back()->with('error', 'Customer profile not found.');
        }

        $request->validate([
            'products'         => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.qty'        => ['required', 'integer', 'min:1'],
            'net_total'        => ['required', 'numeric', 'min:0'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'supplier_id'      => ['nullable', 'exists:suppliers,id'],
            'note'             => ['nullable', 'string', 'max:500'],
        ]);

        try {
            return DB::transaction(function () use ($request, $user, $customer) {
                $branchId = $customer->branch_id ?? $user->branch_id;

                // 1. Get Branch Managers
                $manager = User::where('branch_id', $branchId)
                    ->where('role', 'manager')
                    ->first();

                $managers = User::where('branch_id', $branchId)
                    ->where('role', 'manager')
                    ->get();

                // 2. Fetch Products with Supplier Deductions
                $productIds = collect($request->products)
                    ->pluck('product_id')
                    ->filter()
                    ->unique()
                    ->values();

                $products = Product::with(['supplier.deductions'])->whereIn('id', $productIds)->get()->keyBy('id');

                if ($products->count() !== $productIds->count()) {
                    throw new \RuntimeException('One or more selected products no longer exist.');
                }

                // 3. Determine Dominant Supplier (supplier with highest item count; otherwise null) & Applied Deduction Percent
                $supplierCounts = [];
                foreach ($request->products as $item) {
                    $prod = $products->get($item['product_id']);
                    $supId = $prod?->supplier_id;
                    if ($supId) {
                        $supplierCounts[$supId] = ($supplierCounts[$supId] ?? 0) + 1;
                    }
                }

                $dominantSupplierId = null;
                $dominantDeduction = 0;

                if (!empty($supplierCounts)) {
                    arsort($supplierCounts);
                    $counts = array_values($supplierCounts);
                    $topSupplierId = array_key_first($supplierCounts);

                    // Check if top supplier is unique (strictly greater than the second, or only 1 supplier)
                    if (count($counts) === 1 || $counts[0] > $counts[1]) {
                        $dominantSupplierId = $topSupplierId;
                    }

                    // Get customer_deduction of the dominant supplier (or fallback to the first supplier in cart)
                    $targetSupplierId = $dominantSupplierId ?: $topSupplierId;
                    $targetProduct = $products->firstWhere('supplier_id', $targetSupplierId);
                    $targetSupplier = $targetProduct?->supplier ?? Supplier::with('deductions')->find($targetSupplierId);
                    $dominantDeduction = (float) ($targetSupplier?->deduction?->customer_deduction 
                        ?? ($targetSupplier?->deductions?->first()?->customer_deduction ?? 0));
                }

                // If still 0, fallback to the first product in the cart that has a supplier deduction
                if ($dominantDeduction <= 0) {
                    foreach ($products as $prod) {
                        $ded = (float) ($prod->supplier?->deduction?->customer_deduction 
                            ?? ($prod->supplier?->deductions?->first()?->customer_deduction ?? 0));
                        if ($ded > 0) {
                            $dominantDeduction = $ded;
                            break;
                        }
                    }
                }

                // 4. Fetch Active Offers for Snapshot
                $today = Carbon::today();
                $activeOffers = Offer::where('status', 1)
                    ->where('customer_type', 'wholesale')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->whereIn('product_id', $productIds)
                    ->get()
                    ->keyBy('product_id');

                // 5. Pre-calculate Items Data and Order Totals from DB values
                $orderItemsData = [];
                $orderDiscountTotal = 0;
                $orderNetTotal = 0;

                foreach ($request->products as $item) {
                    $productId = $item['product_id'];
                    $product = $products->get($productId);
                    $qty = (int) $item['qty'];

                    if ($qty <= 0) {
                        throw new \RuntimeException("Invalid quantity for product ID: {$productId}");
                    }

                    // 100% Authoritative base MRP price from Database
                    $basePrice = (float) $product->price;

                    // Product's Supplier Wholesale Deduction
                    $supplier = $product->supplier;
                    $productDeductionPercent = 0;
                    if ($supplier && $supplier->deductions->isNotEmpty()) {
                        $productDeductionPercent = (float) ($supplier->deductions->first()->customer_deduction ?? 0);
                    }

                    $deductionAmount = round($basePrice * $productDeductionPercent / 100, 2);
                    $sellingRate = (int) round($basePrice - $deductionAmount);
                    if ($sellingRate < 0) {
                        $sellingRate = 0;
                    }

                    // Determine active wholesale offer discount and snapshot
                    $offer = $activeOffers->get($productId);
                    $offerSnapshot = null;
                    $offerDiscount = 0;

                    if ($offer) {
                        if ($offer->type === 'percentage') {
                            $offerDiscount = round($sellingRate * $offer->discount_amount / 100, 2);
                            $offerSnapshot = ((float) $offer->discount_amount) . '%';
                        } else {
                            $offerDiscount = (float) $offer->discount_amount;
                            $offerSnapshot = ((float) $offer->discount_amount) . ' TK';
                        }
                    }

                    $unitFinalPrice = max(0, $sellingRate - $offerDiscount);
                    $itemNetTotal = round($unitFinalPrice * $qty, 2);

                    // Purchase Price & Profit Calculation
                    $purchasePrice = (float) ($product->purchase_price ?? 0);

                    // If purchase_price is not populated (0 or null) in the product table,
                    // calculate it dynamically based on the supplier's standard deduction policy
                    if ($purchasePrice <= 0) {
                        $deduction = $supplier?->deductions?->first();
                        if ($deduction) {
                            $custPct = max(0.0, (float) ($deduction->customer_deduction ?? 0));
                            $treePct = max(0.0, (float) ($deduction->tree_deduction ?? 0));
                            $myPct   = max(0.0, (float) ($deduction->my_deduction ?? 0));

                            $afterCustomer = $basePrice * (1 - $custPct / 100);
                            $afterTree     = $afterCustomer * (1 - $treePct / 100);
                            $purchasePrice = round($afterTree * (1 - $myPct / 100), 2);
                        } else {
                            $purchasePrice = $sellingRate;
                        }
                    }

                    $totalPurchaseCost = round($purchasePrice * $qty, 2);
                    $profit = round($itemNetTotal - $totalPurchaseCost, 2);

                    $orderDiscountTotal += ($offerDiscount * $qty);
                    $orderNetTotal += $itemNetTotal;

                    $orderItemsData[] = [
                        'product_id'            => $product->id,
                        'quantity'              => $qty,
                        'price'                 => $basePrice,
                        'unit_deduction_amount' => $deductionAmount,
                        'selling_rate'          => $sellingRate,
                        'discount_amount'       => $offerDiscount,
                        'offer'                 => $offerSnapshot,
                        'net_total'             => $itemNetTotal,
                        'profit'                => $profit,
                    ];
                }

                // 6. Generate Order ID
                $orderId = app(OrderIdGeneratorService::class)->generate();

                // 7. Create Order Header
                $order = Order::create([
                    'order_id'                  => $orderId,
                    'customer_id'               => $customer->id,
                    'supplier_id'               => $dominantSupplierId,
                    'sr_id'                     => $customer->sr_id,
                    'manager_id'                => $manager?->id,
                    'branch_id'                 => $branchId,
                    'status'                    => 'pending_sr',
                    'special_discount'          => 0,
                    'discount_amount'           => round($orderDiscountTotal, 2),
                    'net_total'                 => (int) round($orderNetTotal),
                    'applied_deduction_percent' => $dominantDeduction,
                    'note'                      => $request->note,
                    'order_type'                => 'online',
                    'customer_name'             => $customer->shop_name,
                    'customer_phone'            => $customer->phone,
                    'country'                   => $customer->country,
                    'city'                      => $customer->city,
                    'address'                   => $request->delivery_address ?: $customer->address,
                ]);

                // 8. Create Order Items
                foreach ($orderItemsData as $itemData) {
                    $itemData['order_id'] = $order->id;
                    OrderItem::create($itemData);
                }

                // 7. Send Notifications
                if ($order->sr_id) {
                    $assignedSr = User::find($order->sr_id);
                    if ($assignedSr) {
                        $assignedSr->notify(new SystemNotification([
                            'title'   => 'New Wholesale Order',
                            'message' => "Customer {$customer->shop_name} has placed a new online order #{$order->order_id}",
                            'url'     => route('sr.order.show', $order->id),
                        ]));
                    }
                }

                foreach ($managers as $mgr) {
                    $mgr->notify(new SystemNotification([
                        'title'   => 'New Online Order',
                        'message' => "New wholesale order #{$order->order_id} placed by {$customer->shop_name}",
                        'url'     => route('manager.order.show', $order->id),
                    ]));
                }

                return redirect()->route('customer.orders.index')
                    ->with('success', "Order #{$order->order_id} placed successfully!");
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
