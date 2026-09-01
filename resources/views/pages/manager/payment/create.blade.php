@extends('layouts.managerlayout')

@section('content')
<style>
    /* ── Customer Live Search (CLS) ──────────────────────────────────────── */
    .cls-container { position: relative; }
    .cls-input-wrap {
        display: flex; align-items: center;
        background: var(--section-bg, #fff);
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 10px; padding: 0 14px;
        transition: border-color .2s, box-shadow .2s;
    }
    .cls-input-wrap:focus-within {
        border-color: var(--primary, #3131ff);
        box-shadow: 0 0 0 3px rgba(49,49,255,.12);
    }
    .cls-search-icon { color: var(--text-muted, #64748b); margin-right: 10px; font-size: 15px; flex-shrink: 0; }
    .cls-input {
        flex: 1; min-width: 0; border: none; outline: none; background: transparent;
        padding: 12px 0; font-size: 15px; color: var(--text-main, #1e293b);
    }
    .cls-input::placeholder { color: var(--text-muted, #94a3b8); font-size: 13px; }
    .cls-dropdown {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: var(--section-bg, #fff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.13);
        max-height: 260px; overflow-y: auto; z-index: 9999;
    }
    .cls-item {
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; flex-wrap: wrap;
        padding: 10px 14px; cursor: pointer;
        border-bottom: 1px solid var(--border-color, #f1f5f9);
        transition: background .15s;
    }
    .cls-item:last-child { border-bottom: none; }
    .cls-item:hover, .cls-item:active { background: var(--primary-soft, #eef2ff); }
    .cls-item-shop { font-weight: 700; font-size: 14px; color: var(--text-main, #1e293b); }
    .cls-item-due {
        font-size: 12px; font-weight: 700; color: #dc2626;
        background: rgba(220, 38, 38, 0.08); padding: 3px 8px;
        border-radius: 20px; white-space: nowrap; flex-shrink: 0;
    }
    .cls-empty { padding: 16px; text-align: center; color: var(--text-muted, #64748b); font-size: 14px; }
    .cls-selected-box {
        background: var(--section-bg, #fff);
        border: 2px solid var(--primary, #3131ff);
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .cls-selected-info {
        display: flex; align-items: center; flex-wrap: wrap;
        gap: 8px; flex: 1; min-width: 0;
    }
    .cls-selected-info strong {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    @media (max-width: 575px) {
        .cls-input { font-size: 14px; }
    }

    /* Due Preview */
    .due-preview {
        display: none;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        padding: 9px 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        background: rgba(49, 49, 255, 0.05);
        border: 1px dashed var(--primary, #3131ff);
        flex-wrap: wrap;
    }
    .due-preview.danger {
        background: rgba(220, 38, 38, 0.06);
        border-color: #dc2626;
    }
    .due-preview .dp-label { color: var(--text-muted, #64748b); }
    .due-preview .dp-val { font-size: 16px; font-weight: 800; }
    .due-preview .dp-val.ok  { color: #16a34a; }
    .due-preview .dp-val.bad { color: #dc2626; }
</style>

<div class="container justify-center">
  <div class="form-card">
    <h2>New Payment Entry</h2>
    <p style="color: #666; margin-bottom: 20px; font-size: 0.9rem;">
      Record customer payment collection and update due balance automatically.
    </p>

    {{-- Success/Error Alert Component --}}
    @include('components.alert')

    <form class="adduser-form" method="POST" action="{{ route('manager.payments.store') }}">
      @csrf

      <div class="row">
      {{-- Smart Customer Live Search --}}
      <div class="col-md-12 mb-3">
        <label>Select Customer / Shop</label>
        <input type="hidden" name="customer_id" id="selected_customer_id" value="{{ old('customer_id') }}" required>

        {{-- Selected Customer Box --}}
        <div id="cls-selected-box" class="cls-selected-box" style="display: none;">
            <div class="cls-selected-info">
                <i class="fas fa-store text-primary" style="flex-shrink:0;"></i>
                <strong id="cls-selected-shop" class="fs-6 text-dark"></strong>
                <span class="cls-item-due" id="cls-selected-due"></span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" id="cls-clear-btn" style="flex-shrink:0;">
                <i class="fas fa-times me-1"></i> Change
            </button>
        </div>

        {{-- Search Input & Dropdown --}}
        <div class="cls-container" id="cls-container">
            <div class="cls-input-wrap">
                <span class="cls-search-icon"><i class="fas fa-search"></i></span>
                <input
                    type="text"
                    id="cls-input"
                    class="cls-input"
                    placeholder="Type shop name (or 2 spaces for all customers with due)..."
                    autocomplete="off"
                    inputmode="search">
            </div>
            <div id="cls-dropdown" class="cls-dropdown" style="display:none"></div>
        </div>
        @error('customer_id')<div class="error-text mt-1">{{ $message }}</div>@enderror
      </div>

      {{-- Payment Amount --}}
      <div class="col-md-6 mb-3">
        <label>Collection Amount (TK)</label>
        <input type="number" step="0.01" class="input-form" name="amount" id="amount-input"
          placeholder="Enter collected amount" required autocomplete="off"
          value="{{ old('amount') }}">
        @error('amount')<div class="error-text">{{ $message }}</div>@enderror

        {{-- Remaining Due Preview --}}
        <div class="due-preview" id="due-preview">
            <span class="dp-label"><i class="fas fa-calculator me-1"></i> Remaining Due After Collection:</span>
            <span class="dp-val" id="dp-val"></span>
        </div>
      </div>

      {{-- Payment Method --}}
      <div class="col-md-6 mb-3">
        <label>Select Payment Method</label>
        <select class="input-form" name="payment_method" required>
          <option value="">--Select Payment Method--</option>
          <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
          <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank</option>
          <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
        </select>
        @error('payment_method')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      {{-- Note / Remarks --}}
      <div class="col-md-12 mb-3">
        <label>Note / Remarks (Optional)</label>
        <textarea class="input-form" name="note" rows="3"
          placeholder="Any specific info about this payment...">{{ old('note') }}</textarea>
        @error('note')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      </div> <!-- end row -->

      {{-- Info Box for Manager --}}
      <div
        style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #3131ff;">
        <small style="color: #555;">
          <i class="fa-solid fa-circle-info"></i>
          After submitting, this payment will be <strong>completed</strong> automatically & customer due will be updated.
        </small>
      </div>

      <div>
        <button class="btn-submit" type="submit">
          <i class="fa-solid fa-paper-plane"></i> Record Payment
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Back Button --}}
<div class="container justify-center" style="margin-top: 15px;">
  <a href="{{ route('manager.payments.index') }}" style="text-decoration: none; color: #666;">
    ← Back to Payment List
  </a>
</div>

@endsection

@php
    $customersJson = $customers->map(function($c) {
        return [
            'id' => $c->id,
            'shop_name' => $c->shop_name ?? 'N/A',
            'due' => (float) $c->due,
        ];
    })->values();
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var allCustomers = {!! json_encode($customersJson) !!};

    var clsInput           = document.getElementById('cls-input');
    var clsDropdown        = document.getElementById('cls-dropdown');
    var clsClearBtn        = document.getElementById('cls-clear-btn');
    var selectedCustomerId = document.getElementById('selected_customer_id');
    var clsContainer       = document.getElementById('cls-container');
    var clsSelectedBox     = document.getElementById('cls-selected-box');
    var clsSelectedShop    = document.getElementById('cls-selected-shop');
    var clsSelectedDue     = document.getElementById('cls-selected-due');
    var amountInput        = document.getElementById('amount-input');
    var duePreview         = document.getElementById('due-preview');
    var dpVal              = document.getElementById('dp-val');

    var currentSelectedDue = 0;

    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function updateDuePreview() {
        if (!currentSelectedDue || !amountInput) return;
        var amount = parseFloat(amountInput.value) || 0;
        if (amount <= 0) { duePreview.style.display = 'none'; return; }
        var remaining = currentSelectedDue - amount;
        duePreview.style.display = 'flex';
        if (remaining < 0) {
            duePreview.classList.add('danger');
            dpVal.className = 'dp-val bad';
            dpVal.textContent = '৳ ' + Math.abs(remaining).toFixed(2) + ' Exceeds Due!';
        } else {
            duePreview.classList.remove('danger');
            dpVal.className = 'dp-val ok';
            dpVal.textContent = '৳ ' + remaining.toFixed(2);
        }
    }

    function selectCustomer(cust) {
        selectedCustomerId.value = cust.id;
        currentSelectedDue = parseFloat(cust.due) || 0;
        clsSelectedShop.textContent = cust.shop_name;
        clsSelectedDue.textContent = 'Due: ৳ ' + currentSelectedDue.toFixed(2);

        clsContainer.style.display = 'none';
        clsSelectedBox.style.display = 'flex';
        clsDropdown.style.display = 'none';

        updateDuePreview();
    }

    function clearCustomer() {
        selectedCustomerId.value = '';
        currentSelectedDue = 0;
        clsSelectedBox.style.display = 'none';
        clsContainer.style.display = 'block';
        clsInput.value = '';
        clsDropdown.style.display = 'none';
        if (duePreview) duePreview.style.display = 'none';
        clsInput.focus();
    }

    // Auto load old customer selection if validation failed
    var oldId = parseInt(selectedCustomerId.value);
    if (oldId) {
        var found = allCustomers.find(function(c) { return c.id === oldId; });
        if (found) {
            selectCustomer(found);
        }
    }

    if (clsInput) {
        clsInput.addEventListener('input', function () {
            var raw = this.value;
            var q = raw.trim().toLowerCase();

            // 2+ spaces = show all, 2+ chars = search
            var isShowAll   = raw.length >= 2 && raw.trim() === '';
            var isSearchMatch = q.length >= 2;

            if (!isShowAll && !isSearchMatch) {
                clsDropdown.style.display = 'none';
                clsDropdown.innerHTML = '';
                return;
            }

            var filtered = isShowAll
                ? allCustomers.filter(function(c) { return c.due > 0; })
                : allCustomers.filter(function(c) {
                    return String(c.shop_name).toLowerCase().includes(q);
                });

            if (!filtered.length) {
                clsDropdown.innerHTML = '<div class="cls-empty"><i class="fas fa-store-slash me-1"></i> No matching customers with due found</div>';
                clsDropdown.style.display = 'block';
                return;
            }

            var html = '';
            filtered.forEach(function(c) {
                html += '<div class="cls-item" data-id="' + c.id + '">'
                      + '<div class="cls-item-shop">' + esc(c.shop_name) + '</div>'
                      + '<span class="cls-item-due">Due: ৳ ' + parseFloat(c.due).toFixed(2) + '</span>'
                      + '</div>';
            });

            clsDropdown.innerHTML = html;
            clsDropdown.style.display = 'block';
        });

        clsDropdown.addEventListener('click', function(e) {
            var item = e.target.closest('.cls-item');
            if (!item) return;
            var id   = parseInt(item.dataset.id);
            var found = allCustomers.find(function(c) { return c.id === id; });
            if (found) selectCustomer(found);
        });

        if (clsClearBtn) {
            clsClearBtn.addEventListener('click', clearCustomer);
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#cls-container')) {
                clsDropdown.style.display = 'none';
            }
        });
    }

    // Amount input -> live due preview
    if (amountInput) {
        amountInput.addEventListener('input', updateDuePreview);
        if (amountInput.value) updateDuePreview();
    }
});
</script>
@endpush