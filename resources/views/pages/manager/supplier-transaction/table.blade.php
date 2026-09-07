@forelse($transactions as $tx)
@php
  $viewUrl = route('manager.supplier-transactions.show', $tx->id);
  if ($tx->type == 'buy') {
    if ($tx->stock_in_request_id) {
      $viewUrl = route('manager.stock.in.request.show', $tx->stock_in_request_id);
    } elseif (preg_match('/BRSK(\d+)/i', $tx->note ?? '', $m)) {
      $viewUrl = route('manager.stock.in.request.show', $m[1]);
    }
  } elseif ($tx->type == 'return') {
    if ($tx->stock_cut_id) {
      $viewUrl = route('manager.stock.cut.show', $tx->stock_cut_id);
    } elseif (preg_match('/BRSKR(\d+)/i', $tx->note ?? '', $m)) {
      $viewUrl = route('manager.stock.cut.show', $m[1]);
    }
  }
@endphp
<tr>

  <td>
    {{ $transactions->firstItem() ? $transactions->firstItem() + $loop->index : $loop->iteration }}
  </td>

  <td>
    <span style="font-weight: 600; color: var(--primary);">BRST00{{ $tx->id }}</span>
  </td>

  <td>
    {{ $tx->supplier->company_name ?? 'N/A' }}
  </td>

  <td>
    @if($tx->type == 'buy')
      <span style="color:#7c3aed; font-weight:600; background:#f3e8ff; padding:2px 8px; border-radius:10px; font-size:0.8rem;">Purchase</span>
    @elseif($tx->type == 'pay')
      <span style="color:#15803d; font-weight:600; background:#dcfce7; padding:2px 8px; border-radius:10px; font-size:0.8rem;">Payment</span>
    @elseif($tx->type == 'return')
      <span style="color:#b91c1c; font-weight:600; background:#fee2e2; padding:2px 8px; border-radius:10px; font-size:0.8rem;">Return</span>
    @elseif($tx->type == 'opening_balance')
      <span style="color:#0369a1; font-weight:600; background:#e0f2fe; padding:2px 8px; border-radius:10px; font-size:0.8rem;">Opening Bal.</span>
    @else
      <span style="color:#6b7280; font-weight:600;">{{ ucfirst($tx->type) }}</span>
    @endif
  </td>

  <td>
    <strong>{{ number_format($tx->amount, 2) }} TK</strong>
  </td>

  <td>
    {{ $tx->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
  </td>

  <td class="action-icons">
    <a href="{{ $viewUrl }}" class="icon-btn view-icon" title="View Details">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>

</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted py-4">
    <i class="fas fa-inbox me-1"></i> No supplier transactions found.
  </td>
</tr>
@endforelse
