@forelse($requests as $req)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $requests->firstItem() ? $requests->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Supplier</span>
      <p>{{ $req->supplier->company_name ?? '-' }}</p>
    </div>
    <div><span>Amount</span>
      <p>{{ number_format($req->net_total, 2) }} TK</p>
    </div>
    <div><span>Status</span>
      <p>
        @if($req->status == "pending")
          <span style="color:#d39e00;">Pending...</span>
        @elseif($req->status == "rejected")
          <span style="color:#dc3545;">● Rejected</span>
        @else
          <span style="color:#28a745;">● Approved</span>
        @endif
      </p>
    </div>
    <div><span>Date & Time</span>
      <p>{{ $req->created_at->timezone(auth()->user()->timezone ?? 'UTC')->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('manager.stock.in.request.show', $req->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>
</div>
@empty
<p class="text-center text-muted">No stock-in requests found.</p>
@endforelse
