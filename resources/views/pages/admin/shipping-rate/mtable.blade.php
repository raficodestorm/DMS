@forelse($shippingRates as $rate)
<div class="manage-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2 pb-2" style="border-bottom: 1px solid var(--border-color, #e2e8f0);">
            <div>
                <h4 class="mb-0" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">{{ $rate->name }}</h4>
                <span style="font-size: 0.75rem; color: var(--text-muted);">#{{ $shippingRates->firstItem() ? $shippingRates->firstItem() + $loop->index : $loop->iteration }}</span>
            </div>
            <div>
                <button type="button" 
                        class="border-0 bg-transparent p-0 toggle-status-btn"
                        data-url="{{ route('admin.shipping-rates.toggle-status', $rate->id) }}"
                        onclick="toggleShippingRateStatus(this)"
                        title="Click to toggle status"
                        style="cursor: pointer;">
                    @if($rate->status)
                        <span class="status-active-badge">● Active</span>
                    @else
                        <span class="status-inactive-badge">● Inactive</span>
                    @endif
                </button>
            </div>
        </div>

        <div class="mb-2">
            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Location</span>
            <p style="font-size: 0.86rem; color: var(--text-main); margin-bottom: 0;">
                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $rate->city }}, <span class="text-muted">{{ $rate->country }}</span>
            </p>
        </div>

        <div class="mb-1">
            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Base Rate</span>
            <p style="font-size: 1rem; font-weight: 800; color: var(--primary, #3131ff); margin-bottom: 0;">
                ৳{{ number_format($rate->base_rate, 2) }}
            </p>
        </div>
    </div>

    <div class="card-actions d-flex align-items-center justify-content-end gap-2 pt-2" style="border-top: 1px solid var(--border-color, #e2e8f0);">
        <a href="{{ route('admin.shipping-rates.edit', $rate->id) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.shipping-rates.destroy', $rate->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this shipping rate?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;" title="Delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</div>
@empty
<p class="text-center text-muted py-4">No shipping rates found matching your criteria.</p>
@endforelse
