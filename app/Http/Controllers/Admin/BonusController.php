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
        $query = Bonus::with('creator')->latest('bonus_date');

        // Filter by month/year if provided, else default to current month
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $query->whereMonth('bonus_date', $month)
              ->whereYear('bonus_date', $year);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $bonuses = $query->paginate(15);
        
        // Summary for the filtered period
        $totalBonus = Bonus::whereMonth('bonus_date', $month)
                           ->whereYear('bonus_date', $year)
                           ->sum('amount');

        return view('pages.admin.bonus.index', compact('bonuses', 'totalBonus', 'month', 'year'));
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
