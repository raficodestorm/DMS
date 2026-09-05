<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    use UploadHelper;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'asc')->paginate(10);
        return view('pages.admin.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'required|string|max:200',
            'phone' => 'required|string|max:200',
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:suppliers,email'],
            'address' => 'nullable|string|max:500',
            'due' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadFile($request->file('image'), 'suppliers');
        }

        $validated['due'] = $request->input('due', 0.00);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('pages.admin.supplier.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('pages.admin.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'required|string|max:200',
            'phone' => 'required|string|max:200',
            'email' => ['nullable', 'string', 'email', Rule::unique('suppliers')->ignore($supplier->id)],
            'address' => 'nullable|string|max:500',
            'due' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteFile($supplier->image);
            $validated['image'] = $this->uploadFile($request->file('image'), 'suppliers');
        }

        if ($request->has('due')) {
            $validated['due'] = $request->input('due', 0.00);
        }

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->deleteFile($supplier->image);
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully!');
    }
}
