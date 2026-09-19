@extends('layouts.userlayout')

@section('title', (!empty($offersOnly) ? 'Special Offers — ' : ($currentCategory ? $currentCategory->name . ' — ' : '')) . 'Shop — R Electric')

@section('content')
<style>
/* ===================== CLEAN & OPTIMIZED SHOP STYLES ===================== */
.shop-page { padding: 20px 15px 50px; }

/* Header Row */
.shop-header-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
.shop-header-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.shop-page-title { font-size: 20px; font-weight: 800; color: var(--text-main); margin: 0; font-family: "El Messiri", sans-serif; display: flex; align-items: center; gap: 8px; line-height: 1; }
.shop-page-title i { color: var(--primary); font-size: 17px; }
.shop-breadcrumb { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; color: var(--text-muted); margin: 0; }
.shop-breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color .2s; }
.shop-breadcrumb a:hover { color: var(--primary); }
.shop-breadcrumb .sep { opacity: .45; font-size: 8px; }
.shop-breadcrumb .current { color: var(--text-main); font-weight: 600; }
.shop-results-badge { display: inline-flex; align-items: center; gap: 5px; background: var(--primary-soft); color: var(--primary); border: 1px solid rgba(2, 2, 226, 0.12); padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }

/* Toolbar: Search, Sort & Stock */
.shop-toolbar { background: var(--section-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 8px 12px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); }
.shop-toolbar-inner { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
.shop-search-form { flex: 1; min-width: 220px; position: relative; }
.shop-search-input-wrap { width: 100%; position: relative; display: flex; align-items: center; }
.shop-search-icon { position: absolute; left: 12px; color: var(--text-muted); font-size: 13px; pointer-events: none; }
.shop-search-spinner { position: absolute; right: 32px; color: var(--primary); font-size: 13px; display: none; animation: spin .8s linear infinite; }
@keyframes spin { 100% { transform: rotate(360deg); } }
.shop-search-input { width: 100%; height: 36px; padding: 6px 36px 6px 32px; background: var(--background); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-size: 12.5px; outline: none; transition: all .2s; }
.shop-search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary-soft); }
.shop-search-input:focus ~ .shop-search-icon { color: var(--primary); }
.shop-search-clear { position: absolute; right: 10px; background: none; border: none; color: var(--text-muted); font-size: 13px; cursor: pointer; padding: 2px; display: none; }
.shop-search-clear:hover { color: var(--text-main); }
.shop-search-submit { display: none; }

.shop-toolbar-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.shop-sort-wrap, .shop-stock-toggle { display: inline-flex; align-items: center; gap: 6px; background: var(--background); border: 1px solid var(--border-color); border-radius: 8px; padding: 0 10px; height: 36px; font-size: 12px; font-weight: 600; color: var(--text-main); }
.shop-sort-label { font-size: 11.5px; font-weight: 700; color: var(--text-muted); white-space: nowrap; display: flex; align-items: center; gap: 4px; }
.shop-sort-select { background: transparent; border: none; color: var(--text-main); font-size: 12px; font-weight: 600; cursor: pointer; outline: none; }
.shop-sort-select option { background: var(--section-bg); color: var(--text-main); }

.shop-stock-toggle { cursor: pointer; user-select: none; transition: all .2s; }
.shop-stock-toggle.active { border-color: #059669; background: rgba(5, 150, 105, 0.08); color: #059669; }
.shop-stock-toggle input { display: none; }
.shop-stock-toggle .toggle-icon { width: 13px; height: 13px; border-radius: 3px; border: 1px solid var(--border-color); display: inline-flex; align-items: center; justify-content: center; font-size: 8px; color: transparent; transition: all .2s; }
.shop-stock-toggle input:checked ~ .toggle-icon { background: #059669; border-color: #059669; color: #fff; }

/* Category Tabs / Pills */
.shop-cat-tabs-wrap { position: relative; margin-bottom: 14px; }
.shop-cat-tabs { display: flex; align-items: center; gap: 6px; overflow-x: auto; padding: 2px 2px 6px; scrollbar-width: thin; scrollbar-color: var(--primary) transparent; -webkit-overflow-scrolling: touch; }
.shop-cat-tabs::-webkit-scrollbar { height: 3px; }
.shop-cat-tabs::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 6px; }
.shop-cat-tab { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; background: var(--section-bg); border: 1px solid var(--border-color); border-radius: 20px; font-size: 11.5px; font-weight: 600; color: var(--text-muted); text-decoration: none; white-space: nowrap; flex-shrink: 0; transition: all .2s; cursor: pointer; }
.shop-cat-tab:hover { color: var(--primary); border-color: rgba(2, 2, 226, 0.35); transform: translateY(-1px); }
.shop-cat-tab.active { background: linear-gradient(135deg, var(--primary), var(--accent)); border-color: transparent; color: #fff; font-weight: 700; box-shadow: 0 2px 8px rgba(2, 2, 226, 0.25); }
.shop-cat-tab .cat-count { display: inline-flex; align-items: center; justify-content: center; background: var(--background); color: var(--text-main); font-size: 10px; font-weight: 700; padding: 1px 5px; border-radius: 12px; line-height: 1.2; }
.shop-cat-tab.active .cat-count { background: rgba(255, 255, 255, 0.25); color: #fff; }

/* Active Filter Chips */
.shop-active-filters { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; padding: 6px 10px; background: var(--primary-soft); border: 1px dashed rgba(2, 2, 226, 0.2); border-radius: 8px; font-size: 11px; }
.shop-active-filters-title { font-size: 11px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 4px; }
.shop-filter-chip { display: inline-flex; align-items: center; gap: 5px; background: var(--section-bg); border: 1px solid var(--border-color); color: var(--text-main); font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 14px; }
.shop-filter-chip a { color: var(--text-muted); text-decoration: none; font-size: 10px; transition: color .2s; display: flex; align-items: center; }
.shop-filter-chip a:hover { color: #dc2626; }
.shop-clear-all-link { font-size: 11px; font-weight: 700; color: #dc2626; text-decoration: none; margin-left: auto; cursor: pointer; }
.shop-clear-all-link:hover { text-decoration: underline; opacity: .85; }

/* Products Grid & Loading */
.shop-products-section { min-height: 350px; transition: opacity .2s; }
.shop-products-section.loading { opacity: 0.55; pointer-events: none; }

/* Empty State */
.shop-empty-state { text-align: center; padding: 45px 20px; background: var(--section-bg); border: 1px solid var(--border-color); border-radius: 14px; margin: 16px 0; }
.shop-empty-icon { width: 64px; height: 64px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 12px; }
.shop-empty-title { font-size: 17px; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
.shop-empty-desc { font-size: 13px; color: var(--text-muted); max-width: 400px; margin: 0 auto 16px; }
.shop-btn-reset { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, var(--primary), var(--accent)); color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: 13px; font-weight: 700; text-decoration: none; transition: all .2s; cursor: pointer; }
.shop-btn-reset:hover { opacity: .92; color: #fff; transform: translateY(-1px); }

/* Pagination */
.shop-pagination-wrap { margin-top: 30px; display: flex; justify-content: center; }
.shop-pagination-wrap .pagination { gap: 4px; }
.shop-pagination-wrap .page-item .page-link { background: var(--section-bg); border: 1px solid var(--border-color); color: var(--text-main); font-size: 12.5px; font-weight: 600; border-radius: 8px; padding: 6px 12px; transition: all .2s; }
.shop-pagination-wrap .page-item.active .page-link { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 3px 8px rgba(2, 2, 226, 0.22); }
.shop-pagination-wrap .page-item .page-link:hover { background: var(--primary-soft); border-color: var(--primary); color: var(--primary); }

/* Mobile Responsive */
@media (max-width: 768px) {
  .shop-page { padding: 12px 16px 35px; }
  .shop-header-row { margin-bottom: 8px; gap: 8px; }
  .shop-page-title { font-size: 16px; }
  .shop-page-title i { font-size: 15px; }
  .shop-breadcrumb, .shop-results-badge { font-size: 10.5px; }
  .shop-results-badge { padding: 2px 8px; }
  .shop-toolbar { padding: 8px 10px; border-radius: 10px; margin-bottom: 8px; }
  .shop-toolbar-inner { flex-direction: column; align-items: stretch; gap: 6px; }
  .shop-search-form { min-width: 100%; }
  .shop-search-input { height: 34px; font-size: 12px; }
  .shop-toolbar-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
  .shop-sort-wrap, .shop-stock-toggle { height: 32px; padding: 0 8px; font-size: 11px; justify-content: center; border-radius: 6px; }
  .shop-sort-label, .shop-sort-select { font-size: 10.5px; }
  .shop-cat-tabs-wrap { margin-bottom: 10px; }
  .shop-cat-tabs { gap: 5px; padding-bottom: 4px; }
  .shop-cat-tab { padding: 4px 10px; font-size: 11px; border-radius: 16px; }
  .shop-cat-tab .cat-count { font-size: 9px; padding: 1px 4px; }
  .shop-active-filters { padding: 5px 8px; font-size: 10.5px; margin-bottom: 10px; }
  .shop-filter-chip { font-size: 10.5px; padding: 2px 6px; }
  .shop-clear-all-link { width: 100%; margin-top: 2px; font-size: 10.5px; }
}
</style>

<div class="container shop-page">

  {{-- Compact Header: Breadcrumb + Title + Count --}}
  <div class="shop-header-row">
    <div class="shop-header-left">
      <h1 class="shop-page-title" id="shopPageTitle">
        <i class="{{ !empty($offersOnly) ? 'fas fa-percent text-danger' : 'fas fa-store' }}"></i>
        <span>
          @if(!empty($offersOnly))
            Special Offers
          @elseif($currentCategory)
            {{ $currentCategory->name }}
          @elseif(!empty($search))
            "{{ $search }}"
          @else
            Shop
          @endif
        </span>
      </h1>

      <nav class="shop-breadcrumb">
        <a href="{{ route('home-page') }}">Home</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <span id="shopBreadcrumbCat">
          @if(!empty($offersOnly))
            <a href="javascript:void(0)" onclick="selectCategory('all', null, event)">Shop</a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Special Offers</span>
          @elseif($currentCategory)
            <a href="javascript:void(0)" onclick="selectCategory('all', null, event)">Shop</a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <span class="current">{{ $currentCategory->name }}</span>
          @else
            <span class="current">All Products</span>
          @endif
        </span>
      </nav>
    </div>

    <div class="shop-results-badge" id="shopResultsBadge">
      <i class="fas fa-boxes-stacked"></i>
      <span id="shopResultsCountText">{{ $products->total() }} items</span>
    </div>
  </div>

  {{-- Compact Search & Controls Toolbar --}}
  <div class="shop-toolbar">
    <div class="shop-toolbar-inner">

      {{-- Live Search Form --}}
      <form action="{{ route('shop') }}" method="GET" class="shop-search-form" id="shopSearchForm" onsubmit="event.preventDefault(); triggerLiveSearch();">
        <div class="shop-search-input-wrap">
          <i class="fas fa-search shop-search-icon"></i>
          <input type="text"
            name="search"
            id="shopSearchInput"
            class="shop-search-input"
            placeholder="Live search by product name or brand"
            value="{{ $search }}"
            autocomplete="off"
            oninput="handleSearchInput(this.value)">
          <i class="fas fa-circle-notch shop-search-spinner" id="shopSearchSpinner"></i>
          <button type="button" class="shop-search-clear" id="shopSearchClearBtn" style="{{ !empty($search) ? 'display:block;' : 'display:none;' }}" onclick="clearSearch()" title="Clear Search">
            <i class="fas fa-times-circle"></i>
          </button>
        </div>
        <button type="submit" class="shop-search-submit"></button>
      </form>

      {{-- Actions: Sort & In-Stock --}}
      <div class="shop-toolbar-actions">

        {{-- Sort Dropdown --}}
        <div class="shop-sort-wrap">
          <span class="shop-sort-label"><i class="fas fa-arrow-down-wide-short"></i> Sort:</span>
          <select class="shop-sort-select" id="shopSortSelect" onchange="applySort(this.value)">
            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
            <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Popular</option>
            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low</option>
            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High</option>
            <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
            <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
          </select>
        </div>

        {{-- In Stock Only Toggle --}}
        <label class="shop-stock-toggle {{ $inStockOnly ? 'active' : '' }}" id="shopStockToggleLabel" title="Show only in-stock products">
          <input type="checkbox" id="shopStockToggle" {{ $inStockOnly ? 'checked' : '' }} onchange="toggleStockFilter(this.checked)">
          <span class="toggle-icon"><i class="fas fa-check"></i></span>
          <span>In Stock</span>
        </label>

      </div>

    </div>
  </div>

  {{-- Small Category Tabs / Pills --}}
  <div class="shop-cat-tabs-wrap">
    <div class="shop-cat-tabs" id="shopCatTabs">
      {{-- All Products Tab --}}
      <button type="button"
        class="shop-cat-tab {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'active' : '' }}"
        data-category="all"
        onclick="selectCategory('all', this, event)">
        <i class="fas fa-grip"></i>
        <span>All</span>
        <span class="cat-count">{{ $totalActiveProducts }}</span>
      </button>

      {{-- Category Tabs --}}
      @foreach($categories as $cat)
        @php
          $catActive = ($selectedCategory == $cat->id || $selectedCategory == $cat->name);
        @endphp
        <button type="button"
          class="shop-cat-tab {{ $catActive ? 'active' : '' }}"
          data-category="{{ $cat->id }}"
          data-category-name="{{ $cat->name }}"
          onclick="selectCategory('{{ $cat->id }}', this, event)">
          <span>{{ $cat->name }}</span>
          <span class="cat-count">{{ $cat->products_count }}</span>
        </button>
      @endforeach
    </div>
  </div>

  {{-- Active Filter Tags Strip --}}
  <div class="shop-active-filters-wrap" id="shopActiveFiltersWrap">
    @include('pages.shop.filters_partial', [
      'search' => $search,
      'currentCategory' => $currentCategory,
      'selectedCategory' => $selectedCategory,
      'inStockOnly' => $inStockOnly,
      'offersOnly' => $offersOnly,
      'sort' => $sort,
    ])
  </div>

  {{-- Products Grid Section --}}
  <div class="shop-products-section" id="shopProductsSection">
    @include('pages.shop.products_partial', [
      'products' => $products,
      'search' => $search,
      'currentCategory' => $currentCategory,
      'selectedCategory' => $selectedCategory,
      'inStockOnly' => $inStockOnly,
      'offersOnly' => $offersOnly,
      'sort' => $sort,
    ])
  </div>

</div>

<script>
/* ===================== SHOP LIVE STATE & AJAX ===================== */
const SHOP_CONFIG = {
  baseUrl: "{{ route('shop') }}",
  currentCategory: "{{ $selectedCategory ?? 'all' }}",
  currentSort: "{{ $sort ?? 'latest' }}",
  currentStock: {{ $inStockOnly ? 'true' : 'false' }},
  currentOffer: {{ !empty($offersOnly) ? 'true' : 'false' }},
  searchDebounceTimer: null,
  activeAbortController: null,
};

/* -------- Handle Search Input (Live as user types) -------- */
function handleSearchInput(val) {
  const clearBtn = document.getElementById('shopSearchClearBtn');
  if (clearBtn) {
    clearBtn.style.display = val.trim().length > 0 ? 'block' : 'none';
  }

  // Clear previous debounce timer
  clearTimeout(SHOP_CONFIG.searchDebounceTimer);

  // Debounce for 300ms before making AJAX live search call
  SHOP_CONFIG.searchDebounceTimer = setTimeout(() => {
    triggerLiveSearch();
  }, 300);
}

/* -------- Clear Search -------- */
function clearSearch() {
  const input = document.getElementById('shopSearchInput');
  if (input) {
    input.value = '';
    const clearBtn = document.getElementById('shopSearchClearBtn');
    if (clearBtn) clearBtn.style.display = 'none';
    triggerLiveSearch();
  }
}

/* -------- Category Tab Click -------- */
function selectCategory(catId, btnEl, event) {
  if (event) event.preventDefault();
  SHOP_CONFIG.currentCategory = catId;

  // Update category active tab classes immediately for snappy UI
  document.querySelectorAll('.shop-cat-tab').forEach(t => t.classList.remove('active'));
  if (btnEl) {
    btnEl.classList.add('active');
  } else {
    const target = document.querySelector(`.shop-cat-tab[data-category="${catId}"]`);
    if (target) target.classList.add('active');
  }

  triggerLiveSearch(1);
}

/* -------- Clear Category Filter -------- */
function clearCategoryFilter() {
  selectCategory('all', null, null);
}

/* -------- Sort Change -------- */
function applySort(val) {
  SHOP_CONFIG.currentSort = val || 'latest';
  const sortSelect = document.getElementById('shopSortSelect');
  if (sortSelect) sortSelect.value = SHOP_CONFIG.currentSort;
  triggerLiveSearch(1);
}

/* -------- Stock Filter Toggle -------- */
function toggleStockFilter(checked) {
  SHOP_CONFIG.currentStock = !!checked;
  const toggleInput = document.getElementById('shopStockToggle');
  const toggleLabel = document.getElementById('shopStockToggleLabel');
  if (toggleInput) toggleInput.checked = SHOP_CONFIG.currentStock;
  if (toggleLabel) {
    if (SHOP_CONFIG.currentStock) toggleLabel.classList.add('active');
    else toggleLabel.classList.remove('active');
  }
  triggerLiveSearch(1);
}

/* -------- Offer Filter Toggle -------- */
function toggleOfferFilter(checked) {
  SHOP_CONFIG.currentOffer = !!checked;
  triggerLiveSearch(1);
}

/* -------- Reset All Filters -------- */
function resetAllFilters() {
  const searchInput = document.getElementById('shopSearchInput');
  if (searchInput) searchInput.value = '';
  const clearBtn = document.getElementById('shopSearchClearBtn');
  if (clearBtn) clearBtn.style.display = 'none';

  SHOP_CONFIG.currentCategory = 'all';
  SHOP_CONFIG.currentSort = 'latest';
  SHOP_CONFIG.currentStock = false;
  SHOP_CONFIG.currentOffer = false;

  const sortSelect = document.getElementById('shopSortSelect');
  if (sortSelect) sortSelect.value = 'latest';

  const toggleInput = document.getElementById('shopStockToggle');
  const toggleLabel = document.getElementById('shopStockToggleLabel');
  if (toggleInput) toggleInput.checked = false;
  if (toggleLabel) toggleLabel.classList.remove('active');

  document.querySelectorAll('.shop-cat-tab').forEach(t => t.classList.remove('active'));
  const allTab = document.querySelector('.shop-cat-tab[data-category="all"]');
  if (allTab) allTab.classList.add('active');

  triggerLiveSearch(1);
}

/* -------- Core AJAX Live Fetch Function -------- */
function triggerLiveSearch(page = 1) {
  const searchInput = document.getElementById('shopSearchInput');
  const query = searchInput ? searchInput.value.trim() : '';

  const params = new URLSearchParams();
  if (SHOP_CONFIG.currentCategory && SHOP_CONFIG.currentCategory !== 'all') {
    params.set('category', SHOP_CONFIG.currentCategory);
  }
  if (query.length > 0) {
    params.set('search', query);
  }
  if (SHOP_CONFIG.currentSort && SHOP_CONFIG.currentSort !== 'latest') {
    params.set('sort', SHOP_CONFIG.currentSort);
  }
  if (SHOP_CONFIG.currentStock) {
    params.set('in_stock', '1');
  }
  if (SHOP_CONFIG.currentOffer) {
    params.set('offer', '1');
  }
  if (page > 1) {
    params.set('page', page);
  }

  const newUrl = `${SHOP_CONFIG.baseUrl}${params.toString() ? '?' + params.toString() : ''}`;

  // Update browser URL without reloading page
  window.history.replaceState(null, '', newUrl);

  // Show UI spinner & loading opacity
  const spinner = document.getElementById('shopSearchSpinner');
  const productsSection = document.getElementById('shopProductsSection');
  if (spinner) spinner.style.display = 'block';
  if (productsSection) productsSection.classList.add('loading');

  // Cancel any ongoing fetch
  if (SHOP_CONFIG.activeAbortController) {
    SHOP_CONFIG.activeAbortController.abort();
  }
  SHOP_CONFIG.activeAbortController = new AbortController();

  fetch(newUrl, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    },
    signal: SHOP_CONFIG.activeAbortController.signal
  })
  .then(res => res.json())
  .then(data => {
    if (spinner) spinner.style.display = 'none';
    if (productsSection) {
      productsSection.classList.remove('loading');
      if (data.grid_html !== undefined) {
        productsSection.innerHTML = data.grid_html;
      }
    }

    // Update active filter chips
    const filtersWrap = document.getElementById('shopActiveFiltersWrap');
    if (filtersWrap && data.filters_html !== undefined) {
      filtersWrap.innerHTML = data.filters_html;
    }

    // Update count badge
    const countText = document.getElementById('shopResultsCountText');
    if (countText && data.total_text !== undefined) {
      countText.textContent = data.total_text;
    }

    // Update title
    const titleEl = document.querySelector('#shopPageTitle span');
    if (titleEl && data.title !== undefined) {
      titleEl.textContent = data.title;
    }

    // Re-attach AJAX pagination click listeners
    attachPaginationListeners();
  })
  .catch(err => {
    if (err.name !== 'AbortError') {
      if (spinner) spinner.style.display = 'none';
      if (productsSection) productsSection.classList.remove('loading');
    }
  });
}

/* -------- Intercept pagination links for smooth AJAX pagination -------- */
function attachPaginationListeners() {
  const container = document.getElementById('shopProductsSection');
  if (!container) return;

  const links = container.querySelectorAll('.pagination a.page-link');
  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const href = link.getAttribute('href');
      if (href) {
        const url = new URL(href, window.location.origin);
        const page = url.searchParams.get('page') || 1;
        triggerLiveSearch(page);
        window.scrollTo({
          top: document.querySelector('.shop-toolbar').offsetTop - 80,
          behavior: 'smooth'
        });
      }
    });
  });
}

/* -------- Init on Page Load -------- */
document.addEventListener('DOMContentLoaded', () => {
  attachPaginationListeners();

  // Smooth scroll active category into view
  const activeTab = document.querySelector('.shop-cat-tab.active');
  if (activeTab && activeTab.parentElement) {
    const parent = activeTab.parentElement;
    const tabOffset = activeTab.offsetLeft - parent.offsetLeft;
    if (tabOffset > 100) {
      parent.scrollTo({
        left: tabOffset - 40,
        behavior: 'smooth'
      });
    }
  }
});
</script>
@endsection
