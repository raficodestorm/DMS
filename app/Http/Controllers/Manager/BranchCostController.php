<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BranchCost;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BranchCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $query = BranchCost::where('branch_id', $branchId)->with('creator')->latest('cost_date');

        // Filters
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));
        
        $query->whereMonth('cost_date', $month)
              ->whereYear('cost_date', $year);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $costs = $query->paginate(15);
        
        // Summary for current filter
        $totalCost = BranchCost::where('branch_id', $branchId)
                               ->whereMonth('cost_date', $month)
                               ->whereYear('cost_date', $year)
                               ->sum('amount');

        return view('pages.manager.cost.index', compact('costs', 'totalCost', 'month', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.manager.cost.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cost_date' => 'required|date',
            'category' => 'required|in:office,transport,staff,maintenance,product,utility,marketing,miscellaneous',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        BranchCost::create([
            'branch_id' => Auth::user()->branch_id,
            'amount' => $request->amount,
            'cost_date' => $request->cost_date,
            'category' => $request->category,
            'description' => $request->description,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('manager.costs.index')
            ->with('success', 'Cost entry recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cost = BranchCost::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return view('pages.manager.cost.show', compact('cost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cost = BranchCost::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return view('pages.manager.cost.edit', compact('cost'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cost = BranchCost::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cost_date' => 'required|date',
            'category' => 'required|in:office,transport,staff,maintenance,product,utility,marketing,miscellaneous',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cost->update($request->all());

        return redirect()->route('manager.costs.index')
            ->with('success', 'Cost entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cost = BranchCost::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $cost->delete();

        return redirect()->route('manager.costs.index')
            ->with('success', 'Cost entry deleted successfully.');
    }
}
