<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\StockCut;
use App\Models\StockCutItem;
use App\Models\Branch;
use App\Models\User;
use App\Notifications\SystemNotification;

class StockCutController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Admin Methods
  |--------------------------------------------------------------------------
  */

  public function index()
  {
    $suppliers = Supplier::orderBy('company_name', 'asc')->get();
    $branches  = Branch::orderBy('name', 'asc')->get();
    return view('pages.admin.stock-cut.index', compact('suppliers', 'branches'));
  }

  public function fetchStockCutsIndexData(Request $request)
  {
    $query = StockCut::with(['supplier', 'requestedBy', 'branch'])->orderBy('created_at', 'desc');

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('id', $search)
          ->orWhereHas('supplier', function ($supplier) use ($search) {
            $supplier->where('company_name', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%");
          })
          ->orWhereHas('requestedBy', function ($user) use ($search) {
            $user->where('fullname', 'like', "%{$search}%")
                 ->orWhere('username', 'like', "%{$search}%");
          })
          ->orWhereHas('branch', function ($branch) use ($search) {
            $branch->where('name', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
    }

    if ($request->filled('branch_id')) {
      $query->where('branch_id', $request->branch_id);
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

    $stockCuts = $query->paginate(15)->withQueryString();

    return response()->json([
      'table'      => view('pages.admin.stock-cut.table', compact('stockCuts'))->render(),
      'mobile'     => view('pages.admin.stock-cut.mtable', compact('stockCuts'))->render(),
      'pagination' => (string) $stockCuts->links(),
      'total'      => $stockCuts->total(),
    ]);
  }

  public function createStockCut()
  {
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    $branches  = Branch::select('id', 'name')->orderBy('name', 'asc')->get();
    return view('pages.admin.stock-cut.stock-cut-create', compact('suppliers', 'branches'));
  }

  public function getProductsBySupplier($supplier_id)
  {
    $products = Product::where('supplier_id', $supplier_id)
      ->select('id', 'supplier_id', 'name', 'price', 'purchase_price')
      ->orderBy('name', 'asc')
      ->get();

    return response()->json($products);
  }

  /**
   * Return only products that belong to the given supplier
   * AND have stock quantity > 0 in the given branch.
   */
  public function getProductsBySupplierAndBranch($supplier_id, $branch_id)
  {
    $products = Product::where('supplier_id', $supplier_id)
      ->select('products.id', 'products.supplier_id', 'products.name', 'products.price', 'products.purchase_price')
      ->join('stocks', function ($join) use ($branch_id) {
        $join->on('stocks.product_id', '=', 'products.id')
             ->where('stocks.branch_id', '=', (int) $branch_id)
             ->where('stocks.quantity', '>', 0);
      })
      ->addSelect('stocks.quantity as branch_stock')
      ->orderBy('products.name', 'asc')
      ->get();

    return response()->json($products);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'supplier_id'           => 'required|exists:suppliers,id',
      'branch_id'             => 'required|exists:branches,id',
      'products'              => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty'        => 'required|integer|min:1',
      'net_total'             => 'required|numeric|min:0',
      'note'                  => 'nullable|string|max:500',
    ]);

    try {
      DB::transaction(function () use ($validated) {
        $branchId = (int) $validated['branch_id'];

        $stockCut = StockCut::create([
          'supplier_id'  => $validated['supplier_id'],
          'requested_by' => Auth::id(),
          'branch_id'    => $branchId,
          'net_total'    => $validated['net_total'],
          'note'         => $validated['note'] ?? null,
          'status'       => 'approved',
        ]);

        foreach ($validated['products'] as $item) {
          $product = Product::lockForUpdate()->findOrFail($item['product_id']);
          $price   = (float) ($product->purchase_price ?? $product->price ?? 0);
          $total   = $price * (int) $item['qty'];

          StockCutItem::create([
            'stock_cut_id' => $stockCut->id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'price'        => $price,
            'total'        => $total,
          ]);

          // Reduce branch stock safely
          $stock = Stock::where('product_id', $item['product_id'])
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first();

          if ($stock) {
            $stock->decrement('quantity', $item['qty']);
          } else {
            Stock::create([
              'product_id' => $item['product_id'],
              'branch_id'  => $branchId,
              'quantity'   => -(int) $item['qty'],
            ]);
          }
        }

        /*
        |--------------------------------------------------------------------------
        | Supplier Return Ledger
        |--------------------------------------------------------------------------
        */
        $supplier = Supplier::lockForUpdate()->findOrFail($validated['supplier_id']);

        $dueBeforeReturn = (float) ($supplier->due ?? 0);
        $returnAmount    = (float) $validated['net_total'];
        $dueAfterReturn  = round($dueBeforeReturn - $returnAmount, 2);

        $supplier->update(['due' => $dueAfterReturn]);

        SupplierTransaction::create([
          'supplier_id'            => $supplier->id,
          'stock_in_request_id'    => null,
          'stock_cut_id'           => $stockCut->id,
          'branch_id'              => $branchId,
          'type'                   => 'return',
          'amount'                 => $returnAmount,
          'due_before_transaction' => $dueBeforeReturn,
          'due_after_transaction'  => $dueAfterReturn,
          'note'                   => 'Stock Return (Cut) BRSKR' . $stockCut->id,
        ]);
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock return recorded successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function show($id)
  {
    $stockCut = StockCut::with(['supplier', 'requestedBy', 'branch', 'items.product'])->findOrFail($id);
    return view('pages.admin.stock-cut.show', compact('stockCut'));
  }

  public function edit($id)
  {
    $stockCut = StockCut::with(['supplier', 'branch', 'items.product'])->findOrFail($id);

    // If status is approved, check if subsequent supplier transactions exist
    if ($stockCut->status === 'approved') {
      $transaction = SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
        ->where(function ($q) use ($stockCut) {
          $q->where('note', 'like', '%BRSKR' . $stockCut->id . '%')
            ->orWhere('note', 'like', '%BRSC' . $stockCut->id . '%')
            ->orWhere('note', 'like', '%BRSK' . $stockCut->id . '%');
        })
        ->first();

      if ($transaction) {
        $hasLaterTransaction = SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
          ->where('id', '>', $transaction->id)
          ->exists();

        if ($hasLaterTransaction) {
          return redirect()->route('admin.stock.cut.cuts.index')
            ->with('error', 'Cannot edit this stock return because subsequent transactions already exist for this supplier.');
        }
      }
    }

    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    $branches  = Branch::select('id', 'name')->orderBy('name', 'asc')->get();
    return view('pages.admin.stock-cut.edit', compact('stockCut', 'suppliers', 'branches'));
  }

  public function update(Request $request, $id)
  {
    $stockCut = StockCut::findOrFail($id);

    // If status is approved, check if subsequent supplier transactions exist
    if ($stockCut->status === 'approved') {
      $transaction = SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
        ->where(function ($q) use ($stockCut) {
          $q->where('note', 'like', '%BRSKR' . $stockCut->id . '%')
            ->orWhere('note', 'like', '%BRSC' . $stockCut->id . '%')
            ->orWhere('note', 'like', '%BRSK' . $stockCut->id . '%');
        })
        ->first();

      if ($transaction) {
        $hasLaterTransaction = SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
          ->where('id', '>', $transaction->id)
          ->exists();

        if ($hasLaterTransaction) {
          return back()->with('error', 'Cannot update this stock return because subsequent transactions already exist for this supplier.');
        }
      }
    }

    $validated = $request->validate([
      'supplier_id'           => 'required|exists:suppliers,id',
      'branch_id'             => 'required|exists:branches,id',
      'products'              => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty'        => 'required|integer|min:1',
      'net_total'             => 'required|numeric|min:0',
      'note'                  => 'nullable|string|max:500',
    ]);

    try {
      DB::transaction(function () use ($validated, $stockCut) {
        $oldBranchId = $stockCut->branch_id ?? ($stockCut->requestedBy->branch_id ?? 1);
        $newBranchId = (int) $validated['branch_id'];
        $wasApproved = ($stockCut->status === 'approved');

        if ($wasApproved) {
          // 1. Restore old branch stock
          foreach ($stockCut->items as $oldItem) {
            Stock::where('product_id', $oldItem->product_id)
              ->where('branch_id', $oldBranchId)
              ->increment('quantity', $oldItem->quantity);
          }

          // 2. Reverse previous supplier transaction & due adjustment
          $oldSupplier = Supplier::lockForUpdate()->find($stockCut->supplier_id);
          if ($oldSupplier) {
            $oldSupplier->increment('due', (float) $stockCut->net_total);
          }
          SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
            ->where(function ($q) use ($stockCut) {
              $q->where('note', 'like', '%BRSKR' . $stockCut->id . '%')
                ->orWhere('note', 'like', '%BRSC' . $stockCut->id . '%')
                ->orWhere('note', 'like', '%BRSK' . $stockCut->id . '%');
            })->delete();
        }

        // 3. Update StockCut header
        $stockCut->update([
          'supplier_id' => $validated['supplier_id'],
          'branch_id'   => $newBranchId,
          'net_total'   => $validated['net_total'],
          'note'        => $validated['note'] ?? $stockCut->note,
        ]);

        // 4. Delete old items and insert new items
        $stockCut->items()->delete();

        foreach ($validated['products'] as $item) {
          $product = Product::lockForUpdate()->findOrFail($item['product_id']);
          $price   = (float) ($product->purchase_price ?? $product->price ?? 0);
          $total   = $price * (int) $item['qty'];

          StockCutItem::create([
            'stock_cut_id' => $stockCut->id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'price'        => $price,
            'total'        => $total,
          ]);

          if ($wasApproved) {
            // Reduce branch stock for new selection
            $stock = Stock::where('product_id', $item['product_id'])
              ->where('branch_id', $newBranchId)
              ->lockForUpdate()
              ->first();

            if ($stock) {
              $stock->decrement('quantity', $item['qty']);
            } else {
              Stock::create([
                'product_id' => $item['product_id'],
                'branch_id'  => $newBranchId,
                'quantity'   => -(int) $item['qty'],
              ]);
            }
          }
        }

        if ($wasApproved) {
          // 5. Apply new supplier transaction and reduce due
          $newSupplier     = Supplier::lockForUpdate()->findOrFail($validated['supplier_id']);
          $dueBeforeReturn = (float) ($newSupplier->due ?? 0);
          $returnAmount    = (float) $validated['net_total'];
          $dueAfterReturn  = round($dueBeforeReturn - $returnAmount, 2);

          $newSupplier->update(['due' => $dueAfterReturn]);

          SupplierTransaction::create([
            'supplier_id'            => $newSupplier->id,
            'stock_in_request_id'    => null,
            'stock_cut_id'           => $stockCut->id,
            'branch_id'              => $newBranchId,
            'type'                   => 'return',
            'amount'                 => $returnAmount,
            'due_before_transaction' => $dueBeforeReturn,
            'due_after_transaction'  => $dueAfterReturn,
            'note'                   => 'Stock Return (Cut) BRSKR' . $stockCut->id,
          ]);
        }
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock return updated successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function destroy($id)
  {
    $stockCut = StockCut::findOrFail($id);

    if ($stockCut->status === 'approved') {
      // Check if subsequent supplier transactions exist
      $transaction = SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
        ->where(function ($q) use ($stockCut) {
          $q->where('note', 'like', '%BRSKR' . $stockCut->id . '%')
            ->orWhere('note', 'like', '%BRSC' . $stockCut->id . '%')
            ->orWhere('note', 'like', '%BRSK' . $stockCut->id . '%');
        })
        ->first();

      if ($transaction) {
        $hasLaterTransaction = SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
          ->where('id', '>', $transaction->id)
          ->exists();

        if ($hasLaterTransaction) {
          return back()->with('error', 'Cannot delete this stock return because subsequent transactions already exist for this supplier.');
        }
      }
    }

    try {
      DB::transaction(function () use ($stockCut) {
        $branchId = $stockCut->branch_id ?? ($stockCut->requestedBy->branch_id ?? 1);

        if ($stockCut->status === 'approved') {
          // Restore stock
          foreach ($stockCut->items as $item) {
            Stock::where('product_id', $item->product_id)
              ->where('branch_id', $branchId)
              ->increment('quantity', $item->quantity);
          }

          // Restore supplier due & delete transaction ledger entry
          $supplier = Supplier::lockForUpdate()->find($stockCut->supplier_id);
          if ($supplier) {
            $supplier->increment('due', (float) $stockCut->net_total);
          }
          SupplierTransaction::where('supplier_id', $stockCut->supplier_id)
            ->where(function ($q) use ($stockCut) {
              $q->where('note', 'like', '%BRSKR' . $stockCut->id . '%')
                ->orWhere('note', 'like', '%BRSC' . $stockCut->id . '%')
                ->orWhere('note', 'like', '%BRSK' . $stockCut->id . '%');
            })->delete();
        }

        $stockCut->items()->delete();
        $stockCut->delete();
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock return deleted successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function approve($id)
  {
    try {
      DB::transaction(function () use ($id) {
        $stockCut = StockCut::with(['items.product', 'supplier', 'branch'])->lockForUpdate()->findOrFail($id);

        if ($stockCut->status !== 'pending') {
          throw new \Exception('This stock return request is already ' . $stockCut->status . '.');
        }

        $branchId = (int) ($stockCut->branch_id ?? ($stockCut->requestedBy->branch_id ?? 1));

        foreach ($stockCut->items as $item) {
          $stock = Stock::where('product_id', $item->product_id)
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first();

          if ($stock) {
            $stock->decrement('quantity', $item->quantity);
          } else {
            Stock::create([
              'product_id' => $item->product_id,
              'branch_id'  => $branchId,
              'quantity'   => -(int) $item->quantity,
            ]);
          }
        }

        // Supplier ledger & due update
        $supplier = Supplier::lockForUpdate()->find($stockCut->supplier_id);
        if ($supplier) {
          $dueBeforeReturn = (float) ($supplier->due ?? 0);
          $returnAmount    = (float) $stockCut->net_total;
          $dueAfterReturn  = round($dueBeforeReturn - $returnAmount, 2);

          $supplier->update(['due' => $dueAfterReturn]);

          SupplierTransaction::create([
            'supplier_id'            => $supplier->id,
            'stock_in_request_id'    => null,
            'stock_cut_id'           => $stockCut->id,
            'branch_id'              => $branchId,
            'type'                   => 'return',
            'amount'                 => $returnAmount,
            'due_before_transaction' => $dueBeforeReturn,
            'due_after_transaction'  => $dueAfterReturn,
            'note'                   => 'Stock Return (Cut) BRSKR' . $stockCut->id . ' Approved',
          ]);
        }

        $stockCut->update(['status' => 'approved']);

        if ($stockCut->requestedBy) {
          $stockCut->requestedBy->notify(new SystemNotification([
            'title'   => 'Stock Return Approved',
            'message' => [
              'text' => 'Your stock return request has been approved',
              'from' => 'Admin',
            ],
            'url'     => route('manager.stock.cut.show', $stockCut->id),
            'type'    => 'stock_cut',
          ]));
        }
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock return approved and stock deducted successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function reject($id)
  {
    try {
      $stockCut = StockCut::with('requestedBy')->findOrFail($id);

      if ($stockCut->status !== 'pending') {
        return back()->with('error', 'This stock return request is already ' . $stockCut->status . '.');
      }

      $stockCut->update(['status' => 'rejected']);

      if ($stockCut->requestedBy) {
        $stockCut->requestedBy->notify(new SystemNotification([
          'title'   => 'Stock Return Rejected',
          'message' => [
            'text' => 'Your stock return request has been rejected',
            'from' => 'Admin',
          ],
          'url'     => route('manager.stock.cut.show', $stockCut->id),
          'type'    => 'stock_cut',
        ]));
      }

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock return request rejected.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Manager Methods
  |--------------------------------------------------------------------------
  */

  public function managerIndex()
  {
    $suppliers = Supplier::orderBy('company_name', 'asc')->get();
    return view('pages.manager.stock-cut.index', compact('suppliers'));
  }

  public function fetchManagerStockCutsData(Request $request)
  {
    $branchId = Auth::user()->branch_id;
    $query = StockCut::with(['supplier', 'requestedBy', 'branch'])
      ->where('branch_id', $branchId)
      ->orderBy('created_at', 'desc');

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('id', $search)
          ->orWhereHas('supplier', function ($supplier) use ($search) {
            $supplier->where('company_name', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%");
          })
          ->orWhereHas('requestedBy', function ($user) use ($search) {
            $user->where('fullname', 'like', "%{$search}%")
                 ->orWhere('username', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
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

    $stockCuts = $query->paginate(15)->withQueryString();

    return response()->json([
      'table'      => view('pages.manager.stock-cut.table', compact('stockCuts'))->render(),
      'mobile'     => view('pages.manager.stock-cut.mtable', compact('stockCuts'))->render(),
      'pagination' => (string) $stockCuts->links(),
      'total'      => $stockCuts->total(),
    ]);
  }

  public function managerCreate()
  {
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    return view('pages.manager.stock-cut.create', compact('suppliers'));
  }

  public function managerGetProductsBySupplier($supplier_id)
  {
    $branch_id = Auth::user()->branch_id;

    $products = Product::where('supplier_id', $supplier_id)
      ->select('products.id', 'products.supplier_id', 'products.name', 'products.price', 'products.purchase_price')
      ->join('stocks', function ($join) use ($branch_id) {
        $join->on('stocks.product_id', '=', 'products.id')
             ->where('stocks.branch_id', '=', (int) $branch_id)
             ->where('stocks.quantity', '>', 0);
      })
      ->addSelect('stocks.quantity as branch_stock')
      ->orderBy('products.name', 'asc')
      ->get();

    return response()->json($products);
  }

  public function managerStore(Request $request)
  {
    $branchId = Auth::user()->branch_id;

    if (!$branchId) {
      return back()->with('error', 'You are not assigned to any branch.');
    }

    $validated = $request->validate([
      'supplier_id'           => 'required|exists:suppliers,id',
      'products'              => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty'        => 'required|integer|min:1',
      'net_total'             => 'required|numeric|min:0',
      'note'                  => 'nullable|string|max:500',
    ]);

    try {
      DB::transaction(function () use ($validated, $branchId) {
        $stockCut = StockCut::create([
          'supplier_id'  => $validated['supplier_id'],
          'requested_by' => Auth::id(),
          'branch_id'    => $branchId,
          'net_total'    => $validated['net_total'],
          'note'         => $validated['note'] ?? null,
          'status'       => 'pending',
        ]);

        foreach ($validated['products'] as $item) {
          $product = Product::findOrFail($item['product_id']);
          $price   = (float) ($product->purchase_price ?? $product->price ?? 0);
          $total   = $price * (int) $item['qty'];

          StockCutItem::create([
            'stock_cut_id' => $stockCut->id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'price'        => $price,
            'total'        => $total,
          ]);
        }

        // Notify Admin about new Stock Return Request
        $branchName = Auth::user()->branch->name ?? 'Branch';
        $notificationData = [
          'title'   => 'Stock Return Request',
          'message' => [
            'text' => 'A new stock return request from',
            'from' => $branchName,
          ],
          'url'     => route('admin.stock.cut.cut.show', $stockCut->id),
          'type'    => 'stock_cut',
        ];

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
          $admin->notify(new SystemNotification($notificationData));
        }
      });

      return redirect()->route('manager.stock.cut.index')->with('success', 'Stock return request submitted successfully and is pending admin approval.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function managerShow($id)
  {
    $branchId = Auth::user()->branch_id;
    $stockCut = StockCut::with(['supplier', 'requestedBy', 'branch', 'items.product'])
      ->where('branch_id', $branchId)
      ->findOrFail($id);

    return view('pages.manager.stock-cut.show', compact('stockCut'));
  }

  public function managerEdit($id)
  {
    $branchId = Auth::user()->branch_id;
    $stockCut = StockCut::with(['supplier', 'branch', 'items.product'])
      ->where('branch_id', $branchId)
      ->findOrFail($id);

    if ($stockCut->status !== 'pending') {
      return redirect()->route('manager.stock.cut.index')->with('error', 'Only pending stock return requests can be edited.');
    }

    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    return view('pages.manager.stock-cut.edit', compact('stockCut', 'suppliers'));
  }

  public function managerUpdate(Request $request, $id)
  {
    $branchId = Auth::user()->branch_id;
    $stockCut = StockCut::where('branch_id', $branchId)->findOrFail($id);

    if ($stockCut->status !== 'pending') {
      return back()->with('error', 'Only pending stock return requests can be edited.');
    }

    $validated = $request->validate([
      'supplier_id'           => 'required|exists:suppliers,id',
      'products'              => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty'        => 'required|integer|min:1',
      'net_total'             => 'required|numeric|min:0',
      'note'                  => 'nullable|string|max:500',
    ]);

    try {
      DB::transaction(function () use ($validated, $stockCut) {
        $stockCut->update([
          'supplier_id' => $validated['supplier_id'],
          'net_total'   => $validated['net_total'],
          'note'        => $validated['note'] ?? null,
        ]);

        $stockCut->items()->delete();

        foreach ($validated['products'] as $item) {
          $product = Product::findOrFail($item['product_id']);
          $price   = (float) ($product->purchase_price ?? $product->price ?? 0);
          $total   = $price * (int) $item['qty'];

          StockCutItem::create([
            'stock_cut_id' => $stockCut->id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'price'        => $price,
            'total'        => $total,
          ]);
        }
      });

      return redirect()->route('manager.stock.cut.index')->with('success', 'Stock return request updated successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function managerDestroy($id)
  {
    $branchId = Auth::user()->branch_id;
    $stockCut = StockCut::where('branch_id', $branchId)->findOrFail($id);

    if ($stockCut->status !== 'pending') {
      return back()->with('error', 'Only pending stock return requests can be deleted.');
    }

    try {
      DB::transaction(function () use ($stockCut) {
        $stockCut->items()->delete();
        $stockCut->delete();
      });

      return redirect()->route('manager.stock.cut.index')->with('success', 'Stock return request deleted successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }
}
