@forelse($orders as $order)
<tr>
  <td scope="row">{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration}}</td>
  <td>BRS{{ $order->id }}</td>
  <td>{{ $order->sr->branch->name ?? $order->manager->branch->name }}</td>
  <td>{{ $order->sr->fullname ?? $order->manager->fullname }}</td>
  <td>{{ number_format($order->net_total, 2) }} TK</td>
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
  <td>{{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</td>

  <td class="action-icons">
    <a href="{{ route('admin.order.show', $order->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted">No orders found.</td>
</tr>
@endforelse