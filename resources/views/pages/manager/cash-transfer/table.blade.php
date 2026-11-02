@forelse($transfers as $item)
<tr>
  <td>{{ $transfers->firstItem() ? $transfers->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>
    <span class="fw-bold text-primary">#TR-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</span>
  </td>
  <td>
    <span class="fw-bold text-main">৳ {{ number_format($item->amount, 2) }}</span>
  </td>
  <td class="text-muted">
    ৳ {{ number_format($item->cash_before_transfer, 2) }}
  </td>
  <td class="text-muted">
    ৳ {{ number_format($item->cash_after_transfer, 2) }}
  </td>
  <td>
    @if($item->status === 'pending')
      <span class="status-pending-badge">Pending...</span>
    @elseif($item->status === 'approved')
      <span class="status-approved-badge">Approved</span>
    @elseif($item->status === 'rejected')
      <span class="status-rejected-badge">Rejected</span>
    @else
      <span class="status-undefined-badge">{{ ucfirst($item->status) }}</span>
    @endif
  </td>
  <td>
    <div style="font-size: 0.85rem; line-height: 1.3;">
      <span>{{ $item->created_at->format('d M Y') }}</span>
      <span class="text-muted d-block" style="font-size: 0.75rem;">{{ $item->created_at->format('h:i A') }}</span>
    </div>
  </td>
  <td class="action-icons">
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
      <i class="fa-solid fa-eye"></i>
    </button>

    @if($item->status === 'pending')
      <form action="{{ route('manager.cash-transfers.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirmCancelTransfer(event, this)">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon border-0" title="Cancel Request">
          <i class="fa-solid fa-trash-can"></i>
        </button>
      </form>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center py-4 text-muted">
    <i class="fas fa-inbox me-1"></i> No cash transfer records found.
  </td>
</tr>
@endforelse
