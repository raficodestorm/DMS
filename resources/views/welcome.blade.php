@extends('layouts.userlayout')

@section('title','Home')

@section('content')


<style>
  /* ======================
GLOBAL
====================== */

  .section {
    padding: 40px 0;
  }

  .section-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 45px;
    text-align: center;
  }


  /* ======================
     HERO BANNER SLIDER
  ====================== */
  .main-slider {
    margin-top: 24px;
    position: relative;
  }

  .hero-slider {
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
    background: var(--section-bg);
  }

  .banner-slide-link {
    display: block;
    width: 100%;
    overflow: hidden;
    border-radius: 18px;
    position: relative;
    text-decoration: none;
    line-height: 0;
  }

  .banner-slide-img {
    width: 100%;
    height: auto;
    aspect-ratio: 16 / 5.5;
    object-fit: cover;
    display: block;
    border-radius: 18px;
    transition: transform 0.4s ease;
  }

  .banner-slide-link:hover .banner-slide-img {
    transform: scale(1.012);
  }

  /* Swiper Pagination */
  .hero-slider .swiper-pagination {
    bottom: 12px !important;
  }

  .hero-slider .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    border-radius: 10px;
    background: #ffffff;
    opacity: 0.6;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
  }

  .hero-slider .swiper-pagination-bullet-active {
    width: 24px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    opacity: 1;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
  }

  @media(max-width: 768px) {
    .main-slider {
      margin-top: 14px;
    }
    .hero-slider, .banner-slide-link, .banner-slide-img {
      border-radius: 14px;
    }
    .banner-slide-img {
      aspect-ratio: 16 / 6.5;
    }
    .hero-slider .swiper-pagination {
      bottom: 8px !important;
    }
    .hero-slider .swiper-pagination-bullet {
      width: 6px;
      height: 6px;
    }
    .hero-slider .swiper-pagination-bullet-active {
      width: 18px;
    }
  }

  @media(max-width: 480px) {
    .banner-slide-img {
      aspect-ratio: 16 / 7.5;
    }
  }


  /* ======================
     CATEGORY
  ====================== */

  .category-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px 16px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    position: relative;
  }

  .category-card:hover {
    transform: translateY(-6px);
    border-color: var(--primary);
    box-shadow: 0 12px 24px rgba(49, 49, 255, 0.08);
  }

  .category-img-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    background: var(--primary-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: transform 0.3s ease;
  }

  .category-card:hover .category-img-wrapper {
    transform: scale(1.08);
  }

  .category-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .category-img-wrapper i {
    font-size: 24px;
    color: var(--primary);
  }

  .category-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-main);
    margin: 0 0 4px 0;
    transition: color 0.2s ease;
  }

  .category-card:hover .category-title {
    color: var(--primary);
  }

  .category-count {
    font-size: 12px;
    font-weight: 500;
    color: var(--text-muted);
    margin: 0;
  }


  /* ======================
     WHOLESALE & RETAIL PROMO BANNER
  ====================== */

  .promo-banner-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 34px 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  }

  .promo-banner-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.06);
    border-color: var(--primary);
  }

  .promo-banner-card::before {
    content: "";
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    top: -80px;
    right: -80px;
    filter: blur(60px);
    opacity: 0.28;
    pointer-events: none;
  }

  .promo-banner-card.b2b::before {
    background: var(--primary);
  }

  .promo-banner-card.b2c::before {
    background: var(--accent);
  }

  .promo-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 5px 12px;
    border-radius: 20px;
    width: fit-content;
    margin-bottom: 18px;
  }

  .promo-badge.b2b {
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid rgba(29, 29, 255, 0.2);
  }

  .promo-badge.b2c {
    background: rgba(158, 0, 220, 0.1);
    color: var(--accent);
    border: 1px solid rgba(158, 0, 220, 0.2);
  }

  .promo-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 10px;
    line-height: 1.35;
  }

  .promo-desc {
    color: var(--text-muted);
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
  }

  .promo-features {
    list-style: none;
    padding: 0;
    margin: 0 0 26px 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .promo-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text-main);
  }

  .promo-feature-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
  }

  .promo-banner-card.b2b .promo-feature-icon {
    background: var(--primary-soft);
    color: var(--primary);
  }

  .promo-banner-card.b2c .promo-feature-icon {
    background: rgba(158, 0, 220, 0.1);
    color: var(--accent);
  }

  .promo-actions {
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .btn-promo-primary {
    background: linear-gradient(90deg, var(--primary), var(--accent));
    color: #ffffff;
    font-weight: 600;
    font-size: 13.5px;
    padding: 9px 18px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: opacity 0.2s, transform 0.15s;
  }

  .btn-promo-primary:hover {
    opacity: 0.92;
    transform: translateY(-2px);
    color: #ffffff;
  }

  .btn-promo-outline {
    background: transparent;
    color: var(--text-main);
    border: 1px solid var(--border-color);
    font-weight: 600;
    font-size: 13.5px;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }

  .btn-promo-outline:hover {
    background: var(--primary-soft);
    color: var(--primary);
    border-color: var(--primary);
  }

  @media (max-width: 768px) {
    .promo-banner-card {
      padding: 24px 18px;
    }
    .promo-title {
      font-size: 18px;
    }
    .promo-actions {
      flex-direction: column;
      width: 100%;
    }
    .btn-promo-primary,
    .btn-promo-outline {
      width: 100%;
      justify-content: center;
    }
  }

  /* ======================
TRUST
====================== */

  .trust-card {
    background: var(--section-bg);
    border-radius: 14px;
    border: 1px solid var(--border-color);
    padding: 30px;
    text-align: center;
    transition: .3s;
  }

  .trust-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
  }

  .trust-icon {
    font-size: 38px;
    color: var(--primary);
    margin-bottom: 10px;
  }

  .trust-card h5 {
    color: var(--text-main);
  }

  .trust-card p {
    color: var(--text-muted);
    font-size: 14px;
  }
</style>


<!-- HERO BANNER SLIDER -->

<section class="container main-slider">

  <div class="swiper hero-slider">

    <div class="swiper-wrapper">

      @if(isset($slides) && $slides->isNotEmpty())
        @foreach($slides as $slide)
          <div class="swiper-slide">
            <a href="{{ $slide->target_url }}" class="banner-slide-link" title="{{ $slide->name }}">
              <img src="{{ asset($slide->image) }}" class="banner-slide-img" alt="{{ $slide->name }}">
            </a>
          </div>
        @endforeach
      @else
        <!-- Fallback Default Banners -->
        <div class="swiper-slide">
          <a href="{{ route('shop') }}" class="banner-slide-link" title="Explore Products">
            <img src="{{ asset('image/banner1.png') }}" class="banner-slide-img" alt="Special Offer Banner 1">
          </a>
        </div>
        <div class="swiper-slide">
          <a href="{{ route('shop') }}" class="banner-slide-link" title="Explore Products">
            <img src="{{ asset('image/banner1.png') }}" class="banner-slide-img" alt="Special Offer Banner 2">
          </a>
        </div>
      @endif

    </div>

    <!-- Swiper Pagination -->
    <div class="swiper-pagination"></div>

  </div>

</section>


<!-- CATEGORY -->

<section class="section">

  <div class="container">

    <h3 class="section-title">Shop By Category</h3>

    <div class="row g-3 g-md-4 justify-content-center">

      @forelse($featuredCategories ?? [] as $cat)
      <div class="col-xl-3 col-lg-4 col-md-4 col-6">
        <a href="{{ route('shop') }}?category={{ $cat->id }}" class="category-card" style="text-decoration: none; color: inherit; display: flex;">
          <div class="category-img-wrapper">
            @if(!empty($cat->image))
              <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}">
            @else
              <i class="fas fa-layer-group"></i>
            @endif
          </div>
          <h6 class="category-title">{{ $cat->name }}</h6>
          <p class="category-count">{{ $cat->products_count ?? 0 }} {{ Str::plural('Product', $cat->products_count ?? 0) }}</p>
        </a>
      </div>
      @empty
      <div class="col-12 text-center py-4">
        <p class="text-muted mb-0">No featured categories found.</p>
      </div>
      @endforelse

    </div>

  </div>

</section>


<!-- FEATURED PRODUCTS -->

<section class="section pt-0">

  <div class="container">

    <h3 class="section-title">Featured Products</h3>

    <div class="row g-3 g-md-4">

      @forelse($featuredProducts ?? [] as $product)
      <div class="col-lg-3 col-6 d-flex">
        @include('components.product-card', ['product' => $product])
      </div>
      @empty
      <div class="col-12 text-center py-4">
        <p class="text-muted mb-0">No featured products available.</p>
      </div>
      @endforelse

    </div>

  </div>

</section>


<!-- BEST SELLING PRODUCTS -->

<section class="section pt-0">

  <div class="container">

    <h3 class="section-title">Best Selling Products</h3>

    <div class="row g-3 g-md-4">

      @forelse($bestSellingProducts ?? [] as $product)
      <div class="col-lg-3 col-6 d-flex">
        @include('components.product-card', ['product' => $product])
      </div>
      @empty
      <div class="col-12 text-center py-4">
        <p class="text-muted mb-0">No best selling products available.</p>
      </div>
      @endforelse

    </div>

  </div>

</section>


<!-- WHOLESALE & RETAIL ADVANTAGE SECTION -->

<section class="section pt-0">

  <div class="container">

    <div class="row g-4">

      <!-- B2B & Wholesale Supply Hub -->
      <div class="col-lg-6 col-12 d-flex">
        <div class="promo-banner-card b2b">
          <span class="promo-badge b2b">
            <i class="fas fa-boxes-stacked"></i> B2B & Bulk Orders
          </span>
          <h4 class="promo-title">Wholesale & Contractor Supply</h4>
          <p class="promo-desc">
            Direct dealership supply with tier-based wholesale pricing for retailers, electrical contractors, and corporate projects.
          </p>
          <ul class="promo-features">
            <li>
              <span class="promo-feature-icon"><i class="fas fa-check"></i></span>
              <span>Special Bulk Tier Discounts on 100+ items</span>
            </li>
            <li>
              <span class="promo-feature-icon"><i class="fas fa-check"></i></span>
              <span>Priority Warehouse Dispatch & Delivery</span>
            </li>
            <li>
              <span class="promo-feature-icon"><i class="fas fa-check"></i></span>
              <span>Dedicated Credit & Order Management</span>
            </li>
          </ul>
          <div class="promo-actions">
            <a href="{{ route('contact') }}" class="btn-promo-primary">
              <i class="fas fa-file-invoice-dollar"></i> Request Bulk Quote
            </a>
            <a href="tel:+8801828333233" class="btn-promo-outline">
              <i class="fas fa-phone-alt"></i> Call Wholesale Desk
            </a>
          </div>
        </div>
      </div>

      <!-- B2C & Retail Excellence -->
      <div class="col-lg-6 col-12 d-flex">
        <div class="promo-banner-card b2c">
          <span class="promo-badge b2c">
            <i class="fas fa-shield-halved"></i> 100% Genuine Guaranteed
          </span>
          <h4 class="promo-title">Direct Retail & Smart Home Delivery</h4>
          <p class="promo-desc">
            Premium authentic switches, smart lighting, and wiring solutions delivered safely to your home or workspace with official warranty.
          </p>
          <ul class="promo-features">
            <li>
              <span class="promo-feature-icon"><i class="fas fa-check"></i></span>
              <span>100% Original Walton & Certified Brands</span>
            </li>
            <li>
              <span class="promo-feature-icon"><i class="fas fa-check"></i></span>
              <span>Official Manufacturer Replacement Warranty</span>
            </li>
            <li>
              <span class="promo-feature-icon"><i class="fas fa-check"></i></span>
              <span>Fast Nationwide Cash on Delivery (COD)</span>
            </li>
          </ul>
          <div class="promo-actions">
            <a href="{{ route('contact') }}" class="btn-promo-primary">
              <i class="fas fa-headset"></i> Get Expert Support
            </a>
            <a href="{{ route('contact') }}" class="btn-promo-outline">
              <i class="fas fa-location-dot"></i> Visit Outlet
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>

</section>





<!-- TRUST -->

<section class="section">

  <div class="container">

    <div class="row g-4">

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-shipping-fast"></i></div>
          <h5>Fast Delivery</h5>
          <p>Across Bangladesh.</p>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-shield-alt"></i></div>
          <h5>Secure Payment</h5>
          <p>Safe payment system.</p>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-star"></i></div>
          <h5>Top Quality</h5>
          <p>Best electrical brands.</p>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-headset"></i></div>
          <h5>24/7 Support</h5>
          <p>Always ready to help.</p>
        </div>
      </div>

    </div>

  </div>

</section>


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
  new Swiper(".hero-slider", {
    loop: true,
    speed: 800,
    autoplay: {
      delay: 3500,
      disableOnInteraction: false
    },
    effect: "slide",
    spaceBetween: 14,
    pagination: {
      el: ".swiper-pagination",
      clickable: true
    }
  });
</script>

@endsection