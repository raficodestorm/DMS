@forelse($closings as $item)
<tr>
  <td>{{ $closings->firstItem() ? $closings->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>
    <div style="line-height: 1.2;">
      <span class="fw-bold text-main">{{ \Carbon\Carbon::parse($item->closing_date)->format('d M Y') }}</span>
      <span class="text-muted d-block" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($item->closing_date)->format('l') }}</span>
    </div>
  </td>
  <td class="fw-semibold" style="color: var(--primary);">
    <i class="fa-solid fa-store me-1 text-muted" style="font-size: 0.8rem;"></i>
    {{ $item->branch->name ?? 'N/A' }}
  </td>
  <td class="text-muted">৳ {{ number_format($item->opening_cash, 2) }}</td>
  <td class="text-success fw-semibold">+৳ {{ number_format($item->cash_in, 2) }}</td>
  <td class="text-danger fw-semibold">-৳ {{ number_format($item->cash_out, 2) }}</td>
  <td class="fw-bold text-main">৳ {{ number_format($item->expected_cash, 2) }}</td>
  <td class="fw-bold text-primary">৳ {{ number_format($item->actual_cash, 2) }}</td>
  <td>
    @if(abs((float)$item->cash_variance) < 0.001)
      <span class="badge" style="background: rgba(100,116,139,0.12); color: var(--text-muted); font-size: 11px; padding: 4px 8px; border-radius: 6px;">
        Balanced
      </span>
    @elseif((float)$item->cash_variance > 0)
      <span class="badge" style="background: #dcfce7; color: #15803d; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
        +৳ {{ number_format($item->cash_variance, 2) }}
      </span>
    @else
      <span class="badge" style="background: #fee2e2; color: #b91c1c; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
        -৳ {{ number_format(abs($item->cash_variance), 2) }}
      </span>
    @endif
  </td>
  <td>
    <div style="font-size: 0.82rem; line-height: 1.2;">
      <span>{{ $item->closedBy->fullname ?? $item->closedBy->username ?? 'Manager' }}</span>
      <span class="text-muted d-block" style="font-size: 0.73rem;">
        {{ $item->closed_at ? \Carbon\Carbon::parse($item->closed_at)->format('h:i A') : '-' }}
      </span>
    </div>
  </td>
  <td class="action-icons">
    <button type="button"
            class="icon-btn view-icon border-0"
            title="View Closing Breakdown"
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
      <i class="fa-solid fa-eye"></i>
    </button>
  </td>
</tr>
@empty
<tr>
  <td colspan="11" class="text-center py-4 text-muted">
    <i class="fas fa-inbox me-1"></i> No daily cash closing records found.
  </td>
</tr>
@endforelse
