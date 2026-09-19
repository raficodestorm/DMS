@if($products->count() > 0)
  <div class="row g-3 g-md-4">
    @foreach($products as $product)
      <div class="col-xl-3 col-lg-3 col-md-4 col-6 d-flex">
        @include('components.product-card', ['product' => $product])
      </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  @if($products->hasPages())
    <div class="shop-pagination-wrap">
      {{ $products->links('pagination::bootstrap-5') }}
    </div>
  @endif
@else
  {{-- Empty State --}}
  <div class="shop-empty-state">
    <div class="shop-empty-icon">
      <i class="fas fa-box-open"></i>
    </div>
    <h3 class="shop-empty-title">No Products Found</h3>
    <p class="shop-empty-desc">
      @if(!empty($offersOnly))
        There are currently no active special offers available at this moment. Check back soon!
      @elseif(!empty($search))
        We couldn't find any products matching "<strong>{{ $search }}</strong>". Try checking for spelling errors or using more general terms.
      @elseif($currentCategory)
        There are currently no active products in <strong>{{ $currentCategory->name }}</strong>.
      @else
        No products match your currently selected filters.
      @endif
    </p>
    <button type="button" class="shop-btn-reset" onclick="resetAllFilters()">
      <i class="fas fa-arrow-rotate-left"></i> View All Products
    </button>
  </div>
@endif
