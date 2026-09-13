@extends('layouts.userlayout')

@section('content')
<div class="checkout-page-wrapper">
  <div class="checkout-page-container">

    <!-- Breadcrumbs -->
    <nav class="checkout-breadcrumbs" aria-label="breadcrumb">
      <a href="{{ route('home-page') }}">Home</a>
      <span class="breadcrumb-sep">/</span>
      <a href="javascript:void(0)" onclick="typeof openCartModal === 'function' ? openCartModal() : window.location.href='{{ route('home-page') }}'">Cart</a>
      <span class="breadcrumb-sep">/</span>
      <span class="current">Checkout</span>
    </nav>

    <!-- Empty Cart Alert View -->
    <div id="checkoutEmptyState" class="checkout-card checkout-empty-card" style="display: none;">
      <div class="empty-icon-wrap">
        <i class="fas fa-shopping-basket"></i>
      </div>
      <h2>Your Cart is Empty</h2>
      <p>Please add products to your cart before proceeding to checkout.</p>
      <a href="{{ route('home-page') }}" class="btn-return-shop">
        <i class="fas fa-arrow-left"></i>
        <span>Return to Shop</span>
      </a>
    </div>

    <!-- Active Checkout Form & Summary Grid -->
    <div id="checkoutMainGrid" class="checkout-main-grid" style="display: none;">
      
      <!-- Left Column: Delivery Details Card -->
      <div class="checkout-card delivery-details-card">
        <div class="card-header-custom">
          <div class="header-icon-wrap">
            <i class="fas fa-user"></i>
          </div>
          <h2 class="card-title">Delivery Details</h2>
        </div>

        <form id="checkoutForm" onsubmit="handlePlaceOrder(event)" novalidate>
          @csrf
          
          <!-- Full Name -->
          <div class="form-group-custom">
            <label for="fullname" class="form-label-custom">Full Name <span class="required-star">*</span></label>
            <input 
              type="text" 
              id="fullname" 
              name="fullname" 
              class="form-control-custom" 
              placeholder="Enter your full name" 
              value="{{ old('fullname', $prefillData['fullname']) }}" 
              required
            >
            <div class="form-error-msg" id="err_fullname"></div>
          </div>

          <!-- Phone / WhatsApp -->
          <div class="form-group-custom">
            <label for="phone" class="form-label-custom">Phone / WhatsApp <span class="required-star">*</span></label>
            <input 
              type="tel" 
              id="phone" 
              name="phone" 
              class="form-control-custom" 
              placeholder="Enter your phone number" 
              value="{{ old('phone', $prefillData['phone']) }}" 
              required
            >
            <span class="form-helper-text">We'll confirm your order on this number.</span>
            <div class="form-error-msg" id="err_phone"></div>
          </div>

          <!-- Country & City Row -->
          <div class="form-row-custom">
            <!-- Country -->
            <div class="form-group-custom flex-1">
              <label for="country" class="form-label-custom">Country <span class="required-star">*</span></label>
              <select 
                id="country" 
                name="country" 
                class="form-control-custom form-select-custom" 
                onchange="handleCountryChange()" 
                required
              >
                @php
                  $selectedCountry = old('country', $prefillData['country']);
                  $countryList = !empty($countriesData) ? array_keys($countriesData) : ['Bangladesh'];
                  if (!in_array('Bangladesh', $countryList)) {
                      array_unshift($countryList, 'Bangladesh');
                  }
                @endphp
                @foreach($countryList as $cName)
                  <option value="{{ $cName }}" {{ strcasecmp($selectedCountry, $cName) === 0 ? 'selected' : '' }}>
                    {{ $cName }}
                  </option>
                @endforeach
              </select>
              <div class="form-error-msg" id="err_country"></div>
            </div>

            <!-- City -->
            <div class="form-group-custom flex-1">
              <label for="city" class="form-label-custom">City <span class="required-star">*</span></label>
              <select 
                id="city" 
                name="city" 
                class="form-control-custom form-select-custom" 
                onchange="handleCityChange()" 
                required
              >
                <option value="" disabled selected>Select City</option>
              </select>
              <div class="form-error-msg" id="err_city"></div>
            </div>
          </div>

          <!-- Delivery Address -->
          <div class="form-group-custom">
            <label for="address" class="form-label-custom">Delivery Address <span class="required-star">*</span></label>
            <textarea 
              id="address" 
              name="address" 
              class="form-control-custom form-textarea-custom" 
              rows="3" 
              placeholder="House, Road, Area, Post Office..." 
              required
            >{{ old('address', $prefillData['address']) }}</textarea>
            <div class="form-error-msg" id="err_address"></div>
          </div>

          <!-- Order Note -->
          <div class="form-group-custom">
            <label for="note" class="form-label-custom">Order Note <span class="optional-label">(optional)</span></label>
            <textarea 
              id="note" 
              name="note" 
              class="form-control-custom form-textarea-custom" 
              rows="2" 
              placeholder="Any special instruction for the delivery."
            >{{ old('note') }}</textarea>
          </div>

          <!-- Payment Method -->
          <div class="form-group-custom payment-method-section">
            <label class="form-label-custom">Payment Method <span class="required-star">*</span></label>
            <div class="payment-methods-grid" id="paymentMethodsGrid">

              <label class="payment-method-card active" for="pm_cod">
                <input type="radio" id="pm_cod" name="payment_method" value="Cash on Delivery" checked onchange="handlePaymentMethodChange(this)">
                <div class="pm-icon-wrap">
                  <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="pm-label">Cash on Delivery</div>
                <div class="pm-check"><i class="fas fa-check-circle"></i></div>
              </label>

              {{-- Future payment methods (commented out for now) --}}
              {{-- 
              <label class="payment-method-card" for="pm_bkash">
                <input type="radio" id="pm_bkash" name="payment_method" value="bKash" onchange="handlePaymentMethodChange(this)">
                <div class="pm-icon-wrap pm-bkash">
                  <span class="pm-text-icon">b</span>
                </div>
                <div class="pm-label">bKash</div>
                <div class="pm-check"><i class="fas fa-check-circle"></i></div>
              </label>

              <label class="payment-method-card" for="pm_nagad">
                <input type="radio" id="pm_nagad" name="payment_method" value="Nagad" onchange="handlePaymentMethodChange(this)">
                <div class="pm-icon-wrap pm-nagad">
                  <span class="pm-text-icon">N</span>
                </div>
                <div class="pm-label">Nagad</div>
                <div class="pm-check"><i class="fas fa-check-circle"></i></div>
              </label>

              <label class="payment-method-card" for="pm_bank">
                <input type="radio" id="pm_bank" name="payment_method" value="Bank Transfer" onchange="handlePaymentMethodChange(this)">
                <div class="pm-icon-wrap pm-bank">
                  <i class="fas fa-university"></i>
                </div>
                <div class="pm-label">Bank Transfer</div>
                <div class="pm-check"><i class="fas fa-check-circle"></i></div>
              </label>
              --}}

            </div>

            <!-- Bangla Notice: Why only COD -->
            <div class="cod-notice-box">
              <div class="cod-notice-icon">
                <i class="fas fa-info-circle"></i>
              </div>
              <div class="cod-notice-content">
                <p class="cod-notice-title">কেন শুধু ক্যাশ অন ডেলিভারি?</p>
                <p class="cod-notice-text">
                  আমরা একটি <strong>হোলসেল (পাইকারি) ব্যবসা প্রতিষ্ঠান</strong> — তাই আমাদের সকল অর্ডার 
                  <strong>ক্যাশ অন ডেলিভারি</strong> পদ্ধতিতে প্রক্রিয়া করা হয়। পণ্য হাতে পাওয়ার পরেই 
                  আপনাকে পেমেন্ট করতে হবে।
                </p>
              </div>
            </div>

          </div>

        </form>
      </div>

      <!-- Right Column: Order Summary Card -->
      <div class="checkout-card order-summary-card">
        <h2 class="card-title order-summary-heading">Order Summary</h2>

        <!-- Items List -->
        <div class="summary-items-list" id="checkoutItemsList">
          <!-- Loaded live via PricingEngine -->
        </div>

        <div class="summary-divider"></div>

        <!-- Coupon Section -->
        <div class="checkout-coupon-section">
          <label class="coupon-label">Have a Coupon Code?</label>
          <div class="coupon-input-group" id="couponInputGroup">
            <input 
              type="text" 
              id="checkoutCouponInput" 
              class="form-control-custom coupon-input" 
              placeholder="ENTER COUPON CODE"
              autocomplete="off"
            >
            <button type="button" class="btn-coupon-apply" id="btnApplyCoupon" onclick="handleApplyCoupon()">
              <span>Apply</span>
            </button>
          </div>
          <div id="checkoutCouponAlert" class="coupon-feedback-msg" style="display: none;"></div>
        </div>

        <!-- Breakdown Details -->
        <div class="summary-breakdown">
          
          <div class="breakdown-row">
            <span class="breakdown-label">Items</span>
            <span class="breakdown-value" id="summaryItemsCount">0</span>
          </div>

          <div class="breakdown-row">
            <span class="breakdown-label">Subtotal</span>
            <span class="breakdown-value" id="summarySubtotal">৳0</span>
          </div>

          <div class="breakdown-row" id="summarySavingsRow" style="display: none;">
            <span class="breakdown-label text-success-highlight">Total Savings</span>
            <span class="breakdown-value text-success-highlight" id="summarySavings">-৳0</span>
          </div>

          <div class="breakdown-row">
            <span class="breakdown-label">Shipping</span>
            <span class="breakdown-value" id="summaryShipping">৳0</span>
          </div>

          <div class="summary-divider bold-divider"></div>

          <div class="breakdown-row total-row">
            <span class="total-label">Total</span>
            <span class="total-value" id="summaryGrandTotal">৳0</span>
          </div>

        </div>

        <!-- Place Order Button -->
        <button 
          type="button" 
          class="btn-place-order" 
          id="btnPlaceOrder" 
          onclick="document.getElementById('checkoutForm').requestSubmit()"
        >
          <i class="fas fa-lock"></i>
          <span>Place Order</span>
        </button>

        <!-- Payment Security Badge -->
        <div class="cod-security-badge" id="paymentBadge">
          <i class="fas fa-shield-alt"></i>
          <span id="paymentBadgeText">Pay cash on delivery when your order arrives.</span>
        </div>

      </div>

    </div>

  </div>
</div>

<style>
  /* ==========================================================================
     CHECKOUT PAGE STYLES (Smart, Compact, Attractive Design)
     ========================================================================== */

  .checkout-page-wrapper {
    background-color: var(--background);
    min-height: calc(100vh - 120px);
    padding: 24px 16px 48px 16px;
    font-family: 'Inter', 'Roboto', sans-serif;
    color: var(--text-main);
  }

  .checkout-page-container {
    max-width: 1140px;
    margin: 0 auto;
  }

  /* Breadcrumbs */
  .checkout-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-muted);
    margin-bottom: 18px;
    font-weight: 500;
  }

  .checkout-breadcrumbs a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .checkout-breadcrumbs a:hover {
    color: var(--primary);
  }

  .checkout-breadcrumbs .breadcrumb-sep {
    color: var(--border-color);
    font-size: 11px;
  }

  .checkout-breadcrumbs .current {
    color: var(--text-main);
    font-weight: 600;
  }

  /* Main Grid */
  .checkout-main-grid {
    display: grid;
    grid-template-columns: 1.35fr 1fr;
    gap: 20px;
    align-items: start;
  }

  /* Shared Card Style */
  .checkout-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 20px 22px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
  }

  .checkout-card:hover {
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
  }

  /* Header */
  .card-header-custom {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
  }

  .header-icon-wrap {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }

  .card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    letter-spacing: -0.2px;
  }

  .order-summary-heading {
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
  }

  /* Form Elements */
  .form-group-custom {
    margin-bottom: 14px;
  }

  .form-row-custom {
    display: flex;
    gap: 12px;
  }

  .form-row-custom .flex-1 {
    flex: 1;
  }

  .form-label-custom {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-main);
    margin-bottom: 5px;
  }

  .required-star {
    color: #ef4444;
    margin-left: 2px;
  }

  .optional-label {
    font-size: 11px;
    font-weight: 400;
    color: var(--text-muted);
  }

  .form-control-custom {
    width: 100%;
    padding: 8px 12px;
    font-size: 13px;
    font-family: inherit;
    color: var(--text-main);
    background-color: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.2s ease;
    box-sizing: border-box;
  }

  .form-control-custom:focus {
    background-color: var(--section-bg);
    border-color: var(--primary);
    box-shadow: 0 0 0 2.5px var(--primary-soft);
    outline: none;
  }

  .form-select-custom {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 14px 14px;
    padding-right: 32px;
    cursor: pointer;
  }

  .form-textarea-custom {
    resize: vertical;
    min-height: 60px;
    line-height: 1.4;
    padding: 8px 12px;
  }

  .form-helper-text {
    display: block;
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 4px;
  }

  .form-error-msg {
    font-size: 11.5px;
    color: #ef4444;
    margin-top: 4px;
    display: none;
    font-weight: 500;
  }

  /* Right Side: Order Summary */
  .summary-items-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 260px;
    overflow-y: auto;
    padding-right: 4px;
    margin-bottom: 12px;
  }

  .summary-item-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
  }

  .summary-item-info {
    flex: 1;
    min-width: 0;
  }

  .summary-item-title {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-main);
    margin: 0 0 2px 0;
    line-height: 1.3;
    word-break: break-word;
  }

  .summary-item-qty-price {
    font-size: 11.5px;
    color: var(--text-muted);
    font-weight: 500;
  }

  .summary-item-total {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
    white-space: nowrap;
  }

  .summary-free-shipping-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: rgba(22, 163, 74, 0.1);
    color: #16a34a;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    margin-top: 3px;
  }

  .summary-divider {
    height: 1px;
    background-color: var(--border-color);
    margin: 12px 0;
  }

  .bold-divider {
    height: 1px;
    background-color: var(--border-color);
    margin: 10px 0;
  }

  /* Coupon Section */
  .checkout-coupon-section {
    margin-bottom: 14px;
  }

  .coupon-label {
    display: block;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 6px;
  }

  .coupon-input-group {
    display: flex;
    gap: 6px;
  }

  .coupon-input {
    text-transform: uppercase;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    padding: 7px 10px;
  }

  .btn-coupon-apply {
    padding: 0 16px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }

  .btn-coupon-apply:hover {
    filter: brightness(1.1);
  }

  .coupon-feedback-msg {
    font-size: 11px;
    margin-top: 4px;
    font-weight: 600;
  }

  .coupon-feedback-msg.success {
    color: #16a34a;
  }

  .coupon-feedback-msg.error {
    color: #ef4444;
  }

  /* Breakdown */
  .summary-breakdown {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
  }

  .breakdown-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12.5px;
  }

  .breakdown-label {
    color: var(--text-muted);
    font-weight: 500;
  }

  .breakdown-value {
    color: var(--text-main);
    font-weight: 600;
  }

  .text-success-highlight {
    color: #16a34a !important;
    font-weight: 700;
  }

  .total-row {
    margin-top: 2px;
  }

  .total-label {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-main);
  }

  .total-value {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-main);
  }

  /* Place Order Button */
  .btn-place-order {
    width: 100%;
    padding: 11px 20px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 24px;
    font-size: 14.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(2, 2, 226, 0.2);
    transition: all 0.2s ease;
  }

  .btn-place-order:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(2, 2, 226, 0.3);
  }

  .btn-place-order:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
  }

  /* COD Security Badge */
  .cod-security-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 12px;
    font-size: 11.5px;
    color: var(--text-muted);
    font-weight: 500;
    text-align: center;
  }

  .cod-security-badge i {
    color: var(--primary);
    font-size: 12px;
  }

  /* Payment Method Section */
  .payment-method-section {
    margin-top: 4px;
  }

  .payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 2px;
  }

  .payment-method-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    padding: 10px 6px 8px;
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    background: var(--background);
    cursor: pointer;
    transition: all 0.18s ease;
    text-align: center;
    user-select: none;
  }

  .payment-method-card input[type="radio"] {
    display: none;
  }

  .payment-method-card:hover {
    border-color: var(--primary);
    background: var(--primary-soft);
  }

  .payment-method-card.active {
    border-color: var(--primary);
    background: var(--primary-soft);
    box-shadow: 0 0 0 2px var(--primary-soft);
  }

  .pm-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  .pm-bkash {
    background: rgba(220, 0, 90, 0.1);
    color: #dc005a;
  }

  .pm-nagad {
    background: rgba(240, 100, 0, 0.1);
    color: #f06400;
  }

  .pm-bank {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
  }

  .pm-text-icon {
    font-size: 16px;
    font-weight: 900;
    line-height: 1;
  }

  .pm-label {
    font-size: 10.5px;
    font-weight: 600;
    color: var(--text-main);
    line-height: 1.2;
  }

  .pm-check {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 11px;
    color: var(--primary);
    opacity: 0;
    transition: opacity 0.15s ease;
  }

  .payment-method-card.active .pm-check {
    opacity: 1;
  }

  @media (max-width: 520px) {
    .payment-methods-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* COD Notice Box */
  .cod-notice-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 10px;
    padding: 11px 13px;
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.06) 0%, rgba(34, 197, 94, 0.03) 100%);
    border: 1px solid rgba(22, 163, 74, 0.2);
    border-left: 3px solid #16a34a;
    border-radius: 8px;
  }

  .cod-notice-icon {
    color: #16a34a;
    font-size: 14px;
    margin-top: 2px;
    flex-shrink: 0;
  }

  .cod-notice-content {
    flex: 1;
  }

  .cod-notice-title {
    font-size: 12px;
    font-weight: 700;
    color: #15803d;
    margin: 0 0 4px 0;
    line-height: 1.3;
  }

  .cod-notice-text {
    font-size: 11.5px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.6;
  }

  .cod-notice-text strong {
    color: var(--text-main);
    font-weight: 600;
  }

  /* Empty Cart State */
  .checkout-empty-card {
    text-align: center;
    padding: 48px 20px;
    max-width: 440px;
    margin: 30px auto;
  }

  .empty-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(234, 88, 12, 0.1);
    color: #ea580c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin: 0 auto 16px auto;
  }

  .checkout-empty-card h2 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 6px;
  }

  .checkout-empty-card p {
    font-size: 13.5px;
    color: var(--text-muted);
    margin-bottom: 18px;
  }

  .btn-return-shop {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    border-radius: 20px;
    background: var(--primary);
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 13.5px;
    transition: all 0.2s ease;
  }

  .btn-return-shop:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }

  /* Responsive Adjustments */
  @media (max-width: 860px) {
    .checkout-main-grid {
      grid-template-columns: 1fr;
      gap: 18px;
    }

    .checkout-card {
      padding: 18px 16px;
    }
  }

  @media (max-width: 520px) {
    .form-row-custom {
      flex-direction: column;
      gap: 0;
    }

    .total-value {
      font-size: 17px;
    }
  }
</style>

@push('scripts')
<script>
  // Countries and Cities data from server
  const countriesData = @json($countriesData);
  const activeShippingRates = @json($activeShippingRates);

  let currentCalculatedData = null;
  let isSubmitting = false;

  // --- Cookie and Storage Helpers ---
  function getCartItems() {
    try {
      const match = document.cookie.match(new RegExp('(^| )dms_cart=([^;]+)'));
      if (match) {
        return JSON.parse(decodeURIComponent(match[2]));
      }
      const local = localStorage.getItem('dms_cart');
      if (local) {
        return JSON.parse(local);
      }
    } catch (e) {
      console.error('Error parsing cart items', e);
    }
    return [];
  }

  function getAppliedCoupons() {
    try {
      const match = document.cookie.match(new RegExp('(^| )dms_applied_coupons=([^;]+)'));
      if (match) {
        return JSON.parse(decodeURIComponent(match[2]));
      }
      const local = localStorage.getItem('dms_applied_coupons');
      if (local) {
        return JSON.parse(local);
      }
    } catch (e) {
      console.error('Error parsing applied coupons', e);
    }
    return {};
  }

  function setAppliedCoupons(coupons) {
    const jsonStr = JSON.stringify(coupons);
    document.cookie = `dms_applied_coupons=${encodeURIComponent(jsonStr)}; path=/; max-age=604800`;
    localStorage.setItem('dms_applied_coupons', jsonStr);
  }

  function clearCartStorage() {
    document.cookie = 'dms_cart=; path=/; max-age=0';
    document.cookie = 'dms_applied_coupons=; path=/; max-age=0';
    localStorage.removeItem('dms_cart');
    localStorage.removeItem('dms_applied_coupons');
  }

  // Initial prefill / old city
  const oldOrPrefillCity = @json(old('city', $prefillData['city'] ?? ''));

  // --- Populate Cities Dropdown (matching admin/customer/create) ---
  function populateCities(country, selectedCity = '') {
    const citySelect = document.getElementById('city');
    if (!citySelect) return;

    citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';

    if (country && countriesData[country]) {
      const cities = countriesData[country];

      cities.forEach(function (city) {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        if (selectedCity && city.toLowerCase() === selectedCity.toLowerCase()) {
          option.selected = true;
        }
        citySelect.appendChild(option);
      });

      citySelect.disabled = false;
    } else {
      citySelect.disabled = true;
    }
  }

  function handleCountryChange() {
    const country = document.getElementById('country').value;
    populateCities(country, '');
    syncCheckoutPricing();
  }

  function handleCityChange() {
    syncCheckoutPricing();
  }

  // --- Live Pricing Engine Call ---
  async function syncCheckoutPricing() {
    const cartItems = getCartItems();
    const appliedCoupons = getAppliedCoupons();

    const emptyStateEl = document.getElementById('checkoutEmptyState');
    const mainGridEl = document.getElementById('checkoutMainGrid');

    if (!cartItems || cartItems.length === 0) {
      if (emptyStateEl) emptyStateEl.style.display = 'block';
      if (mainGridEl) mainGridEl.style.display = 'none';
      return;
    }

    if (emptyStateEl) emptyStateEl.style.display = 'none';
    if (mainGridEl) mainGridEl.style.display = 'grid';

    const country = document.getElementById('country') ? document.getElementById('country').value : 'Bangladesh';
    const city = document.getElementById('city') ? document.getElementById('city').value : 'Dhaka';

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const response = await fetch("{{ route('checkout.calculate') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          items: cartItems,
          applied_coupons: appliedCoupons,
          country: country,
          city: city
        })
      });

      if (!response.ok) {
        throw new Error('Network calculation error');
      }

      const data = await response.json();
      if (data.success) {
        currentCalculatedData = data;
        renderCheckoutSummary(data);
      }
    } catch (err) {
      console.error('Checkout sync error:', err);
    }
  }

  // --- Render Order Summary UI ---
  function renderCheckoutSummary(data) {
    const itemsListEl = document.getElementById('checkoutItemsList');
    const countEl = document.getElementById('summaryItemsCount');
    const subtotalEl = document.getElementById('summarySubtotal');
    const savingsRow = document.getElementById('summarySavingsRow');
    const savingsEl = document.getElementById('summarySavings');
    const shippingEl = document.getElementById('summaryShipping');
    const grandTotalEl = document.getElementById('summaryGrandTotal');

    if (!itemsListEl) return;

    // Render items list
    itemsListEl.innerHTML = data.items.map(item => `
      <div class="summary-item-row">
        <div class="summary-item-info">
          <h4 class="summary-item-title">${escapeHtml(item.name)}</h4>
          <div class="summary-item-qty-price">${item.quantity} × ${item.final_unit_price_formatted}</div>
          ${item.has_free_shipping ? '<div class="summary-free-shipping-tag"><i class="fas fa-truck"></i> Free Shipping</div>' : ''}
        </div>
        <div class="summary-item-total">${item.subtotal_formatted}</div>
      </div>
    `).join('');

    if (countEl) countEl.textContent = data.total_items;
    if (subtotalEl) subtotalEl.textContent = data.subtotal_formatted;

    if (data.total_savings > 0) {
      if (savingsRow) savingsRow.style.display = 'flex';
      if (savingsEl) savingsEl.textContent = '-' + data.total_savings_formatted;
    } else {
      if (savingsRow) savingsRow.style.display = 'none';
    }

    if (shippingEl) {
      shippingEl.textContent = data.shipping_formatted;
      if (data.is_free_shipping) {
        shippingEl.classList.add('text-success-highlight');
      } else {
        shippingEl.classList.remove('text-success-highlight');
      }
    }

    if (grandTotalEl) grandTotalEl.textContent = data.grand_total_formatted;
  }

  // --- Apply Coupon on Checkout Page ---
  async function handleApplyCoupon() {
    const input = document.getElementById('checkoutCouponInput');
    const alertEl = document.getElementById('checkoutCouponAlert');
    const btn = document.getElementById('btnApplyCoupon');
    const code = input ? input.value.trim() : '';

    if (!code) {
      showCouponFeedback('Please enter a coupon code.', 'error');
      return;
    }

    const cartItems = getCartItems();
    if (cartItems.length === 0) {
      showCouponFeedback('Your cart is empty.', 'error');
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let matched = false;
    let appliedCount = 0;
    const currentCoupons = getAppliedCoupons();

    for (const item of cartItems) {
      try {
        const res = await fetch("{{ route('cart.apply_coupon') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            product_id: item.id || item.product_id,
            coupon_code: code
          })
        });
        const resData = await res.json();
        if (resData.success) {
          currentCoupons[item.id || item.product_id] = code;
          matched = true;
          appliedCount++;
        }
      } catch (e) {
        console.error('Coupon check error for item:', item, e);
      }
    }

    btn.disabled = false;
    btn.innerHTML = '<span>Apply</span>';

    if (matched) {
      setAppliedCoupons(currentCoupons);
      showCouponFeedback(`Coupon "${code}" applied successfully!`, 'success');
      if (input) input.value = '';
      syncCheckoutPricing();
    } else {
      showCouponFeedback('Invalid coupon code for items in your cart.', 'error');
    }
  }

  function showCouponFeedback(msg, type) {
    const alertEl = document.getElementById('checkoutCouponAlert');
    if (!alertEl) return;
    alertEl.textContent = msg;
    alertEl.className = 'coupon-feedback-msg ' + type;
    alertEl.style.display = 'block';
  }

  // --- Payment Method Selection ---
  function handlePaymentMethodChange(input) {
    // Remove active class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
      card.classList.remove('active');
    });
    // Add active to selected
    if (input && input.parentElement) {
      input.parentElement.classList.add('active');
    }
    // Update badge text
    const badgeText = document.getElementById('paymentBadgeText');
    if (badgeText) {
      const pm = input ? input.value : 'Cash on Delivery';
      const badges = {
        'Cash on Delivery': 'Pay cash on delivery when your order arrives.',
        'bKash'           : 'You selected bKash. Payment details will be shared after order confirmation.',
        'Nagad'           : 'You selected Nagad. Payment details will be shared after order confirmation.',
        'Bank Transfer'   : 'You selected Bank Transfer. Account details will be shared after order confirmation.',
      };
      badgeText.textContent = badges[pm] || 'Secure payment guaranteed.';
    }
  }

  // --- Place Order Submission ---
  async function handlePlaceOrder(e) {
    e.preventDefault();
    if (isSubmitting) return;

    // Reset error messages
    document.querySelectorAll('.form-error-msg').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });

    const fullname = document.getElementById('fullname').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const country = document.getElementById('country').value.trim();
    const city = document.getElementById('city').value.trim();
    const address = document.getElementById('address').value.trim();
    const note = document.getElementById('note').value.trim();
    const paymentMethodEl = document.querySelector('input[name="payment_method"]:checked');
    const paymentMethod = paymentMethodEl ? paymentMethodEl.value : 'Cash on Delivery';

    let hasError = false;

    if (!fullname) {
      showFieldError('fullname', 'Please enter your full name.');
      hasError = true;
    }
    if (!phone) {
      showFieldError('phone', 'Please enter your phone / WhatsApp number.');
      hasError = true;
    }
    if (!country) {
      showFieldError('country', 'Please select your country.');
      hasError = true;
    }
    if (!city) {
      showFieldError('city', 'Please enter your city.');
      hasError = true;
    }
    if (!address) {
      showFieldError('address', 'Please enter your delivery address.');
      hasError = true;
    }

    if (hasError) return;

    const cartItems = getCartItems();
    if (cartItems.length === 0) {
      alert('Your cart is empty.');
      return;
    }

    const appliedCoupons = getAppliedCoupons();
    const btn = document.getElementById('btnPlaceOrder');

    isSubmitting = true;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>Processing Order...</span>';

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const response = await fetch("{{ route('checkout.place_order') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          fullname: fullname,
          phone: phone,
          country: country,
          city: city,
          address: address,
          note: note,
          payment_method: paymentMethod,
          items: cartItems,
          applied_coupons: appliedCoupons
        })
      });

      const resData = await response.json();

      if (response.ok && resData.success) {
        clearCartStorage();
        window.location.href = resData.redirect_url;
      } else {
        if (resData.errors) {
          Object.keys(resData.errors).forEach(field => {
            showFieldError(field, resData.errors[field][0]);
          });
        } else {
          alert(resData.message || 'An error occurred while placing your order. Please try again.');
        }
        isSubmitting = false;
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock"></i> <span>Place Order</span>';
      }
    } catch (err) {
      console.error('Order submission error:', err);
      alert('Network error. Please check your connection and try again.');
      isSubmitting = false;
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-lock"></i> <span>Place Order</span>';
    }
  }

  function showFieldError(fieldId, msg) {
    const errEl = document.getElementById('err_' + fieldId);
    if (errEl) {
      errEl.textContent = msg;
      errEl.style.display = 'block';
    }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // --- Initial Load ---
  document.addEventListener('DOMContentLoaded', () => {
    const countryEl = document.getElementById('country');
    if (countryEl && countryEl.value) {
      populateCities(countryEl.value, oldOrPrefillCity);
    }
    syncCheckoutPricing();
  });
</script>
@endpush
@endsection
