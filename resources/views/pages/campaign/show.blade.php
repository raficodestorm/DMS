@extends('layouts.userlayout')

@section('title', $slide->name . ' — Special Campaign Deals — ' . config('app.name'))

@section('content')
<style>
/* ===================== SMART CAMPAIGN DEALS PAGE ===================== */
.campaign-page {
  padding: 20px 15px 60px;
  position: relative;
}

/* Smart Hero Header */
.campaign-hero {
  position: relative;
  z-index: 1;
  background: radial-gradient(circle at 50% 0%, rgba(158, 0, 220, 0.16) 0%, rgba(2, 2, 226, 0.1) 45%, transparent 70%), var(--section-bg);
  border: 1.5px solid rgba(158, 0, 220, 0.25);
  border-radius: 20px;
  padding: 32px 20px;
  text-align: center;
  margin-bottom: 28px;
  box-shadow: 0 10px 30px -10px rgba(158, 0, 220, 0.15);
}

.campaign-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 14px;
  border-radius: 20px;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 8px;
  box-shadow: 0 4px 12px rgba(158, 0, 220, 0.25);
}

.campaign-title {
  font-size: clamp(24px, 4.5vw, 38px);
  font-weight: 900;
  font-family: "El Messiri", sans-serif;
  background: linear-gradient(135deg, var(--primary), var(--accent), #e11d48);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin: 8px 0 8px;
  line-height: 1.2;
}

.campaign-sub {
  font-size: 14px;
  color: var(--text-muted);
  max-width: 620px;
  margin: 0 auto;
  line-height: 1.6;
}

.campaign-count-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--primary-soft);
  color: var(--primary);
  border: 1px solid rgba(2, 2, 226, 0.18);
  font-size: 12px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  margin-top: 10px;
}

/* Products Grid */
.campaign-products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

/* Empty State */
.campaign-empty-state {
  text-align: center;
  padding: 50px 20px;
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  margin: 20px 0;
}

.campaign-empty-icon {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: var(--primary-soft);
  color: var(--primary);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin-bottom: 14px;
}

@media (max-width: 991px) {
  .campaign-products-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
  }
}

@media (max-width: 768px) {
  .campaign-page {
    padding: 12px 10px 40px;
  }
  .campaign-hero {
    padding: 22px 14px;
    border-radius: 16px;
    margin-bottom: 18px;
  }
  .campaign-title {
    font-size: 22px;
  }
  .campaign-sub {
    font-size: 12.5px;
  }
  .campaign-products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
}
</style>

<div class="container campaign-page">

  {{-- Smart Header Section --}}
  <div class="campaign-hero">
    <span class="campaign-badge">
      <i class="fas fa-bolt"></i> Special Campaign Deals
    </span>

    <h1 class="campaign-title">{{ $slide->name }}</h1>

    <p class="campaign-sub">
      Discover exclusive discounts and special promotional pricing curated for this campaign.
    </p>

    <div class="campaign-count-pill">
      <i class="fas fa-boxes-stacked"></i>
      <span>{{ $products->count() }} {{ Str::plural('Product', $products->count()) }} on Offer</span>
    </div>
  </div>

  {{-- Products Grid --}}
  @if($products->count() > 0)
    <div class="campaign-products-grid">
      @foreach($products as $product)
        <div>
          @include('components.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>
  @else
    <div class="campaign-empty-state">
      <div class="campaign-empty-icon">
        <i class="fas fa-box-open"></i>
      </div>
      <h4 class="fw-bold" style="color: var(--text-main);">No Products Available</h4>
      <p class="text-muted mb-3" style="max-width: 420px; margin: 0 auto; font-size: 13.5px;">
        There are currently no active products under the offers attached to this campaign.
      </p>
      <a href="{{ route('shop') }}" class="btn btn-sm btn-primary" style="border-radius: 8px; padding: 7px 18px; font-weight: 700;">
        <i class="fas fa-store me-1"></i> Explore Shop Catalog
      </a>
    </div>
  @endif

</div>
@endsection
