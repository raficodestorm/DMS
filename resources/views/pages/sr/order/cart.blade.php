@extends('layouts.srlayout')

@section('content')
<style>
  .cart-container {
    max-width: 850px;
    margin: 0 auto;
  }

  .cart-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 4px 16px var(--glass);
  }

  .cart-item-row {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .cart-item-img {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    object-fit: cover;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    flex-shrink: 0;
  }

  .qty-box {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--section-bg);
  }

  .qty-btn {
    width: 26px;
    height: 26px;
    border: none;
    background: transparent;
    color: var(--primary);
    font-weight: bold;
    cursor: pointer;
  }

  .qty-input {
    width: 38px;
    height: 26px;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
    outline: none;
  }

  .cart-summary-box {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 14px 18px;
    margin-top: 15px;
  }

  /* Simple Modal */
  .confirm-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
  }

  .confirm-modal-content {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    width: 100%;
    max-width: 400px;
    padding: 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  }

  @media (max-width: 576px) {
    .cart-item-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 8px;
    }
    .cart-item-right {
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
  }
</style>

<div class="container-fluid py-3">
  <div class="cart-container">
    <div class="cart-card">
      
      {{-- Header --}}
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h5 class="m-0 fw-bold"><i class="fas fa-shopping-cart text-primary me-1"></i> Wholesale Cart</h5>
        <a href="{{ route('sr.order.pos') }}" class="btn btn-sm btn-outline-primary">
          <i class="fas fa-arrow-left me-1"></i> Add More...
        </a>
      </div>

      @include('components.alert')

      <form method="POST" action="{{ route('sr.order.store') }}" id="srCartOrderForm" onsubmit="return handleCartSubmit(event)">
        @csrf

        {{-- 1. Customer Select --}}
        <div class="mb-3">
          <label class="form-label fw-bold small">Select Customer / Shop <span class="text-danger">*</span></label>
          <select name="customer_id" id="srCustomerSelect" class="form-select" required>
            <option value="" data-due="0" data-name="">-- Choose Wholesale Customer --</option>
            @foreach($customers as $c)
              @php $dueVal = (float)($c->due ?: 0); @endphp
              <option value="{{ $c->id }}" data-due="{{ $dueVal }}" data-name="{{ $c->shop_name }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                {{ $c->shop_name }} - {{ $c->phone ?? 'No phone' }} (Due: ৳{{ number_format($dueVal) }})
              </option>
            @endforeach
          </select>
          @error('customer_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- 2. Deduction Controls --}}
        <div class="p-3 mb-3 border rounded bg-light">
          <div class="row align-items-center g-2">
            <div class="col-sm-6 mb-3">
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" name="apply_global" id="srApplyGlobalDeduction" value="1" checked onchange="recalcCartTotals()">
                <label class="form-check-label fw-bold small" for="srApplyGlobalDeduction">
                  Apply Standard Deduction
                </label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white">Custom %</span>
                <input type="number" name="applied_custom_deduction" id="srCustomDeductionInput" class="form-control" placeholder="0.00" step="0.01" min="0" value="0" oninput="recalcCartTotals()">
              </div>
            </div>
          </div>
        </div>

        {{-- 3. Cart Items --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold small text-muted text-uppercase">Cart Items</span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="clearCartConfirm()">Clear All</button>
          </div>

          <div id="srCartItemsContainer"></div>

          <div class="text-center py-4 text-muted" id="srCartEmptyMsg" style="display:none;">
            <i class="fas fa-shopping-basket fs-1 mb-2 opacity-50"></i>
            <p class="mb-2">Your cart is empty</p>
            <a href="{{ route('sr.order.pos') }}" class="btn btn-sm btn-primary">Add Products</a>
          </div>
        </div>

        {{-- 4. Order Note --}}
        <div class="mb-3">
          <label class="form-label fw-bold small">Order Note (Optional)</label>
          <textarea name="note" class="form-control" rows="2" placeholder="Write any instructions...">{{ old('note') }}</textarea>
        </div>

        {{-- 5. Financial Summary --}}
        <div class="cart-summary-box">
          <div class="d-flex justify-content-between small text-muted mb-2">
            <span>Total Items:</span>
            <strong class="text-dark" id="srSummaryTotalItems">0 items (0 pcs)</strong>
          </div>

          <div class="d-flex justify-content-between small text-muted mb-2">
            <span>Gross Subtotal:</span>
            <strong class="text-dark" id="srSummaryGrossSubtotal">৳ 0</strong>
          </div>

          <div class="d-flex justify-content-between small text-danger mb-2" id="srSummaryOfferDiscRow" style="display:none;">
            <span>Offer Discount:</span>
            <strong>- ৳ <span id="srSummaryOfferDiscVal">0</span></strong>
          </div>

          <div class="d-flex justify-content-between align-items-center small mb-3">
            <span class="fw-bold text-dark">Special Discount:</span>
            <div class="input-group input-group-sm" style="width: 120px;">
              <span class="input-group-text">৳</span>
              <input type="number" name="special_discount" id="srSpecialDiscInput" class="form-control text-end" placeholder="0" min="0" value="0" oninput="recalcCartTotals()">
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center p-2 rounded bg-white border mb-3">
            <span class="fw-bold text-primary">Net Payable:</span>
            <span class="fs-4 fw-bold text-primary">৳ <span id="srNetTotalDisplay">0</span></span>
          </div>

          <input type="hidden" name="supplier_id" id="formSupplierId" value="">
          <input type="hidden" name="net_total" id="formNetTotal" value="0">
          <input type="hidden" name="total_discount" id="formTotalDiscount" value="0">

          <button type="submit" class="btn-submit w-100 py-2" id="srSubmitOrderBtn">
            <i class="fas fa-check-circle me-1"></i> Place Order
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- Simple Confirm Modal --}}
<div class="confirm-modal-backdrop" id="srConfirmOrderModal" style="display: none;">
  <div class="confirm-modal-content">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
      <h6 class="m-0 fw-bold"><i class="fas fa-file-invoice text-primary me-1"></i> Order Confirmation</h6>
      <button type="button" class="btn-close btn-sm" onclick="closeConfirmModal()"></button>
    </div>

    <div class="mb-3">
      <div class="p-2 mb-3 rounded bg-light border small">
        <strong id="modalCustomerName">-</strong>
      </div>

      <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Order Net Total:</span>
        <strong class="text-primary">৳ <span id="modalNetTotal">0</span></strong>
      </div>

      <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Previous Due:</span>
        <strong class="text-danger">৳ <span id="modalPreviousDue">0</span></strong>
      </div>

      <div class="d-flex justify-content-between py-2 small fw-bold bg-light px-2 rounded mt-2">
        <span>Current Total Due:</span>
        <span class="text-dark">৳ <span id="modalCurrentDue">0</span></span>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
      <button type="button" class="btn btn-sm btn-secondary px-3" onclick="closeConfirmModal()">Cancel</button>
      <button type="button" class="btn btn-sm btn-primary px-3" id="btnModalSubmitOrder" onclick="executeFinalOrderSubmit()">
        <i class="fas fa-check-circle me-1"></i> Confirm
      </button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  let srCart = {};

  function loadCart() {
    try {
      const saved = localStorage.getItem('sr_pos_cart');
      srCart = saved ? JSON.parse(saved) : {};
    } catch(e) {
      srCart = {};
    }
    renderCartItems();
    recalcCartTotals();
    if (typeof updateGlobalSrCartBadge === 'function') {
      updateGlobalSrCartBadge();
    }
  }

  function saveCart() {
    localStorage.setItem('sr_pos_cart', JSON.stringify(srCart));
    if (typeof updateGlobalSrCartBadge === 'function') {
      updateGlobalSrCartBadge();
    }
  }

  function updateItemQty(id, delta) {
    if (!srCart[id]) return;
    srCart[id].qty += delta;
    if (srCart[id].qty <= 0) {
      delete srCart[id];
    }
    saveCart();
    renderCartItems();
    recalcCartTotals();
  }

  function setItemQty(id, val) {
    let qty = parseInt(val) || 1;
    if (qty < 1) qty = 1;
    if (srCart[id]) {
      srCart[id].qty = qty;
      saveCart();
      recalcCartTotals();
    }
  }

  function removeItem(id) {
    if (srCart[id]) {
      delete srCart[id];
      saveCart();
      renderCartItems();
      recalcCartTotals();
    }
  }

  function clearCartConfirm() {
    if (confirm('Clear all items from cart?')) {
      srCart = {};
      saveCart();
      renderCartItems();
      recalcCartTotals();
    }
  }

  function renderCartItems() {
    const container = document.getElementById('srCartItemsContainer');
    const emptyMsg = document.getElementById('srCartEmptyMsg');
    const submitBtn = document.getElementById('srSubmitOrderBtn');
    
    let keys = Object.keys(srCart).filter(k => srCart[k] && srCart[k].qty > 0);

    if (keys.length === 0) {
      container.innerHTML = '';
      emptyMsg.style.display = 'block';
      submitBtn.disabled = true;
      return;
    }

    emptyMsg.style.display = 'none';
    submitBtn.disabled = false;

    let html = '';
    let index = 0;
    let firstSupplierId = '';

    keys.forEach(k => {
      const item = srCart[k];
      if (!firstSupplierId && item.supplier_id) {
        firstSupplierId = item.supplier_id;
      }

      const imgUrl = item.image 
        ? (item.image.startsWith('/') ? item.image : '/' + item.image) 
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=3131ff&color=fff`;

      html += `
        <div class="cart-item-row" id="cartItemRow_${item.id}">
          <div class="d-flex align-items-center gap-2">
            <img src="${imgUrl}" class="cart-item-img" alt="${item.name}" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=3131ff&color=fff'">
            <div>
              <div class="fw-bold small text-dark">${item.name}</div>
              <div class="small text-muted">
                Rate: <span class="text-success fw-bold" id="itemSellingRateDisplay_${item.id}">৳ 0</span>
                <del class="ms-1" id="itemBaseRateDisplay_${item.id}">৳ ${Math.round(item.price)}</del>
                ${item.offer_text ? `<span class="badge bg-danger-subtle text-danger ms-1">${item.offer_text}</span>` : ''}
              </div>
            </div>
          </div>

          <div class="cart-item-right d-flex align-items-center gap-3">
            <div class="qty-box">
              <button type="button" class="qty-btn" onclick="updateItemQty(${item.id}, -1)">-</button>
              <input type="number" class="qty-input" value="${item.qty}" min="1" onchange="setItemQty(${item.id}, this.value)">
              <button type="button" class="qty-btn" onclick="updateItemQty(${item.id}, 1)">+</button>
            </div>

            <strong class="text-dark small" style="min-width: 60px; text-align: right;">৳ <span id="itemSubtotalDisplay_${item.id}">0</span></strong>

            <button type="button" class="btn btn-sm text-danger p-0" onclick="removeItem(${item.id})" title="Remove">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>

          <input type="hidden" name="products[${index}][product_id]" value="${item.id}">
          <input type="hidden" name="products[${index}][price]" value="${item.price}">
          <input type="hidden" name="products[${index}][discount]" value="${item.discount || 0}">
          <input type="hidden" name="products[${index}][qty]" id="itemFormQty_${item.id}" value="${item.qty}">
        </div>
      `;
      index++;
    });

    document.getElementById('formSupplierId').value = firstSupplierId;
    container.innerHTML = html;
  }

  function recalcCartTotals() {
    const applyGlobal = document.getElementById('srApplyGlobalDeduction').checked;
    const customRate = parseFloat(document.getElementById('srCustomDeductionInput').value) || 0;
    const specialDisc = parseFloat(document.getElementById('srSpecialDiscInput').value) || 0;

    let totalItemsCount = 0;
    let totalQty = 0;
    let totalGrossSubtotal = 0;
    let totalOfferDiscounts = 0;

    for (let id in srCart) {
      const item = srCart[id];
      if (!item || item.qty <= 0) continue;

      totalItemsCount++;
      totalQty += item.qty;

      const standardDeduction = applyGlobal ? (item.customer_deduction || 0) : 0;
      const totalDeductionPct = Math.min(100, standardDeduction + customRate);

      const basePrice = item.price || 0;
      const unitDeductionAmount = (basePrice * totalDeductionPct / 100);
      const unitSellingRate = Math.round(basePrice - unitDeductionAmount);
      const offerDisc = item.discount || 0;

      const lineRate = Math.max(0, unitSellingRate - offerDisc);
      const lineSubtotal = lineRate * item.qty;

      totalGrossSubtotal += (unitSellingRate * item.qty);
      totalOfferDiscounts += (offerDisc * item.qty);

      const sellingRateEl = document.getElementById('itemSellingRateDisplay_' + id);
      const baseRateEl = document.getElementById('itemBaseRateDisplay_' + id);
      const subtotalEl = document.getElementById('itemSubtotalDisplay_' + id);
      const formQtyEl = document.getElementById('itemFormQty_' + id);

      if (sellingRateEl) sellingRateEl.textContent = '৳ ' + unitSellingRate.toLocaleString('en-US');
      if (baseRateEl) baseRateEl.style.display = totalDeductionPct > 0 ? 'inline' : 'none';
      if (subtotalEl) subtotalEl.textContent = Math.round(lineSubtotal).toLocaleString('en-US');
      if (formQtyEl) formQtyEl.value = item.qty;
    }

    const grandSubtotalAfterOffer = Math.max(0, totalGrossSubtotal - totalOfferDiscounts);
    const finalNetPayable = Math.max(0, Math.round(grandSubtotalAfterOffer - specialDisc));
    const finalTotalDiscount = totalOfferDiscounts + specialDisc;

    document.getElementById('srSummaryTotalItems').textContent = `${totalItemsCount} items (${totalQty} pcs)`;
    document.getElementById('srSummaryGrossSubtotal').textContent = '৳ ' + Math.round(totalGrossSubtotal).toLocaleString('en-US');
    
    const offerRow = document.getElementById('srSummaryOfferDiscRow');
    if (totalOfferDiscounts > 0) {
      offerRow.style.display = 'flex';
      document.getElementById('srSummaryOfferDiscVal').textContent = Math.round(totalOfferDiscounts).toLocaleString('en-US');
    } else {
      offerRow.style.display = 'none';
    }

    document.getElementById('srNetTotalDisplay').textContent = finalNetPayable.toLocaleString('en-US');
    document.getElementById('formNetTotal').value = finalNetPayable;
    document.getElementById('formTotalDiscount').value = finalTotalDiscount;
  }

  function handleCartSubmit(e) {
    e.preventDefault();

    const keys = Object.keys(srCart).filter(k => srCart[k] && srCart[k].qty > 0);
    if (keys.length === 0) {
      alert('Your cart is empty!');
      return false;
    }

    const selectEl = document.getElementById('srCustomerSelect');
    if (!selectEl.value) {
      alert('Please select a customer.');
      selectEl.focus();
      return false;
    }

    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const customerName = selectedOption.getAttribute('data-name') || selectedOption.text.split('-')[0].trim();
    const previousDue = parseFloat(selectedOption.getAttribute('data-due')) || 0;
    const netTotal = parseFloat(document.getElementById('formNetTotal').value) || 0;
    const currentDue = previousDue + netTotal;

    document.getElementById('modalCustomerName').textContent = customerName;
    document.getElementById('modalNetTotal').textContent = Math.round(netTotal).toLocaleString('en-US');
    document.getElementById('modalPreviousDue').textContent = Math.round(previousDue).toLocaleString('en-US');
    document.getElementById('modalCurrentDue').textContent = Math.round(currentDue).toLocaleString('en-US');

    document.getElementById('srConfirmOrderModal').style.display = 'flex';
    return false;
  }

  function closeConfirmModal() {
    document.getElementById('srConfirmOrderModal').style.display = 'none';
  }

  function executeFinalOrderSubmit() {
    const btn = document.getElementById('btnModalSubmitOrder');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Placing...';

    localStorage.removeItem('sr_pos_cart');
    document.getElementById('srCartOrderForm').submit();
  }

  document.addEventListener('DOMContentLoaded', function() {
    loadCart();
  });
</script>
@endpush
