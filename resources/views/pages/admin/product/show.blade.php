@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Product details</p>
<div class="show-card">
    <div class="header-accent">
        <div class="square-photo-container">
            <img class="img-fluid"
                src="{{ $product->image ? asset('storage/' . $product->image) : 'https://ui-avatars.com/api/?name='.urlencode($product->name).'&background=3131ff&color=fff' }}">
        </div>
    </div>

    <div class="content-area">
        <div class="rank-pill">Details of {{ $product->name }}</div>
        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Name</span>
                <span class="i-value">{{ $product->name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">SKU</span>
                <span class="i-value">{{ $product->sku }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Category</span>
                <span class="i-value">{{ $product->category->name ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Supplier</span>
                <span class="i-value">{{ $product->supplier->company_name ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Price</span>
                <span class="i-value">{{ $product->price }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Stock Alert Q.</span>
                <span class="i-value">{{ $product->stock_alert }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Status</span>
                <span class="i-value">{{ $product->status == 1 ? 'Active' : 'Inactive' }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $product->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i
                    A') }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                <strong>Description</strong>
                {{ $product->description }}
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <a href="{{ route('admin.products.edit', $product) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('admin.products.index') }}" class="back-btn">
    ← Back
</a>

@endsection