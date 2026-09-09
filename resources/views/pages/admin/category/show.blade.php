@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Category details</p>
<div class="show-card">
  <div class="header-accent">
    <div class="photo-container">
      @php
        $catImg = $category->image 
          ? (str_starts_with($category->image, 'uploads/') ? asset($category->image) : asset('uploads/' . $category->image))
          : 'https://ui-avatars.com/api/?name='.urlencode($category->name).'&background=3131ff&color=fff&size=200';
      @endphp
      <img class="img-fluid" src="{{ $catImg }}" alt="{{ $category->name }}">
    </div>
  </div>

  <div class="content-area">
    <h1 class="show-name">{{ $category->name }}</h1>
    <div class="rank-pill">{{ $category->is_featured ? 'Featured Category' : 'Product Category' }}</div>

    <div class="info-list">
      <div class="info-group">
        <span class="i-label">Category Name</span>
        <span class="i-value">{{ $category->name }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Total Products</span>
        <span class="i-value font-weight-bold" style="color: var(--primary);">
          {{ $category->products_count ?? $category->products()->count() }} Products
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Featured Status</span>
        <span class="i-value">
          @if($category->is_featured)
            <span style="color: #d97706; font-weight: 700;"><i class="fas fa-star text-warning me-1"></i> Featured</span>
          @else
            <span style="color: var(--text-muted);">Standard</span>
          @endif
        </span>
      </div>

      <div class="info-group">
        <span class="i-label">Created at</span>
        <span class="i-value">
          {{ $category->created_at ? $category->created_at->timezone(auth()->user()->timezone ?? 'Asia/Dhaka')->format('d M Y, h:i A') : 'N/A' }}
        </span>
      </div>
    </div>

    <div class="statement">
      <p class="statement-text">
        <span style="font-weight: bold; color: var(--primary);">Description : </span></br>
        {{ $category->description ? $category->description : 'Dedicated to high-performance electrical products, switches, and components.' }}"
      </p>
    </div>
  </div>

  <div class="card-footer-actions">
    <a href="{{ route('admin.categories.edit', $category) }}" class="icon-btn edit-icon" title="Edit Category">
      <i class="fa-solid fa-pen"></i>
    </a>

    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this category?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="icon-btn delete-icon" style="border: none;" title="Delete Category">
        <i class="fa-solid fa-trash"></i>
      </button>
    </form>
  </div>
</div>

<a href="{{ route('admin.categories.index') }}" class="back-btn">
  ← Back
</a>
@endsection