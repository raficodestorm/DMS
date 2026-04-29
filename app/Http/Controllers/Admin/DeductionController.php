<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeductionController extends Controller
{

    public function index()
    {
        $deductions = Deduction::latest()->paginate(10);
        return view('pages.admin.deduction.index', compact('deductions'));
    }


    public function create()
    {
        return view('pages.admin.deduction.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'type'            => 'required|in:main,specific',
            'customer_deduction' => 'required|numeric|min:0',
            'my_deduction' => 'required|numeric|min:0',
            'tree_deduction' => 'nullable|numeric|min:0',
            'floor_deduction' => 'nullable|numeric|min:0',

        ]);

        Deduction::create($validated);

        return redirect()->route('admin.deductions.index')->with('success', 'Deduction created successfully!');
    }

    public function show(Deduction $deduction)
    {
        return view('pages.admin.deduction.show', compact('deduction'));
    }



    public function edit(Deduction $deduction)
    {

        return view('pages.admin.deduction.edit', compact('deduction'));
    }


    public function update(Request $request, Deduction $deduction)
    {
        $validated = $request->validate([
            'type'            => 'required|in:main,specific',
            'customer_deduction' => 'required|numeric|min:0',
            'my_deduction' => 'required|numeric|min:0',
            'tree_deduction' => 'nullable|numeric|min:0',
            'floor_deduction' => 'nullable|numeric|min:0',
        ]);

        $deduction->update($validated);

        return redirect()->route('admin.deductions.index')->with('success', 'Deduction updated successfully!');
    }


    public function destroy(Deduction $deduction)
    {
        $deduction->delete();
        return redirect()->route('admin.deductions.index')->with('success', 'Deduction deleted successfully!');
    }
}
