<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchCost;
use App\Models\CashTransfer;
use App\Models\DailyCashClosing;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashController extends Controller
{
    /**
     * Display the Manager's branch cash overview.
     */
    public function index()
    {
        $user = Auth::user();
        $branch = $user->branch ?? ($user->branch_id ? Branch::find($user->branch_id) : null);

        $today = Carbon::today();
        $todayClosing = $branch
            ? DailyCashClosing::where('branch_id', $branch->id)
                ->whereDate('closing_date', $today)
                ->first()
            : null;

        return view('pages.manager.cash.index', compact('branch', 'todayClosing'));
    }

    /**
     * Show the Daily Cash Closing page with computed financial figures.
     */
    public function createClosing()
    {
        $user = Auth::user();
        $branch = $user->branch ?? ($user->branch_id ? Branch::find($user->branch_id) : null);

        if (!$branch) {
            return redirect()->route('manager.cash.index')->with('error', 'No branch is assigned to your account.');
        }

        $today = Carbon::today();

        // 1. Prevent duplicate closing on the same date
        $alreadyClosed = DailyCashClosing::where('branch_id', $branch->id)
            ->whereDate('closing_date', $today)
            ->first();

        if ($alreadyClosed) {
            return redirect()->route('manager.cash.index')
                ->with('warning', 'Daily cash closing for today (' . $today->format('d M, Y') . ') has already been completed.');
        }

        // 2. Opening Cash: Yesterday / Most recent closed day's actual_cash
        $previousClosing = DailyCashClosing::where('branch_id', $branch->id)
            ->whereDate('closing_date', '<', $today)
            ->orderByDesc('closing_date')
            ->first();

        $openingCash = $previousClosing ? (float) $previousClosing->actual_cash : 0.00;

        // 3. Total Cash In for Today:
        // a) Complete customer payments
        $todayPayments = (float) Transaction::where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                      ->orWhereHas('customer', fn($c) => $c->where('branch_id', $branch->id));
            })
            ->where('type', 'pay')
            ->where('status', 'complete')
            ->whereDate('updated_at', $today)
            ->sum('amount');

        // b) Branch Retail Sales
        $todayRetailSales = (float) Order::where('branch_id', $branch->id)
            ->where('order_type', 'retail')
            ->whereDate('created_at', $today)
            ->sum('net_total');

        // c) Online Orders delivered today for this branch
        $todayOnlineDelivered = (float) Order::where('branch_id', $branch->id)
            ->where('order_type', 'online')
            ->where('status', 'delivered')
            ->where(function ($query) use ($today) {
                $query->whereDate('delivered_at', $today)
                      ->orWhere(function ($sub) use ($today) {
                          $sub->whereNull('delivered_at')
                              ->whereDate('updated_at', $today);
                      });
            })
            ->sum('net_total');

        $totalCashIn = $todayPayments + $todayRetailSales + $todayOnlineDelivered;

        // 4. Total Cash Out for Today:
        // a) Branch costs recorded today
        $todayBranchCosts = (float) BranchCost::where('branch_id', $branch->id)
            ->whereDate('cost_date', $today)
            ->sum('amount');

        // b) Cash transfers approved today
        $todayCashTransfers = (float) CashTransfer::where('branch_id', $branch->id)
            ->where('status', 'approved')
            ->whereDate('updated_at', $today)
            ->sum('amount');

        $totalCashOut = $todayBranchCosts + $todayCashTransfers;

        // 5. Expected Cash (Current balance recorded in branches table)
        $expectedCash = (float) ($branch->cash ?? 0.00);

        return view('pages.manager.cash.closing', compact(
            'branch',
            'today',
            'openingCash',
            'todayPayments',
            'todayRetailSales',
            'todayOnlineDelivered',
            'totalCashIn',
            'todayBranchCosts',
            'todayCashTransfers',
            'totalCashOut',
            'expectedCash'
        ));
    }

    /**
     * Store the finalized Daily Cash Closing record.
     */
    public function storeClosing(Request $request)
    {
        $user = Auth::user();
        $branch = $user->branch ?? ($user->branch_id ? Branch::find($user->branch_id) : null);

        if (!$branch) {
            return redirect()->route('manager.cash.index')->with('error', 'No branch assigned.');
        }

        $today = Carbon::today();

        // 1. Guard against duplicate submissions on the same date
        $alreadyClosed = DailyCashClosing::where('branch_id', $branch->id)
            ->whereDate('closing_date', $today)
            ->exists();

        if ($alreadyClosed) {
            return redirect()->route('manager.cash.index')
                ->with('warning', 'Daily cash closing for today has already been submitted.');
        }

        // 2. Validate input
        $validated = $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $actualCash = (float) $validated['actual_cash'];
        $expectedCash = (float) ($branch->cash ?? 0.00);
        $variance = round($actualCash - $expectedCash, 2);

        // 3. Conditional validation: Note is required if there is a shortage or excess
        if (abs($variance) > 0.001 && empty(trim($request->input('notes', '')))) {
            return back()
                ->withErrors(['notes' => 'A detailed note/reason is required when Actual Cash does not match Expected Cash.'])
                ->withInput();
        }

        // 4. Re-calculate financial figures for accuracy
        $previousClosing = DailyCashClosing::where('branch_id', $branch->id)
            ->whereDate('closing_date', '<', $today)
            ->orderByDesc('closing_date')
            ->first();
        $openingCash = $previousClosing ? (float) $previousClosing->actual_cash : 0.00;

        $todayPayments = (float) Transaction::where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                      ->orWhereHas('customer', fn($c) => $c->where('branch_id', $branch->id));
            })
            ->where('type', 'pay')
            ->where('status', 'complete')
            ->whereDate('updated_at', $today)
            ->sum('amount');

        $todayRetailSales = (float) Order::where('branch_id', $branch->id)
            ->where('order_type', 'retail')
            ->whereDate('created_at', $today)
            ->sum('net_total');

        $todayOnlineDelivered = (float) Order::where('branch_id', $branch->id)
            ->where('order_type', 'online')
            ->where('status', 'delivered')
            ->where(function ($query) use ($today) {
                $query->whereDate('delivered_at', $today)
                      ->orWhere(function ($sub) use ($today) {
                          $sub->whereNull('delivered_at')
                              ->whereDate('updated_at', $today);
                      });
            })
            ->sum('net_total');

        $totalCashIn = $todayPayments + $todayRetailSales + $todayOnlineDelivered;

        $todayBranchCosts = (float) BranchCost::where('branch_id', $branch->id)
            ->whereDate('cost_date', $today)
            ->sum('amount');

        $todayCashTransfers = (float) CashTransfer::where('branch_id', $branch->id)
            ->where('status', 'approved')
            ->whereDate('updated_at', $today)
            ->sum('amount');

        $totalCashOut = $todayBranchCosts + $todayCashTransfers;

        // 5. Store daily cash closing
        DB::transaction(function () use ($branch, $today, $openingCash, $totalCashIn, $totalCashOut, $expectedCash, $actualCash, $variance, $user, $request) {
            DailyCashClosing::create([
                'branch_id'     => $branch->id,
                'closing_date'  => $today,
                'opening_cash'  => $openingCash,
                'cash_in'       => $totalCashIn,
                'cash_out'      => $totalCashOut,
                'expected_cash' => $expectedCash,
                'actual_cash'   => $actualCash,
                'cash_variance' => $variance,
                'closed_by'     => $user->id,
                'closed_at'     => now(),
                'status'        => 'closed',
                'notes'         => $request->input('notes'),
            ]);
        });

        return redirect()->route('manager.cash.index')
            ->with('success', 'Daily cash closing completed successfully for ' . $today->format('d M, Y') . '!');
    }
}
