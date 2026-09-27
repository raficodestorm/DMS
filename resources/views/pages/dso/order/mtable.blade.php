@forelse($orders as $order)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Order ID</span>
      <p><strong>{{ $order->order_id ?? ('BRS' . $order->id) }}</strong></p>
    </div>
    <div><span>Customer / Shop</span>
      <p>{{ $order->customer_name ?? ($order->customer->shop_name ?? ($order->customer->manager ?? 'N/A')) }}</p>
    </div>
    <div><span>Amount</span>
      <p>{{ number_format($order->net_total, 2) }} TK</p>
    </div>
    <div><span>Order Type</span>
      <p>
        @if($order->order_type == "field_order")
          <span class="emerald-type-badge">Field Order</span>
        @elseif($order->order_type == 'retail')
          <span class="pink-type-badge">Retail</span>
        @elseif($order->order_type == 'online')
          <span class="purple-type-badge">Online</span>
        @else
          <span class="status-undefined-badge">Undefined</span>
        @endif
      </p>
    </div>
    <div><span>Status</span>
      <p><span class="status-delivered-badge">● Delivered</span></p>
    </div>
    <div><span>Payment Status</span>
      <p>
        @if($order->payment_status == "unpaid")
          <span class="orange-type-badge">Unpaid</span>
        @elseif($order->payment_status == 'partial')
          <span class="purple-type-badge">Partial</span>
        @elseif($order->payment_status == 'paid')
          <span class="emerald-type-badge">Paid</span>
        @else
          <span class="status-undefined-badge">Undefined</span>
        @endif
      </p>
    </div>
    <div><span>Delivered Date</span>
      <p>{{ ($order->delivered_at ?? $order->created_at)->timezone(auth()->user()->timezone ?? 'Asia/Dhaka')->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('dso.order.show', $order->id) }}" class="icon-btn view-icon" title="View Order Details">
      <i class="fa-solid fa-eye"></i>
    </a>
    @php
      $invoiceUrl = null;
      if ($order->order_type == "retail") {
        $invoiceUrl = route('dso.order.view_retail_invoice', $order->id);
      } elseif ($order->order_type == "online" && (empty($order->customer_id) || $order->customer?->customer_type == 'retail')) {
        $invoiceUrl = route('dso.order.view_online_invoice', $order->id);
      } else {
        $invoiceUrl = route('dso.order.view_invoice', $order->id);
      }
    @endphp
    @if($invoiceUrl)
      <a href="{{ $invoiceUrl }}" class="icon-btn slip-icon" title="View Invoice" target="_blank">
        <i class="fa-solid fa-file-invoice"></i>
      </a>
    @endif
  </div>
</div>
@empty
<p class="text-center text-muted py-4">No delivered orders found.</p>
@endforelse
