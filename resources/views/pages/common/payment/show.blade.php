@extends('layouts.blank')

@section('content')
<style>
  .show-head {
    text-align: center;
    font-size: 1.5rem;
    color: var(--accent);
    margin-top: 50px;
  }

  .show-card {
    width: 100%;
    max-width: 550px;
    height: auto;
    margin: auto;
    background: var(--section-bg);
    border-radius: 30px;
    position: relative;
    z-index: 10;
    overflow: hidden;
    border: 1px solid var(--glass);
    box-shadow: 3px 20px 60px 0px rgba(255, 255, 255, 0.69);
    display: flex;
    flex-direction: column;
  }


  .content-area {
    flex: 1;
    padding: 35px 30px 20px;
    text-align: center;
  }

  .show-name {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
  }

  .rank-pill {
    display: inline-block;
    margin-top: 8px;
    padding: 4px 16px;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
  }

  .info-list {
    margin-top: 30px;
    text-align: left;
    background: var(--background);
    border-radius: 24px;
    padding: 15px;
    border: 1px solid var(--border-color);
  }

  .info-group {
    display: flex;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid var(--border-color);
  }

  .info-group:last-child {
    border: 0;
  }

  .i-label {
    color: var(--primary);
    font-size: 0.8rem;
  }

  .i-value {
    color: var(--text-main);
    font-size: 0.8rem;
  }

  .statement {
    width: 100%;
    margin-top: 25px;
    padding: 15px;
    background: var(--glass);
    border-radius: 18px;
    border-left: 4px solid var(--accent);
  }

  .statement-text {
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    white-space: normal;
    font-size: 0.8rem;
    color: var(--text-muted);
    font-style: italic;
  }

  .card-footer-actions {
    padding: 20px;
    text-align: center;
    background: var(--border-color);
    display: flex;
    justify-content: space-around;
    align-items: center;
  }

  .brand-text {
    color: #fff;
    font-weight: 900;
    font-size: 0.9rem;
    letter-spacing: 2px;
  }

  .back-btn:hover {
    background: var(--primary-light);
  }

  @media (max-width: 480px) {
    .show-card {
      max-width: 93%;
      border-radius: 25px;
    }

    .header-accent {
      height: 140px;
      width: 120%;
      left: -10%;
      top: -30px;
    }

    .content-area {
      padding: 32px 15px 15px;
    }

    .name {
      font-size: 1.5rem;
    }

    .info-list {
      padding: 15px;
    }
  }
</style>
@php
$isPayment = $payment->type == 'pay';
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

      <div class="info-group">
        <span class="i-label">
          {{ $isPayment ? 'Payment Date' : 'Purchase Date' }}
        </span>

        <span class="i-value">
          {{ $payment->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
        </span>
      </div>

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

    <div class="info-list">
      <div class="info-group">

        <span class="i-label">
          {{ $isPayment ? 'Due after payment' : 'Due after purchase' }}
        </span>

        <span class="i-value" style="color: var(--danger-color); font-weight:700;">

          {{ number_format($payment->due_after_transaction, 2) }} TK

        </span>

      </div>
    </div>

  </div>
  <div class="card-footer-actions">
    <a href="{{ route('home-page') }}" style="color:var(--text-muted); text-decoration:none;">

      {{ config('app.name') }} <i class="fas fa-arrow-right"></i>

    </a>

  </div>
</div>

@endsection