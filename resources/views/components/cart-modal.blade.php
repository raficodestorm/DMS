<!-- Cart Modal Component with Pricing Engine Service Integration -->
<style>
  /* ================= CART MODAL OVERLAY & CONTAINER ================= */
  .cart-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.68);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 100000;
    display: none;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 20px;
    box-sizing: border-box;
  }

  .cart-modal-overlay.show {
    display: flex;
    opacity: 1;
  }

  .cart-modal-container {
    width: 95%;
    max-width: 1100px;
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.22);
    overflow: hidden;
    position: relative;
    transform: scale(0.92) translateY(20px);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    max-height: 88vh;
    display: flex;
    flex-direction: column;
  }

  .cart-modal-overlay.show .cart-modal-container {
    transform: scale(1) translateY(0);
  }

  /* ================= CART MODAL HEADER ================= */
  .cart-modal-header {
    padding: 22px 30px 16px 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid var(--border-color);
    background: var(--section-bg);
    flex-shrink: 0;
  }

  .cart-modal-header-left {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .cart-breadcrumb {
    font-size: 13px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .cart-breadcrumb span.current {
    font-weight: 700;
    color: var(--text-main);
  }

  .cart-modal-title {
    font-family: 'Cinzel', 'El Messiri', serif;
    font-size: 29px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    letter-spacing: -0.5px;
  }

  .cart-modal-header-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .btn-clear-cart {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid #ef4444;
    background: rgba(239, 68, 68, 0.08);
    color: #ef4444;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-clear-cart:hover {
    background: #ef4444;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
  }

  .cart-modal-close-btn {
    background: transparent;
    border: none;
    font-size: 22px;
    color: var(--text-muted);
    cursor: pointer;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
  }

  .cart-modal-close-btn:hover {
    background: var(--glass);
    color: var(--text-main);
    transform: rotate(90deg);
  }

  /* ================= CART MODAL BODY ================= */
  .cart-modal-body {
    padding: 24px 30px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    flex: 1;
    background: var(--background);
    position: relative;
  }

  /* Custom Scrollbar */
  .cart-modal-body::-webkit-scrollbar,
  .cart-items-list::-webkit-scrollbar {
    width: 6px;
  }
  .cart-modal-body::-webkit-scrollbar-thumb,
  .cart-items-list::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
  }

  /* 2-Column Grid for Filled State (Desktop) */
  .cart-filled-layout {
    display: grid;
    grid-template-columns: 1.7fr 1.05fr;
    gap: 24px;
    align-items: start;
  }

  /* Left Side: Items List Card */
  .cart-items-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 18px 22px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
  }

  .cart-items-list {
    display: flex;
    flex-direction: column;
    max-height: 480px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .cart-item-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.15s ease;
  }

  .cart-item-row:last-child {
    border-bottom: none;
  }

  .cart-item-img-wrap {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    background: var(--primary-soft);
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .cart-item-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 4px;
  }

  .cart-item-img-wrap .placeholder-icon {
    font-size: 26px;
    color: var(--primary);
  }

  .cart-item-info {
    flex: 1;
    min-width: 0;
  }

  .cart-item-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 3px 0;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .cart-item-meta {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0 0 2px 0;
  }

  .cart-item-unit-price {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
  }

  .cart-item-coupon-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(22, 163, 74, 0.1);
    color: #16a34a;
    border: 1px solid rgba(22, 163, 74, 0.25);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-top: 4px;
  }

  .cart-item-coupon-input-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
  }

  .cart-coupon-input {
    border: 1px solid var(--border-color);
    background: var(--background);
    color: var(--text-main);
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    width: 100px;
    outline: none;
    text-transform: uppercase;
  }

  .cart-coupon-input:focus {
    border-color: var(--primary);
  }

  .btn-apply-item-coupon {
    border: none;
    background: var(--primary);
    color: #fff;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-apply-item-coupon:hover {
    opacity: 0.9;
  }

  .btn-remove-item-coupon {
    border: none;
    background: transparent;
    color: #ef4444;
    cursor: pointer;
    font-size: 11px;
    padding: 0 4px;
  }

  /* Quantity Controller Pill */
  .cart-qty-ctrl {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--section-bg);
    overflow: hidden;
    flex-shrink: 0;
  }

  .cart-qty-btn {
    width: 30px;
    height: 32px;
    border: none;
    background: transparent;
    color: var(--text-main);
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s ease;
  }

  .cart-qty-btn:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }

  .cart-qty-val {
    width: 34px;
    text-align: center;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-main);
    border-left: 1px solid var(--border-color);
    border-right: 1px solid var(--border-color);
    height: 32px;
    line-height: 32px;
  }

  /* Item Subtotal & Delete */
  .cart-item-price {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-main);
    min-width: 80px;
    text-align: right;
    flex-shrink: 0;
  }

  .cart-item-remove-btn {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 16px;
    cursor: pointer;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  .cart-item-remove-btn:hover {
    background: rgba(239, 68, 68, 0.1);
    transform: scale(1.15);
  }

  /* Right Side: Order Summary Card */
  .cart-summary-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
  }

  .cart-summary-title {
    font-family: 'Cinzel', 'El Messiri', serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 20px 0;
  }

  .cart-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 14.5px;
  }

  .cart-summary-label {
    color: var(--text-muted);
  }

  .cart-summary-value {
    font-weight: 700;
    color: var(--text-main);
  }

  .cart-summary-divider {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 18px 0;
  }

  .cart-total-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
  }

  .cart-total-label-wrap {
    display: flex;
    flex-direction: column;
  }

  .cart-total-label {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-main);
  }

  .cart-total-sub {
    font-size: 11.5px;
    color: var(--text-muted);
  }

  .cart-total-amount {
    font-size: 21px;
    font-weight: 800;
    color: var(--text-main);
  }

  /* Summary Buttons */
  .btn-cart-checkout {
    width: 100%;
    padding: 13px 18px;
    border-radius: 12px;
    border: none;
    background: var(--primary);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(2, 2, 226, 0.25);
    margin-bottom: 10px;
  }

  .btn-cart-checkout:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(2, 2, 226, 0.35);
  }

  .btn-cart-continue {
    width: 100%;
    padding: 11px 18px;
    border-radius: 12px;
    border: 1.5px solid var(--primary);
    background: transparent;
    color: var(--primary);
    font-size: 14.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-cart-continue:hover {
    background: var(--primary-soft);
    transform: translateY(-1px);
  }

  /* ================= EMPTY CART STATE ================= */
  .cart-empty-layout {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 60px 20px;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
  }

  .cart-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(234, 88, 12, 0.1);
    color: #ea580c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin-bottom: 20px;
  }

  .cart-empty-title {
    font-family: 'Cinzel', 'El Messiri', serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 8px 0;
  }

  .cart-empty-subtitle {
    font-size: 14.5px;
    color: var(--text-muted);
    margin: 0 0 24px 0;
    max-width: 400px;
  }

  .btn-cart-start-shopping {
    padding: 12px 28px;
    border-radius: 25px;
    border: none;
    background: var(--primary);
    color: #fff;
    font-size: 14.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(2, 2, 226, 0.2);
  }

  .btn-cart-start-shopping:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
  }

  /* Loading Skeleton / Shimmer */
  .cart-loading-shimmer {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 20px;
  }

  .shimmer-line {
    height: 18px;
    background: linear-gradient(90deg, var(--border-color) 25%, var(--primary-soft) 50%, var(--border-color) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 8px;
  }

  @keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* ================= CART TOAST NOTIFICATION ================= */
  .cart-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: var(--section-bg);
    color: var(--text-main);
    border: 1px solid var(--border-color);
    border-left: 4px solid var(--primary);
    padding: 12px 18px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    z-index: 100001;
    display: flex;
    align-items: center;
    gap: 12px;
    transform: translateY(60px);
    opacity: 0;
    visibility: hidden;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease, visibility 0.3s ease;
  }

  .cart-toast.show {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
  }

  .cart-toast-icon {
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

  .cart-toast-body {
    flex: 1;
    font-size: 13.5px;
    font-weight: 600;
  }

  .cart-toast-btn {
    padding: 4px 10px;
    border-radius: 6px;
    background: var(--primary);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
  }

  /* ================= RESPONSIVENESS & MOBILE FLEXIBILITY ================= */
  @media (max-width: 900px) {
    .cart-filled-layout {
      grid-template-columns: 1fr;
      gap: 18px;
    }

    .cart-modal-container {
      max-height: 92vh;
    }
  }

  @media (max-width: 650px) {
    .cart-modal-overlay {
      padding: 10px;
    }

    .cart-modal-container {
      width: 100%;
      border-radius: 16px;
      max-height: 94vh;
    }

    .cart-modal-header {
      padding: 15px 16px 12px 16px;
    }

    .cart-modal-title {
      font-size: 22px;
    }

    .cart-breadcrumb {
      font-size: 11.5px;
    }

    .btn-clear-cart {
      padding: 4px 10px;
      font-size: 11.5px;
    }

    .cart-modal-body {
      padding: 14px 12px;
    }

    .cart-items-card {
      padding: 12px 14px;
      border-radius: 14px;
    }

    .cart-items-list {
      max-height: none;
    }

    .cart-item-row {
      display: grid;
      grid-template-columns: 54px 1fr auto;
      grid-template-areas: 
        "img info delete"
        "img actions actions";
      gap: 8px 12px;
      padding: 12px 0;
      align-items: center;
    }

    .cart-item-img-wrap {
      grid-area: img;
      width: 54px;
      height: 54px;
      border-radius: 10px;
      align-self: start;
    }

    .cart-item-info {
      grid-area: info;
    }

    .cart-item-title {
      font-size: 13.5px;
      white-space: normal;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
    }

    .cart-item-meta {
      font-size: 11px;
    }

    .cart-item-unit-price {
      font-size: 11px;
    }

    .cart-item-remove-btn {
      grid-area: delete;
      align-self: start;
      padding: 2px;
    }

    .cart-item-actions-mobile {
      grid-area: actions;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      margin-top: 2px;
    }

    .cart-qty-ctrl {
      border-radius: 6px;
    }

    .cart-qty-btn {
      width: 26px;
      height: 28px;
      font-size: 13px;
    }

    .cart-qty-val {
      width: 28px;
      height: 28px;
      line-height: 28px;
      font-size: 12px;
    }

    .cart-item-price {
      font-size: 13.5px;
      min-width: auto;
    }

    .cart-summary-card {
      padding: 18px 16px;
      border-radius: 14px;
    }

    .cart-summary-title {
      font-size: 17px;
      margin-bottom: 14px;
    }

    .cart-summary-row {
      font-size: 13.5px;
      margin-bottom: 10px;
    }

    .cart-total-row {
      margin-bottom: 18px;
    }

    .cart-total-label {
      font-size: 16px;
    }

    .cart-total-amount {
      font-size: 18px;
    }

    .btn-cart-checkout {
      padding: 12px 16px;
      font-size: 14px;
    }

    .btn-cart-continue {
      padding: 10px 16px;
      font-size: 13.5px;
    }
  }
</style>

<!-- Cart Modal Overlay -->
<div class="cart-modal-overlay" id="cartModalOverlay" onclick="handleCartOverlayClick(event)">
  <div class="cart-modal-container" id="cartModalContainer" onclick="event.stopPropagation()">
    
    <!-- Modal Header -->
    <div class="cart-modal-header">
      <div class="cart-modal-header-left">
        <div class="cart-breadcrumb">
          <span>Home</span> / <span>Shop</span> / <span class="current">Cart</span>
        </div>
        <h3 class="cart-modal-title">Your Cart</h3>
      </div>
      <div class="cart-modal-header-right">
        <button type="button" class="btn-clear-cart" id="btnClearCart" onclick="clearCart()" style="display: none;">
          <i class="far fa-trash-alt"></i>
          <span>Clear Cart</span>
        </button>
        <button type="button" class="cart-modal-close-btn" onclick="closeCartModal()" aria-label="Close Cart">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <!-- Modal Body -->
    <div class="cart-modal-body">
      
      <!-- Filled State -->
      <div class="cart-filled-layout" id="cartFilledView" style="display: none;">
        
        <!-- Left: Cart Items List -->
        <div class="cart-items-card">
          <div class="cart-items-list" id="cartItemsList">
            <!-- Rendered dynamically via Live Pricing Engine -->
          </div>
        </div>

        <!-- Right: Order Summary Card -->
        <div class="cart-summary-card">
          <h4 class="cart-summary-title">Order Summary</h4>
          
          <div class="cart-summary-row">
            <span class="cart-summary-label">Items</span>
            <span class="cart-summary-value" id="cartSummaryItemsCount">0</span>
          </div>

          <div class="cart-summary-row">
            <span class="cart-summary-label">Subtotal</span>
            <span class="cart-summary-value" id="cartSummarySubtotal">৳0.00</span>
          </div>

          <div class="cart-summary-row" id="cartSavingsRow" style="display: none;">
            <span class="cart-summary-label" style="color: #16a34a;">Total Savings</span>
            <span class="cart-summary-value" id="cartSummarySavings" style="color: #16a34a;">-৳0.00</span>
          </div>

          <div class="cart-summary-row" id="cartFreeShippingRow" style="display: none;">
            <span class="cart-summary-label" style="color: #16a34a;">Shipping</span>
            <span class="cart-summary-value" style="color: #16a34a; font-weight: 800;">FREE 🎉</span>
          </div>

          <hr class="cart-summary-divider">

          <div class="cart-total-row">
            <div class="cart-total-label-wrap">
              <span class="cart-total-label">Total</span>
              <span class="cart-total-sub">Before shipping</span>
            </div>
            <span class="cart-total-amount" id="cartSummaryTotal">৳0.00</span>
          </div>

          <button type="button" class="btn-cart-checkout" onclick="proceedToCheckout()">
            <i class="fas fa-lock"></i>
            <span>Proceed to Checkout</span>
          </button>

          <button type="button" class="btn-cart-continue" onclick="closeCartModal()">
            <i class="fas fa-arrow-left"></i>
            <span>Continue Shopping</span>
          </button>
        </div>

      </div>

      <!-- Empty State -->
      <div class="cart-empty-layout" id="cartEmptyView" style="display: none;">
        <div class="cart-empty-icon">
          <i class="fas fa-basket-shopping"></i>
        </div>
        <h4 class="cart-empty-title">Your Cart is Currently Empty</h4>
        <p class="cart-empty-subtitle">Looks like you haven't added any products to your cart yet.</p>
        <button type="button" class="btn-cart-start-shopping" onclick="closeCartModal(); scrollToShop();">
          <i class="fas fa-arrow-left"></i>
          <span>Start Shopping</span>
        </button>
      </div>

    </div>

  </div>
</div>

<!-- Cart Toast -->
<div class="cart-toast" id="cartToast">
  <div class="cart-toast-icon">
    <i class="fas fa-check"></i>
  </div>
  <div class="cart-toast-body" id="cartToastMsg">Product added to cart!</div>
  <button type="button" class="cart-toast-btn" onclick="openCartModal()">View Cart</button>
</div>

<!-- ================= CART JAVASCRIPT LOGIC & PRICING ENGINE CLIENT ================= -->
<script>
  const CART_COOKIE_NAME = 'dms_cart';
  const CART_COUPONS_COOKIE_NAME = 'dms_applied_coupons';
  const CART_CALCULATE_URL = "{{ route('cart.calculate') }}";
  const CART_APPLY_COUPON_URL = "{{ route('cart.apply_coupon') }}";

  // --- Cookie Helper Functions ---
  function getCookieJson(name) {
    const prefix = name + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i].trim();
      if (c.indexOf(prefix) === 0) {
        try {
          return JSON.parse(c.substring(prefix.length, c.length));
        } catch (e) {
          return null;
        }
      }
    }
    // Fallback to localStorage
    try {
      const ls = localStorage.getItem(name);
      return ls ? JSON.parse(ls) : null;
    } catch (e) {
      return null;
    }
  }

  function setCookieJson(name, value) {
    const d = new Date();
    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 days
    const expires = "expires=" + d.toUTCString();
    const jsonStr = JSON.stringify(value);
    document.cookie = name + "=" + encodeURIComponent(jsonStr) + ";" + expires + ";path=/;SameSite=Lax";
    try {
      localStorage.setItem(name, jsonStr);
    } catch (e) {}
  }

  // --- Cart State Getters / Setters ---
  function getCartItems() {
    const cart = getCookieJson(CART_COOKIE_NAME);
    return Array.isArray(cart) ? cart : [];
  }

  function saveCartItems(items) {
    setCookieJson(CART_COOKIE_NAME, items);
  }

  function getAppliedCoupons() {
    const coupons = getCookieJson(CART_COUPONS_COOKIE_NAME);
    return (coupons && typeof coupons === 'object' && !Array.isArray(coupons)) ? coupons : {};
  }

  function saveAppliedCoupons(coupons) {
    setCookieJson(CART_COUPONS_COOKIE_NAME, coupons);
  }

  // --- Add to Cart Action ---
  function addToCart(btnElement) {
    const id = parseInt(btnElement.getAttribute('data-id'), 10);
    const name = btnElement.getAttribute('data-name') || '';

    if (!id || isNaN(id)) return;

    let items = getCartItems();
    const existingIndex = items.findIndex(item => parseInt(item.id, 10) === id);

    if (existingIndex > -1) {
      items[existingIndex].quantity = (parseInt(items[existingIndex].quantity, 10) || 1) + 1;
    } else {
      items.push({
        id: id,
        name: name,
        quantity: 1
      });
    }

    saveCartItems(items);
    showCartToast(name + ' added to your cart!');

    // Button animation feedback
    const originalHtml = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="fas fa-check"></i> Added';
    btnElement.style.opacity = '0.85';
    setTimeout(() => {
      btnElement.innerHTML = originalHtml;
      btnElement.style.opacity = '';
    }, 1000);

    // Sync live pricing with server & update navbar badges
    syncCartPricing();
  }

  function updateItemQuantity(id, delta) {
    let items = getCartItems();
    const index = items.findIndex(item => parseInt(item.id, 10) === parseInt(id, 10));
    if (index > -1) {
      items[index].quantity = (parseInt(items[index].quantity, 10) || 1) + delta;
      if (items[index].quantity <= 0) {
        items.splice(index, 1);
        // Also remove coupon if product is removed
        let coupons = getAppliedCoupons();
        delete coupons[id];
        saveAppliedCoupons(coupons);
      }
      saveCartItems(items);
      syncCartPricing(true);
    }
  }

  function removeCartItem(id) {
    let items = getCartItems();
    items = items.filter(item => parseInt(item.id, 10) !== parseInt(id, 10));
    saveCartItems(items);

    let coupons = getAppliedCoupons();
    delete coupons[id];
    saveAppliedCoupons(coupons);

    syncCartPricing(true);
  }

  function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
      saveCartItems([]);
      saveAppliedCoupons({});
      syncCartPricing(true);
    }
  }

  // --- Coupon Logic ---
  async function applyCouponForProduct(productId, couponCode) {
    if (!couponCode || !couponCode.trim()) {
      alert('Please enter a coupon code.');
      return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    try {
      const response = await fetch(CART_APPLY_COUPON_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          product_id: productId,
          coupon_code: couponCode.trim()
        })
      });

      const data = await response.json();

      if (response.ok && data.success) {
        let coupons = getAppliedCoupons();
        coupons[productId] = data.coupon_code;
        saveAppliedCoupons(coupons);

        showCartToast('Coupon "' + data.coupon_code + '" applied successfully!');
        syncCartPricing(true);
      } else {
        alert(data.message || 'Invalid or expired coupon code.');
      }
    } catch (error) {
      alert('Failed to apply coupon. Please try again.');
    }
  }

  function removeCouponForProduct(productId) {
    let coupons = getAppliedCoupons();
    delete coupons[productId];
    saveAppliedCoupons(coupons);
    showCartToast('Coupon removed.');
    syncCartPricing(true);
  }

  // --- PricingEngine Live Calculation Service ---
  let isCalculating = false;
  async function syncCartPricing(isModalOpen = false) {
    const items = getCartItems();
    const appliedCoupons = getAppliedCoupons();

    if (items.length === 0) {
      updateCartDisplay({
        items: [],
        total_items: 0,
        subtotal: 0,
        subtotal_formatted: '৳0.00',
        total_savings_formatted: '৳0.00',
        final_total_formatted: '৳0.00'
      });
      return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    try {
      isCalculating = true;
      const response = await fetch(CART_CALCULATE_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          items: items.map(i => ({
            product_id: i.id,
            quantity: i.quantity,
            coupon_code: appliedCoupons[i.id] || null
          })),
          applied_coupons: appliedCoupons
        })
      });

      if (!response.ok) throw new Error('Calculation failed');

      const data = await response.json();
      updateCartDisplay(data);
    } catch (e) {
      console.error('PricingEngine Calculation Error:', e);
    } finally {
      isCalculating = false;
    }
  }

  // --- Render DOM from Pricing Engine Response ---
  function updateCartDisplay(data) {
    const totalCount = data.total_items || 0;
    const finalTotalFormatted = data.final_total_formatted || '৳0.00';

    // 1. Update Navbar Badges
    document.querySelectorAll('.cart-count-badge').forEach(badge => {
      badge.textContent = totalCount;
      badge.style.display = totalCount > 0 ? 'inline-flex' : 'none';
    });

    document.querySelectorAll('.cart-total-text').forEach(totalEl => {
      totalEl.textContent = finalTotalFormatted;
    });

    // 2. Update Modal State
    const filledView = document.getElementById('cartFilledView');
    const emptyView = document.getElementById('cartEmptyView');
    const btnClear = document.getElementById('btnClearCart');
    const itemsList = document.getElementById('cartItemsList');

    if (!filledView || !emptyView || !itemsList) return;

    if (!data.items || data.items.length === 0) {
      filledView.style.display = 'none';
      emptyView.style.display = 'flex';
      if (btnClear) btnClear.style.display = 'none';
      return;
    }

    emptyView.style.display = 'none';
    filledView.style.display = 'grid';
    if (btnClear) btnClear.style.display = 'inline-flex';

    // 3. Render Calculated Items
    itemsList.innerHTML = '';
    data.items.forEach(item => {
      const row = document.createElement('div');
      row.className = 'cart-item-row';

      let couponHtml = '';
      if (item.coupon_applied) {
        const discountAmt = item.offer_discount_unit > 0
          ? ` <span style="font-weight:700; margin-left:4px;">-৳${Math.round(item.offer_discount_unit)}</span>`
          : '';
        couponHtml = `
          <div class="cart-item-coupon-pill">
            <i class="fas fa-tag"></i> Coupon "${escapeHtml(item.applied_coupon_code)}" Applied${discountAmt}
            <button type="button" class="btn-remove-item-coupon" onclick="removeCouponForProduct(${item.product_id})" title="Remove Coupon">&times;</button>
          </div>
        `;
      } else if (item.has_free_shipping) {
        couponHtml = `
          <div class="cart-item-coupon-pill" style="background: rgba(22,163,74,0.1); color:#16a34a; border-color: rgba(22,163,74,0.3);">
            <i class="fas fa-truck"></i> Free Shipping!
          </div>
        `;
      } else if (item.has_coupon_requirement) {
        couponHtml = `
          <div class="cart-item-coupon-input-wrap">
            <input type="text" class="cart-coupon-input" id="coupon_input_${item.product_id}" placeholder="${escapeHtml(item.available_coupon_code || 'CODE')}" autocomplete="off">
            <button type="button" class="btn-apply-item-coupon" onclick="applyCouponForProduct(${item.product_id}, document.getElementById('coupon_input_${item.product_id}').value)">Apply</button>
          </div>
        `;
      } else if (item.has_offer && item.offer_text) {
        couponHtml = `
          <div class="cart-item-coupon-pill" style="background: rgba(59, 130, 246, 0.1); color: #2563eb; border-color: rgba(59, 130, 246, 0.25);">
            <i class="fas fa-bolt"></i> ${escapeHtml(item.offer_text)}
          </div>
        `;
      }

      row.innerHTML = `
        <div class="cart-item-img-wrap">
          ${item.image ? `<img src="${item.image}" alt="${escapeHtml(item.name)}" onerror="this.parentElement.innerHTML='<i class=\\'fas fa-box placeholder-icon\\'></i>'">` : `<i class="fas fa-box placeholder-icon"></i>`}
        </div>
        <div class="cart-item-info">
          <h5 class="cart-item-title" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</h5>
          
          <div class="cart-item-unit-price">Unit Price: ${item.unit_price_formatted}</div>
          ${couponHtml}
        </div>
        <div class="cart-item-actions-mobile">
          <div class="cart-qty-ctrl">
            <button type="button" class="cart-qty-btn" onclick="updateItemQuantity(${item.product_id}, -1)" aria-label="Decrease quantity">-</button>
            <span class="cart-qty-val">${item.quantity}</span>
            <button type="button" class="cart-qty-btn" onclick="updateItemQuantity(${item.product_id}, 1)" aria-label="Increase quantity">+</button>
          </div>
          <div class="cart-item-price">${item.subtotal_formatted}</div>
        </div>
        <button type="button" class="cart-item-remove-btn" onclick="removeCartItem(${item.product_id})" title="Remove item" aria-label="Remove item">
          <i class="fas fa-times"></i>
        </button>
      `;
      itemsList.appendChild(row);
    });

    // 4. Update Summary Card
    const itemsCountEl = document.getElementById('cartSummaryItemsCount');
    const subtotalEl = document.getElementById('cartSummarySubtotal');
    const savingsRow = document.getElementById('cartSavingsRow');
    const savingsEl = document.getElementById('cartSummarySavings');
    const totalEl = document.getElementById('cartSummaryTotal');

    if (itemsCountEl) itemsCountEl.textContent = data.total_items;
    if (subtotalEl) subtotalEl.textContent = data.subtotal_formatted;
    
    if (data.total_savings > 0) {
      if (savingsRow) savingsRow.style.display = 'flex';
      if (savingsEl) savingsEl.textContent = '-' + data.total_savings_formatted;
    } else {
      if (savingsRow) savingsRow.style.display = 'none';
    }

    // Free Shipping row in summary
    const freeShippingRow = document.getElementById('cartFreeShippingRow');
    if (freeShippingRow) {
      freeShippingRow.style.display = data.cart_has_free_shipping ? 'flex' : 'none';
    }

    if (totalEl) totalEl.textContent = data.final_total_formatted;
  }

  // --- Modal Open / Close ---
  function openCartModal() {
    syncCartPricing(true);
    const modal = document.getElementById('cartModalOverlay');
    if (modal) {
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeCartModal() {
    const modal = document.getElementById('cartModalOverlay');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  function handleCartOverlayClick(e) {
    if (e.target.id === 'cartModalOverlay') {
      closeCartModal();
    }
  }

  function proceedToCheckout() {
    const items = getCartItems();
    if (items.length === 0) {
      showCartToast('Your cart is empty. Please add items before checking out.');
      return;
    }
    
    closeCartModal();
    window.location.href = "{{ route('checkout.index') }}";
  }

  function scrollToShop() {
    const productsSection = document.getElementById('products');
    if (productsSection) {
      productsSection.scrollIntoView({ behavior: 'smooth' });
    } else {
      window.location.href = "{{ route('home-page') }}#products";
    }
  }

  // --- Toast ---
  let toastTimer = null;
  function showCartToast(msg) {
    const toast = document.getElementById('cartToast');
    const toastMsg = document.getElementById('cartToastMsg');
    if (!toast) return;
    if (toastMsg) toastMsg.textContent = msg;

    toast.classList.add('show');
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  }

  function escapeHtml(string) {
    if (!string) return '';
    return String(string)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // --- Init on Page Load ---
  document.addEventListener('DOMContentLoaded', () => {
    syncCartPricing();

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeCartModal();
      }
    });
  });
</script>
