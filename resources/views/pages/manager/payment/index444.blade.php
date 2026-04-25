@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>My Payment Collections</h2>
    <p>List of all payment requests submitted by you</p>
    @include('components.alert')
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Customer / Shop</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($payments as $payment)
        <tr>
          <td scope="row">
            {{ $payments->firstItem() ? $payments->firstItem() + $loop->index : $loop->iteration }}
          </td>
          <td class="name">
            {{ $payment->customer->shop_name ?? 'N/A' }}
            <br>
            <small style="color: #666;">{{ $payment->customer->name ?? '' }}</small>
          </td>
          <td><strong>{{ number_format($payment->amount, 2) }} TK</strong></td>
          <td>
            @if($payment->status == 'complete')
            <span class="status-active-badge" style="background: #e6fffa; color: #2d6a4f; border: 1px solid #b7e4c7;">
              ● Complete
            </span>
            @else
            <span class="status-inactive-badge" style="background: #fffbe6; color: #856404; border: 1px solid #ffeeba;">
              ● Pending
            </span>
            @endif
          </td>
          <td>{{ $payment->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</td>

          <td class="action-icons">
            <a href="{{ route('payments.show', $payment->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted">No payment records found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile View Cards --}}
  <div class="manage-mobile-cards">
    @forelse($payments as $payment)
    <div class="manage-card">
      <div class="card-body">
        <div><span>S.No</span>
          <p>{{ $payments->firstItem() ? $payments->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Shop</span>
          <p><strong>{{ $payment->customer->shop_name ?? 'N/A' }}</strong></p>
        </div>
        <div><span>Amount</span>
          <p>{{ number_format($payment->amount, 2) }} TK</p>
        </div>
        <div><span>Status</span>
          <p>
            @if($payment->status == 'complete')
            <span style="color:green;">● Complete</span>
            @else
            <span style="color:orange;">● Pending</span>
            @endif
          </p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('payments.show', $payment->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No records found.</p>
    @endforelse
  </div>
</div>

<div class="d-flex justify-content-center mt-3">
  {{ $payments->links() }}
</div>
@endsection