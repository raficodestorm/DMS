<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCash;
use App\Models\Branch;
use App\Models\CashTransfer;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashTransferController extends Controller
{
    /**
     * Display the Admin Cash Transfer index page.
     */
    public function index()
    {
        $branches = Branch::select('id', 'name')->orderBy('name', 'asc')->get();
        return view('pages.admin.cash-transfer.index', compact('branches'));
    }

    /**
     * Fetch Cash Transfers data via AJAX for Admin.
     */
    public function fetchData(Request $request)
    {
        $query = CashTransfer::with(['branch', 'requestedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by Branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Date Range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by Note or ID / Amount
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

        $transfers = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.cash-transfer.table', compact('transfers'))->render(),
            'mobile'     => view('pages.admin.cash-transfer.mtable', compact('transfers'))->render(),
            'total'      => $transfers->total(),
            'pagination' => (string) $transfers->links(),
        ]);
    }

    /**
     * Show Cash Transfer details page for Admin (with Approve/Reject action).
     */
    public function show($id)
    {
        $transfer = CashTransfer::with(['branch', 'requestedBy'])->findOrFail($id);

        // Fetch central admin cash total for context
        $totalAdminCash = (float) AdminCash::sum('cash');

        return view('pages.admin.cash-transfer.show', compact('transfer', 'totalAdminCash'));
    }

    /**
     * Approve cash transfer: deduct from branch cash and credit to admin central cash.
     */
    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transfer = CashTransfer::lockForUpdate()->findOrFail($id);

                if ($transfer->status !== 'pending') {
                    throw new \Exception("This transfer request has already been {$transfer->status}.");
                }

                $branch = Branch::lockForUpdate()->findOrFail($transfer->branch_id);
                $amount = (float) $transfer->amount;

                // Validate branch has sufficient cash to transfer
                if ((float) $branch->cash < $amount) {
                    throw new \Exception("Cannot approve: Branch '{$branch->name}' currently has ৳ " . number_format($branch->cash, 2) . ", which is less than the requested transfer of ৳ " . number_format($amount, 2) . ".");
                }

                // 1. Deduct from Branch Cash
                $cashBefore = (float) $branch->cash;
                $branch->decrement('cash', $amount);
                $cashAfter = round($cashBefore - $amount, 2);

                // 2. Credit to Central Admin Cash
                $adminId = Auth::id();
                $adminCash = AdminCash::where('admin_id', $adminId)->lockForUpdate()->first()
                    ?? AdminCash::lockForUpdate()->first();

                $adminCashBefore = $adminCash ? (float) $adminCash->cash : 0;

                if (!$adminCash) {
                    $adminCash = AdminCash::create([
                        'admin_id' => $adminId,
                        'cash'     => $amount,
                    ]);
                    $adminCashAfter = $amount;
                } else {
                    $adminCash->increment('cash', $amount);
                    $adminCashAfter = round($adminCashBefore + $amount, 2);
                }

                // Log in Admin Cash Ledger
                if (\Illuminate\Support\Facades\Schema::hasTable('admin_cash_ledgers')) {
                    \App\Models\AdminCashLedger::create([
                        'admin_id'       => $adminId,
                        'type'           => 'cash_transfer',
                        'amount'         => $amount,
                        'cash_before'    => $adminCashBefore,
                        'cash_after'     => $adminCashAfter,
                        'payment_method' => 'cash',
                        'status'         => 'complete',
                        'reference_type' => 'CashTransfer',
                        'reference_id'   => $transfer->id,
                        'note'           => "Cash transfer received from {$branch->name}",
                    ]);
                }

                // 3. Mark Transfer as Approved
                $transfer->update([
                    'status'               => 'approved',
                    'cash_before_transfer' => $cashBefore,
                    'cash_after_transfer'  => $cashAfter,
                ]);

                // 4. Send System Notification to Branch Manager
                if ($transfer->requestedBy) {
                    $transfer->requestedBy->notify(new SystemNotification([
                        'title'   => 'Cash Transfer Approved',
                        'message' => "Your cash transfer request of ৳ " . number_format($amount, 2) . " from {$branch->name} has been approved by Admin.",
                        'url'     => route('manager.cash-transfers.index'),
                        'type'    => 'cash_transfer_approved',
                    ]));
                }
            });

            return redirect()
                ->route('admin.cash-transfers.show', $id)
                ->with('success', 'Cash transfer approved successfully! Branch cash has been deducted and credited to Admin Cash.');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject cash transfer request.
     */
    public function reject($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transfer = CashTransfer::lockForUpdate()->findOrFail($id);

                if ($transfer->status !== 'pending') {
                    throw new \Exception("Only pending transfer requests can be rejected.");
                }

                $transfer->update([
                    'status' => 'rejected',
                ]);

                // Notify requesting user
                if ($transfer->requestedBy) {
                    $transfer->requestedBy->notify(new SystemNotification([
                        'title'   => 'Cash Transfer Rejected',
                        'message' => "Your cash transfer request of ৳ " . number_format($transfer->amount, 2) . " from {$transfer->branch->name} was rejected by Admin.",
                        'url'     => route('manager.cash-transfers.index'),
                        'type'    => 'cash_transfer_rejected',
                    ]));
                }
            });

            return redirect()
                ->route('admin.cash-transfers.show', $id)
                ->with('success', 'Cash transfer request rejected.');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }
}
