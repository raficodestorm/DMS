@extends('layouts.adminlayout')

@section('content')
<style>
    /* ── Form Styling ─────────────────────────────────────────────────────── */
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

    /* ── Supplier Live Search (SLS) ──────────────────────────────────────── */
    .sls-container { position: relative; }
    .sls-input-wrap {
        display: flex; align-items: center;
        background: var(--section-bg, #fff);
        border: 2px solid var(--border-color, #cbd5e1);
        border-radius: 10px; padding: 0 14px;
        transition: border-color .2s, box-shadow .2s;
    }
    .sls-input-wrap:focus-within {
        border-color: var(--primary, #3131ff);
        box-shadow: 0 0 0 3px rgba(49,49,255,.12);
    }
    .sls-search-icon { color: var(--text-muted, #64748b); margin-right: 10px; font-size: 15px; flex-shrink: 0; }
    .sls-input {
        flex: 1; min-width: 0; border: none; outline: none; background: transparent;
        padding: 12px 0; font-size: 14px; color: var(--text-main, #1e293b);
    }
    .sls-input::placeholder { color: var(--text-muted, #94a3b8); font-size: 13px; }
    .sls-dropdown {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: var(--section-bg, #fff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        box-shadow: 0 10px 28px rgba(0,0,0,.12);
        max-height: 260px; overflow-y: auto; z-index: 9999;
    }
    .sls-item {
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; flex-wrap: wrap;
        padding: 11px 15px; cursor: pointer;
        border-bottom: 1px solid var(--border-color, #f1f5f9);
        transition: background .15s;
    }
    .sls-item:last-child { border-bottom: none; }
    .sls-item:hover, .sls-item:active { background: var(--primary-soft, #eef2ff); }
    .sls-item-company { font-weight: 700; font-size: 14px; color: var(--text-main, #1e293b); }
    .sls-item-contact { font-size: 12px; color: var(--text-muted, #64748b); margin-top: 2px; }
    .sls-item-due {
        font-size: 12px; font-weight: 700; color: #dc2626;
        background: rgba(220, 38, 38, 0.08); padding: 4px 10px;
        border-radius: 20px; white-space: nowrap; flex-shrink: 0;
    }
    .sls-empty { padding: 16px; text-align: center; color: var(--text-muted, #64748b); font-size: 14px; }
    .sls-selected-box {
        background: #f8faff;
        border: 2px solid var(--primary, #3131ff);
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .sls-selected-info {
        display: flex; align-items: center; flex-wrap: wrap;
        gap: 10px; flex: 1; min-width: 0;
    }
    .sls-selected-info strong {
        font-size: 0.95rem;
        color: var(--text-main, #1e293b);
    }

    /* ── Due Live Calculator Preview ─────────────────────────────────────── */
    .due-preview-box {
        display: none;
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
      <i class="fas fa-file-invoice-dollar" style="color: var(--primary, #3131ff);"></i>
      <h2>New Supplier Payment</h2>
    </div>
    <p class="form-head-sub">
      Record supplier payment disbursement and update ledger balance automatically.
    </p>

    {{-- Alert Messages --}}
    @include('components.alert')

    <form method="POST" action="{{ route('admin.supplier-transactions.store') }}">
      @csrf

      {{-- ── 1. Supplier Live Search ── --}}
      <div class="form-group-item">
        <label>
          <span>Select Supplier</span>
          <span class="req-star">*</span>
        </label>
        <input type="hidden" name="supplier_id" id="selected_supplier_id" value="{{ old('supplier_id') }}" required>

        {{-- Selected Supplier Chip Box --}}
        <div id="sls-selected-box" class="sls-selected-box" style="display: none;">
            <div class="sls-selected-info">
                <i class="fas fa-building text-primary" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                <strong id="sls-selected-company"></strong>
                <span class="sls-item-due" id="sls-selected-due"></span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" id="sls-clear-btn" style="flex-shrink:0; font-weight:600; padding: 4px 10px;">
                <i class="fas fa-times me-1"></i> Change
            </button>
        </div>

        {{-- Search Input & Dropdown --}}
        <div class="sls-container" id="sls-container">
            <div class="sls-input-wrap">
                <span class="sls-search-icon"><i class="fas fa-search"></i></span>
                <input
                    type="text"
                    id="sls-input"
                    class="sls-input"
                    placeholder="Type supplier / company name (or double space for all with due)..."
                    autocomplete="off"
                    inputmode="search">
            </div>
            <div id="sls-dropdown" class="sls-dropdown" style="display:none"></div>
        </div>
        @error('supplier_id')<div class="error-text mt-1 text-danger small font-weight-bold">{{ $message }}</div>@enderror
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
            value="{{ old('amount') }}" style="font-weight: 700; font-size: 1.05rem;">
          @error('amount')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 form-group-item">
          <label>
            <span>Payment Method</span>
            <span class="req-star">*</span>
          </label>
          <select class="input-form" name="payment_method" required>
            <option value="">-- Select Payment Method --</option>
            <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
            <option value="bkash" {{ old('payment_method') == 'bkash' ? 'selected' : '' }}>bKash / Mobile Banking</option>
          </select>
          @error('payment_method')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- ── Live Due Calculation Breakdown ── --}}
      <div class="due-preview-box mb-3" id="due-preview-box">
        <div class="due-preview-grid">
          <div class="dp-item">
            <label>Current Due</label>
            <p class="dp-cur-due" id="dp-cur-due">৳ 0.00</p>
          </div>
          <div class="dp-item">
            <label>Payment Amount</label>
            <p class="dp-pay-amt" id="dp-pay-amt">৳ 0.00</p>
          </div>
          <div class="dp-item">
            <label id="dp-rem-label">Remaining Due</label>
            <p class="dp-rem-due ok" id="dp-rem-due">৳ 0.00</p>
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
              <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
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
            placeholder="Cheque no, transaction ID, bank note..." value="{{ old('note') }}">
          @error('note')<div class="error-text text-danger small font-weight-bold">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- ── 4. Notice Banner ── --}}
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid var(--primary, #3131ff); padding: 12px 16px; border-radius: 8px; margin-bottom: 22px;">
        <small style="color: #475569; display: flex; align-items: center; gap: 8px; font-size: 0.83rem;">
          <i class="fa-solid fa-circle-check text-primary" style="font-size: 1rem;"></i>
          <span>Submitting will record this payment in <strong>Supplier Ledger</strong> and automatically deduct the payable balance.</span>
        </small>
      </div>

      {{-- ── 5. Action Button ── --}}
      <div>
        <button class="btn-submit" type="submit" style="width: 100%; height: 46px; font-size: 0.95rem;">
          <i class="fa-solid fa-paper-plane me-1"></i> Record Supplier Payment
        </button>
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

@php
    $suppliersJson = $suppliers->map(function($s) {
        return [
            'id' => $s->id,
            'company_name' => $s->company_name ?? $s->name ?? 'N/A',
            'contact_person' => $s->contact_person ?? $s->name ?? '',
            'due' => (float) ($s->due ?? 0),
        ];
    })->values();
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var allSuppliers = {!! json_encode($suppliersJson) !!};

    var slsInput           = document.getElementById('sls-input');
    var slsDropdown        = document.getElementById('sls-dropdown');
    var slsClearBtn        = document.getElementById('sls-clear-btn');
    var selectedSupplierId = document.getElementById('selected_supplier_id');
    var slsContainer       = document.getElementById('sls-container');
    var slsSelectedBox     = document.getElementById('sls-selected-box');
    var slsSelectedCompany = document.getElementById('sls-selected-company');
    var slsSelectedDue     = document.getElementById('sls-selected-due');
    var amountInput        = document.getElementById('amount-input');
    var duePreviewBox      = document.getElementById('due-preview-box');
    var dpCurDue           = document.getElementById('dp-cur-due');
    var dpPayAmt           = document.getElementById('dp-pay-amt');
    var dpRemDue           = document.getElementById('dp-rem-due');
    var dpRemLabel         = document.getElementById('dp-rem-label');

    var currentSelectedDue = 0;

    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function updateDuePreview() {
        if (!selectedSupplierId.value) {
            duePreviewBox.style.display = 'none';
            return;
        }
        var amount = parseFloat(amountInput.value) || 0;
        dpCurDue.textContent = '৳ ' + currentSelectedDue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        dpPayAmt.textContent = '৳ ' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        var remaining = currentSelectedDue - amount;
        duePreviewBox.style.display = 'block';

        if (remaining < 0) {
            dpRemLabel.textContent = 'Advance / Overpayment';
            dpRemDue.className = 'dp-rem-due bad';
            dpRemDue.textContent = '৳ ' + Math.abs(remaining).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (Advance)';
        } else {
            dpRemLabel.textContent = 'Remaining Due';
            dpRemDue.className = 'dp-rem-due ok';
            dpRemDue.textContent = '৳ ' + remaining.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    function selectSupplier(s) {
        selectedSupplierId.value = s.id;
        currentSelectedDue = parseFloat(s.due) || 0;
        slsSelectedCompany.textContent = s.company_name + (s.contact_person ? ' (' + s.contact_person + ')' : '');
        slsSelectedDue.textContent = 'Due: ৳ ' + currentSelectedDue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        slsContainer.style.display = 'none';
        slsSelectedBox.style.display = 'flex';
        slsDropdown.style.display = 'none';
        slsInput.value = '';

        updateDuePreview();
    }

    function clearSupplier() {
        selectedSupplierId.value = '';
        currentSelectedDue = 0;
        slsSelectedBox.style.display = 'none';
        slsContainer.style.display = 'block';
        duePreviewBox.style.display = 'none';
        slsInput.value = '';
        slsInput.focus();
    }

    function filterSuppliers(query) {
        var q = query.trim().toLowerCase();
        if (query === '  ') {
            return allSuppliers.filter(function(s) { return s.due > 0; });
        }
        if (!q) return [];
        return allSuppliers.filter(function (s) {
            return (s.company_name && s.company_name.toLowerCase().includes(q)) ||
                   (s.contact_person && s.contact_person.toLowerCase().includes(q));
        });
    }

    function renderDropdown(items) {
        if (!items.length) {
            slsDropdown.innerHTML = '<div class="sls-empty"><i class="fas fa-search me-1"></i> No suppliers found</div>';
            slsDropdown.style.display = 'block';
            return;
        }
        var html = '';
        items.forEach(function (s) {
            var dueVal = parseFloat(s.due) || 0;
            var dueHtml = '<span class="sls-item-due">Due: ৳ ' + dueVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
            var contactHtml = s.contact_person ? '<div class="sls-item-contact"><i class="fas fa-user me-1"></i>' + esc(s.contact_person) + '</div>' : '';

            html += '<div class="sls-item" data-id="' + s.id + '">' +
                        '<div>' +
                            '<div class="sls-item-company">' + esc(s.company_name) + '</div>' +
                            contactHtml +
                        '</div>' +
                        dueHtml +
                    '</div>';
        });
        slsDropdown.innerHTML = html;
        slsDropdown.style.display = 'block';
    }

    if (slsInput) {
        slsInput.addEventListener('input', function () {
            var results = filterSuppliers(this.value);
            if (results.length || this.value.length >= 1) {
                renderDropdown(results);
            } else {
                slsDropdown.style.display = 'none';
            }
        });

        slsInput.addEventListener('focus', function () {
            if (this.value) {
                renderDropdown(filterSuppliers(this.value));
            }
        });
    }

    if (slsDropdown) {
        slsDropdown.addEventListener('click', function (e) {
            var item = e.target.closest('.sls-item');
            if (!item) return;
            var id = item.getAttribute('data-id');
            var found = allSuppliers.find(function (s) { return String(s.id) === String(id); });
            if (found) selectSupplier(found);
        });
    }

    if (slsClearBtn) {
        slsClearBtn.addEventListener('click', clearSupplier);
    }

    if (amountInput) {
        amountInput.addEventListener('input', updateDuePreview);
    }

    document.addEventListener('click', function (e) {
        if (slsContainer && !slsContainer.contains(e.target)) {
            if (slsDropdown) slsDropdown.style.display = 'none';
        }
    });

    // Check old value on validation error
    var oldSupplierId = selectedSupplierId ? selectedSupplierId.value : null;
    if (oldSupplierId) {
        var preselected = allSuppliers.find(function (s) { return String(s.id) === String(oldSupplierId); });
        if (preselected) selectSupplier(preselected);
    }
});
</script>
@endpush
