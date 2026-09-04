@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      @if(isset($sr))
      <h2 class="mb-0">Orders of {{ $sr->fullname }}</h2>
      @elseif(isset($customer))
      <h2 class="mb-0">Orders of {{ $customer->shop_name }}</h2>
      @elseif(isset($branch))
      <h2 class="mb-0">Orders of {{ $branch->name }} branch</h2>
      @endif
      <p class="text-muted mb-0">Viewing all orders</p>
    </div>
  </div>

  {{-- Smart Search Bar --}}
  <div class="smart-filter-wrapper my-3" style="background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <form method="GET" action="{{ url()->current() }}" id="searchForm" class="row g-2 align-items-center">
      <div class="col-12 col-md-9">
        <div style="position: relative;">
          @if(isset($sr))
            <input type="text" name="search" id="searchInput" class="input-form" placeholder="Search by Customer shop name or Order ID..." value="{{ request('search') }}" style="padding-left: 35px; height: 42px; margin-bottom: 0;">
          @elseif(isset($customer))
            <input type="text" name="search" id="searchInput" class="input-form" placeholder="Search by SR name or Order ID..." value="{{ request('search') }}" style="padding-left: 35px; height: 42px; margin-bottom: 0;">
          @else
            <input type="text" name="search" id="searchInput" class="input-form" placeholder="Search by Customer shop name, SR name or Order ID..." value="{{ request('search') }}" style="padding-left: 35px; height: 42px; margin-bottom: 0;">
          @endif
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>
      <div class="col-12 col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
          <i class="fas fa-search"></i> Search
        </button>
        <a href="{{ url()->current() }}" id="resetBtn" class="btn btn-outline-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; width: 42px; padding: 0;" title="Reset Search">
          <i class="fas fa-undo"></i>
        </a>
      </div>
    </form>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Reference</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($orders as $order)
        <tr>
          <td scope="row">{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration}}</td>
          <td>BRS{{ $order->id }}</td>
          <td>{{ $order->customer->shop_name ?? 'N/A' }}</td>
          <td>{{ $order->sr->fullname ?? ($order->sr->username ?? 'N/A') }}</td>
          <td>{{ number_format($order->net_total, 2) }} TK</td>
          <td>
            @if($order->status == "pending_sr")
            <span class="status-pending-badge">Pending..SR..</span>
            @elseif($order->status == 'pending_manager')
            <span class="status-pmanager-badge">Pending..Manager..</span>
            @elseif($order->status == 'rejected')
            <span class="status-rejected-badge">Rejected</span>
            @elseif($order->status == 'complete')
            <span class="status-complete-badge">Complete</span>
            @elseif($order->status == 'delivered')
            <span class="status-delivered-badge">Delivered</span>
            @elseif($order->status == 'approved')
            <span class="status-approved-badge">Approved</span>
            @else
            <span class="status-undefined-badge">Undefined</span>
            @endif
          </td>
          <td>{{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</td>
          <td class="action-icons">
            <a href="{{ route('manager.order.show', $order->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No orders found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($orders as $order)
    <div class="manage-card">
      <div class="card-body">
        <div><span>S.No</span>
          <p>{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Order ID</span>
          <p>BRS{{ $order->id }}</p>
        </div>
        <div><span>Customer</span>
          <p>{{ $order->customer->shop_name ?? 'N/A' }}</p>
        </div>
        <div><span>Reference</span>
          <p>{{ $order->sr->fullname ?? ($order->sr->username ?? 'N/A') }}</p>
        </div>
        <div><span>Status</span>
          <p>
            @if($order->status == "pending_sr")
            <span style="color:#d39e00;">Pending..SR..</span>

            @elseif($order->status == "pending_manager")
            <span style="color:#1d4ed8;">Pending..Manager..</span>

            @elseif($order->status == "rejected")
            <span style="color:#dc3545;">● Rejected</span>

            @elseif($order->status == "cancelled")
            <span style="color:#dc3545;">● Cancelled</span>

            @elseif($order->status == "approved")
            <span style="color:#16a34a;">● Approved</span>

            @elseif($order->status == "complete")
            <span style="color:#6d28d9;">● Complete</span>

            @elseif($order->status == "delivered")
            <span style="color:#15803d;">● Delivered</span>

            @else
            <span style="color:#6b7280;">Undefined</span>
            @endif
          </p>
        </div>
      </div>
      <div class="card-actions">
        <a href="{{ route('manager.order.show', $order->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No orders found.</p>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $orders->links() }}
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const query = searchInput.value.toLowerCase().trim();

            const tableRows = document.querySelectorAll('.desktop-table tr');
            tableRows.forEach(row => {
                if (row.querySelector('td[colspan]')) return;
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });

            const mobileCards = document.querySelectorAll('.manage-mobile-cards > .manage-card');
            mobileCards.forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush