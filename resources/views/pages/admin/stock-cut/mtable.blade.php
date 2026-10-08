@forelse($stockCuts as $cut)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span><p>{{ $stockCuts->firstItem() ? $stockCuts->firstItem() + $loop->index : $loop->iteration }}</p></div>
    <div><span>Branch</span><p>{{ $cut->branch->name ?? 'N/A' }}</p></div>
    <div><span>Supplier</span><p>{{ $cut->supplier->company_name ?? 'N/A' }}</p></div>
    <div><span>Amount</span><p>{{ number_format($cut->net_total, 2) }} TK</p></div>
    <div>
      <span>Status</span>
      <p>
        @if($cut->status === 'approved')
          <span class="badge" style="background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 12px; font-weight: 600; font-size: 0.75rem;">Approved</span>
        @elseif($cut->status === 'rejected')
          <span class="badge" style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 12px; font-weight: 600; font-size: 0.75rem;">Rejected</span>
        @else
          <span class="badge" style="background: #fef9c3; color: #a16207; padding: 3px 8px; border-radius: 12px; font-weight: 600; font-size: 0.75rem;">Pending</span>
        @endif
      </p>
    </div>
    <div><span>Date</span><p>{{ $cut->created_at ? $cut->created_at->format('d M Y, h:i A') : 'N/A' }}</p></div>
  </div>
  <div class="card-actions">
    <a href="{{ route('admin.stock.cut.cut.show', $cut->id) }}" class="icon-btn view-icon" title="View Detail">
      <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.stock.cut.cut.edit', $cut->id) }}" class="icon-btn edit-icon" title="Edit Record">
      <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.stock.cut.cut.destroy', $cut->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="icon-btn delete-icon" title="Delete Record">
        <i class="fas fa-trash"></i>
      </button>
    </form>
  </div>
</div>
@empty
<p class="text-center text-muted py-4">No stock cuts found.</p>
@endforelse
