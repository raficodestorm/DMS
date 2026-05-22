@extends('layouts.srlayout')

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
      <h2 class="m-0"><i class="fas fa-edit text-primary"></i> Edit Order #{{ $order->id }}</h2>
    </div>

    @include('components.alert')

    <form method="POST" action="{{ route('sr.order.update', $order->id) }}" id="orderForm">
      @csrf
      @method('PUT')

      <div class="deduction-control-card p-3 mb-4 border rounded bg-light shadow-sm">
        <div class="row align-items-center">
          <div class="col-md-6 mb-2 mb-md-0">
            <div class="form-check form-switch">
              {{-- Check if order had global deduction applied --}}
              <input class="form-check-input" type="checkbox" name="apply_global" id="applyGlobalDeduction"
                data-percentage="{{ $deductionSettings->customer_deduction ?? 0 }}" {{ $order->applied_deduction_percent
              > 0 ? 'checked' : '' }}>

              <input type="hidden" name="customer_deduction" id="hiddenGlobalDeduction"
                value="{{ $order->applied_deduction_percent }}">

              <label class="form-check-label fw-bold" for="applyGlobalDeduction">
                Apply Standard Deduction ({{ $deductionSettings->customer_deduction ?? 0 }}%)
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-white">Custom Deduction %</span>
              <input type="number" id="customDeductionRate" class="form-control" placeholder="0.00" step="0.01" min="0"
                value="{{ $order->applied_deduction_percent - $deductionSettings->customer_deduction }}"
                onkeyup="refreshAllCards()">
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

      <div class="mb-3">
        <select id="product-search" class="input-form text-center" onchange="addProductCard(this)">
          <option value="">+ Add More Product</option>
          @foreach($products as $p)
          <option value="{{ $p['id'] }}" data-name="{{ $p['name'] }}" data-stock="{{ $p['available_qty'] }}"
            data-image-name="{{ $p['image'] }}">
            {{ $p['name'] }} (Stock: {{ $p['available_qty'] }})
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
        <input type="hidden" name="applied_custom_deduction" id="hiddenCustomDeduction"
          value="{{ $order->applied_custom_deduction }}">
      </div>

      <button type="submit" class="btn-submit w-100 mt-4 py-3 shadow-lg">
        Update Order <i class="fas fa-save ms-2"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Index সেট করা হচ্ছে বিদ্যমান আইটেমের সংখ্যার ওপর ভিত্তি করে
  let index = {{ count($order->items) }};

$(document).ready(function() {
    // ১. গ্লোবাল ডিডাকশন ভ্যালু আপডেট করা
    updateGlobalDeductionValue();

    // ২. পেজ লোড হওয়ার সাথে সাথে প্রতিটি কার্ডের জন্য ক্যালকুলেশন রান করা
    // সামান্য ডিলে (delay) দিলে DOM এলিমেন্টগুলো পেতে সুবিধা হয়
    setTimeout(function() {
        refreshAllCards();
    }, 100);

    $('#applyGlobalDeduction').on('change', function() {
        updateGlobalDeductionValue();
        refreshAllCards();
    });

    $('#customDeductionRate').on('input', function() {
        $('#hiddenCustomDeduction').val($(this).val());
        refreshAllCards();
    });
});

function updateGlobalDeductionValue() {
    let checkbox = $('#applyGlobalDeduction');
    if (checkbox.is(':checked')) {
        $('#hiddenGlobalDeduction').val(checkbox.data('percentage'));
    } else {
        $('#hiddenGlobalDeduction').val(0);
    }
}

function refreshAllCards() {
    $('.qty-input').each(function() {
        calculateCard(this);
    });
}

// addProductCard, updateQty, calculateCard, removeCard, calculateTotal 
// ফাংশনগুলো আপনার Create Blade এর মত হুবহু থাকবে। 
// (নিচে সংক্ষেপে আবার দেওয়া হলো)

function calculateCard(el) {
    let card = $(el).closest('.product-card');
    let basePrice = parseFloat(card.find('.card-price').val()) || 0;
    let qty = parseFloat(card.find('.qty-input').val()) || 0;
    let offerDisc = parseFloat(card.find('.card-discount').val()) || 0;

    let globalRate = parseFloat($('#hiddenGlobalDeduction').val()) || 0;
    let customRate = parseFloat($('#hiddenCustomDeduction').val()) || 0;
    let totalDeductionPercent = Math.min(globalRate + customRate, 100);

    let deductionAmountPerUnit = (basePrice * totalDeductionPercent / 100);
    let sellingPricePerUnit = basePrice - deductionAmountPerUnit;

    let subtotal = (sellingPricePerUnit - offerDisc) * qty;

    card.find('.selling-price-display').text(sellingPricePerUnit.toFixed(2) + ' ৳');
    card.find('.card-subtotal').text(subtotal.toFixed(2));
    card.find('.card-subtotal-val').val(subtotal.toFixed(2));
    
    if (totalDeductionPercent > 0) {
        card.find('.base-price-display').parent().show();
    } else {
        card.find('.base-price-display').parent().hide();
    }
    
    calculateTotal();
}

function calculateTotal() {
    let totalItemCount = 0;
    let totalSubtotalFromCards = 0;
    let totalOfferDiscount = 0;

    let globalRate = parseFloat($('#hiddenGlobalDeduction').val()) || 0;
    let customRate = parseFloat($('#hiddenCustomDeduction').val()) || 0;
    let totalDeductionPercent = Math.min(globalRate + customRate, 100);

    $('.product-card').each(function() {
        let card = $(this);
        let basePrice = parseFloat(card.find('.card-price').val()) || 0;
        let qty = parseFloat(card.find('.qty-input').val()) || 0;
        let offerDiscPerUnit = parseFloat(card.find('.card-discount').val()) || 0;

        let deductionPerUnit = (basePrice * totalDeductionPercent / 100);
        let adjustedSellingRate = basePrice - deductionPerUnit;
        let cardSubtotal = (adjustedSellingRate - offerDiscPerUnit) * qty;
        
        totalOfferDiscount += (offerDiscPerUnit * qty);
        totalSubtotalFromCards += cardSubtotal;
        totalItemCount++;
    });

    let specialDisc = parseFloat($('#specialDiscountInput').val()) || 0;
    let finalNetTotal = totalSubtotalFromCards - specialDisc;
    if (finalNetTotal < 0) finalNetTotal = 0;

    let totalPromotionalDiscount = totalOfferDiscount + specialDisc;

    $('#itemCount').text(totalItemCount);
    $('#totalDiscount').text(totalPromotionalDiscount.toFixed(2));
    $('#netTotalDisplay').text(finalNetTotal.toLocaleString('en-US', {minimumFractionDigits: 2}));

    $('#netTotalInput').val(finalNetTotal.toFixed(2));
    $('#totalDiscountInput').val(totalPromotionalDiscount.toFixed(2));
}

// কপি করুন Create Blade থেকে: addProductCard, removeCard, updateQty
function updateQty(btn, change) {
    let input = $(btn).siblings('.qty-input');
    let newVal = parseInt(input.val()) + change;
    if (newVal >= 1) { input.val(newVal).trigger('input'); }
}

function removeCard(btn) {
    $(btn).closest('.product-card-container').fadeOut(300, function() {
        $(this).remove();
        calculateTotal();
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

    let finalImgPath = (imageName && imageName.trim() !== "" && imageName !== "null") 
        ? (imageName.startsWith('uploads/') ? '/' + imageName : '/uploads/' + imageName)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3131ff&color=fff`;

    $.get(`/sr/get-product-data/${productId}`, function(data) {
        let disc = data.discount_type === 'percentage' ? (data.price * data.discount / 100) : data.discount;
        let cardHtml = `
        <div class="col-12 col-md-6 col-lg-4 mb-3 product-card-container">
            <div class="product-card h-100" data-id="${productId}">
                <div class="remove-card-btn" onclick="removeCard(this)"><i class="fas fa-times"></i></div>
                <div class="row g-2 align-items-center">
                    <div class="col-4">
                        <div class="product-img-box mb-2">
                            <img src="${finalImgPath}" class="img-fluid rounded" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3131ff&color=fff'">
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
                            <div class="price-info small text-muted">Base Rate: <del class="base-price-display">${data.price}</del> ৳</div>
                            <div class="price-info small">Selling Rate: <b class="text-success selling-price-display">${data.price} ৳</b></div>
                            <div class="price-info small text-muted">Offer Disc: <b class="text-dark">${disc.toFixed(2)} ৳</b></div>
                        </div>
                        <div class="mt-2 ps-2">
                            <div class="subtotal-badge w-100 py-1 text-center">Total: <span class="card-subtotal">0.00</span> ৳</div>
                        </div>
                        <input type="hidden" name="products[${index}][product_id]" value="${productId}">
                        <input type="hidden" name="products[${index}][price]" class="card-price" value="${data.price}">
                        <input type="hidden" name="products[${index}][discount]" class="card-discount" value="${disc}">
                    </div>
                </div>
            </div>
        </div>`;
        $('#product-wrapper').append(cardHtml); 
        index++;
        $(el).val(''); 
        calculateCard($('#product-wrapper .product-card').last().find('.qty-input'));
    });
}
</script>
@endpush