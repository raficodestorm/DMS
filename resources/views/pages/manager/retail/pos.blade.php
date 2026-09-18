@extends('layouts.managerlayout')

@section('content')
<style>
  /* ── POS Overall Container Layout ───────────────────────────── */
  .pos-container {
    display: flex;
    gap: 16px;
    height: calc(100vh - 110px);
    min-height: 580px;
    margin-top: 5px;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
  }

  /* ── Left Side: Product Catalog ────────────────────────────── */
  .pos-catalog-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px var(--glass);
  }

  .pos-catalog-header {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 12px;
    flex-shrink: 0;
  }

  .pos-search-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .pos-search-box {
    position: relative;
    flex: 1;
  }

  .pos-search-box i.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
  }

  .pos-search-input {
    width: 100%;
    padding: 10px 36px 10px 38px;
    border-radius: 10px;
    border: 1.5px solid var(--border-color);
    background: var(--background);
    color: var(--text-main);
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.2s ease;
    outline: none;
  }

  .pos-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--section-bg);
  }

  .pos-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 13px;
    cursor: pointer;
    display: none;
  }

  .pos-categories-pills {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: none;
  }

  .pos-categories-pills::-webkit-scrollbar {
    display: none;
  }

  .pos-cat-pill {
    padding: 5px 12px;
    border-radius: 20px;
    background: var(--background);
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
    user-select: none;
  }

  .pos-cat-pill:hover,
  .pos-cat-pill.active {
    background: var(--primary);
    color: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 2px 8px var(--primary-soft);
  }

  /* ── Product Grid ─────────────────────────────────────────── */
  .pos-product-grid {
    flex: 1;
    overflow-y: auto;
    padding-right: 4px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    align-content: start;
  }

  .pos-product-grid::-webkit-scrollbar {
    width: 5px;
  }

  .pos-product-grid::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 10px;
  }

  /* ── Compact Product Card ─────────────────────────────────── */
  .pos-prod-card {
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    padding: 8px;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    position: relative;
    user-select: none;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .pos-prod-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px var(--glass);
    background: var(--section-bg);
  }

  .pos-prod-card:active {
    transform: scale(0.97);
  }

  .pos-prod-card.in-cart {
    border-color: var(--primary);
    background: var(--primary-soft);
  }

  .pos-prod-card.out-of-stock {
    opacity: 0.55;
    cursor: not-allowed;
    pointer-events: none;
  }

  .pos-prod-badge-wrap {
    position: absolute;
    top: 6px;
    left: 6px;
    right: 6px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    z-index: 2;
    pointer-events: none;
  }

  .pos-stock-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.65);
    color: #ffffff;
    backdrop-filter: blur(4px);
  }

  .pos-offer-badge {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 4px;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .pos-img-container {
    width: 100%;
    height: 95px;
    border-radius: 6px;
    overflow: hidden;
    background: var(--section-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 6px;
  }

  .pos-img-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.2s ease;
  }

  .pos-prod-card:hover .pos-img-container img {
    transform: scale(1.06);
  }

  .pos-prod-title {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-main);
    line-height: 1.25;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 28px;
  }

  .pos-price-group {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-top: auto;
    gap: 4px;
  }

  .pos-selling-rate {
    font-size: 13px;
    font-weight: 800;
    color: #10b981;
  }

  .pos-base-rate {
    font-size: 10px;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: line-through;
  }

  .pos-cart-qty-indicator {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px var(--primary-soft);
  }

  .pos-prod-card.in-cart .pos-cart-qty-indicator {
    display: flex;
  }

  /* ── Right Side: POS Bill & Cart Terminal ──────────────────── */
  .pos-cart-panel {
    width: 390px;
    min-width: 360px;
    max-width: 440px;
    display: flex;
    flex-direction: column;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px var(--glass);
  }

  .pos-cart-header {
    padding: 12px 14px;
    background: var(--section-bg);
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
  }

  .pos-cart-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .pos-cart-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .pos-btn-clear-cart {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    padding: 3px 8px;
    border-radius: 6px;
    transition: background 0.15s ease;
  }

  .pos-btn-clear-cart:hover {
    background: rgba(239, 68, 68, 0.1);
  }

  /* Customer Information Box */
  .pos-cust-box {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 8px 10px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }

  .pos-cust-field {
    display: flex;
    flex-direction: column;
  }

  .pos-cust-field label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .pos-cust-field input {
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 12px;
    outline: none;
    transition: border-color 0.2s ease;
  }

  .pos-cust-field input:focus {
    border-color: var(--primary);
  }

  /* ── Cart Items List ──────────────────────────────────────── */
  .pos-cart-body {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .pos-cart-body::-webkit-scrollbar {
    width: 5px;
  }

  .pos-cart-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 10px;
  }

  .pos-cart-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    text-align: center;
    padding: 30px 15px;
  }

  .pos-cart-empty-state i {
    font-size: 42px;
    margin-bottom: 12px;
    opacity: 0.35;
    color: var(--primary);
  }

  .pos-cart-item {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    animation: fadeIn 0.15s ease-out;
  }

  .pos-cart-item-info {
    flex: 1;
    min-width: 0;
  }

  .pos-cart-item-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
  }

  .pos-cart-item-price-row {
    font-size: 11px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .pos-cart-item-rate {
    color: #10b981;
    font-weight: 700;
  }

  .pos-cart-item-disc-tag {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 600;
  }

  .pos-cart-item-controls {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .pos-qty-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.15s ease;
  }

  .pos-qty-btn:hover {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
  }

  .pos-qty-input {
    width: 36px;
    height: 24px;
    text-align: center;
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    outline: none;
    -moz-appearance: textfield;
  }

  .pos-qty-input::-webkit-outer-spin-button,
  .pos-qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .pos-cart-item-subtotal {
    font-size: 12.5px;
    font-weight: 800;
    color: var(--text-main);
    min-width: 55px;
    text-align: right;
  }

  .pos-cart-item-del {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    padding: 3px;
    margin-left: 2px;
    transition: color 0.15s;
  }

  .pos-cart-item-del:hover {
    color: #ef4444;
  }

  /* ── Cart Bottom / Summary ────────────────────────────────── */
  .pos-cart-footer {
    padding: 12px 14px;
    background: var(--section-bg);
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .pos-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--text-muted);
  }

  .pos-summary-row.special-disc-row {
    background: var(--background);
    padding: 6px 8px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
  }

  .pos-special-disc-input {
    width: 90px;
    padding: 4px 6px;
    font-size: 12px;
    font-weight: 700;
    text-align: right;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--section-bg);
    color: var(--text-main);
    outline: none;
  }

  .pos-special-disc-input:focus {
    border-color: var(--primary);
  }

  .pos-net-total-box {
    background: var(--primary-soft);
    border: 1.5px solid var(--primary);
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2px;
  }

  .pos-net-total-box .label {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
  }

  .pos-net-total-box .val {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary);
    line-height: 1;
  }

  .pos-btn-submit {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    transition: all 0.2s ease;
  }

  .pos-btn-submit:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
  }

  .pos-btn-submit:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    opacity: 0.6;
    box-shadow: none;
  }

  /* ── Responsive Behavior ──────────────────────────────────── */
  @media (max-width: 991px) {
    .pos-container {
      flex-direction: column;
      height: auto;
    }

    .pos-cart-panel {
      width: 100%;
      max-width: 100%;
    }

    .pos-product-grid {
      max-height: 480px;
    }
  }
</style>

<div class="pos-container">
  {{-- Left Side: Product Catalog --}}
  <div class="pos-catalog-panel">
    <div class="pos-catalog-header">
      <div class="pos-search-row">
        <div class="pos-search-box">
          <i class="fas fa-search search-icon"></i>
          <input type="text" id="posSearchInput" class="pos-search-input" placeholder="Search products by name or scan barcode..." autocomplete="off" autofocus>
          <button type="button" id="posSearchClear" class="pos-search-clear"><i class="fas fa-times"></i></button>
        </div>
      </div>

      {{-- Category Filter Pills --}}
      <div class="pos-categories-pills">
        <span class="pos-cat-pill active" data-cat-id="all">
          <i class="fas fa-th-large me-1"></i> All (<span id="totalProductsCount">{{ count($products) }}</span>)
        </span>
        @foreach($categories as $catId => $catName)
          <span class="pos-cat-pill" data-cat-id="{{ $catId }}">{{ $catName }}</span>
        @endforeach
      </div>
    </div>

    {{-- Product Cards Grid --}}
    <div class="pos-product-grid" id="posProductGrid">
      @forelse($products as $p)
        @php
          $hasOffer = !empty($p['has_offer']) && !empty($p['offer_text']);
          $hasDeduction = ($p['retail_deduction'] > 0 && $p['selling_rate'] < $p['price']);
          $imageSrc = (!empty($p['image']) && $p['image'] !== 'null') 
            ? (str_starts_with($p['image'], 'uploads/') ? '/' . $p['image'] : '/uploads/' . $p['image']) 
            : null;
        @endphp
        <div class="pos-prod-card" 
             data-id="{{ $p['id'] }}" 
             data-name="{{ strtolower($p['name']) }}" 
             data-cat="{{ $p['category_id'] }}"
             data-price="{{ $p['price'] }}"
             data-selling-rate="{{ $p['selling_rate'] }}"
             data-stock="{{ $p['available_qty'] }}"
             data-offer-disc="{{ $p['offer_discount'] }}"
             data-offer-type="{{ $p['offer_type'] }}"
             onclick="addToPosCart({{ $p['id'] }})">
          
          <div class="pos-prod-badge-wrap">
            <span class="pos-stock-badge"><i class="fas fa-cubes"></i> {{ $p['available_qty'] }}</span>
            @if($hasOffer)
              <span class="pos-offer-badge"><i class="fas fa-bolt"></i> {{ $p['offer_text'] }}</span>
            @elseif($hasDeduction)
              <span class="badge bg-light text-dark border" style="font-size:9px; font-weight:700;">-{{ (float)$p['retail_deduction'] }}%</span>
            @endif
          </div>

          <div class="pos-img-container">
            @if($imageSrc)
              <img src="{{ asset($imageSrc) }}" alt="{{ $p['name'] }}" loading="lazy" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=0202e2&color=fff'">
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=0202e2&color=fff" alt="{{ $p['name'] }}" loading="lazy">
            @endif
          </div>

          <div class="pos-prod-title" title="{{ $p['name'] }}">{{ $p['name'] }}</div>

          <div class="pos-price-group">
            <span class="pos-selling-rate">৳{{ number_format($p['selling_rate']) }}</span>
            @if($hasDeduction)
              <span class="pos-base-rate">৳{{ number_format($p['price']) }}</span>
            @endif
          </div>

          <div class="pos-cart-qty-indicator" id="cardQtyBadge_{{ $p['id'] }}">0</div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px 10px; color: var(--text-muted);">
          <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.4;"></i>
          <p class="m-0">No products in stock for this branch.</p>
        </div>
      @endforelse
    </div>
  </div>

  {{-- Right Side: POS Bill & Cart Terminal --}}
  <div class="pos-cart-panel">
    {{-- Cart Header & Customer Inputs --}}
    <div class="pos-cart-header">
      <div class="pos-cart-title-row">
        <h3 class="pos-cart-title"><i class="fas fa-cash-register"></i> Retail Sale</h3>
        <button type="button" class="pos-btn-clear-cart" onclick="clearPosCart()"><i class="fas fa-trash-alt me-1"></i> Reset</button>
      </div>

      <div class="pos-cust-box">
        <div class="pos-cust-field">
          <label><i class="fas fa-user text-primary me-1"></i> Customer</label>
          <input type="text" id="posCustName" placeholder="Name (Optional)">
        </div>
        <div class="pos-cust-field">
          <label><i class="fas fa-phone text-primary me-1"></i> Phone</label>
          <input type="text" id="posCustPhone" placeholder="01XXXXXXXXX">
        </div>
      </div>
    </div>

    {{-- Cart Items Body --}}
    <div class="pos-cart-body" id="posCartBody">
      <div class="pos-cart-empty-state" id="posEmptyState">
        <i class="fas fa-shopping-basket"></i>
        <h6 class="fw-bold mb-1">Cart is Empty</h6>
        <p class="small m-0">Click any product from the catalog to start selling.</p>
      </div>
    </div>

    {{-- Cart Bottom / Financial Summary --}}
    <div class="pos-cart-footer">
      <div class="pos-summary-row">
        <span>Total Items:</span>
        <strong id="posSummaryItemCount">0 pcs (0 lines)</strong>
      </div>
      
      <div class="pos-summary-row">
        <span>Subtotal:</span>
        <strong id="posSummarySubtotal">৳ 0</strong>
      </div>

      <div class="pos-summary-row" id="posOfferDiscRow" style="display:none;">
        <span class="text-danger">Offer Discount:</span>
        <strong class="text-danger">- ৳ <span id="posSummaryOfferDisc">0</span></strong>
      </div>

      <div class="pos-summary-row special-disc-row">
        <span class="fw-bold text-warning"><i class="fas fa-tag me-1"></i> Special Discount:</span>
        <div>
          ৳ <input type="number" id="posSpecialDiscInput" class="pos-special-disc-input" placeholder="0" min="0" step="1" value="0" oninput="recalcPosTotals()">
        </div>
      </div>

      <div class="pos-net-total-box">
        <span class="label">Net Payable:</span>
        <span class="val">৳ <span id="posNetPayableDisplay">0</span></span>
      </div>

      {{-- Hidden Final Submission Form --}}
      <form method="POST" action="{{ route('manager.retail.store') }}" id="posOrderForm" style="display:none;">
        @csrf
        <input type="hidden" name="customer_name" id="formCustName">
        <input type="hidden" name="customer_phone" id="formCustPhone">
        <input type="hidden" name="special_discount" id="formSpecialDiscount" value="0">
        <input type="hidden" name="total_discount" id="formTotalDiscount" value="0">
        <input type="hidden" name="net_total" id="formNetTotal" value="0">
        <div id="formProductsWrapper"></div>
      </form>

      <button type="button" class="btn-submit" id="posBtnSubmit" onclick="confirmPosSale()" disabled>
        <i class="fas fa-check-circle"></i> Confirm Sale (৳ <span id="posBtnTotalDisplay">0</span>)
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // In-memory products database & cart state
  const allProductsList = @json($products);
  const productsMap = {};
  allProductsList.forEach(p => { productsMap[p.id] = p; });

  let posCart = {}; // { [productId]: { product, qty } }

  // ── Add to Cart ──────────────────────────────────────────────
  function addToPosCart(productId) {
    const prod = productsMap[productId];
    if (!prod) return;

    if (prod.available_qty <= 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Out of Stock', text: 'This item is not available in stock.', timer: 1500, showConfirmButton: false });
      }
      return;
    }

    if (!posCart[productId]) {
      posCart[productId] = {
        id: prod.id,
        name: prod.name,
        price: parseFloat(prod.price) || 0,
        selling_rate: parseFloat(prod.selling_rate) || 0,
        offer_discount: parseFloat(prod.offer_discount) || 0,
        offer_text: prod.offer_text,
        max_qty: parseInt(prod.available_qty) || 1,
        qty: 1
      };
    } else {
      if (posCart[productId].qty < posCart[productId].max_qty) {
        posCart[productId].qty += 1;
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `Max available stock reached (${prod.available_qty} pcs)`,
            showConfirmButton: false,
            timer: 1600
          });
        }
      }
    }

    renderPosCart();
  }

  // ── Update Item Quantity ────────────────────────────────────
  function updatePosCartQty(productId, change) {
    if (!posCart[productId]) return;

    let newQty = posCart[productId].qty + change;
    if (newQty <= 0) {
      removePosCartItem(productId);
      return;
    }

    if (newQty > posCart[productId].max_qty) {
      newQty = posCart[productId].max_qty;
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'info',
          title: `Max stock is ${posCart[productId].max_qty} pcs`,
          showConfirmButton: false,
          timer: 1500
        });
      }
    }

    posCart[productId].qty = newQty;
    renderPosCart();
  }

  // ── Direct Quantity Input ────────────────────────────────────
  function onPosQtyInput(productId, inputEl) {
    if (!posCart[productId]) return;

    let val = parseInt(inputEl.value);
    if (isNaN(val) || val <= 0) {
      val = 1;
    }

    if (val > posCart[productId].max_qty) {
      val = posCart[productId].max_qty;
      inputEl.value = val;
    }

    posCart[productId].qty = val;
    renderPosCart();
  }

  // ── Remove Single Item ──────────────────────────────────────
  function removePosCartItem(productId) {
    if (posCart[productId]) {
      delete posCart[productId];
      renderPosCart();
    }
  }

  // ── Clear Entire Cart ───────────────────────────────────────
  function clearPosCart() {
    if (Object.keys(posCart).length === 0) return;
    posCart = {};
    $('#posSpecialDiscInput').val(0);
    renderPosCart();
  }

  // ── Render Cart UI ──────────────────────────────────────────
  function renderPosCart() {
    const $cartBody = $('#posCartBody');
    const items = Object.values(posCart);

    // Update product card indicators in catalog
    $('.pos-prod-card').removeClass('in-cart');
    $('.pos-cart-qty-indicator').text('0');

    if (items.length === 0) {
      $cartBody.html(`
        <div class="pos-cart-empty-state" id="posEmptyState">
          <i class="fas fa-shopping-basket"></i>
          <h6 class="fw-bold mb-1">Cart is Empty</h6>
          <p class="small m-0">Click any product from the catalog to start selling.</p>
        </div>
      `);
      $('#posBtnSubmit').prop('disabled', true);
    } else {
      let html = '';
      items.forEach(item => {
        // Highlight in catalog grid
        const $card = $(`.pos-prod-card[data-id="${item.id}"]`);
        $card.addClass('in-cart');
        $card.find('.pos-cart-qty-indicator').text(item.qty);

        const unitEffectivePrice = Math.max(0, item.selling_rate - item.offer_discount);
        const itemSubtotal = Math.round(unitEffectivePrice * item.qty);

        html += `
          <div class="pos-cart-item" data-id="${item.id}">
            <div class="pos-cart-item-info">
              <div class="pos-cart-item-title" title="${item.name}">${item.name}</div>
              <div class="pos-cart-item-price-row">
                <span class="pos-cart-item-rate">৳${Math.round(item.selling_rate)}</span>
                ${item.offer_discount > 0 ? `<span class="pos-cart-item-disc-tag">-${Math.round(item.offer_discount)}</span>` : ''}
                <span style="color:var(--text-muted);">x ${item.qty}</span>
              </div>
            </div>

            <div class="pos-cart-item-controls">
              <button type="button" class="pos-qty-btn" onclick="updatePosCartQty(${item.id}, -1)">-</button>
              <input type="number" class="pos-qty-input" value="${item.qty}" min="1" max="${item.max_qty}" onchange="onPosQtyInput(${item.id}, this)">
              <button type="button" class="pos-qty-btn" onclick="updatePosCartQty(${item.id}, 1)">+</button>
            </div>

            <div class="pos-cart-item-subtotal">৳${itemSubtotal}</div>

            <button type="button" class="pos-cart-item-del" title="Remove Item" onclick="removePosCartItem(${item.id})">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        `;
      });
      $cartBody.html(html);
      $('#posBtnSubmit').prop('disabled', false);
    }

    recalcPosTotals();
  }

  // ── Recalculate Totals (Rounded Integers) ────────────────────
  function recalcPosTotals() {
    const items = Object.values(posCart);
    let totalQty = 0;
    let rawSubtotal = 0;
    let totalOfferDiscount = 0;

    items.forEach(item => {
      totalQty += item.qty;
      rawSubtotal += (item.selling_rate * item.qty);
      totalOfferDiscount += (item.offer_discount * item.qty);
    });

    let specialDisc = parseFloat($('#posSpecialDiscInput').val()) || 0;
    if (specialDisc < 0) specialDisc = 0;

    let subtotalRounded = Math.round(rawSubtotal);
    let offerDiscRounded = Math.round(totalOfferDiscount);
    let specialDiscRounded = Math.round(specialDisc);

    let effectiveAfterOffer = rawSubtotal - totalOfferDiscount;
    let finalNetPayable = Math.max(0, Math.round(effectiveAfterOffer - specialDiscRounded));
    let totalCombinedDiscount = offerDiscRounded + specialDiscRounded;

    // Summary UI Updates
    $('#posSummaryItemCount').text(`${totalQty} pcs (${items.length} lines)`);
    $('#posSummarySubtotal').text(`৳ ${subtotalRounded}`);

    if (offerDiscRounded > 0) {
      $('#posOfferDiscRow').show();
      $('#posSummaryOfferDisc').text(offerDiscRounded);
    } else {
      $('#posOfferDiscRow').hide();
    }

    $('#posNetPayableDisplay').text(finalNetPayable);
    $('#posBtnTotalDisplay').text(finalNetPayable);

    // Sync Hidden Form Fields
    $('#formSpecialDiscount').val(specialDiscRounded);
    $('#formTotalDiscount').val(totalCombinedDiscount);
    $('#formNetTotal').val(finalNetPayable);
  }

  // ── Instant Live Catalog Search & Filtering ─────────────────
  function filterCatalog() {
    const query = $('#posSearchInput').val().trim().toLowerCase();
    const activeCat = $('.pos-cat-pill.active').data('cat-id');

    if (query.length > 0) {
      $('#posSearchClear').show();
    } else {
      $('#posSearchClear').hide();
    }

    let matchCount = 0;
    $('.pos-prod-card').each(function() {
      const $card = $(this);
      const name = $card.data('name') || '';
      const cat = String($card.data('cat') || '');

      const matchesSearch = query === '' || name.includes(query);
      const matchesCat = activeCat === 'all' || cat === String(activeCat);

      if (matchesSearch && matchesCat) {
        $card.show();
        matchCount++;
      } else {
        $card.hide();
      }
    });
  }

  // ── Document Ready Event Bindings ───────────────────────────
  $(document).ready(function() {
    // Search input
    $('#posSearchInput').on('input', filterCatalog);

    $('#posSearchClear').on('click', function() {
      $('#posSearchInput').val('').focus();
      filterCatalog();
    });

    // Category pills click
    $('.pos-cat-pill').on('click', function() {
      $('.pos-cat-pill').removeClass('active');
      $(this).addClass('active');
      filterCatalog();
    });

    // Barcode scanning / Enter key in search box
    $('#posSearchInput').on('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const $visibleCards = $('.pos-prod-card:visible');
        if ($visibleCards.length === 1) {
          const id = $visibleCards.first().data('id');
          addToPosCart(id);
          $(this).val('');
          filterCatalog();
        }
      }
    });

    // Keyboard shortcuts (F2 to checkout)
    $(document).on('keydown', function(e) {
      if (e.key === 'F2') {
        e.preventDefault();
        if (!$('#posBtnSubmit').prop('disabled')) {
          confirmPosSale();
        }
      }
    });
  });

  // ── Checkout Confirmation & Submit ──────────────────────────
  function confirmPosSale() {
    const items = Object.values(posCart);
    if (items.length === 0) return;

    const custName = $('#posCustName').val().trim();
    const custPhone = $('#posCustPhone').val().trim();
    const netTotal = $('#posNetPayableDisplay').text().trim() || '0';

    $('#formCustName').val(custName);
    $('#formCustPhone').val(custPhone);

    // Build product hidden inputs
    let inputsHtml = '';
    items.forEach((item, idx) => {
      inputsHtml += `
        <input type="hidden" name="products[${idx}][product_id]" value="${item.id}">
        <input type="hidden" name="products[${idx}][price]" value="${item.price}">
        <input type="hidden" name="products[${idx}][selling_rate]" value="${item.selling_rate}">
        <input type="hidden" name="products[${idx}][discount]" value="${item.offer_discount}">
        <input type="hidden" name="products[${idx}][qty]" value="${item.qty}">
      `;
    });
    $('#formProductsWrapper').html(inputsHtml);

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '⚠️ নিশ্চিত করুন',
        html: `
          <div style="text-align: left; font-size: 14px; line-height: 1.8;">
            <div style="background: var(--primary-soft); border: 1px solid var(--primary); border-radius: 10px; padding: 12px; text-align: center; margin-bottom: 15px;">
              <span style="font-size: 13px; color: var(--primary); font-weight: 600; display: block;">কাস্টমারের কাছ থেকে নগদ গ্রহণ করুন:</span>
              <span style="font-size: 26px; color: var(--primary); font-weight: 800;">৳ ${netTotal}</span>
            </div>
            <p style="font-weight: 700; color: #dc2626; margin-bottom: 8px;">Retail Order সম্পর্কে গুরুত্বপূর্ণ তথ্য:</p>
            <ul style="padding-left: 18px; color: var(--text-main); margin-bottom: 0;">
              <li>এই Retail Order <strong>স্বয়ংক্রিয়ভাবে Delivered</strong> হিসেবে মার্ক হবে।</li>
              <li>Payment স্বয়ংক্রিয়ভাবে <strong>Paid</strong> হিসেবে মার্ক হবে।</li>
              <li>কাস্টমারের কাছ থেকে <strong>সাথে সাথে টাকা নিতে হবে।</strong></li>
              <li style="color: #dc2626; font-weight: 600;">⛔ Due রাখার কোনো সুযোগ নেই।</li>
            </ul>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✅ নিশ্চিত, Submit করুন',
        cancelButtonText: '← ফিরে যান',
        reverseButtons: true,
        width: 480
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('posOrderForm').submit();
        }
      });
    } else {
      if (confirm(`Total Payable Amount: ৳ ${netTotal}\n\nThis Retail Order will be auto-delivered and payment auto-marked as Paid. Collect payment immediately — no due allowed. Confirm?`)) {
        document.getElementById('posOrderForm').submit();
      }
    }
  }
</script>
@endpush
