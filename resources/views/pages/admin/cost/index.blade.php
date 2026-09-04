@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Company Global Costs</h2>
      <p class="text-muted mb-0">Record and track all global company-wide expenses</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(239, 68, 68, 0.08); color: #ef4444; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(239, 68, 68, 0.2);">
        <i class="fas fa-wallet me-1"></i> Total Cost: <span id="totalCostAmount">0.00 ৳</span>
      </div>
      <a href="{{ route('admin.company_costs.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> Record Global Cost
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
          <input type="text" id="searchInput" class="input-form" placeholder="Search description..." value="{{ request('search') }}" style="padding-left: 30px;">
          <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
        </div>
      </div>

      {{-- Category Filter --}}
      <div>
        <label>Category</label>
        <select id="categoryFilter" class="input-form">
          <option value="">-- All Categories --</option>
          @foreach(['office', 'transport', 'staff', 'maintenance', 'salary', 'product', 'utility', 'marketing', 'miscellaneous'] as $cat)
          <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>

      {{-- Month Filter --}}
      <div>
        <label>Month</label>
        <select id="monthFilter" class="input-form">
          <option value="">-- All Months --</option>
          @for($m=1; $m<=12; $m++)
          <option value="{{ sprintf('%02d', $m) }}" {{ request('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
          </option>
          @endfor
        </select>
      </div>

      {{-- Year Filter --}}
      <div>
        <label>Year</label>
        <select id="yearFilter" class="input-form">
          <option value="">-- All Years --</option>
          @for($y=date('Y'); $y>=2020; $y--)
          <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
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
          <th>Date</th>
          <th>Category</th>
          <th>Description</th>
          <th>Amount</th>
          <th>Recorded By</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="6" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view expense records.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view expense records.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput    = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const monthFilter    = document.getElementById('monthFilter');
    const yearFilter     = document.getElementById('yearFilter');
    const fromDate       = document.getElementById('fromDate');
    const toDate         = document.getElementById('toDate');
    const resetBtn       = document.getElementById('resetBtn');

    const desktopTable      = document.getElementById('desktopTable');
    const mobileTable       = document.getElementById('mobileTable');
    const totalAmountEl     = document.getElementById('totalCostAmount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading expense records...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading expense records...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load expense data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load expense data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)    searchInput.value    = '';
        if (categoryFilter) categoryFilter.value = '';
        if (monthFilter)    monthFilter.value    = '';
        if (yearFilter)     yearFilter.value     = '';
        if (fromDate)       fromDate.value       = '';
        if (toDate)         toDate.value         = '';
    }

    function fetchFilteredCosts(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search   = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const category = encodeURIComponent(categoryFilter ? categoryFilter.value : '');
            const month    = encodeURIComponent(monthFilter ? monthFilter.value : '');
            const year     = encodeURIComponent(yearFilter ? yearFilter.value : '');
            const from     = encodeURIComponent(fromDate ? fromDate.value : '');
            const to       = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.company_costs.index.data') }}?search=${search}&category=${category}&month=${month}&year=${year}&from_date=${from}&to_date=${to}`;
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
            if (totalAmountEl && data.totalCost !== undefined) {
                totalAmountEl.innerText = data.totalCost;
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
        fetchFilteredCosts();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredCosts(), 450);
        });
    }

    if (categoryFilter) categoryFilter.addEventListener('change', () => fetchFilteredCosts());
    if (monthFilter)    monthFilter.addEventListener('change',    () => fetchFilteredCosts());
    if (yearFilter)     yearFilter.addEventListener('change',     () => fetchFilteredCosts());
    if (fromDate)       fromDate.addEventListener('change',       () => fetchFilteredCosts());
    if (toDate)         toDate.addEventListener('change',         () => fetchFilteredCosts());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredCosts();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredCosts(link.href);
            }
        });
    }
});
</script>
@endpush