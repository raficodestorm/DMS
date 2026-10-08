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
      <p>{{ ($order->delivered_at ?? $order->created_at)->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('dso.order.show', $order->id) }}" class="icon-btn view-icon" title="View Order Details">
      <i class="fa-solid fa-eye"></i>
    </a>
    @php
    $invoicePrefix = match (auth()->user()->role) {
        'admin'    => 'admin',
        'manager'  => 'manager',
        'sr'       => 'sr',
        'dso'       => 'dso',
        'customer' => 'customer',
        default    => null,
    };
    @endphp

    @if(
        $invoicePrefix &&
        in_array($order->status, ['complete', 'delivered'], true) &&
        ($invoiceRoute = $order->invoiceRouteName($invoicePrefix))
    )
        <a href="{{ route($invoiceRoute, $order->id) }}"
          class="icon-btn slip-icon"
          title="View Purchase Invoice">
            <i class="fa-solid fa-file-invoice"></i>
        </a>
    @endif
  </div>
</div>
@empty
<p class="text-center text-muted py-4">No delivered orders found.</p>
@endforelse
