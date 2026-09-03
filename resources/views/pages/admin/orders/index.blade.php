@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">All Orders</h2>
      <p class="text-muted mb-0">Manage all registered orders</p>
    </div>
    
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <div class="row g-2 align-items-end">

      {{-- Search Input --}}
      <div class="col-12 col-md-4 col-lg-3">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
        <div style="position: relative;">
          <input type="text" id="search" class="input-form" placeholder="Search by Order ID or Shop Name..." value="{{ request('search') }}" style="margin-bottom: 0; padding-left: 35px; height: 42px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>

      {{-- Branch Filter --}}
      <div class="col-6 col-md-3 col-lg-2">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Branch</label>
        <select id="branchFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
          <option value="">-- All Branches --</option>
          @foreach($branches as $b)
          <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Status Filter --}}
      <div class="col-6 col-md-2 col-lg-2">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Status</label>
        <select id="statusFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
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
      <div class="col-6 col-md-3 col-lg-2">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
        <input type="date" id="fromDate" class="input-form" value="{{ request('from_date') }}" style="margin-bottom: 0; height: 42px; padding: 5px 10px;">
      </div>

      {{-- To Date --}}
      <div class="col-6 col-md-3 col-lg-2">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
        <input type="date" id="toDate" class="input-form" value="{{ request('to_date') }}" style="margin-bottom: 0; height: 42px; padding: 5px 10px;">
      </div>

      {{-- Reset Button --}}
      <div class="col-12 col-md-1 col-lg-1">
        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100" title="Reset Filters" style="height: 42px; display: inline-flex; align-items: center; justify-content: center;">
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
          <th>Branch</th>
          <th>Reference</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        @include('pages.admin.orders.table')
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.admin.orders.mtable')
  </div>

</div>

<div class="mt-3" id="paginationWrapper">
  {{ $orders->links() }}
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('search');
    const branchFilter = document.getElementById('branchFilter');
    const statusFilter = document.getElementById('statusFilter');
    const fromDate     = document.getElementById('fromDate');
    const toDate       = document.getElementById('toDate');
    const resetBtn     = document.getElementById('resetBtn');

    function fetchFilteredOrders() {
        const query  = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
        const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');
        const status = encodeURIComponent(statusFilter ? statusFilter.value : '');
        const from   = encodeURIComponent(fromDate ? fromDate.value : '');
        const to     = encodeURIComponent(toDate ? toDate.value : '');

        const url = `{{ route('admin.order.index') }}?search=${query}&branch_id=${branch}&status=${status}&from_date=${from}&to_date=${to}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('desktopTable').innerHTML = data.table;
            document.getElementById('mobileTable').innerHTML = data.mobile;
            if (document.getElementById('totalOrderCount') && data.total !== undefined) {
                document.getElementById('totalOrderCount').innerText = data.total;
            }
            if (document.getElementById('paginationWrapper') && data.pagination !== undefined) {
                document.getElementById('paginationWrapper').innerHTML = data.pagination;
            }
        })
        .catch(err => console.error('Filter fetch error:', err));
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchFilteredOrders, 350);
        });
    }

    if (branchFilter) branchFilter.addEventListener('change', fetchFilteredOrders);
    if (statusFilter) statusFilter.addEventListener('change', fetchFilteredOrders);
    if (fromDate)     fromDate.addEventListener('change', fetchFilteredOrders);
    if (toDate)       toDate.addEventListener('change', fetchFilteredOrders);

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput)  searchInput.value  = '';
            if (branchFilter) branchFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (fromDate)     fromDate.value     = '';
            if (toDate)       toDate.value       = '';
            fetchFilteredOrders();
        });
    }
});
</script>
@endpush