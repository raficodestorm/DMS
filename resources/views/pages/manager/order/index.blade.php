@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>My Orders</h2>
    <p>Manage all orders from your branch SRs</p>
    @include('components.alert')
  </div>

  {{-- Search and Filter Controls --}}
  <form id="orderFilterForm" onsubmit="return false;">
    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; background: var(--background); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color);">
      
      {{-- Search Bar --}}
      <div style="flex: 1; min-width: 200px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
        <div style="position: relative;">
          <input type="text" 
                 name="search" 
                 id="searchInput"
                 class="input-form" 
                 placeholder="Search by Order ID or Customer..." 
                 value="{{ request('search') }}"
                 style="margin-bottom: 0; padding-left: 36px; height: 42px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>

      {{-- Status Filter --}}
      <div style="min-width: 160px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Filter Status</label>
        <select name="status" id="statusFilter" class="input-form" style="padding: 5px; margin-bottom: 0; height: 42px;">
          <option value="">-- All Statuses --</option>
          <option value="pending_sr" {{ request('status') == 'pending_sr' ? 'selected' : '' }}>Pending SR</option>
          <option value="pending_manager" {{ request('status') == 'pending_manager' ? 'selected' : '' }}>Pending Manager</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
          <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
      </div>

      {{-- From Date --}}
      <div style="min-width: 140px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
        <input type="date" 
               name="from_date" 
               id="fromDate" 
               class="input-form" 
               value="{{ request('from_date') }}"
               style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- To Date --}}
      <div style="min-width: 140px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
        <input type="date" 
               name="to_date" 
               id="toDate" 
               class="input-form" 
               value="{{ request('to_date') }}"
               style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- Reset Button --}}
      <div>
        <button type="button" id="resetBtn" class="btn-submit" style="padding: 0 1rem; height: 42px; font-size: 0.85rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; background: #6c757d; color: #fff; border-radius: 8px;">
          <i class="fas fa-undo"></i> Reset
        </button>
      </div>

    </div>
  </form>

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
      <tbody class="desktop-table" id="desktopTable">
        @include('pages.manager.order.table')
      </tbody>
    </table>
  </div>
  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.manager.order.mtable')
  </div>

</div>
<div class="mt-3" id="paginationWrapper">
  {{ $orders->links() }}
</div>
@endsection

@push('scripts')
<script>
  const filterForm = document.getElementById('orderFilterForm');

  function fetchFilteredOrders() {
    const params = new URLSearchParams(new FormData(filterForm)).toString();
    fetch(`{{ route('manager.order.index') }}?${params}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      document.getElementById('desktopTable').innerHTML = data.table;
      document.getElementById('mobileTable').innerHTML = data.mobile;
      if (data.pagination) {
        document.getElementById('paginationWrapper').innerHTML = data.pagination;
      }
    });
  }

  filterForm.addEventListener('input', fetchFilteredOrders);
  filterForm.addEventListener('change', fetchFilteredOrders);

  document.getElementById('resetBtn').addEventListener('click', function () {
    filterForm.reset();
    fetchFilteredOrders();
  });
</script>
@endpush