@extends('layouts.srlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Orders of {{ $customer->shop_name }}</h2>
      <p class="text-muted mb-0">Viewing all orders</p>
    </div>
    <div style="background: rgba(49,49,255,0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49,49,255,0.2);">
      <i class="fas fa-shopping-cart me-1"></i> Total: <span id="visibleCount">{{ $orders->count() }}</span>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-5">

      {{-- Search --}}
      <div>
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
        <div style="position: relative;">
          <input type="text" id="liveSearch" class="input-form" placeholder="Search by Order ID..." style="padding-left: 32px; margin-bottom: 0;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>

      {{-- Status Filter --}}
      <div>
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Status</label>
        <select id="statusFilter" class="input-form" style="margin-bottom: 0;">
          <option value="">-- All Statuses --</option>
          <option value="pending_sr">Pending SR</option>
          <option value="pending_manager">Pending Manager</option>
          <option value="approved">Approved</option>
          <option value="complete">Complete</option>
          <option value="delivered">Delivered</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      {{-- From Date --}}
      <div>
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
        <input type="date" id="fromDate" class="input-form" style="margin-bottom: 0;">
      </div>

      {{-- To Date --}}
      <div>
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
        <input type="date" id="toDate" class="input-form" style="margin-bottom: 0;">
      </div>

      {{-- Reset Button --}}
      <div>
        <label style="font-size: 0.8rem; font-weight: 600; color: transparent; margin-bottom: 4px; display: block;">Reset</label>
        <button type="button" id="resetSearchBtn" class="btn btn-outline-secondary" title="Reset Filters" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
          <i class="fas fa-undo"></i>
        </button>
      </div>

    </div>
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
      <tbody class="desktop-table" id="desktopTableBody">
        @forelse($orders as $order)
        <tr class="searchable-row" data-search="brs{{ $order->id }} {{ strtolower($order->status) }} {{ strtolower($order->customer->shop_name ?? '') }}" data-status="{{ $order->status }}" data-date="{{ $order->created_at->format('Y-m-d') }}">
          <td scope="row">{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration}}</td>
          <td>BRS{{ $order->id }}</td>
          <td>{{ $order->customer->shop_name }}</td>
          <td>{{ $order->sr->fullname }}</td>
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
            <a href="{{ route('sr.order.show', $order->id) }}" class="icon-btn view-icon">
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

  <div class="manage-mobile-cards" id="mobileCardsWrapper">
    @forelse($orders as $order)
    <div class="manage-card searchable-card" data-search="brs{{ $order->id }} {{ strtolower($order->status) }} {{ strtolower($order->customer->shop_name ?? '') }}" data-status="{{ $order->status }}" data-date="{{ $order->created_at->format('Y-m-d') }}">
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
          <p>{{ $order->sr->name ?? 'N/A' }}</p>
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
        <a href="{{ route('sr.order.show', $order->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No orders found.</p>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $orders->links() }} </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const liveSearch   = document.getElementById('liveSearch');
    const statusFilter = document.getElementById('statusFilter');
    const fromDate     = document.getElementById('fromDate');
    const toDate       = document.getElementById('toDate');
    const resetBtn     = document.getElementById('resetSearchBtn');
    const visibleCount = document.getElementById('visibleCount');
    const totalRows    = document.querySelectorAll('.searchable-row').length;

    function applyFilters() {
        const q      = liveSearch ? liveSearch.value.toLowerCase().trim() : '';
        const status = statusFilter ? statusFilter.value : '';
        const from   = fromDate ? fromDate.value : '';
        const to     = toDate ? toDate.value : '';

        let count = 0;

        // Desktop rows
        document.querySelectorAll('.searchable-row').forEach(row => {
            const text      = row.getAttribute('data-search') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            const rowDate   = row.getAttribute('data-date') || '';

            let match = true;

            if (q && !text.includes(q)) match = false;
            if (status && rowStatus !== status) match = false;
            if (from && rowDate < from) match = false;
            if (to && rowDate > to) match = false;

            row.style.display = match ? '' : 'none';
            if (match) count++;
        });

        // Mobile cards
        document.querySelectorAll('.searchable-card').forEach(card => {
            const text       = card.getAttribute('data-search') || '';
            const cardStatus = card.getAttribute('data-status') || '';
            const cardDate   = card.getAttribute('data-date') || '';

            let match = true;

            if (q && !text.includes(q)) match = false;
            if (status && cardStatus !== status) match = false;
            if (from && cardDate < from) match = false;
            if (to && cardDate > to) match = false;

            card.style.display = match ? '' : 'none';
        });

        if (visibleCount) {
            visibleCount.innerText = (q || status || from || to) ? count : totalRows;
        }
    }

    if (liveSearch)   liveSearch.addEventListener('input', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (fromDate)     fromDate.addEventListener('change', applyFilters);
    if (toDate)       toDate.addEventListener('change', applyFilters);

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (liveSearch)   liveSearch.value   = '';
            if (statusFilter) statusFilter.value = '';
            if (fromDate)     fromDate.value     = '';
            if (toDate)       toDate.value       = '';
            applyFilters();
        });
    }
});
</script>
@endpush