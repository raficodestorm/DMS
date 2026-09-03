<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{

  public function managerIndex(Request $request)
  {
    $query = Stock::with(['product.supplier'])
      ->select('stocks.*')
      ->where('stocks.branch_id', auth()->user()->branch_id);

    $hasJoinedProducts = false;

    // Search by product name OR supplier company name (using JOIN for fast DB execution)
    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->join('products', 'stocks.product_id', '=', 'products.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('suppliers.company_name', 'like', "%{$search}%");
            });
      $hasJoinedProducts = true;
    }

    // Filter by status (available or low_stock)
    if ($request->filled('status')) {
      if (!$hasJoinedProducts) {
        $query->join('products', 'stocks.product_id', '=', 'products.id');
        $hasJoinedProducts = true;
      }
      $status = $request->status;
      if ($status === 'low_stock') {
        $query->whereColumn('stocks.quantity', '<=', 'products.stock_alert');
      } elseif ($status === 'available') {
        $query->whereColumn('stocks.quantity', '>', 'products.stock_alert');
      }
    }

    $stocks = $query->get();

    return view('pages.manager.stock.index', compact('stocks'));
  }


  // সব ব্রাঞ্চের বক্স ভিউ
  public function allStocksSummary()
  {
    // প্রতিটি ব্রাঞ্চের টোটাল স্টক ভ্যালু ক্যালকুলেট করা
    $branches = \App\Models\Branch::with(['stocks.product'])->get()->map(function ($branch) {
      $branch->total_value = $branch->stocks->sum(function ($stock) {
        return $stock->quantity * $stock->product->price;
      });
      return $branch;
    });

    // পুরো কোম্পানির টোটাল স্টক ভ্যালু
    $company_total_value = \App\Models\Stock::with('product')->get()->sum(function ($stock) {
      return $stock->quantity * $stock->product->price;
    });

    return view('pages.admin.stock.all-stocks', compact('branches', 'company_total_value'));
  }

  // স্পেসিফিক ব্রাঞ্চের ডিটেইল
  public function specificStock(Request $request, $branch_id = null)
  {
    $branches = Branch::orderBy('name', 'asc')->get();
    $hasJoined = false;

    if ($branch_id) {
      // নির্দিষ্ট ব্রাঞ্চের স্টক
      $query = Stock::with(['product.supplier', 'branch'])
        ->select('stocks.*')
        ->where('stocks.branch_id', $branch_id);

      if ($request->filled('search')) {
        $search = trim($request->search);
        $query->join('products', 'stocks.product_id', '=', 'products.id')
              ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
              ->where(function ($q) use ($search) {
                  $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('suppliers.company_name', 'like', "%{$search}%");
              });
        $hasJoined = true;
      }

      if ($request->filled('status')) {
        if (!$hasJoined) {
          $query->join('products', 'stocks.product_id', '=', 'products.id');
          $hasJoined = true;
        }
        if ($request->status === 'low_stock') {
          $query->whereColumn('stocks.quantity', '<=', 'products.stock_alert');
        } elseif ($request->status === 'available') {
          $query->whereColumn('stocks.quantity', '>', 'products.stock_alert');
        }
      }

      $stocks = $query->get();
      $title = Branch::find($branch_id)->name . " Stock";
    } else {
      // পুরো কোম্পানির স্টক — branch_id filter support করে
      $filterBranchId = $request->filled('branch_id') ? $request->branch_id : null;

      if ($filterBranchId) {
        // নির্দিষ্ট ব্রাঞ্চ ফিল্টার করা হলে সরাসরি সেই ব্রাঞ্চের স্টক দেখাও
        $query = Stock::with(['product.supplier'])
          ->select('stocks.*')
          ->where('stocks.branch_id', $filterBranchId);
      } else {
        $query = Stock::with(['product.supplier'])
          ->select('product_id', DB::raw('SUM(quantity) as quantity'))
          ->groupBy('product_id');
      }

      if ($request->filled('search')) {
        $search = trim($request->search);
        $query->join('products', 'stocks.product_id', '=', 'products.id')
              ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
              ->where(function ($q) use ($search) {
                  $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('suppliers.company_name', 'like', "%{$search}%");
              });
        $hasJoined = true;
      }

      if ($request->filled('status')) {
        if (!$hasJoined) {
          $query->join('products', 'stocks.product_id', '=', 'products.id');
        }
        if ($request->status === 'low_stock') {
          $query->whereColumn('stocks.quantity', '<=', 'products.stock_alert');
        } elseif ($request->status === 'available') {
          $query->whereColumn('stocks.quantity', '>', 'products.stock_alert');
        }
      }

      $stocks = $query->get();
      $title = $filterBranchId
        ? (Branch::find($filterBranchId)->name ?? 'Branch') . ' Stock'
        : 'Company Total Stock';
    }

    return view('pages.admin.stock.specific-stock', compact('stocks', 'title', 'branch_id', 'branches'));
  }
}
