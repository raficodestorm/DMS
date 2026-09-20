<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Slide;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    use UploadHelper;

    /**
     * Display a listing of the slides.
     */
    public function index()
    {
        $totalSlides    = Slide::count();
        $activeSlides   = Slide::where('status', 1)->count();
        $inactiveSlides = Slide::where('status', 0)->count();

        $today = now()->toDateString();
        $retailOffers = Offer::where('status', 1)
            ->where(function ($q) {
                $q->where('customer_type', 'retail')->orWhere('customer_type', 'all');
            })
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with('product')
            ->orderBy('name', 'asc')
            ->get();

        return view('pages.admin.slide.index', compact('totalSlides', 'activeSlides', 'inactiveSlides', 'retailOffers'));
    }

    /**
     * Fetch slide index data for AJAX table, filtering, and pagination.
     */
    public function fetchSlidesIndexData(Request $request)
    {
        $query = Slide::with(['offers', 'offers.product'])->ordered();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('custom_url', 'like', "%{$search}%")
                  ->orWhereHas('offers', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                         ->orWhereHas('product', function ($pq) use ($search) {
                             $pq->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }

        if ($request->filled('offer_id') && $request->offer_id !== '') {
            if ($request->offer_id === 'none') {
                $query->doesntHave('offers');
            } else {
                $query->whereHas('offers', function ($oq) use ($request) {
                    $oq->where('offers.id', $request->offer_id);
                });
            }
        }

        $activeCount = (clone $query)->where('status', 1)->count();
        $totalCount  = (clone $query)->count();
        $slides      = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'        => view('pages.admin.slide.table', compact('slides'))->render(),
            'mobile'       => view('pages.admin.slide.mtable', compact('slides'))->render(),
            'pagination'   => (string) $slides->links(),
            'total'        => $slides->total(),
            'activeSlides' => $activeCount,
            'totalSlides'  => $totalCount,
        ]);
    }

    /**
     * Show the form for creating a new slide.
     */
    public function create()
    {
        $today = now()->toDateString();
        $retailOffers = Offer::where('status', 1)
            ->where(function ($q) {
                $q->where('customer_type', 'retail')->orWhere('customer_type', 'all');
            })
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with(['product', 'product.category'])
            ->orderBy('name', 'asc')
            ->get();

        $nextPosition = (Slide::max('position') ?? 0) + 1;

        return view('pages.admin.slide.create', compact('retailOffers', 'nextPosition'));
    }

    /**
     * Store a newly created slide in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'position'     => 'required|integer|min:1',
            'offer_ids'    => 'nullable|array',
            'offer_ids.*'  => 'exists:offers,id',
            'custom_url'   => 'nullable|url|max:500',
            'image'        => 'required|image|mimes:jpeg,png,jpg,webp,svg,gif|max:5120',
            'status'       => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadFile($request->file('image'), 'slides');
        }

        $slide = Slide::create($validated);

        if ($request->has('offer_ids')) {
            $slide->offers()->sync($request->input('offer_ids', []));
        }

        return redirect()->route('admin.slides.index')->with('success', 'Slide banner created successfully!');
    }

    /**
     * Display the specified slide.
     */
    public function show(Slide $slide)
    {
        $slide->load(['offers', 'offers.product']);
        return view('pages.admin.slide.show', compact('slide'));
    }

    /**
     * Show the form for editing the specified slide.
     */
    public function edit(Slide $slide)
    {
        $today = now()->toDateString();
        $retailOffers = Offer::where('status', 1)
            ->where(function ($q) {
                $q->where('customer_type', 'retail')->orWhere('customer_type', 'all');
            })
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with(['product', 'product.category'])
            ->orderBy('name', 'asc')
            ->get();

        $slide->load('offers');

        return view('pages.admin.slide.edit', compact('slide', 'retailOffers'));
    }

    /**
     * Update the specified slide in database.
     */
    public function update(Request $request, Slide $slide)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'position'    => 'required|integer|min:1',
            'offer_ids'   => 'nullable|array',
            'offer_ids.*' => 'exists:offers,id',
            'custom_url'  => 'nullable|url|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif|max:5120',
            'status'      => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteFile($slide->image);
            $validated['image'] = $this->uploadFile($request->file('image'), 'slides');
        }

        $slide->update($validated);
        $slide->offers()->sync($request->input('offer_ids', []));

        return redirect()->route('admin.slides.index')->with('success', 'Slide banner updated successfully!');
    }

    /**
     * Remove the specified slide from database.
     */
    public function destroy(Slide $slide)
    {
        $this->deleteFile($slide->image);
        $slide->offers()->detach();
        $slide->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Slide banner deleted successfully!'
            ]);
        }

        return redirect()->route('admin.slides.index')->with('success', 'Slide banner deleted successfully!');
    }

    /**
     * Toggle slide active / inactive status.
     */
    public function toggleStatus(Slide $slide)
    {
        $slide->status = !$slide->status;
        $slide->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'     => true,
                'status'      => (bool) $slide->status,
                'totalActive' => Slide::where('status', 1)->count(),
                'message'     => $slide->status ? 'Slide activated successfully!' : 'Slide deactivated successfully!',
            ]);
        }

        return back()->with('success', $slide->status ? 'Slide activated successfully!' : 'Slide deactivated successfully!');
    }
}
