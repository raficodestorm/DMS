@extends('layouts.customerlayout')

@section('content')
<style>
  /* ── Wholesale Shop Wrapper ───────────────────────────────────── */
  .cust-shop-wrapper {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding-bottom: 85px; /* Space for sticky checkout bar */
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
  }

  /* ── Top Filter Card ──────────────────────────────────────────── */
  .cust-shop-top-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .cust-shop-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }

  .cust-shop-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Cinzel', serif;
  }

  .cust-branch-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: 12px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 50px;
    border: 1px solid var(--primary);
  }

  /* ── Search & Supplier Controls ───────────────────────────────── */
  .cust-filter-controls-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }

  @media (min-width: 768px) {
    .cust-filter-controls-grid {
      grid-template-columns: 2fr 1.2fr;
    }
  }

  .cust-search-box {
    position: relative;
    width: 100%;
  }

  .cust-search-box i.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
  }

  .cust-search-input {
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

  .cust-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--section-bg);
  }

  .cust-search-clear {
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

  .cust-supplier-select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1.5px solid var(--border-color);
    background: var(--background);
    color: var(--text-main);
    font-size: 13.5px;
    font-weight: 600;
    outline: none;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .cust-supplier-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
  }

  /* ── Category Tabs ────────────────────────────────────────────── */
  .cust-tabs-scroll {
    display: flex;
    gap: 7px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
  }

  .cust-tab-pill {
    padding: 6px 14px;
    border-radius: 20px;
    background: var(--background);
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
  }

  .cust-tab-pill:hover,
  .cust-tab-pill.active {
    background: var(--primary);
    color: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 2px 8px var(--primary-soft);
  }

  /* ── Product Grid ─────────────────────────────────────────────── */
  .cust-product-grid-wrap {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    min-height: 400px;
  }

  .cust-grid-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color);
  }

  .cust-grid-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .cust-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
    gap: 14px;
  }

  @media (max-width: 576px) {
    .cust-products-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
    }
  }

  /* ── Compact Product Card ─────────────────────────────────────── */
  .cust-prod-card {
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 12px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    position: relative;
    user-select: none;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease, border-color 0.2s ease;
  }

  .cust-prod-card:hover {
    border-color: var(--primary);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
  }

  .cust-prod-badge-wrap {
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

  .cust-badge-offer {
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

  .cust-badge-stock {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.65);
    color: #ffffff;
    backdrop-filter: blur(4px);
  }

  .cust-badge-stock.out-stock {
    background: rgba(239, 68, 68, 0.85);
  }

  .cust-img-box {
    width: 100%;
    height: 110px;
    border-radius: 8px;
    overflow: hidden;
    background: var(--section-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
  }

  .cust-img-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.25s ease;
  }

  .cust-prod-card:hover .cust-img-box img {
    transform: scale(1.06);
  }

  .cust-prod-title {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-main);
    line-height: 1.35;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 34px;
  }

  .cust-price-group {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 8px;
    gap: 4px;
  }

  .cust-selling-rate {
    font-size: 14px;
    font-weight: 800;
    color: #10b981;
  }

  .cust-base-rate {
    font-size: 10.5px;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: line-through;
  }

  .cust-card-actions {
    margin-top: auto;
  }

  .btn-cust-add-cart {
    width: 100%;
    padding: 7px 10px;
    border-radius: 8px;
    background: var(--primary);
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
  }

  .btn-cust-add-cart:hover {
    background: #2323e6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--primary-soft);
  }

  .btn-cust-add-cart:active {
    transform: scale(0.96);
  }

  .btn-cust-add-cart.out-of-stock {
    background: var(--border-color);
    color: var(--text-muted);
    cursor: not-allowed;
  }

  /* ── Sticky Bottom Checkout Bar ───────────────────────────────── */
  .cust-sticky-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--section-bg);
    border-top: 2px solid var(--primary);
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    backdrop-filter: blur(8px);
    transition: transform 0.25s ease;
  }

  .cust-bar-info {
    display: flex;
    flex-direction: column;
  }

  .cust-bar-count {
    font-size: 11.5px;
    color: var(--text-muted);
    font-weight: 600;
  }

  .cust-bar-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--primary);
    line-height: 1.1;
  }

  .cust-btn-checkout {
    background: linear-gradient(135deg, var(--primary), #00b4d8);
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

  .cust-btn-checkout:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px var(--primary-soft);
  }
</style>

<div class="container-fluid px-1 px-md-2">
  <div class="cust-shop-wrapper">

    {{-- Top Filter Card --}}
    <div class="cust-shop-top-card">
      <div class="cust-shop-header-row">
        <div>
          <h2 class="cust-shop-title">
            <i class="fas fa-store"></i> Wholesale Product Shop
          </h2>
          <p class="small text-muted m-0">Direct stock products with wholesale deduction & offers</p>
        </div>
        <span class="cust-branch-badge">
          <i class="fas fa-building"></i> {{ $customer->branch?->name ?? 'Main' }} Branch Stock
        </span>
      </div>

      {{-- Search & Supplier Filter --}}
      <div class="cust-filter-controls-grid">
        <div class="cust-search-box">
          <i class="fas fa-search search-icon"></i>
          <input type="text" id="custShopSearchInput" class="cust-search-input" placeholder="Search products by name..." autocomplete="off">
          <button type="button" id="custShopSearchClear" class="cust-search-clear" onclick="clearCustSearch()"><i class="fas fa-times"></i></button>
        </div>

        <div>
          <select id="custSupplierSelect" class="cust-supplier-select" onchange="filterCustProducts()">
            <option value="all">-- All Brands --</option>
            @foreach($suppliers as $s)
              @php
                $ded = (float) ($s->deduction?->customer_deduction ?? ($s->deductions->first()?->customer_deduction ?? 0));
              @endphp
              <option value="{{ $s->id }}" data-deduction="{{ $ded }}">
                {{ $s->company_name ?? $s->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Category Tabs --}}
      <div class="cust-tabs-scroll">
        <span class="cust-tab-pill active" data-tab="all" onclick="selectCustTab('all', this)">
          <i class="fas fa-boxes-stacked"></i> All Products
        </span>
        <span class="cust-tab-pill" data-tab="offer" onclick="selectCustTab('offer', this)">
          <i class="fas fa-bolt text-warning"></i> Wholesale Offers
        </span>
        @foreach($categories as $catId => $catName)
          <span class="cust-tab-pill" data-tab="cat_{{ $catId }}" onclick="selectCustTab('cat_{{ $catId }}', this)">
            {{ $catName }}
          </span>
        @endforeach
      </div>
    </div>

    {{-- Product Catalog Grid --}}
    <div class="cust-product-grid-wrap">
      <div class="cust-grid-header">
        <h4 class="cust-grid-title">
          <i class="fas fa-cubes text-primary"></i> Available Stock Items
        </h4>
        <span class="badge bg-light text-dark border" style="font-size: 11px; font-weight: 600;">
          Showing <span id="custShowingCount">{{ count($products) }}</span> items
        </span>
      </div>

      <div class="cust-products-grid" id="custProductsGrid">
        @forelse($products as $p)
          @php
            $hasOffer = !empty($p['has_offer']) && !empty($p['offer_text']);
            $hasDeduction = ($p['customer_deduction'] > 0 && $p['selling_rate'] < $p['price']);
            $isInStock = ($p['available_qty'] > 0);
            $imageSrc = (!empty($p['image']) && $p['image'] !== 'null') 
              ? (str_starts_with($p['image'], 'uploads/') ? '/' . $p['image'] : '/uploads/' . $p['image']) 
              : null;
          @endphp
          <div class="cust-prod-card" 
               id="custProdCard_{{ $p['id'] }}"
               data-id="{{ $p['id'] }}" 
               data-name="{{ strtolower($p['name']) }}" 
               data-raw-name="{{ $p['name'] }}"
               data-category-id="{{ $p['category_id'] }}"
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
               data-image="{{ $imageSrc ? asset($imageSrc) : '' }}">
            
            <div class="cust-prod-badge-wrap">
              <span class="cust-badge-stock {{ $isInStock ? '' : 'out-stock' }}">
                <i class="fas fa-cubes"></i> {{ $p['available_qty'] }}
              </span>
              @if($hasOffer)
                <span class="cust-badge-offer"><i class="fas fa-bolt"></i> {{ $p['offer_text'] }}</span>
              @elseif($hasDeduction)
                <span class="badge bg-light text-dark border" style="font-size:9px; font-weight:700;">-{{ (float)$p['customer_deduction'] }}%</span>
              @endif
            </div>

            <div class="cust-img-box">
              @if($imageSrc)
                <img src="{{ asset($imageSrc) }}" alt="{{ $p['name'] }}" loading="lazy" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=3131ff&color=fff'">
              @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=3131ff&color=fff" alt="{{ $p['name'] }}" loading="lazy">
              @endif
            </div>

            <div class="cust-prod-title" title="{{ $p['name'] }}">{{ $p['name'] }}</div>

            <div class="cust-price-group">
              <span class="cust-selling-rate">৳{{ number_format($p['selling_rate']) }}</span>
              @if($hasDeduction)
                <span class="cust-base-rate">৳{{ number_format($p['price']) }}</span>
              @endif
            </div>

            <div class="cust-card-actions">
              @if($isInStock)
                <button type="button" class="btn-cust-add-cart" onclick="addToCustomerCart(this, {{ $p['id'] }})">
                  <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
              @else
                <button type="button" class="btn-cust-add-cart out-of-stock" disabled>
                  <i class="fas fa-ban"></i> Out of Stock
                </button>
              @endif
            </div>
          </div>
        @empty
          <div style="grid-column: 1 / -1; text-align: center; padding: 50px 10px; color: var(--text-muted);">
            <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.4;"></i>
            <p class="m-0">No products available in your branch stock.</p>
          </div>
        @endforelse
      </div>

      <div id="custNoProductsMsg" style="display:none; text-align: center; padding: 40px 10px; color: var(--text-muted);">
        <i class="fas fa-search" style="font-size: 36px; margin-bottom: 10px; opacity: 0.4;"></i>
        <p class="m-0">No matching products found.</p>
      </div>
    </div>

  </div>
</div>

{{-- Sticky Bottom Action Bar --}}
<div class="cust-sticky-bar" id="custStickyBar">
  <div class="cust-bar-info">
    <span class="cust-bar-count"><span id="custStickyCount">0</span> items in cart</span>
    <span class="cust-bar-total">৳ <span id="custStickyTotal">0</span></span>
  </div>
  <a href="{{ route('customer.orders.cart') }}" class="cust-btn-checkout" id="custCheckoutBtn">
    <i class="fas fa-shopping-cart"></i> View Cart & Checkout <i class="fas fa-arrow-right ms-1"></i>
  </a>
</div>

@endsection

@push('scripts')
<script>
  let selectedTab = 'all';
  let custCart = {};

  // Load Cart from localStorage
  function loadCustCart() {
    try {
      const saved = localStorage.getItem('customer_wholesale_cart');
      custCart = saved ? JSON.parse(saved) : {};
    } catch(e) {
      custCart = {};
    }
    updateCustStickyBar();
    if (typeof updateGlobalCustomerCartBadge === 'function') {
      updateGlobalCustomerCartBadge();
    }
  }

  // Save Cart to localStorage
  function saveCustCart() {
    localStorage.setItem('customer_wholesale_cart', JSON.stringify(custCart));
    updateCustStickyBar();
    if (typeof updateGlobalCustomerCartBadge === 'function') {
      updateGlobalCustomerCartBadge();
    }
  }

  // Add Product to Cart with Flying Animation
  function addToCustomerCart(btn, productId) {
    const card = document.getElementById('custProdCard_' + productId);
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

    if (!custCart[id]) {
      custCart[id] = {
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
      custCart[id].qty += 1;
    }

    saveCustCart();

    // Button feedback
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Added';
    btn.style.background = '#10b981';
    setTimeout(() => {
      btn.innerHTML = originalHtml;
      btn.style.background = '';
    }, 900);

    // Flying Image Animation to Navbar Cart Icon
    if (image) {
      flyImageToNavbarCart(card, image);
    }
  }

  // Fly Image Animation Function
  function flyImageToNavbarCart(card, imgSrc) {
    const srcImg = card.querySelector('.cust-img-box img');
    const cartTarget = document.getElementById('customerNavbarCartBtn') || document.querySelector('.customer-nav-cart') || document.getElementById('custCheckoutBtn');
    if (!cartTarget) return;

    const srcRect = srcImg ? srcImg.getBoundingClientRect() : card.getBoundingClientRect();
    const destRect = cartTarget.getBoundingClientRect();
    const size = Math.min(srcRect.width, srcRect.height, 80);

    const clone = document.createElement('img');
    clone.src = imgSrc;
    clone.className = 'fly-to-cart-clone';
    Object.assign(clone.style, {
      width: size + 'px',
      height: size + 'px',
      top: srcRect.top + (srcRect.height - size) / 2 + 'px',
      left: srcRect.left + (srcRect.width - size) / 2 + 'px',
      transition: 'none'
    });
    document.body.appendChild(clone);

    const dx = destRect.left + destRect.width / 2 - (srcRect.left + srcRect.width / 2);
    const dy = destRect.top + destRect.height / 2 - (srcRect.top + srcRect.height / 2);

    requestAnimationFrame(() => {
      clone.style.transition = 'transform 0.38s ease';
      clone.style.transform = 'translateY(-110px) scale(1.1)';

      setTimeout(() => {
        clone.style.transition = 'transform 0.52s cubic-bezier(0.4, 0, 0.6, 1), opacity 0.52s ease';
        clone.style.transform = `translate(${dx}px, ${dy}px) scale(0.15)`;
        clone.style.opacity = '0.1';

        setTimeout(() => {
          clone.remove();
          cartTarget.classList.add('cart-icon-pop');
          setTimeout(() => cartTarget.classList.remove('cart-icon-pop'), 400);
        }, 530);
      }, 380);
    });
  }

  // Update Sticky Bar summary
  function updateCustStickyBar() {
    let totalItems = 0;
    let totalQty = 0;
    let totalAmount = 0;

    for (let id in custCart) {
      const item = custCart[id];
      if (item && item.qty > 0) {
        totalItems++;
        totalQty += item.qty;
        const lineRate = Math.max(0, item.selling_rate - (item.discount || 0));
        totalAmount += lineRate * item.qty;
      }
    }

    document.getElementById('custStickyCount').textContent = `${totalItems} items (${totalQty} pcs)`;
    document.getElementById('custStickyTotal').textContent = Math.round(totalAmount).toLocaleString('en-US');
  }

  // Filter Products
  function filterCustProducts() {
    const supplierId = document.getElementById('custSupplierSelect').value;
    const searchVal = document.getElementById('custShopSearchInput').value.trim().toLowerCase();
    const clearBtn = document.getElementById('custShopSearchClear');
    clearBtn.style.display = searchVal ? 'block' : 'none';

    let visibleCount = 0;
    const cards = document.querySelectorAll('.cust-prod-card');

    cards.forEach(card => {
      const cardSupplier = card.dataset.supplierId;
      const cardCategory = card.dataset.categoryId;
      const cardHasOffer = card.dataset.hasOffer === '1';
      const cardName = card.dataset.name;

      const matchSupplier = (supplierId === 'all' || cardSupplier === supplierId);
      let matchTab = false;
      if (selectedTab === 'all') {
        matchTab = true;
      } else if (selectedTab === 'offer') {
        matchTab = cardHasOffer;
      } else if (selectedTab.startsWith('cat_')) {
        const catId = selectedTab.replace('cat_', '');
        matchTab = (cardCategory === catId);
      }

      const matchSearch = (!searchVal || cardName.includes(searchVal));

      if (matchSupplier && matchTab && matchSearch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    document.getElementById('custShowingCount').textContent = visibleCount;
    document.getElementById('custNoProductsMsg').style.display = (visibleCount === 0) ? 'block' : 'none';
  }

  function selectCustTab(tab, el) {
    selectedTab = tab;
    document.querySelectorAll('.cust-tab-pill').forEach(pill => pill.classList.remove('active'));
    if (el) el.classList.add('active');
    filterCustProducts();
  }

  function clearCustSearch() {
    document.getElementById('custShopSearchInput').value = '';
    filterCustProducts();
  }

  document.addEventListener('DOMContentLoaded', function() {
    loadCustCart();

    const searchInput = document.getElementById('custShopSearchInput');
    if (searchInput) {
      searchInput.addEventListener('input', filterCustProducts);
    }
  });
</script>
@endpush
