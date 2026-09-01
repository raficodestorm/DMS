@extends('layouts.managerlayout')

@section('content')
<style>
  .form-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 15px;
    box-shadow: 0 8px 32px var(--glass);
  }

  #product-wrapper {
    margin-right: -10px;
    margin-left: -10px;
    margin-top: 20px
  }

  .product-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 10px;
    position: relative;
    box-shadow: 0 2px 8px var(--glass);
    transition: all 0.3s ease;
    animation: fadeIn 0.3s ease-in-out;
  }

  .product-card:hover {
    border-color: var(--primary);
    box-shadow: 0 5px 15px var(--glass);
  }

  .card-layout {
    display: flex;
    gap: 12px;
  }

  .left-side {
    width: 32%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }

  .product-img-box {
    width: 100%;
    height: 80px;
    background: var(--background);
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--border-color);
  }

  .product-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .qty-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background: var(--background);
    border-radius: 6px;
    padding: 2px;
    border: 1px solid var(--border-color);
  }

  .qty-btn {
    width: 20px;
    height: 24px;
    border: none;
    background: var(--primary);
    color: white;
    border-radius: 4px;
    font-size: 8px;
    cursor: pointer;
  }

  .qty-input {
    width: 100%;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-main);
  }

  .right-side {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .product-name {
    font-weight: 700;
    font-size: 0.90rem;
    color: var(--text-main);
    line-height: 1.2;
    margin-bottom: 5px;
  }

  .price-info {
    font-size: 0.85rem;
    color: var(--text-muted);
  }

  .price-info b {
    color: var(--text-main);
  }

  .subtotal-badge {
    background: var(--primary-soft);
    color: var(--primary);
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 800;
    text-align: center;
  }

  .remove-card-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }

  .p-summary-card {
    background: var(--glass);
    color: var(--text-main);
    border-radius: 12px;
    padding: 20px;
    font-size: 15px margin-top: 30px;
    border: 1px solid var(--glass);
  }

  .summary-cont {
    width: 45%;
  }


  @media (max-width: 768px) {

    .p-summary-card {
      padding: 15px;
      margin-top: 20px;
    }

    /* মোবাইল ভিউতে দুই পাশের কলামকে ওপর-নিচে করা */
    .p-summary-card .row {
      flex-direction: column;
      text-align: center !important;
      gap: 15px;
      width: 100%;
    }

    .p-summary-card .col-6 {
      width: 100%;
      text-align: center !important;
    }

    .p-summary-card .col-6:first-child {
      border-bottom: 1px solid var(--glass);
      padding-bottom: 12px;
    }

    .p-summary-card h2 {
      font-size: 1.8rem;
      margin-top: 5px;
    }

    .p-summary-card small {
      font-size: 0.9rem;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
  }

  /* ── Product Live Search ───────────────────────────────────────── */
  .pls-container { position: relative; margin-top: 1rem; margin-bottom: 1rem; z-index: 99999; }
  .pls-input-wrap {
    display: flex; align-items: center;
    background: var(--section-bg);
    border: 2px solid var(--border-color);
    border-radius: 10px; padding: 0 14px;
    transition: border-color .2s, box-shadow .2s;
  }
  .pls-input-wrap:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(49,49,255,.12);
  }
  .pls-search-icon { color: var(--text-muted); margin-right: 10px; font-size: 15px; }
  .pls-input {
    flex: 1; border: none; outline: none; background: transparent;
    padding: 12px 0; font-size: 15px; color: var(--text-main);
  }
  .pls-input::placeholder { color: var(--text-muted); }
  .pls-spinner { color: var(--primary); font-size: 14px; margin-left: 8px; }
  .pls-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: var(--section-bg, #fff);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
    max-height: 280px; overflow-y: auto; z-index: 999999;
  }
  .pls-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid var(--border-color);
    transition: background .15s;
  }
  .pls-item:last-child { border-bottom: none; }
  .pls-item:hover, .pls-item:active { background: var(--primary-soft, #eef2ff); }
  .pls-item-name { font-weight: 600; font-size: 14px; color: var(--text-main); }
  .pls-item-stock {
    font-size: 12px; color: var(--text-muted);
    background: var(--background); padding: 2px 8px;
    border-radius: 20px; white-space: nowrap; margin-left: 8px;
  }
  .pls-empty { padding: 16px; text-align: center; color: var(--text-muted); font-size: 14px; }
  @media (max-width: 576px) { .pls-input { font-size: 16px; } }

  /* ── Customer Live Search ──────────────────────────────────────── */
  .cls-container { position: relative; z-index: 99999; }
  .cls-input-wrap {
    display: flex; align-items: center;
    background: var(--section-bg);
    border: 2px solid var(--border-color);
    border-radius: 10px; padding: 0 14px;
    transition: border-color .2s, box-shadow .2s;
  }
  .cls-input-wrap:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(49,49,255,.12);
  }
  .cls-search-icon { color: var(--text-muted); margin-right: 10px; font-size: 15px; }
  .cls-input {
    flex: 1; border: none; outline: none; background: transparent;
    padding: 12px 0; font-size: 15px; color: var(--text-main);
  }
  .cls-input::placeholder { color: var(--text-muted); }
  .cls-spinner { color: var(--primary); font-size: 14px; margin-left: 8px; }
  .cls-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: var(--section-bg, #fff);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
    max-height: 280px; overflow-y: auto; z-index: 999999;
  }
  .cls-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid var(--border-color);
    transition: background .15s;
  }
  .cls-item:last-child { border-bottom: none; }
  .cls-item:hover, .cls-item:active { background: var(--primary-soft, #eef2ff); }
  .cls-item-name { font-weight: 600; font-size: 14px; color: var(--text-main); }
  .cls-item-due {
    font-size: 12px; color: #d97706; font-weight: 700;
    background: rgba(245,158,11,.1); padding: 3px 10px;
    border-radius: 20px; white-space: nowrap; margin-left: 8px;
  }
  .cls-empty { padding: 16px; text-align: center; color: var(--text-muted); font-size: 14px; }
  .cls-selected-box {
    background: var(--section-bg);
    border: 2px solid var(--primary);
    border-radius: 10px;
    padding: 10px 14px;
    display: flex; align-items: center; justify-content: space-between;
  }
</style>

<div class="container py-4">
  <div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="m-0"><i class="fas fa-shopping-bag text-primary"></i> Edit Order BRS{{ $order->id }}</h2>
    </div>

    @include('components.alert')

    <form method="POST" action="{{ route('manager.order.update', $order->id) }}" id="orderForm">
      @csrf
      @method('PUT')
      @php
      $standard = $deductionSettings->customer_deduction ?? 0;
      $total = $order->applied_deduction_percent ?? 0;

      if ($total >= $standard) {
      $checked = true;
      $custom = $total - $standard;
      } else {
      $checked = false;
      $custom = $total;
      }
      @endphp
      <div class="deduction-control-card p-3 mb-4 border rounded bg-light shadow-sm">
        <div class="row align-items-center">

          <div class="col-md-6 mb-2 mb-md-0">
            <div class="form-check form-switch">

              <input class="form-check-input" type="checkbox" name="apply_global" id="applyGlobalDeduction"
                data-percentage="{{ $standard }}" {{ $checked ? 'checked' : '' }}>

              <label class="form-check-label fw-bold" for="applyGlobalDeduction">
                Apply Standard Deduction ({{ $standard }}%)
              </label>

            </div>
          </div>

          <div class="col-md-6">
            <div class="input-group">

              <span class="input-group-text bg-white">
                Custom Deduction %
              </span>

              <input type="number" name="applied_custom_deduction" id="customDeductionRate" class="form-control"
                placeholder="0.00" step="0.01" min="0" value="{{ $custom }}" onkeyup="refreshAllCards()">

            </div>
          </div>

        </div>
      </div>

      <div class="customer-section mb-4">
        <label class="form-label fw-bold">Select Shop / Customer</label>
        
        <input type="hidden" name="customer_id" id="selected_customer_id" value="{{ old('customer_id', $order->customer_id) }}" required>
        
        <div id="cls-selected-box" class="cls-selected-box mb-2">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <i class="fas fa-store text-primary fs-5"></i>
            <strong id="cls-selected-name" class="fs-6 text-dark">{{ $order->customer?->shop_name }}</strong>
            <span class="cls-item-due" id="cls-selected-due">Due: {{ $order->customer?->due ?: 0 }} TK</span>
          </div>
          <button type="button" class="btn btn-sm btn-outline-danger" id="cls-clear-btn">
            <i class="fas fa-times me-1"></i> Change
          </button>
        </div>

        <div class="cls-container" id="cls-container" style="display: none;">
          <div class="cls-input-wrap">
            <span class="cls-search-icon"><i class="fas fa-user-check"></i></span>
            <input
              type="text"
              id="cls-input"
              class="cls-input"
              placeholder="Type shop name to search customer or 2 spaces for all..."
              autocomplete="off"
              inputmode="search">
            <span class="cls-spinner" id="cls-spinner" style="display:none">
              <i class="fas fa-circle-notch fa-spin"></i>
            </span>
          </div>
          <div id="cls-dropdown" class="cls-dropdown" style="display:none"></div>
        </div>

        @error('customer_id') <div class="error-msg">{{ $message }}</div> @enderror
      </div>

      <div id="product-wrapper" class="row">
        @foreach($order->items as $item)
        <div class="col-12 col-md-6 col-lg-4 mb-3 product-card-container">
          <div class="product-card h-100" data-id="{{ $item->product_id }}">
            <div class="remove-card-btn" onclick="removeCard(this)"><i class="fas fa-times"></i></div>

            <div class="row g-2 align-items-center">
              <div class="col-4">
                <div class="product-img-box mb-2">
                  @php
                  $finalImgPath = ($item->product->image)
                  ? (str_starts_with($item->product->image, 'uploads/') ? asset($item->product->image) : asset('uploads/' . $item->product->image))
                  : "https://ui-avatars.com/api/?name=".urlencode($item->product->name)."&background=3131ff&color=fff";
                  @endphp
                  <img src="{{ $finalImgPath }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                </div>
                <div class="qty-controls d-flex align-items-center justify-content-between">
                  <button type="button" class="qty-btn" onclick="updateQty(this, -1)"><i
                      class="fas fa-minus"></i></button>
                  {{-- এখানে class "qty-input" আছে কিনা নিশ্চিত হোন --}}
                  <input type="number" name="products[{{ $loop->index }}][qty]" class="qty-input mx-1"
                    value="{{ $item->quantity }}" min="1" oninput="calculateCard(this)">
                  <button type="button" class="qty-btn" onclick="updateQty(this, 1)"><i
                      class="fas fa-plus"></i></button>
                </div>
              </div>

              <div class="col-8 d-flex flex-column justify-content-between" style="min-height: 110px;">
                <div class="ps-2">
                  <h6 class="product-name mb-1 fw-bold text-dark text-wrap">{{ $item->product->name }}</h6>
                  <div class="price-info small text-muted">Base Rate: <del class="base-price-display">{{
                      $item->price }}</del> ৳</div>
                  {{-- শুরুতে এটি ০ না রেখে বেস প্রাইস রাখা ভালো, পরে JS আপডেট করবে --}}
                  <div class="price-info small">Selling Rate: <b class="text-success selling-price-display">{{
                      number_format($item->selling_rate, 2) }} ৳</b></div>
                  <div class="price-info small text-muted">Offer Disc: <b class="text-dark">{{
                      number_format($item->discount_amount, 2) }} ৳</b></div>
                </div>

                <div class="mt-2 ps-2">
                  <div class="subtotal-badge w-100 py-1 text-center">
                    Total: <span class="card-subtotal">{{ number_format(($item->quantity * $item->selling_rate) ,
                      2)}}</span> ৳
                  </div>
                </div>

                <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                <input type="hidden" name="products[{{ $loop->index }}][price]" class="card-price"
                  value="{{ $item->price }}">
                <input type="hidden" name="products[{{ $loop->index }}][discount]" class="card-discount"
                  value="{{ $item->discount_amount }}">
                <input type="hidden" class="card-subtotal-val" value="0">
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="pls-container mb-4" id="pls-container">
        <label class="form-label fw-bold">Add Product</label>
        <div class="pls-input-wrap">
          <span class="pls-search-icon"><i class="fas fa-search"></i></span>
          <input
            type="text"
            id="pls-input"
            class="pls-input"
            placeholder="Type product name to search or 2 spaces for all..."
            autocomplete="off"
            inputmode="search">
          <span class="pls-spinner" id="pls-spinner" style="display:none">
            <i class="fas fa-circle-notch fa-spin"></i>
          </span>
        </div>
        <div id="pls-dropdown" class="pls-dropdown" style="display:none"></div>
      </div>

      <div class="p-summary-card">
        <div class="row align-items-center summary-cont">
          <div class="col-6">
            <p class="mb-1 opacity-75">Total Items: <span id="itemCount">0</span></p>

            <div class="input-group input-group-sm mt-2" style="max-width: 200px;">
              <span class="input-group-text bg-warning text-dark border-warning">Special Disc</span>
              <input type="number" name="special_discount" id="specialDiscountInput" class="form-control border-warning"
                value="{{ $order->special_discount }}" placeholder="0.00" step="0.01" min="0"
                oninput="calculateTotal()">
            </div>

            <p class="mb-0 opacity-75">Total Discount: <span id="totalDiscount">{{
                number_format($order->discount_amount, 2) }}</span> TK</p>
          </div>
          <div class="col-6 text-end">
            <small class="d-block opacity-75">Net Payable</small>
            <h2 class="mb-0" style="font-weight: 700; font-size: 28px; color: var(--primary);">
              <span id="netTotalDisplay">{{ number_format($order->net_total, 2) }}</span> ৳
            </h2>
          </div>
        </div>
        <input type="hidden" name="net_total" id="netTotalInput">
        <input type="hidden" name="total_discount" id="totalDiscountInput">

      </div>

      <button type="submit" class="btn-submit w-100 mt-4 py-3 shadow-lg">
        Update <i class="fas fa-check-circle ms-2"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let index = 0;
  // Optimized for Real-Time "Snappy" Updates
  $(document).ready(function() {
    // Triggers whenever the checkbox is toggled
    $('#applyGlobalDeduction').on('change', function() {
      refreshAllCards();
    });

    // Triggers immediately while typing, incrementing with arrows, or pasting
    $('#customDeductionRate').on('input', function() {
      $('#hiddenCustomDeduction').val($(this).val());
      refreshAllCards();
    });
  });

  function refreshAllCards() {
    // Find every qty input and run the calculation logic
    $('.qty-input').each(function() {
      calculateCard(this);
    });
  }


  function addProductCard(productId, name, stock, imageName) {
    if (typeof productId === 'object') {
      let el = productId;
      productId = $(el).val();
      if (!productId) return;
      let option = $(el).find(':selected');
      name = option.attr('data-name');
      stock = option.attr('data-stock');
      imageName = option.attr('data-image-name');
      $(el).val('');
    }

    if (!productId) return;

    if ($(`.product-card[data-id="${productId}"]`).length > 0) {
      alert('প্রোডাক্টটি অলরেডি লিস্টে আছে!');
      return;
    }

    name      = name      || '';
    stock     = parseInt(stock)  || 0;
    imageName = imageName || '';

    let finalImgPath = (imageName && imageName.trim() !== "" && imageName !== "null") ?
      (imageName.startsWith('uploads/') ? '/' + imageName : '/uploads/' + imageName) :
      `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3131ff&color=fff`;

    $.get(`/manager/get-product-data/${productId}`, function(data) {
      let disc = data.discount_type === 'percentage' ? (data.price * data.discount / 100) : data.discount;
      let showDisc = data.discount_type === 'percentage' ? data.discount + '%' : data.discount + 'TK';

      let basePrice = parseFloat(data.price);
      let globalRate = $('#applyGlobalDeduction').is(':checked') ? parseFloat($('#applyGlobalDeduction').data('percentage')) : 0;
      let customRate = parseFloat($('#customDeductionRate').val()) || 0;
      let totalDeductionPercent = Math.min(globalRate + customRate, 100);

      let deductionAmountPerUnit = (basePrice * totalDeductionPercent / 100);
      let sellingPricePerUnit = basePrice - deductionAmountPerUnit;

      let cardHtml = `
        <div class="col-12 col-md-6 col-lg-4 mb-3 product-card-container">
            <div class="product-card h-100" data-id="${productId}">
                <div class="remove-card-btn" onclick="removeCard(this)"><i class="fas fa-times"></i></div>
                
                <div class="row g-2 align-items-center">
                    <div class="col-4">
                        <div class="product-img-box mb-2">
                            <img src="${finalImgPath}" class="img-fluid rounded" alt="${name}" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3131ff&color=fff'">
                        </div>
                        <div class="qty-controls d-flex align-items-center justify-content-between">
                            <button type="button" class="qty-btn" onclick="updateQty(this, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" name="products[${index}][qty]" class="qty-input mx-1" value="1" min="1" max="${stock}" oninput="calculateCard(this)">
                            <button type="button" class="qty-btn" onclick="updateQty(this, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <div class="col-8 d-flex flex-column justify-content-between" style="min-height: 110px;">
                        <div class="ps-2">
                            <h6 class="product-name mb-1 fw-bold text-dark text-wrap">${name}</h6>
                            <div class="price-info small text-muted">Base Rate: <del class="base-price-display">${basePrice.toFixed(2)}</del> ৳</div>
                            <div class="price-info small">Selling Rate: <b class="text-success selling-price-display">${sellingPricePerUnit.toFixed(2)} ৳</b></div>
                            <div class="price-info small text-muted">Offer Disc: ${disc.toFixed(2)} ৳  <small style="color:red;">(${showDisc})</small></div>
                        </div>

                        <div class="mt-2 ps-2">
                            <div class="subtotal-badge w-100 py-1 text-center">
                                Total: <span class="card-subtotal">0.00</span> ৳
                            </div>
                        </div>

                        <input type="hidden" name="products[${index}][product_id]" value="${productId}">
                        <input type="hidden" name="products[${index}][price]" class="card-price" value="${data.price}">
                        <input type="hidden" name="products[${index}][discount]" class="card-discount" value="${disc}">
                        <input type="hidden" class="card-subtotal-val" value="0">
                    </div>
                </div>
            </div>
        </div>`;

      $('#product-wrapper').append(cardHtml);
      index++;

      let lastInput = $('#product-wrapper .product-card').last().find('.qty-input');
      calculateCard(lastInput);
      $('#product-wrapper .product-card').last()[0].scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      });
    });
  }

  function updateQty(btn, change) {
    let input = $(btn).siblings('.qty-input');
    let newVal = parseInt(input.val()) + change;
    if (newVal >= 1) {
      input.val(newVal).trigger('input');
    }
  }

  function calculateCard(el) {
    let card = $(el).closest('.product-card');
    let basePrice = parseFloat(card.find('.card-price').val()) || 0;
    let qty = parseFloat(card.find('.qty-input').val()) || 0;
    let offerDisc = parseFloat(card.find('.card-discount').val()) || 0;

    // Deduction Logic
    let globalRate = $('#applyGlobalDeduction').is(':checked') ? parseFloat($('#applyGlobalDeduction').data('percentage')) : 0;
    let customRate = parseFloat($('#customDeductionRate').val()) || 0;
    let totalDeductionPercent = Math.min(globalRate + customRate, 100);

    let deductionAmountPerUnit = (basePrice * totalDeductionPercent / 100);
    let sellingPricePerUnit = basePrice - deductionAmountPerUnit;

    // Final subtotal for this card
    let subtotal = (sellingPricePerUnit - offerDisc) * qty;

    // UI Updates
    card.find('.selling-price-display').text(sellingPricePerUnit.toFixed(2) + ' ৳');
    card.find('.card-subtotal').text(subtotal.toFixed(2));
    card.find('.card-subtotal-val').val(subtotal.toFixed(2));

    // Toggle strike-through
    if (totalDeductionPercent > 0) {
      card.find('.base-price-display').parent().show();
    } else {
      card.find('.base-price-display').parent().hide();
    }

    calculateTotal();
  }

  function removeCard(btn) {
    $(btn).closest('.product-card-container').fadeOut(300, function() {
      $(this).remove();
      calculateTotal();
    });
  }

  function calculateTotal() {
    let totalItemCount = 0;
    let totalSubtotalFromCards = 0;
    let totalOfferDiscount = 0; // Fixed discounts per item

    // 1. Fetch Deduction Rates (Percentage Cuts)
    let globalRate = $('#applyGlobalDeduction').is(':checked') ? parseFloat($('#applyGlobalDeduction').data('percentage')) : 0;
    let customRate = parseFloat($('#customDeductionRate').val()) || 0;
    let totalDeductionPercent = Math.min(globalRate + customRate, 100);
    // 2. Loop through each product card
    $('.product-card').each(function() {
      let card = $(this);
      let basePrice = parseFloat(card.find('.card-price').val()) || 0;
      let qty = parseFloat(card.find('.qty-input').val()) || 0;
      let offerDiscPerUnit = parseFloat(card.find('.card-discount').val()) || 0;

      // Calculate the adjusted Unit Selling Price (after percentage cut)
      let deductionPerUnit = (basePrice * totalDeductionPercent / 100);
      let adjustedSellingRate = basePrice - deductionPerUnit;

      // Subtotal for this card = (Adjusted Rate - Offer) * Qty
      let cardSubtotal = (adjustedSellingRate - offerDiscPerUnit) * qty;

      // Aggregate only Offer Discounts for the "Discount" label
      totalOfferDiscount += (offerDiscPerUnit * qty);
      totalSubtotalFromCards += cardSubtotal;
      totalItemCount++;
    });

    // 3. Get Special Discount (The final flat amount)
    let specialDisc = parseFloat($('#specialDiscountInput').val()) || 0;

    // 4. Calculate Final Totals
    let finalNetTotal = totalSubtotalFromCards - specialDisc;
    if (finalNetTotal < 0) finalNetTotal = 0;

    // ONLY Offer Discounts + Special Discount
    let totalPromotionalDiscount = totalOfferDiscount + specialDisc;

    // 5. Update UI Displays
    $('#itemCount').text(totalItemCount);
    $('#totalDiscount').text(totalPromotionalDiscount.toFixed(2)); // Show only Offers + Special
    $('#netTotalDisplay').text(finalNetTotal.toLocaleString('en-US', {
      minimumFractionDigits: 2
    }));

    // 6. Update Hidden Inputs for Form Submission
    $('#netTotalInput').val(finalNetTotal.toFixed(2));
    $('#totalDiscountInput').val(totalPromotionalDiscount.toFixed(2));
  }

  // Initialize Live Product Search & Customer Live Search
  function esc(s) {
    return String(s || '')
      .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
      .replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  $(document).ready(function () {
    // ── Live Product Search ──────────────────────────────────────
    var plsTimer;
    function performPlsSearch() {
      clearTimeout(plsTimer);
      var $input = $('#pls-input');
      if (!$input.length) return;
      var raw = $input.val();
      var q = raw.trim();

      plsTimer = setTimeout(function () {
        $('#pls-spinner').show();
        let userRole = "{{ auth()->user()->role ?? 'manager' }}";
        var url = '/' + userRole + '/products/search?search=' + encodeURIComponent(raw) + (q === '' ? '&all=1' : '');

        $.ajax({
          url: url,
          type: 'GET',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          success: function (products) {
            $('#pls-spinner').hide();
            if (!products || !products.length) {
              $('#pls-dropdown').html('<div class="pls-empty"><i class="fas fa-box-open me-1"></i> No products found</div>').show();
              return;
            }
            var html = '';
            products.forEach(function (p) {
              html += '<div class="pls-item"'
                + ' data-id="'    + p.id                   + '"'
                + ' data-name="'  + esc(p.name)            + '"'
                + ' data-stock="' + (p.available_qty || 0) + '"'
                + ' data-image="' + esc(p.image || '')     + '">'
                + '<span class="pls-item-name">'  + esc(p.name) + '</span>'
                + '<span class="pls-item-stock">Stock: ' + (p.available_qty || 0) + '</span>'
                + '</div>';
            });
            $('#pls-dropdown').html(html).show();
          },
          error: function (xhr, status, err) {
            console.error('[PLS] error - status:', xhr.status, 'resp:', xhr.responseText);
            $('#pls-spinner').hide();
            $('#pls-dropdown').html('<div class="pls-empty">Unable to load products (' + xhr.status + ')</div>').show();
          }
        });
      }, 200);
    }

    $(document).on('input', '#pls-input', performPlsSearch);
    $(document).on('focus', '#pls-input', function () {
      if ($('#pls-dropdown').children().length > 0) {
        $('#pls-dropdown').show();
      } else {
        performPlsSearch();
      }
    });

    $(document).on('click', '.pls-item', function (e) {
      e.stopPropagation();
      var $item = $(this);
      addProductCard($item.attr('data-id'), $item.attr('data-name'), $item.attr('data-stock'), $item.attr('data-image'));
      $('#pls-input').val('');
      $('#pls-dropdown').hide().empty();
      $('#pls-input').focus();
    });

    // ── Live Customer Search ─────────────────────────────────────
    var clsTimer;
    $(document).on('input', '#cls-input', function () {
      clearTimeout(clsTimer);
      var raw = $(this).val();
      var q = raw.trim();
      if (raw.length < 2 || (q.length > 0 && q.length < 2)) {
        $('#cls-dropdown').hide().empty();
        return;
      }
      clsTimer = setTimeout(function () {
        $('#cls-spinner').show();
        var url = '/manager/customers/search?search=' + encodeURIComponent(raw) + (q === '' ? '&all=1' : '');
        $.ajax({
          url: url,
          type: 'GET',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          success: function (customers) {
            $('#cls-spinner').hide();
            if (!customers || !customers.length) {
              $('#cls-dropdown').html('<div class="cls-empty"><i class="fas fa-users-slash me-1"></i> No customer found</div>').show();
              return;
            }
            var html = '';
            customers.forEach(function (c) {
              html += '<div class="cls-item"'
                + ' data-id="'    + c.id             + '"'
                + ' data-name="'  + esc(c.shop_name) + '"'
                + ' data-due="'   + c.due            + '">'
                + '<span class="cls-item-name">' + esc(c.shop_name) + '</span>'
                + '<span class="cls-item-due">Due: ' + c.due + ' TK</span>'
                + '</div>';
            });
            $('#cls-dropdown').html(html).show();
          },
          error: function (xhr, status, err) {
            console.error('[CLS] error - status:', xhr.status);
            $('#cls-spinner').hide();
          }
        });
      }, 300);
    });

    $(document).on('click', '.cls-item', function (e) {
      e.stopPropagation();
      var $item = $(this);
      $('#selected_customer_id').val($item.attr('data-id'));
      $('#cls-selected-name').text($item.attr('data-name'));
      $('#cls-selected-due').text('Due: ' + $item.attr('data-due') + ' TK');

      $('#cls-input').val('');
      $('#cls-dropdown').hide().empty();
      $('#cls-container').hide();
      $('#cls-selected-box').css('display', 'flex');
    });

    $(document).on('click', '#cls-clear-btn', function (e) {
      e.preventDefault();
      $('#selected_customer_id').val('');
      $('#cls-selected-box').hide();
      $('#cls-container').show();
      $('#cls-input').focus();
    });

    // Close dropdowns on outside click
    $(document).on('click', function (e) {
      if (!$(e.target).closest('#pls-container, .pls-dropdown').length) {
        $('#pls-dropdown').hide();
      }
      if (!$(e.target).closest('#cls-container, #cls-selected-box, .cls-dropdown').length) {
        $('#cls-dropdown').hide();
      }
    });
  });
</script>
@endpush