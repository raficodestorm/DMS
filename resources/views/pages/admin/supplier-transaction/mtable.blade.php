@forelse($transactions as $tx)
@php
  $viewUrl = route('admin.supplier-transactions.show', $tx->id);
  if ($tx->type == 'buy') {
    if ($tx->stock_in_request_id) {
      $viewUrl = route('admin.stock.in.request.show', $tx->stock_in_request_id);
    } elseif (preg_match('/BRSK(\d+)/i', $tx->note ?? '', $m)) {
      $viewUrl = route('admin.stock.in.request.show', $m[1]);
    }
  } elseif ($tx->type == 'return') {
    if ($tx->stock_cut_id) {
      $viewUrl = route('admin.stock.cut.cut.show', $tx->stock_cut_id);
    } elseif (preg_match('/BRSKR(\d+)/i', $tx->note ?? '', $m)) {
      $viewUrl = route('admin.stock.cut.cut.show', $m[1]);
    }
  }
@endphp

<div class="manage-card">
  <div class="card-body">

    <div>
      <span>S.No</span>
      <p>{{ $transactions->firstItem() ? $transactions->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>

    <div>
      <span>Txn ID</span>
      <p style="font-weight:700; color:var(--primary);">BRST00{{ $tx->id }}</p>
    </div>

    <div>
      <span>Supplier</span>
      <p>{{ $tx->supplier->company_name ?? 'N/A' }}</p>
    </div>

    <div>
      <span>Branch</span>
      <p>{{ $tx->branch->name ?? 'N/A' }}</p>
    </div>

    <div>
      <span>Type</span>
      <p>
        @if($tx->type == 'buy')
          <span style="color:#7c3aed; font-weight:600;">Purchase</span>
        @elseif($tx->type == 'pay')
          <span style="color:#15803d; font-weight:600;">Payment</span>
        @elseif($tx->type == 'return')
          <span style="color:#b91c1c; font-weight:600;">Return</span>
        @elseif($tx->type == 'opening_balance')
          <span style="color:#0369a1; font-weight:600;">Opening Balance</span>
        @else
          <span>{{ ucfirst($tx->type) }}</span>
        @endif
      </p>
    </div>

    <div>
      <span>Amount</span>
      <p><strong>{{ number_format($tx->amount, 2) }} TK</strong></p>
    </div>

  

    <div>
      <span>Date</span>
      <p>{{ $tx->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
    </div>

  </div>

  <div class="card-actions">
    <a href="{{ $viewUrl }}" class="icon-btn view-icon" title="View Details">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>
</div>

@empty
<p class="text-center text-muted py-4">
  <i class="fas fa-inbox me-1"></i> No supplier transactions found.
</p>
@endforelse
