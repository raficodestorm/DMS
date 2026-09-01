@extends('layouts.customerlayout')

@section('content')

@php
$isPayment = $payment->type == 'pay';
$isPurchase = $payment->type == 'buy';
$isReturn = $payment->type == 'return';

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

      @if($isPayment)
      <div class="info-group">
        <span class="i-label">Payment Method</span>
        <span class="i-value">
          <strong>{{ ucfirst($payment->payment_method) }}</strong>
        </span>
      </div>
      @endif

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
            {{ number_format($payment->due_before_transaction, 2) }} TK
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
            {{ number_format($payment->due_after_transaction, 2) }} TK
          </span>

        </div>
      </div>

    </div>

    {{-- Dynamic Statement --}}

<div class="statement">
  <p class="statement-text">

@if($isPayment)

  @if($payment->status == 'pending')

    <strong style="color:#f59e0b;">Pending Notice:</strong>
    Your payment request has been received successfully and is under verification.
    Once approved by branch management, your due balance will be updated automatically.

  @elseif($payment->status == 'complete')

    <strong style="color:#16a34a;">Payment Confirmed:</strong>
    Thank you for your payment. Your transaction has been verified successfully.
    Your latest due balance has been updated in your account.

  @else

    <strong>Transaction Update:</strong>
    Please review the current payment status shown above.

  @endif

@elseif($isReturn)

  @if($payment->status == 'pending')

    <strong style="color:#f59e0b;">Return Pending:</strong>
    Your return request has been received successfully and is currently under verification.
    Once approved by branch management, the returned amount will be adjusted against your outstanding due balance.

  @elseif($payment->status == 'complete')

    <strong style="color:#16a34a;">Return Confirmed:</strong>
    Your return has been approved successfully.
    The returned amount has been adjusted against your outstanding due balance.

  @else

    <strong>Return Update:</strong>
    Please review the current return status shown above.

  @endif

@elseif($isPurchase)

  @if($payment->status == 'pending')

    <strong style="color:#f59e0b;">Purchase Pending:</strong>
    Your purchase transaction has been created successfully and is awaiting confirmation.

  @elseif($payment->status == 'complete')

    <strong style="color:#16a34a;">Purchase Confirmed:</strong>
    Your purchase has been processed successfully.
    The amount has been added to your outstanding balance.

  @else

    <strong>Purchase Update:</strong>
    Please review the latest purchase status shown above.

  @endif

@else

  <strong>Transaction Update:</strong>
  Please review the current transaction status shown above.

@endif

  </p>
</div>



  </div>

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
  <a href="{{ route('customer.payments.index') }}" style="color:var(--text-muted); text-decoration:none;">

    <i class="fas fa-arrow-left"></i> Back to List

  </a>
</div>

@endsection