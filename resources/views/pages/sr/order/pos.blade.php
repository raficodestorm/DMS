@extends('layouts.srlayout')

@section('content')
<style>
  /* ── POS Overall Container ──────────────────────────────────── */
  .sr-pos-wrapper {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 5px;
    padding-bottom: 80px; /* Space for sticky bottom cart bar */
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
  }

  /* ── Supplier & Filter Card ─────────────────────────────────── */
  .sr-pos-top-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 14px 16px;
    box-shadow: 0 4px 18px var(--glass);
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .sr-supplier-select-row {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .sr-supplier-select-row label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .sr-supplier-select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1.5px solid var(--border-color);
    background: var(--background);
    color: var(--text-main);
    font-size: 14px;
    font-weight: 600;
    outline: none;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .sr-supplier-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--section-bg);
  }

  /* ── Search & Filter Controls ────────────────────────────────── */
  .sr-pos-search-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .sr-pos-search-box {
    position: relative;
    flex: 1;
  }

  .sr-pos-search-box i.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
  }

  .sr-pos-search-input {
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

  .sr-pos-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--section-bg);
  }

  .sr-pos-search-clear {
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

  .sr-pos-tabs-row {
    display: flex;
    gap: 6px;
    padding-bottom: 2px;
  }

  .sr-pos-tab-pill {
    padding: 5px 12px;
    border-radius: 20px;
    background: var(--background);
    color: var(--text-muted);
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .sr-pos-tab-pill:hover,
  .sr-pos-tab-pill.active {
    background: var(--primary);
    color: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 2px 8px var(--primary-soft);
  }

  .sr-pos-tab-pill.active i.fa-bolt {
    color: #fde047 !important;
  }

  /* ── Products Grid ─────────────────────────────────────────── */
  .sr-pos-grid-container {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 14px;
    box-shadow: 0 4px 18px var(--glass);
    min-height: 400px;
  }

  .sr-pos-grid-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color);
  }

  .sr-pos-grid-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .sr-pos-product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    align-content: start;
  }

  @media (max-width: 576px) {
    .sr-pos-product-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
  }

  /* ── Product Card ─────────────────────────────────────────── */
  .sr-pos-prod-card {
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 12px;
    padding: 9px;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    position: relative;
    user-select: none;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .sr-pos-prod-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px var(--glass);
    background: var(--section-bg);
  }

  .sr-pos-prod-card:active {
    transform: scale(0.97);
  }

  .sr-pos-prod-card.in-cart {
    border-color: var(--primary);
    background: var(--primary-soft);
  }

  .sr-pos-prod-badge-wrap {
    position: absolute;
    top: 6px;
    left: 6px;
    right: 6px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    z-index: 2;
    pointer-events: none;
    gap: 4px;
  }

  .sr-pos-stock-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.65);
    color: #ffffff;
    backdrop-filter: blur(4px);
  }

  .sr-pos-stock-badge.out-stock {
    background: rgba(239, 68, 68, 0.85);
  }

  .sr-pos-offer-badge {
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
    gap: 2px;
  }

  .sr-pos-img-container {
    width: 100%;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    background: var(--section-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
  }

  .sr-pos-img-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.2s ease;
  }

  .sr-pos-prod-card:hover .sr-pos-img-container img {
    transform: scale(1.06);
  }

  .sr-pos-prod-title {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-main);
    line-height: 1.3;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 31px;
  }

  .sr-pos-price-group {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-top: auto;
    gap: 4px;
  }

  .sr-pos-selling-rate {
    font-size: 13.5px;
    font-weight: 800;
    color: #10b981;
  }

  .sr-pos-base-rate {
    font-size: 10px;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: line-through;
  }

  .sr-pos-cart-qty-indicator {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px var(--primary-soft);
    border: 1.5px solid #fff;
  }

  .sr-pos-prod-card.in-cart .sr-pos-cart-qty-indicator {
    display: flex;
  }

  /* ── Sticky Bottom Bar ──────────────────────────────────────── */
  .sr-pos-sticky-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--section-bg);
    border-top: 2px solid var(--primary);
    padding: 10px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    backdrop-filter: blur(8px);
    transition: transform 0.25s ease;
  }

  .sr-pos-bar-info {
    display: flex;
    flex-direction: column;
  }

  .sr-pos-bar-count {
    font-size: 11.5px;
    color: var(--text-muted);
    font-weight: 600;
  }

  .sr-pos-bar-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--primary);
    line-height: 1.1;
  }

  .sr-pos-btn-checkout {
    background: var(--primary);
    color: #ffffff;
    text-decoration: none;
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px var(--primary-soft);
    transition: all 0.2s ease;
  }

  .sr-pos-btn-checkout:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px var(--primary-soft);
  }

  /* Pop animation on add */
  @keyframes cartBounce {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
  }

  .animate-cart-pop {
    animation: cartBounce 0.3s ease-in-out;
  }
</style>

<div class="container-fluid px-2 px-md-3">
  <div class="sr-pos-wrapper">

    {{-- Top Controls Card --}}
    <div class="sr-pos-top-card">
      {{-- Supplier Selector --}}
      <div class="sr-supplier-select-row">
        <label for="srSupplierSelect"><i class="fas fa-truck text-primary"></i> Filter by Supplier</label>
        <select id="srSupplierSelect" class="sr-supplier-select" onchange="filterSrProducts()">
          <option value="all">-- All Suppliers --</option>
          @foreach($suppliers as $s)
            @php
              $ded = (float) ($s->deduction?->customer_deduction ?? ($s->deductions->first()?->customer_deduction ?? 0));
            @endphp
            <option value="{{ $s->id }}" data-deduction="{{ $ded }}">
              {{ $s->company_name ?? $s->name }} ({{ $ded }}% Wholesale Deduction)
            </option>
          @endforeach
        </select>
      </div>

      {{-- Search Input --}}
      <div class="sr-pos-search-row">
        <div class="sr-pos-search-box">
          <i class="fas fa-search search-icon"></i>
          <input type="text" id="srPosSearchInput" class="sr-pos-search-input" placeholder="Search wholesale products by name..." autocomplete="off">
          <button type="button" id="srPosSearchClear" class="sr-pos-search-clear" onclick="clearSrSearch()"><i class="fas fa-times"></i></button>
        </div>
      </div>

      {{-- Tabs: All Products & Offer --}}
      <div class="sr-pos-tabs-row">
        <span class="sr-pos-tab-pill active" data-tab="all" onclick="selectSrTab('all', this)">
          <i class="fas fa-boxes-stacked"></i> All Products
        </span>
        <span class="sr-pos-tab-pill" data-tab="offer" onclick="selectSrTab('offer', this)">
          <i class="fas fa-bolt text-warning"></i> Offer
        </span>
      </div>
    </div>

    {{-- Product Catalog Grid --}}
    <div class="sr-pos-grid-container">
      <div class="sr-pos-grid-header">
        <h4 class="sr-pos-grid-title">
          <i class="fas fa-box-open text-primary"></i> Product Catalog
        </h4>
        <span class="badge bg-light text-dark border" style="font-size: 11px; font-weight: 600;">
          Showing <span id="srShowingCount">{{ count($products) }}</span> items
        </span>
      </div>

      <div class="sr-pos-product-grid" id="srPosProductGrid">
        @forelse($products as $p)
          @php
            $hasOffer = !empty($p['has_offer']) && !empty($p['offer_text']);
            $hasDeduction = ($p['customer_deduction'] > 0 && $p['selling_rate'] < $p['price']);
            $imageSrc = (!empty($p['image']) && $p['image'] !== 'null') 
              ? (str_starts_with($p['image'], 'uploads/') ? '/' . $p['image'] : '/uploads/' . $p['image']) 
              : null;
          @endphp
          <div class="sr-pos-prod-card" 
               id="srProdCard_{{ $p['id'] }}"
               data-id="{{ $p['id'] }}" 
               data-name="{{ strtolower($p['name']) }}" 
               data-raw-name="{{ $p['name'] }}"
               data-supplier-id="{{ $p['supplier_id'] }}"
               data-supplier-name="{{ $p['supplier_name'] }}"
               data-has-offer="{{ $p['has_offer'] ? '1' : '0' }}"
               data-price="{{ $p['price'] }}"
               data-selling-rate="{{ $p['selling_rate'] }}"
               data-customer-deduction="{{ $p['customer_deduction'] }}"
               data-stock="{{ $p['available_qty'] }}"
               data-offer-disc="{{ $p['offer_discount'] }}"
               data-offer-text="{{ $p['offer_text'] }}"
               data-offer-type="{{ $p['offer_type'] }}"
               data-image="{{ $imageSrc ?? '' }}"
               onclick="addToSrCart({{ $p['id'] }})">
            
            <div class="sr-pos-prod-badge-wrap">
              <span class="sr-pos-stock-badge {{ $p['available_qty'] <= 0 ? 'out-stock' : '' }}">
                <i class="fas fa-cubes"></i> {{ $p['available_qty'] }}
              </span>
              @if($hasOffer)
                <span class="sr-pos-offer-badge"><i class="fas fa-bolt"></i> {{ $p['offer_text'] }}</span>
              @elseif($hasDeduction)
                <span class="badge bg-light text-dark border" style="font-size:9px; font-weight:700;">-{{ (float)$p['customer_deduction'] }}%</span>
              @endif
            </div>

            <div class="sr-pos-img-container">
              @if($imageSrc)
                <img src="{{ asset($imageSrc) }}" alt="{{ $p['name'] }}" loading="lazy" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=3131ff&color=fff'">
              @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=3131ff&color=fff" alt="{{ $p['name'] }}" loading="lazy">
              @endif
            </div>

            <div class="sr-pos-prod-title" title="{{ $p['name'] }}">{{ $p['name'] }}</div>

            <div class="sr-pos-price-group">
              <span class="sr-pos-selling-rate">৳{{ number_format($p['selling_rate']) }}</span>
              @if($hasDeduction)
                <span class="sr-pos-base-rate">৳{{ number_format($p['price']) }}</span>
              @endif
            </div>

            <div class="sr-pos-cart-qty-indicator" id="srCardQtyBadge_{{ $p['id'] }}">0</div>
          </div>
        @empty
          <div style="grid-column: 1 / -1; text-align: center; padding: 50px 10px; color: var(--text-muted);">
            <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.4;"></i>
            <p class="m-0">No wholesale products available.</p>
          </div>
        @endforelse
      </div>

      <div id="srNoProductsMsg" style="display:none; text-align: center; padding: 40px 10px; color: var(--text-muted);">
        <i class="fas fa-search" style="font-size: 36px; margin-bottom: 10px; opacity: 0.4;"></i>
        <p class="m-0">No matching products found.</p>
      </div>
    </div>

  </div>
</div>

{{-- Sticky Bottom Action Bar --}}
<div class="sr-pos-sticky-bar" id="srStickyBar">
  <div class="sr-pos-bar-info">
    <span class="sr-pos-bar-count"><span id="srStickyCount">0</span> items selected</span>
    <span class="sr-pos-bar-total">৳ <span id="srStickyTotal">0</span></span>
  </div>
  <a href="{{ route('sr.order.cart') }}" class="sr-pos-btn-checkout" id="srCheckoutBtn">
    <i class="fas fa-shopping-cart"></i> View Cart <i class="fas fa-arrow-right ms-1"></i>
  </a>
</div>

@endsection

@push('scripts')
<script>
  let selectedTab = 'all';
  let srCart = {};

  // Load Cart from localStorage
  function loadSrCart() {
    try {
      const saved = localStorage.getItem('sr_pos_cart');
      srCart = saved ? JSON.parse(saved) : {};
    } catch(e) {
      srCart = {};
    }
    updateAllCardBadges();
    updateStickyBar();
    if (typeof updateGlobalSrCartBadge === 'function') {
      updateGlobalSrCartBadge();
    }
  }

  // Save Cart to localStorage
  function saveSrCart() {
    localStorage.setItem('sr_pos_cart', JSON.stringify(srCart));
    updateAllCardBadges();
    updateStickyBar();
    if (typeof updateGlobalSrCartBadge === 'function') {
      updateGlobalSrCartBadge();
    }
  }

  // Add Product to Cart
  function addToSrCart(productId) {
    const card = document.getElementById('srProdCard_' + productId);
    if (!card) return;

    const id = card.dataset.id;
    const name = card.dataset.rawName || card.dataset.name;
    const price = parseFloat(card.dataset.price) || 0;
    const sellingRate = parseFloat(card.dataset.sellingRate) || 0;
    const customerDeduction = parseFloat(card.dataset.customerDeduction) || 0;
    const offerDisc = parseFloat(card.dataset.offerDisc) || 0;
    const offerText = card.dataset.offerText || '';
    const offerType = card.dataset.offerType || 'fixed';
    const supplierId = card.dataset.supplierId || '';
    const supplierName = card.dataset.supplierName || '';
    const image = card.dataset.image || '';
    const stock = parseInt(card.dataset.stock) || 0;

    if (!srCart[id]) {
      srCart[id] = {
        id: id,
        name: name,
        price: price,
        selling_rate: sellingRate,
        customer_deduction: customerDeduction,
        discount: offerDisc,
        offer_text: offerText,
        offer_type: offerType,
        supplier_id: supplierId,
        supplier_name: supplierName,
        image: image,
        stock: stock,
        qty: 1
      };
    } else {
      srCart[id].qty += 1;
    }

    saveSrCart();

    // Add pop visual animation
    card.classList.add('animate-cart-pop');
    setTimeout(() => card.classList.remove('animate-cart-pop'), 300);
  }

  // Update Badges on All Cards
  function updateAllCardBadges() {
    document.querySelectorAll('.sr-pos-prod-card').forEach(card => {
      const id = card.dataset.id;
      const badge = document.getElementById('srCardQtyBadge_' + id);
      if (srCart[id] && srCart[id].qty > 0) {
        card.classList.add('in-cart');
        if (badge) {
          badge.textContent = srCart[id].qty;
          badge.style.display = 'flex';
        }
      } else {
        card.classList.remove('in-cart');
        if (badge) {
          badge.style.display = 'none';
        }
      }
    });
  }

  // Update Sticky Bar summary
  function updateStickyBar() {
    let totalItems = 0;
    let totalQty = 0;
    let totalAmount = 0;

    for (let id in srCart) {
      const item = srCart[id];
      if (item && item.qty > 0) {
        totalItems++;
        totalQty += item.qty;
        const lineRate = Math.max(0, item.selling_rate - (item.discount || 0));
        totalAmount += lineRate * item.qty;
      }
    }

    document.getElementById('srStickyCount').textContent = totalQty;
    document.getElementById('srStickyTotal').textContent = Math.round(totalAmount).toLocaleString('en-US');
  }

  // Filter Products by Supplier, Tab (All / Offer), Search
  function filterSrProducts() {
    const supplierId = document.getElementById('srSupplierSelect').value;
    const searchVal = document.getElementById('srPosSearchInput').value.trim().toLowerCase();
    const clearBtn = document.getElementById('srPosSearchClear');
    clearBtn.style.display = searchVal ? 'block' : 'none';

    let visibleCount = 0;
    const cards = document.querySelectorAll('.sr-pos-prod-card');

    cards.forEach(card => {
      const cardSupplier = card.dataset.supplierId;
      const cardHasOffer = card.dataset.hasOffer === '1';
      const cardName = card.dataset.name;

      const matchSupplier = (supplierId === 'all' || cardSupplier === supplierId);
      const matchTab = (selectedTab === 'all' || (selectedTab === 'offer' && cardHasOffer));
      const matchSearch = (!searchVal || cardName.includes(searchVal));

      if (matchSupplier && matchTab && matchSearch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    document.getElementById('srShowingCount').textContent = visibleCount;
    document.getElementById('srNoProductsMsg').style.display = (visibleCount === 0) ? 'block' : 'none';
  }

  function selectSrTab(tab, el) {
    selectedTab = tab;
    document.querySelectorAll('.sr-pos-tab-pill').forEach(pill => pill.classList.remove('active'));
    if (el) el.classList.add('active');
    filterSrProducts();
  }

  function clearSrSearch() {
    document.getElementById('srPosSearchInput').value = '';
    filterSrProducts();
  }

  document.addEventListener('DOMContentLoaded', function() {
    loadSrCart();

    const searchInput = document.getElementById('srPosSearchInput');
    if (searchInput) {
      searchInput.addEventListener('input', filterSrProducts);
    }
  });
</script>
@endpush
