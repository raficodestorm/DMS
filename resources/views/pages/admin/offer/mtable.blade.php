@forelse($offers as $offer)
<div class="manage-card">
    <div class="card-body">
        <div>
            <span>S.No</span>
            <p class="fw-semibold">#{{ $offers->firstItem() ? $offers->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div>
            <span>Offer Name</span>
            <p class="fw-bold mb-1">{{ $offer->name }}</p>
            @if($offer->customer_type == 'retail' && !empty($offer->coupon_code))
                <span class="badge" style="background: rgba(49, 49, 255, 0.08); color: var(--primary); font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px dashed rgba(49, 49, 255, 0.3); font-weight: 600;">
                    <i class="fas fa-ticket-alt me-1"></i>{{ $offer->coupon_code }}
                </span>
            @endif
        </div>
        <div>
            <span>Customer Type</span>
            <p>
                @if($offer->customer_type == 'retail')
                    <span class="purple-type-badge">Retail</span>
                @else
                    <span class="emerald-type-badge">Wholesale</span>
                @endif
            </p>
        </div>
        <div>
            <span>Product</span>
            <p class="fw-semibold">{{ $offer->product->name ?? 'N/A' }}</p>
        </div>
        <div>
            <span>Discount</span>
            <p class="fw-bold" style="color: var(--primary, #3131ff);">
                {{ $offer->type == 'percentage' ? $offer->discount_amount . '%' : number_format($offer->discount_amount, 2) . ' TK' }}
            </p>
        </div>
        <div>
            <span>Validity</span>
            <p style="font-size: 0.85rem;">
                {{ \Carbon\Carbon::parse($offer->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}
            </p>
        </div>
        <div>
            <span>Status</span>
            <p>
                @if($offer->status == 1)
                    <span class="status-active-badge">● Active</span>
                @else
                    <span class="status-inactive-badge">● Inactive</span>
                @endif
            </p>
        </div>
    </div>

    <div class="card-actions">
        <a href="{{ route('admin.offers.show', $offer->id) }}" class="icon-btn view-icon" title="View Offer">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.offers.edit', $offer->id) }}" class="icon-btn edit-icon" title="Edit Offer">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this offer?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; cursor:pointer;">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</div>
@empty
<p class="text-center text-muted py-4">
    <i class="fas fa-info-circle me-1"></i> No offers found.
</p>
@endforelse
