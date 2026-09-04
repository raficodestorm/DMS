@forelse($transfers as $transfer)
@php
  $isOutgoing = $transfer->from_branch_id == auth()->user()->branch_id;
@endphp
<tr>
  <td>BRST{{ $transfer->id }}</td>
  <td>
    @if($isOutgoing)
      <span class="badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px;">Outgoing</span>
    @else
      <span class="badge" style="background: #f1f8e9; color: #388e3c; padding: 4px 8px; border-radius: 4px;">Incoming</span>
    @endif
  </td>
  <td>
    @if($isOutgoing)
      To: {{ $transfer->toBranch->name ?? '-' }}
    @else
      From: {{ $transfer->fromBranch->name ?? '-' }}
    @endif
  </td>
  <td>
    @if($transfer->status == 'pending')
      <span class="status-pending-badge">Pending</span>
    @elseif($transfer->status == 'approved')
      <span class="status-approved-badge" style="background: #fff3e0; color: #ef6c00;">Approved</span>
    @elseif($transfer->status == 'completed')
      <span class="status-approved-badge">Completed</span>
    @elseif($transfer->status == 'rejected')
      <span class="status-rejected-badge">Rejected</span>
    @endif
  </td>
  <td>{{ $transfer->created_at->format('d M Y') }}</td>
  <td class="action-icons">
    <a href="{{ route('manager.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if($transfer->status == 'pending' && $isOutgoing)
      <a href="{{ route('manager.stock-transfer.edit', $transfer->id) }}" class="icon-btn edit-icon">
        <i class="fa-solid fa-pen-to-square"></i>
      </a>
      <form action="{{ route('manager.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this request?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon">
          <i class="fa-solid fa-trash"></i>
        </button>
      </form>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted">No stock transfers found.</td>
</tr>
@endforelse
