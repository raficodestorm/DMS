@extends('layouts.adminlayout')

@section('content')

@php
$isPayment = $payment->type == 'pay';

if ($isPayment) {

// PAYMENT
if ($payment->status == 'complete') {
$dueBefore = $payment->due + $payment->amount;
$dueAfter = $payment->due;
} else {
$dueBefore = $payment->due;
$dueAfter = max(0, $payment->due - $payment->amount);
}

} else {

// PURCHASE / BUY
if ($payment->status == 'complete') {
$dueBefore = $payment->due - $payment->amount;
$dueAfter = $payment->due;
} else {
$dueBefore = $payment->due;
$dueAfter = $payment->due + $payment->amount;
}

}
@endphp

<p class="show-head">
  {{ $isPayment ? 'Payment Details' : 'Purchase Details' }}
</p>

<div class="show-card">

  <div class="content-area">
    <p style="color: gray; font-style:italic; text-alaign: center;">This customer belongs to {{
      $payment->customer->branch->name}} zone</p>
    <div class="rank-pill">
      BRT00{{ $payment->id }}
    </div>

    <div class="info-list">

      <div class="info-group">
        <span class="i-label">Shop Name</span>
        <span class="i-value">
          <strong>{{ $payment->customer->shop_name ?? 'N/A' }}</strong>
        </span>
      </div>

      @if(!$isPayment)
      <div class="info-group">
        <span class="i-label">Order ID</span>
        <span class="i-value">
          <strong>BRS00{{ $payment->order_id ?? 'N/A' }}</strong>
        </span>
      </div>
      @endif

      <div class="info-group">
        <span class="i-label">
          {{ $isPayment ? 'Collected By' : 'Processed By' }}
        </span>
        <span class="i-value">
          {{ $payment->sr->fullname ?? 'N/A' }}
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">
          {{ $isPayment ? 'Payment Amount' : 'Purchase Amount' }}
        </span>

        <span class="i-value" style="font-size:1rem; color: var(--primary);">
          <strong>{{ number_format($payment->amount, 2) }} TK</strong>
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Status</span>
        <span class="i-value">

          @if($payment->status == 'complete')
          <span style="color:#16a34a;font-weight:700;">
            ● Completed
          </span>

          @elseif($payment->status == 'pending')
          <span style="color:#f59e0b;font-weight:700;">
            ● Pending Approval
          </span>

          @else
          <span style="color:#64748b;font-weight:700;">
            ● {{ ucfirst($payment->status) }}
          </span>
          @endif

        </span>
      </div>

      <div class="info-group">
        <span class="i-label">
          {{ $isPayment ? 'Payment Date' : 'Purchase Date' }}
        </span>

        <span class="i-value">
          {{ $payment->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
        </span>
      </div>

    </div>

    <div class="info-list">
      <div class="info-group">

        <span class="i-label">
          {{ $isPayment ? 'Due Before Payment' : 'Due Before Purchase' }}
        </span>

        <span class="i-value" style="color: var(--text-main); font-weight:700;">
          {{ number_format($dueBefore, 2) }} TK
        </span>

      </div>
      <div class="info-group">

        <span class="i-label">
          {{ $isPayment ? 'Due After Payment' : 'Due After Purchase' }}
        </span>

        <span class="i-value" style="color: var(--danger-color); font-weight:700;">
          {{ number_format($dueAfter, 2) }} TK
        </span>

      </div>
    </div>

    <div class="statement">
      <p class="statement-text">
        <strong>Note / Remarks:</strong><br>
        {{ $payment->note ?? 'No additional notes provided.' }}
      </p>
    </div>

    {{-- Dynamic Statement --}}
    <div class="statement">
      <p class="statement-text">

        @if($isPayment)

        @if($payment->status == 'pending')

        <strong style="color:#f59e0b;">Pending Notice:</strong>
        Payment request has been received successfully and is under verification.

        @elseif($payment->status == 'complete')

        <strong style="color:#16a34a;">Payment Confirmed:</strong>
        This transaction has been verified successfully. Payment received by {{ $payment->customer->branch->name}}
        branch.

        @else

        <strong>Transaction Update:</strong>
        Please review the current payment status shown above.

        @endif

        @else

        @if($payment->status == 'pending')

        <strong style="color:#f59e0b;">Purchase Pending:</strong>
        This purchase transaction has been created successfully and is awaiting confirmation.

        @elseif($payment->status == 'complete')

        <strong style="color:#16a34a;">Purchase Confirmed:</strong>
        This purchase has been processed successfully.
        The amount has been added to the customer's outstanding balance.

        @else

        <strong>Purchase Update:</strong>
        Please review the latest purchase status shown above.

        @endif

        @endif

      </p>
    </div>


  </div>
  <div class="card-footer-actions">
    @if($isPayment)

    <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this request?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-smart btn-red" style="border: none;">
        <i class="fa-solid fa-trash"></i> Delete
      </button>
    </form>

    @if($payment->status == 'complete')
    <a href="{{ route('payments.slip', $payment->id) }}" class="btn-smart btn-blue">
      <i class="fa-solid fa-file-invoice"></i> View Slip
    </a>
    @endif
    @endif

  </div>
</div>

<div style="text-align:center; margin-top:20px;">
  <a href="{{ route('admin.payments.index') }}" style="color:var(--text-muted); text-decoration:none;">

    <i class="fas fa-arrow-left"></i> Back to List

  </a>
</div>

@endsection