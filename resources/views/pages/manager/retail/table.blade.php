@forelse($orders as $order)
<tr>
  <td><strong style="color: var(--primary);">BRS{{ $order->id }}</strong></td>
  <td>{{ $order->customer->shop_name ?? '—' }}</td>
  <td>৳ {{ number_format($order->net_total, 2) }}</td>
  <td>{{ $order->applied_deduction_percent ?? 0 }}%</td>
  <td>
    @if($order->status == 'approved')
      <span class="status-approved-badge">Approved</span>
    @elseif($order->status == 'pending_manager')
      <span class="status-pending-badge">Pending Manager</span>
    @elseif($order->status == 'complete')
      <span class="status-approved-badge" style="background: rgba(49,49,255,.1); color: var(--primary);">Complete</span>
    @elseif($order->status == 'delivered')
      <span class="status-approved-badge" style="background: rgba(79,172,254,.12); color: #4facfe;">Delivered</span>
    @elseif($order->status == 'rejected')
      <span class="status-rejected-badge">Rejected</span>
    @else
      <span class="status-pending-badge">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
    @endif
  </td>
  <td>{{ $order->created_at->format('d M Y') }}</td>
  <td class="action-icons">
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
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted">No retail orders found.</td>
</tr>
@endforelse
