@extends('layouts.adminlayout')

@section('title', 'Barcode Print')

@section('content')

<style>
  /* ====== Page Layout ====== */
  .barcode-page-wrapper {
    padding: 20px;
  }

  /* ====== Title ====== */
  .barcode-page-title {
    text-align: center;
    margin-bottom: 24px;
  }
  .bp-title-ornament {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary);
  }
  .bp-title-ornament .bp-divider {
    width: 60px;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--primary), transparent);
  }
  .bp-title-ornament .bp-bars {
    display: flex;
    align-items: center;
    gap: 2px;
  }
  .bp-title-ornament .bp-bars span {
    display: block;
    width: 3px;
    background: var(--primary);
    border-radius: 2px;
  }
  .bp-title-ornament .bp-bars span:nth-child(1) { height: 18px; }
  .bp-title-ornament .bp-bars span:nth-child(2) { height: 12px; }
  .bp-title-ornament .bp-bars span:nth-child(3) { height: 18px; }
  .bp-title-ornament .bp-bars span:nth-child(4) { height: 12px; }

  /* ====== Two-Column Layout ====== */
  .bp-layout {
    display: grid;
    grid-template-columns: 230px 1fr;
    gap: 20px;
    align-items: start;
  }

  /* ====== Left Control Panel ====== */
  .bp-control-panel {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 18px 14px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: sticky;
    top: 80px;
  }
  .bp-ctrl-group {}
  .bp-ctrl-label {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    color: var(--primary) !important;
    margin-bottom: 7px !important;
    width: 100% !important;
  }
  .bp-ctrl-label i {
    font-size: 0.8rem;
  }
  .bp-ctrl-group select,
  .bp-ctrl-group input[type="number"],
  .bp-ctrl-group input[type="text"] {
    width: 100%;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 7px 10px;
    font-size: 0.84rem;
    background: #fff;
    color: #333;
    transition: border-color 0.2s;
    outline: none;
    display: block;
  }
  .bp-ctrl-group select:focus,
  .bp-ctrl-group input:focus {
    border-color: var(--primary);
    box-shadow: none;
  }

  /* ====== Smart Search Autocomplete ====== */
  .bp-search-wrap {
    position: relative;
  }
  .bp-search-input-box {
    display: flex;
    align-items: center;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
    overflow: hidden;
    transition: border-color 0.2s;
  }
  .bp-search-input-box:focus-within {
    border-color: var(--primary);
  }
  .bp-search-input-box i {
    padding: 0 6px 0 10px;
    color: #aaa;
    font-size: 0.8rem;
    flex-shrink: 0;
    pointer-events: none;
  }
  .bp-search-input-box input {
    flex: 1;
    border: none !important;
    outline: none !important;
    padding: 7px 6px 7px 2px !important;
    font-size: 0.84rem !important;
    background: transparent !important;
    color: #333 !important;
    width: 100% !important;
    border-radius: 0 !important;
    min-width: 0;
  }
  .bp-search-clear {
    padding: 0 8px;
    background: none;
    border: none;
    color: #bbb;
    cursor: pointer;
    font-size: 0.8rem;
    flex-shrink: 0;
    display: none;
    line-height: 1;
  }
  .bp-search-clear:hover { color: #888; }

  /* Dropdown */
  .bp-search-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    max-height: 230px;
    overflow-y: auto;
    z-index: 9999;
  }
  .bp-search-dropdown.open { display: block; }
  .bp-dd-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.15s;
  }
  .bp-dd-item:last-child { border-bottom: none; }
  .bp-dd-item:hover,
  .bp-dd-item.focused {
    background: rgba(2, 2, 226, 0.06);
  }
  .bp-dd-item .bp-dd-name {
    font-size: 0.84rem;
    font-weight: 500;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .bp-dd-item .bp-dd-name mark {
    background: rgba(2,2,226,0.12);
    color: var(--primary);
    border-radius: 2px;
    padding: 0 1px;
    font-weight: 700;
  }
  .bp-dd-no-result {
    padding: 14px 12px;
    text-align: center;
    color: #aaa;
    font-size: 0.82rem;
  }

  /* Selected product chip */
  .bp-selected-chip {
    display: none;
    align-items: center;
    gap: 6px;
    background: rgba(2,2,226,0.07);
    border: 1px solid rgba(2,2,226,0.2);
    border-radius: 6px;
    padding: 5px 10px;
    margin-top: 5px;
    font-size: 0.78rem;
  }
  .bp-selected-chip.show { display: flex; }
  .bp-selected-chip .bp-chip-name {
    flex: 1;
    font-weight: 600;
    color: var(--primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .bp-selected-chip .bp-chip-code {
    font-size: 0.7rem;
    color: #555;
    flex-shrink: 0;
  }
  .bp-ctrl-hint {
    font-size: 0.72rem;
    color: #888;
    margin-top: 4px;
    display: block;
  }

  /* Size Buttons */
  .bp-size-group {
    display: flex;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
  }
  .bp-size-btn {
    flex: 1;
    padding: 6px 0;
    font-size: 0.8rem;
    font-weight: 500;
    border: none;
    background: #fff;
    cursor: pointer;
    color: #555;
    transition: background 0.2s, color 0.2s;
    line-height: 1;
  }
  .bp-size-btn:not(:last-child) {
    border-right: 1px solid #dee2e6;
  }
  .bp-size-btn.active {
    background: var(--primary);
    color: #fff;
    font-weight: 600;
  }

  /* Display Options Checkboxes */
  .bp-display-opts {
    display: flex;
    flex-direction: column;
    gap: 7px;
  }
  .bp-display-opts .bp-check-label {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 0.84rem !important;
    cursor: pointer !important;
    color: #333 !important;
    font-weight: 400 !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    margin-bottom: 0 !important;
    width: auto !important;
  }
  .bp-display-opts input[type="checkbox"] {
    width: 15px;
    height: 15px;
    accent-color: var(--primary);
    cursor: pointer;
    flex-shrink: 0;
  }

  /* Print Button */
  .bp-print-btn {
    width: 100%;
    padding: 10px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s, transform 0.1s;
  }
  .bp-print-btn:hover {
    background: var(--primary-light, #4466ff);
    transform: translateY(-1px);
    color: #fff;
  }

  /* ====== Right Preview Panel ====== */
  .bp-preview-panel {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
  }
  .bp-preview-header {
    background: linear-gradient(135deg, var(--primary, #0202e2), var(--primary-light, #4466ff));
    color: #fff;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .bp-preview-title {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .bp-count-badge {
    background: rgba(255,255,255,0.25);
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
  }
  .bp-preview-body {
    padding: 16px;
    min-height: 360px;
  }

  /* ====== Stickers Grid ====== */
  #bpStickersGrid {
    display: grid;
    gap: 8px;
    width: 100%;
    min-height: 320px;
  }
  #bpStickersGrid.bp-size-small  { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
  #bpStickersGrid.bp-size-medium { grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); }
  #bpStickersGrid.bp-size-large  { grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); }

  .bp-sticker {
    border: 1.5px dashed #bbb;
    border-radius: 8px;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 6px 4px 5px;
    text-align: center;
  }
  .bp-sticker .bp-s-name {
    font-size: 0.72rem;
    font-weight: 600;
    color: #222;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    padding: 0 4px;
  }
  /* Size-specific name font sizes */
  #bpStickersGrid.bp-size-large  .bp-s-name { font-size: 0.72rem; }
  #bpStickersGrid.bp-size-medium .bp-s-name { font-size: 0.62rem; }
  #bpStickersGrid.bp-size-small  .bp-s-name { font-size: 0.55rem; }
  .bp-sticker svg {
    max-width: 100%;
    height: auto;
  }
  .bp-sticker .bp-s-code {
    font-size: 0.65rem;
    color: var(--primary, #0202e2);
    font-weight: 600;
    margin-top: 2px;
    letter-spacing: 0.5px;
  }
  .bp-sticker .bp-s-price {
    font-size: 0.7rem;
    font-weight: 700;
    color: #333;
    margin-top: 2px;
  }

  /* Empty State */
  .bp-empty {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
    width: 100%;
    min-height: 280px;
    margin: 0 auto;
  }
  .bp-empty i {
    font-size: 3.5rem;
    margin-bottom: 14px;
    display: block;
    color: #cbd5e1;
  }
  .bp-empty div {
    font-size: 0.95rem;
    color: #64748b;
    font-weight: 500;
  }

  /* ====== PRINT STYLES ====== */
  @media print {
    body > * { visibility: hidden; }
    #bpPrintArea, #bpPrintArea * { visibility: visible; }
    #bpPrintArea {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
    }
    #bpStickersGrid {
      display: grid !important;
      gap: 4px !important;
    }
    #bpStickersGrid.bp-size-small  { grid-template-columns: repeat(6, 1fr) !important; }
    #bpStickersGrid.bp-size-medium { grid-template-columns: repeat(4, 1fr) !important; }
    #bpStickersGrid.bp-size-large  { grid-template-columns: repeat(3, 1fr) !important; }
    .bp-sticker {
      page-break-inside: avoid;
      border: 1px dashed #999 !important;
      padding: 4px !important;
    }
    @page {
      margin: 8mm;
      size: auto;
    }
  }
</style>

<div class="barcode-page-wrapper">

  {{-- ===== Title ===== --}}
  <div class="barcode-page-title">
    <div class="bp-title-ornament">
      <div class="bp-divider"></div>
      <span>•</span>
      <div class="bp-bars">
        <span></span><span></span><span></span><span></span>
      </div>
      <span>Print Barcode</span>
      <div class="bp-bars">
        <span></span><span></span><span></span><span></span>
      </div>
      <span>•</span>
      <div class="bp-divider"></div>
    </div>
  </div>

  {{-- ===== Two-Column Layout ===== --}}
  <div class="bp-layout">

    {{-- ===== LEFT: Controls ===== --}}
    <div class="bp-control-panel">

      {{-- Product Smart Search --}}
      <div class="bp-ctrl-group">
        <label class="bp-ctrl-label"><i class="fa-solid fa-box"></i> Select Product</label>
        <div class="bp-search-wrap" id="bpSearchWrap">
          <div class="bp-search-input-box">
            
            <input
              type="text"
              id="bpSearchInput"
              placeholder="Type product name to search..."
              autocomplete="off"
            >
            <button class="bp-search-clear" id="bpSearchClear" type="button" title="Clear">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div class="bp-search-dropdown" id="bpSearchDropdown"></div>
        </div>
        {{-- Selected Product Chip --}}
        <div class="bp-selected-chip" id="bpSelectedChip">
          <span class="bp-chip-name" id="bpChipName">—</span>
          <span class="bp-chip-code" id="bpChipCode"></span>
        </div>
      </div>

      {{-- Barcode Format --}}
      <div class="bp-ctrl-group">
        <label class="bp-ctrl-label"><i class="fa-solid fa-sliders"></i> Barcode Format</label>
        <select id="bpBarcodeFormat">
          <option value="CODE128" selected>CODE128 (Recommended)</option>
          <option value="auto">Auto Detect (EAN13 / CODE128)</option>
          <option value="EAN13">EAN13</option>
          <option value="CODE39">CODE39</option>
          <option value="UPC">UPC-A</option>
        </select>
        <span class="bp-ctrl-hint" id="bpFormatHint">CODE128 — সব bar সমান, সব ধরনের barcode support করে।</span>
      </div>

      {{-- Sticker Size --}}
      <div class="bp-ctrl-group">
        <label class="bp-ctrl-label"><i class="fa-solid fa-expand"></i> Sticker Size</label>
        <div class="bp-size-group">
          <button class="bp-size-btn" data-size="small">Small</button>
          <button class="bp-size-btn active" data-size="medium">Medium</button>
          <button class="bp-size-btn" data-size="large">Large</button>
        </div>
      </div>

      {{-- Quantity --}}
      <div class="bp-ctrl-group">
        <label class="bp-ctrl-label"><i class="fa-solid fa-arrow-up-1-9"></i> Quantity</label>
        <input type="number" id="bpStickerQty" value="28" min="1" max="200">
        <span class="bp-ctrl-hint">কতটি স্টিকার প্রিন্ট করবেন</span>
      </div>

      {{-- Custom Price --}}
      <div class="bp-ctrl-group">
        <label class="bp-ctrl-label"><i class="fa-solid fa-tag"></i> Custom Price</label>
        <input type="text" id="bpCustomPrice" placeholder="খালি রাখলে = product price">
      </div>

      {{-- Display Options --}}
      <div class="bp-ctrl-group">
        <label class="bp-ctrl-label"><i class="fa-solid fa-eye"></i> Display Options</label>
        <div class="bp-display-opts">
          <label class="bp-check-label">
            <input type="checkbox" id="bpShowName" checked> Show Product Name
          </label>
          <label class="bp-check-label">
            <input type="checkbox" id="bpShowNumber" checked> Show Barcode Number
          </label>
          <label class="bp-check-label">
            <input type="checkbox" id="bpShowPrice"> Show Price
          </label>
        </div>
      </div>

      {{-- Print Button --}}
      <button class="bp-print-btn" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print Stickers
      </button>

    </div>
    {{-- END Left Controls --}}

    {{-- ===== RIGHT: Preview ===== --}}
    <div class="bp-preview-panel">
      <div class="bp-preview-header">
        <div class="bp-preview-title">
          <i class="fa-solid fa-barcode"></i> BARCODE PREVIEW
        </div>
        <div class="bp-count-badge" id="bpCountBadge">0 STICKERS</div>
      </div>
      <div class="bp-preview-body" id="bpPrintArea">
        <div id="bpStickersGrid" class="bp-size-medium">
          <div class="bp-empty" id="bpEmptyState">
            <i class="fa-solid fa-box-open"></i>
            <div>উপরে থেকে একটি Product সিলেক্ট করুন</div>
          </div>
        </div>
      </div>
    </div>
    {{-- END Right Preview --}}

  </div>
</div>

@endsection

@push('scripts')
@php
  $productsJson = $products->map(function($p) {
    $bcode = $p->barcode ?: ($p->sku ?: str_pad($p->id, 8, '0', STR_PAD_LEFT));
    return [
      'id'      => $p->id,
      'name'    => $p->name,
      'barcode' => $bcode,
      'sku'     => $p->sku ?? '',
      'price'   => number_format($p->price, 2),
    ];
  })->values();
@endphp
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
(function () {
  'use strict';

  // ============================================================
  // 1. PRODUCT DATA (from server)
  // ============================================================
  var allProducts = @json($productsJson);

  // ============================================================
  // 2. STATE
  // ============================================================
  var currentProduct = null;
  var currentSize    = 'medium';
  var currentFormat  = 'CODE128';
  var focusedIndex   = -1;

  // ============================================================
  // 3. ELEMENTS
  // ============================================================
  var searchInput   = document.getElementById('bpSearchInput');
  var searchClear   = document.getElementById('bpSearchClear');
  var dropdown      = document.getElementById('bpSearchDropdown');
  var selectedChip  = document.getElementById('bpSelectedChip');
  var chipName      = document.getElementById('bpChipName');
  var chipCode      = document.getElementById('bpChipCode');
  var barcodeFormat = document.getElementById('bpBarcodeFormat');
  var formatHint    = document.getElementById('bpFormatHint');
  var stickerQty    = document.getElementById('bpStickerQty');
  var customPrice   = document.getElementById('bpCustomPrice');
  var showName      = document.getElementById('bpShowName');
  var showNumber    = document.getElementById('bpShowNumber');
  var showPrice     = document.getElementById('bpShowPrice');
  var stickersGrid  = document.getElementById('bpStickersGrid');
  var countBadge    = document.getElementById('bpCountBadge');
  var emptyState    = document.getElementById('bpEmptyState');
  var sizeBtns      = document.querySelectorAll('.bp-size-btn');

  // ============================================================
  // 4. FORMAT HINTS
  // ============================================================
  var formatHints = {
    CODE128: 'CODE128 — সব bar সমান উচ্চতা, সব ধরনের character support করে।',
    auto:    'Auto — 12-13 digit হলে EAN13, না হলে CODE128।',
    EAN13:   'EAN-13 — guard bar লম্বা হয়, GS1 standard।',
    CODE39:  'CODE39 — A-Z, 0-9 এবং special character support করে।',
    UPC:     'UPC-A — 11 বা 12 digit প্রয়োজন।'
  };

  // ============================================================
  // 5. HELPERS
  // ============================================================
  function esc(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function highlight(text, query) {
    if (!query) return esc(text);
    var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    var re = new RegExp('(' + escaped + ')', 'gi');
    return esc(text).replace(re, '<mark>$1</mark>');
  }

  function resolveFormat(barcode) {
    if (currentFormat !== 'auto') return currentFormat;
    if (/^\d{12,13}$/.test(barcode)) return 'EAN13';
    if (/^\d{11,12}$/.test(barcode)) return 'UPC';
    return 'CODE128';
  }

  // ============================================================
  // 6. SMART SEARCH
  // ============================================================
  function filterProducts(query) {
    var q = query.trim();
    // Two spaces = show all
    if (query === '  ' || query.endsWith('  ')) return allProducts;
    // Less than 2 chars = show nothing
    if (q.length < 2) return [];
    var lower = q.toLowerCase();
    return allProducts.filter(function (p) {
      return p.name.toLowerCase().indexOf(lower) !== -1
          || (p.sku && p.sku.toLowerCase().indexOf(lower) !== -1)
          || p.barcode.toLowerCase().indexOf(lower) !== -1;
    });
  }

  function openDropdown(results, query) {
    focusedIndex = -1;
    if (results.length === 0) {
      dropdown.innerHTML = '<div class="bp-dd-no-result"><i class="fa-solid fa-face-frown"></i> কোনো product পাওয়া যায়নি</div>';
    } else {
      var html = '';
      results.forEach(function (p, idx) {
        html += '<div class="bp-dd-item" data-idx="' + idx + '">'
          + '<div class="bp-dd-name">' + highlight(p.name, query.trim()) + '</div>'
          + '</div>';
      });
      dropdown.innerHTML = html;

      // Click on item
      dropdown.querySelectorAll('.bp-dd-item').forEach(function (el) {
        el.addEventListener('mousedown', function (e) {
          e.preventDefault(); // prevent blur
          var idx = parseInt(el.getAttribute('data-idx'));
          selectProduct(results[idx]);
        });
      });
    }
    dropdown.classList.add('open');
  }

  function closeDropdown() {
    dropdown.classList.remove('open');
    focusedIndex = -1;
  }

  function selectProduct(product) {
    currentProduct = product;
    // Update chip
    chipName.textContent = product.name;
    chipCode.textContent = product.barcode;
    selectedChip.classList.add('show');
    // Update input
    searchInput.value = product.name;
    searchClear.style.display = 'inline-flex';
    closeDropdown();
    renderStickers();
  }

  function clearSelection() {
    currentProduct = null;
    searchInput.value = '';
    searchClear.style.display = 'none';
    selectedChip.classList.remove('show');
    closeDropdown();
    renderStickers();
  }

  // Keyboard navigation
  function moveFocus(dir) {
    var items = dropdown.querySelectorAll('.bp-dd-item');
    if (!items.length) return;
    items[focusedIndex] && items[focusedIndex].classList.remove('focused');
    focusedIndex = (focusedIndex + dir + items.length) % items.length;
    items[focusedIndex].classList.add('focused');
    items[focusedIndex].scrollIntoView({ block: 'nearest' });
  }

  // ============================================================
  // 7. SEARCH EVENTS
  // ============================================================
  searchInput.addEventListener('input', function () {
    var query = this.value;
    searchClear.style.display = query.length ? 'inline-flex' : 'none';

    // If user edits after selection, clear selection
    if (currentProduct && query !== currentProduct.name) {
      currentProduct = null;
      selectedChip.classList.remove('show');
      countBadge.textContent = '0 STICKERS';
      stickersGrid.innerHTML = '';
      stickersGrid.appendChild(emptyState);
      emptyState.style.display = 'flex';
    }

    var results = filterProducts(query);
    if (results.length > 0 || (query.trim().length >= 2)) {
      openDropdown(results, query);
    } else {
      closeDropdown();
    }
  });

  searchInput.addEventListener('keydown', function (e) {
    if (!dropdown.classList.contains('open')) {
      // Press space-space or any key to open
      return;
    }
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      moveFocus(1);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      moveFocus(-1);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      var focused = dropdown.querySelector('.bp-dd-item.focused');
      if (focused) focused.dispatchEvent(new MouseEvent('mousedown'));
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  });

  searchInput.addEventListener('focus', function () {
    // Show all if input has 2+ chars (re-open if closed)
    if (this.value.trim().length >= 2 || this.value === '  ') {
      var results = filterProducts(this.value);
      openDropdown(results, this.value);
    }
  });

  searchInput.addEventListener('blur', function () {
    // Small delay so mousedown click on item can fire first
    setTimeout(closeDropdown, 180);
  });

  searchClear.addEventListener('click', function () {
    clearSelection();
    searchInput.focus();
  });

  // ============================================================
  // 8. OTHER CONTROL EVENTS
  // ============================================================
  barcodeFormat.addEventListener('change', function () {
    currentFormat = this.value;
    formatHint.textContent = formatHints[currentFormat] || '';
    renderStickers();
  });

  sizeBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      sizeBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      currentSize = btn.getAttribute('data-size');
      stickersGrid.className = 'bp-size-' + currentSize;
      renderStickers();
    });
  });

  stickerQty.addEventListener('input', renderStickers);
  customPrice.addEventListener('input', renderStickers);
  showName.addEventListener('change', renderStickers);
  showNumber.addEventListener('change', renderStickers);
  showPrice.addEventListener('change', renderStickers);

  // ============================================================
  // 9. RENDER STICKERS
  // ============================================================
  function renderStickers() {
    if (!currentProduct) {
      stickersGrid.innerHTML = '';
      stickersGrid.appendChild(emptyState);
      emptyState.style.display = 'flex';
      countBadge.textContent = '0 STICKERS';
      return;
    }

    var qty     = Math.max(1, Math.min(200, parseInt(stickerQty.value) || 28));
    var barcode = currentProduct.barcode;
    var name    = currentProduct.name;
    var price   = customPrice.value.trim() || currentProduct.price;
    var fmt     = resolveFormat(barcode);
    var bheight = currentSize === 'small' ? 28 : currentSize === 'large' ? 55 : 40;

    countBadge.textContent = qty + ' STICKERS';
    emptyState.style.display = 'none';

    var html = '';
    for (var i = 0; i < qty; i++) {
      html += '<div class="bp-sticker">';
      if (showName.checked) {
        html += '<div class="bp-s-name">' + esc(name) + '</div>';
      }
      html += '<svg id="bpSvg_' + i + '"></svg>';
      if (showNumber.checked) {
        html += '<div class="bp-s-code">' + esc(barcode) + '</div>';
      }
      if (showPrice.checked) {
        html += '<div class="bp-s-price">৳ ' + esc(String(price)) + '</div>';
      }
      html += '</div>';
    }

    stickersGrid.innerHTML = html;

    for (var j = 0; j < qty; j++) {
      try {
        JsBarcode('#bpSvg_' + j, barcode, {
          format:       fmt,
          width:        1.2,
          height:       bheight,
          displayValue: false,
          margin:       2,
          lineColor:    '#000',
          background:   '#fff'
        });
      } catch (e) {
        var el = document.getElementById('bpSvg_' + j);
        if (el) {
          el.outerHTML = '<div style="color:#e74c3c;font-size:0.65rem;padding:4px;">Invalid barcode</div>';
        }
      }
    }
  }

  // ============================================================
  // 10. AUTO-SELECT IF product_id IN URL
  // ============================================================
  @if($selectedProductId)
  (function () {
    var pid = {{ (int) $selectedProductId }};
    var found = allProducts.find(function (p) { return p.id === pid; });
    if (found) selectProduct(found);
  })();
  @endif

  // ============================================================
  // 11. INITIAL STATE
  // ============================================================
  stickersGrid.innerHTML = '';
  stickersGrid.appendChild(emptyState);
  emptyState.style.display = 'flex';

})();
</script>
@endpush
