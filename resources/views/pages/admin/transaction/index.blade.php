@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">All Transactions</h2>
      <p class="text-muted mb-0">View all payment, return and purchase records</p>
    </div>
    <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
      <i class="fas fa-receipt me-1"></i> Total Transactions: <span id="totalTransactionCount">0</span>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-7">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="ID, Customer or SR..." value="{{ request('search') }}" style="padding-left: 30px;">
          <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
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

      {{-- Type Filter --}}
      <div>
        <label>Type</label>
        <select id="typeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="pay" {{ request('type') == 'pay' ? 'selected' : '' }}>Payment</option>
          <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>Purchase</option>
          <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
          <option value="opening_balance" {{ request('type') == 'opening_balance' ? 'selected' : '' }}>Opening Balance</option>
        </select>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Statuses --</option>
          <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Completed</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
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

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Transaction ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view transactions.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view transactions.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('searchInput');
    const branchFilter  = document.getElementById('branchFilter');
    const typeFilter    = document.getElementById('typeFilter');
    const statusFilter  = document.getElementById('statusFilter');
    const fromDate      = document.getElementById('fromDate');
    const toDate        = document.getElementById('toDate');
    const resetBtn      = document.getElementById('resetBtn');

    const desktopTable      = document.getElementById('desktopTable');
    const mobileTable       = document.getElementById('mobileTable');
    const totalCountEl      = document.getElementById('totalTransactionCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading transactions...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading transactions...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load transaction data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load transaction data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)  searchInput.value  = '';
        if (branchFilter) branchFilter.value = '';
        if (typeFilter)   typeFilter.value   = '';
        if (statusFilter) statusFilter.value = '';
        if (fromDate)     fromDate.value     = '';
        if (toDate)       toDate.value       = '';
    }

    function fetchFilteredTransactions(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');
            const type   = encodeURIComponent(typeFilter ? typeFilter.value : '');
            const status = encodeURIComponent(statusFilter ? statusFilter.value : '');
            const from   = encodeURIComponent(fromDate ? fromDate.value : '');
            const to     = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.payments.index.data') }}?search=${search}&branch_id=${branch}&type=${type}&status=${status}&from_date=${from}&to_date=${to}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (desktopTable) desktopTable.innerHTML = data.table;
            if (mobileTable)  mobileTable.innerHTML  = data.mobile;
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

    // Initial Load: Only fetch if filters or page parameter exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredTransactions();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredTransactions(), 450);
        });
    }

    if (branchFilter) branchFilter.addEventListener('change', () => fetchFilteredTransactions());
    if (typeFilter)   typeFilter.addEventListener('change',   () => fetchFilteredTransactions());
    if (statusFilter) statusFilter.addEventListener('change', () => fetchFilteredTransactions());
    if (fromDate)     fromDate.addEventListener('change',     () => fetchFilteredTransactions());
    if (toDate)       toDate.addEventListener('change',       () => fetchFilteredTransactions());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredTransactions();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredTransactions(link.href);
            }
        });
    }
});
</script>
@endpush