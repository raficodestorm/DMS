<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('pages.admin.bonus.index');
    }

    public function fetchBonusesIndexData(Request $request)
    {
        $query = Bonus::with('creator')->latest('bonus_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('month')) {
            $query->whereMonth('bonus_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('bonus_date', $request->year);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('bonus_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('bonus_date', '<=', $request->to_date);
        }

        $totalBonus = (clone $query)->sum('amount');
        $bonuses = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.bonus.table', compact('bonuses'))->render(),
            'mobile'     => view('pages.admin.bonus.mtable', compact('bonuses'))->render(),
            'pagination' => (string) $bonuses->links(),
            'total'      => $bonuses->total(),
            'totalBonus' => number_format($totalBonus, 2) . ' ৳',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.bonus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'bonus_date' => 'required|date',
            'type' => 'required|in:incentive,cashback,special,other',
            'description' => 'nullable|string|max:1000',
        ]);

        Bonus::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'bonus_date' => $request->bonus_date,
            'type' => $request->type,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.bonuses.index')
            ->with('success', 'Bonus entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonus $bonus) 
    {
        return view('pages.admin.bonus.show', compact('bonus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonus $bonus)
    {
        return view('pages.admin.bonus.edit', compact('bonus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bonus $bonus)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'bonus_date' => 'required|date',
            'type' => 'required|in:incentive,cashback,special,other',
            'description' => 'nullable|string|max:1000',
        ]);

        $bonus->update($request->all());

        return redirect()->route('admin.bonuses.index')
            ->with('success', 'Bonus entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus)
    {
        $bonus->delete();

        return redirect()->route('admin.bonuses.index')
            ->with('success', 'Bonus entry deleted successfully.');
    }
}
