<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\OrderIdGeneratorService;
use App\Services\PricingEngineService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected PricingEngineService $pricingEngine;
    protected OrderIdGeneratorService $orderIdGenerator;

    public function __construct(
        PricingEngineService $pricingEngine,
        OrderIdGeneratorService $orderIdGenerator
    ) {
        $this->pricingEngine = $pricingEngine;
        $this->orderIdGenerator = $orderIdGenerator;
    }

    /**
     * Show the Checkout page.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $prefillData = [
            'fullname' => '',
            'phone'    => '',
            'country'  => 'Bangladesh',
            'city'     => 'Dhaka',
            'address'  => '',
        ];

        if ($user) {
            $customer = $user->customer;
            $prefillData['fullname'] = $user->fullname ?: ($customer->manager ?? ($customer->shop_name ?? ''));
            $prefillData['phone']    = $customer->phone ?? '';
            $prefillData['country']  = $customer->country ?: 'Bangladesh';
            $prefillData['city']     = $customer->city ?: 'Dhaka';
            $prefillData['address']  = $customer->address ?? '';
        }

        $activeShippingRates = ShippingRate::where('status', 1)->get();
        $countriesData = $this->getCountriesData();

        // Extract available shipping countries & cities from active shipping rates
        $shippingCountries = $activeShippingRates->pluck('country')->unique()->values()->toArray();
        if (empty($shippingCountries)) {
            $shippingCountries = ['Bangladesh'];
        }

        return view('pages.checkout.index', compact(
            'prefillData',
            'activeShippingRates',
            'shippingCountries',
            'countriesData'
        ));
    }

    /**
     * Live pricing and shipping calculation for checkout.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculate(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $appliedCoupons = $request->input('applied_coupons', []);
        $country = $request->input('country');
        $city = $request->input('city');

        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }

        if (is_string($appliedCoupons)) {
            $appliedCoupons = json_decode($appliedCoupons, true) ?? [];
        }

        $result = $this->pricingEngine->calculateCart($items, $appliedCoupons, $country, $city);

        return response()->json($result);
    }

    /**
     * Get matching shipping rate for selected location.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getShippingRate(Request $request): JsonResponse
    {
        $country = $request->input('country');
        $city = $request->input('city');

        $rate = $this->pricingEngine->getShippingRateForLocation($country, $city);

        return response()->json([
            'success'   => true,
            'base_rate' => $rate ? (float) $rate->base_rate : 3000.0,
            'name'      => $rate ? $rate->name : 'Standard Delivery',
            'country'   => $rate ? $rate->country : $country,
            'city'      => $rate ? $rate->city : $city,
        ]);
    }

    /**
     * Place an online order with server-side validation & verification.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fullname'        => ['required', 'string', 'max:150'],
            'phone'           => ['required', 'string', 'max:30'],
            'country'         => ['required', 'string', 'max:100'],
            'city'            => ['required', 'string', 'max:100'],
            'address'         => ['required', 'string', 'max:1000'],
            'note'            => ['nullable', 'string', 'max:1000'],
            'payment_method'  => ['nullable', 'string', 'max:50'],
            'items'           => ['required'],
            'applied_coupons' => ['nullable'],
        ]);

        $items = $validated['items'];
        $appliedCoupons = $validated['applied_coupons'] ?? [];

        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }

        if (is_string($appliedCoupons)) {
            $appliedCoupons = json_decode($appliedCoupons, true) ?? [];
        }

        if (empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Please add items before placing an order.',
            ], 422);
        }

        // Server-side tamper-proof calculation
        $cartCalculation = $this->pricingEngine->calculateCart(
            $items,
            $appliedCoupons,
            $validated['country'],
            $validated['city']
        );

        if (empty($cartCalculation['items'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to calculate order total. Please refresh and try again.',
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($validated, $cartCalculation) {
                $user = Auth::user();
                $orderId = $this->orderIdGenerator->generate();

                // Branch assignment: user branch or default first branch
                $branchId = $user->branch_id ?? Branch::value('id') ?? 1;
                $customerId = $user->customer_id ?? null;

                $order = Order::create([
                    'order_id'                  => $orderId,
                    'customer_id'               => $customerId,
                    'sr_id'                     => null,
                    'manager_id'                => null,
                    'branch_id'                 => $branchId,
                    'status'                    => 'pending_manager',
                    'special_discount'          => 0,
                    'discount_amount'           => $cartCalculation['total_savings'] ?? 0,
                    'shipping_charge'           => $cartCalculation['shipping_fee'] ?? 0,
                    'net_total'                 => $cartCalculation['grand_total'],
                    'applied_deduction_percent' => $cartCalculation['retail_deduction_percent'] ?? 0,
                    'note'                      => $validated['note'] ?? null,
                    'order_type'                => 'online',
                    'payment_status'            => 'unpaid',
                    'payment_amount'            => 0,
                    'payment_method'            => $validated['payment_method'] ?? 'Cash on Delivery',
                    'customer_name'             => $validated['fullname'],
                    'customer_phone'            => $validated['phone'],
                    'country'                   => $validated['country'],
                    'city'                      => $validated['city'],
                    'address'                   => $validated['address'],
                ]);

                // Create Order Items
                $productIds = collect($cartCalculation['items'])->pluck('product_id')->unique();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($cartCalculation['items'] as $item) {
                    $productId = $item['product_id'];
                    $qty = (int) $item['quantity'];
                    $product = $products->get($productId);

                    $purchasePrice = (float) ($product->purchase_price ?? 0);
                    $totalPurchaseCost = round($purchasePrice * $qty, 2);
                    $profit = round($item['subtotal'] - $totalPurchaseCost, 2);

                    OrderItem::create([
                        'order_id'              => $order->id,
                        'product_id'            => $productId,
                        'quantity'              => $qty,
                        'price'                 => $item['base_price'],
                        'unit_deduction_amount' => $item['deduction_amount'],
                        'selling_rate'          => $item['price_after_deduction'],
                        'discount_amount'       => $item['offer_discount_unit'],
                        'offer'                 => $item['offer_snapshot'] ?? null,
                        'net_total'             => $item['subtotal'],
                        'profit'                => $profit,
                    ]);

                    // Optionally decrement branch stock if available
                    if ($branchId) {
                        $stock = Stock::where([
                            'product_id' => $productId,
                            'branch_id'  => $branchId,
                        ])->lockForUpdate()->first();

                        if ($stock && $stock->quantity >= $qty) {
                            $stock->decrement('quantity', $qty);
                        }
                    }
                }

                // Notify Admins
                $admins = User::where('role', 'admin')->get();
                $notificationData = [
                    'title'   => 'New Online Order',
                    'message' => [
                        'text' => 'A new online order has been placed by',
                        'from' => $validated['fullname'],
                    ],
                    'url'     => route('admin.order.show', $order->id),
                    'type'    => 'new_order',
                ];

                foreach ($admins as $admin) {
                    $admin->notify(new SystemNotification($notificationData));
                }

                return $order;
            });

            return response()->json([
                'success'      => true,
                'message'      => 'Your order has been placed successfully!',
                'order_id'     => $order->order_id,
                'redirect_url' => route('order.success', $order->order_id),
            ]);
        } catch (Exception $e) {
            Log::error('Checkout place order error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the Order Success page.
     *
     * @param string $orderId
     * @return View
     */
    public function orderSuccess(string $orderId): View
    {
        $order = Order::with(['items.product.category'])
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('pages.checkout.success', compact('order'));
    }

    /**
     * Load countries data from countries.json file.
     *
     * @return array<string, array<int, string>>
     */
    private function getCountriesData(): array
    {
        $filePath = resource_path('data/countries.json');

        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);

        return json_decode($content, true) ?? [];
    }
}
