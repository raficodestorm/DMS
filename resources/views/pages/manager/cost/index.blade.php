@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Branch Cost Management</h2>
      <p class="text-muted mb-0">Record and track all branch-level expenses</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(220, 53, 69, 0.08); color: #dc3545; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(220, 53, 69, 0.2);">
        <i class="fas fa-wallet me-1"></i> Total Expenses: <span id="totalCostAmount">0.00 ৳</span>
      </div>
      <a href="{{ route('manager.costs.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> Record New Cost
      </a>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-5">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Description or Category..." style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>

      {{-- Category Filter --}}
      <div>
        <label>Category</label>
        <select id="categoryFilter" class="input-form">
          <option value="">-- All Categories --</option>
          @foreach(['office', 'transport', 'salary', 'maintenance', 'product', 'utility', 'marketing', 'miscellaneous'] as $cat)
            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>

      {{-- From Date --}}
      <div>
        <label>From Date</label>
        <input type="date" id="fromDate" class="input-form">
      </div>

      {{-- To Date --}}
      <div>
        <label>To Date</label>
        <input type="date" id="toDate" class="input-form">
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
          <th>Date</th>
          <th>Category</th>
          <th>Description</th>
          <th>Amount</th>
          <th>Recorded By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="costTable">
        <tr>
          <td colspan="7" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view cost records.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="costMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view cost records.
    </p>
  </div>

  <div id="paginationWrapper" class="mt-3"></div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput        = document.getElementById('searchInput');
    const categoryFilter     = document.getElementById('categoryFilter');
    const fromDate           = document.getElementById('fromDate');
    const toDate             = document.getElementById('toDate');
    const resetBtn           = document.getElementById('resetBtn');
    const costTable          = document.getElementById('costTable');
    const costMobile         = document.getElementById('costMobile');
    const paginationWrapper  = document.getElementById('paginationWrapper');
    const totalCostCount     = document.getElementById('totalCostCount');
    const totalCostAmount    = document.getElementById('totalCostAmount');

    let debounceTimer;

    function fetchCosts(pageUrl = null) {
        const query    = searchInput ? searchInput.value.trim() : '';
        const category = categoryFilter ? categoryFilter.value : '';
        const from     = fromDate ? fromDate.value : '';
        const to       = toDate ? toDate.value : '';

        let url = pageUrl || "{{ route('manager.costs.index.data') }}";
        const params = new URLSearchParams();

        if (query)    params.append('search', query);
        if (category) params.append('category', category);
        if (from)     params.append('from_date', from);
        if (to)       params.append('to_date', to);

        if (params.toString()) {
            url += (url.includes('?') ? '&' : '?') + params.toString();
        }

        costTable.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading cost records...
                </td>
            </tr>
        `;
        costMobile.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                Loading cost records...
            </div>
        `;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            costTable.innerHTML = data.table;
            costMobile.innerHTML = data.mobile;
            paginationWrapper.innerHTML = data.pagination || '';
            if (totalCostCount) {
                totalCostCount.innerText = data.total_count || 0;
            }
            if (totalCostAmount) {
                totalCostAmount.innerText = (data.total_amount || '0.00') + ' ৳';
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            costTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">
                        Failed to load data. Please try again.
                    </td>
                </tr>
            `;
            costMobile.innerHTML = `
                <div class="text-center text-danger py-4">
                    Failed to load data. Please try again.
                </div>
            `;
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchCosts(), 350);
        });
    }

    if (categoryFilter) categoryFilter.addEventListener('change', () => fetchCosts());
    if (fromDate)       fromDate.addEventListener('change',       () => fetchCosts());
    if (toDate)         toDate.addEventListener('change',         () => fetchCosts());

    // Initial Load: Only fetch if URL parameters exist
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchCosts();
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput)    searchInput.value    = '';
            if (categoryFilter) categoryFilter.value = '';
            if (fromDate)       fromDate.value       = '';
            if (toDate)         toDate.value         = '';
            fetchCosts();
        });
    }

    // Pagination link click handler
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationWrapper .pagination a');
        if (link) {
            e.preventDefault();
            fetchCosts(link.href);
        }
    });
});
</script>
@endpush
