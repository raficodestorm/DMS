@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Bonus Management</h2>
      <p class="text-muted mb-0">Track additional income, incentives, and cashback</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-coins me-1"></i> Total Bonus: <span id="totalBonusAmount">0.00 ৳</span>
      </div>
      <a href="{{ route('admin.bonuses.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> Add Bonus
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
          <input type="text" id="searchInput" class="input-form" placeholder="Search by Title..." value="{{ request('search') }}" style="padding-left: 30px;">
          <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
        </div>
      </div>

      {{-- Type Filter --}}
      <div>
        <label>Type</label>
        <select id="typeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="incentive" {{ request('type') == 'incentive' ? 'selected' : '' }}>Incentive</option>
          <option value="cashback" {{ request('type') == 'cashback' ? 'selected' : '' }}>Cashback</option>
          <option value="special" {{ request('type') == 'special' ? 'selected' : '' }}>Special</option>
          <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
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
          <th>Title</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Created By</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="6" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view bonus entries.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view bonus entries.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const typeFilter   = document.getElementById('typeFilter');
    const monthFilter  = document.getElementById('monthFilter');
    const yearFilter   = document.getElementById('yearFilter');
    const fromDate     = document.getElementById('fromDate');
    const toDate       = document.getElementById('toDate');
    const resetBtn     = document.getElementById('resetBtn');

    const desktopTable      = document.getElementById('desktopTable');
    const mobileTable       = document.getElementById('mobileTable');
    const totalAmountEl     = document.getElementById('totalBonusAmount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading bonus entries...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading bonus entries...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load bonus data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load bonus data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput) searchInput.value = '';
        if (typeFilter)  typeFilter.value  = '';
        if (monthFilter) monthFilter.value = '';
        if (yearFilter)  yearFilter.value  = '';
        if (fromDate)    fromDate.value    = '';
        if (toDate)      toDate.value      = '';
    }

    function fetchFilteredBonuses(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const type   = encodeURIComponent(typeFilter ? typeFilter.value : '');
            const month  = encodeURIComponent(monthFilter ? monthFilter.value : '');
            const year   = encodeURIComponent(yearFilter ? yearFilter.value : '');
            const from   = encodeURIComponent(fromDate ? fromDate.value : '');
            const to     = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.bonuses.index.data') }}?search=${search}&type=${type}&month=${month}&year=${year}&from_date=${from}&to_date=${to}`;
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
            if (totalAmountEl && data.totalBonus !== undefined) {
                totalAmountEl.innerText = data.totalBonus;
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
        fetchFilteredBonuses();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredBonuses(), 450);
        });
    }

    if (typeFilter)  typeFilter.addEventListener('change',  () => fetchFilteredBonuses());
    if (monthFilter) monthFilter.addEventListener('change', () => fetchFilteredBonuses());
    if (yearFilter)  yearFilter.addEventListener('change',  () => fetchFilteredBonuses());
    if (fromDate)    fromDate.addEventListener('change',    () => fetchFilteredBonuses());
    if (toDate)      toDate.addEventListener('change',      () => fetchFilteredBonuses());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredBonuses();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredBonuses(link.href);
            }
        });
    }
});
</script>
@endpush
