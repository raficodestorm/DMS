@forelse($orders as $order)
<tr>
  <td scope="row">{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration}}</td>
  <td>{{ $order->order_id ?? ('BRS' . $order->id) }}</td>
  <td>{{ $order->branch->name ?? "N/A" }}</td>
  <td>{{ number_format($order->net_total, 2) }} TK</td>
  
  <td>
    @if($order->order_type == "field_order")
    <span class="emerald-type-badge">Field Order</span>
    @elseif($order->order_type == 'retail')
    <span class="pink-type-badge">Retail</span>
    @elseif($order->order_type == 'online')
    <span class="purple-type-badge">Online</span>
    @else
    <span class="status-undefined-badge">Undefined</span>
    @endif
  </td>

  <td>
    @if($order->status == "pending_sr")
    <span class="status-pending-badge">Pending..SR..</span>
    @elseif($order->status == 'pending_manager')
    <span class="status-pmanager-badge">Pending..Manager..</span>
    @elseif($order->status == 'rejected')
    <span class="status-rejected-badge">Rejected</span>
    @elseif($order->status == 'complete')
    <span class="status-complete-badge">Complete</span>
    @elseif($order->status == 'delivered')
    <span class="status-delivered-badge">Delivered</span>
    @elseif($order->status == 'approved')
    <span class="status-approved-badge">Approved</span>
    @else
    <span class="status-undefined-badge">Undefined</span>
    @endif
  </td>

  <td >
    @if($order->payment_status == "unpaid")
    <span class="orange-type-badge ">Unpaid</span>
    @elseif($order->payment_status == "partial")
    <span class="purple-type-badge">Partial</span>
    @elseif($order->payment_status == 'paid')
    <span class="emerald-type-badge">Paid</span>
    @else
    <span class="status-undefined-badge">Undefined</span>
    @endif
  </td>

  <td>{{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</td>

  <td class="action-icons">
    <a href="{{ route('admin.order.show', $order->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @if($order->status == 'complete' || $order->status == 'delivered')
    @if($order->order_type == "field_order" || ($order->order_type == "online" && $order->customer?->customer_type == 'wholesale'))
            <a href="{{ route('manager.order.view_invoice', $order->id) }}" class="icon-btn slip-icon" title="View Purchase Invoice">
              <i class="fa-solid fa-file-invoice"></i>
            </a>

        @elseif($order->order_type == "retail")
            <a href="{{ route('manager.order.view_retail_invoice', $order->id) }}" class="icon-btn slip-icon" title="View Purchase Invoice">
              <i class="fa-solid fa-file-invoice"></i>
            </a>
        @elseif( $order->order_type == "online" && (empty($order->customer_id) || $order->customer?->customer_type == 'retail'))
            <a href="{{ route('manager.order.view_online_invoice', $order->id) }}" class="icon-btn slip-icon" title="View Purchase Invoice">
              <i class="fa-solid fa-file-invoice"></i>
            </a>
        @endif
    
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="9" class="text-center text-muted">No orders found.</td>
</tr>
@endforelse