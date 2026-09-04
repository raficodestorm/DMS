@forelse($costs as $cost)
<div class="manage-card">
  <div class="card-body">
    <div><span>Date</span>
      <p>{{ $cost->cost_date ? $cost->cost_date->format('d M Y') : 'N/A' }}</p>
    </div>
    <div><span>Category</span>
      <p><span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span></p>
    </div>
    <div><span>Description</span>
      <p class="fw-bold">{{ $cost->description }}</p>
    </div>
    <div><span>Amount</span>
      <p class="fw-bold text-danger">{{ number_format($cost->amount, 2) }} ৳</p>
    </div>
    <div><span>Recorded By</span>
      <p>{{ $cost->creator->username ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.company_costs.show', $cost->id) }}" class="icon-btn view-icon" title="View">
      <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.company_costs.edit', $cost->id) }}" class="icon-btn edit-icon" title="Edit">
      <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.company_costs.destroy', $cost->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
        @csrf @method('DELETE')
        <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; cursor:pointer;">
            <i class="fas fa-trash"></i>
        </button>
    </form>
  </div>
</div>
@empty
<p class="text-center text-muted">No expense records found.</p>
@endforelse
