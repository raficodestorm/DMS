<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use UploadHelper;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalCategories = Category::count();
        $totalFeatured   = Category::where('is_featured', true)->count();
        $totalProducts   = Product::whereNotNull('category_id')->count();

        return view('pages.admin.category.index', compact('totalCategories', 'totalFeatured', 'totalProducts'));
    }

    /**
     * Fetch category index data for dynamic AJAX table and pagination.
     */
    public function fetchCategoriesIndexData(Request $request)
    {
        $query = Category::withCount('products')->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(20)->withQueryString();

        return response()->json([
            'table'          => view('pages.admin.category.table', compact('categories'))->render(),
            'mobile'         => view('pages.admin.category.mtable', compact('categories'))->render(),
            'pagination'     => (string) $categories->links(),
            'total'          => $categories->total(),
            'total_featured' => Category::where('is_featured', true)->count(),
        ]);
    }

    /**
     * Toggle the featured status of a category.
     */
    public function toggleFeatured(Category $category)
    {
        $category->is_featured = !$category->is_featured;
        $category->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'        => true,
                'is_featured'    => (bool) $category->is_featured,
                'total_featured' => Category::where('is_featured', true)->count(),
                'message'        => $category->is_featured ? 'Category marked as featured!' : 'Category unfeatured successfully!',
            ]);
        }

        return back()->with('success', $category->is_featured ? 'Category marked as featured!' : 'Category unfeatured successfully!');
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
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'is_featured' => 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadFile($request->file('image'), 'categories');
        }

        $category = Category::create($validated);

        return redirect()->route('admin.categories.show', $category)->with('success', 'Category added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->loadCount('products');

        return view('pages.admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('pages.admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => 'nullable|string|max:1000',
            'is_featured' => 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            // Delete existing image if any
            $this->deleteFile($category->image);
            $validated['image'] = $this->uploadFile($request->file('image'), 'categories');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteFile($category->image);
            $validated['image'] = null;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.show', $category)->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with(
                    'error',
                    "This category cannot be deleted because {$productCount} product(s) are assigned to it. Please reassign or remove those products first."
                );
        }

        $this->deleteFile($category->image);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
