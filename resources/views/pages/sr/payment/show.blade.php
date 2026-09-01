@extends('layouts.srlayout')

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
            ● Pending
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

    {{-- Dynamic Statement --}}
    <div class="statement">
      <p class="statement-text">
        <strong>Note / Remarks:</strong><br>
        {{ $payment->note ?? 'No additional notes provided.' }}
      </p>
    </div>



  </div>
  <div class="card-footer-actions">
    @if(($payment->status == 'pending' && auth()->user()->id == $payment->sr_id) )

    <a href="{{ route('sr.payments.edit', $payment->id) }}" class="icon-btn edit-icon">
      <i class="fa-solid fa-pen"></i>
    </a>


    <form action="{{ route('sr.payments.destroy', $payment->id) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this request?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="icon-btn delete-icon" style="border: none;">
        <i class="fa-solid fa-trash"></i>
      </button>
    </form>

    @endif

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
  <a href="{{ route('sr.payments.index') }}" style="color:var(--text-muted); text-decoration:none;">

    <i class="fas fa-arrow-left"></i> Back to List

  </a>
</div>

@endsection