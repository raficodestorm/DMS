@forelse($orders as $order)
<div class="manage-card">
  <div class="card-body">
    <div><span>Order</span><p><strong style="color: var(--primary);">BRS{{ $order->id }}</strong></p></div>
    <div><span>Customer</span><p>{{ $order->customer->shop_name ?? '—' }}</p></div>
    <div><span>Net Total</span><p>৳ {{ number_format($order->net_total, 2) }}</p></div>
    <div><span>Deduction</span><p>{{ $order->applied_deduction_percent ?? 0 }}%</p></div>
    <div><span>Status</span>
      <p>
        @if($order->status == 'approved')
          <span style="color: #10b981;">Approved</span>
        @elseif($order->status == 'pending_manager')
          <span style="color: #f59e0b;">Pending Manager</span>
        @elseif($order->status == 'complete')
          <span style="color: var(--primary);">Complete</span>
        @elseif($order->status == 'delivered')
          <span style="color: #4facfe;">Delivered</span>
        @elseif($order->status == 'rejected')
          <span style="color: #ef4444;">Rejected</span>
        @else
          <span>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
        @endif
      </p>
    </div>
    <div><span>Date</span><p>{{ $order->created_at->format('d M Y') }}</p></div>
  </div>
  <div class="card-actions">
    <a href="{{ route('manager.order.view_retail_invoice', $order->id) }}" class="icon-btn" title="Invoice" style="color: #10b981;">
      <i class="fas fa-print"></i>
    </a>
    <a href="{{ route('manager.retail.show', $order->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if(!in_array($order->status, ['complete','delivered']))
    <a href="{{ route('manager.retail.edit', $order->id) }}" class="icon-btn edit-icon">
      <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <form action="{{ route('manager.retail.destroy', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete BRS{{ $order->id }}?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="icon-btn delete-icon">
        <i class="fa-solid fa-trash"></i>
      </button>
    </form>
    @endif
  </div>
</div>
@empty
<p class="text-center text-muted">No retail orders found.</p>
@endforelse
