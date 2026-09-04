@forelse($requests as $request)
<tr>
  <td scope="row">{{ $requests->firstItem() ? $requests->firstItem() + $loop->index : $loop->iteration }}</td>
  <td class="name">{{ $request->branch->name ?? 'N/A' }}</td>
  <td>{{ $request->supplier->company_name ?? 'N/A' }}</td>
  <td>{{ number_format($request->net_total, 2) }} TK</td>
  <td>
    @if($request->status == "pending")
    <span class="status-pending-badge">Pending...</span>
    @elseif($request->status == 'rejected')
    <span class="status-rejected-badge">Rejected</span>
    @else
    <span class="status-approved-badge">Approved</span>
    @endif
  </td>
  <td>{{ $request->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</td>
  <td class="action-icons">
    <a href="{{ route('admin.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted">No requests found.</td>
</tr>
@endforelse
