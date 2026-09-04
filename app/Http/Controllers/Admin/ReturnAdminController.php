<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnAdminController extends Controller
{
    public function index(Request $request)
    {
        $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        return view('pages.admin.return.index', compact('branches'));
    }

    public function fetchReturnsIndexData(Request $request)
    {
        $query = ProductReturn::with(['customer', 'sr', 'order', 'branch'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                if (preg_match('/^BRET(\d+)$/i', $search, $match)) {
                    $q->where('id', $match[1]);
                    return;
                }
                $q->where('id', $search)
                  ->orWhereHas('customer', function ($customer) use ($search) {
                      $customer->where('shop_name', 'like', "%{$search}%")
                               ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('sr', function ($sr) use ($search) {
                      $sr->where('username', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
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

        return response()->json([
            'table'      => view('pages.admin.return.table', compact('returns'))->render(),
            'mobile'     => view('pages.admin.return.mtable', compact('returns'))->render(),
            'pagination' => (string) $returns->links(),
            'total'      => $returns->total(),
        ]);
    }

    public function show($id)
    {
        $return = ProductReturn::with(['customer', 'order', 'items.product', 'sr', 'branch'])->findOrFail($id);
        return view('pages.admin.return.show', compact('return'));
    }

    public function approve($id)
    {
        $return = ProductReturn::with(['items', 'customer', 'order'])->findOrFail($id);

        if ($return->status != 'pending_manager') {
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
                        $orderItem = OrderItem::query()
                            ->where('order_id', $return->order_id)
                            ->where('product_id', $item->product_id)
                            ->lockForUpdate()
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

                // 4. Update Customer Due & Calculate Refund / Note (if customer exists)
                $orderCode = $return->order_id
                    ? 'BRS' . $return->order_id
                    : 'N/A';

                $returnCode = 'BRET' . $return->id;

                if ($return->customer_id) {
                    $customer = Customer::query()
                        ->whereKey($return->customer_id)
                        ->lockForUpdate()
                        ->first();

                    if ($customer) {
                        $currentDue = round((float) ($customer->due ?? 0), 2);
                        $returnAmount = round((float) $return->total_amount, 2);

                        $dueBeforeTransaction = $currentDue;

                        if ($currentDue <= 0) {
                            // Scenario 1: Customer due is 0 -> full amount is cash refund, due stays 0
                            $newDue = 0.00;
                            $customer->update(['due' => $newDue]);

                            $note = "অর্ডার #{$orderCode} এর রিটার্ন ({$returnCode}) অনুমোদিত হয়েছে। কাস্টমারের কোনো বকেয়া না থাকায় মোট রিটার্ন ৳" . number_format($returnAmount, 2) . " ক্যাশ রিফান্ড হিসেবে প্রসেস করা হলো।";
                        } elseif ($currentDue < $returnAmount) {
                            // Scenario 2: Customer due is less than return amount -> cut due to 0, remaining is cash refund
                            $refundAmount = $returnAmount - $currentDue;
                            $adjustedDue = $currentDue;
                            $newDue = 0.00;
                            $customer->update(['due' => $newDue]);

                            $note = "অর্ডার #{$orderCode} এর রিটার্ন ({$returnCode}) অনুমোদিত হয়েছে। মোট ৳" . number_format($returnAmount, 2) . " এর মধ্যে আগের বকেয়া ৳" . number_format($adjustedDue, 2) . " সমন্বয় করা হলো এবং অবশিষ্ট ৳" . number_format($refundAmount, 2) . " ক্যাশ রিফান্ড হিসেবে প্রসেস করা হলো।";
                        } else {
                            // Scenario 3: Customer due is greater than or equal to return amount -> decrement due
                            $newDue = round($currentDue - $returnAmount, 2);
                            $customer->update(['due' => $newDue]);

                            $note = "অর্ডার #{$orderCode} এর রিটার্ন ({$returnCode}) অনুমোদিত হয়েছে। কাস্টমারের বকেয়া থেকে মোট ৳" . number_format($returnAmount, 2) . " কেটে সমন্বয় করা হলো (বর্তমান বকেয়া: ৳" . number_format($newDue, 2) . ")।";
                        }

                        // 5. Create Transaction (Type: return)
                        Transaction::create([
                            'customer_id' => $customer->id,
                            'order_id' => $return->order_id,
                            'sr_id' => $return->sr_id,
                            'branch_id' => $return->branch_id,
                            'type' => 'return',
                            'amount' => $returnAmount,
                            'due_before_transaction' => $dueBeforeTransaction,
                            'due_after_transaction'  => $newDue,
                            'status' => 'complete',
                            'note' => $note
                        ]);
                    }
                }

                $return->update(['status' => 'approved']);

                if ($return->order) {
                    $return->order->update([
                        'is_returned' => true,
                    ]);
                }

                // 6. Notify all managers of this branch
                $managers = User::where('role', 'manager')
                    ->where('branch_id', $return->branch_id)
                    ->get();

                foreach ($managers as $manager) {
                    $manager->notify(new SystemNotification([
                        'title'   => 'Return Approved & Refund Notice',
                        'message' => [
                            'text' => "Return {$returnCode} (Order {$orderCode}) has been approved.",
                            'from' => 'Admin'
                        ],
                        'url'     => route('notice.return', $return->id),
                        'type'    => 'return_approved'
                    ]));
                }

                return back()->with('success', 'Return approved successfully. Stocks, Customer Due, and Accounts updated.');
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

                    // Rollback Customer Due (if customer exists)
                    if ($return->customer) {
                        $return->customer->increment('due', $return->total_amount);
                    }

                    // Remove Transaction
                    Transaction::where('order_id', $return->order_id)
                        ->where('type', 'return')
                        ->where('amount', $return->total_amount)
                        ->where('note', 'like', "%Return ID: #{$return->id}%")
                        ->delete();
                }

                /*
                * ---------------------------------------------------------
                * Update Order Return Flag
                *
                * If another approved return exists for this order,
                * keep is_returned = true.
                *
                * Otherwise set it to false.
                * ---------------------------------------------------------
                */
                if ($return->order_id) {

                    $hasOtherApprovedReturn = ProductReturn::query()
                        ->where('order_id', $return->order_id)
                        ->where('status', 'approved')
                        ->where('id', '!=', $return->id)
                        ->exists();

                    $return->order->update([
                        'is_returned' => $hasOtherApprovedReturn,
                    ]);
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
