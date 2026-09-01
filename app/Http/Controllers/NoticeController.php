<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\Transaction;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Display Return Approval Notice Details with financial breakdown
     */
    public function showReturnNotice($id)
    {
        $user = auth()->user();

        $return = ProductReturn::with([
            'customer',
            'sr',
            'order',
            'branch',
            'items.product'
        ])->findOrFail($id);

        // Scope check: Manager can view returns belonging to their branch
        if ($user->role === 'manager' && $user->branch_id != $return->branch_id) {
            abort(403, 'Unauthorized access to this notice.');
        }

        // Fetch matching transaction record created during approval
        $transaction = Transaction::where('order_id', $return->order_id)
            ->where('customer_id', $return->customer_id)
            ->where('type', 'return')
            ->where('note', 'like', "%BRET{$return->id}%")
            ->latest()
            ->first();

        $totalReturn = (float) $return->total_amount;
        $currentDue = $transaction ? (float) $transaction->due : (float) $return->customer->due;
        $note = $transaction ? $transaction->note : 'Return approved successfully.';

        $adjustedDue = 0.00;
        $cashRefund = 0.00;
        $previousDue = 0.00;

        if (str_contains($note, 'ক্যাশ রিফান্ড')) {
            if (str_contains($note, 'আগের বকেয়া')) {
                // Partial due adjustment + cash refund
                if (preg_match('/আগের বকেয়া ৳([\d,.]+)/u', $note, $matches)) {
                    $adjustedDue = (float) str_replace(',', '', $matches[1]);
                } else {
                    $adjustedDue = 0.00;
                }
                $cashRefund = max(0, $totalReturn - $adjustedDue);
                $previousDue = $adjustedDue;
            } else {
                // Full cash refund
                $cashRefund = $totalReturn;
                $adjustedDue = 0.00;
                $previousDue = 0.00;
            }
        } else {
            // Full due cut / reduction
            $adjustedDue = $totalReturn;
            $cashRefund = 0.00;
            $previousDue = $currentDue + $totalReturn;
        }

        return view('pages.common.notice.show', compact(
            'return',
            'transaction',
            'totalReturn',
            'previousDue',
            'adjustedDue',
            'cashRefund',
            'currentDue',
            'note'
        ));
    }
}
