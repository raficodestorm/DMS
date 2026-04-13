<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use Illuminate\Http\Request;
use App\Models\User;


class CostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $costs = Cost::with('user.counter')->latest()->get();
        return view('pages.admin.cost.index', compact('costs'));
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
        $validated = $request->validate([
            'amount' => 'required|integer',
            'purpose' => 'required|string|max:100',
            'staff_name' => 'nullable|string|max:100',
            'details' => 'required|string|max:100',
        ]);
        $validated['user_id'] = auth()->id();
        Cost::create($validated);
        return redirect()->route('admin.costs.index')->with('success', 'Cost added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cost $cost) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cost $cost)
    {
        return view('pages.admin.cost.edit', compact('cost'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cost $cost)
    {
        $validated = $request->validate([
            'amount' => 'required|integer',
            'purpose' => 'required|string|max:100',
            'staff_name' => 'nullable|string|max:100',
            'details' => 'required|string|max:100',
        ]);
        $validated['user_id'] = auth()->id();
        $cost->update($validated);
        return redirect()->route('admin.costs.index')->with('success', 'Cost updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cost $cost)
    {
        $cost->delete();
        return redirect()->route('admin.costs.index')->with('success', 'Cost deleted successfully');
    }
}
