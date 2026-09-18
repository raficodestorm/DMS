<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeductionController extends Controller
{

    public function index()
    {
        $deductions = Deduction::with('supplier')->latest()->paginate(10);
        return view('pages.admin.deduction.index', compact('deductions'));
    }


    public function create()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        return view('pages.admin.deduction.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                'unique:deductions,supplier_id',
            ],
            'type'            => 'required|in:main,specific',
            'customer_deduction' => 'required|numeric|min:0',
            'retail_deduction' => 'required|numeric|min:0',
            'my_deduction' => 'required|numeric|min:0',
        ], [
            'supplier_id.unique' => 'A deduction record already exists for this supplier.',
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
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        return view('pages.admin.deduction.edit', compact('deduction','suppliers'));
    }


    public function update(Request $request, Deduction $deduction)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                Rule::unique('deductions', 'supplier_id')->ignore($deduction->id),
            ],
            'type'            => 'required|in:main,specific',
            'customer_deduction' => 'required|numeric|min:0',
            'retail_deduction' => 'required|numeric|min:0',
            'my_deduction' => 'required|numeric|min:0',
        ], [
            'supplier_id.unique' => 'A deduction record already exists for this supplier.',
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
