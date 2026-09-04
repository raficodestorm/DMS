@forelse($stocks as $stock)
<tr>
  <td>{{ $stocks->firstItem() ? $stocks->firstItem() + $loop->index : $loop->iteration }}</td>
  <td><strong>{{ $stock->product->name ?? '-' }}</strong></td>
  <td>{{ $stock->product->supplier->company_name ?? '-' }}</td>
  <td>{{ number_format($stock->product->price ?? 0, 2) }} TK</td>
  <td>{{ $stock->quantity }}</td>
  <td>
    @if($stock->product && $stock->quantity <= $stock->product->stock_alert)
      <span style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Low Stock</span>
    @else
      <span style="background: rgba(22, 163, 74, 0.1); color: #16a34a; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Available</span>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted">No stock items found.</td>
</tr>
@endforelse
