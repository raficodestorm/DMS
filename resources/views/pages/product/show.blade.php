@extends('layouts.userlayout')

@section('title', $product->name . ' — R Electric')

@section('content')
<style>
/* ===================== PAGE LAYOUT ===================== */
.pd-page {
  padding: 24px 15px 60px;
}

/* ===================== BREADCRUMB ===================== */
.pd-breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--text-muted);
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.pd-breadcrumb a {
  color: var(--text-muted);
  text-decoration: none;
  transition: color .2s;
}
.pd-breadcrumb a:hover { color: var(--primary); }
.pd-breadcrumb .sep { opacity: .45; }
.pd-breadcrumb .current {
  color: var(--text-main);
  font-weight: 600;
  max-width: 240px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* ===================== MAIN GRID ===================== */
.pd-main-grid {
  display: grid;
  grid-template-columns: 5fr 7fr;
  gap: 28px;
  align-items: start;
}
@media (max-width: 900px) {
  .pd-main-grid { grid-template-columns: 1fr; gap: 20px; }
}

/* ===================== GALLERY ===================== */
.pd-gallery {
  position: sticky;
  top: 100px;
}
.pd-main-img-wrap {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 18px;
  overflow: hidden;
  aspect-ratio: 1;
  max-height: 380px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  cursor: zoom-in;
}
.pd-main-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform .4s cubic-bezier(.4,0,.2,1);
}
.pd-main-img-wrap:hover img { transform: scale(1.06); }
.pd-main-img-no-img {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 72px;
  color: var(--text-muted);
  opacity: .3;
  background: var(--primary-soft);
}

/* Offer ribbon on gallery */
.pd-gallery-ribbon {
  position: absolute;
  top: 16px;
  left: -6px;
  background: linear-gradient(135deg, #e11d48, #f8640e);
  color: #fff;
  font-size: 12px;
  font-weight: 800;
  padding: 5px 16px 5px 12px;
  border-radius: 0 6px 6px 0;
  display: flex;
  align-items: center;
  gap: 5px;
  box-shadow: 0 4px 12px rgba(225,29,72,.35);
  z-index: 2;
}
.pd-gallery-ribbon::before {
  content: '';
  position: absolute;
  left: 0;
  bottom: -5px;
  width: 6px;
  height: 5px;
  background: #9b1226;
  clip-path: polygon(0 0, 100% 0, 100% 100%);
}

/* Thumbnails */
.pd-thumbs {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  flex-wrap: wrap;
}
.pd-thumb {
  width: 64px;
  height: 64px;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid var(--border-color);
  cursor: pointer;
  transition: border-color .2s, transform .2s;
  background: var(--section-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.pd-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.pd-thumb:hover, .pd-thumb.active {
  border-color: var(--primary);
  transform: translateY(-2px);
}

/* ===================== PRODUCT INFO ===================== */
.pd-info { display: flex; flex-direction: column; gap: 18px; }

/* Category badge */
.pd-category-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--primary-soft);
  color: var(--primary);
  border: 1px solid rgba(2,2,226,.15);
  font-size: 11.5px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: .5px;
  width: fit-content;
}

/* Product name */
.pd-name {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-main);
  line-height: 1.3;
  margin: 0;
  font-family: "El Messiri", sans-serif;
}

/* SKU / Barcode */
.pd-meta-row {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  align-items: center;
}
.pd-meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
  color: var(--text-muted);
}
.pd-meta-item strong { color: var(--text-main); }

/* Stock pill */
.pd-stock-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
}
.pd-stock-pill.in-stock {
  background: rgba(5,150,105,.1);
  color: #059669;
  border: 1px solid rgba(5,150,105,.25);
}
.pd-stock-pill.out-stock {
  background: rgba(220,38,38,.08);
  color: #dc2626;
  border: 1px solid rgba(220,38,38,.2);
}

/* ===================== PRICING BOX ===================== */
.pd-price-box {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 20px 22px;
}
.pd-price-label {
  font-size: 11.5px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: .6px;
  margin-bottom: 6px;
}
.pd-price-main {
  font-size: 36px;
  font-weight: 800;
  color: var(--primary);
  line-height: 1.1;
  font-family: "Segoe UI", sans-serif;
}
.pd-price-original {
  font-size: 17px;
  color: var(--text-muted);
  text-decoration: line-through;
  margin-left: 8px;
  font-weight: 500;
}
.pd-price-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}
.pd-savings-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: linear-gradient(135deg, #e11d4815, #f8640e15);
  color: #e11d48;
  border: 1px solid rgba(225,29,72,.2);
  font-size: 12px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 20px;
}
.pd-offer-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #e11d48, #f8640e);
  color: #fff;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 20px;
  margin-top: 8px;
}
.pd-offer-ends {
  font-size: 11.5px;
  color: var(--text-muted);
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 5px;
}
.pd-coupon-box {
  margin-top: 10px;
  background: var(--primary-soft);
  border: 1px dashed rgba(2,2,226,.35);
  border-radius: 8px;
  padding: 8px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.pd-coupon-label {
  font-size: 11.5px;
  color: var(--text-muted);
}
.pd-coupon-code {
  font-size: 14px;
  font-weight: 800;
  color: var(--primary);
  letter-spacing: .5px;
}

/* ===================== ACTIONS ===================== */
.pd-actions {
  display: flex;
  gap: 10px;
  margin-top: 14px;
}
.pd-btn-cart {
  flex: 1;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 10px 24px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: opacity .2s, transform .2s;
  box-shadow: 0 6px 20px rgba(2,2,226,.22);
}
.pd-btn-cart:hover { opacity: .9; transform: translateY(-2px); }
.pd-btn-cart:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.pd-btn-wishlist {
  width: 52px;
  height: 48px;
  border-radius: 12px;
  border: 1px solid var(--border-color);
  background: var(--section-bg);
  color: var(--text-muted);
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .2s;
  flex-shrink: 0;
}
.pd-btn-wishlist:hover, .pd-btn-wishlist.wishlisted {
  color: #e11d48;
  border-color: #fca5a5;
  background: #fff0f3;
}
.pd-btn-wishlist.wishlisted i { animation: heartPop .3s ease; }

/* ===================== DESCRIPTION BOX ===================== */
.pd-desc-box {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 20px 22px;
}
.pd-desc-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pd-desc-title i { color: var(--primary); font-size: 14px; }
.pd-desc-text {
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.7;
  margin: 0;
  white-space: pre-wrap;
}

/* ===================== SPECS TABLE ===================== */
.pd-specs-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13.5px;
}
.pd-specs-table tr:not(:last-child) td {
  border-bottom: 1px solid var(--border-color);
}
.pd-specs-table td {
  padding: 10px 4px;
  vertical-align: top;
}
.pd-specs-table td:first-child {
  color: var(--text-muted);
  font-weight: 600;
  width: 38%;
  white-space: nowrap;
}
.pd-specs-table td:last-child {
  color: var(--text-main);
  font-weight: 500;
}

/* ===================== TRUST BADGES ===================== */
.pd-trust-row {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.pd-trust-item {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-muted);
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  padding: 12px 14px;
  border-radius: 10px;
  flex: 1;
  min-width: 130px;
}
.pd-trust-item i { color: var(--primary); font-size: 16px; }

/* ===================== RELATED SECTION ===================== */
.pd-related {
  margin-top: 64px;
}
.pd-related-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 24px;
  font-family: "El Messiri", sans-serif;
  display: flex;
  align-items: center;
  gap: 10px;
}
.pd-related-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border-color);
}

/* Image zoom lightbox */
.pd-lightbox {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.88);
  z-index: 99998;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  pointer-events: none;
  transition: opacity .25s;
}
.pd-lightbox.open {
  opacity: 1;
  pointer-events: all;
}
.pd-lightbox img {
  max-width: 90vw;
  max-height: 90vh;
  object-fit: contain;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,.5);
}
.pd-lightbox-close {
  position: absolute;
  top: 20px;
  right: 24px;
  background: none;
  border: none;
  color: #fff;
  font-size: 28px;
  cursor: pointer;
  opacity: .8;
  transition: opacity .2s;
}
.pd-lightbox-close:hover { opacity: 1; }

/* ===================== MOBILE RESPONSIVE ===================== */
@media (max-width: 768px) {

  /* Page */
  .pd-page { padding: 14px 16px 40px; }

  /* Breadcrumb */
  .pd-breadcrumb { font-size: 11.5px; margin-bottom: 14px; gap: 4px; }
  .pd-breadcrumb .current { max-width: 150px; font-size: 11.5px; }

  /* Gallery — no sticky on mobile, full width image */
  .pd-gallery { position: static; }
  .pd-main-img-wrap {
    aspect-ratio: 4 / 3;   /* wider rectangle fills full width */
    max-height: none;      /* remove height cap so it fills width */
    border-radius: 12px;
  }

  /* Thumbnails */
  .pd-thumb { width: 48px; height: 48px; border-radius: 7px; }
  .pd-thumbs { gap: 6px; margin-top: 8px; }

  /* Info section */
  .pd-info { gap: 12px; }

  /* Name */
  .pd-name { font-size: 18px; }

  /* Meta row — wrap each item cleanly */
  .pd-meta-row { gap: 6px 14px; }
  .pd-meta-item { font-size: 11.5px; }

  /* Price box */
  .pd-price-box { padding: 14px 16px; border-radius: 12px; }
  .pd-price-main { font-size: 26px; }
  .pd-price-original { font-size: 14px; }

  /* Actions — cart full width, heart fixed on right */
  .pd-actions { gap: 8px; flex-wrap: nowrap; }
  .pd-btn-cart {
    flex: 1;
    padding: 12px 16px;
    font-size: 14px;
    border-radius: 10px;
    min-width: 0;
  }
  .pd-btn-wishlist {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    font-size: 16px;
    flex-shrink: 0;
  }

  /* Desc & Spec boxes */
  .pd-desc-box { padding: 14px 16px; border-radius: 12px; }
  .pd-desc-title { font-size: 13.5px; margin-bottom: 8px; }
  .pd-desc-text { font-size: 13px; }
  .pd-specs-table { font-size: 12.5px; }
  .pd-specs-table td { padding: 8px 4px; }
  .pd-specs-table td:first-child { width: 42%; }

  /* Trust badges — 2×2 grid */
  .pd-trust-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }
  .pd-trust-item {
    min-width: 0;
    font-size: 11.5px;
    padding: 10px 10px;
    border-radius: 8px;
    gap: 5px;
    flex: unset;
  }
  .pd-trust-item i { font-size: 14px; }

  /* Offer ribbon */
  .pd-gallery-ribbon { font-size: 10.5px; padding: 4px 12px 4px 10px; }

  /* Related */
  .pd-related { margin-top: 36px; }
  .pd-related-title { font-size: 19px; margin-bottom: 16px; }

  /* Savings pill & offer badge wrapping */
  .pd-price-row { flex-wrap: wrap; }
}

@media (max-width: 380px) {
  .pd-name { font-size: 16px; }
  .pd-price-main { font-size: 22px; }
  .pd-meta-row { flex-direction: column; gap: 4px; }
}
</style>

{{-- Lightbox --}}
<div id="pdLightbox" class="pd-lightbox" onclick="closeLightbox(event)">
  <button class="pd-lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
  <img id="pdLightboxImg" src="" alt="">
</div>

<div class="container pd-page">

  {{-- Breadcrumb --}}
  <nav class="pd-breadcrumb">
    <a href="{{ route('home-page') }}"><i class="fas fa-house"></i> Home</a>
    <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
    @if($product->category)
      <a href="{{ route('home-page') }}?category={{ $product->category->id }}">{{ $product->category->name }}</a>
      <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
    @endif
    <span class="current">{{ $product->name }}</span>
  </nav>

  {{-- Main Grid --}}
  <div class="pd-main-grid">

    {{-- LEFT: Gallery --}}
    <div class="pd-gallery">
      <div class="pd-main-img-wrap" id="pdMainImgWrap" onclick="openLightbox()">
        @if($offer && $offerText)
          <div class="pd-gallery-ribbon"><i class="fas fa-bolt"></i> {{ $offerText }}</div>
        @endif
        @php
          $mainImage = $product->images->where('is_active', true)->first()?->image ?? $product->image;
        @endphp
        @if($mainImage)
          <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" id="pdMainImg">
        @else
          <div class="pd-main-img-no-img"><i class="fas fa-box-open"></i></div>
        @endif
      </div>

      {{-- Thumbnails --}}
      @if($product->images->count() > 0 || $product->image)
        <div class="pd-thumbs" id="pdThumbs">
          {{-- Primary image --}}
          @if($product->image)
            <div class="pd-thumb active" onclick="switchImage('{{ asset($product->image) }}', this)">
              <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
            </div>
          @endif
          {{-- Gallery images --}}
          @foreach($product->images->where('is_active', true) as $img)
            @if($img->image !== $product->image)
              <div class="pd-thumb" onclick="switchImage('{{ asset($img->image) }}', this)">
                <img src="{{ asset($img->image) }}" alt="{{ $product->name }}">
              </div>
            @endif
          @endforeach
        </div>
      @endif

      {{-- Actions (under Gallery) --}}
      <div class="pd-actions">
        @if($product->status == 1)
          <button type="button" class="pd-btn-cart"
            id="pdAddCartBtn"
            data-id="{{ $product->id }}"
            data-name="{{ $product->name }}"
            onclick="addToCart(this)">
            <i class="fas fa-shopping-bag"></i> Add to Cart
          </button>
        @else
          <button type="button" class="pd-btn-cart" disabled>
            <i class="fas fa-ban"></i> Out of Stock
          </button>
        @endif
        <button type="button"
          class="pd-btn-wishlist {{ $inWishlist ? 'wishlisted' : '' }}"
          id="pdWishlistBtn"
          data-product-id="{{ $product->id }}"
          title="{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
          onclick="toggleWishlist(this, event)">
          <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
        </button>
      </div>
    </div>

    {{-- RIGHT: Info --}}
    <div class="pd-info">

      {{-- Category Badge + Stock --}}
      <div class="d-flex align-items-center gap-2 flex-wrap">
        @if($product->category)
          <span class="pd-category-badge">
            <i class="fas fa-layer-group"></i> {{ $product->category->name }}
          </span>
        @endif
        @if($product->status == 1)
          <span class="pd-stock-pill in-stock"><i class="fas fa-check-circle"></i> In Stock</span>
        @else
          <span class="pd-stock-pill out-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
        @endif
      </div>

      {{-- Product Name --}}
      <h1 class="pd-name">{{ $product->name }}</h1>

      {{-- SKU / Barcode meta --}}
      <div class="pd-meta-row">
        
        @if($product->barcode)
          <span class="pd-meta-item"><i class="fas fa-barcode"></i> Barcode: <strong>{{ $product->barcode }}</strong></span>
        @endif
        @if($product->supplier)
          <span class="pd-meta-item"><i class="fas fa-building"></i> Brand: <strong>{{ $product->supplier->company_name }}</strong></span>
        @endif
      </div>

      {{-- Pricing Box --}}
      <div class="pd-price-box">
        <div class="pd-price-label">Retail Price</div>
        <div class="pd-price-row">
          <span class="pd-price-main">৳{{ number_format($sellingPrice) }}</span>
          @if($hasDiscount)
            <del class="pd-price-original">৳{{ number_format($originalPrice) }}</del>
          @endif
        </div>
        @if($hasDiscount)
          <span class="pd-savings-pill">
            <i class="fas fa-tag"></i> You save ৳{{ number_format($originalPrice - $sellingPrice) }}
          </span>
        @endif
        @if($offer && $offerText)
          <div class="mt-2">
            <span class="pd-offer-badge"><i class="fas fa-bolt"></i> {{ $offerText }}</span>
          </div>
          @if($offerEndDate)
            <div class="pd-offer-ends">
              <i class="fas fa-clock"></i> Offer ends: {{ \Carbon\Carbon::parse($offerEndDate)->format('d M Y') }}
            </div>
          @endif
        @endif
        @if($offer && $offer->coupon_code)
          <div class="pd-coupon-box">
            <div>
              <div class="pd-coupon-label">Use coupon at checkout</div>
              <div class="pd-coupon-code">{{ $offer->coupon_code }}</div>
            </div>
            <button type="button" onclick="copyCoupon('{{ $offer->coupon_code }}')"
              style="background:var(--primary);color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
              <i class="fas fa-copy"></i> Copy
            </button>
          </div>
        @endif
      </div>


      {{-- Short Description --}}
      @if($product->short_description)
        <div class="pd-desc-box">
          <div class="pd-desc-title"><i class="fas fa-align-left"></i> About this Product</div>
          <p class="pd-desc-text">{{ $product->short_description }}</p>
        </div>
      @endif

      {{-- Specifications --}}
      @php
        $specs = array_filter([
          'Category'    => $product->category?->name,
          'Brand'       => $product->supplier?->company_name,
          'Barcode'     => $product->barcode,
          'Weight'      => $product->weight ? $product->weight . ' kg' : null,
          'Dimensions'  => ($product->length && $product->width && $product->height)
                            ? "{$product->length} × {$product->width} × {$product->height} cm"
                            : null,
          'Availability' => $product->status == 1 ? 'In Stock' : 'Out of Stock',
        ]);
      @endphp
      @if(count($specs) > 0)
        <div class="pd-desc-box">
          <div class="pd-desc-title"><i class="fas fa-list-check"></i> Specifications</div>
          <table class="pd-specs-table">
            @foreach($specs as $label => $val)
              <tr>
                <td>{{ $label }}</td>
                <td>{{ $val }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      @endif

      {{-- Long Description --}}
      @if($product->long_description)
        <div class="pd-desc-box">
          <div class="pd-desc-title"><i class="fas fa-file-lines"></i> Product Details</div>
          <p class="pd-desc-text">{{ $product->long_description }}</p>
        </div>
      @endif

      {{-- Trust Badges --}}
      <div class="pd-trust-row">
        <div class="pd-trust-item"><i class="fas fa-shield-halved"></i> 100% Genuine</div>
        <div class="pd-trust-item"><i class="fas fa-shipping-fast"></i> Fast Delivery</div>
        <div class="pd-trust-item"><i class="fas fa-rotate-left"></i> Easy Returns</div>
        <div class="pd-trust-item"><i class="fas fa-headset"></i> 24/7 Support</div>
      </div>

    </div>
  </div>{{-- end main grid --}}

  {{-- Related Products --}}
  @if($relatedProducts->count() > 0)
    <section class="pd-related">
      <h3 class="pd-related-title">Related Products</h3>
      <div class="row g-3 g-md-4">
        @foreach($relatedProducts as $rp)
          <div class="col-lg-3 col-6 d-flex">
            @include('components.product-card', ['product' => $rp])
          </div>
        @endforeach
      </div>
    </section>
  @endif

</div>

<script>
/* -------- Gallery -------- */
function switchImage(src, thumbEl) {
  const img = document.getElementById('pdMainImg');
  if (img) img.src = src;
  document.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
  if (thumbEl) thumbEl.classList.add('active');
}

/* -------- Lightbox -------- */
function openLightbox() {
  const img = document.getElementById('pdMainImg');
  if (!img) return;
  document.getElementById('pdLightboxImg').src = img.src;
  document.getElementById('pdLightbox').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
  if (e && e.target !== document.getElementById('pdLightbox') && !e.target.closest('.pd-lightbox-close')) return;
  document.getElementById('pdLightbox').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeLightbox({ target: document.getElementById('pdLightbox') });
});

/* -------- Copy Coupon -------- */
function copyCoupon(code) {
  navigator.clipboard.writeText(code).then(() => {
    showWishlistToast('Coupon code copied: ' + code, true);
  }).catch(() => {});
}
</script>
@endsection
