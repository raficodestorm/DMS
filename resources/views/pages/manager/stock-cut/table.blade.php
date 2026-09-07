@forelse($stockCuts as $cut)
<tr>
  <td>{{ $stockCuts->firstItem() ? $stockCuts->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>{{ $cut->supplier->company_name ?? 'N/A' }}</td>
  <td>{{ number_format($cut->net_total, 2) }} TK</td>
  <td>
    @if($cut->status === 'approved')
      <span class="badge" style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 0.78rem;">Approved</span>
    @elseif($cut->status === 'rejected')
      <span class="badge" style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 0.78rem;">Rejected</span>
    @else
      <span class="badge" style="background: #fef9c3; color: #a16207; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 0.78rem;">Pending</span>
    @endif
  </td>
  <td>{{ $cut->created_at ? $cut->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') : 'N/A' }}</td>
  <td class="action-icons">
    <div style="display: flex; gap: 5px;">
      <a href="{{ route('manager.stock.cut.show', $cut->id) }}" class="icon-btn view-icon" title="View Detail">
        <i class="fas fa-eye"></i>
      </a>

      @if($cut->status === 'pending')
      <a href="{{ route('manager.stock.cut.edit', $cut->id) }}" class="icon-btn edit-icon" title="Edit Request">
        <i class="fas fa-edit"></i>
      </a>

      <form action="{{ route('manager.stock.cut.destroy', $cut->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this pending return request?')" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon" title="Delete Request">
          <i class="fas fa-trash"></i>
        </button>
      </form>
      @endif
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted py-4">No stock return requests found.</td>
</tr>
@endforelse
