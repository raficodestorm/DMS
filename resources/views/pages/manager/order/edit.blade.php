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

      if ($total > $standard) {
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
        <select name="customer_id" class="input-form @error('customer_id') is-invalid @enderror" required>
          @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ $order->customer_id == $c->id ? 'selected' : '' }}>
            {{ $c->shop_name }} (Due: {{ $c->due ?: 0 }} TK)
          </option>
          @endforeach
        </select>
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
                  ? asset('storage/' . $item->product->image)
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

      <div class="mb-3">
        <select id="product-search" class="input-form text-center" onchange="addProductCard(this)">
          <option value="">+ Click to Add Product</option>
          @foreach($products as $p)
          @php $p = (object)$p; @endphp
          <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-stock="{{ $p->available_qty }}"
            data-image-name="{{ $p->image }}">
            {{ $p->name }} (Stock: {{ $p->available_qty }})
          </option>
          @endforeach
        </select>
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


  function addProductCard(el) {
    let productId = $(el).val();
    if (!productId) return;

    if ($(`.product-card[data-id="${productId}"]`).length > 0) {
      alert('প্রোডাক্টটি অলরেডি লিস্টে আছে!');
      $(el).val('');
      return;
    }

    let option = $(el).find(':selected');
    let name = option.attr('data-name');
    let stock = option.attr('data-stock');
    let imageName = option.attr('data-image-name');

    let storagePath = "{{ asset('storage') }}";
    let finalImgPath = (imageName && imageName.trim() !== "" && imageName !== "null") ?
      storagePath.replace(/\/$/, "") + '/' + imageName.replace(/^\//, "") :
      `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3131ff&color=fff`;

    $.get(`/sr/get-product-data/${productId}`, function(data) {
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
      $(el).val('');

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
</script>
@endpush