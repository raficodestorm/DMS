@forelse($categories as $category)
<tr>
    <td scope="row">{{ $categories->firstItem() ? $categories->firstItem() + $loop->index : $loop->iteration }}</td>
    
    <td>
        @if($category->image)
            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" style="width: 42px; height: 40px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color, #cbd5e1);">
        @else
            <div style="width: 42px; height: 40px; border-radius: 8px; background: rgba(49, 49, 255, 0.06); border: 1px solid rgba(49, 49, 255, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                <i class="fa-solid fa-tags"></i>
            </div>
        @endif
    </td>

    <td class="name">
        <a href="{{ route('admin.categories.show', $category) }}" style="font-weight: 600; color: var(--text-main); text-decoration: none;">
            {{ $category->name }}
        </a>
    </td>

    <td style="max-width: 320px; color: var(--text-muted); font-size: 0.85rem;">
        {{ Str::limit($category->description ?: '—', 85) }}
    </td>

    <td>
        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 12px; font-size: 0.78rem; font-weight: 700; background: rgba(49, 49, 255, 0.07); color: var(--primary); border: 1px solid rgba(49, 49, 255, 0.15);">
            <i class="fas fa-boxes-stacked" style="font-size: 0.75rem;"></i> {{ $category->products_count ?? $category->products()->count() }}
        </span>
    </td>

    <td class="action-icons">
        <div class="d-flex align-items-center gap-1 justify-content-center">
            <button type="button" 
                    class="icon-btn star-btn {{ $category->is_featured ? 'is-featured' : '' }}" 
                    data-url="{{ route('admin.categories.toggle-featured', $category) }}"
                    onclick="toggleCategoryFeatured(this)"
                    title="{{ $category->is_featured ? 'Featured (Click to unfeature)' : 'Mark as Featured' }}">
                <i class="{{ $category->is_featured ? 'fa-solid fa-star' : 'fa-regular fa-star' }}"></i>
            </button>

            <a href="{{ route('admin.categories.show', $category) }}" class="icon-btn view-icon" title="View Category">
                <i class="fa-solid fa-eye"></i>
            </a>

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
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted py-4">No categories found.</td>
</tr>
@endforelse
