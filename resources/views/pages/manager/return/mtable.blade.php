@forelse($returns as $return)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $returns->firstItem() ? $returns->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Return ID</span>
      <p>BRET{{ $return->id }}</p>
    </div>
    <div><span>SR Name</span>
      <p>{{ $return->sr->username ?? 'N/A' }}</p>
    </div>
    <div><span>Customer</span>
      <p>{{ $return->customer->shop_name ?? 'N/A' }}</p>
    </div>
    <div><span>Amount</span>
      <p>{{ number_format($return->total_amount, 2) }} TK</p>
    </div>
    <div><span>Status</span>
      <p>
        @if($return->status == 'pending_sr')
            <span style="color:#d39e00;">Pending SR</span>
        @elseif($return->status == 'pending_manager')
            <span style="color:#1d4ed8;">Pending For Approval</span>
        @elseif($return->status == 'approved')
            <span style="color:#16a34a;">● Approved</span>
        @else
            <span style="color:#dc3545;">● Rejected</span>
        @endif
      </p>
    </div>
    <div><span>Date</span>
      <p>{{ $return->created_at->format('d M Y') }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('manager.return.show', $return->id) }}" class="icon-btn view-icon" title="View">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if($return->status == 'pending_manager' || $return->status == 'pending_sr')
        <a href="{{ route('manager.return.edit', $return->id) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
    @endif
  </div>
</div>
@empty
<p class="text-center text-muted">No return requests found.</p>
@endforelse
