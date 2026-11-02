<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCash;
use App\Models\Branch;
use App\Models\DailyCashClosing;
use Illuminate\Http\Request;

class CashController extends Controller
{
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
}
