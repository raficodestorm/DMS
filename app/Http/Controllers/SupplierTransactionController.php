<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
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
        if (preg_match('/^BRST00(\d+)$/i', $search, $match)) {
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

    // Get the latest transaction id for each supplier
    $latestTxIds = SupplierTransaction::groupBy('supplier_id')
      ->selectRaw('MAX(id) as max_id')
      ->pluck('max_id')
      ->toArray();

    return response()->json([
      'table'       => view('pages.admin.supplier-transaction.table', compact('transactions', 'latestTxIds'))->render(),
      'mobile'      => view('pages.admin.supplier-transaction.mtable', compact('transactions', 'latestTxIds'))->render(),
      'pagination'  => (string) $transactions->links(),
      'total'       => $totalCount,
    ]);
  }

  public function showForAdmin($id)
  {
    $transaction = SupplierTransaction::with(['supplier', 'branch', 'stock_in_request', 'stockCut'])->findOrFail($id);

    $hasLaterTransaction = SupplierTransaction::where('supplier_id', $transaction->supplier_id)
      ->where('id', '>', $transaction->id)
      ->exists();

    return view('pages.admin.supplier-transaction.show', compact('transaction', 'hasLaterTransaction'));
  }

  public function createForAdmin()
  {
    $suppliers = Supplier::orderBy('company_name', 'asc')->get();
    $branches  = Branch::orderBy('name', 'asc')->get();
    return view('pages.admin.supplier-transaction.create', compact('suppliers', 'branches'));
  }

  public function storeForAdmin(Request $request)
  {
    $validated = $request->validate([
      'supplier_id'    => ['required', 'exists:suppliers,id'],
      'branch_id'      => ['nullable', 'exists:branches,id'],
      'amount'         => ['required', 'numeric', 'min:0.01'],
      'payment_method' => ['required', 'string', 'max:50'],
      'note'           => ['nullable', 'string', 'max:500'],
    ]);

    try {
      DB::transaction(function () use ($validated) {
        $supplier = Supplier::lockForUpdate()->findOrFail($validated['supplier_id']);

        $paymentAmount = round((float) $validated['amount'], 2);
        $dueBeforeTransaction = round((float) ($supplier->due ?? 0), 2);
        $dueAfterTransaction  = round($dueBeforeTransaction - $paymentAmount, 2);

        // Update supplier due
        $supplier->update([
          'due' => $dueAfterTransaction,
        ]);

        // Create Supplier Transaction record
        SupplierTransaction::create([
          'supplier_id'            => $supplier->id,
          'stock_in_request_id'    => null,
          'stock_cut_id'           => null,
          'branch_id'              => $validated['branch_id'] ?? null,
          'type'                   => 'pay',
          'amount'                 => $paymentAmount,
          'due_before_transaction' => $dueBeforeTransaction,
          'due_after_transaction'  => $dueAfterTransaction,
          'payment_method'         => $validated['payment_method'],
          'note'                   => $validated['note'] ?? 'Supplier Payment',
        ]);
      });

      return redirect()
        ->route('admin.supplier-transactions.index')
        ->with('success', 'Supplier payment of ৳' . number_format($validated['amount'], 2) . ' recorded successfully.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', 'Error recording supplier payment: ' . $e->getMessage());
    }
  }

  public function editForAdmin($id)
  {
    $transaction = SupplierTransaction::with(['supplier', 'branch'])->findOrFail($id);

    // Check if subsequent transactions exist
    $hasLaterTransaction = SupplierTransaction::where('supplier_id', $transaction->supplier_id)
      ->where('id', '>', $transaction->id)
      ->exists();

    if ($hasLaterTransaction) {
      return redirect()->route('admin.supplier-transactions.index')
        ->with('error', 'Cannot edit this transaction because subsequent transactions already exist for this supplier.');
    }

    if ($transaction->type !== 'pay') {
      return redirect()->route('admin.supplier-transactions.index')
        ->with('error', 'Only direct supplier payment transactions can be edited here.');
    }

    $branches = Branch::orderBy('name', 'asc')->get();
    return view('pages.admin.supplier-transaction.edit', compact('transaction', 'branches'));
  }

  public function updateForAdmin(Request $request, $id)
  {
    $transaction = SupplierTransaction::findOrFail($id);

    // Check if subsequent transactions exist
    $hasLaterTransaction = SupplierTransaction::where('supplier_id', $transaction->supplier_id)
      ->where('id', '>', $transaction->id)
      ->exists();

    if ($hasLaterTransaction) {
      return redirect()->route('admin.supplier-transactions.index')
        ->with('error', 'Cannot update this transaction because subsequent transactions already exist for this supplier.');
    }

    if ($transaction->type !== 'pay') {
      return redirect()->route('admin.supplier-transactions.index')
        ->with('error', 'Only direct supplier payment transactions can be edited here.');
    }

    $validated = $request->validate([
      'amount'         => ['required', 'numeric', 'min:0.01'],
      'payment_method' => ['required', 'string', 'max:50'],
      'branch_id'      => ['nullable', 'exists:branches,id'],
      'note'           => ['nullable', 'string', 'max:500'],
    ]);

    try {
      DB::transaction(function () use ($transaction, $validated) {
        $supplier = Supplier::lockForUpdate()->findOrFail($transaction->supplier_id);

        $dueBeforeTransaction = (float) $transaction->due_before_transaction;
        $newPaymentAmount     = round((float) $validated['amount'], 2);
        $newDueAfter          = round($dueBeforeTransaction - $newPaymentAmount, 2);

        // Update supplier balance
        $supplier->update(['due' => $newDueAfter]);

        // Update transaction record
        $transaction->update([
          'amount'                => $newPaymentAmount,
          'due_after_transaction' => $newDueAfter,
          'payment_method'        => $validated['payment_method'],
          'branch_id'             => $validated['branch_id'] ?? null,
          'note'                  => $validated['note'] ?? $transaction->note,
        ]);
      });

      return redirect()->route('admin.supplier-transactions.index')
        ->with('success', 'Supplier payment BRST00' . $transaction->id . ' updated successfully.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', 'Error updating supplier payment: ' . $e->getMessage());
    }
  }

  public function destroyForAdmin($id)
  {
    $transaction = SupplierTransaction::findOrFail($id);

    // Check if subsequent transactions exist
    $hasLaterTransaction = SupplierTransaction::where('supplier_id', $transaction->supplier_id)
      ->where('id', '>', $transaction->id)
      ->exists();

    if ($hasLaterTransaction) {
      return back()->with('error', 'Cannot delete this transaction because subsequent transactions already exist for this supplier.');
    }

    if ($transaction->type !== 'pay') {
      return back()->with('error', 'Only direct supplier payment transactions can be deleted here.');
    }

    try {
      DB::transaction(function () use ($transaction) {
        $supplier = Supplier::lockForUpdate()->findOrFail($transaction->supplier_id);

        // Revert payment amount back to supplier due
        $restoredDue = round((float) ($supplier->due ?? 0) + (float) $transaction->amount, 2);
        $supplier->update(['due' => $restoredDue]);

        // Delete transaction
        $transaction->delete();
      });

      return redirect()->route('admin.supplier-transactions.index')
        ->with('success', 'Supplier payment BRST00' . $id . ' deleted and supplier due balance restored successfully.');
    } catch (\Exception $e) {
      return back()->with('error', 'Error deleting supplier payment: ' . $e->getMessage());
    }
  }

  public function viewSlip($id)
  {
    $transaction = SupplierTransaction::with(['supplier', 'branch'])->findOrFail($id);

    if ($transaction->type !== 'pay') {
      return back()->with('error', 'Payment slip is only available for payment transactions.');
    }

    return view('pages.common.supplier-transaction.slip', compact('transaction'));
  }

  public function publicShow($id)
  {
    $transaction = SupplierTransaction::with(['supplier', 'branch'])->findOrFail($id);

    return view('pages.common.supplier-transaction.public-show', compact('transaction'));
  }

}
