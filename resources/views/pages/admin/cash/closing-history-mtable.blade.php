@forelse($closings as $item)
<div class="manage-card">
  <div class="card-body">
    <div>
      <span>S.No</span>
      <p>{{ $closings->firstItem() ? $closings->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div>
      <span>Closing Date</span>
      <p class="fw-bold text-main">{{ \Carbon\Carbon::parse($item->closing_date)->format('d M Y') }} ({{ \Carbon\Carbon::parse($item->closing_date)->format('D') }})</p>
    </div>
    <div>
      <span>Branch</span>
      <p class="fw-bold" style="color: var(--primary);">{{ $item->branch->name ?? 'N/A' }}</p>
    </div>
    <div>
      <span>Expected Cash</span>
      <p class="fw-bold text-main">৳ {{ number_format($item->expected_cash, 2) }}</p>
    </div>
    <div>
      <span>Actual Cash</span>
      <p class="fw-bold text-primary">৳ {{ number_format($item->actual_cash, 2) }}</p>
    </div>
    <div>
      <span>Variance</span>
      <p>
        @if(abs((float)$item->cash_variance) < 0.001)
          <span style="color: var(--text-muted); font-weight: 600;">Balanced</span>
        @elseif((float)$item->cash_variance > 0)
          <span style="color: #15803d; font-weight: 700;">+৳ {{ number_format($item->cash_variance, 2) }}</span>
        @else
          <span style="color: #b91c1c; font-weight: 700;">-৳ {{ number_format(abs($item->cash_variance), 2) }}</span>
        @endif
      </p>
    </div>
    <div>
      <span>Cash In / Out</span>
      <p style="font-size: 0.85rem;">
        <span class="text-success">+৳ {{ number_format($item->cash_in, 2) }}</span> /
        <span class="text-danger">-৳ {{ number_format($item->cash_out, 2) }}</span>
      </p>
    </div>
    <div>
      <span>Closed By</span>
      <p>{{ $item->closedBy->fullname ?? $item->closedBy->username ?? 'Manager' }}</p>
    </div>
    @if(!empty($item->notes))
    <div>
      <span>Note</span>
      <p class="text-truncate" style="max-width: 180px;" title="{{ $item->notes }}">{{ $item->notes }}</p>
    </div>
    @endif
  </div>

  <div class="card-actions">
    <button type="button"
            class="icon-btn view-icon w-100 border-0"
            title="View Breakdown"
            onclick="viewClosingDetails({{ json_encode([
              'date'          => \Carbon\Carbon::parse($item->closing_date)->format('l, d F Y'),
              'branch'        => $item->branch->name ?? 'N/A',
              'opening_cash'  => '৳ ' . number_format($item->opening_cash, 2),
              'cash_in'       => '৳ ' . number_format($item->cash_in, 2),
              'cash_out'      => '৳ ' . number_format($item->cash_out, 2),
              'expected_cash' => '৳ ' . number_format($item->expected_cash, 2),
              'actual_cash'   => '৳ ' . number_format($item->actual_cash, 2),
              'cash_variance' => ((float)$item->cash_variance > 0 ? '+৳ ' : ((float)$item->cash_variance < 0 ? '-৳ ' : '৳ ')) . number_format(abs($item->cash_variance), 2),
              'variance_raw'  => (float)$item->cash_variance,
              'status'        => $item->status,
              'closed_by'     => $item->closedBy->fullname ?? $item->closedBy->username ?? 'Manager',
              'closed_at'     => $item->closed_at ? \Carbon\Carbon::parse($item->closed_at)->format('d M Y, h:i A') : '-',
              'notes'         => $item->notes ?: 'No note recorded.',
            ]) }})">
      <i class="fa-solid fa-eye me-1"></i> View Breakdown
    </button>
  </div>
</div>
@empty
<div class="text-center text-muted py-4">
  <i class="fas fa-inbox me-1"></i> No daily cash closing records found.
</div>
@endforelse
