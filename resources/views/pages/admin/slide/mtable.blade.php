@forelse($slides as $index => $slide)
<div class="manage-mobile-card mb-3 p-3" id="slide-mcard-{{ $slide->id }}" style="background: var(--section-bg); border: 1px solid var(--border-color); border-radius: 14px;">
    
    {{-- Banner Image on top of card --}}
    <div style="width: 100%; height: 110px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color); margin-bottom: 12px; background: #000;">
        <img src="{{ asset($slide->image) }}" alt="{{ $slide->name }}" style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <h6 class="fw-bold mb-1" style="color: var(--text-main);">{{ $slide->name }}</h6>
            <span style="background: rgba(49, 49, 255, 0.08); color: var(--primary); font-weight: 700; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; border: 1px solid rgba(49, 49, 255, 0.2);">
                Position #{{ $slide->position }}
            </span>
        </div>

        <button 
            type="button" 
            class="badge border-0 cursor-pointer status-toggle-btn"
            style="background: {{ $slide->status ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $slide->status ? '#059669' : '#dc2626' }}; padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;"
            onclick="toggleSlideStatus({{ $slide->id }}, this)">
            <i class="fas {{ $slide->status ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
            <span>{{ $slide->status ? 'Active' : 'Inactive' }}</span>
        </button>
    </div>

    {{-- Connected Offers Info --}}
    <div class="mb-3 p-2" style="background: var(--background); border-radius: 8px; font-size: 0.82rem;">
        @if($slide->offers && $slide->offers->count() > 0)
            <div class="d-flex align-items-center gap-1 text-primary fw-semibold mb-1">
                <i class="fas fa-tags text-accent"></i> {{ $slide->offers->count() }} {{ Str::plural('Offer', $slide->offers->count()) }} Connected:
            </div>
            <div class="text-muted" style="font-size: 0.76rem;">
                @foreach($slide->offers->take(3) as $off)
                    <div>• {{ $off->name }} @if($off->product)({{ $off->product->name }})@endif</div>
                @endforeach
                @if($slide->offers->count() > 3)
                    <div class="text-accent">+{{ $slide->offers->count() - 3 }} more offers</div>
                @endif
            </div>
        @elseif($slide->custom_url)
            <div class="text-muted">Target: <span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $slide->custom_url }}</span></div>
        @else
            <div class="text-muted"><i class="fas fa-store me-1"></i> Default (Shop Catalog)</div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
        <a href="{{ route('admin.slides.show', $slide) }}" class="btn btn-sm btn-outline-info" style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">
            <i class="fas fa-eye me-1"></i> View
        </a>
        <a href="{{ route('admin.slides.edit', $slide) }}" class="btn btn-sm btn-outline-primary" style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <form action="{{ route('admin.slides.destroy', $slide) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slide banner?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">
                <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
        </form>
    </div>

</div>
@empty
<div class="text-center py-5 text-muted">
    <i class="fas fa-images fa-2x mb-2 d-block text-muted opacity-50"></i>
    No slide banners found matching your criteria.
</div>
@endforelse
