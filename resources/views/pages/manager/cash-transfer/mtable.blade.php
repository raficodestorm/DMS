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
    <button type="button" 
            class="icon-btn view-icon border-0" 
            title="View Details" 
            onclick="viewTransferDetails({{ json_encode([
              'id' => '#TR-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
              'branch' => $item->branch->name ?? 'N/A',
              'amount' => '৳ ' . number_format($item->amount, 2),
              'cash_before' => '৳ ' . number_format($item->cash_before_transfer, 2),
              'cash_after' => '৳ ' . number_format($item->cash_after_transfer, 2),
              'status' => $item->status,
              'requested_by' => $item->requestedBy->fullname ?? $item->requestedBy->username ?? 'Branch Manager',
              'date' => $item->created_at->format('d M Y, h:i A'),
              'note' => $item->note ?? 'No note provided'
            ]) }})">
      <i class="fa-solid fa-eye me-1"></i> Details
    </button>

    @if($item->status === 'pending')
      <form action="{{ route('manager.cash-transfers.destroy', $item->id) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirmCancelTransfer(event, this)">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon border-0 w-100" title="Cancel Request">
          <i class="fa-solid fa-trash-can me-1"></i> Cancel
        </button>
      </form>
    @endif
  </div>
</div>
@empty
<div class="text-center text-muted py-4">
  <i class="fas fa-inbox me-1"></i> No cash transfer records found.
</div>
@endforelse
