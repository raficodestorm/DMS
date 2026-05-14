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
  }

  .product-card:hover {
    border-color: var(--primary);
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
</style>

<div class="container py-4">
  <div class="form-card animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h2 class="m-0">
        <i class="fas fa-pen-to-square text-warning me-2"></i>Edit Retail Order — BRS{{ $order->id }}
      </h2>
      <a href="{{ route('manager.retail.index') }}" style="font-size:12px; color:var(--text-muted); text-decoration:none; font-family:'Inter',sans-serif;">
        <i class="fas fa-arrow-left me-1"></i> Back to Retail
      </a>
    </div>

    @include('components.alert')

    <form method="POST" action="{{ route('manager.retail.update', $order->id) }}" id="orderForm">
      @csrf @method('PUT')

      {{-- Custom Deduction --}}
      <div class="deduction-control-card p-3 mb-4 border rounded shadow-sm" style="background: var(--section-bg);">
        <div class="row align-items-center">
          <div class="col-md-5 mb-2 mb-md-0">
            <label class="fw-bold" style="font-size:13px; color:var(--text-muted);">
              <i class="fas fa-percent me-1 text-success"></i> Custom Deduction (%)
            </label>
          </div>
          <div class="col-md-7">
            <div class="input-group">
              <span class="input-group-text" style="background:var(--section-bg); border-color:var(--border-color);">
                <i class="fas fa-tag text-success"></i>
              </span>
              <input type="number" name="applied_custom_deduction" id="customDeductionRate" class="form-control" placeholder="0.00" step="0.01" min="0" max="100" value="{{ old('applied_custom_deduction', $order->applied_deduction_percent) }}" style="background:var(--section-bg); color:var(--text-main); border-color:var(--border-color);" onkeyup="refreshAllCards()" oninput="refreshAllCards()">
              <span class="input-group-text" style="background:var(--section-bg); border-color:var(--border-color);">%</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Customer --}}
      <div class="customer-section mb-4">
        <label class="form-label fw-bold">Select Shop / Customer</label>
        <select name="customer_id" class="input-form @error('customer_id') is-invalid @enderror" required>
          <option value="">-- Choose Customer --</option>
          @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ old('customer_id', $order->customer_id) == $c->id ? 'selected' : '' }}>
            {{ $c->shop_name }} (Due: {{ number_format($c->due, 0) }} TK)
          </option>
          @endforeach
        </select>
        @error('customer_id') <div class="error-msg">{{ $message }}</div> @enderror
      </div>

      {{-- Product Grid --}}
      <div id="product-wrapper" class="row">
        @foreach($order->items as $item)
        @php $p = (object)['id' => $item->product_id, 'name' => $item->product->name ?? '', 'price' => $item->price, 'image' => $item->product->image ?? '', 'available_qty' => 999]; @endphp
        <div class="col-12 col-md-6 col-lg-4 mb-3 product-card-container">
          <div class="product-card h-100" data-id="{{ $item->product_id }}">
            <div class="remove-card-btn" onclick="removeCard(this)"><i class="fas fa-times"></i></div>
            <div class="row g-2 align-items-center">
              <div class="col-4">
                <div class="product-img-box mb-2">
                  @php
                  $imgSrc = ($p->image && $p->image !== 'null')
                  ? asset('storage/' . $p->image)
                  : 'https://ui-avatars.com/api/?name='.urlencode($p->name).'&background=10b981&color=fff';
                  @endphp
                  <img src="{{ $imgSrc }}" class="img-fluid rounded" alt="{{ $p->name }}" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=10b981&color=fff'">
                </div>
                <div class="qty-controls d-flex align-items-center justify-content-between">
                  <button type="button" class="qty-btn" onclick="updateQty(this,-1)"><i class="fas fa-minus"></i></button>
                  <input type="number" name="products[{{ $loop->index }}][qty]" class="qty-input mx-1" value="{{ $item->quantity }}" min="1" oninput="calculateCard(this)">
                  <button type="button" class="qty-btn" onclick="updateQty(this,1)"><i class="fas fa-plus"></i></button>
                </div>
              </div>
              <div class="col-8 d-flex flex-column justify-content-between" style="min-height:110px;">
                <div class="ps-2">
                  <h6 class="product-name mb-1 fw-bold text-wrap">{{ $p->name }}</h6>
                  <div class="price-info small text-muted">Base Rate: <del class="base-price-display">{{ number_format($p->price, 2) }}</del> ৳</div>
                  <div class="price-info small">Selling Rate: <b class="text-success selling-price-display">{{ number_format($item->selling_rate, 2) }} ৳</b></div>
                  <div class="price-info small text-muted">Offer Disc: {{ number_format($item->discount_amount, 2) }} ৳</div>
                </div>
                <div class="mt-2 ps-2">
                  <div class="subtotal-badge w-100 py-1 text-center">
                    Total: <span class="card-subtotal">{{ number_format($item->net_total, 2) }}</span> ৳
                  </div>
                </div>
                <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                <input type="hidden" name="products[{{ $loop->index }}][price]" class="card-price" value="{{ $item->price }}">
                <input type="hidden" name="products[{{ $loop->index }}][discount]" class="card-discount" value="{{ $item->discount_amount }}">
                <input type="hidden" class="card-subtotal-val" value="{{ $item->net_total }}">
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Add more products --}}
      <div class="mb-3">
        <select id="product-search" class="input-form text-center" onchange="addProductCard(this)">
          <option value="">+ Click to Add More Products</option>
          @foreach($products as $p)
          @php $p = (object)$p; @endphp
          <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-stock="{{ $p->available_qty }}" data-image-name="{{ $p->image }}">
            {{ $p->name }} (Stock: {{ $p->available_qty }})
          </option>
          @endforeach
        </select>
      </div>

      {{-- Note --}}
      <div class="mb-4">
        <label class="form-label fw-bold">Order Note (Optional)</label>
        <textarea name="note" class="input-form" rows="2" placeholder="Write any special instructions...">{{ old('note', $order->note) }}</textarea>
      </div>

      {{-- Summary --}}
      <div class="p-summary-card">
        <div class="row align-items-center" style="max-width:450px;">
          <div class="col-6">
            <p class="mb-1 opacity-75">Total Items: <span id="itemCount">{{ $order->items->count() }}</span></p>
            <div class="input-group input-group-sm mt-2" style="max-width:200px;">
              <span class="input-group-text bg-warning text-dark border-warning">Special Disc</span>
              <input type="number" name="special_discount" id="specialDiscountInput" class="form-control border-warning" placeholder="0.00" step="0.01" min="0" value="{{ old('special_discount', $order->special_discount) }}" oninput="calculateTotal()">
            </div>
            <p class="mb-0 opacity-75 mt-1">Total Discount: <span id="totalDiscount">{{ number_format($order->discount_amount, 2) }}</span> TK</p>
          </div>
          <div class="col-6 text-end">
            <small class="d-block opacity-75">Net Payable</small>
            <h2 class="mb-0" style="font-weight:700; font-size:28px; color:var(--primary);">
              <span id="netTotalDisplay">{{ number_format($order->net_total, 2) }}</span> ৳
            </h2>
          </div>
        </div>
        <input type="hidden" name="net_total" id="netTotalInput" value="{{ $order->net_total }}">
        <input type="hidden" name="total_discount" id="totalDiscountInput" value="{{ $order->discount_amount }}">
      </div>

      <button type="submit" class="btn-submit w-100 mt-4 py-3 shadow-lg">
        Update Retail Order <i class="fas fa-save ms-2"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let index = {{ $order->items->count() }};

  $(document).ready(function() {
    $('#customDeductionRate').on('input', function() {
      refreshAllCards();
    });
    calculateTotal(); // init on load
  });

  function refreshAllCards() {
    $('.qty-input').each(function() {
      calculateCard(this);
    });
  }

  function addProductCard(el) {
    let productId = $(el).val();
    if (!productId) return;
    if ($(`.product-card[data-id="${productId}"]`).length > 0) {
      alert('Product is already in the list!');
      $(el).val('');
      return;
    }

    let option = $(el).find(':selected');
    let name = option.attr('data-name');
    let stock = option.attr('data-stock');
    let imageName = option.attr('data-image-name');
    let storagePath = "{{ asset('storage') }}";
    let finalImgPath = (imageName && imageName.trim() !== '' && imageName !== 'null') ?
      storagePath.replace(/\/$/, '') + '/' + imageName.replace(/^\//, '') :
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
        <div class="remove-card-btn" onclick="removeCard(this)"><i class="fas fa-times"></i></div>
        <div class="row g-2 align-items-center">
          <div class="col-4">
            <div class="product-img-box mb-2">
              <img src="${finalImgPath}" class="img-fluid rounded" alt="${name}"
                   onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=10b981&color=fff'">
            </div>
            <div class="qty-controls d-flex align-items-center justify-content-between">
              <button type="button" class="qty-btn" onclick="updateQty(this,-1)"><i class="fas fa-minus"></i></button>
              <input type="number" name="products[${index}][qty]" class="qty-input mx-1" value="1" min="1" max="${stock}" oninput="calculateCard(this)">
              <button type="button" class="qty-btn" onclick="updateQty(this,1)"><i class="fas fa-plus"></i></button>
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
      $(el).val('');
      let lastInput = $('#product-wrapper .product-card').last().find('.qty-input');
      calculateCard(lastInput);
    });
  }

  function updateQty(btn, change) {
    let input = $(btn).siblings('.qty-input');
    let newVal = parseInt(input.val()) + change;
    if (newVal >= 1) input.val(newVal).trigger('input');
  }

  function calculateCard(el) {
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
    calculateTotal();
  }

  function removeCard(btn) {
    $(btn).closest('.product-card-container').fadeOut(300, function() {
      $(this).remove();
      calculateTotal();
    });
  }

  function calculateTotal() {
    let totalItems = 0,
      totalSubtotal = 0,
      totalOfferDisc = 0;
    let customRate = parseFloat($('#customDeductionRate').val()) || 0;
    let totalDeduct = Math.min(customRate, 100);

    $('.product-card').each(function() {
      let card = $(this);
      let basePrice = parseFloat(card.find('.card-price').val()) || 0;
      let qty = parseFloat(card.find('.qty-input').val()) || 0;
      let offerDisc = parseFloat(card.find('.card-discount').val()) || 0;
      let deductAmt = basePrice * totalDeduct / 100;
      let sellingRate = basePrice - deductAmt;
      totalOfferDisc += offerDisc * qty;
      totalSubtotal += (sellingRate - offerDisc) * qty;
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
  }
</script>
@endpush
