<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCash;
use App\Models\Branch;
use App\Models\CashTransfer;
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

        // 5. Recent Cash Transfer records
        $recentTransfers = CashTransfer::with(['branch', 'user'])
            ->latest()
            ->take(5)
            ->get();

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
            'averageBranchCash',
            'recentTransfers'
        ));
    }
}
