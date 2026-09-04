@forelse($costs as $cost)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $costs->firstItem() ? $costs->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Date</span>
      <p>{{ $cost->cost_date ? $cost->cost_date->format('d M Y') : 'N/A' }}</p>
    </div>
    <div><span>Category</span>
      <p><span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span></p>
    </div>
    <div><span>Description</span>
      <p>{{ Str::limit($cost->description, 50) }}</p>
    </div>
    <div><span>Amount</span>
      <p><strong>{{ number_format($cost->amount, 2) }} TK</strong></p>
    </div>
    <div><span>Recorded By</span>
      <p>{{ $cost->creator->username ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('manager.costs.show', $cost->id) }}" class="icon-btn view-icon" title="View">
      <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('manager.costs.edit', $cost->id) }}" class="icon-btn edit-icon" title="Edit">
      <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('manager.costs.destroy', $cost->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this cost record?')">
      @csrf @method('DELETE')
      <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; padding:0; cursor:pointer;">
        <i class="fas fa-trash"></i>
      </button>
    </form>
  </div>
</div>
@empty
<p class="text-center text-muted py-4">No expense records found.</p>
@endforelse
