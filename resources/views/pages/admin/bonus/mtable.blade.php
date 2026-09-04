@forelse($bonuses as $bonus)
<div class="manage-card">
  <div class="card-body">
    <div><span>Date</span>
      <p>{{ $bonus->bonus_date ? $bonus->bonus_date->format('d M Y') : 'N/A' }}</p>
    </div>
    <div><span>Title</span>
      <p class="fw-bold">{{ $bonus->title }}</p>
    </div>
    <div><span>Type</span>
      <p>
        @php
            $badgeClass = match($bonus->type) {
                'incentive' => 'bg-info',
                'cashback'  => 'bg-success',
                'special'   => 'bg-warning',
                default     => 'bg-secondary',
            };
        @endphp
        <span class="badge {{ $badgeClass }}">{{ ucfirst($bonus->type) }}</span>
      </p>
    </div>
    <div><span>Amount</span>
      <p class="fw-bold text-success">{{ number_format($bonus->amount, 2) }} TK</p>
    </div>
    <div><span>Created By</span>
      <p>{{ $bonus->creator->username ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.bonuses.show', $bonus->id) }}" class="icon-btn view-icon" title="View">
      <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.bonuses.edit', $bonus->id) }}" class="icon-btn edit-icon" title="Edit">
      <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.bonuses.destroy', $bonus->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bonus entry?')">
        @csrf @method('DELETE')
        <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; cursor:pointer;">
            <i class="fas fa-trash"></i>
        </button>
    </form>
  </div>
</div>
@empty
<p class="text-center text-muted">No bonus entries found.</p>
@endforelse
