@forelse($slides as $index => $slide)
<tr id="slide-row-{{ $slide->id }}">
    <td class="fw-bold">{{ $slides->firstItem() + $index }}</td>
    
    {{-- Banner Thumbnail --}}
    <td>
        <div style="width: 130px; height: 50px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); background: var(--section-bg); display: flex; align-items: center; justify-content: center;">
            <img src="{{ asset($slide->image) }}" alt="{{ $slide->name }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </td>

    {{-- Slide Name --}}
    <td>
        <span class="fw-bold text-dark d-block" style="color: var(--text-main) !important;">{{ $slide->name }}</span>
        <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>{{ $slide->created_at->format('d M, Y') }}</small>
    </td>

    {{-- Position --}}
    <td>
        <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(49, 49, 255, 0.08); color: var(--primary); font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem; border: 1px solid rgba(49, 49, 255, 0.2);">
            <i class="fas fa-arrow-down-1-9"></i> #{{ $slide->position }}
        </span>
    </td>

    {{-- Connected Offers / Link --}}
    <td>
        @if($slide->offers && $slide->offers->count() > 0)
            <div class="d-flex flex-column gap-1">
                <span style="background: rgba(158, 0, 220, 0.1); color: var(--accent); font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 0.8rem; width: fit-content;">
                    <i class="fas fa-tags me-1"></i> {{ $slide->offers->count() }} {{ Str::plural('Offer', $slide->offers->count()) }} Connected
                </span>
                <div class="text-muted" style="font-size: 0.76rem; max-width: 220px;">
                    @foreach($slide->offers->take(2) as $off)
                        <span class="d-inline-block text-truncate" style="max-width: 100px;">• {{ $off->name }}</span>
                    @endforeach
                    @if($slide->offers->count() > 2)
                        <span>+{{ $slide->offers->count() - 2 }} more</span>
                    @endif
                </div>
            </div>
        @elseif($slide->custom_url)
            <div class="d-flex flex-column">
                <span class="badge bg-secondary" style="font-size: 0.75rem; width: fit-content;">Custom URL</span>
                <small class="text-muted text-truncate" style="max-width: 160px;">{{ $slide->custom_url }}</small>
            </div>
        @else
            <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                <i class="fas fa-store me-1 text-muted"></i> All Products (Shop)
            </span>
        @endif
    </td>

    {{-- Status Toggle --}}
    <td>
        <button 
            type="button" 
            class="badge border-0 cursor-pointer status-toggle-btn"
            style="background: {{ $slide->status ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $slide->status ? '#059669' : '#dc2626' }}; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;"
            onclick="toggleSlideStatus({{ $slide->id }}, this)"
            title="Click to toggle status">
            <i class="fas {{ $slide->status ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
            <span>{{ $slide->status ? 'Active' : 'Inactive' }}</span>
        </button>
    </td>

    {{-- Actions --}}
    <td class="text-end">
        <div class="d-inline-flex gap-1">
            <a href="{{ route('admin.slides.show', $slide) }}" class="btn btn-sm btn-outline-info" title="View Details" style="padding: 4px 8px; border-radius: 6px;">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.slides.edit', $slide) }}" class="btn btn-sm btn-outline-primary" title="Edit Slide" style="padding: 4px 8px; border-radius: 6px;">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.slides.destroy', $slide) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slide banner?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Slide" style="padding: 4px 8px; border-radius: 6px;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-5 text-muted">
        <i class="fas fa-images fa-2x mb-2 d-block text-muted opacity-50"></i>
        No slide banners found matching your criteria.
    </td>
</tr>
@endforelse
