@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Stock Transfer Requests</h2>
      <p class="text-muted mb-0">Manage your branch's incoming and outgoing stock transfers</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-right-left me-1"></i> Total Transfers: <span id="totalTransferCount">0</span>
      </div>
      <a href="{{ route('manager.stock-transfer.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> New Transfer Request
      </a>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-6">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="ID or Branch name..." value="{{ request('search') }}" style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>

      {{-- Transfer Type --}}
      <div>
        <label>Transfer Type</label>
        <select id="typeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="outgoing" {{ request('transfer_type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
          <option value="incoming" {{ request('transfer_type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
        </select>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Statuses --</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
      </div>

      {{-- From Date --}}
      <div>
        <label>From</label>
        <input type="date" id="fromDate" class="input-form" value="{{ request('from_date') }}">
      </div>

      {{-- To Date --}}
      <div>
        <label>To</label>
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

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
          <th>Branch</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="transferTable">
        <tr>
          <td colspan="6" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view stock transfers.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="transferMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view stock transfers.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const typeFilter        = document.getElementById('typeFilter');
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
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading stock transfers...
                    </td>
                </tr>`;
        }
        if (transferMobile) {
            transferMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading stock transfers...
                </p>`;
        }
    }

    function showErrorState() {
        if (transferTable) {
            transferTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load stock transfer data. Please try again.
                    </td>
                </tr>`;
        }
        if (transferMobile) {
            transferMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load stock transfer data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)  searchInput.value  = '';
        if (typeFilter)   typeFilter.value   = '';
        if (statusFilter) statusFilter.value = '';
        if (fromDate)     fromDate.value     = '';
        if (toDate)       toDate.value       = '';
    }

    function fetchFilteredTransfers(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const type   = encodeURIComponent(typeFilter ? typeFilter.value : '');
            const status = encodeURIComponent(statusFilter ? statusFilter.value : '');
            const from   = encodeURIComponent(fromDate ? fromDate.value : '');
            const to     = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('manager.stock-transfer.index.data') }}?search=${search}&transfer_type=${type}&status=${status}&from_date=${from}&to_date=${to}`;
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

    // Initial Load: Only fetch if search/filters/page parameters exist in URL
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
    if (typeFilter)   typeFilter.addEventListener('change',   () => fetchFilteredTransfers());
    if (statusFilter) statusFilter.addEventListener('change', () => fetchFilteredTransfers());
    if (fromDate)     fromDate.addEventListener('change',     () => fetchFilteredTransfers());
    if (toDate)       toDate.addEventListener('change',       () => fetchFilteredTransfers());

    // Reset button handler
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
