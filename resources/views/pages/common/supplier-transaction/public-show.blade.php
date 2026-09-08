@extends('layouts.blank')

@section('content')
<style>
  .show-head {
    text-align: center;
    font-size: 1.5rem;
    color: var(--accent, #3131ff);
    margin-top: 40px;
    font-weight: 700;
  }

  .show-card {
    width: 100%;
    max-width: 520px;
    height: auto;
    margin: 20px auto 40px;
    background: var(--section-bg, #ffffff);
    border-radius: 24px;
    position: relative;
    z-index: 10;
    overflow: hidden;
    border: 1px solid var(--border-color, #e2e8f0);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
  }

  .content-area {
    flex: 1;
    padding: 30px 25px 20px;
    text-align: center;
  }

  .rank-pill {
    display: inline-block;
    margin-top: 8px;
    padding: 6px 20px;
    background: var(--primary-soft, #eef2ff);
    color: var(--primary, #3131ff);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
  }

  .info-list {
    margin-top: 25px;
    text-align: left;
    background: #f8fafc;
    border-radius: 16px;
    padding: 16px;
    border: 1px solid #e2e8f0;
  }

  .info-group {
    display: flex;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid #edf2f7;
    font-size: 0.9rem;
  }

  .info-group:last-child {
    border: 0;
  }

  .i-label {
    color: #64748b;
    font-size: 0.85rem;
  }

  .i-value {
    color: #1e293b;
    font-weight: 600;
    text-align: right;
  }

  .statement {
    width: 100%;
    margin-top: 20px;
    padding: 14px;
    background: #f0fdf4;
    border-radius: 12px;
    border-left: 4px solid #16a34a;
    text-align: left;
  }

  .statement-text {
    font-size: 0.84rem;
    color: #166534;
    margin: 0;
  }

  .verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #16a34a;
    font-weight: 700;
    font-size: 0.9rem;
    margin-top: 10px;
  }
</style>

<p class="show-head">
  <i class="fas fa-shield-halved me-1"></i> Transaction Verification
</p>

<div class="show-card">
  <div class="content-area">
    <div>
      <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo" style="width: 180px; height: auto;">
    </div>
    
    <div class="verified-badge">
      <i class="fas fa-circle-check"></i> Official Supplier Payment
    </div>

    <div class="mt-2">
      <div class="rank-pill">
        BRST00{{ $transaction->id }}
      </div>
    </div>

    <div class="info-list">
      <div class="info-group">
        <span class="i-label">Supplier Company</span>
        <span class="i-value">{{ $transaction->supplier->company_name ?? 'N/A' }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Contact Person</span>
        <span class="i-value">{{ $transaction->supplier->name ?? 'N/A' }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Amount Paid</span>
        <span class="i-value" style="color: var(--primary, #3131ff); font-size: 1.05rem; font-weight: 800;">
          ৳ {{ number_format($transaction->amount, 2) }}
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Payment Method</span>
        <span class="i-value" style="text-transform: capitalize;">{{ $transaction->payment_method ?? 'Cash' }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Reference Branch</span>
        <span class="i-value">{{ $transaction->branch->name ?? 'Main / Head Office' }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Payment Date</span>
        <span class="i-value">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Due Before Payment</span>
        <span class="i-value">৳ {{ number_format($transaction->due_before_transaction, 2) }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Due After Payment</span>
        <span class="i-value" style="color: #16a34a;">৳ {{ number_format($transaction->due_after_transaction, 2) }}</span>
      </div>
    </div>

    <div class="statement">
      <p class="statement-text">
        <strong>Payment Confirmed:</strong> This voucher is digitally verified and logged in the {{ config('app.name') }} central ledger system.
      </p>
    </div>
  </div>
</div>
@endsection
