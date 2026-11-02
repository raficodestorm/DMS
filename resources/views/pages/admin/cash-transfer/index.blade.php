@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  {{-- Card Header --}}
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Cash Transfer Requests</h2>
      <p class="text-muted mb-0">Review and manage branch to central admin cash transfer requests</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-money-bill-transfer me-1"></i> Total Transfers: <span id="totalTransferCount">0</span>
      </div>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar (Admin Grid with Branch Filter) --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-6">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Search ID or note..." value="{{ request('search') }}" style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>

      {{-- Branch Filter --}}
      <div>
        <label>Branch</label>
        <select id="branchFilter" class="input-form">
          <option value="">-- All Branches --</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Statuses --</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
      </div>

      {{-- From Date --}}
      <div>
        <label>From Date</label>
        <input type="date" id="fromDate" class="input-form" value="{{ request('from_date') }}">
      </div>

      {{-- To Date --}}
      <div>
        <label>To Date</label>
        <input type="date" id="toDate" class="input-form" value="{{ request('to_date') }}">
      </div>

      {{-- Reset Button --}}
      <div>
        <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show All" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
          <i class="fas fa-undo"></i>
        </button>
      </div>

    </div>
  </div>

  {{-- Desktop Table View --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Transfer ID</th>
          <th>Branch</th>
          <th>Requested By</th>
          <th>Amount</th>
          <th>Cash Before</th>
          <th>Cash After</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="transferTable">
        <tr>
          <td colspan="10" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view cash transfers.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards View --}}
  <div class="manage-mobile-cards" id="transferMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view cash transfers.
    </p>
  </div>

</div>

{{-- Dynamic Pagination --}}
<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const branchFilter      = document.getElementById('branchFilter');
    const statusFilter      = document.getElementById('statusFilter');
    const fromDate          = document.getElementById('fromDate');
    const toDate            = document.getElementById('toDate');
    const resetBtn          = document.getElementById('resetBtn');

    const transferTable     = document.getElementById('transferTable');
    const transferMobile    = document.getElementById('transferMobile');
    const totalCountEl      = document.getElementById('totalTransferCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (transferTable) {
            transferTable.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading cash transfers...
                    </td>
                </tr>`;
        }
        if (transferMobile) {
            transferMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading cash transfers...
                </p>`;
        }
    }

    function showErrorState() {
        if (transferTable) {
            transferTable.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load cash transfer records. Please try again.
                    </td>
                </tr>`;
        }
        if (transferMobile) {
            transferMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load cash transfer records.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)  searchInput.value  = '';
        if (branchFilter) branchFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (fromDate)     fromDate.value     = '';
        if (toDate)       toDate.value       = '';
    }

    function fetchFilteredTransfers(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');
            const status = encodeURIComponent(statusFilter ? statusFilter.value : '');
            const from   = encodeURIComponent(fromDate ? fromDate.value : '');
            const to     = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.cash-transfers.data') }}?search=${search}&branch_id=${branch}&status=${status}&from_date=${from}&to_date=${to}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (transferTable)  transferTable.innerHTML  = data.table;
            if (transferMobile) transferMobile.innerHTML = data.mobile;
            if (totalCountEl && data.total !== undefined) {
                totalCountEl.innerText = data.total;
            }
            if (paginationWrapper && data.pagination !== undefined) {
                paginationWrapper.innerHTML = data.pagination;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showErrorState();
        });
    }

    // Initial Load: Only fetch if parameters exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredTransfers();
    }

    // Debounce search input
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredTransfers(), 350);
        });
    }

    // Filter change listeners
    if (branchFilter) branchFilter.addEventListener('change', () => fetchFilteredTransfers());
    if (statusFilter) statusFilter.addEventListener('change', () => fetchFilteredTransfers());
    if (fromDate)     fromDate.addEventListener('change',     () => fetchFilteredTransfers());
    if (toDate)       toDate.addEventListener('change',       () => fetchFilteredTransfers());

    // Reset button listener
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredTransfers();
        });
    }

    // AJAX pagination handling
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredTransfers(link.href);
            }
        });
    }
});
</script>
@endpush
