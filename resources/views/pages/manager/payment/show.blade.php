@extends('layouts.managerlayout')

@section('content')

@php
$isPayment = $payment->type == 'pay';
$isPurchase = $payment->type == 'buy';
$isReturn = $payment->type == 'return';

$dueBefore = 0;
$dueAfter = 0;

switch ($payment->type) {

case 'pay':
if ($payment->status == 'complete') {
// Payment already deducted
$dueBefore = $payment->due + $payment->amount;
$dueAfter = $payment->due;

} else {
// Pending payment
$dueBefore = $payment->due;
$dueAfter = max(0, $payment->due - $payment->amount);
}
break;

case 'buy':
if ($payment->status == 'complete') {
// Purchase already added
$dueBefore = max(0, $payment->due - $payment->amount);
$dueAfter = $payment->due;

} else {
// Pending purchase
$dueBefore = $payment->due;
$dueAfter = $payment->due + $payment->amount;
}
break;

case 'return':
if ($payment->status == 'complete') {
// Return reduces due
$dueBefore = $payment->due + $payment->amount;
$dueAfter = $payment->due;

} else {
// Pending return
$dueBefore = $payment->due;
$dueAfter = max(0, $payment->due - $payment->amount);
}
break;
default:
$dueBefore = $payment->due;
$dueAfter = $payment->due;
break;
}
@endphp

<p class="show-head">
  @if($isPayment)
  Payment Details
  @elseif($isReturn)
  Return Details
  @else
  Purchase Details
  @endif
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
          @if($isPayment)
          Collected By
          @elseif($isReturn)
          Returned By
          @else
          Processed By
          @endif
        </span>
        <span class="i-value">
          {{ $payment->sr->fullname ?? 'N/A' }}
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">
          @if($isPayment)
          Payment Amount
          @elseif($isReturn)
          Return Amount
          @else
          Purchase Amount
          @endif
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
          @if($isPayment)
          Payment Date
          @elseif($isReturn)
          Return Date
          @else
          Purchase Date
          @endif
        </span>

        <span class="i-value">
          {{ $payment->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
        </span>
      </div>

    </div>

    <div class="info-list">
      <div class="info-group">

        <span class="i-label">
          @if($isPayment)
          Due Before Payment
          @elseif($isReturn)
          Due Before Return
          @else
          Due Before Purchase
          @endif
        </span>

        <span class="i-value" style="color: var(--text-main); font-weight:700;">
          {{ number_format($dueBefore, 2) }} TK
        </span>

      </div>
      <div class="info-group">

        <span class="i-label">
          @if($isPayment)
          Due After Payment
          @elseif($isReturn)
          Due After Return
          @else
          Due After Purchase
          @endif
        </span>

        <span class="i-value" style="color: var(--danger-color); font-weight:700;">
          {{ number_format($dueAfter, 2) }} TK
        </span>

      </div>
    </div>

    {{-- Dynamic Statement --}}
    <div class="statement">
      <p class="statement-text">
        <strong>Note / Remarks:</strong>
        {{ $payment->note ?? 'No additional notes provided.' }}
      </p>
    </div>


  </div>
  @if($isPayment)
  @if($payment->status == 'pending')
  <div class="approval-box"
    style="margin-top: 20px; padding: 20px; background: #f0f7ff; border-radius: 8px; border: 1px dashed #3131ff; text-align: center;">
    <p style="margin-bottom: 15px; color: #333;">Would you like to approve this payment? This will deduct the amount
      from the customer's due.</p>
    <form action="{{ route('manager.payments.approve', $payment->id) }}" method="POST">
      @csrf
      <button type="submit" class="btn-smart btn-green">
        <i class="fa-solid fa-check-circle"></i> Approve Now
      </button>
    </form>
  </div>
  @endif
  @endif

  <div class="card-footer-actions">
    @if($isPayment)
    @if($payment->status == 'complete')
    <a href="{{ route('payments.slip', $payment->id) }}" class="btn-smart btn-blue">
      <i class="fa-solid fa-file-invoice"></i> View Slip
    </a>
    @endif
    @endif

  </div>
</div>

<div style="text-align:center; margin-top:20px;">
  <a href="{{ route('manager.payments.index') }}" style="color:var(--text-muted); text-decoration:none;">

    <i class="fas fa-arrow-left"></i> Back to List

  </a>
</div>

@endsection