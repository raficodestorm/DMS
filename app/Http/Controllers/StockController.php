<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{

  public function managerIndex()
  {

    $stocks = Stock::with(['product.supplier'])
      ->where('branch_id', auth()->user()->branch_id)
      ->get();

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
  public function specificStock($branch_id = null)
  {
    $query = \App\Models\Stock::with(['product.supplier', 'branch']);

    if ($branch_id) {
      // নির্দিষ্ট ব্রাঞ্চের স্টক
      $stocks = $query->where('branch_id', $branch_id)->get();
      $title = \App\Models\Branch::find($branch_id)->name . " Stock";
    } else {
      // পুরো কোম্পানির স্টক (প্রোডাক্ট অনুযায়ী গ্রুপ করে সামারি)
      $stocks = \App\Models\Stock::with(['product.supplier'])
        ->select('product_id', \DB::raw('SUM(quantity) as quantity'))
        ->groupBy('product_id')
        ->get();
      $title = "Company Total Stock";
    }

    return view('pages.admin.stock.specific-stock', compact('stocks', 'title', 'branch_id'));
  }
}
