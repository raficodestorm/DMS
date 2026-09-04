<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyCost;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CompanyCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('pages.admin.cost.index');
    }

    public function fetchCompanyCostsIndexData(Request $request)
    {
        $query = CompanyCost::with('creator')->latest('cost_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('month')) {
            $query->whereMonth('cost_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('cost_date', $request->year);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('cost_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('cost_date', '<=', $request->to_date);
        }

        $totalCost = (clone $query)->sum('amount');
        $costs = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.cost.table', compact('costs'))->render(),
            'mobile'     => view('pages.admin.cost.mtable', compact('costs'))->render(),
            'pagination' => (string) $costs->links(),
            'total'      => $costs->total(),
            'totalCost'  => number_format($totalCost, 2) . ' ৳',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.cost.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cost_date' => 'required|date',
            'category' => 'required|in:office,transport,staff,maintenance,salary,product,utility,marketing,miscellaneous',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        CompanyCost::create([
            'amount' => $request->amount,
            'cost_date' => $request->cost_date,
            'category' => $request->category,
            'description' => $request->description,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.company_costs.index')
            ->with('success', 'Company cost recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cost = CompanyCost::findOrFail($id);
        return view('pages.admin.cost.show', compact('cost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cost = CompanyCost::findOrFail($id);
        return view('pages.admin.cost.edit', compact('cost'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cost = CompanyCost::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cost_date' => 'required|date',
            'category' => 'required|in:office,transport,staff,maintenance,salary,product,utility,marketing,miscellaneous',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cost->update($request->all());

        return redirect()->route('admin.company_costs.index')
            ->with('success', 'Company cost updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cost = CompanyCost::findOrFail($id);
        $cost->delete();

        return redirect()->route('admin.company_costs.index')
            ->with('success', 'Company cost deleted successfully.');
    }
}
