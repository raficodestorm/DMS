@forelse($transfers as $transfer)
<tr>
  <td><strong>BRST{{ $transfer->id }}</strong></td>
  <td>{{ $transfer->fromBranch->name ?? 'N/A' }}</td>
  <td>{{ $transfer->toBranch->name ?? 'N/A' }}</td>
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
    <a href="{{ route('admin.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if($transfer->status != 'completed')
      <form action="{{ route('admin.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this transfer request?')">
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
