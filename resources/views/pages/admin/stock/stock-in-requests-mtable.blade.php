@forelse($requests as $request)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $requests->firstItem() ? $requests->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Branch</span>
      <p>{{ $request->branch->name ?? 'N/A' }}</p>
    </div>
    <div><span>Supplier</span>
      <p>{{ $request->supplier->company_name ?? 'N/A' }}</p>
    </div>
    <div><span>Amount</span>
      <p>{{ number_format($request->net_total, 2) }} TK</p>
    </div>
    <div><span>Status</span>
      <p>
        @if($request->status == "pending")
        <span style="color:#d39e00;">⏳ Pending...</span>
        @elseif($request->status == "rejected")
        <span style="color:#dc3545;">● Rejected</span>
        @else
        <span style="color:#28a745;">✓ Approved</span>
        @endif
      </p>
    </div>
    <div><span>Date & Time</span>
      <p>{{ $request->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
    </div>
  </div>
  <div class="card-actions">
    <a href="{{ route('admin.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>
</div>
@empty
<p class="text-center text-muted">No requests found.</p>
@endforelse
