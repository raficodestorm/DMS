@forelse($categories as $category)
<div class="manage-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2 pb-2" style="border-bottom: 1px solid var(--border-color, #e2e8f0);">
            <div class="d-flex align-items-center gap-2">
                @if($category->image)
                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" style="width: 44px; height: 42px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color, #cbd5e1);">
                @else
                    <div style="width: 44px; height: 42px; border-radius: 8px; background: rgba(49, 49, 255, 0.06); border: 1px solid rgba(49, 49, 255, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                @endif
                <div>
                    <h4 class="mb-0" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">{{ $category->name }}</h4>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">#{{ $categories->firstItem() ? $categories->firstItem() + $loop->index : $loop->iteration }}</span>
                </div>
            </div>
            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; background: rgba(49, 49, 255, 0.07); color: var(--primary); border: 1px solid rgba(49, 49, 255, 0.15);">
                <i class="fas fa-boxes-stacked"></i> {{ $category->products_count ?? $category->products()->count() }}
            </span>
        </div>

        @if($category->description)
        <div class="mb-1">
            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Description</span>
            <p style="font-size: 0.84rem; color: var(--text-main); margin-bottom: 0;">{{ Str::limit($category->description, 100) }}</p>
        </div>
        @endif
    </div>

    <div class="card-actions d-flex align-items-center justify-content-end gap-2 pt-2" style="border-top: 1px solid var(--border-color, #e2e8f0);">
        <button type="button" 
                class="icon-btn star-btn {{ $category->is_featured ? 'is-featured' : '' }}" 
                data-url="{{ route('admin.categories.toggle-featured', $category) }}"
                onclick="toggleCategoryFeatured(this)"
                title="{{ $category->is_featured ? 'Featured (Click to unfeature)' : 'Mark as Featured' }}">
            <i class="{{ $category->is_featured ? 'fa-solid fa-star' : 'fa-regular fa-star' }}"></i>
        </button>

        <a href="{{ route('admin.categories.show', $category) }}" class="icon-btn view-icon" title="View Details">
            <i class="fa-solid fa-eye"></i>
        </a>

        <a href="{{ route('admin.categories.edit', $category) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this category?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;" title="Delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</div>
@empty
<p class="text-center text-muted py-4">No categories found.</p>
@endforelse
