@forelse($products as $product)
<tr>
    <td scope="row">{{ $products->firstItem() ? $products->firstItem() + $loop->index : $loop->iteration }}</td>
    <td>
                        @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color, #cbd5e1);">
                        @else
                        <div style="width: 40px; height: 38px; border-radius: 6px; background: rgba(49, 49, 255, 0.06); border: 1px solid rgba(49, 49, 255, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        @endif
                    </td>
    <td class="name">{{ $product->name }}</td>
    <td>{{ $product->supplier->company_name ?? 'N/A' }}</td>
    <td>{{ $product->sku }}</td>
    <td>{{ number_format($product->price, 2) }} TK</td>
    <td>
        @if($product->status == 1)
        <span class="status-active-badge">● Active</span>
        @else
        <span class="status-inactive-badge">● Inactive</span>
        @endif
    </td>
    <td>{{ number_format($product->purchase_price, 2) ?? "N/A" }} TK</td>

    <td class="action-icons">
        <div class="d-flex align-items-center gap-1 justify-content-center">
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
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted">No records found.</td>
</tr>
@endforelse
