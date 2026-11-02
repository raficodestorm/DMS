@forelse($transfers as $item)
<tr>
  <td>{{ $transfers->firstItem() ? $transfers->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>
    <span class="fw-bold text-primary">#TR-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</span>
  </td>
  <td>
    <span class="fw-semibold text-main">{{ $item->branch->name ?? 'N/A' }}</span>
  </td>
  <td>
    <span class="text-muted" style="font-size: 0.85rem;">{{ $item->requestedBy->fullname ?? $item->requestedBy->username ?? 'Branch Manager' }}</span>
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
    <a href="{{ route('admin.cash-transfers.show', $item->id) }}" class="icon-btn view-icon" title="View Details & Action">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="10" class="text-center py-4 text-muted">
    <i class="fas fa-inbox me-1"></i> No cash transfer requests found.
  </td>
</tr>
@endforelse
