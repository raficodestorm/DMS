<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\Order;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $returns = $query->paginate(15);

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
        // Filter items where quantity is greater than 0
        $filteredItems = collect($request->items)->filter(function($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        })->toArray();

        // If no items were selected with quantity > 0, return error
        if (empty($filteredItems)) {
            return back()->withInput()->with('error', 'Please select at least one product with a quantity greater than 0 to return.');
        }

        // Re-index the items array to avoid validation key issues (like items.2.quantity)
        $request->merge(['items' => array_values($filteredItems)]);

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = auth()->user();
                $order = Order::with('items')->findOrFail($request->order_id);

                // Check if SR owns this order
                if ($order->branch_id != $user->branch_id) {
                    throw new \Exception("Unauthorized access to this order.");
                }

                $productReturn = ProductReturn::create([
                    'customer_id' => $order->customer_id,
                    'sr_id' => $user->id,
                    'order_id' => $order->id,
                    'branch_id' => $user->branch_id,
                    'reason' => $request->reason,
                    'status' => 'pending_admin',
                    'total_amount' => 0, // Calculated below
                ]);

                $totalAmount = 0;

                foreach ($request->items as $itemData) {
                    $orderItem = $order->items->where('product_id', $itemData['product_id'])->first();
                    
                    if (!$orderItem) {
                        throw new \Exception("Product not found in this order.");
                    }

                    // Validation: Cannot return more than purchased quantity
                    // Note: We should also consider previous returns for this order
                    $previouslyReturnedQty = ReturnItem::whereHas('productReturn', function($q) use ($order) {
                        $q->where('order_id', $order->id)->where('status', '!=', 'rejected');
                    })->where('product_id', $itemData['product_id'])->sum('quantity');

                    $availableToReturn = $orderItem->quantity - $previouslyReturnedQty;

                    if ($itemData['quantity'] > $availableToReturn) {
                        throw new \Exception("Cannot return more than available quantity for " . $orderItem->product->name);
                    }

                    // Use the original selling rate from order_items
                    $price = $orderItem->selling_rate;
                    $subtotal = $itemData['quantity'] * $price;

                    ReturnItem::create([
                        'product_return_id' => $productReturn->id,
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    $totalAmount += $subtotal;
                }

                $productReturn->update(['total_amount' => $totalAmount]);

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

                return redirect()->route('manager.return.index')->with('success', 'Return request submitted successfully.');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
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

        if ($return->status != 'pending_manager') {
            return back()->with('error', 'Return already processed.');
        }

        $return->update(['status' => 'pending_admin']);

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

        if ($return->status != 'pending_manager') {
            return back()->with('error', 'Cannot reject at this stage.');
        }

        $return->update(['status' => 'rejected']);

        return back()->with('success', 'Return request rejected.');
    }
}
