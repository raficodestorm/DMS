@extends('layouts.managerlayout')

@section('content')
<div class="container justify-center">
  <div class="form-card" style="max-width: 900px;">
    <h2><i class="fas fa-edit"></i> Edit Stock Transfer Request BRST{{ $transfer->id }}</h2>
    
    @include('components.alert')

    <form method="POST" action="{{ route('manager.stock-transfer.update', $transfer->id) }}" id="transferForm"
          onsubmit="return validateProducts(event)">
      @csrf
      @method('PUT')

      <div class="mb-4">
        <label>Transfer To Branch</label>
        <select name="to_branch_id" class="input-form" required>
          <option value="">-- Select Destination Branch --</option>
          @foreach($branches as $branch)
          <option value="{{ $branch->id }}" {{ $transfer->to_branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="product-table-header" style="grid-template-columns: 3fr 1.5fr 1.5fr 3fr 50px;">
        <span>Product</span>
        <span>Available Stock</span>
        <span>Transfer Qty</span>
        <span>Note</span>
        <span></span>
      </div>

      <div id="product-wrapper">
        @foreach($transfer->items as $item)
        <div class="product-row stock-transfer-row" style="grid-template-columns: 3fr 1.5fr 1.5fr 3fr 50px; align-items: center; gap: 10px; margin-bottom: 10px;">
            <div>
                {{-- Pre-filled live-search widget for existing items --}}
                <div class="product-search-wrapper">
                  <input type="hidden"
                         name="products[{{ $loop->index }}][product_id]"
                         class="ps-hidden-id"
                         value="{{ $item->product_id }}">
                  <input type="text"
                         class="input-form product-search-input"
                         placeholder="-- Choose Product --"
                         autocomplete="off"
                         value="{{ $item->product->name }}">
                  <div class="product-search-dropdown"></div>
                </div>
            </div>
            <div>
                @php 
                    $currStock = $availableProducts->where('product_id', $item->product_id)->first();
                    $qty = $currStock ? $currStock->quantity : 0;
                @endphp
                <input type="number" class="input-form available-qty" value="{{ $qty }}" readonly tabindex="-1">
            </div>
            <div>
                <input type="number" name="products[{{ $loop->index }}][quantity]" class="input-form transfer-qty" value="{{ $item->quantity }}" min="1" required oninput="validateQty(this)">
            </div>
            <div>
                <input type="text" name="products[{{ $loop->index }}][note]" class="input-form" value="{{ $item->note }}" placeholder="Item note">
            </div>
            <div>
                <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        @endforeach
      </div>

      <button type="button" class="p-add-more-btn" id="addMoreBtn" onclick="addRow()">
        <i class="fas fa-plus-circle"></i> Add Product
      </button>

      <div class="mb-4 mt-4">
        <label>General Note (Optional)</label>
        <textarea name="note" class="input-form" rows="3" placeholder="Additional details about this transfer...">{{ $transfer->note }}</textarea>
      </div>

      <button type="submit" class="btn-submit">
        Update Transfer Request <i class="fas fa-save"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<style>
  /* ── Live Product Search Dropdown ─────────────────────────────── */
  .product-search-wrapper {
    position: relative;
  }
  .product-search-input {
    width: 100%;
    cursor: text;
  }
  .product-search-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    min-width: 100%;
    max-height: 220px;
    overflow-y: auto;
    background: var(--section-bg, #fff);
    border: 1.5px solid var(--border-color, #e0e0e0);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    z-index: 9999;
    padding: 4px 0;
    animation: dropIn .15s ease;
  }
  @keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .product-search-dropdown.open { display: block; }
  .ps-option {
    padding: 6px 10px;
    cursor: pointer;
    font-size: .875rem;
    color: var(--text-main, #222);
    transition: background .12s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .ps-option:hover, .ps-option.active {
    background: var(--primary, #6c47ff);
    color: #ffffff;
    font-weight: 600;
  }
  .ps-no-result {
    padding: 10px 14px;
    color: var(--text-muted, #999);
    font-size: .85rem;
    text-align: center;
  }

  /* ── Mobile Stock Transfer Card Layout ─────────────────────────── */
  @media (max-width: 768px) {
    .product-table-header {
      display: none !important;
    }
    .product-row {
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      grid-template-areas:
        "product   product"
        "avail     qty"
        "note      note" !important;
      gap: 12px !important;
      padding: 14px !important;
      padding-top: 48px !important;
      position: relative !important;
      background: var(--background) !important;
      border: 1px solid var(--border-color) !important;
      border-radius: 12px !important;
      margin-bottom: 14px !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }

    .product-row > div:nth-child(5) {
      position: absolute !important;
      top: 10px !important;
      right: 10px !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    .product-row > div:nth-child(1) { grid-area: product !important; width: 100% !important; }
    .product-row > div:nth-child(2) { grid-area: avail !important;   width: 100% !important; }
    .product-row > div:nth-child(3) { grid-area: qty !important;     width: 100% !important; }
    .product-row > div:nth-child(4) { grid-area: note !important;    width: 100% !important; }

    .product-row .product-search-wrapper,
    .product-row .product-search-input,
    .product-row .input-form {
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box !important;
      margin-bottom: 0 !important;
    }

    .product-row > div:nth-child(1)::before { content: "Product";         color: var(--primary) !important; }
    .product-row > div:nth-child(2)::before { content: "Available Stock"; color: var(--text-muted) !important; }
    .product-row > div:nth-child(3)::before { content: "Transfer Qty";   color: var(--text-muted) !important; }
    .product-row > div:nth-child(4)::before { content: "Note";           color: var(--text-muted) !important; }

    .product-row > div:nth-child(1)::before,
    .product-row > div:nth-child(2)::before,
    .product-row > div:nth-child(3)::before,
    .product-row > div:nth-child(4)::before {
      display: block !important;
      font-size: 11px !important;
      font-weight: 700 !important;
      text-transform: uppercase !important;
      letter-spacing: .5px !important;
      margin-bottom: 5px !important;
    }
  }
</style>

<script type="module">
    let index = {{ $transfer->items->count() }};
    const availableProducts = @json($availableProducts);

    $(document).ready(function () {
      // Global click: close open dropdowns
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.product-search-wrapper').length) {
          $('.product-search-dropdown').removeClass('open');
        }
      });
    });

    /* ── Build product search widget ─────────────────── */
    function buildSearchWidget(idx) {
      return `
        <div class="product-search-wrapper">
          <input type="hidden" name="products[${idx}][product_id]" class="ps-hidden-id">
          <input type="text" class="input-form product-search-input" placeholder="-- Choose Product --" autocomplete="off" data-idx="${idx}">
          <div class="product-search-dropdown">
            <div class="ps-no-result">Type 2 chars or 2 spaces to search…</div>
          </div>
        </div>`;
    }

    /* ── Helper to get selected product IDs ─────────── */
    function getSelectedProductIds($currentHidden) {
      const selected = [];
      $('.ps-hidden-id').not($currentHidden).each(function () {
        const val = $(this).val();
        if (val) selected.push(val.toString());
      });
      return selected;
    }

    /* ── Render dropdown options ─────────────────────── */
    function renderOptions($dropdown, query) {
      const $wrapper  = $dropdown.closest('.product-search-wrapper');
      const isShowAll = query === '  ';
      const trimmed   = query.trim().toLowerCase();
      const results   = isShowAll
        ? availableProducts
        : (trimmed.length >= 2
            ? availableProducts.filter(item =>
                item.product && item.product.name.toLowerCase().includes(trimmed))
            : null);

      $dropdown.empty();

      if (results === null) {
        $dropdown.append('<div class="ps-no-result">Type at least 2 characters…</div>');
        return;
      }
      if (results.length === 0) {
        $dropdown.append('<div class="ps-no-result">No products found.</div>');
        return;
      }

      const selectedIds = getSelectedProductIds($wrapper.find('.ps-hidden-id'));

      results.forEach(item => {
        const isAlreadyAdded = selectedIds.includes(item.product_id.toString());
        const pName = item.product ? item.product.name : 'Product #' + item.product_id;
        const stockQty = item.quantity || 0;

        if (isAlreadyAdded) {
          $dropdown.append(
            `<div class="ps-option ps-already-added"
                  data-id="${item.product_id}"
                  data-qty="${stockQty}"
                  data-name="${pName}"
                  style="opacity: 0.6; cursor: not-allowed; background: #fff0f0; color: #dc3545;">
              ${pName} (Stock: ${stockQty}) <small style="font-weight: 700; float: right; color: #dc3545;">(Already Added)</small>
             </div>`
          );
        } else {
          $dropdown.append(
            `<div class="ps-option"
                  data-id="${item.product_id}"
                  data-qty="${stockQty}"
                  data-name="${pName}">${pName} (Stock: ${stockQty})</div>`
          );
        }
      });
    }

    /* ── Live-search events (delegated) ─────────────── */
    $(document).on('input', '.product-search-input', function () {
      const $input    = $(this);
      const $wrapper  = $input.closest('.product-search-wrapper');
      const $dropdown = $wrapper.find('.product-search-dropdown');
      renderOptions($dropdown, $input.val());
      $dropdown.addClass('open');
    });

    $(document).on('focus', '.product-search-input', function () {
      const $wrapper  = $(this).closest('.product-search-wrapper');
      const $dropdown = $wrapper.find('.product-search-dropdown');
      if ($wrapper.find('.ps-option').length) $dropdown.addClass('open');
    });

    /* ── Option selected ─────────────────────────────── */
    $(document).on('click', '.ps-option', function () {
      const $option  = $(this);
      const $wrapper = $option.closest('.product-search-wrapper');
      const $row     = $option.closest('.product-row');

      const id       = $option.data('id');
      const stockQty = parseFloat($option.data('qty')) || 0;
      const pName    = $option.data('name');

      // Prevent selecting duplicate products
      if ($option.hasClass('ps-already-added')) {
        alert(`"${pName}" is already added in another transfer row!`);
        return;
      }

      const currentHidden = $wrapper.find('.ps-hidden-id');
      const selectedIds = getSelectedProductIds(currentHidden);
      if (selectedIds.includes(id.toString())) {
        alert(`"${pName}" is already added in another transfer row!`);
        return;
      }

      currentHidden.val(id);
      $wrapper.find('.product-search-input').val(pName).css('border', '');

      // Update available stock
      $row.find('.available-qty').val(stockQty);

      // Validate transfer qty against stock
      const transferInput = $row.find('.transfer-qty');
      if (parseFloat(transferInput.val()) > stockQty) {
        transferInput.val(stockQty);
      }

      $wrapper.find('.product-search-dropdown').removeClass('open');
    });

    /* ── addRow ──────────────────────────────────────── */
    window.addRow = function() {
      let html = `
      <div class="product-row stock-transfer-row animate__animated animate__fadeIn" style="grid-template-columns: 3fr 1.5fr 1.5fr 3fr 50px; align-items: center; gap: 10px; margin-bottom: 10px;">
          <div>${buildSearchWidget(index)}</div>
          <div>
              <input type="number" class="input-form available-qty" placeholder="0" readonly tabindex="-1">
          </div>
          <div>
              <input type="number" name="products[${index}][quantity]" class="input-form transfer-qty" placeholder="Qty" min="1" required oninput="validateQty(this)">
          </div>
          <div>
              <input type="text" name="products[${index}][note]" class="input-form" placeholder="Item note">
          </div>
          <div>
              <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
          </div>
      </div>
      `;

      $('#product-wrapper').append(html);
      index++;
    };

    window.validateQty = function(el) {
      let row = $(el).closest('.product-row');
      let available = parseFloat(row.find('.available-qty').val()) || 0;
      let requested = parseFloat($(el).val()) || 0;

      if (requested > available) {
          alert('Transfer quantity cannot exceed available stock!');
          $(el).val(available);
      }
    };

    window.removeRow = function(btn) {
      if ($('.product-row').length > 1) {
          $(btn).closest('.product-row').remove();
      } else {
          alert('At least one product is required.');
      }
    };

    /* ── validateProducts on form submit ─────────────── */
    window.validateProducts = function(e) {
      let valid = true;
      let hasDuplicate = false;
      const seenIds = new Set();

      $('.ps-hidden-id').each(function () {
        const val = $(this).val();
        const $input = $(this).siblings('.product-search-input');

        if (!val) {
          valid = false;
          $input.css({ border: '1.5px solid red' }).attr('placeholder', '⚠ Please select a product');
        } else if (seenIds.has(val)) {
          valid = false;
          hasDuplicate = true;
          $input.css({ border: '1.5px solid red' });
        } else {
          seenIds.add(val);
          $input.css({ border: '' });
        }
      });

      if (!valid) {
        e.preventDefault();
        if (hasDuplicate) {
          alert('Duplicate products detected! Each product can only be transferred once per request.');
        } else {
          alert('Please select a product for every row before submitting.');
        }
        return false;
      }
      return true;
    };
</script>
@endpush
