@forelse($shippingRates as $rate)
<tr>
    <td>
        <span class="fw-semibold text-muted">#{{ $shippingRates->firstItem() ? $shippingRates->firstItem() + $loop->index : $loop->iteration }}</span>
    </td>
    <td>
        <div class="fw-bold" style="color: var(--text-main, #1e293b);">{{ $rate->name }}</div>
    </td>
    <td>
        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; background: rgba(49, 49, 255, 0.06); color: var(--primary); border: 1px solid rgba(49, 49, 255, 0.15);">
            <i class="fas fa-globe-asia" style="font-size: 0.75rem;"></i> {{ $rate->country }}
        </span>
    </td>
    <td>
        <span style="font-weight: 600; color: var(--text-main); font-size: 0.88rem;">
            <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 0.8rem;"></i> {{ $rate->city }}
        </span>
    </td>
    <td>
        <strong style="color: var(--primary, #3131ff); font-size: 0.95rem;">
            ৳{{ number_format($rate->base_rate, 2) }}
        </strong>
    </td>
    <td>
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
    </td>
    <td class="text-end">
        <div class="d-flex align-items-center justify-content-end gap-1">
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
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted py-5">
        <i class="fas fa-shipping-fast fa-2x mb-2 d-block opacity-50"></i>
        No shipping rates found matching your criteria.
    </td>
</tr>
@endforelse
