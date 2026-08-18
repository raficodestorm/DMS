<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'asc')->paginate(10);
        return view('pages.admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return view('pages.admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:250',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'category added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('pages.admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('pages.admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:250',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
{
    if ($category->products()->exists()) {
        return redirect()
            ->route('admin.categories.index')
            ->with(
                'error',
                'This category cannot be deleted because products are associated with it.'
            );
    }

    $category->delete();

    return redirect()
        ->route('admin.categories.index')
        ->with('success', 'Category deleted successfully!');
}
}
