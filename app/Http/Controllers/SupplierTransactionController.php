<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SupplierTransactionController extends Controller
{

  public function indexForManager(Request $request)
  {
    $suppliers = Supplier::orderBy('company_name', 'asc')->get();
    return view('pages.manager.supplier-transaction.index', compact('suppliers'));
  }

  /**
   * Fetch Supplier transactions data for Manager via AJAX with filters.
   */
  public function fetchManagerSupplierTransactionsData(Request $request)
  {
    $manager = auth()->user();

    $query = SupplierTransaction::with(['supplier', 'branch', 'stock_in_request', 'stockCut'])
      ->where('branch_id', $manager->branch_id)
      ->latest();

    if ($request->filled('search')) {
      $search = trim($request->search);

      $query->where(function ($q) use ($search) {
        if (preg_match('/^BRST00(\d+)$/i', $search, $match) || preg_match('/^SBT(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }

        $q->where('id', 'like', "%{$search}%")
          ->orWhereHas('supplier', function ($supplier) use ($search) {
            $supplier->where('company_name', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
    }

    if ($request->filled('type')) {
      $query->where('type', $request->type);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    $totalCount   = $query->count();
    $transactions = $query->paginate(15)->withQueryString();

    return response()->json([
      'table'       => view('pages.manager.supplier-transaction.table', compact('transactions'))->render(),
      'mobile'      => view('pages.manager.supplier-transaction.mtable', compact('transactions'))->render(),
      'pagination'  => (string) $transactions->links(),
      'total'       => $totalCount,
    ]);
  }

  public function showForManager($id)
  {
    $manager = auth()->user();
    $transaction = SupplierTransaction::with(['supplier', 'branch', 'stock_in_request', 'stockCut'])
      ->where('branch_id', $manager->branch_id)
      ->findOrFail($id);
    return view('pages.manager.supplier-transaction.show', compact('transaction'));
  }



 




  public function indexForAdmin(Request $request)
  {
    $branches  = \App\Models\Branch::orderBy('name', 'asc')->get();
    $suppliers = \App\Models\Supplier::orderBy('company_name', 'asc')->get();
    return view('pages.admin.supplier-transaction.index', compact('branches', 'suppliers'));
  }

  public function fetchSupplierTransactionsIndexData(Request $request)
  {
    $query = SupplierTransaction::with(['supplier', 'branch', 'stock_in_request', 'stockCut'])->latest();

    if ($request->filled('search')) {
      $search = trim($request->search);

      $query->where(function ($q) use ($search) {
        if (preg_match('/^SBT(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }

        $q->where('id', 'like', "%{$search}%")
          ->orWhereHas('supplier', function ($supplier) use ($search) {
            $supplier->where('company_name', 'like', "%{$search}%")
                     ->orWhere('contact_person', 'like', "%{$search}%");
          })
          ->orWhereHas('branch', function ($branch) use ($search) {
            $branch->where('name', 'like', "%{$search}%");
          });
      });
    }

    if ($request->filled('branch_id')) {
      $query->where('branch_id', $request->branch_id);
    }

    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
    }

    if ($request->filled('type')) {
      $query->where('type', $request->type);
    }

    if ($request->filled('payment_method')) {
      $query->where('payment_method', $request->payment_method);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->to_date);
    }

    $totalCount  = $query->count();
    $transactions = $query->paginate(15)->withQueryString();

    return response()->json([
      'table'       => view('pages.admin.supplier-transaction.table', compact('transactions'))->render(),
      'mobile'      => view('pages.admin.supplier-transaction.mtable', compact('transactions'))->render(),
      'pagination'  => (string) $transactions->links(),
      'total'       => $totalCount,
    ]);
  }

  public function showForAdmin($id)
  {
    $transaction = SupplierTransaction::with(['supplier', 'branch', 'stock_in_request', 'stockCut'])->findOrFail($id);
    return view('pages.admin.supplier-transaction.show', compact('transaction'));
  }

}
