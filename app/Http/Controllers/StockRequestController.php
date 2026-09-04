<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockInItem;
use App\Models\StockInRequest;
use App\Models\Supplier;
use App\Services\PurchasePriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
  public function createStockRequest()
  {
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    return view('pages.manager.stock.stock-in-create', compact('suppliers'));
  }

  public function getProductsBySupplier($supplier_id)
  {
    $products = Product::where('supplier_id', $supplier_id)
      ->select('id', 'supplier_id', 'name', 'price')
      ->orderBy('name', 'asc')
      ->get();

    return response()->json($products);
  }


  public function store(Request $request)
  {

    $request->validate([
      'supplier_id'                   => 'required|exists:suppliers,id',
      'products'                      => 'required|array|min:1',
      'products.*.product_id'         => 'required|exists:products,id',
      'products.*.qty'                => 'required|integer|min:1',
      'products.*.tree_deduction'     => 'nullable|numeric|min:0',
      'net_total'                     => 'required|numeric|min:0',
    ]);

    try {
      DB::transaction(function () use ($request) {
        $stockRequest = StockInRequest::create([
          'supplier_id' => $request->supplier_id,
          'requested_by' => Auth::id(),
          'branch_id'   => auth()->user()->branch_id,
          'net_total' => $request->net_total,
          'status' => 'pending'
        ]);

        foreach ($request->products as $item) {
          $product = \App\Models\Product::find($item['product_id']);

          StockInItem::create([
            'stock_in_request_id' => $stockRequest->id,
            'product_id'          => $item['product_id'],
            'quantity'            => $item['qty'],
            'cost_price'          => $product->price,
            'tree_deduction'      => $item['tree_deduction'] ?? 0,
          ]);
        }

        // --- notification data as array ---
        $notificationData = [
          'title'   => 'Stock-In Request',
          'message' => [
            'text' => 'A new stock-in request from',
            'from' => Auth::user()->branch->name
          ],
          'url'     => route('admin.stock.in.request.show', $stockRequest->id),
          'type'    => 'stock_request'
        ];

        // 🔔 Notify Admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
          // sent by $notificationData array
          $admin->notify(new \App\Notifications\SystemNotification($notificationData));
        }
      });

      return redirect()->route('manager.stock.in.requests.index')->with('success', 'Stock request sent successfully!');
    } catch (\Exception $e) {
      dd($e);
    }
  }

  /**
   * Load UI page only for Stock-In Requests (no heavy query on page load).
   */
  public function stockInRequestIndexForAdmin()
  {
    $branches = \App\Models\Branch::select('id', 'name')->orderBy('name', 'asc')->get();
    return view('pages.admin.stock.stock-in-requests-index', compact('branches'));
  }

  /**
   * Fetch Stock-In Requests data via AJAX.
   */
  public function fetchStockInRequestsData(Request $request)
  {
    $query = StockInRequest::with(['supplier', 'requestedBy.branch', 'branch'])
      ->orderBy('created_at', 'desc');

    if ($request->filled('branch_id')) {
      $branchId = $request->branch_id;
      $query->where(function ($q) use ($branchId) {
        $q->where('branch_id', $branchId)
          ->orWhereHas('requestedBy', function ($userQ) use ($branchId) {
            $userQ->where('branch_id', $branchId);
          });
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    $requests = $query->paginate(15)->withQueryString();

    return response()->json([
      'table'      => view('pages.admin.stock.stock-in-requests-table', compact('requests'))->render(),
      'mobile'     => view('pages.admin.stock.stock-in-requests-mtable', compact('requests'))->render(),
      'total'      => $requests->total(),
      'pagination' => (string) $requests->links(),
    ]);
  }

  /**
   * Load UI page for Manager Stock-In Requests.
   */
  public function stockInRequestIndexForManager()
  {
    return view('pages.manager.stock.stock-in-requests-index');
  }

  /**
   * Fetch Manager Stock-In Requests data via AJAX.
   */
  public function fetchStockInRequestsDataForManager(Request $request)
  {
    $query = StockInRequest::with(['supplier', 'requestedBy'])
      ->where('branch_id', auth()->user()->branch_id);

    // Filter by Date Range
    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    // Filter by Status (pending, approved, rejected)
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // Search by Supplier Name
    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->whereHas('supplier', function ($sq) use ($search) {
        $sq->where('company_name', 'like', "%{$search}%");
      });
    }

    $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

    return response()->json([
      'table'      => view('pages.manager.stock.stock-in-requests-table', compact('requests'))->render(),
      'mobile'     => view('pages.manager.stock.stock-in-requests-mtable', compact('requests'))->render(),
      'total'      => $requests->total(),
      'pagination' => (string) $requests->links(),
    ]);
  }

  // for showing request details
  public function showForAdmin($id)
  {
    $request = StockInRequest::with(['supplier', 'requestedBy', 'items.product'])->findOrFail($id);
    return view('pages.admin.stock.stock-in-request-show', compact('request'));
  }


  // ম্যানেজারের জন্য শো পেজ
  public function showForManager($id)
  {
    $request = StockInRequest::with(['supplier', 'items.product'])->findOrFail($id);
    return view('pages.manager.stock.stock-in-request-show', compact('request'));
  }

  // রিকোয়েস্ট ডিলিট করা
  public function stockInDestroy($id)
  {
    $request = StockInRequest::findOrFail($id);

    if ($request->status == 'approved') {
      return back()->with('error', 'Approved requests cannot be deleted.');
    }

    DB::transaction(function () use ($request) {
      $request->items()->delete();
      $request->delete();
    });

    return redirect()->route('manager.stock.in.requests.index')->with('success', 'Request deleted successfully.');
  }


  public function stockInEdit($id)
  {
    $request = StockInRequest::findOrFail($id);
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    if ($request->status == 'approved') {
      return back()->with('error', 'You cannot edit approved request.');
    }

    return view('pages.manager.stock.stock-in-edit', compact('request', 'suppliers'));
  }


  public function stockInUpdate(Request $request, $id)
  {
    $request->validate([
      'supplier_id'               => 'required|exists:suppliers,id',
      'products'                  => 'required|array|min:1',
      'products.*.product_id'     => 'required|exists:products,id',
      'products.*.qty'            => 'required|integer|min:1',
      'products.*.tree_deduction' => 'nullable|numeric|min:0',
      'net_total'                 => 'required|numeric|min:0',
    ]);

    try {
      return DB::transaction(function () use ($request, $id) {
        $stockRequest = StockInRequest::where('branch_id', auth()->user()->branch_id)
          ->findOrFail($id);

        if ($stockRequest->status !== 'pending') {
          return back()->with('error', 'Only pending requests can be updated.');
        }

        $stockRequest->update([
          'supplier_id' => $request->supplier_id,
          'net_total' => $request->net_total,
        ]);

        $stockRequest->items()->delete();

        $itemsToInsert = [];
        foreach ($request->products as $item) {

          $product = \App\Models\Product::find($item['product_id']);

          $itemsToInsert[] = [
            'stock_in_request_id' => $stockRequest->id,
            'product_id'          => $item['product_id'],
            'quantity'            => $item['qty'],
            'cost_price'          => $product->price,
            'tree_deduction'      => $item['tree_deduction'] ?? 0,
            'created_at'          => now(),
            'updated_at'          => now(),
          ];
        }

        StockInItem::insert($itemsToInsert);

        return redirect()->route('manager.stock.in.requests.index')->with('success', 'Stock request updated successfully!');
      });
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function approve($id)
  {
    try {
      DB::transaction(function () use ($id) {

        // Eager-load product on items so the calculator can read product.price
        $stockInRequest = StockInRequest::with(['items.product', 'requestedBy'])->findOrFail($id);

        if ($stockInRequest->status !== 'pending') {
          throw new \Exception('This request has already been ' . $stockInRequest->status);
        }

        $targetBranchId = $stockInRequest->branch_id;

        if (!$targetBranchId) {
          throw new \Exception('The requesting user is not assigned to any branch.');
        }

        // Fetch the active deduction policy once — avoid repeated DB calls inside the loop
        $deduction = Deduction::where('type', 'main')->first();

        if (!$deduction) {
          throw new \Exception('No main deduction policy found. Please configure deductions first.');
        }

        $calculator = new PurchasePriceCalculator();

        foreach ($stockInRequest->items as $item) {
          // 1. Update branch stock
          $stock = Stock::firstOrCreate(
            ['product_id' => $item->product_id, 'branch_id' => $targetBranchId],
            ['quantity' => 0]
          );
          $stock->increment('quantity', $item->quantity);

          // 2. Calculate and persist the new purchase_price on the product
          $newPurchasePrice = $calculator->calculate($item, $deduction);
          $item->product->update(['purchase_price' => $newPurchasePrice]);
        }

        $stockInRequest->update([
          'status'      => 'approved',
          'approved_by' => Auth::id(),
        ]);
      });

      return redirect()->back()->with('success', 'Approved! Branch stock updated and purchase prices recalculated.');
    } catch (\Exception $e) {
      return back()->with('error', $e->getMessage());
    }
  }

  public function reject($id)
  {
    try {
      $request = StockInRequest::with('requestedBy')->findOrFail($id);

      if ($request->status !== 'pending') {
        return back()->with('error', 'Only pending requests can be rejected.');
      }

      $request->update(['status' => 'rejected']);

      return redirect()->route('dashboards')->with('success', 'Request rejected!');
    } catch (\Exception $e) {
      return back()->with('error', 'Something went wrong!');
    }
  }















public function stockInAdminEdit($id)
  {
    $request = StockInRequest::findOrFail($id);
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    
    return view('pages.admin.stock.stock-in-edit', compact('request', 'suppliers'));
  }




  public function stockInAdminUpdate(Request $request, $id)
{
    $request->validate([
        'supplier_id'               => 'required|exists:suppliers,id',
        'products'                  => 'required|array|min:1',
        'products.*.product_id'     => 'required|exists:products,id',
        'products.*.qty'            => 'required|integer|min:1',
        'products.*.tree_deduction' => 'nullable|numeric|min:0',
        'net_total'                 => 'required|numeric|min:0',
    ]);

    try {

        DB::transaction(function () use ($request, $id) {

            $stockRequest = StockInRequest::with([
                'items.product',
                'requestedBy'
            ])->lockForUpdate()->findOrFail($id);

            $branchId = $stockRequest->branch_id;

            if (!$branchId) {
                throw new \Exception('Branch not found.');
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 1
            | Rollback old approved stock effect
            |--------------------------------------------------------------------------
            */
            if ($stockRequest->status === 'approved') {

                foreach ($stockRequest->items as $oldItem) {

                    $stock = Stock::where([
                        'product_id' => $oldItem->product_id,
                        'branch_id'  => $branchId,
                    ])->lockForUpdate()->first();

                    if ($stock) {

                        $newQty = $stock->quantity - $oldItem->quantity;

                        if ($newQty < 0) {
                            throw new \Exception(
                                "Stock inconsistency detected for {$oldItem->product->name}"
                            );
                        }

                        $stock->update([
                            'quantity' => $newQty
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 2
            | Update request header
            |--------------------------------------------------------------------------
            */

            $stockRequest->update([
                'supplier_id' => $request->supplier_id,
                'net_total'   => $request->net_total,
            ]);

            /*
            |--------------------------------------------------------------------------
            | STEP 3
            | Replace request items
            |--------------------------------------------------------------------------
            */

            $stockRequest->items()->delete();

            $itemsToInsert = [];

            foreach ($request->products as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                $itemsToInsert[] = [
                    'stock_in_request_id' => $stockRequest->id,
                    'product_id'          => $product->id,
                    'quantity'            => $item['qty'],
                    'cost_price'          => $product->price,
                    'tree_deduction'      => $item['tree_deduction'] ?? 0,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            StockInItem::insert($itemsToInsert);

            /*
            |--------------------------------------------------------------------------
            | STEP 4
            | Re-load items
            |--------------------------------------------------------------------------
            */

            $stockRequest->load('items.product');

            /*
            |--------------------------------------------------------------------------
            | STEP 5
            | Re-apply stock effect
            |--------------------------------------------------------------------------
            */

            if ($stockRequest->status === 'approved') {

                $deduction = Deduction::where(
                    'type',
                    'main'
                )->first();

                $calculator = new PurchasePriceCalculator();

                foreach ($stockRequest->items as $item) {

                    $stock = Stock::firstOrCreate(
                        [
                            'product_id' => $item->product_id,
                            'branch_id'  => $branchId,
                        ],
                        [
                            'quantity' => 0
                        ]
                    );

                    $stock->increment(
                        'quantity',
                        $item->quantity
                    );

                    $purchasePrice =
                        $calculator->calculate(
                            $item,
                            $deduction
                        );

                    $item->product->update([
                        'purchase_price' => $purchasePrice
                    ]);
                }
            }

        });

        return redirect()
            ->back()
            ->with(
                'success',
                'Stock request & stock updated successfully.'
            );

    } catch (\Exception $e) {

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}
}
