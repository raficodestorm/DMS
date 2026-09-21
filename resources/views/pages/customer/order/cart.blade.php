@extends('layouts.customerlayout')

@section('content')
<style>
  .cart-container {
    max-width: 850px;
    margin: 0 auto;
  }

  .cart-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
  }

  /* ── Customer Info Banner ─────────────────────────────────────── */
  .cust-profile-summary-box {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 16px;
  }

  .cust-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    font-size: 13px;
  }

  @media (min-width: 768px) {
    .cust-info-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  .cust-info-item .info-label {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
  }

  .cust-info-item .info-val {
    font-weight: 700;
    color: var(--text-main);
  }

  /* ── Cart Item Row ────────────────────────────────────────────── */
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
    width: 28px;
    height: 28px;
    border: none;
    background: transparent;
    color: var(--primary);
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .qty-input {
    width: 38px;
    height: 28px;
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
    padding: 16px 20px;
    margin-top: 15px;
  }

  .btn-submit-order {
    background: linear-gradient(135deg, var(--primary), #00b4d8);
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 4px 14px var(--primary-soft);
    transition: all 0.2s ease;
  }

  .btn-submit-order:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px var(--primary-soft);
  }

  /* ── Confirm Modal ────────────────────────────────────────────── */
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
    backdrop-filter: blur(4px);
  }

  .confirm-modal-content {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    width: 100%;
    max-width: 440px;
    padding: 22px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
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
        <h5 class="m-0 fw-bold" style="color: var(--primary);">
          <i class="fas fa-shopping-cart me-1"></i> Wholesale Cart & Checkout
        </h5>
        <a href="{{ route('customer.orders.shop') }}" class="btn btn-sm btn-outline-primary" style="font-weight: 600; border-radius: 8px;">
          <i class="fas fa-arrow-left me-1"></i> Add More Products
        </a>
      </div>

      @include('components.alert')

      {{-- 1. Customer Information Card (Auto-filled) --}}
      <div class="cust-profile-summary-box">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
          <span class="small fw-bold text-uppercase text-muted"><i class="fas fa-user-check text-primary me-1"></i> Customer Profile</span>
          <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">Wholesale Account</span>
        </div>
        <div class="cust-info-grid">
          <div class="cust-info-item">
            <div class="info-label">Shop Name</div>
            <div class="info-val">{{ $customer->shop_name }}</div>
          </div>
          <div class="cust-info-item">
            <div class="info-label">Contact Phone</div>
            <div class="info-val">{{ $customer->phone ?? 'N/A' }}</div>
          </div>
          <div class="cust-info-item">
            <div class="info-label">Branch & SR</div>
            <div class="info-val">{{ $customer->branch?->name ?? 'Main' }} · {{ $customer->sr?->fullname ?? ($customer->sr?->username ?? 'Assigned SR') }}</div>
          </div>
          <div class="cust-info-item">
            <div class="info-label">Outstanding Due</div>
            <div class="info-val {{ $customer->due > 0 ? 'text-danger' : 'text-success' }}">
              ৳ {{ number_format($customer->due ?? 0) }}
            </div>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ route('customer.orders.store') }}" id="custCartOrderForm" onsubmit="return handleCustCartSubmit(event)">
        @csrf

        {{-- 2. Delivery Address (Editable) --}}
        <div class="mb-3">
          <label class="form-label fw-bold small">Delivery Address <span class="text-danger">*</span></label>
          <textarea name="delivery_address" id="custDeliveryAddress" class="form-control" rows="2" placeholder="Enter delivery address..." required>{{ old('delivery_address', $customer->address) }}</textarea>
          @error('delivery_address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- 3. Cart Items Container --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold small text-muted text-uppercase">Cart Items</span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="clearCustomerCartConfirm()">Clear All</button>
          </div>

          <div id="custCartItemsContainer"></div>

          <div class="text-center py-5 text-muted" id="custCartEmptyMsg" style="display:none;">
            <i class="fas fa-shopping-basket fs-1 mb-2 opacity-50 text-primary"></i>
            <h6 class="fw-bold text-dark">Your cart is empty</h6>
            <p class="small mb-3">Add items from the wholesale catalog to place an order.</p>
            <a href="{{ route('customer.orders.shop') }}" class="btn btn-sm btn-primary px-3 py-2" style="border-radius: 8px;">
              <i class="fas fa-store me-1"></i> Browse Wholesale Shop
            </a>
          </div>
        </div>

        {{-- 5. Order Note --}}
        <div class="mb-3">
          <label class="form-label fw-bold small">Order Note (Optional)</label>
          <textarea name="note" class="form-control" rows="2" placeholder="Any delivery instructions or notes...">{{ old('note') }}</textarea>
        </div>

        {{-- 6. Financial Summary --}}
        <div class="cart-summary-box">
          <div class="d-flex justify-content-between small text-muted mb-2">
            <span>Total Items:</span>
            <strong class="text-dark" id="custSummaryTotalItems">0 items (0 pcs)</strong>
          </div>

          <div class="d-flex justify-content-between small text-muted mb-2">
            <span>Gross Subtotal:</span>
            <strong class="text-dark" id="custSummaryGrossSubtotal">৳ 0</strong>
          </div>

          <div class="d-flex justify-content-between small text-danger mb-2" id="custSummaryOfferDiscRow" style="display:none;">
            <span>Wholesale Offer Discount:</span>
            <strong>- ৳ <span id="custSummaryOfferDiscVal">0</span></strong>
          </div>

          <div class="d-flex justify-content-between align-items-center p-3 rounded bg-white border mb-3 mt-2">
            <span class="fw-bold text-primary fs-6">Net Payable Amount:</span>
            <span class="fs-4 fw-bold text-primary">৳ <span id="custNetTotalDisplay">0</span></span>
          </div>

          <input type="hidden" name="supplier_id" id="formSupplierId" value="">
          <input type="hidden" name="net_total" id="formNetTotal" value="0">
          <input type="hidden" name="total_discount" id="formTotalDiscount" value="0">

          <button type="submit" class="btn-submit-order w-100 py-3" id="custSubmitOrderBtn">
            <i class="fas fa-check-circle me-1"></i> Place Wholesale Order
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- Confirmation Modal --}}
<div class="confirm-modal-backdrop" id="custConfirmOrderModal" style="display: none;">
  <div class="confirm-modal-content">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
      <h6 class="m-0 fw-bold" style="color: var(--primary);">
        <i class="fas fa-file-invoice text-primary me-1"></i> Order Confirmation
      </h6>
      <button type="button" class="btn-close btn-sm" onclick="closeCustConfirmModal()"></button>
    </div>

    <div class="mb-3">
      <div class="p-2 mb-3 rounded bg-light border small">
        <strong>Shop:</strong> {{ $customer->shop_name }}<br>
        <span class="text-muted"><strong>Deliver to:</strong> <span id="modalDeliveryAddress">-</span></span>
      </div>

      <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Order Net Payable:</span>
        <strong class="text-primary">৳ <span id="modalNetTotal">0</span></strong>
      </div>

      <div class="d-flex justify-content-between py-1 border-bottom small">
        <span class="text-muted">Previous Due:</span>
        <strong class="text-danger">৳ {{ number_format($customer->due ?? 0) }}</strong>
      </div>

      <div class="d-flex justify-content-between py-2 small fw-bold bg-light px-2 rounded mt-2">
        <span>Estimated Total Due:</span>
        <span class="text-dark">৳ <span id="modalCurrentDue">0</span></span>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
      <button type="button" class="btn btn-sm btn-secondary px-3" onclick="closeCustConfirmModal()">Cancel</button>
      <button type="button" class="btn btn-sm btn-primary px-3" id="btnModalSubmitCustomerOrder" onclick="executeFinalCustomerOrderSubmit()">
        <i class="fas fa-check-circle me-1"></i> Confirm & Submit
      </button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  let custCart = {};
  const customerPreviousDue = {{ (float)($customer->due ?? 0) }};

  function loadCustomerCart() {
    try {
      const saved = localStorage.getItem('customer_wholesale_cart');
      custCart = saved ? JSON.parse(saved) : {};
    } catch(e) {
      custCart = {};
    }
    renderCustomerCartItems();
    recalcCustomerCartTotals();
    if (typeof updateGlobalCustomerCartBadge === 'function') {
      updateGlobalCustomerCartBadge();
    }
  }

  function saveCustomerCart() {
    localStorage.setItem('customer_wholesale_cart', JSON.stringify(custCart));
    if (typeof updateGlobalCustomerCartBadge === 'function') {
      updateGlobalCustomerCartBadge();
    }
  }

  function updateCustomerItemQty(id, delta) {
    if (!custCart[id]) return;
    custCart[id].qty += delta;
    if (custCart[id].qty <= 0) {
      delete custCart[id];
    }
    saveCustomerCart();
    renderCustomerCartItems();
    recalcCustomerCartTotals();
  }

  function setCustomerItemQty(id, val) {
    let qty = parseInt(val) || 1;
    if (qty < 1) qty = 1;
    if (custCart[id]) {
      custCart[id].qty = qty;
      saveCustomerCart();
      recalcCustomerCartTotals();
    }
  }

  function removeCustomerItem(id) {
    if (custCart[id]) {
      delete custCart[id];
      saveCustomerCart();
      renderCustomerCartItems();
      recalcCustomerCartTotals();
    }
  }

  function clearCustomerCartConfirm() {
    if (confirm('Clear all items from your cart?')) {
      custCart = {};
      saveCustomerCart();
      renderCustomerCartItems();
      recalcCustomerCartTotals();
    }
  }

  function renderCustomerCartItems() {
    const container = document.getElementById('custCartItemsContainer');
    const emptyMsg = document.getElementById('custCartEmptyMsg');
    const submitBtn = document.getElementById('custSubmitOrderBtn');
    
    let keys = Object.keys(custCart).filter(k => custCart[k] && custCart[k].qty > 0);

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
      const item = custCart[k];
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
              <button type="button" class="qty-btn" onclick="updateCustomerItemQty(${item.id}, -1)">-</button>
              <input type="number" class="qty-input" value="${item.qty}" min="1" onchange="setCustomerItemQty(${item.id}, this.value)">
              <button type="button" class="qty-btn" onclick="updateCustomerItemQty(${item.id}, 1)">+</button>
            </div>

            <strong class="text-dark small" style="min-width: 60px; text-align: right;">৳ <span id="itemSubtotalDisplay_${item.id}">0</span></strong>

            <button type="button" class="btn btn-sm text-danger p-0" onclick="removeCustomerItem(${item.id})" title="Remove">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>

          <input type="hidden" name="products[${index}][product_id]" value="${item.id}">
          <input type="hidden" name="products[${index}][price]" value="${item.price}">
          <input type="hidden" name="products[${index}][discount]" value="${item.discount || 0}">
          <input type="hidden" name="products[${index}][offer]" value="${item.offer_text || ''}">
          <input type="hidden" name="products[${index}][qty]" id="itemFormQty_${item.id}" value="${item.qty}">
        </div>
      `;
      index++;
    });

    document.getElementById('formSupplierId').value = firstSupplierId;
    container.innerHTML = html;
  }

  function recalcCustomerCartTotals() {
    let totalItemsCount = 0;
    let totalQty = 0;
    let totalGrossSubtotal = 0;
    let totalOfferDiscounts = 0;

    for (let id in custCart) {
      const item = custCart[id];
      if (!item || item.qty <= 0) continue;

      totalItemsCount++;
      totalQty += item.qty;

      const standardDeduction = item.customer_deduction || 0;
      const totalDeductionPct = Math.min(100, standardDeduction);

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
    const finalNetPayable = Math.round(grandSubtotalAfterOffer);

    document.getElementById('custSummaryTotalItems').textContent = `${totalItemsCount} items (${totalQty} pcs)`;
    document.getElementById('custSummaryGrossSubtotal').textContent = '৳ ' + Math.round(totalGrossSubtotal).toLocaleString('en-US');
    
    const offerRow = document.getElementById('custSummaryOfferDiscRow');
    if (totalOfferDiscounts > 0) {
      offerRow.style.display = 'flex';
      document.getElementById('custSummaryOfferDiscVal').textContent = Math.round(totalOfferDiscounts).toLocaleString('en-US');
    } else {
      offerRow.style.display = 'none';
    }

    document.getElementById('custNetTotalDisplay').textContent = finalNetPayable.toLocaleString('en-US');
    document.getElementById('formNetTotal').value = finalNetPayable;
    document.getElementById('formTotalDiscount').value = totalOfferDiscounts;
  }

  function handleCustCartSubmit(e) {
    e.preventDefault();

    const keys = Object.keys(custCart).filter(k => custCart[k] && custCart[k].qty > 0);
    if (keys.length === 0) {
      alert('Your cart is empty!');
      return false;
    }

    const deliveryAddress = document.getElementById('custDeliveryAddress').value.trim();
    if (!deliveryAddress) {
      alert('Please enter a delivery address.');
      document.getElementById('custDeliveryAddress').focus();
      return false;
    }

    const netTotal   = parseFloat(document.getElementById('formNetTotal').value) || 0;
    const currentDue = customerPreviousDue + netTotal;

    document.getElementById('modalDeliveryAddress').textContent = deliveryAddress;
    document.getElementById('modalNetTotal').textContent = Math.round(netTotal).toLocaleString('en-US');
    document.getElementById('modalCurrentDue').textContent = Math.round(currentDue).toLocaleString('en-US');

    document.getElementById('custConfirmOrderModal').style.display = 'flex';
    return false;
  }

  function closeCustConfirmModal() {
    document.getElementById('custConfirmOrderModal').style.display = 'none';
  }

  function executeFinalCustomerOrderSubmit() {
    const btn = document.getElementById('btnModalSubmitCustomerOrder');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Placing Order...';

    localStorage.removeItem('customer_wholesale_cart');
    document.getElementById('custCartOrderForm').submit();
  }

  document.addEventListener('DOMContentLoaded', loadCustomerCart);
</script>
@endpush
