@forelse($transfers as $transfer)
<div class="manage-card">
  <div class="card-body">
    <div><span>ID</span><p>BRST{{ $transfer->id }}</p></div>
    <div><span>From</span><p>{{ $transfer->fromBranch->name ?? 'N/A' }}</p></div>
    <div><span>To</span><p>{{ $transfer->toBranch->name ?? 'N/A' }}</p></div>
    <div><span>Status</span>
      <p>
        @if($transfer->status == 'pending')
          <span style="color: #ffc107;">⏳ Pending</span>
        @elseif($transfer->status == 'approved')
          <span style="color: #fd7e14;">● Approved</span>
        @elseif($transfer->status == 'completed')
          <span style="color: #28a745;">✓ Completed</span>
        @else
          <span style="color: #dc3545;">✖ Rejected</span>
        @endif
      </p>
    </div>
    <div><span>Date</span><p>{{ $transfer->created_at->format('d M Y') }}</p></div>
  </div>
  <div class="card-actions">
    <a href="{{ route('admin.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if($transfer->status != 'completed')
      <form action="{{ route('admin.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon">
          <i class="fa-solid fa-trash"></i>
        </button>
      </form>
    @endif
  </div>
</div>
@empty
<p class="text-center text-muted">No stock transfers found.</p>
@endforelse
