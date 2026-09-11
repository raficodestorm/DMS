@forelse($offers as $offer)
<tr>
    <td>
        <span class="fw-semibold text-muted">#{{ $offers->firstItem() ? $offers->firstItem() + $loop->index : $loop->iteration }}</span>
    </td>
    <td>
        <div class="fw-bold" style="color: var(--text-main, #1e293b);">{{ $offer->name }}</div>
        @if($offer->customer_type == 'retail' && !empty($offer->coupon_code))
            <span class="badge" style="background: rgba(49, 49, 255, 0.08); color: var(--primary); font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px dashed rgba(49, 49, 255, 0.3); font-weight: 600;">
                <i class="fas fa-ticket-alt me-1"></i>{{ $offer->coupon_code }}
            </span>
        @endif
    </td>
    <td>
        @if($offer->product)
            @php
                $img = $offer->product->image ? (str_starts_with($offer->product->image, 'uploads/') ? asset($offer->product->image) : asset('uploads/' . $offer->product->image)) : null;
            @endphp
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 34px; height: 34px; border-radius: 6px; background: var(--background, #f1f5f9); border: 1px solid var(--border-color, #e2e8f0); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $offer->product->name }}" style="width: 100%; height: 100%; object-fit: contain;">
                    @else
                        <i class="fas fa-box text-muted" style="font-size: 13px;"></i>
                    @endif
                </div>
                <div style="min-width: 0;">
                    <div class="fw-semibold" style="font-size: 0.88rem; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $offer->product->name }}">{{ $offer->product->name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Price: {{ $offer->product->price ?? 'N/A' }}
                    </div>
                </div>
            </div>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    <td>
        @if($offer->customer_type == 'retail')
            <span class="purple-type-badge">Retail</span>
        @else
            <span class="emerald-type-badge">Wholesale</span>
        @endif
    </td>
    <td>
        <strong style="color: var(--primary, #3131ff); font-size: 0.92rem;">
            {{ $offer->type == 'percentage' ? $offer->discount_amount . '%' : number_format($offer->discount_amount, 2) . ' TK' }}
        </strong>
    </td>
    <td style="font-size: 0.82rem; white-space: nowrap;">
        <div><i class="far fa-calendar-alt text-muted me-1"></i>{{ \Carbon\Carbon::parse($offer->start_date)->format('d M Y') }}</div>
        <div class="text-muted"><i class="fas fa-arrow-right text-muted me-1" style="font-size: 10px;"></i>{{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}</div>
    </td>
    <td>
        @if($offer->status == 1)
            <span class="status-active-badge">● Active</span>
        @else
            <span class="status-inactive-badge">● Inactive</span>
        @endif
    </td>
    <td class="action-icons">
        <a href="{{ route('admin.offers.show', $offer->id) }}" class="icon-btn view-icon" title="View Offer">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.offers.edit', $offer->id) }}" class="icon-btn edit-icon" title="Edit Offer">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this offer?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" title="Delete" style="border: none;">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-4 text-muted">
        <i class="fas fa-info-circle me-1"></i> No offers found.
    </td>
</tr>
@endforelse
