@forelse($customers as $index => $customer)
@php
  $rank    = $customer->global_rank ?? ($customers->firstItem() ? $customers->firstItem() + $index : $index + 1);
  $isAdmin = auth()->user()->role === 'admin';
@endphp
<tr>
  <td>
    @if($rank == 1)
      <span class="rank-badge rank-1" title="Top Ranked Customer">
        <i class="fas fa-crown me-1 text-warning"></i> #1
      </span>
    @elseif($rank == 2)
      <span class="rank-badge rank-2" title="2nd Ranked Customer">
        <i class="fas fa-medal me-1" style="color: #94a3b8;"></i> #2
      </span>
    @elseif($rank == 3)
      <span class="rank-badge rank-3" title="3rd Ranked Customer">
        <i class="fas fa-medal me-1" style="color: #cd7f32;"></i> #3
      </span>
    @else
      <span class="rank-badge rank-normal">
        #{{ $rank }}
      </span>
    @endif
  </td>
  <td class="fw-semibold">BRC200{{ $customer->id }}</td>
  <td class="name">
    <div class="d-flex align-items-center gap-2">
      <div class="customer-avatar-sm">
        <i class="fas fa-store"></i>
      </div>
      <div>
        <div class="fw-bold">{{ $customer->shop_name }}</div>
        @if($customer->manager)
          <small class="text-muted"><i class="fas fa-user-tie me-1"></i>{{ $customer->manager }}</small>
        @endif
      </div>
    </div>
  </td>
  <td>
    <span class="badge bg-light text-dark border">
      <i class="fas fa-code-branch me-1 text-primary"></i>{{ $customer->branch->name ?? 'N/A' }}
    </span>
  </td>
  @if($isAdmin)
    <td>
      @if($customer->sr)
        <span class="text-muted"><i class="fas fa-user-tag me-1"></i>{{ $customer->sr->fullname }}</span>
      @else
        <span class="text-muted">-</span>
      @endif
    </td>
  @endif
  <td class="text-center">
    <span class="orders-count-badge">
      <i class="fas fa-box me-1"></i> {{ $customer->total_orders_30_days ?? 0 }}
    </span>
  </td>
  <td class="text-end">
    <span class="sales-amount-highlight">
      ৳ {{ number_format($customer->total_amount_30_days ?? 0, 2) }}
    </span>
  </td>
  @if($isAdmin)
    <td class="text-end">
      @if(($customer->due ?? 0) > 0)
        <span class="text-danger fw-semibold">৳ {{ number_format($customer->due, 2) }}</span>
      @else
        <span class="text-success fw-semibold">৳ 0.00</span>
      @endif
    </td>
  @endif
  <td class="action-icons">
    <button type="button" class="icon-btn view-icon btn-ranking-details" data-id="{{ $customer->id }}" data-rank="{{ $rank }}" style="border: none; cursor: pointer;" title="View Ranking Position Details">
      <i class="fa-solid fa-eye"></i>
    </button>
  </td>
</tr>
@empty
<tr>
  <td colspan="{{ auth()->user()->role === 'admin' ? 9 : 7 }}" class="text-center py-5 text-muted">
    <i class="fas fa-info-circle me-1 text-primary"></i> No wholesale customers found matching the criteria.
  </td>
</tr>
@endforelse
