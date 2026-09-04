@forelse($payments as $payment)
<tr>

  <td>
    {{ $payments->firstItem() ? $payments->firstItem() + $loop->index : $loop->iteration }}
  </td>

  <td>
    BRT00{{ $payment->id }}
  </td>

  <td>
    {{ $payment->customer?->shop_name ?? 'N/A' }}
  </td>

  <td>
    @if($payment->type == 'pay')

    <span style="color:var(--primary); font-weight:600;">
      Payment
    </span>

    @elseif($payment->type == 'return')

    <span style="color:#dc2626; font-weight:600;">
      Return
    </span>

    @else

    <span style="color:#7c3aed; font-weight:600;">
      Purchase
    </span>

    @endif
  </td>

  <td>
    <strong>{{ number_format($payment->amount, 2) }} TK</strong>
  </td>

  <td>
    @if($payment->status == 'complete')

    <span class="status-active-badge" style="background:#ecfdf5; color:#15803d; border:1px solid #bbf7d0;">
      ● Completed
    </span>

    @else

    <span class="status-inactive-badge" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a;">
      ● Pending
    </span>

    @endif
  </td>

  <td>
    {{ $payment->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
  </td>

  <td class="action-icons">
    <a href="{{ route('admin.payments.show', $payment->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>

</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted">
    No transaction records found.
  </td>
</tr>
@endforelse