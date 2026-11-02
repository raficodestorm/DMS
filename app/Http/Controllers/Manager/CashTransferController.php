<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CashTransfer;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashTransferController extends Controller
{
    /**
     * Display the Cash Transfer index page for the branch manager.
     */
    public function index()
    {
        $user = Auth::user();
        $branch = $user->branch ?? ($user->branch_id ? Branch::find($user->branch_id) : null);

        $totalTransfersCount = 0;
        $pendingSum = 0.00;
        $availableCash = 0.00;

        if ($branch) {
            $totalTransfersCount = CashTransfer::where('branch_id', $branch->id)->count();
            $pendingSum = (float) CashTransfer::where('branch_id', $branch->id)
                ->where('status', 'pending')
                ->sum('amount');
            $availableCash = max(0, (float) $branch->cash - $pendingSum);
        }

        return view('pages.manager.cash-transfer.index', compact(
            'branch',
            'totalTransfersCount',
            'pendingSum',
            'availableCash'
        ));
    }

    /**
     * Fetch Cash Transfers data via AJAX for dynamic filtering & pagination.
     */
    public function fetchData(Request $request)
    {
        $user = Auth::user();

        if (!$user->branch_id) {
            return response()->json([
                'table'      => '<tr><td colspan="8" class="text-center text-muted">No branch assigned to your account.</td></tr>',
                'mobile'     => '<p class="text-center text-muted">No branch assigned to your account.</p>',
                'total'      => 0,
                'pagination' => '',
            ]);
        }

        $query = CashTransfer::with(['requestedBy', 'branch'])
            ->where('branch_id', $user->branch_id);

        // Date Range Filtering
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Status Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search in Note or by ID / Amount
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanSearch = ltrim($search, '#');

            $query->where(function ($q) use ($search, $cleanSearch) {
                $q->where('note', 'like', "%{$search}%");
                if (is_numeric($cleanSearch)) {
                    $q->orWhere('id', (int) $cleanSearch)
                      ->orWhere('amount', 'like', "%{$cleanSearch}%");
                }
            });
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.manager.cash-transfer.table', compact('transfers'))->render(),
            'mobile'     => view('pages.manager.cash-transfer.mtable', compact('transfers'))->render(),
            'total'      => $transfers->total(),
            'pagination' => (string) $transfers->links(),
        ]);
    }

    /**
     * Store a new Cash Transfer request from Branch to Admin.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->branch_id) {
            return back()->with('error', 'You are not assigned to any branch.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'note'   => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                $branch = Branch::lockForUpdate()->findOrFail($user->branch_id);
                $amount = round((float) $validated['amount'], 2);
                $currentCash = (float) $branch->cash;

                // 1. Check if requested amount exceeds actual branch cash
                if ($currentCash < $amount) {
                    throw new \Exception('Transfer amount (৳ ' . number_format($amount, 2) . ') cannot exceed current branch cash balance (৳ ' . number_format($currentCash, 2) . ').');
                }

                // 2. Check if pending transfers consume the available balance
                $pendingSum = (float) CashTransfer::where('branch_id', $branch->id)
                    ->where('status', 'pending')
                    ->sum('amount');

                if (($currentCash - $pendingSum) < $amount) {
                    $available = max(0, $currentCash - $pendingSum);
                    throw new \Exception('You already have ৳ ' . number_format($pendingSum, 2) . ' in pending transfer requests. Maximum available for a new transfer is ৳ ' . number_format($available, 2) . '.');
                }

                $cashBefore = $currentCash;
                $cashAfter  = round($currentCash - $amount, 2);

                $transfer = CashTransfer::create([
                    'branch_id'            => $branch->id,
                    'amount'               => $amount,
                    'cash_before_transfer' => $cashBefore,
                    'cash_after_transfer'  => $cashAfter,
                    'requested_by'         => $user->id,
                    'status'               => 'pending',
                    'note'                 => $validated['note'] ?? null,
                ]);

                // 3. Notify Admins about the new request
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SystemNotification([
                        'title'   => 'New Cash Transfer Request',
                        'message' => "Branch '{$branch->name}' requested cash transfer of ৳ " . number_format($transfer->amount, 2),
                        'url'     => route('admin.cash-transfers.show', $transfer->id),
                        'type'    => 'cash_transfer',
                    ]));
                }
            });

            return redirect()
                ->route('manager.cash-transfers.index')
                ->with('success', 'Cash transfer request submitted successfully! Awaiting Admin approval.');

        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel / Delete a pending cash transfer request.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->branch_id) {
            return back()->with('error', 'Unauthorized.');
        }

        $transfer = CashTransfer::where('branch_id', $user->branch_id)->findOrFail($id);

        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfer requests can be cancelled.');
        }

        $transfer->delete();

        return redirect()
            ->route('manager.cash-transfers.index')
            ->with('success', 'Cash transfer request cancelled successfully.');
    }
}
