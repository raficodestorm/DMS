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
    margin-top: 20px;
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

  .product-name {
    font-weight: 700;
    font-size: .90rem;
    color: var(--text-main);
    line-height: 1.2;
    margin-bottom: 5px;
  }

  .price-info {
    font-size: .85rem;
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
    font-size: .8rem;
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
    box-shadow: 0 2px 5px rgba(0, 0, 0, .2);
  }

  .p-summary-card {
    background: var(--glass);
    color: var(--text-main);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid var(--glass);
  }

  .retail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 50px;
    background: rgba(16, 185, 129, .12);
    color: #10b981;
    font-family: 'Inter', sans-serif;
  }

  @media (max-width: 768px) {
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
  }

  /* ── Live Product Search Dropdown ─────────────────────────────── */
  .product-search-wrapper {
    position: relative;
    width: 100%;
  }

  .product-search-input {
    width: 100%;
    cursor: text;
  }

  .product-search-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    max-height: 260px;
    overflow-y: auto;
    background: var(--section-bg, #ffffff);
    border: 1px solid var(--border-color, #dee2e6);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
    padding: 6px;
  }

  .product-search-dropdown.open {
    display: block !important;
  }

  .ps-option {
    padding: 10px 14px;
    cursor: pointer;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-main, #212529);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.15s ease;
  }

  .ps-option:hover {
    background: var(--primary-soft, rgba(16, 185, 129, 0.1));
    color: var(--primary, #10b981);
  }

  .ps-option.ps-already-added {
    opacity: 0.6;
    background: rgba(220, 53, 69, 0.05);
  }

  .ps-option.ps-already-added:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
  }

  .ps-no-result {
    padding: 12px;
    text-align: center;
    color: var(--text-muted, #6c757d);
    font-size: 13px;
  }
</style>

<div class="container py-4">
  <div class="form-card animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h2 class="m-0"><i class="fas fa-store text-success me-2"></i>Retail Sales — New Order</h2>
      <span class="retail-badge"><i class="fas fa-check-circle"></i> Auto Approved</span>
    </div>

    @include('components.alert')

    <form method="POST" action="{{ route('manager.retail.store') }}" id="orderForm">
      @csrf

      {{-- Custom Deduction ONLY --}}
      <div class="deduction-control-card p-3 mb-4 border rounded shadow-sm" style="background: var(--section-bg);">
        <div class="row align-items-center">
          <div class="col-md-5 mb-2 mb-md-0">
            <label class="fw-bold" style="font-size: 13px; color: var(--text-muted);">
              <i class="fas fa-percent me-1 text-success"></i> Custom Deduction (%)
            </label>
          </div>
          <div class="col-md-7">
            <div class="input-group">
              <span class="input-group-text" style="background: var(--section-bg); border-color: var(--border-color);">
                <i class="fas fa-tag text-success"></i>
              </span>
              <input type="number" name="applied_custom_deduction" id="customDeductionRate" class="form-control" placeholder="0.00" step="0.01" min="0" max="100" style="background: var(--section-bg); color: var(--text-main); border-color: var(--border-color);" onkeyup="refreshAllCards()" oninput="refreshAllCards()">
              <span class="input-group-text" style="background: var(--section-bg); border-color: var(--border-color);">%</span>
            </div>
          </div>
        </div>
      </div>

      

      {{-- Product Grid --}}
      <div id="product-wrapper" class="row"></div>

      <div class="mb-3">
        <div class="product-search-wrapper">
          <input type="text" id="product-search-input" class="input-form text-center product-search-input" placeholder="+ Click to Add Product / Type to Search" autocomplete="off">
          <div id="product-search-dropdown" class="product-search-dropdown">
            <div class="ps-no-result">Type 2 characters or 2 spaces to search…</div>
          </div>
        </div>
      </div>

      {{-- Note --}}
      <div class="mb-4">
        <label class="form-label fw-bold">Order Note (Optional)</label>
        <textarea name="note" class="input-form" rows="2" placeholder="Write any special instructions here...">{{ old('note') }}</textarea>
      </div>

      {{-- Summary --}}
      <div class="p-summary-card">
        <div class="row align-items-center" style="max-width:450px;">
          <div class="col-6">
            <p class="mb-1 opacity-75">Total Items: <span id="itemCount">0</span></p>
            <div class="input-group input-group-sm mt-2" style="max-width: 200px;">
              <span class="input-group-text bg-warning text-dark border-warning">Special Disc</span>
              <input type="number" name="special_discount" id="specialDiscountInput" class="form-control border-warning" placeholder="0.00" step="0.01" min="0" oninput="calculateTotal()">
            </div>
            <p class="mb-0 opacity-75 mt-1">Total Discount: <span id="totalDiscount">0.00</span> TK</p>
          </div>
          <div class="col-6 text-end">
            <small class="d-block opacity-75">Net Payable</small>
            <h2 class="mb-0" style="font-weight:700; font-size:28px; color:var(--primary);">
              <span id="netTotalDisplay">0.00</span> ৳
            </h2>
          </div>
        </div>
        <input type="hidden" name="net_total" id="netTotalInput">
        <input type="hidden" name="total_discount" id="totalDiscountInput">
      </div>

      <button type="button" class="btn-submit w-100 mt-4 py-3 shadow-lg" onclick="confirmRetailSubmit()">
        Confirm & Submit Retail Order <i class="fas fa-check-circle ms-2"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script type="module">
  let index = 0;
  const allProducts = @json($products);

  function getSelectedProductIds() {
    let ids = [];
    $('.product-card').each(function() {
      let id = $(this).attr('data-id');
      if (id) ids.push(String(id));
    });
    return ids;
  }

  function renderOptions(query) {
    const $dropdown = $('#product-search-dropdown');
    if (!$dropdown.length) return;

    const rawVal = query || '';
    const isTwoSpaces = (rawVal.length >= 2 && rawVal.trim() === '');
    const trimmed = rawVal.trim().toLowerCase();
    const isSearchable = isTwoSpaces || trimmed.length >= 2;

    $dropdown.empty();

    if (!isSearchable) {
      $dropdown.html('<div class="ps-no-result">Type 2 characters or 2 spaces to search…</div>');
      return;
    }

    const selectedIds = getSelectedProductIds();
    const productsArray = Array.isArray(allProducts) ? allProducts : Object.values(allProducts || {});
    let matches = isTwoSpaces 
      ? productsArray 
      : productsArray.filter(p => p && p.name && String(p.name).toLowerCase().includes(trimmed));

    if (!matches || matches.length === 0) {
      $dropdown.html('<div class="ps-no-result">No matched products found</div>');
      return;
    }

    let html = '';
    matches.forEach(p => {
      const isAdded = selectedIds.includes(String(p.id));
      const badgeHtml = isAdded
        ? `<span style="font-size:11px; background:#dc3545; color:#fff; padding:2px 8px; border-radius:10px; margin-left:8px;">(Already Added)</span>`
        : `<span style="font-size:12px; color:var(--text-muted);">Stock: ${p.available_qty}</span>`;

      html += `
        <div class="ps-option ${isAdded ? 'ps-already-added' : ''}" data-id="${p.id}" data-name="${p.name}" data-added="${isAdded ? '1' : '0'}">
          <span><strong>${p.name}</strong></span>
          ${badgeHtml}
        </div>
      `;
    });

    $dropdown.html(html);
  }

  $(document).ready(function() {
    $('#customDeductionRate').on('input', function() {
      window.refreshAllCards();
    });

    $(document).on('input focus', '#product-search-input', function() {
      renderOptions($(this).val());
      $('#product-search-dropdown').addClass('open');
    });

    $(document).on('click', '.ps-option', function(e) {
      e.stopPropagation();
      const $opt = $(this);
      const id = $opt.data('id');
      const name = $opt.data('name');
      const isAdded = $opt.attr('data-added') === '1' || $opt.hasClass('ps-already-added');

      if (isAdded) {
        alert(`"${name}" is already in the list!`);
        return;
      }

      window.addProductCardById(id);
      $('#product-search-input').val('');
      $('#product-search-dropdown').removeClass('open');
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('.product-search-wrapper').length) {
        $('#product-search-dropdown').removeClass('open');
      }
    });
  });

  window.refreshAllCards = function() {
    $('.qty-input').each(function() {
      window.calculateCard(this);
    });
  };

  window.addProductCardById = function(productId) {
    if (!productId) return;

    if ($(`.product-card[data-id="${productId}"]`).length > 0) {
      alert('Product is already in the list!');
      return;
    }

    const productsArray = Array.isArray(allProducts) ? allProducts : Object.values(allProducts || {});
    let prod = productsArray.find(p => p && String(p.id) === String(productId));
    if (!prod) return;

    let name = prod.name;
    let stock = prod.available_qty;
    let imageName = prod.image;

    let finalImgPath = (imageName && String(imageName).trim() !== '' && imageName !== 'null') ?
      (imageName.startsWith('uploads/') ? '/' + imageName : '/uploads/' + imageName) :
      `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=10b981&color=fff`;

    $.get(`{{ url('manager/retail/product-data') }}/${productId}`, function(data) {
      let basePrice = parseFloat(data.price);
      let customRate = parseFloat($('#customDeductionRate').val()) || 0;
      let totalDeduct = Math.min(customRate, 100);

      let deductAmt = basePrice * totalDeduct / 100;
      let sellingRate = basePrice - deductAmt;
      let disc = data.discount_type === 'percentage' ? (sellingRate * data.discount / 100) : data.discount;
      let showDisc = data.discount_type === 'percentage' ? data.discount + '%' : data.discount + ' TK';

      let cardHtml = `
    <div class="col-12 col-md-6 col-lg-4 mb-3 product-card-container">
      <div class="product-card h-100" data-id="${productId}">
        <div class="remove-card-btn" onclick="window.removeCard(this)"><i class="fas fa-times"></i></div>
        <div class="row g-2 align-items-center">
          <div class="col-4">
            <div class="product-img-box mb-2">
              <img src="${finalImgPath}" class="img-fluid rounded" alt="${name}"
                   onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=10b981&color=fff'">
            </div>
            <div class="qty-controls d-flex align-items-center justify-content-between">
              <button type="button" class="qty-btn" onclick="window.updateQty(this,-1)"><i class="fas fa-minus"></i></button>
              <input type="number" name="products[${index}][qty]" class="qty-input mx-1"
                     value="1" min="1" max="${stock}" oninput="window.calculateCard(this)">
              <button type="button" class="qty-btn" onclick="window.updateQty(this,1)"><i class="fas fa-plus"></i></button>
            </div>
          </div>
          <div class="col-8 d-flex flex-column justify-content-between" style="min-height:110px;">
            <div class="ps-2">
              <h6 class="product-name mb-1 fw-bold text-wrap">${name}</h6>
              <div class="price-info small text-muted">Base Rate: <del class="base-price-display">${basePrice.toFixed(2)}</del> ৳</div>
              <div class="price-info small">Selling Rate: <b class="text-success selling-price-display">${sellingRate.toFixed(2)} ৳</b></div>
              <div class="price-info small text-muted">Offer Disc: ${disc.toFixed(2)} ৳ <small style="color:red;">(${showDisc})</small></div>
            </div>
            <div class="mt-2 ps-2">
              <div class="subtotal-badge w-100 py-1 text-center">Total: <span class="card-subtotal">0.00</span> ৳</div>
            </div>
            <input type="hidden" name="products[${index}][product_id]" value="${productId}">
            <input type="hidden" name="products[${index}][price]"      class="card-price"    value="${data.price}">
            <input type="hidden" name="products[${index}][discount]"   class="card-discount" value="${disc}">
            <input type="hidden" class="card-subtotal-val" value="0">
          </div>
        </div>
      </div>
    </div>`;

      $('#product-wrapper').append(cardHtml);
      index++;
      let lastInput = $('#product-wrapper .product-card').last().find('.qty-input');
      window.calculateCard(lastInput);
      $('#product-wrapper .product-card').last()[0].scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      });
    });
  };

  window.updateQty = function(btn, change) {
    let input = $(btn).siblings('.qty-input');
    let newVal = parseInt(input.val()) + change;
    if (newVal >= 1) input.val(newVal).trigger('input');
  };

  window.calculateCard = function(el) {
    let card = $(el).closest('.product-card');
    let basePrice = parseFloat(card.find('.card-price').val()) || 0;
    let qty = parseFloat(card.find('.qty-input').val()) || 0;
    let offerDisc = parseFloat(card.find('.card-discount').val()) || 0;
    let customRate = parseFloat($('#customDeductionRate').val()) || 0;
    let totalDeduct = Math.min(customRate, 100);

    let deductAmt = basePrice * totalDeduct / 100;
    let sellingRate = basePrice - deductAmt;
    let subtotal = (sellingRate - offerDisc) * qty;

    card.find('.selling-price-display').text(sellingRate.toFixed(2) + ' ৳');
    card.find('.card-subtotal').text(subtotal.toFixed(2));
    card.find('.card-subtotal-val').val(subtotal.toFixed(2));
    card.find('.base-price-display').parent().toggle(totalDeduct > 0);

    window.calculateTotal();
  };

  window.removeCard = function(btn) {
    $(btn).closest('.product-card-container').fadeOut(300, function() {
      $(this).remove();
      window.calculateTotal();
    });
  };

  window.calculateTotal = function() {
    let totalItems = 0;
    let totalSubtotal = 0;
    let totalOfferDisc = 0;
    let customRate = parseFloat($('#customDeductionRate').val()) || 0;
    let totalDeduct = Math.min(customRate, 100);

    $('.product-card').each(function() {
      let card = $(this);
      let basePrice = parseFloat(card.find('.card-price').val()) || 0;
      let qty = parseFloat(card.find('.qty-input').val()) || 0;
      let offerDisc = parseFloat(card.find('.card-discount').val()) || 0;
      let deductAmt = basePrice * totalDeduct / 100;
      let sellingRate = basePrice - deductAmt;
      let cardSub = (sellingRate - offerDisc) * qty;

      totalOfferDisc += offerDisc * qty;
      totalSubtotal += cardSub;
      totalItems++;
    });

    let specialDisc = parseFloat($('#specialDiscountInput').val()) || 0;
    let finalNet = Math.max(totalSubtotal - specialDisc, 0);
    let totalDisc = totalOfferDisc + specialDisc;

    $('#itemCount').text(totalItems);
    $('#totalDiscount').text(totalDisc.toFixed(2));
    $('#netTotalDisplay').text(finalNet.toLocaleString('en-US', {
      minimumFractionDigits: 2
    }));
    $('#netTotalInput').val(finalNet.toFixed(2));
    $('#totalDiscountInput').val(totalDisc.toFixed(2));
  };
</script>

<script>
  function confirmRetailSubmit() {
    let totalAmount = $('#netTotalDisplay').text().trim() || '0.00';

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '⚠️ নিশ্চিত করুন',
        html: `
          <div style="text-align: left; font-size: 14px; line-height: 1.8;">
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px; text-align: center; margin-bottom: 15px;">
              <span style="font-size: 13px; color: #166534; font-weight: 600; display: block;">কাস্টমারের কাছ থেকে নগদ গ্রহণ করুন:</span>
              <span style="font-size: 26px; color: #15803d; font-weight: 800;">৳ ${totalAmount}</span>
            </div>
            <p style="font-weight: 700; color: #dc2626; margin-bottom: 8px;">Retail Order সম্পর্কে গুরুত্বপূর্ণ তথ্য:</p>
            <ul style="padding-left: 18px; color: #374151; margin-bottom: 0;">
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
        width: 480,
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('orderForm').submit();
        }
      });
    } else {
      if (confirm(`Total Payable Amount: ৳ ${totalAmount}\n\nThis Retail Order will be auto-delivered and payment auto-marked as Paid. Collect payment immediately — no due allowed. Confirm?`)) {
        document.getElementById('orderForm').submit();
      }
    }
  }
</script>
@endpush
