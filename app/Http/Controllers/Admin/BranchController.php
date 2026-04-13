<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::orderBy('id', 'asc')->paginate(10);
        return view('pages.admin.branch.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return view('pages.admin.branch.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'manager' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Branch added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        return view('pages.admin.branch.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        return view('pages.admin.branch.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'manager' => 'required|string|max:100',
            'address' => 'required|string',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully!');
    }
}
