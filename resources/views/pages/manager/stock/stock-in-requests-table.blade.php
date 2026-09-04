@forelse($requests as $req)
<tr>
  <td scope="row">{{ $requests->firstItem() ? $requests->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>{{ $req->supplier->company_name ?? '-' }}</td>
  <td>{{ number_format($req->net_total, 2) }} TK</td>
  <td>
    @if($req->status == "pending")
      <span class="status-pending-badge">Pending...</span>
    @elseif($req->status == 'rejected')
      <span class="status-rejected-badge">Rejected</span>
    @else
      <span class="status-approved-badge">Approved</span>
    @endif
  </td>
  <td>{{ $req->created_at->timezone(auth()->user()->timezone ?? 'UTC')->format('d M Y, h:i A') }}</td>

  <td class="action-icons">
    <a href="{{ route('manager.stock.in.request.show', $req->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted">No stock-in requests found.</td>
</tr>
@endforelse
