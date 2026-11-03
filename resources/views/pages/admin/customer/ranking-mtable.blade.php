@forelse($customers as $index => $customer)
@php
  $rank    = $customer->global_rank ?? ($customers->firstItem() ? $customers->firstItem() + $index : $index + 1);
  $isAdmin = auth()->user()->role === 'admin';
@endphp
<div class="manage-card mb-3 p-3 position-relative" style="background: var(--section-bg); border: 1px solid var(--border-color); border-radius: 14px;">
  
  <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
    <div class="d-flex align-items-center gap-2">
      @if($rank == 1)
        <span class="rank-badge rank-1"><i class="fas fa-crown text-warning me-1"></i> Rank #1</span>
      @elseif($rank == 2)
        <span class="rank-badge rank-2"><i class="fas fa-medal me-1" style="color: #94a3b8;"></i> Rank #2</span>
      @elseif($rank == 3)
        <span class="rank-badge rank-3"><i class="fas fa-medal me-1" style="color: #cd7f32;"></i> Rank #3</span>
      @else
        <span class="rank-badge rank-normal">Rank #{{ $rank }}</span>
      @endif
      <span class="text-muted small">BRC200{{ $customer->id }}</span>
    </div>
    <button type="button" class="icon-btn view-icon btn-ranking-details" data-id="{{ $customer->id }}" data-rank="{{ $rank }}" style="border: none; cursor: pointer;" title="View Ranking Position Details">
      <i class="fa-solid fa-eye"></i>
    </button>
  </div>

  <div class="mb-2">
    <h5 class="fw-bold mb-1">{{ $customer->shop_name }}</h5>
    <div class="text-muted small">
      <i class="fas fa-code-branch me-1 text-primary"></i> {{ $customer->branch->name ?? 'N/A' }}
      @if($isAdmin && $customer->sr)
        | <i class="fas fa-user-tag me-1"></i> {{ $customer->sr->fullname }}
      @endif
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center p-2 rounded mb-2" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2);">
    <div>
      <small class="text-muted d-block">30 Days Order Amount</small>
      <span class="fw-bold text-success fs-6">৳ {{ number_format($customer->total_amount_30_days ?? 0, 2) }}</span>
    </div>
    <div class="text-end">
      <small class="text-muted d-block">Orders (30d)</small>
      <span class="badge bg-light text-dark border">{{ $customer->total_orders_30_days ?? 0 }} Orders</span>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center small text-muted">
    <span>Manager: {{ $customer->manager ?? 'N/A' }}</span>
    @if($isAdmin)
      <span>Due: <strong class="{{ ($customer->due ?? 0) > 0 ? 'text-danger' : 'text-success' }}">৳ {{ number_format($customer->due ?? 0, 2) }}</strong></span>
    @endif
  </div>

</div>
@empty
<div class="text-center py-5 text-muted">
  <i class="fas fa-info-circle me-1 text-primary"></i> No wholesale customers found matching the criteria.
</div>
@endforelse
