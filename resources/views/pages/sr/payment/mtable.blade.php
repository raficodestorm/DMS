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
      <span>Customer</span>
      <p>{{ $payment->customer->shop_name }}</p>
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
    <a href="{{ route('sr.payments.show', $payment->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>

</div>

@empty
<p class="text-center text-muted">No transaction records found.</p>
@endforelse