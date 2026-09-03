<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\Order;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Customer;

class ReturnManagerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ProductReturn::with(['customer', 'sr', 'order'])
            ->where('branch_id', $user->branch_id)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customer) use ($search) {
                      $customer->where('shop_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('sr', function ($sr) use ($search) {
                      $sr->where('username', 'like', "%{$search}%");
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

        $returns = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'table'  => view('pages.manager.return.table', compact('returns'))->render(),
                'mobile' => view('pages.manager.return.mtable', compact('returns'))->render(),
            ]);
        }

        return view('pages.manager.return.index', compact('returns'));
    }






    public function create(Request $request)
    {
        $user = auth()->user();
        $orders = Order::where('branch_id', $user->branch_id)
            ->where('status', 'delivered')
            ->latest()
            ->get();

        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::with('items.product')->where('branch_id', $user->branch_id)->findOrFail($request->order_id);
            
            foreach ($selectedOrder->items as $item) {
                $returnedQty = \App\Models\ReturnItem::whereHas('productReturn', function($q) use ($selectedOrder) {
                    $q->where('order_id', $selectedOrder->id)->where('status', '!=', 'rejected');
                })->where('product_id', $item->product_id)->sum('quantity');
                
                $item->available_to_return = $item->quantity - $returnedQty;
            }
        }

        return view('pages.manager.return.create', compact('orders', 'selectedOrder'));
    }

    




    public function store(Request $request)
{
    
    $filteredItems = collect($request->input('items', []))
        ->filter(function ($item) {
            return isset($item['quantity']) && (int) $item['quantity'] > 0;
        })
        ->values()
        ->toArray();

    if (empty($filteredItems)) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Please select at least one product with a quantity greater than 0 to return.'
            );
    }

    /*
     * Re-index items so validation keys remain clean.
     */
    $request->merge([
        'items' => $filteredItems,
    ]);

    /*
     * ---------------------------------------------------------
     * 2. Validation
     * ---------------------------------------------------------
     */
    $request->validate([
        'order_id' => ['required', 'exists:orders,id'],

        'items' => ['required', 'array', 'min:1'],

        'items.*.product_id' => [
            'required',
            'integer',
            'exists:products,id',
        ],

        'items.*.quantity' => [
            'required',
            'integer',
            'min:1',
        ],

        'reason' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    try {

        return DB::transaction(function () use ($request) {

            $user = auth()->user();

            /*
             * ---------------------------------------------------------
             * 3. Get order and lock it
             *
             * Locking prevents two return requests from simultaneously
             * exceeding the available return quantity.
             * ---------------------------------------------------------
             */
            $order = Order::query()
                ->where('id', $request->order_id)
                ->where('branch_id', $user->branch_id)
                ->with([
                    'items' => function ($query) {
                        $query->lockForUpdate();
                    },
                ])
                ->lockForUpdate()
                ->first();

            if (!$order) {
                throw new \RuntimeException(
                    'Unauthorized access to this order or order not found.'
                );
            }

            /*
             * ---------------------------------------------------------
             * 4. Normalize requested quantities
             *
             * If the same product appears more than once in the request,
             * combine them into one quantity.
             * ---------------------------------------------------------
             */
            $requestedItems = collect($request->items)
                ->groupBy('product_id')
                ->map(function ($items, $productId) {

                    return [
                        'product_id' => (int) $productId,
                        'quantity'   => $items->sum(
                            fn ($item) => (int) $item['quantity']
                        ),
                    ];

                })
                ->values();


            /*
             * ---------------------------------------------------------
             * 5. Get requested product IDs
             * ---------------------------------------------------------
             */
            $productIds = $requestedItems
                ->pluck('product_id')
                ->unique()
                ->values();


            /*
             * ---------------------------------------------------------
             * 6. Fetch all products in ONE query
             *
             * purchase_price is required for profit calculation.
             * ---------------------------------------------------------
             */
            $products = Product::query()
                ->select([
                    'id',
                    'name',
                    'purchase_price',
                ])
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');


            /*
             * ---------------------------------------------------------
             * 7. Make sure every requested product exists
             * ---------------------------------------------------------
             */
            if ($products->count() !== $productIds->count()) {
                throw new \RuntimeException(
                    'One or more selected products no longer exist.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 8. Make order items easily accessible
             * ---------------------------------------------------------
             */
            $orderItems = $order->items
                ->keyBy('product_id');


            /*
             * Make sure every requested product belongs to this order.
             */
            foreach ($productIds as $productId) {

                if (!$orderItems->has($productId)) {
                    throw new \RuntimeException(
                        'One or more selected products do not belong to this order.'
                    );
                }
            }


            /*
             * ---------------------------------------------------------
             * 9. Get previous returned quantities in ONE query
             *
             * Instead of running ReturnItem query inside the loop,
             * fetch all previous returned quantities at once.
             * ---------------------------------------------------------
             */
            $previouslyReturned = ReturnItem::query()
                ->whereIn('product_id', $productIds)
                ->whereHas('productReturn', function ($query) use ($order) {
                    $query
                        ->where('order_id', $order->id)
                        ->where('status', '!=', 'rejected');
                })
                ->select(
                    'product_id',
                    DB::raw('SUM(quantity) as returned_quantity')
                )
                ->groupBy('product_id')
                ->pluck('returned_quantity', 'product_id');


            /*
             * ---------------------------------------------------------
             * 10. Create Product Return
             * ---------------------------------------------------------
             */
            $productReturn = ProductReturn::create([
                'customer_id' => $order->customer_id,
                'sr_id'       => $user->id,
                'order_id'    => $order->id,
                'branch_id'   => $user->branch_id,
                'reason'      => $request->reason,
                'status'      => 'pending_manager',
                'total_amount'=> 0,
            ]);


            $totalAmount = 0;


            /*
             * ---------------------------------------------------------
             * 11. Create Return Items
             * ---------------------------------------------------------
             */
            foreach ($requestedItems as $requestedItem) {

                $productId = $requestedItem['product_id'];
                $quantity  = (int) $requestedItem['quantity'];

                /** @var \App\Models\OrderItem $orderItem */
                $orderItem = $orderItems->get($productId);

                /** @var \App\Models\Product $product */
                $product = $products->get($productId);


                /*
                 * -----------------------------------------------------
                 * Previously returned quantity
                 * -----------------------------------------------------
                 */
                $previouslyReturnedQty = (int) (
                    $previouslyReturned->get($productId, 0)
                );


                /*
                 * -----------------------------------------------------
                 * Available quantity for return
                 * -----------------------------------------------------
                 */
                $availableToReturn = max(
                    0,
                    (int) $orderItem->quantity - $previouslyReturnedQty
                );


                if ($quantity > $availableToReturn) {

                    throw new \RuntimeException(
                        "Cannot return more than available quantity for {$product->name}."
                    );
                }


                /*
                 * -----------------------------------------------------
                 * Return price
                 *
                 * Keep your existing business rule:
                 * return price = original selling rate.
                 * -----------------------------------------------------
                 */
                $price = round(
                    (float) $orderItem->selling_rate,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Return subtotal
                 * -----------------------------------------------------
                 */
                $subtotal = round(
                    $quantity * $price,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Purchase cost
                 * -----------------------------------------------------
                 */
                $purchasePrice = round(
                    (float) $product->purchase_price,
                    2
                );

                $purchaseCost = round(
                    $quantity * $purchasePrice,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * RETURN PROFIT
                 *
                 * Profit = Return Revenue - Purchase Cost
                 *
                 * Return Revenue = price × quantity
                 * Purchase Cost  = purchase_price × quantity
                 * -----------------------------------------------------
                 */
                $profit = round(
                    $subtotal - $purchaseCost,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Create Return Item
                 * -----------------------------------------------------
                 */
                ReturnItem::create([
                    'product_return_id' => $productReturn->id,
                    'product_id'        => $productId,
                    'quantity'          => $quantity,
                    'price'             => $price,
                    'subtotal'          => $subtotal,
                    'profit'            => $profit,
                ]);


                /*
                 * -----------------------------------------------------
                 * Update return totals
                 * -----------------------------------------------------
                 */
                $totalAmount += $subtotal;
            }


            /*
             * ---------------------------------------------------------
             * 12. Update Product Return total
             * ---------------------------------------------------------
             */
            $productReturn->update([
                'total_amount' => round($totalAmount, 2),
            ]);

            

            /*
             * ---------------------------------------------------------
             * 13. Notify Managers
             * ---------------------------------------------------------
             */
            $admins = User::query()
                ->where('role', 'admin')
                ->get();


            foreach ($admins as $admin) {

                $admin->notify(
                    new SystemNotification([
                        'title' => 'New Return Request',

                        'message' => [
                            'text' => 'A new return request has been submitted by',
                            'from' => $user->username,
                        ],

                        'url' => route(
                            'admin.return.show',
                            $productReturn->id
                        ),

                        'type' => 'new_return',
                    ])
                );
            }


            /*
             * ---------------------------------------------------------
             * 14. Success
             * ---------------------------------------------------------
             */
            return redirect()
                ->route('manager.return.index')
                ->with(
                    'success',
                    'Return request submitted successfully.'
                );
        });

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'Something went wrong: ' . $e->getMessage()
            );
    }
}








    public function show($id)
    {
        $return = ProductReturn::with(['customer', 'order', 'items.product', 'sr'])->findOrFail($id);
        
        if ($return->branch_id != auth()->user()->branch_id) {
            abort(403);
        }

        return view('pages.manager.return.show', compact('return'));
    }

    public function forwardToAdmin($id)
    {
        $return = ProductReturn::findOrFail($id);

        if ($return->status != 'pending_sr') {
            return back()->with('error', 'Return already processed.');
        }

        $return->update(['status' => 'pending_manager']);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SystemNotification([
                'title' => 'Return Approval Request',
                'message' => [
                    'text' => 'A return request has been forwarded by',
                    'from' => auth()->user()->branch->name ?? 'Manager'
                ],
                'url' => route('admin.return.show', $return->id),
                'type' => 'return_approval'
            ]));
        }

        return back()->with('success', 'Return request forwarded to Admin.');
    }

    public function reject($id)
    {
        $return = ProductReturn::findOrFail($id);

        if ($return->status != 'pending_sr') {
            return back()->with('error', 'Cannot reject at this stage.');
        }

        $return->update(['status' => 'rejected']);

        return back()->with('success', 'Return request rejected.');
    }











    public function edit($id)
    {
        $return = ProductReturn::with(['items.product', 'order.items.product'])->findOrFail($id);
        
        if ($return->status == 'approved') {
            return redirect()->back()->with('error', 'Only pending returns can be edited.');
        }

        return view('pages.manager.return.edit', compact('return'));
    }





    public function update(Request $request, $id)
{
   
    $filteredItems = collect($request->input('items', []))
        ->filter(function ($item) {
            return isset($item['quantity'])
                && (int) $item['quantity'] > 0;
        })
        ->values()
        ->toArray();

    /*
     * No valid items selected
     */
    if (empty($filteredItems)) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Please select at least one product with a quantity greater than 0 to return.'
            );
    }

    /*
     * Re-index items for clean validation keys.
     */
    $request->merge([
        'items' => $filteredItems,
    ]);


    /*
     * ---------------------------------------------------------
     * 2. Validation
     * ---------------------------------------------------------
     */
    $request->validate([
        'items' => [
            'required',
            'array',
            'min:1',
        ],

        'items.*.product_id' => [
            'required',
            'integer',
            'exists:products,id',
        ],

        'items.*.quantity' => [
            'required',
            'integer',
            'min:1',
        ],

        'reason' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);


    /*
     * ---------------------------------------------------------
     * 3. Get Return
     * ---------------------------------------------------------
     */
     $user = auth()->user();
    $return = ProductReturn::query()
        ->where('id', $id)
        ->where('branch_id', $user->branch_id)
        ->where('status', '!=', 'approved')
        ->where('status', '!=', 'rejected')
        ->first();

    if (!$return) {
        return redirect()
            ->back()
            ->with('error', 'Unauthorized edit or return request not found.');
    }


    try {

        return DB::transaction(function () use ($request, $return) {

            $user = auth()->user();


            /*
             * ---------------------------------------------------------
             * 4. Get Order and lock it
             *
             * Locking helps prevent concurrent return requests
             * from exceeding available return quantity.
             * ---------------------------------------------------------
             */
            $order = Order::query()
                ->where('id', $return->order_id)
                ->where('branch_id', $user->branch_id)
                ->with([
                    'items' => function ($query) {
                        $query->lockForUpdate();
                    },
                ])
                ->lockForUpdate()
                ->first();

            if (!$order) {
                throw new \RuntimeException(
                    'Unauthorized access to this order or order not found.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 5. Normalize duplicate products
             *
             * If same product somehow appears multiple times in the
             * request, combine the quantities.
             * ---------------------------------------------------------
             */
            $requestedItems = collect($request->items)
                ->groupBy('product_id')
                ->map(function ($items, $productId) {

                    return [
                        'product_id' => (int) $productId,

                        'quantity' => $items->sum(
                            fn ($item) => (int) $item['quantity']
                        ),
                    ];

                })
                ->values();


            /*
             * ---------------------------------------------------------
             * 6. Product IDs
             * ---------------------------------------------------------
             */
            $productIds = $requestedItems
                ->pluck('product_id')
                ->unique()
                ->values();


            /*
             * ---------------------------------------------------------
             * 7. Fetch products in ONE query
             *
             * Needed for purchase_price → profit calculation.
             * ---------------------------------------------------------
             */
            $products = Product::query()
                ->select([
                    'id',
                    'name',
                    'purchase_price',
                ])
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');


            /*
             * Make sure all products still exist.
             */
            if ($products->count() !== $productIds->count()) {
                throw new \RuntimeException(
                    'One or more selected products no longer exist.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 8. Index order items by product_id
             * ---------------------------------------------------------
             */
            $orderItems = $order->items
                ->keyBy('product_id');


            /*
             * Make sure every requested product belongs to the order.
             */
            foreach ($productIds as $productId) {

                if (!$orderItems->has($productId)) {
                    throw new \RuntimeException(
                        'One or more selected products do not belong to this order.'
                    );
                }
            }


            /*
             * ---------------------------------------------------------
             * 9. Get previously returned quantities
             *
             * IMPORTANT:
             * Exclude the current return being edited.
             *
             * One query instead of one query per item.
             * ---------------------------------------------------------
             */
            $previouslyReturned = ReturnItem::query()
                ->whereIn('product_id', $productIds)
                ->whereHas('productReturn', function ($query) use ($order, $return) {

                    $query
                        ->where('order_id', $order->id)
                        ->where('status', '!=', 'rejected')
                        ->where('id', '!=', $return->id);
                })
                ->select(
                    'product_id',
                    DB::raw('SUM(quantity) as returned_quantity')
                )
                ->groupBy('product_id')
                ->pluck('returned_quantity', 'product_id');


            /*
             * ---------------------------------------------------------
             * 10. Update Return basic information
             * ---------------------------------------------------------
             */
            $return->update([
                'reason' => $request->reason,
            ]);


            /*
             * ---------------------------------------------------------
             * 11. Delete old Return Items
             *
             * The update form represents the complete current
             * return request, so rebuilding the items is appropriate.
             * ---------------------------------------------------------
             */
            $return->items()->delete();


            $totalAmount = 0;


            /*
             * ---------------------------------------------------------
             * 12. Create updated Return Items
             * ---------------------------------------------------------
             */
            foreach ($requestedItems as $requestedItem) {

                $productId = $requestedItem['product_id'];

                $quantity = (int) $requestedItem['quantity'];


                /** @var \App\Models\OrderItem $orderItem */
                $orderItem = $orderItems->get($productId);


                /** @var \App\Models\Product $product */
                $product = $products->get($productId);


                /*
                 * -----------------------------------------------------
                 * Previously returned quantity
                 * -----------------------------------------------------
                 */
                $previouslyReturnedQty = (int) (
                    $previouslyReturned->get($productId, 0)
                );


                /*
                 * -----------------------------------------------------
                 * Available quantity
                 * -----------------------------------------------------
                 */
                $availableToReturn = max(
                    0,
                    (int) $orderItem->quantity - $previouslyReturnedQty
                );


                if ($quantity > $availableToReturn) {

                    throw new \RuntimeException(
                        "Cannot return more than available quantity for {$product->name}."
                    );
                }


                /*
                 * -----------------------------------------------------
                 * Return price
                 *
                 * Same rule as your existing store method:
                 * original selling rate.
                 * -----------------------------------------------------
                 */
                $price = round(
                    (float) $orderItem->selling_rate,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Return subtotal
                 * -----------------------------------------------------
                 */
                $subtotal = round(
                    $quantity * $price,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Purchase cost
                 * -----------------------------------------------------
                 */
                $purchasePrice = round(
                    (float) $product->purchase_price,
                    2
                );

                $purchaseCost = round(
                    $quantity * $purchasePrice,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * RETURN PROFIT
                 *
                 * Profit = Return Revenue - Purchase Cost
                 * -----------------------------------------------------
                 */
                $profit = round(
                    $subtotal - $purchaseCost,
                    2
                );


                /*
                 * -----------------------------------------------------
                 * Create Return Item
                 * -----------------------------------------------------
                 */
                ReturnItem::create([
                    'product_return_id' => $return->id,
                    'product_id'        => $productId,
                    'quantity'          => $quantity,
                    'price'             => $price,
                    'subtotal'          => $subtotal,
                    'profit'            => $profit,
                ]);


                /*
                 * -----------------------------------------------------
                 * Total return amount
                 * -----------------------------------------------------
                 */
                $totalAmount += $subtotal;
            }


            /*
             * ---------------------------------------------------------
             * 13. Update Return Total
             * ---------------------------------------------------------
             */
            $return->update([
                'total_amount' => round($totalAmount, 2),
            ]);


            /*
             * ---------------------------------------------------------
             * 14. Success
             * ---------------------------------------------------------
             */
            return redirect()
                ->route('manager.return.index')
                ->with(
                    'success',
                    'Return request updated successfully.'
                );
        });

    } catch (\Throwable $e) {

        /*
         * Log actual exception for debugging.
         */
        report($e);

        /*
         * Do not expose internal exception details to users.
         */
        return back()
            ->withInput()
            ->with(
                'error',
                'Something went wrong while updating the return request.'
            );
    }
}





public function destroy($id)
{
    /*
     * ---------------------------------------------------------
     * 1. Get return
     * ---------------------------------------------------------
     */
    $user = auth()->user();
    $return = ProductReturn::query()
        ->where('id', $id)
        ->where('branch_id', $user->branch_id)
        ->first();

    if (!$return) {
        return back()
            ->with('error', 'Return request not found or unauthorized access.');
    }


    /*
     * ---------------------------------------------------------
     * 2. Approved return cannot be deleted by SR
     * ---------------------------------------------------------
     */
    if ($return->status === 'approved') {
        return back()
            ->with(
                'error',
                'Approved return cannot be deleted.'
            );
    }


    try {

        DB::transaction(function () use ($return) {

            /*
             * -----------------------------------------------------
             * Delete Return Items
             *
             * No stock/order/customer rollback is required because
             * the return has not been approved yet.
             * -----------------------------------------------------
             */
            $return->items()->delete();


            /*
             * -----------------------------------------------------
             * Delete Return Request
             * -----------------------------------------------------
             */
            $return->delete();
        });


        return redirect()
            ->route('manager.return.index')
            ->with(
                'success',
                'Return request deleted successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->with(
                'error',
                'Failed to delete the return request.'
            );
    }
}
}
