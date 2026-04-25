<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PaymentController extends Controller
{

  public function indexForSr(Request $request)
  {
    $sr = auth()->user();

    $query = Transaction::with(['customer', 'sr'])


      ->whereHas('customer', function ($q) use ($sr) {
        $q->where('branch_id', $sr->branch_id);
      })

      ->latest();

    if ($request->filled('search')) {

      $search = trim($request->search);

      $query->where(function ($q) use ($search) {

        if (str_starts_with('BRT00', strtoupper($search))) {
          return;
        }

        if (preg_match('/^BRT00(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }
        // Normal Search
        $q->where('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          });
      });
    }

    $payments = $query->paginate(15);

    if ($request->ajax()) {
      return response()->json([
        'table'  => view('pages.sr.payment.table', compact('payments'))->render(),
        'mobile' => view('pages.sr.payment.mtable', compact('payments'))->render(),
      ]);
    }

    return view('pages.sr.payment.index', compact('payments'));
  }



  public function indexForManager(Request $request)
  {
    $manager = auth()->user();

    $query = Transaction::with(['customer', 'sr'])


      ->whereHas('customer', function ($q) use ($manager) {
        $q->where('branch_id', $manager->branch_id);
      })

      ->latest();

    if ($request->filled('search')) {

      $search = trim($request->search);

      $query->where(function ($q) use ($search) {

        if (str_starts_with('BRT00', strtoupper($search))) {
          return;
        }

        if (preg_match('/^BRT00(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }
        // Normal Search
        $q->where('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          });
      });
    }

    $payments = $query->paginate(15);

    if ($request->ajax()) {
      return response()->json([
        'table'  => view('pages.manager.payment.table', compact('payments'))->render(),
        'mobile' => view('pages.manager.payment.mtable', compact('payments'))->render(),
      ]);
    }

    return view('pages.manager.payment.index', compact('payments'));
  }









  public function indexForAdmin(Request $request)
  {
    $query = Transaction::with(['customer', 'sr'])->latest();

    if ($request->filled('search')) {

      $search = trim($request->search);

      $query->where(function ($q) use ($search) {

        if (str_starts_with('BRT00', strtoupper($search))) {
          return;
        }

        if (preg_match('/^BRT00(\d+)$/i', $search, $match)) {
          $q->where('id', $match[1]);
          return;
        }

        $q->where('id', $search)
          ->orWhereHas('customer', function ($customer) use ($search) {
            $customer->where('shop_name', 'like', "%{$search}%");
          });
      });
    }

    $payments = $query->paginate(15);

    if ($request->ajax()) {
      return response()->json([
        'table'  => view('pages.admin.transaction.table', compact('payments'))->render(),
        'mobile' => view('pages.admin.transaction.mtable', compact('payments'))->render(),
      ]);
    }

    return view('pages.admin.transaction.index', compact('payments'));
  }


  public function indexForCustomer()
  {
    $customerId = auth()->user()->customer_id;

    $payments = Transaction::with(['customer', 'sr'])
      ->where('customer_id', $customerId)
      ->latest()
      ->paginate(20);

    return view(
      'pages.customerpanel.payment.index',
      compact('payments')
    );
  }

  public function create()
  {
    $customers = Customer::where('branch_id', auth()->user()->branch_id)->orderBy('shop_name', 'asc')->get();
    return view('pages.' . auth()->user()->role . '.payment.create', compact('customers'));
  }

  /**
   * 5. Store: Payment pending obosthay save hobe ebong manager-ke notify korbe
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'amount'      => 'required|numeric|min:1',
      'note'        => 'nullable|string',
    ]);

    // Transaction-er baire user fetch kora safe
    $user = auth()->user();
    $branchId = $user->branch_id;

    try {
      // Transaction shudhu DB record create ebong notification triggering-er jonno
      $payment = DB::transaction(function () use ($validated, $user, $branchId) {

        $customer = Customer::lockForUpdate()->findOrFail($validated['customer_id']);
        $payment = Transaction::create([
          'customer_id' => $customer->id,
          'sr_id'       => $user->id,
          'type'        => 'pay',
          'amount'      => $validated['amount'],
          'due'         => $customer->due,
          'status'      => 'pending',
          'note'        => $validated['note'] ?? null,
        ]);

        // Branch wise managers fetch
        $managers = User::where('role', 'manager')
          ->where('branch_id', $branchId)
          ->get();

        // Notification data preparation
        $notificationData = [
          'title'   => 'New Payment Request',
          'message' => [
            'text' => 'A new payment request has been submitted by',
            'from' => $user->username
          ],
          'url'     => route('manager.payments.show', $payment->id),
          'type'    => 'new_payment'
        ];

        // Notification send (Manager thakle notify hobe)
        foreach ($managers as $manager) {
          $manager->notify(
            new SystemNotification($notificationData)
          );
        }

        return $payment;
      });

      // Success message and redirect (Transaction-er baire)
      return redirect()
        ->route('sr.payments.index')
        ->with('success', 'Payment request sent to manager for approval.');
    } catch (\Exception $e) {
      // Validation data return korar jonno withInput() dorkar
      return back()->withInput()->with(
        'error',
        'Something went wrong! ' . $e->getMessage()
      );
    }
  }







  public function managerStore(Request $request)
  {
    $validated = $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'amount'      => 'required|numeric|min:1',
      'note'        => 'nullable|string|max:500',
    ]);

    try {
      DB::transaction(function () use ($validated) {

        $customer = Customer::lockForUpdate()->findOrFail($validated['customer_id']);

        $newDue = max(0, $customer->due - $validated['amount']);

        $payment = Transaction::create([
          'customer_id' => $customer->id,
          'sr_id'       => auth()->id(),
          'type'        => 'pay',
          'amount'      => $validated['amount'],
          'due'         => $newDue,
          'status'      => 'complete',
          'note'        => $validated['note'] ?? null,
        ]);

        $customer->update([
          'due' => $newDue
        ]);

        $notificationData = [
          'title'   => 'Payment Received',
          'message' => [
            'text' => 'আপনার ' . number_format($payment->amount, 2) . ' TK পেমেন্ট সফলভাবে গ্রহণ করা হয়েছে। বর্তমান ডিউ: ' . number_format($customer->due, 2) . ' TK',
            'from' => auth()->user()->username
          ],
          'url'     => route('payments.slip', $payment->id),
          'type'    => 'payment_completed'
        ];

        $thecustomer = User::where('role', 'customer')
          ->where('customer_id', $payment->customer_id)
          ->first();

        if ($thecustomer) {
          $thecustomer->notify(
            new SystemNotification($notificationData)
          );
        }




        $admins = User::where('role', 'admin')->get();

        $adminNotification = [
          'title' => 'Customer Payment Received',
          'message' => [
            'text' => $customer->shop_name .
              ' paid ' .
              number_format($payment->amount, 2) .
              ' TK.   Remaining due: ' .
              number_format($newDue, 2) . ' TK. ',
            'from' => auth()->user()->branch->name
          ],
          'url'  => route('admin.payments.show', $payment->id),
          'type' => 'customer_payment_received'
        ];

        foreach ($admins as $admin) {
          $admin->notify(
            new SystemNotification($adminNotification)
          );
        }
      });

      return redirect()
        ->route('manager.payments.index')
        ->with('success', 'Payment completed successfully.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * 6. Approve: Manager/Admin approve korle status complete hobe ebong Due kombe
   */
  public function approve(Transaction $payment)
  {
    if ($payment->status === 'complete') {
      return back()->with('error', 'Already approved.');
    }

    try {
      DB::transaction(function () use ($payment) {

        $payment = Transaction::lockForUpdate()->findOrFail($payment->id);

        $customer = Customer::lockForUpdate()->findOrFail($payment->customer_id);

        $newDue = max(0, $customer->due - $payment->amount);

        $payment->update([
          'status' => 'complete',
          'due'    => $newDue
        ]);

        $customer->update([
          'due' => $newDue
        ]);

        $notificationData = [
          'title'   => 'Payment Approved',
          'message' => [
            'text' => 'আপনার ' . number_format($payment->amount, 2) . ' TK পেমেন্ট সফলভাবে গ্রহণ করা হয়েছে। বর্তমান ডিউ: ' . number_format($newDue, 2) . ' TK',
            'from' => auth()->user()->username
          ],
          'url'     => route('payments.slip', $payment->id),
          'type'    => 'payment_approved'
        ];

        $thecustomer = User::where('role', 'customer')
          ->where('customer_id', $payment->customer_id)
          ->first();

        if ($thecustomer) {
          $thecustomer->notify(
            new SystemNotification($notificationData)
          );
        }




        $admins = User::where('role', 'admin')->get();

        $adminNotification = [
          'title' => 'Customer Payment Received',
          'message' => [
            'text' => $customer->shop_name .
              ' paid ' .
              number_format($payment->amount, 2) .
              ' TK.   Remaining due: ' .
              number_format($newDue, 2) . ' TK. ',
            'from' => auth()->user()->branch->name
          ],
          'url'  => route('admin.payments.show', $payment->id),
          'type' => 'customer_payment_received'
        ];

        foreach ($admins as $admin) {
          $admin->notify(
            new SystemNotification($adminNotification)
          );
        }
      });

      return back()->with('success', 'Payment approved successfully.');
    } catch (\Exception $e) {
      return back()->with('error', $e->getMessage());
    }
  }


  public function show(Transaction $payment)
  {
    $payment->load(['customer', 'sr']);

    return view('pages.' . auth()->user()->role . '.payment.show', compact('payment'));
  }

  public function showForCust(Transaction $payment)
  {
    $payment->load(['customer', 'sr']);

    return view('pages.customerpanel.payment.show', compact('payment'));
  }

  public function showForAdmin(Transaction $payment)
  {
    $payment->load(['customer', 'sr']);

    return view('pages.admin.transaction.show', compact('payment'));
  }

  public function publicShow(Transaction $payment)
  {
    $payment->load(['customer', 'sr']);

    return view('pages.common.payment.show', compact('payment'));
  }

  public function edit(Transaction $payment)
  {
    if ($payment->status === 'complete') {
      return back()->with('error', 'Completed payment cannot edit.');
    }

    $customers = Customer::orderBy('shop_name')->get();

    return view('pages.sr.payment.edit', compact('payment', 'customers'));
  }

  /**
   * 9. Update: Payment details update kora
   */
  public function update(Request $request, Transaction $payment)
  {
    if ($payment->status === 'complete') {
      return back()->with('error', 'Completed payment cannot update.');
    }

    $validated = $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'amount'      => 'required|numeric|min:1',
      'note'        => 'nullable|string',
    ]);

    $payment->update($validated);

    return redirect()
      ->route('sr.payments.index')
      ->with('success', 'Payment updated successfully.');
  }


  public function viewSlip(Transaction $payment)
  {
    // Ensure only completed payments can have a slip
    if ($payment->status !== 'complete') {
      return back()->with('error', 'Slip is only available for completed payments.');
    }

    // Load necessary relationships
    $payment->load(['customer', 'sr']);

    return view('pages.common.payment.slip', compact('payment'));
  }


  public function destroy(Transaction $payment)
  {
    try {

      DB::transaction(function () use ($payment) {

        $customer = Customer::lockForUpdate()
          ->findOrFail($payment->customer_id);

        if (
          $payment->type === 'pay' &&
          $payment->status === 'complete'
        ) {
          $customer->increment('due', $payment->amount);
        }

        $payment->delete();
      });

      return redirect()
        ->route(auth()->user()->role . '.payments.index')->with(
          'success',
          'Transaction deleted successfully.'
        );
    } catch (\Exception $e) {

      return back()->with(
        'error',
        'Something went wrong!'
      );
    }
  }
}
