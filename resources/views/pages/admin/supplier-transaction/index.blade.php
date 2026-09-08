@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Supplier Ledger</h2>
      <p class="text-muted mb-0">View all supplier purchase, payment &amp; return ledger entries</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-file-invoice-dollar me-1"></i> Total Entries: <span id="totalTxCount">0</span>
      </div>
      <a href="{{ route('admin.supplier-transactions.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> New Supplier Payment
      </a>
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
          <input type="text" id="searchInput" class="input-form" placeholder="ID, Supplier, Branch..." value="{{ request('search') }}" style="padding-left: 30px;">
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

      {{-- Supplier Filter --}}
      <div>
        <label>Supplier</label>
        <select id="supplierFilter" class="input-form">
          <option value="">-- All Suppliers --</option>
          @foreach($suppliers as $s)
          <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->company_name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Type Filter --}}
      <div>
        <label>Type</label>
        <select id="typeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>Purchase (Buy)</option>
          <option value="pay" {{ request('type') == 'pay' ? 'selected' : '' }}>Payment</option>
          <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
          <option value="opening_balance" {{ request('type') == 'opening_balance' ? 'selected' : '' }}>Opening Balance</option>
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
        <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters &amp; Show All"
          style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; margin-top: 22px;">
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
          <th>Txn ID</th>
          <th>Supplier</th>
          <th>Branch</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Date &amp; Time</th>
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
    const searchInput    = document.getElementById('searchInput');
    const branchFilter   = document.getElementById('branchFilter');
    const supplierFilter = document.getElementById('supplierFilter');
    const typeFilter     = document.getElementById('typeFilter');
    const fromDate       = document.getElementById('fromDate');
    const toDate         = document.getElementById('toDate');
    const resetBtn       = document.getElementById('resetBtn');

    const desktopTable      = document.getElementById('desktopTable');
    const mobileTable       = document.getElementById('mobileTable');
    const totalCountEl      = document.getElementById('totalTxCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoading() {
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

    function showError() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load data.
                </p>`;
        }
    }

    function clearFilters() {
        if (searchInput)    searchInput.value    = '';
        if (branchFilter)   branchFilter.value   = '';
        if (supplierFilter) supplierFilter.value = '';
        if (typeFilter)     typeFilter.value     = '';
        if (fromDate)       fromDate.value       = '';
        if (toDate)         toDate.value         = '';
    }

    function fetchTransactions(fetchUrl = null) {
        showLoading();

        let url = fetchUrl;
        if (!url) {
            const search   = encodeURIComponent(searchInput    ? searchInput.value.trim() : '');
            const branch   = encodeURIComponent(branchFilter   ? branchFilter.value       : '');
            const supplier = encodeURIComponent(supplierFilter ? supplierFilter.value     : '');
            const type     = encodeURIComponent(typeFilter     ? typeFilter.value         : '');
            const from     = encodeURIComponent(fromDate       ? fromDate.value           : '');
            const to       = encodeURIComponent(toDate         ? toDate.value             : '');

            url = `{{ route('admin.supplier-transactions.index.data') }}?search=${search}&branch_id=${branch}&supplier_id=${supplier}&type=${type}&from_date=${from}&to_date=${to}`;
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
            showError();
        });
    }

    // Initial Load: Only fetch if filter params exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchTransactions();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchTransactions(), 450);
        });
    }

    if (branchFilter)   branchFilter.addEventListener('change',   () => fetchTransactions());
    if (supplierFilter) supplierFilter.addEventListener('change', () => fetchTransactions());
    if (typeFilter)     typeFilter.addEventListener('change',     () => fetchTransactions());
    if (fromDate)       fromDate.addEventListener('change',       () => fetchTransactions());
    if (toDate)         toDate.addEventListener('change',         () => fetchTransactions());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearFilters();
            fetchTransactions();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchTransactions(link.href);
            }
        });
    }
});
</script>
@endpush
