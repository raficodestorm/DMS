<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockInItem;
use App\Models\StockInRequest;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Services\PurchasePriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
  public function createStockRequest()
  {
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();

    // Pass the active deduction policy so the blade can mirror
    // PurchasePriceCalculator logic client-side (live rate preview).
    $deduction = Deduction::where('type', 'main')->first();

    return view('pages.manager.stock.stock-in-create', compact('suppliers', 'deduction'));
  }

  public function getProductsBySupplier($supplier_id)
  {
    $products = Product::where('supplier_id', $supplier_id)
      ->select('id', 'supplier_id', 'name', 'price', 'purchase_price')
      ->orderBy('name', 'asc')
      ->get();

    return response()->json($products);
  }


  public function store(Request $request)
{
    $validated = $request->validate([
        'supplier_id'                   => 'required|exists:suppliers,id',
        'products'                      => 'required|array|min:1',
        'products.*.product_id'         => ['required','exists:products,id','distinct',],
        'products.*.qty'                => 'required|integer|min:1',
        'products.*.tree_deduction'     => 'nullable|numeric|min:0',
  
    ]);

    try {

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Get active deduction policy
            |--------------------------------------------------------------------------
            */
            $deduction = Deduction::where('type', 'main')->first();

            if (!$deduction) {
                throw new \Exception(
                    'No main deduction policy found. Please configure deductions first.'
                );
            }

            $calculator = new PurchasePriceCalculator();

            /*
            |--------------------------------------------------------------------------
            | STEP 2: Create Stock-In Request
            |--------------------------------------------------------------------------
            */
            $stockRequest = StockInRequest::create([
                'supplier_id' => $validated['supplier_id'],
                'requested_by' => Auth::id(),
                'branch_id'    => Auth::user()->branch_id,
                'net_total'    => 0,
                'status'       => 'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | STEP 3: Create Items
            |
            | cost_price is calculated NOW and stored permanently with the request.
            | It will NOT be recalculated during approval.
            |--------------------------------------------------------------------------
            */
            $netTotal = 0;

            foreach ($validated['products'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                $treeDeduction = $item['tree_deduction'] ?? 0;

                /*
                 * First create the item with the base purchase price.
                 * The calculator needs the StockInItem model.
                 */
                $stockInItem = StockInItem::create([
                    'stock_in_request_id' => $stockRequest->id,
                    'product_id'          => $product->id,
                    'quantity'            => $item['qty'],
                    'cost_price'          => $product->purchase_price,
                    'tree_deduction'      => $treeDeduction,
                ]);

                /*
                 * Calculate final cost price using the same logic
                 * that was previously executed during approval.
                 */
                $costPrice = $calculator->calculate(
                    $stockInItem,
                    $deduction
                );
                $total = $costPrice * $stockInItem->quantity;

                /*
                 * Store the calculated price permanently.
                 */
                $stockInItem->update([
                    'cost_price' => $costPrice,
                    'total'      => $total,
                ]);
                $netTotal += $total;

                
            }
            $stockRequest->update([
                    'net_total'   => $netTotal,
                ]);

            /*
            |--------------------------------------------------------------------------
            | STEP 4: Notify Admin
            |--------------------------------------------------------------------------
            */
            $notificationData = [
                'title'   => 'Stock-In Request',
                'message' => [
                    'text' => 'A new stock-in request from',
                    'from' => Auth::user()->branch->name,
                ],
                'url'     => route(
                    'admin.stock.in.request.show',
                    $stockRequest->id
                ),
                'type'    => 'stock_request',
            ];

            $admins = \App\Models\User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(
                    new \App\Notifications\SystemNotification(
                        $notificationData
                    )
                );
            }
        });

        return redirect()
            ->route('manager.stock.in.requests.index')
            ->with(
                'success',
                'Stock request sent successfully!'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'Unable to create stock request. Please try again.'
            );
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
    $request = StockInRequest::with(['supplier', 'items.product'])
    ->where('branch_id', Auth::user()->branch_id)
    ->findOrFail($id);
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
    $request = StockInRequest::where(
    'branch_id',
    Auth::user()->branch_id
)->findOrFail($id);
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    if ($request->status == 'approved') {
      return back()->with('error', 'You cannot edit approved request.');
    }

    // Pass deduction policy so the blade JS can mirror PurchasePriceCalculator live.
    $deduction = Deduction::where('type', 'main')->first();

    return view('pages.manager.stock.stock-in-edit', compact('request', 'suppliers', 'deduction'));
  }


  public function stockInUpdate(Request $request, $id)
{
    $validated = $request->validate([
        'supplier_id'               => 'required|exists:suppliers,id',
        'products'                  => 'required|array|min:1',
        'products.*.product_id'     => ['required','exists:products,id','distinct',],
        'products.*.qty'            => 'required|integer|min:1',
        'products.*.tree_deduction' => 'nullable|numeric|min:0',
    ]);

    try {

        DB::transaction(function () use ($validated, $id) {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Lock request
            |--------------------------------------------------------------------------
            */
            $stockRequest = StockInRequest::where(
                'branch_id',
                Auth::user()->branch_id
            )
            ->lockForUpdate()
            ->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | STEP 2: Only pending request can be edited
            |--------------------------------------------------------------------------
            */
            if ($stockRequest->status !== 'pending') {
                throw new \Exception(
                    'Only pending requests can be updated.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 3: Get deduction policy
            |--------------------------------------------------------------------------
            */
            $deduction = Deduction::where('type', 'main')->first();

            if (!$deduction) {
                throw new \Exception(
                    'No main deduction policy found. Please configure deductions first.'
                );
            }

            $calculator = new PurchasePriceCalculator();

            /*
            |--------------------------------------------------------------------------
            | STEP 4: Update request header
            |--------------------------------------------------------------------------
            */
            $stockRequest->update([
                'supplier_id' => $validated['supplier_id'],
                
            ]);

            /*
            |--------------------------------------------------------------------------
            | STEP 5: Remove old items
            |--------------------------------------------------------------------------
            */
            $stockRequest->items()->delete();

            /*
            |--------------------------------------------------------------------------
            | STEP 6: Create new items with calculated cost_price
            |--------------------------------------------------------------------------
            */
            $netTotal = 0;
            foreach ($validated['products'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                $treeDeduction = $item['tree_deduction'] ?? 0;

                $stockInItem = StockInItem::create([
                    'stock_in_request_id' => $stockRequest->id,
                    'product_id'          => $product->id,
                    'quantity'            => $item['qty'],
                    'cost_price'          => $product->purchase_price,
                    'tree_deduction'      => $treeDeduction,
                ]);

                /*
                 * Calculate cost price at update time.
                 */
                $costPrice = $calculator->calculate(
                    $stockInItem,
                    $deduction
                );

                $total = $costPrice * $stockInItem->quantity;

                /*
                 * Store the calculated price permanently.
                 */
                $stockInItem->update([
                    'cost_price' => $costPrice,
                    'total'      => $total,
                ]);
                $netTotal += $total;

                
            }
            $stockRequest->update([
                    'net_total'   => $netTotal,
                ]);
        });

        return redirect()
            ->route('manager.stock.in.requests.index')
            ->with(
                'success',
                'Stock request updated successfully!'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );
    }
}




  public function approve($id)
{
    try {

        DB::transaction(function () use ($id) {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Lock and load request
            |--------------------------------------------------------------------------
            */
            $stockInRequest = StockInRequest::with([
                'items.product',
                'requestedBy',
            ])
            ->lockForUpdate()
            ->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | STEP 2: Validate status
            |--------------------------------------------------------------------------
            */
            if ($stockInRequest->status !== 'pending') {
                throw new \Exception(
                    'This request has already been ' .
                    $stockInRequest->status . '.'
                );
            }

            $targetBranchId = $stockInRequest->branch_id;

            if (!$targetBranchId) {
                throw new \Exception(
                    'The requesting user is not assigned to any branch.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 3: Apply stock + purchase price
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | cost_price was already calculated and stored when the request
            | was created.
            |
            | Approval does NOT calculate the price again.
            |--------------------------------------------------------------------------
            */
            foreach ($stockInRequest->items as $item) {

                if (!$item->product) {
                    throw new \Exception(
                        "Product not found for stock item #{$item->id}."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 3A. Update branch stock
                |--------------------------------------------------------------------------
                */
                $stock = Stock::firstOrCreate(
                    [
                        'product_id' => $item->product_id,
                        'branch_id'  => $targetBranchId,
                    ],
                    [
                        'quantity' => 0,
                    ]
                );

                $stock->increment(
                    'quantity',
                    $item->quantity
                );

                /*
                |--------------------------------------------------------------------------
                | 3B. Update product purchase price
                |--------------------------------------------------------------------------
                |
                | NO CALCULATION HERE.
                |
                | stock_in_items.cost_price is the source of truth.
                |--------------------------------------------------------------------------
                */
                $item->product->update([
                    'purchase_price' => $item->cost_price,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 4: Update Supplier Due & Create Supplier Transaction Ledger
            |--------------------------------------------------------------------------
            */
            if ($stockInRequest->supplier_id && $stockInRequest->net_total > 0) {
                $supplier = Supplier::lockForUpdate()->find($stockInRequest->supplier_id);
                if ($supplier) {
                    $dueBeforeTransaction = (float) ($supplier->due ?? 0);
                    $amount               = (float) ($stockInRequest->net_total ?? 0);
                    $dueAfterTransaction  = round($dueBeforeTransaction + $amount, 2);

                    $supplier->update([
                        'due' => $dueAfterTransaction,
                    ]);

                    SupplierTransaction::create([
                        'supplier_id'            => $supplier->id,
                        'stock_in_request_id'    => $stockInRequest->id,
                        'branch_id'              => $targetBranchId,
                        'type'                   => 'buy',
                        'amount'                 => $amount,
                        'due_before_transaction' => $dueBeforeTransaction,
                        'due_after_transaction'  => $dueAfterTransaction,
                        'note'                   => 'Stock-in Request BRSK' . $stockInRequest->id . ' Approved',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 5: Mark request approved
            |--------------------------------------------------------------------------
            */
            $stockInRequest->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
            ]);
        });

        return redirect()
            ->back()
            ->with(
                'success',
                'Approved! Branch stock, product purchase prices, and supplier due updated successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()->with(
            'error',
            $e->getMessage()
        );
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

    // Pass deduction policy so the blade JS can mirror PurchasePriceCalculator live.
    $deduction = Deduction::where('type', 'main')->first();

    return view('pages.admin.stock.stock-in-edit', compact('request', 'suppliers', 'deduction'));
  }




 public function stockInAdminUpdate(Request $request, $id)
{
    $validated = $request->validate([
        'supplier_id'               => 'required|exists:suppliers,id',
        'products'                  => 'required|array|min:1',
        'products.*.product_id'     => ['required','exists:products,id','distinct',],
        'products.*.qty'            => 'required|integer|min:1',
        'products.*.tree_deduction' => 'nullable|numeric|min:0',
    
    ]);

    try {

        DB::transaction(function () use ($validated, $id) {

            /*
            |--------------------------------------------------------------------------
            | STEP 1
            | Lock the stock request
            |--------------------------------------------------------------------------
            */
            $stockRequest = StockInRequest::with([
                'items.product',
                'requestedBy',
            ])
                ->lockForUpdate()
                ->findOrFail($id);


            /*
            |--------------------------------------------------------------------------
            | STEP 2
            | Get branch
            |--------------------------------------------------------------------------
            */
            $branchId = $stockRequest->branch_id;

            if (!$branchId) {
                throw new \Exception('Branch not found.');
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 3
            | Get deduction policy
            |--------------------------------------------------------------------------
            |
            | Cost price will be calculated NOW and stored in
            | stock_in_items.cost_price.
            |
            */
            $deduction = Deduction::where('type', 'main')->first();

            if (!$deduction) {
                throw new \Exception(
                    'No main deduction policy found. Please configure deductions first.'
                );
            }

            $calculator = new PurchasePriceCalculator();


            /*
            |--------------------------------------------------------------------------
            | STEP 4
            | Rollback old approved stock effect
            |--------------------------------------------------------------------------
            |
            | If this request was already approved, its old quantity has already
            | been added to branch stock. Remove that quantity before applying
            | the new items.
            |
            */
            if ($stockRequest->status === 'approved') {

                if ($stockRequest->supplier_id && $stockRequest->net_total > 0) {
                    $oldSupplier = Supplier::lockForUpdate()->find($stockRequest->supplier_id);
                    if ($oldSupplier) {
                        $dueBeforeReversal = (float) ($oldSupplier->due ?? 0);
                        $reversalAmount    = (float) ($stockRequest->net_total ?? 0);
                        $dueAfterReversal  = round($dueBeforeReversal - $reversalAmount, 2);

                        $oldSupplier->update([
                            'due' => $dueAfterReversal,
                        ]);

                        // Create a reversal ledger entry to keep the supplier ledger accurate
                        SupplierTransaction::create([
                            'supplier_id'            => $oldSupplier->id,
                            'stock_in_request_id'    => $stockRequest->id,
                            'branch_id'              => $branchId,
                            'type'                   => 'adjustment',
                            'amount'                 => $reversalAmount,
                            'due_before_transaction' => $dueBeforeReversal,
                            'due_after_transaction'  => $dueAfterReversal,
                            'note'                   => 'Reversal: Stock-in Request BRSK' . $stockRequest->id . ' being edited',
                        ]);
                    }
                }

                foreach ($stockRequest->items as $oldItem) {

                    $stock = Stock::where([
                        'product_id' => $oldItem->product_id,
                        'branch_id'  => $branchId,
                    ])
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        throw new \Exception(
                            "Stock record not found for {$oldItem->product->name}."
                        );
                    }

                    $newQty = $stock->quantity - $oldItem->quantity;

                    if ($newQty < 0) {
                        throw new \Exception(
                            "Stock inconsistency detected for {$oldItem->product->name}."
                        );
                    }

                    $stock->update([
                        'quantity' => $newQty,
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 5
            | Update request header
            |--------------------------------------------------------------------------
            */
            $stockRequest->update([
                'supplier_id' => $validated['supplier_id'],
                
            ]);


            /*
            |--------------------------------------------------------------------------
            | STEP 6
            | Delete old request items
            |--------------------------------------------------------------------------
            */
            $stockRequest->items()->delete();


            /*
            |--------------------------------------------------------------------------
            | STEP 7
            | Create new items
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | The cost_price is calculated here.
            | It is NOT calculated during approval.
            |
            */
            $netTotal = 0;

            foreach ($validated['products'] as $item) {

                $product = Product::lockForUpdate()->findOrFail(
                    $item['product_id']
                );

                $treeDeduction = $item['tree_deduction'] ?? 0;


                /*
                |--------------------------------------------------------------------------
                | Create temporary item using current purchase price
                |--------------------------------------------------------------------------
                |
                | PurchasePriceCalculator needs the StockInItem model.
                |
                */
                $stockInItem = StockInItem::create([
                    'stock_in_request_id' => $stockRequest->id,
                    'product_id'          => $product->id,
                    'quantity'            => $item['qty'],
                    'cost_price'          => $product->purchase_price,
                    'tree_deduction'      => $treeDeduction,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Calculate final cost price
                |--------------------------------------------------------------------------
                */
                $costPrice = $calculator->calculate(
                    $stockInItem,
                    $deduction
                );


                $total = $costPrice * $stockInItem->quantity;

                /*
                 * Store the calculated price permanently.
                 */
                $stockInItem->update([
                    'cost_price' => $costPrice,
                    'total'      => $total,
                ]);
                $netTotal += $total;

                
            }
            $stockRequest->update([
                    'net_total'   => $netTotal,
                ]);


            /*
            |--------------------------------------------------------------------------
            | STEP 8
            | Re-load newly created items
            |--------------------------------------------------------------------------
            */
            $stockRequest->load('items.product');


            /*
            |--------------------------------------------------------------------------
            | STEP 9
            | Re-apply approved stock effect
            |--------------------------------------------------------------------------
            |
            | If the request was already approved, the new items must immediately
            | affect stock.
            |
            | NO CALCULATOR HERE.
            |
            */
            if ($stockRequest->status === 'approved') {

                if ($stockRequest->supplier_id && $stockRequest->net_total > 0) {
                    $newSupplier = Supplier::lockForUpdate()->find($stockRequest->supplier_id);
                    if ($newSupplier) {
                        $dueBeforeReapply = (float) ($newSupplier->due ?? 0);
                        $reapplyAmount    = (float) ($stockRequest->net_total ?? 0);
                        $dueAfterReapply  = round($dueBeforeReapply + $reapplyAmount, 2);

                        $newSupplier->update([
                            'due' => $dueAfterReapply,
                        ]);

                        // Create a fresh buy ledger entry for the updated amounts
                        SupplierTransaction::create([
                            'supplier_id'            => $newSupplier->id,
                            'stock_in_request_id'    => $stockRequest->id,
                            'branch_id'              => $branchId,
                            'type'                   => 'buy',
                            'amount'                 => $reapplyAmount,
                            'due_before_transaction' => $dueBeforeReapply,
                            'due_after_transaction'  => $dueAfterReapply,
                            'note'                   => 'Stock-in Request BRSK' . $stockRequest->id . ' Edited (Re-applied)',
                        ]);
                    }
                }

                foreach ($stockRequest->items as $item) {

                    if (!$item->product) {
                        throw new \Exception(
                            "Product not found for stock item #{$item->id}."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 9A. Update branch stock
                    |--------------------------------------------------------------------------
                    */
                    $stock = Stock::firstOrCreate(
                        [
                            'product_id' => $item->product_id,
                            'branch_id'  => $branchId,
                        ],
                        [
                            'quantity' => 0,
                        ]
                    );

                    $stock->increment(
                        'quantity',
                        $item->quantity
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | 9B. Update product purchase price
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    |
                    | Use the already calculated cost_price.
                    | Do NOT calculate again.
                    |
                    */
                    $item->product->update([
                        'purchase_price' => $item->cost_price,
                    ]);
                }
            }
        });


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->back()
            ->with(
                'success',
                'Stock request & stock updated successfully.'
            );


    } catch (\Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | LOG ERROR
        |--------------------------------------------------------------------------
        */
        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );
    }
}
}
