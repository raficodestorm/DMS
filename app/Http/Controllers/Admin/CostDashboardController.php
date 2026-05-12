<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchCost;
use App\Models\CompanyCost;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CostDashboardController extends Controller
{
    /**
     * Display the cost dashboard with global and branch summaries.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        // 1. Global Costs Summary
        $globalTotal = CompanyCost::whereMonth('cost_date', $month)
                                  ->whereYear('cost_date', $year)
                                  ->sum('amount');

        // 2. Branch-wise Costs Summary
        $branches = Branch::with(['branchCosts' => function ($query) use ($month, $year) {
            $query->whereMonth('cost_date', $month)
                  ->whereYear('cost_date', $year);
        }])->get()->map(function ($branch) {
            $branch->total_cost = $branch->branchCosts->sum('amount');
            return $branch;
        });

        $branchesTotal = $branches->sum('total_cost');
        $grandTotal = $globalTotal + $branchesTotal;

        return view('pages.admin.cost.dashboard', compact(
            'globalTotal', 
            'branches', 
            'branchesTotal', 
            'grandTotal', 
            'month', 
            'year'
        ));
    }

    /**
     * Display specific costs for a branch.
     */
    public function branchCosts(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $query = BranchCost::where('branch_id', $id)->with('creator')->latest('cost_date');

        $query->whereMonth('cost_date', $month)
              ->whereYear('cost_date', $year);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $costs = $query->paginate(15);
        $totalCost = $query->sum('amount');

        return view('pages.admin.cost.branch_costs', compact('branch', 'costs', 'totalCost', 'month', 'year'));
    }
}
