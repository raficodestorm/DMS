@extends('layouts.managerlayout')

@section('content')

@php
  $isPayment = $transaction->type == 'pay';
  $isPurchase = $transaction->type == 'buy';
  $isReturn = $transaction->type == 'return';
  $isOpening = $transaction->type == 'opening_balance';

  $stockCutId = $transaction->stock_cut_id;
  if (!$stockCutId && preg_match('/BRSKR(\d+)/', $transaction->note ?? '', $m)) {
    $stockCutId = $m[1];
  }
@endphp

<p class="show-head">
  @if($isPayment)
    Supplier Payment Details
  @elseif($isReturn)
    Supplier Return Details
  @elseif($isPurchase)
    Supplier Purchase Details
  @else
    Supplier Transaction Details
  @endif
</p>

<div class="show-card">
  <div class="content-area">
    <p style="color: gray; font-style: italic; text-align: center; margin-bottom: 12px;">
      This transaction is associated with {{ $transaction->branch->name ?? 'your' }} branch
    </p>

    <div class="rank-pill">
      BRST00{{ $transaction->id }}
    </div>

    <div class="info-list">

      <div class="info-group">
        <span class="i-label">Supplier Company</span>
        <span class="i-value">
          <strong>{{ $transaction->supplier->company_name ?? 'N/A' }}</strong>
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Contact Person</span>
        <span class="i-value">
          {{ $transaction->supplier->contact_person ?? 'N/A' }}
          @if($transaction->supplier?->phone)
            <small class="text-muted">({{ $transaction->supplier->phone }})</small>
          @endif
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Transaction Type</span>
        <span class="i-value">
          @if($isPayment)
            <span style="color:#15803d; font-weight:700; background:#dcfce7; padding:2px 10px; border-radius:12px; font-size:0.85rem;">Payment</span>
          @elseif($isReturn)
            <span style="color:#b91c1c; font-weight:700; background:#fee2e2; padding:2px 10px; border-radius:12px; font-size:0.85rem;">Return</span>
          @elseif($isPurchase)
            <span style="color:#7c3aed; font-weight:700; background:#f3e8ff; padding:2px 10px; border-radius:12px; font-size:0.85rem;">Purchase</span>
          @else
            <span style="color:#0369a1; font-weight:700; background:#e0f2fe; padding:2px 10px; border-radius:12px; font-size:0.85rem;">{{ ucfirst($transaction->type) }}</span>
          @endif
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Transaction Amount</span>
        <span class="i-value" style="font-size:1.1rem; color: var(--primary);">
          <strong>{{ number_format($transaction->amount, 2) }} TK</strong>
        </span>
      </div>

      @if($transaction->payment_method)
      <div class="info-group">
        <span class="i-label">Payment Method</span>
        <span class="i-value" style="text-transform: capitalize;">
          <strong>{{ $transaction->payment_method }}</strong>
        </span>
      </div>
      @endif

      <div class="info-group">
        <span class="i-label">Transaction Date</span>
        <span class="i-value">
          {{ $transaction->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
        </span>
      </div>

    </div>

    {{-- Note --}}
    <div class="statement">
      <p class="statement-text">
        <strong>Note / Remarks:</strong><br>
        {{ $transaction->note ?? 'No additional notes provided.' }}
      </p>
    </div>

    {{-- Dynamic Ledger Statement --}}
    <div class="statement">
      <p class="statement-text">
        @if($isPayment)
          <strong style="color:#16a34a;">Supplier Payment Record:</strong>
          A payment of <strong>{{ number_format($transaction->amount, 2) }} TK</strong> was disbursed to <strong>{{ $transaction->supplier->company_name ?? 'Supplier' }}</strong>.
        @elseif($isReturn)
          <strong style="color:#b91c1c;">Stock Return Adjustment:</strong>
          Stock return worth <strong>{{ number_format($transaction->amount, 2) }} TK</strong> was deducted for <strong>{{ $transaction->supplier->company_name ?? 'Supplier' }}</strong>.
        @elseif($isPurchase)
          <strong style="color:#7c3aed;">Stock Purchase Entry:</strong>
          Stock in worth <strong>{{ number_format($transaction->amount, 2) }} TK</strong> was received from <strong>{{ $transaction->supplier->company_name ?? 'Supplier' }}</strong>.
        @else
          <strong>Ledger Entry:</strong>
          Supplier account ledger updated by {{ number_format($transaction->amount, 2) }} TK.
        @endif
      </p>
    </div>

  </div>

  <div class="card-footer-actions d-flex justify-content-end gap-2 p-3">
    @if($isPayment)
      <a href="{{ route('supplier-transactions.slip', $transaction->id) }}" class="btn-smart btn-blue">
        <i class="fa-solid fa-file-invoice"></i> View Slip
      </a>
    @endif

    @if($isPurchase && $transaction->stock_in_request_id)
      <a href="{{ route('manager.stock.in.request.show', $transaction->stock_in_request_id) }}" class="btn-smart btn-purple">
        <i class="fa-solid fa-boxes-stacked"></i> View Stock-In Request (BRSK{{ $transaction->stock_in_request_id }})
      </a>
    @endif

    @if($isReturn && $stockCutId)
      <a href="{{ route('manager.stock.cut.show', $stockCutId) }}" class="btn-smart btn-red">
        <i class="fa-solid fa-arrow-rotate-left"></i> View Stock Return (BRSKR{{ $stockCutId }})
      </a>
    @endif
  </div>

</div>

<div style="text-align:center; margin-top:20px;">
  <a href="{{ route('manager.supplier-transactions.index') }}" style="color:var(--text-muted); text-decoration:none;">
    <i class="fas fa-arrow-left"></i> Back to Transactions
  </a>
</div>

@endsection
