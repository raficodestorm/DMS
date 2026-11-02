@forelse($transfers as $item)
<div class="manage-card">
  <div class="card-body">
    <div>
      <span>S.No</span>
      <p>{{ $transfers->firstItem() ? $transfers->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div>
      <span>Transfer ID</span>
      <p class="fw-bold text-primary">#TR-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div>
      <span>Branch</span>
      <p class="fw-semibold text-main">{{ $item->branch->name ?? 'N/A' }}</p>
    </div>
    <div>
      <span>Requested By</span>
      <p class="text-muted">{{ $item->requestedBy->fullname ?? $item->requestedBy->username ?? 'Branch Manager' }}</p>
    </div>
    <div>
      <span>Amount</span>
      <p class="fw-bold text-main">৳ {{ number_format($item->amount, 2) }}</p>
    </div>
    <div>
      <span>Status</span>
      <p>
        @if($item->status === 'pending')
          <span style="color:#d39e00; font-weight: 600;">● Pending...</span>
        @elseif($item->status === 'approved')
          <span style="color:#28a745; font-weight: 600;">● Approved</span>
        @elseif($item->status === 'rejected')
          <span style="color:#dc3545; font-weight: 600;">● Rejected</span>
        @else
          <span>{{ ucfirst($item->status) }}</span>
        @endif
      </p>
    </div>
    <div>
      <span>Cash Before</span>
      <p>৳ {{ number_format($item->cash_before_transfer, 2) }}</p>
    </div>
    <div>
      <span>Cash After</span>
      <p>৳ {{ number_format($item->cash_after_transfer, 2) }}</p>
    </div>
    <div>
      <span>Date & Time</span>
      <p>{{ $item->created_at->format('d M Y, h:i A') }}</p>
    </div>
    @if(!empty($item->note))
    <div>
      <span>Note</span>
      <p class="text-truncate" style="max-width: 180px;" title="{{ $item->note }}">{{ $item->note }}</p>
    </div>
    @endif
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.cash-transfers.show', $item->id) }}" class="icon-btn view-icon w-100" title="View Details & Action">
      <i class="fa-solid fa-eye me-1"></i> View Details & Action
    </a>
  </div>
</div>
@empty
<div class="text-center text-muted py-4">
  <i class="fas fa-inbox me-1"></i> No cash transfer requests found.
</div>
@endforelse
