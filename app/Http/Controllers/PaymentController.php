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
      'pages.customer.payment.index',
      compact('payments')
    );
  }

  public function create()
  {
    $customers = Customer::where('branch_id', auth()->user()->branch_id)
        ->where('due', '>', 0)
        ->orderBy('shop_name', 'asc')
        ->get();
    return view('pages.' . auth()->user()->role . '.payment.create', compact('customers'));
  }

  





  public function store(Request $request)
{
    $validated = $request->validate([
        'customer_id' => ['required', 'exists:customers,id'],
        'amount'      => ['required', 'numeric', 'min:0.01'],
        'note'        => ['nullable', 'string', 'max:1000'],
        'payment_method' => ['required', 'string', 'max:50'],
    ]);

    $user = auth()->user();
    $branchId = $user->branch_id;

    try {

        DB::transaction(function () use ($validated, $user, $branchId) {

            /*
             * ---------------------------------------------------------
             * 1. Get customer with row lock
             * ---------------------------------------------------------
             */
            $customer = Customer::query()
                ->whereKey($validated['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * ---------------------------------------------------------
             * 2. Current due snapshot
             *
             * Payment is still pending, so customer due is NOT
             * changed here.
             * ---------------------------------------------------------
             */
            $dueBeforeTransaction = round(
                (float) ($customer->due ?? 0),
                2
            );

            $paymentAmount = round(
                (float) $validated['amount'],
                2
            );

            /*
             * ---------------------------------------------------------
             * 3. Prevent payment request greater than customer due
             * ---------------------------------------------------------
             */
            if ($paymentAmount > $dueBeforeTransaction) {
                throw new \RuntimeException(
                    'Payment amount cannot be greater than customer due.'
                );
            }

            /*
             * ---------------------------------------------------------
             * 4. Since payment is pending, due_after_transaction
             * is the expected due after approval.
             * ---------------------------------------------------------
             */
            $dueAfterTransaction = round(
                $dueBeforeTransaction - $paymentAmount,
                2
            );

            /*
             * ---------------------------------------------------------
             * 5. Create payment transaction
             * ---------------------------------------------------------
             */
            $payment = Transaction::create([
                'customer_id'            => $customer->id,
                'order_id'               => null,
                'sr_id'                  => $user->id,
                'branch_id'              => $branchId,

                'type'                   => 'pay',
                'amount'                 => $paymentAmount,

                'due_before_transaction' => $dueBeforeTransaction,
                'due_after_transaction'  => $dueAfterTransaction,
                'payment_method'         => $validated['payment_method'],
                'status'                 => 'pending',
                'note'                   => $validated['note'] ?? null,
            ]);

            /*
             * ---------------------------------------------------------
             * 6. Get branch managers
             * ---------------------------------------------------------
             */
            $managers = User::query()
                ->where('role', 'manager')
                ->where('branch_id', $branchId)
                ->get();

            /*
             * ---------------------------------------------------------
             * 7. Notification
             * ---------------------------------------------------------
             */
            $notificationData = [
                'title'   => 'New Payment Request',

                'message' => [
                    'text' => 'A new payment request has been submitted by',
                    'from' => $user->username,
                ],

                'url' => route(
                    'manager.payments.show',
                    $payment->id
                ),

                'type' => 'new_payment',
            ];

            foreach ($managers as $manager) {
                $manager->notify(
                    new SystemNotification($notificationData)
                );
            }
        });

        return redirect()
            ->route('sr.payments.index')
            ->with(
                'success',
                'Payment request sent to manager for approval.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'Something went wrong while submitting the payment request.'
            );
    }
}







public function managerStore(Request $request)
{
    $validated = $request->validate([
        'customer_id' => [
            'required',
            'exists:customers,id',
        ],

        'amount' => [
            'required',
            'numeric',
            'min:0.01',
        ],

        'payment_method' => ['required', 'string', 'max:50'],

        'note' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    try {

        DB::transaction(function () use ($validated) {

            $user = auth()->user();

            /*
             * ---------------------------------------------------------
             * 1. Get customer with row lock
             * ---------------------------------------------------------
             */
            $customer = Customer::query()
                ->whereKey($validated['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * ---------------------------------------------------------
             * 2. Calculate payment and due
             * ---------------------------------------------------------
             */
            $paymentAmount = round(
                (float) $validated['amount'],
                2
            );

            $dueBeforeTransaction = round(
                (float) ($customer->due ?? 0),
                2
            );

            /*
             * Payment cannot be greater than current customer due.
             */
            if ($paymentAmount > $dueBeforeTransaction) {
                throw new \RuntimeException(
                    'Payment amount of ৳' .
                    number_format($paymentAmount, 2) .
                    ' cannot be greater than customer due of ৳' .
                    number_format($dueBeforeTransaction, 2) .
                    '.'
                );
            }

            /*
             * Calculate remaining due.
             */
            $dueAfterTransaction = round(
                $dueBeforeTransaction - $paymentAmount,
                2
            );

            $dueAfterTransaction = max(
                0,
                $dueAfterTransaction
            );

            /*
             * ---------------------------------------------------------
             * 3. Create completed payment transaction
             * ---------------------------------------------------------
             */
            $payment = Transaction::create([
                'customer_id'            => $customer->id,
                'order_id'               => null,
                'sr_id'                  => $user->id,
                'branch_id'              => $user->branch_id,

                'type'                   => 'pay',
                'amount'                 => $paymentAmount,

                'due_before_transaction' => $dueBeforeTransaction,
                'due_after_transaction'  => $dueAfterTransaction,
                'payment_method'         => $validated['payment_method'],
                'status'                 => 'complete',

                'note'                   => $validated['note'] ?? null,
            ]);

            /*
             * ---------------------------------------------------------
             * 4. Update customer's actual due
             * ---------------------------------------------------------
             */
            $customer->update([
                'due' => $dueAfterTransaction,
            ]);

            /*
             * ---------------------------------------------------------
             * 5. Customer notification
             * ---------------------------------------------------------
             */
            $notificationData = [
                'title'   => 'Payment Received',

                'message' => [
                    'text' =>
                        'আপনার ' .
                        number_format($paymentAmount, 2) .
                        ' TK পেমেন্ট সফলভাবে গ্রহণ করা হয়েছে। বর্তমান ডিউ: ' .
                        number_format($dueAfterTransaction, 2) .
                        ' TK',

                    'from' => $user->username,
                ],

                'url' => route(
                    'payments.slip',
                    $payment->id
                ),

                'type' => 'payment_completed',
            ];

            $theCustomer = User::query()
                ->where('role', 'customer')
                ->where('customer_id', $customer->id)
                ->first();

            if ($theCustomer) {
                $theCustomer->notify(
                    new SystemNotification($notificationData)
                );
            }

            /*
             * ---------------------------------------------------------
             * 6. Admin notification
             * ---------------------------------------------------------
             */
            $admins = User::query()
                ->where('role', 'admin')
                ->get();

            $branchName = $user->branch?->name ?? 'Branch';

            $adminNotification = [
                'title' => 'Customer Payment Received',

                'message' => [
                    'text' =>
                        $customer->shop_name .
                        ' paid ' .
                        number_format($paymentAmount, 2) .
                        ' TK. Remaining due: ' .
                        number_format($dueAfterTransaction, 2) .
                        ' TK.',

                    'from' => $branchName,
                ],

                'url' => route(
                    'admin.payments.show',
                    $payment->id
                ),

                'type' => 'customer_payment_received',
            ];

            foreach ($admins as $admin) {
                $admin->notify(
                    new SystemNotification($adminNotification)
                );
            }
        });

        return redirect()
            ->route('manager.payments.index')
            ->with(
                'success',
                'Payment completed successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'Payment processing failed: ' . $e->getMessage()
            );
    }
}


  


  public function approve(Transaction $payment)
{
    try {

        DB::transaction(function () use ($payment) {

            /*
             * ---------------------------------------------------------
             * 1. Lock payment transaction
             * ---------------------------------------------------------
             */
            $payment = Transaction::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Prevent double approval.
             */
            if ($payment->status === 'complete') {
                throw new \RuntimeException(
                    'Payment has already been approved.'
                );
            }

            

            /*
             * ---------------------------------------------------------
             * 2. Lock customer
             * ---------------------------------------------------------
             */
            $customer = Customer::query()
                ->whereKey($payment->customer_id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * ---------------------------------------------------------
             * 3. Calculate actual due
             * ---------------------------------------------------------
             */
            $dueBeforeTransaction = round(
                (float) ($customer->due ?? 0),
                2
            );

            $paymentAmount = round(
                (float) $payment->amount,
                2
            );

            /*
             * A customer cannot pay more than current due.
             */
            if ($paymentAmount > $dueBeforeTransaction) {
                throw new \RuntimeException(
                    'Payment amount of ৳' .
                    number_format($paymentAmount, 2) .
                    ' is greater than the customer current due of ৳' .
                    number_format($dueBeforeTransaction, 2) .
                    '.'
                );
            }

            /*
             * ---------------------------------------------------------
             * 4. Calculate new due
             * ---------------------------------------------------------
             */
            $dueAfterTransaction = round(
                $dueBeforeTransaction - $paymentAmount,
                2
            );

            /*
             * Safety guard against negative floating-point values.
             */
            $dueAfterTransaction = max(
                0,
                $dueAfterTransaction
            );

            /*
             * ---------------------------------------------------------
             * 5. Update Customer Due
             * ---------------------------------------------------------
             */
            $customer->update([
                'due' => $dueAfterTransaction,
            ]);

            /*
             * ---------------------------------------------------------
             * 6. Complete Payment Transaction
             * ---------------------------------------------------------
             */
            $payment->update([
                'status'                 => 'complete',
                'branch_id'              => $payment->branch_id
                    ?? $customer->branch_id
                    ?? auth()->user()->branch_id,

                'due_before_transaction' => $dueBeforeTransaction,
                'due_after_transaction'  => $dueAfterTransaction,
            ]);

            /*
             * ---------------------------------------------------------
             * 7. Customer notification
             * ---------------------------------------------------------
             */
            $notificationData = [
                'title'   => 'Payment Approved',

                'message' => [
                    'text' =>
                        'আপনার ' .
                        number_format($paymentAmount, 2) .
                        ' TK পেমেন্ট সফলভাবে গ্রহণ করা হয়েছে। বর্তমান ডিউ: ' .
                        number_format($dueAfterTransaction, 2) .
                        ' TK',

                    'from' => auth()->user()->username,
                ],

                'url' => route(
                    'payments.slip',
                    $payment->id
                ),

                'type' => 'payment_approved',
            ];

            $theCustomer = User::query()
                ->where('role', 'customer')
                ->where('customer_id', $customer->id)
                ->first();

            if ($theCustomer) {
                $theCustomer->notify(
                    new SystemNotification($notificationData)
                );
            }

            /*
             * ---------------------------------------------------------
             * 8. Admin notification
             * ---------------------------------------------------------
             */
            $admins = User::query()
                ->where('role', 'admin')
                ->get();

            $branchName = auth()->user()->branch?->name
                ?? 'Branch';

            $adminNotification = [
                'title' => 'Customer Payment Received',

                'message' => [
                    'text' =>
                        $customer->shop_name .
                        ' paid ' .
                        number_format($paymentAmount, 2) .
                        ' TK. Remaining due: ' .
                        number_format($dueAfterTransaction, 2) .
                        ' TK.',

                    'from' => $branchName,
                ],

                'url' => route(
                    'admin.payments.show',
                    $payment->id
                ),

                'type' => 'customer_payment_received',
            ];

            foreach ($admins as $admin) {
                $admin->notify(
                    new SystemNotification($adminNotification)
                );
            }
        });

        return back()->with(
            'success',
            'Payment approved successfully.'
        );

    } catch (\Throwable $e) {

        report($e);

        return back()->with(
            'error',
            'Payment approval failed: ' . $e->getMessage()
        );
    }
}


  public function show(Transaction $payment)
  {
    $payment->load(['customer', 'sr']);

    return view('pages.' . auth()->user()->role . '.payment.show', compact('payment'));
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

    $branchId = auth()->user()->branch_id;

    // Include the current payment's customer even if their due is now 0
    $customers = Customer::where('branch_id', $branchId)
        ->where(function($q) use ($payment) {
            $q->where('due', '>', 0)
              ->orWhere('id', $payment->customer_id);
        })
        ->orderBy('shop_name')
        ->get();

    return view('pages.sr.payment.edit', compact('payment', 'customers'));
  }

  /**
   * 9. Update: Payment details update kora
   */
  public function update(Request $request, Transaction $payment)
{
    /*
     * ---------------------------------------------------------
     * 1. Only pending payment can be edited
     * ---------------------------------------------------------
     */
    if ($payment->status !== 'pending') {
        return back()->with(
            'error',
            'Only pending payment requests can be updated.'
        );
    }

    /*
     * ---------------------------------------------------------
     * 2. Authorization
     *
     * SR can edit only his own payment request.
     * ---------------------------------------------------------
     */
    if ($payment->sr_id !== auth()->id()) {
        return back()->with(
            'error',
            'Unauthorized access.'
        );
    }

    /*
     * ---------------------------------------------------------
     * 3. Validate
     * ---------------------------------------------------------
     */
    $validated = $request->validate([
        'customer_id' => [
            'required',
            'exists:customers,id',
        ],

        'amount' => [
            'required',
            'numeric',
            'min:0.01',
        ],

        'payment_method' => [ 'required', 'string', 'max:50', ],

        'note' => [
            'nullable',
            'string',
            'max:1000',
        ],
    ]);

    try {

        DB::transaction(function () use (
            $validated,
            $payment
        ) {

            /*
             * Lock payment row.
             */
            $payment = Transaction::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Prevent editing if it was approved
             * while this request was being processed.
             */
            if ($payment->status !== 'pending') {
                throw new \RuntimeException(
                    'Payment is no longer pending and cannot be updated.'
                );
            }

            /*
             * -----------------------------------------------------
             * Customer
             * -----------------------------------------------------
             */
            $customer = Customer::query()
                ->whereKey($validated['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * -----------------------------------------------------
             * Payment amount
             * -----------------------------------------------------
             */
            $amount = round(
                (float) $validated['amount'],
                2
            );

            $currentDue = round(
                (float) ($customer->due ?? 0),
                2
            );

            /*
             * A payment request cannot exceed current customer due.
             */
            if ($amount > $currentDue) {
                throw new \RuntimeException(
                    'Payment amount cannot be greater than customer due.'
                );
            }

            /*
             * -----------------------------------------------------
             * Update payment
             * -----------------------------------------------------
             *
             * Since this is still pending, we don't change
             * customer's actual due here.
             *
             * Due snapshot is recalculated for the edited request.
             */
            $dueAfterTransaction = round(
                $currentDue - $amount,
                2
            );

            $payment->update([
                'customer_id'            => $customer->id,
                'amount'                 => $amount,
                'payment_method'         => $validated['payment_method'],
                'due_before_transaction' => $currentDue,
                'due_after_transaction'  => $dueAfterTransaction,
                'note'                   => $validated['note'] ?? null,
            ]);
        });

        return redirect()
            ->route('sr.payments.index')
            ->with(
                'success',
                'Payment updated successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'Something went wrong while updating the payment.'
            );
    }
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

            
            $payment = Transaction::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            

            /*
             * ---------------------------------------------------------
             * 3. Pending payment
             *
             * Pending payment has not changed customer due,
             * so simply deleting it is safe.
             * ---------------------------------------------------------
             */
            if (
                $payment->type === 'pay' &&
                $payment->status === 'pending'
            ) {
                $payment->delete();

                return;
            }

            /*
             * ---------------------------------------------------------
             * 4. Completed payment
             *
             * A completed payment already reduced customer due.
             * Therefore, deleting it must restore that amount.
             * ---------------------------------------------------------
             */
            if (
                $payment->type === 'pay' &&
                $payment->status === 'complete'
            ) {

                $customer = Customer::query()
                    ->whereKey($payment->customer_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $amount = round(
                    (float) $payment->amount,
                    2
                );

                $customer->increment('due', $amount);
            }

            /*
             * ---------------------------------------------------------
             * 5. Delete transaction
             * ---------------------------------------------------------
             */
            $payment->delete();
        });

        return redirect()
            ->route(
                auth()->user()->role . '.payments.index'
            )
            ->with(
                'success',
                'Transaction deleted successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()->with(
            'error',
            'Unable to delete this transaction: ' . $e->getMessage()
        );
    }
}


}
