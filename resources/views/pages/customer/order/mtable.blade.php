@forelse($orders as $order)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Order ID</span>
      <p>BRS{{ $order->id }}</p>
    </div>
    <div><span>Shop Name</span>
      <p>{{ $order->customer->shop_name ?? 'N/A' }}</p>
    </div>

    <div><span>Amount</span>
      <p>{{ number_format($order->net_total, 2) }} TK</p>
    </div>
    <div><span>Status</span>
      <p>
        @if($order->status == "pending_sr")
        <span style="color:#d39e00;">Pending..SR..</span>

        @elseif($order->status == "pending_manager")
        <span style="color:#1d4ed8;">Pending..Manager..</span>

        @elseif($order->status == "rejected")
        <span style="color:#dc3545;">● Rejected</span>

        @elseif($order->status == "cancelled")
        <span style="color:#dc3545;">● Cancelled</span>

        @elseif($order->status == "approved")
        <span style="color:#16a34a;">● Approved</span>

        @elseif($order->status == "complete")
        <span style="color:#6d28d9;">● Complete</span>

        @elseif($order->status == "delivered")
        <span style="color:#15803d;">● Delivered</span>

        @else
        <span style="color:#6b7280;">Undefined</span>
        @endif
      </p>
    </div>
    <div><span>Date & Time</span>
      <p>{{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('sr.order.show', $order->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>
</div>
@empty
<p class="text-center text-muted">No orders found.</p>
@endforelse