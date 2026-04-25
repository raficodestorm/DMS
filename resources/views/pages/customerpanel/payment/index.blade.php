@extends('layouts.customerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>My Transactions</h2>
    <p>View all payment and purchase records</p>
    @include('components.alert')
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Transaction ID</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody class="desktop-table">
        @forelse($payments as $payment)
        <tr>

          <td>
            {{ $payments->firstItem() ? $payments->firstItem() + $loop->index : $loop->iteration }}
          </td>

          <td>
            BRT00{{ $payment->id }}
          </td>

          <td>
            @if($payment->type == 'pay')
            <span style="color:var(--primary); font-weight:600;">Payment</span>
            @else
            <span style="color:#7c3aed; font-weight:600;">Purchase</span>
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
            <a href="{{ route('customer.payments.show', $payment->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>

        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted">
            No transaction records found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards">

    @forelse($payments as $payment)

    <div class="manage-card">

      <div class="card-body">

        <div>
          <span>S.No</span>
          <p>
            {{ $payments->firstItem() ? $payments->firstItem() + $loop->index : $loop->iteration }}
          </p>
        </div>

        <div>
          <span>Transaction ID</span>
          <p>BRT00{{ $payment->id }}</p>
        </div>

        <div>
          <span>Type</span>
          <p>
            @if($payment->type == 'pay')
            Payment
            @else
            Purchase
            @endif
          </p>
        </div>

        <div>
          <span>Amount</span>
          <p>{{ number_format($payment->amount, 2) }} TK</p>
        </div>

        <div>
          <span>Status</span>
          <p>
            @if($payment->status == 'complete')
            <span style="color:#16a34a;">● Completed</span>
            @else
            <span style="color:#f59e0b;">● Pending</span>
            @endif
          </p>
        </div>

        <div>
          <span>Date</span>
          <p>
            {{ $payment->created_at->timezone(auth()->user()->timezone)->format('d M Y') }}
          </p>
        </div>

      </div>

      <div class="card-actions">
        <a href="{{ route('customer.payments.show', $payment->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>

    </div>

    @empty
    <p class="text-center text-muted">No transaction records found.</p>
    @endforelse

  </div>

</div>

<div class="mt-3">
  {{ $payments->links() }}
</div>

@endsection