@if(!empty($search) || !empty($selectedCategory) || !empty($offersOnly) || $inStockOnly || $sort !== 'latest')
  <div class="shop-active-filters">
    <span class="shop-active-filters-title"><i class="fas fa-filter"></i> Filters:</span>

    @if(!empty($offersOnly))
      <span class="shop-filter-chip" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #ef4444;">
        <span><i class="fas fa-percent me-1"></i> <strong>Special Offers</strong></span>
        <a href="javascript:void(0)" onclick="toggleOfferFilter(false)" title="Remove offer filter"><i class="fas fa-times"></i></a>
      </span>
    @endif

    @if($currentCategory)
      <span class="shop-filter-chip">
        <span>Category: <strong>{{ $currentCategory->name }}</strong></span>
        <a href="javascript:void(0)" onclick="clearCategoryFilter()" title="Remove category filter"><i class="fas fa-times"></i></a>
      </span>
    @endif

    @if(!empty($search))
      <span class="shop-filter-chip">
        <span>Search: "<strong>{{ $search }}</strong>"</span>
        <a href="javascript:void(0)" onclick="clearSearch()" title="Remove search filter"><i class="fas fa-times"></i></a>
      </span>
    @endif

    @if($inStockOnly)
      <span class="shop-filter-chip">
        <span>In Stock Only</span>
        <a href="javascript:void(0)" onclick="toggleStockFilter(false)" title="Remove stock filter"><i class="fas fa-times"></i></a>
      </span>
    @endif

    @if($sort !== 'latest')
      <span class="shop-filter-chip">
        <span>Sort: <strong>{{ ucwords(str_replace('_', ' ', $sort)) }}</strong></span>
        <a href="javascript:void(0)" onclick="applySort('latest')" title="Reset sort"><i class="fas fa-times"></i></a>
      </span>
    @endif

    <a href="javascript:void(0)" onclick="resetAllFilters()" class="shop-clear-all-link">
      <i class="fas fa-rotate-left me-1"></i> Clear All
    </a>
  </div>
@endif
