<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReturn::with(['customer', 'sr', 'order', 'branch'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customer) use ($search) {
                      $customer->where('shop_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table'  => view('pages.admin.return.table', compact('returns'))->render(),
                'mobile' => view('pages.admin.return.mtable', compact('returns'))->render(),
            ]);
        }

        return view('pages.admin.return.index', compact('returns'));
    }

    public function show($id)
    {
        $return = ProductReturn::with(['customer', 'order', 'items.product', 'sr', 'branch'])->findOrFail($id);
        return view('pages.admin.return.show', compact('return'));
    }

    public function approve($id)
    {
        $return = ProductReturn::with(['items', 'customer', 'order'])->findOrFail($id);

        if ($return->status != 'pending_admin') {
            return back()->with('error', 'Invalid status for approval.');
        }

        try {
            return DB::transaction(function () use ($return) {
                // 1. Update Stocks (Increment Branch Stock)
                foreach ($return->items as $item) {
                    $stock = Stock::where('product_id', $item->product_id)
                        ->where('branch_id', $return->branch_id)
                        ->lockForUpdate()
                        ->first();

                    if ($stock) {
                        $stock->increment('quantity', $item->quantity);
                    } else {
                        Stock::create([
                            'product_id' => $item->product_id,
                            'branch_id' => $return->branch_id,
                            'quantity' => $item->quantity
                        ]);
                    }

                    // 2. Adjust Order Items
                    if ($return->order_id) {
                        $orderItem = OrderItem::where('order_id', $return->order_id)
                            ->where('product_id', $item->product_id)
                            ->first();
                        
                        if ($orderItem) {
                            $orderItem->decrement('quantity', $item->quantity);
                            $orderItem->decrement('net_total', $item->subtotal);
                        }
                    }
                }

                // 3. Adjust Order Total
                if ($return->order_id) {
                    $return->order->decrement('net_total', $return->total_amount);
                }

                // 4. Update Customer Due
                $customer = $return->customer;
                $customer->decrement('due', $return->total_amount);
                $customer->refresh();

                // 5. Create Transaction (Type: return decreases due)
                Transaction::create([
                    'customer_id' => $customer->id,
                    'order_id' => $return->order_id,
                    'sr_id' => $return->sr_id,
                    'type' => 'return',
                    'amount' => $return->total_amount,
                    'due' => $customer->due,
                    'status' => 'complete',
                    'note' => 'Return Approved for Order BRS' . ($return->order_id ?? 'N/A') . ' (Return ID: #' . $return->id . ')'
                ]);

                $return->update(['status' => 'approved']);

                return back()->with('success', 'Return approved successfully. Stocks and Accounts updated.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $return = ProductReturn::with(['items', 'customer', 'order'])->findOrFail($id);

        try {
            return DB::transaction(function () use ($return) {
                // If it was already approved, we must ROLLBACK changes
                if ($return->status == 'approved') {
                    foreach ($return->items as $item) {
                        // Rollback Stock
                        $stock = Stock::where('product_id', $item->product_id)
                            ->where('branch_id', $return->branch_id)
                            ->first();
                        if ($stock) {
                            $stock->decrement('quantity', $item->quantity);
                        }

                        // Rollback Order Items
                        if ($return->order_id) {
                            $orderItem = OrderItem::where('order_id', $return->order_id)
                                ->where('product_id', $item->product_id)
                                ->first();
                            if ($orderItem) {
                                $orderItem->increment('quantity', $item->quantity);
                                $orderItem->increment('net_total', $item->subtotal);
                            }
                        }
                    }

                    // Rollback Order Total
                    if ($return->order_id) {
                        $return->order->increment('net_total', $return->total_amount);
                    }

                    // Rollback Customer Due
                    $customer = $return->customer;
                    $customer->increment('due', $return->total_amount);

                    // Remove Transaction
                    Transaction::where('order_id', $return->order_id)
                        ->where('type', 'return')
                        ->where('amount', $return->total_amount)
                        ->where('note', 'like', "%Return ID: #{$return->id}%")
                        ->delete();
                }

                $return->items()->delete();
                $return->delete();

                return redirect()->route('admin.return.index')->with('success', 'Return request deleted and changes rolled back.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }
}
