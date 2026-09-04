@forelse($transfers as $transfer)
@php
  $isOutgoing = $transfer->from_branch_id == auth()->user()->branch_id;
@endphp
<div class="manage-card">
  <div class="card-body">
    <div><span>ID</span><p>BRST{{ $transfer->id }}</p></div>
    <div><span>Type</span>
      <p>
        @if($isOutgoing)
          <span style="color: #1976d2;">Outgoing</span>
        @else
          <span style="color: #388e3c;">Incoming</span>
        @endif
      </p>
    </div>
    <div><span>Branch</span>
      <p>
        @if($isOutgoing)
          To: {{ $transfer->toBranch->name ?? '-' }}
        @else
          From: {{ $transfer->fromBranch->name ?? '-' }}
        @endif
      </p>
    </div>
    <div><span>Status</span>
      <p>
        @if($transfer->status == 'pending')
          <span style="color: #ffc107;">Pending</span>
        @elseif($transfer->status == 'approved')
          <span style="color: #fd7e14;">Approved</span>
        @elseif($transfer->status == 'completed')
          <span style="color: #28a745;">Completed</span>
        @else
          <span style="color: #dc3545;">Rejected</span>
        @endif
      </p>
    </div>
    <div><span>Date</span><p>{{ $transfer->created_at->format('d M Y') }}</p></div>
  </div>
  <div class="card-actions">
    <a href="{{ route('manager.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if($transfer->status == 'pending' && $isOutgoing)
      <a href="{{ route('manager.stock-transfer.edit', $transfer->id) }}" class="icon-btn edit-icon">
        <i class="fa-solid fa-pen-to-square"></i>
      </a>
      <form action="{{ route('manager.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon" onclick="return confirm('Are you sure?')">
          <i class="fa-solid fa-trash"></i>
        </button>
      </form>
    @endif
  </div>
</div>
@empty
<p class="text-center text-muted">No stock transfers found.</p>
@endforelse
