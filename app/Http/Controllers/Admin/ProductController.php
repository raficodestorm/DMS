<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use UploadHelper;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::orderBy('id', 'asc');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('amount')) {
            $query->where('price', '>=', $request->amount);
        }

        $products = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('pages.admin.product.table', compact('products'))->render(),
                'mobile' => view('pages.admin.product.mtable', compact('products'))->render(),
            ]);
        }

        return view('pages.admin.product.index', compact('products'));
    }


    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        return view('pages.admin.product.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'required|string|max:200|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'stock_alert' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadFile($request->file('image'), 'products');
        }
        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('pages.admin.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        return view('pages.admin.product.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => ['required', 'string', 'max:200', Rule::unique('products')->ignore($product->id),],
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'stock_alert' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $this->deleteFile($product->image);
            $validated['image'] = $this->uploadFile($request->file('image'), 'products');
        }
        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->deleteFile($product->image);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'product deleted successfully!');
    }
}
