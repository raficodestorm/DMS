@forelse($products as $product)
<div class="manage-card">
    <div class="card-body">
        <div><span>S.No</span>
            <p>{{ $products->firstItem() ? $products->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
                        @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color, #cbd5e1);">
                        @else
                        <div style="width: 40px; height: 38px; border-radius: 6px; background: rgba(49, 49, 255, 0.06); border: 1px solid rgba(49, 49, 255, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        @endif
        <div><span>Name</span>
            <p>{{ $product->name }}</p>
        </div>
        <div><span>Brand</span>
            <p>{{ $product->supplier->company_name ?? 'N/A' }}</p>
        </div>
        <div><span>SKU</span>
            <p>{{ $product->sku }}</p>
        </div>
        <div><span>Price</span>
            <p>{{ number_format($product->price, 2) }} TK</p>
        </div>
        <div><span>Status</span>
            <p>
                @if($product->status == 1)
                <span style="color:green;">● Active</span>
                @else
                <span style="color:red;">● Inactive</span>
                @endif
            </p>
        </div>
    </div>

    <div class="card-actions d-flex align-items-center gap-1">
        <button type="button" 
                class="icon-btn star-btn {{ $product->is_featured ? 'is-featured' : '' }}" 
                data-url="{{ route('admin.products.toggle-featured', $product) }}"
                onclick="toggleFeatured(this)"
                title="{{ $product->is_featured ? 'Featured (Click to unfeature)' : 'Mark as Featured' }}">
            <i class="{{ $product->is_featured ? 'fa-solid fa-star' : 'fa-regular fa-star' }}"></i>
        </button>
        <a href="{{ route('admin.products.show', $product) }}" class="icon-btn view-icon" title="View Product Details">
            <i class="fa-solid fa-eye"></i>
        </a>
    </div>
</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse
