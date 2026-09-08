@extends('layouts.adminlayout')

@section('content')
<style>
  .pshow-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 10px 0 40px;
  }

  /* ── Header ── */
  .pshow-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
  }

  .pshow-header h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text-main);
  }

  .pshow-cat {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 3px;
  }

  .pshow-actions {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* ── Main Layout ── */
  .pshow-box {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 28px;
    box-sizing: border-box;
  }

  /* ── Image Gallery (Left) ── */
  .pshow-img-col {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .pshow-main-img {
    width: 100%;
    height: 300px;
    border-radius: 12px;
    border: 1.5px solid var(--border-color);
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .pshow-main-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .pshow-thumbs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .pshow-thumb {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    border: 1.5px solid var(--border-color);
    object-fit: cover;
    cursor: pointer;
    background: var(--background);
    transition: border-color .15s;
  }

  .pshow-thumb:hover,
  .pshow-thumb.active {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  }

  /* ── Info Content (Right) ── */
  .pshow-info-col {
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-width: 0; /* Prevents overflow issues */
  }

  .pshow-price-card {
    background: var(--primary-soft);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 12px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .pshow-price-val {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--text-main);
  }

  .pshow-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }

  .pshow-item {
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 9px;
    padding: 9px 12px;
  }

  .pshow-item span {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 2px;
  }

  .pshow-item strong {
    font-size: 0.88rem;
    color: var(--text-main);
    word-break: break-word;
  }

  .pshow-desc-card {
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 9px;
    padding: 12px 14px;
  }

  .pshow-desc-card span {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .pshow-desc-card p {
    margin: 0;
    font-size: 0.86rem;
    color: var(--text-main);
    line-height: 1.55;
    white-space: pre-line;
    word-break: break-word;
  }

  @media (max-width: 760px) {
    .pshow-box {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="pshow-page">

  {{-- Header --}}
  <div class="pshow-header">
    <div class="pshow-actions">
      <a href="{{ route('admin.products.index') }}" class="btn-smart btn-gray">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <a href="{{ route('admin.products.edit', $product) }}" class="btn-smart btn-blue">
        <i class="fas fa-edit"></i> Edit
      </a>
      <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure you want to delete this product?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-smart btn-red">
          <i class="fas fa-trash"></i> Delete
        </button>
      </form>
    </div>
  </div>

  @include('components.alert')

  {{-- Details Box --}}
  <div class="pshow-box">

    {{-- Left: Image & Thumbnails --}}
    <div class="pshow-img-col">
      @php
        $mainImg = $product->image
          ? (str_starts_with($product->image, 'uploads/') ? asset($product->image) : asset('uploads/' . $product->image))
          : 'https://ui-avatars.com/api/?name='.urlencode($product->name).'&background=6366f1&color=fff';
      @endphp

      <div class="pshow-main-img">
        <img id="pshowMainImg" src="{{ $mainImg }}" alt="{{ $product->name }}">
      </div>

      @if($product->image || $product->images->count() > 0)
      <div class="pshow-thumbs">
        @if($product->image)
          <img src="{{ $mainImg }}" class="pshow-thumb active" onclick="switchMainImg(this.src, this)" alt="Thumbnail">
        @endif
        @foreach($product->images as $img)
          @php
            $thumbUrl = str_starts_with($img->image, 'uploads/') ? asset($img->image) : asset('uploads/' . $img->image);
          @endphp
          <img src="{{ $thumbUrl }}" class="pshow-thumb" onclick="switchMainImg(this.src, this)" alt="Gallery Thumbnail">
        @endforeach
      </div>
      @endif
    </div>

    {{-- Right: Info & Pricing --}}
    <div class="pshow-info-col">
      <div>
      <h2>{{ $product->name }}</h2>
      <div class="pshow-cat">
        {{-- Short Description --}}
      @if($product->short_description)
      
        <p>{{ $product->short_description }}</p>
      
      @endif
        <i class="fas fa-tag"></i> {{ $product->category->name ?? 'Uncategorized' }}
      </div>
    </div>
      
      {{-- Price & Status Banner --}}
      <div class="pshow-price-card">
        <div>
          <span style="font-size:0.7rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; display:block;">Selling Price</span>
          <div class="pshow-price-val">৳ {{ number_format($product->price, 2) }}</div>
        </div>
        <div style="text-align:right;">
          <span style="font-size:0.7rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; display:block;">Status</span>
          <span style="font-size:0.85rem; font-weight:700; color:{{ $product->status == 1 ? '#10b981' : '#ef4444' }};">
            {{ $product->status == 1 ? '● Active' : '○ Inactive' }}
          </span>
        </div>
      </div>

      {{-- Specs Grid --}}
      <div class="pshow-grid">
        <div class="pshow-item">
          <span>SKU</span>
          <strong>{{ $product->sku }}</strong>
        </div>
        <div class="pshow-item">
          <span>Supplier</span>
          <strong>{{ $product->supplier->company_name ?? '-' }}</strong>
        </div>
        <div class="pshow-item">
          <span>Stock Alert</span>
          <strong>{{ $product->stock_alert }} units</strong>
        </div>
        <div class="pshow-item">
          <span>Featured</span>
          <strong>{{ $product->is_featured ? 'Yes' : 'No' }}</strong>
        </div>
      </div>

      

      {{-- Long Description --}}
      @if($product->long_description)
      <div class="pshow-desc-card">
        <span>Long Description</span>
        <p>{!! nl2br(e($product->long_description)) !!}</p>
      </div>
      @endif

    </div>

  </div>

</div>

<script>
  function switchMainImg(src, el) {
    document.getElementById('pshowMainImg').src = src;
    document.querySelectorAll('.pshow-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
  }
</script>
@endsection