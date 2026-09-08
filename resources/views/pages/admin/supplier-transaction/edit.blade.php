@extends('layouts.adminlayout')

@section('content')
<style>
    .payment-form-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        max-width: 820px;
        margin: 0 auto;
    }
    .form-head-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--text-main, #1e293b);
        margin-bottom: 6px;
    }
    .form-head-sub {
        color: var(--text-muted, #64748b);
        font-size: 0.88rem;
        margin-bottom: 24px;
    }
    .form-group-item {
        margin-bottom: 20px;
    }
    .form-group-item label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--text-main, #1e293b);
        margin-bottom: 7px;
    }
    .req-star {
        color: #ef4444;
        font-weight: 700;
        font-size: 1rem;
        line-height: 1;
        display: inline;
    }
    .ref-hint {
        font-size: 0.74rem;
        font-weight: 500;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .supplier-static-chip {
        background: #f8faff;
        border: 2px solid var(--primary, #3131ff);
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .sls-item-due {
        font-size: 12px;
        font-weight: 700;
        color: #dc2626;
        background: rgba(220, 38, 38, 0.08);
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    /* Due Live Calculator Preview */
    .due-preview-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 10px;
    }
    .due-preview-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        text-align: center;
    }
    .dp-item label {
        display: block !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        color: var(--text-muted, #64748b) !important;
        text-transform: uppercase;
        margin-bottom: 2px !important;
    }
    .dp-item p {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }
    .dp-cur-due { color: #dc2626; }
    .dp-pay-amt { color: var(--primary, #3131ff); }
    .dp-rem-due.ok { color: #16a34a; }
    .dp-rem-due.bad { color: #ea580c; }

    @media (max-width: 575px) {
        .due-preview-grid { grid-template-columns: 1fr; gap: 8px; text-align: left; }
        .payment-form-card { padding: 18px; }
    }
</style>

<div class="container justify-center">
  <div class="payment-form-card">
    <div class="form-head-title">
      <i class="fas fa-pen-to-square" style="color: var(--primary, #3131ff);"></i>
      <h2>Edit Supplier Payment (BRST00{{ $transaction->id }})</h2>
    </div>
    <p class="form-head-sub">
      Modify supplier payment details and automatically re-calculate ledger balance.
    </p>

    {{-- Alert Messages --}}
    @include('components.alert')

    <form method="POST" action="{{ route('admin.supplier-transactions.update', $transaction->id) }}">
      @csrf
      @method('PUT')

      {{-- ── 1. Supplier Display (Read-only) ── --}}
      <div class="form-group-item">
        <label>
          <span>Supplier Company</span>
          <span class="ref-hint"><i class="fas fa-lock"></i> Locked</span>
        </label>

        <div class="supplier-static-chip">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-building text-primary" style="font-size: 1.1rem;"></i>
                <div>
                    <strong style="font-size: 1rem; color: var(--text-main, #1e293b);">
                      {{ $transaction->supplier->company_name ?? 'N/A' }}
                    </strong>
                    @if($transaction->supplier?->name)
                      <span class="text-muted ms-2 small">({{ $transaction->supplier->name }})</span>
                    @endif
                </div>
            </div>
            <span class="sls-item-due">
              Initial Due: ৳ {{ number_format($transaction->due_before_transaction, 2) }}
            </span>
        </div>
      </div>

      {{-- ── 2. Payment Amount & Method ── --}}
      <div class="row">
        <div class="col-md-6 form-group-item">
          <label>
            <span>Payment Amount (TK)</span>
            <span class="req-star">*</span>
          </label>
          <input type="number" step="0.01" class="input-form" name="amount" id="amount-input"
            placeholder="0.00" required autocomplete="off"
            value="{{ old('amount', $transaction->amount) }}" style="font-weight: 700; font-size: 1.05rem;">
          @error('amount')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 form-group-item">
          <label>
            <span>Payment Method</span>
            <span class="req-star">*</span>
          </label>
          <select class="input-form" name="payment_method" required>
            <option value="">-- Select Payment Method --</option>
            <option value="cash" {{ old('payment_method', $transaction->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="bank" {{ old('payment_method', $transaction->payment_method) == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="cheque" {{ old('payment_method', $transaction->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
            <option value="bkash" {{ old('payment_method', $transaction->payment_method) == 'bkash' ? 'selected' : '' }}>bKash / Mobile Banking</option>
          </select>
          @error('payment_method')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- ── Live Due Calculation Breakdown ── --}}
      <div class="due-preview-box mb-3" id="due-preview-box">
        <div class="due-preview-grid">
          <div class="dp-item">
            <label>Due Before Payment</label>
            <p class="dp-cur-due" id="dp-cur-due">৳ {{ number_format($transaction->due_before_transaction, 2) }}</p>
          </div>
          <div class="dp-item">
            <label>New Payment Amount</label>
            <p class="dp-pay-amt" id="dp-pay-amt">৳ {{ number_format($transaction->amount, 2) }}</p>
          </div>
          <div class="dp-item">
            <label id="dp-rem-label">New Remaining Due</label>
            <p class="dp-rem-due ok" id="dp-rem-due">৳ {{ number_format($transaction->due_after_transaction, 2) }}</p>
          </div>
        </div>
      </div>

      {{-- ── 3. Branch & Reference Notes ── --}}
      <div class="row">
        <div class="col-md-6 form-group-item">
          <label>
            <span>Branch / Reference</span>
            <span class="ref-hint"><i class="fas fa-info-circle"></i> Reference only</span>
          </label>
          <select class="input-form" name="branch_id">
            <option value="">-- Select branch as reference --</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" {{ old('branch_id', $transaction->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
          </select>
          @error('branch_id')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 form-group-item">
          <label>
            <span>Note / Remarks</span>
            <small style="color: var(--text-muted); font-weight: normal;">(Optional)</small>
          </label>
          <input type="text" class="input-form" name="note"
            placeholder="Cheque no, transaction ID, bank note..." value="{{ old('note', $transaction->note) }}">
          @error('note')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- ── 4. Notice Banner ── --}}
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid var(--primary, #3131ff); padding: 12px 16px; border-radius: 8px; margin-bottom: 22px;">
        <small style="color: #475569; display: flex; align-items: center; gap: 8px; font-size: 0.83rem;">
          <i class="fa-solid fa-circle-check text-primary" style="font-size: 1rem;"></i>
          <span>Updating this record will recalculate the <strong>Supplier Ledger</strong> and adjust the payable balance.</span>
        </small>
      </div>

      {{-- ── 5. Action Buttons ── --}}
      <div class="d-flex gap-2">
        <button class="btn-submit" type="submit" style="flex: 1; height: 46px; font-size: 0.95rem;">
          <i class="fa-solid fa-check me-1"></i> Update Supplier Payment
        </button>
        <a href="{{ route('admin.supplier-transactions.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="padding: 0 20px; border-radius: 8px; font-weight: 600;">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

{{-- Back Link --}}
<div class="container justify-center" style="margin-top: 16px; margin-bottom: 30px;">
  <a href="{{ route('admin.supplier-transactions.index') }}" style="text-decoration: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem;">
    <i class="fas fa-arrow-left me-1"></i> Back to Supplier Ledger
  </a>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var baseDue       = {{ (float) $transaction->due_before_transaction }};
    var amountInput   = document.getElementById('amount-input');
    var dpPayAmt      = document.getElementById('dp-pay-amt');
    var dpRemDue      = document.getElementById('dp-rem-due');
    var dpRemLabel    = document.getElementById('dp-rem-label');

    function updatePreview() {
        var amount = parseFloat(amountInput.value) || 0;
        dpPayAmt.textContent = '৳ ' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        var remaining = baseDue - amount;

        if (remaining < 0) {
            dpRemLabel.textContent = 'Advance / Overpayment';
            dpRemDue.className = 'dp-rem-due bad';
            dpRemDue.textContent = '৳ ' + Math.abs(remaining).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (Advance)';
        } else {
            dpRemLabel.textContent = 'New Remaining Due';
            dpRemDue.className = 'dp-rem-due ok';
            dpRemDue.textContent = '৳ ' + remaining.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    if (amountInput) {
        amountInput.addEventListener('input', updatePreview);
    }
});
</script>
@endpush
