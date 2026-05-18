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

use App\Models\StockCut;
use App\Models\StockCutItem;
use App\Models\Branch;
use App\Models\User;

class StockCutController extends Controller
{
  public function index()
  {
    $stockCuts = StockCut::with(['supplier', 'requestedBy'])->orderBy('created_at', 'desc')->get();
    return view('pages.admin.stock-cut.index', compact('stockCuts'));
  }

  public function createStockCut()
  {
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    return view('pages.admin.stock-cut.stock-cut-create', compact('suppliers'));
  }

  public function getProductsBySupplier($supplier_id)
  {
    $products = Product::where('supplier_id', $supplier_id)
      ->select('id', 'supplier_id', 'name', 'purchase_price')
      ->orderBy('name', 'asc')
      ->get();

    return response()->json($products);
  }

  public function store(Request $request)
  {
    $request->validate([
      'supplier_id'           => 'required|exists:suppliers,id',
      'products'              => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty'        => 'required|integer|min:1',
      'net_total'             => 'required|numeric|min:0',
    ]);

    try {
      DB::transaction(function () use ($request) {
        $stockCut = StockCut::create([
          'supplier_id'  => $request->supplier_id,
          'requested_by' => Auth::id(),
          'net_total'    => $request->net_total,
        ]);

        $branchId = Auth::user()->branch_id ?? 1; // Default to Head Office if no branch

        foreach ($request->products as $item) {
          $product = Product::findOrFail($item['product_id']);

          StockCutItem::create([
            'stock_cut_id' => $stockCut->id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'price'        => $product->purchase_price,
          ]);

          // Reduce stock
          $stock = Stock::where('product_id', $item['product_id'])
            ->where('branch_id', $branchId)
            ->first();

          if ($stock) {
            $stock->decrement('quantity', $item['qty']);
          } else {
            Stock::create([
              'product_id' => $item['product_id'],
              'branch_id'  => $branchId,
              'quantity'   => -$item['qty']
            ]);
          }
        }
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock cut recorded successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function show($id)
  {
    $stockCut = StockCut::with(['supplier', 'requestedBy', 'items.product'])->findOrFail($id);
    return view('pages.admin.stock-cut.show', compact('stockCut'));
  }

  public function edit($id)
  {
    $stockCut = StockCut::with('items.product')->findOrFail($id);
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    return view('pages.admin.stock-cut.edit', compact('stockCut', 'suppliers'));
  }

  public function update(Request $request, $id)
  {
    $request->validate([
      'supplier_id'           => 'required|exists:suppliers,id',
      'products'              => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty'        => 'required|integer|min:1',
      'net_total'             => 'required|numeric|min:0',
    ]);

    try {
      DB::transaction(function () use ($request, $id) {
        $stockCut = StockCut::findOrFail($id);
        $branchId = $stockCut->requestedBy->branch_id ?? 1;

        // Restore old stock
        foreach ($stockCut->items as $oldItem) {
          Stock::where('product_id', $oldItem->product_id)
            ->where('branch_id', $branchId)
            ->increment('quantity', $oldItem->quantity);
        }

        $stockCut->update([
          'supplier_id' => $request->supplier_id,
          'net_total'   => $request->net_total,
        ]);

        $stockCut->items()->delete();

        foreach ($request->products as $item) {
          $product = Product::findOrFail($item['product_id']);

          StockCutItem::create([
            'stock_cut_id' => $stockCut->id,
            'product_id'   => $item['product_id'],
            'quantity'     => $item['qty'],
            'price'        => $product->purchase_price,
          ]);

          // Reduce new stock
          $stock = Stock::where('product_id', $item['product_id'])
            ->where('branch_id', $branchId)
            ->first();

          if ($stock) {
            $stock->decrement('quantity', $item['qty']);
          } else {
            Stock::create([
              'product_id' => $item['product_id'],
              'branch_id'  => $branchId,
              'quantity'   => -$item['qty']
            ]);
          }
        }
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock cut updated successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function destroy($id)
  {
    try {
      DB::transaction(function () use ($id) {
        $stockCut = StockCut::findOrFail($id);
        $branchId = $stockCut->requestedBy->branch_id ?? 1;

        // Restore stock
        foreach ($stockCut->items as $item) {
          Stock::where('product_id', $item->product_id)
            ->where('branch_id', $branchId)
            ->increment('quantity', $item->quantity);
        }

        $stockCut->items()->delete();
        $stockCut->delete();
      });

      return redirect()->route('admin.stock.cut.cuts.index')->with('success', 'Stock cut deleted successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }
}

