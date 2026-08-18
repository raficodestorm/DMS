@extends('layouts.managerlayout')

@section('content')
<div class="container justify-center">
  <div class="form-card">
    <h2><i class="fas fa-edit"></i> Edit Stock-in Request</h2>

    @include('components.alert')

    <form method="POST" action="{{ route('manager.stock.in.update', $request->id) }}" id="stockForm"
          onsubmit="return validateProducts(event)"
    >
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label>Select Supplier</label>
        <select name="supplier_id" class="input-form" required>
          <option value="">--Choose a Supplier--</option>
          @foreach($suppliers as $supplier)
          <option value="{{ $supplier->id }}" {{ $request->supplier_id == $supplier->id ? 'selected' : '' }}>
            {{ $supplier->company_name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="product-table-header" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
        <span>Product</span>
        <span>Rate</span>
        <span>Qty</span>
        <span>Tree Deduction</span>
        <span>Subtotal</span>
        <span></span>
      </div>

      <div id="product-wrapper">
        @foreach($request->items as $key => $item)
        <div class="product-row animate__animated animate__fadeIn" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
          <div>
            {{-- Pre-filled live-search widget for existing items --}}
            <div class="product-search-wrapper">
              <input type="hidden"
                     name="products[{{ $key }}][product_id]"
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
            <input type="number" class="input-form rate"
                   value="{{ number_format($item->cost_price, 2, '.', '') }}"
                   readonly tabindex="-1">
          </div>
          <div>
            <input type="number" name="products[{{ $key }}][qty]"
                   class="input-form qty" value="{{ $item->quantity }}"
                   min="1" required oninput="updateRow(this)">
          </div>
          <div>
            <input type="number" name="products[{{ $key }}][tree_deduction]"
                   class="input-form tree-ded"
                   value="{{ number_format($item->tree_deduction ?? 0, 2, '.', '') }}"
                   min="0" step="0.01">
          </div>
          <div>
            <input type="number" class="input-form subtotal"
                   value="{{ number_format($item->cost_price * $item->quantity, 2, '.', '') }}"
                   readonly tabindex="-1">
          </div>
          <div>
            <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        @endforeach
      </div>

      <button type="button" class="p-add-more-btn" id="addMoreBtn" onclick="addRow()">
        <i class="fas fa-plus-circle"></i> Add New Product Row
      </button>

      <div class="p-summary-card">
        <div class="p-net-total-box">
          <span>Total Amount (Estimated)</span>
          <h3 id="netTotalDisplay">{{ number_format($request->net_total, 2) }}</h3>
          <input type="hidden" name="net_total" id="netTotalInput" value="{{ $request->net_total }}">
        </div>
      </div>

      <button type="submit" class="btn-submit">
        Update Stock Request <i class="fas fa-save"></i>
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

  /* ── Mobile Product Row Card Layout ───────────────────────────── */
  @media (max-width: 768px) {
    .product-table-header {
      display: none !important;
    }
    .product-row {
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      grid-template-areas:
        "product  product"
        "rate     qty"
        "tree     subtotal" !important;
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

    .product-row > div:nth-child(6) {
      position: absolute !important;
      top: 10px !important;
      right: 10px !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    .product-row > div:nth-child(1) { grid-area: product !important; width: 100% !important; }
    .product-row > div:nth-child(2) { grid-area: rate !important;    width: 100% !important; }
    .product-row > div:nth-child(3) { grid-area: qty !important;     width: 100% !important; }
    .product-row > div:nth-child(4) { grid-area: tree !important;    width: 100% !important; }
    .product-row > div:nth-child(5) { grid-area: subtotal !important;width: 100% !important; }

    .product-row .product-search-wrapper,
    .product-row .product-search-input,
    .product-row .input-form {
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box !important;
      margin-bottom: 0 !important;
    }

    .product-row > div:nth-child(1)::before { content: "Product";        color: var(--primary);    }
    .product-row > div:nth-child(2)::before { content: "Rate";           color: var(--text-muted); }
    .product-row > div:nth-child(3)::before { content: "Quantity";       color: var(--text-muted); }
    .product-row > div:nth-child(4)::before { content: "Tree Deduction"; color: var(--text-muted); }
    .product-row > div:nth-child(5)::before { content: "Subtotal";       color: var(--text-muted); }

    .product-row > div:nth-child(1)::before,
    .product-row > div:nth-child(2)::before,
    .product-row > div:nth-child(3)::before,
    .product-row > div:nth-child(4)::before,
    .product-row > div:nth-child(5)::before {
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
  let index = {{ count($request->items) }};
  let currentSupplierProducts = [];

  $(document).ready(function () {
    // Load products for the pre-selected supplier on page load (no row clearing)
    const initialSupplierId = $('select[name="supplier_id"]').val();
    if (initialSupplierId) {
      fetchProducts(initialSupplierId, false);
    }

    // Supplier change → clear rows and re-fetch
    $('select[name="supplier_id"]').on('change', function () {
      fetchProducts($(this).val(), true);
    });

    // Global click: close any open dropdowns
    $(document).on('click', function (e) {
      if (!$(e.target).closest('.product-search-wrapper').length) {
        $('.product-search-dropdown').removeClass('open');
      }
    });
  });

  /* ── fetchProducts ───────────────────────────────── */
  function fetchProducts(supplierId, shouldEmpty) {
    if (!supplierId) return;
    $.ajax({
      url: '/manager/stock/get-products/' + supplierId,
      type: 'GET',
      success: function (data) {
        currentSupplierProducts = data;
        if (shouldEmpty) {
          $('#product-wrapper').empty();
          index = 0;
          window.addRow();
        }
        $('#addMoreBtn').prop('disabled', false);
        window.calculateNetTotal();
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        alert('Error loading products.');
      }
    });
  }

  /* ── Build one product-search widget ──────────────── */
  function buildSearchWidget(idx) {
    return `
      <div class="product-search-wrapper">
        <input type="hidden"
               name="products[${idx}][product_id]"
               class="ps-hidden-id">
        <input type="text"
               class="input-form product-search-input"
               placeholder="-- Choose Product --"
               autocomplete="off"
               data-idx="${idx}">
        <div class="product-search-dropdown">
          <div class="ps-no-result">Type 2 chars or 2 spaces to search…</div>
        </div>
      </div>`;
  }

  /* ── Helper to get already selected product IDs ──── */
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
      ? currentSupplierProducts
      : (trimmed.length >= 2
          ? currentSupplierProducts.filter(p =>
              p.name.toLowerCase().includes(trimmed))
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

    results.forEach(p => {
      const isAlreadyAdded = selectedIds.includes(p.id.toString());
      if (isAlreadyAdded) {
        $dropdown.append(
          `<div class="ps-option ps-already-added"
                data-id="${p.id}"
                data-price="${p.price || 0}"
                data-name="${p.name}"
                style="opacity: 0.6; cursor: not-allowed; background: #fff0f0; color: #dc3545;">
            ${p.name} <small style="font-weight: 700; float: right; color: #dc3545;">(Already Added)</small>
           </div>`
        );
      } else {
        $dropdown.append(
          `<div class="ps-option"
                data-id="${p.id}"
                data-price="${p.price || 0}"
                data-name="${p.name}">${p.name}</div>`
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

    const id    = $option.data('id');
    const price = parseFloat($option.data('price')) || 0;
    const name  = $option.data('name') || $option.text();

    // Prevent selecting duplicate products
    if ($option.hasClass('ps-already-added')) {
      alert(`"${name}" is already added in another row!`);
      return;
    }

    const currentHidden = $wrapper.find('.ps-hidden-id');
    const selectedIds = getSelectedProductIds(currentHidden);
    if (selectedIds.includes(id.toString())) {
      alert(`"${name}" is already added in another row!`);
      return;
    }

    currentHidden.val(id);
    $wrapper.find('.product-search-input').val(name).css('border', '');

    const qty = parseFloat($row.find('.qty').val()) || 0;
    $row.find('.rate').val(price.toFixed(2));
    $row.find('.subtotal').val((price * qty).toFixed(2));

    $wrapper.find('.product-search-dropdown').removeClass('open');
    window.calculateNetTotal();
  });

  /* ── addRow ──────────────────────────────────────── */
  window.addRow = function () {
    if (currentSupplierProducts.length === 0) {
      alert('Please select a supplier first!');
      return;
    }

    const html = `
    <div class="product-row animate__animated animate__fadeIn"
         style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
      <div>${buildSearchWidget(index)}</div>
      <div>
        <input type="number" class="input-form rate"
               placeholder="0.00" readonly tabindex="-1">
      </div>
      <div>
        <input type="number" name="products[${index}][qty]"
               class="input-form qty" placeholder="Qty"
               min="1" required oninput="updateRow(this)">
      </div>
      <div>
        <input type="number" name="products[${index}][tree_deduction]"
               class="input-form tree-ded" placeholder="0.00"
               min="0" step="0.01" value="0">
      </div>
      <div>
        <input type="number" class="input-form subtotal"
               placeholder="0.00" readonly tabindex="-1">
      </div>
      <div>
        <button type="button" class="icon-btn delete-icon"
                onclick="removeRow(this)">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>`;

    $('#product-wrapper').append(html);
    index++;
  };

  /* ── updateRow ───────────────────────────────────── */
  window.updateRow = function (el) {
    const $row  = $(el).closest('.product-row');
    const price = parseFloat($row.find('.rate').val()) || 0;
    const qty   = parseFloat($row.find('.qty').val())  || 0;
    $row.find('.subtotal').val((price * qty).toFixed(2));
    window.calculateNetTotal();
  };

  /* ── removeRow ───────────────────────────────────── */
  window.removeRow = function (btn) {
    if ($('.product-row').length > 1) {
      $(btn).closest('.product-row').remove();
      window.calculateNetTotal();
    } else {
      alert('At least one product is required.');
    }
  };

  /* ── calculateNetTotal ───────────────────────────── */
  window.calculateNetTotal = function () {
    let total = 0;
    $('.subtotal').each(function () {
      total += parseFloat($(this).val()) || 0;
    });
    $('#netTotalDisplay').text(
      total.toLocaleString(undefined, { minimumFractionDigits: 2 })
    );
    $('#netTotalInput').val(total.toFixed(2));
  };

  /* ── validateProducts ────────────────────────────── */
  window.validateProducts = function (e) {
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
        alert('Duplicate products detected! Each product can only be added once.');
      } else {
        alert('Please select a product for every row before submitting.');
      }
      return false;
    }
    return true;
  };
</script>
@endpush