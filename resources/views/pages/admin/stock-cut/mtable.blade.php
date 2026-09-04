@forelse($stockCuts as $cut)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span><p>{{ $stockCuts->firstItem() ? $stockCuts->firstItem() + $loop->index : $loop->iteration }}</p></div>
    <div><span>Date</span><p>{{ $cut->created_at ? $cut->created_at->format('d M Y, h:i A') : 'N/A' }}</p></div>
    <div><span>Supplier</span><p>{{ $cut->supplier->company_name ?? 'N/A' }}</p></div>
    <div><span>Requested By</span><p>{{ $cut->requestedBy->fullname ?? $cut->requestedBy->username ?? 'N/A' }}</p></div>
    <div><span>Amount</span><p>{{ number_format($cut->net_total, 2) }} TK</p></div>
  </div>
  <div class="card-actions">
    <a href="{{ route('admin.stock.cut.cut.show', $cut->id) }}" class="icon-btn view-icon">
      <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.stock.cut.cut.edit', $cut->id) }}" class="icon-btn edit-icon">
      <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.stock.cut.cut.destroy', $cut->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record? Stock will be restored.')" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="icon-btn delete-icon" style="border: none; cursor: pointer;">
        <i class="fas fa-trash"></i>
      </button>
    </form>
  </div>
</div>
@empty
<p class="text-center text-muted">No stock cuts found.</p>
@endforelse
