<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCash;
use App\Models\AdminCashLedger;
use App\Models\Branch;
use App\Models\DailyCashClosing;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CashController extends Controller
{
    /**
     * Ensure the admin_cash_ledgers table exists.
     */
    protected function ensureLedgerTableExists(): void
    {
        if (!Schema::hasTable('admin_cash_ledgers')) {
            Schema::create('admin_cash_ledgers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type'); // 'cash-in', 'supplier_payment', 'company_cost', 'cash_transfer'
                $table->decimal('amount', 10, 2);
                $table->decimal('cash_before', 10, 2)->default(0);
                $table->decimal('cash_after', 10, 2)->default(0);
                $table->string('payment_method')->nullable();
                $table->string('status')->default('complete');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Get or create the AdminCash record.
     */
    protected function getAdminCashRecord(): AdminCash
    {
        $adminId = Auth::id();
        $adminCash = AdminCash::where('admin_id', $adminId)->lockForUpdate()->first();
        if (!$adminCash) {
            $adminCash = AdminCash::lockForUpdate()->first();
        }
        if (!$adminCash) {
            $adminCash = AdminCash::create([
                'admin_id' => $adminId,
                'cash'     => 0,
            ]);
        }
        return $adminCash;
    }

    /**
     * Display the comprehensive company cash dashboard.
     * Shows:
     * 1. Grand Company Current Cash (Admin Cash + All Branches Cash)
     * 2. Admin Central Cash
     * 3. Individual Branch Cash Cards
     */
    public function index(Request $request)
    {
        // 1. Fetch Admin Cash records with admin user details
        $adminCashRecords = AdminCash::with('admin')->get();
        $totalAdminCash = (float) $adminCashRecords->sum('cash');

        // 2. Fetch all Branch Cash balances and details
        $branches = Branch::select('id', 'name', 'manager', 'address', 'cash', 'updated_at')
            ->orderBy('name', 'asc')
            ->get();
        $totalBranchCash = (float) $branches->sum('cash');
        $branchCount = $branches->count();

        // 3. Grand Total: Company Current Cash
        $totalCompanyCash = $totalAdminCash + $totalBranchCash;

        // 4. Analytics & Percentage Shares
        $adminSharePercent = $totalCompanyCash > 0 ? round(($totalAdminCash / $totalCompanyCash) * 100, 1) : 0;
        $branchSharePercent = $totalCompanyCash > 0 ? round(($totalBranchCash / $totalCompanyCash) * 100, 1) : 0;

        $topBranch = $branches->sortByDesc('cash')->first();
        $averageBranchCash = $branchCount > 0 ? ($totalBranchCash / $branchCount) : 0;


        return view('pages.admin.cash.index', compact(
            'totalCompanyCash',
            'totalAdminCash',
            'adminCashRecords',
            'totalBranchCash',
            'branches',
            'branchCount',
            'adminSharePercent',
            'branchSharePercent',
            'topBranch',
            'averageBranchCash'
        ));
    }

    /**
     * Display the Admin Daily Cash Closing History index page (all branches).
     */
    public function closingHistory()
    {
        $branches      = Branch::select('id', 'name')->orderBy('name')->get();
        $totalClosings = DailyCashClosing::count();

        return view('pages.admin.cash.closing-history', compact('branches', 'totalClosings'));
    }

    /**
     * Fetch Daily Cash Closing history data via AJAX (all branches, with filters).
     */
    public function fetchClosingHistoryData(Request $request)
    {
        $query = DailyCashClosing::with(['branch', 'closedBy']);

        // Branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('closing_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('closing_date', '<=', $request->to_date);
        }

        // Search in notes
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('notes', 'like', "%{$search}%");
        }

        $closings = $query->orderByDesc('closing_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.cash.closing-history-table', compact('closings'))->render(),
            'mobile'     => view('pages.admin.cash.closing-history-mtable', compact('closings'))->render(),
            'total'      => $closings->total(),
            'pagination' => (string) $closings->links(),
        ]);
    }

    /**
     * Process Cash-In for Admin Cash and record in AdminCashLedger.
     */
    public function cashIn(Request $request)
    {
        $this->ensureLedgerTableExists();

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'note'           => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $amount = round((float) $validated['amount'], 2);
                $adminId = Auth::id();

                $adminCash = $this->getAdminCashRecord();
                $cashBefore = (float) $adminCash->cash;
                $adminCash->increment('cash', $amount);
                $cashAfter = round($cashBefore + $amount, 2);

                AdminCashLedger::create([
                    'admin_id'       => $adminId,
                    'type'           => 'cash-in',
                    'amount'         => $amount,
                    'cash_before'    => $cashBefore,
                    'cash_after'     => $cashAfter,
                    'payment_method' => $validated['payment_method'],
                    'status'         => 'complete',
                    'note'           => $validated['note'] ?? 'Cash In',
                ]);
            });

            return back()->with('success', 'Cash in of ৳ ' . number_format($validated['amount'], 2) . ' recorded successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error recording cash in: ' . $e->getMessage());
        }
    }

    /**
     * Display the Admin Cash Ledger index page.
     * Modeled after the Customer Ledger page.
     */
    public function ledgerIndex(Request $request)
    {
        $this->ensureLedgerTableExists();

        $totalAdminCash = (float) AdminCash::sum('cash');
        $totalEntries   = AdminCashLedger::count();

        // Calculate summary metrics
        $totalInflow  = (float) AdminCashLedger::whereIn('type', ['cash-in', 'cash_transfer'])->sum('amount');
        $totalOutflow = (float) AdminCashLedger::whereIn('type', ['supplier_payment', 'company_cost'])->sum('amount');

        return view('pages.admin.cash.ledger-index', compact('totalAdminCash', 'totalEntries', 'totalInflow', 'totalOutflow'));
    }

    /**
     * Fetch Admin Cash Ledger data via AJAX with filters.
     */
    public function fetchLedgerData(Request $request)
    {
        $this->ensureLedgerTableExists();

        $query = AdminCashLedger::with('admin')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                if (preg_match('/^ACL00(\d+)$/i', $search, $match)) {
                    $q->where('id', $match[1]);
                    return;
                }
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhereHas('admin', function ($adminQ) use ($search) {
                      $adminQ->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $totalCount = $query->count();
        $ledgers    = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.cash.ledger-table', compact('ledgers'))->render(),
            'mobile'     => view('pages.admin.cash.ledger-mtable', compact('ledgers'))->render(),
            'total'      => $totalCount,
            'pagination' => (string) $ledgers->links(),
        ]);
    }

    /**
     * Show single ledger record.
     */
    public function ledgerShow($id)
    {
        $this->ensureLedgerTableExists();
        $ledger = AdminCashLedger::with('admin')->findOrFail($id);
        return view('pages.admin.cash.ledger-show', compact('ledger'));
    }
}
