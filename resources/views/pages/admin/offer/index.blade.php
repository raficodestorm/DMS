@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  {{-- Card Header --}}
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Offer Management</h2>
      <p class="text-muted mb-0">Track and manage product discounts and promotional campaigns</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(16, 185, 129, 0.25);">
        <i class="fas fa-tags me-1"></i> Active Offers: <span id="activeOffersCount">{{ $activeOffers ?? 0 }}</span>
      </div>
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-layer-group me-1"></i> Total: <span id="totalOffersCount">{{ $totalOffers ?? 0 }}</span>
      </div>
      <a href="{{ route('admin.offers.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> Add Offer
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
          <input type="text" id="searchInput" class="input-form" placeholder="Search offer or product..." value="{{ request('search') }}" style="padding-left: 30px;">
          <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
        </div>
      </div>

      {{-- Customer Type Filter --}}
      <div>
        <label>Customer Type</label>
        <select id="customerTypeFilter" class="input-form">
          <option value="">-- All Customers --</option>
          <option value="retail" {{ request('customer_type') == 'retail' ? 'selected' : '' }}>Retail</option>
          <option value="wholesale" {{ request('customer_type') == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
        </select>
      </div>

      {{-- Offer Type Filter --}}
      <div>
        <label>Offer Type</label>
        <select id="typeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
          <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (TK)</option>
        </select>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Status --</option>
          <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
          <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
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

  {{-- Desktop Table --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Offer Name</th>
          <th>Applied Product</th>
          <th>Customer Type</th>
          <th>Discount</th>
          <th>Validity</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view offer entries.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view offer entries.
    </p>
  </div>

</div>

{{-- Pagination --}}
<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput        = document.getElementById('searchInput');
    const customerTypeFilter  = document.getElementById('customerTypeFilter');
    const typeFilter          = document.getElementById('typeFilter');
    const statusFilter        = document.getElementById('statusFilter');
    const monthFilter         = document.getElementById('monthFilter');
    const fromDate            = document.getElementById('fromDate');
    const toDate              = document.getElementById('toDate');
    const resetBtn            = document.getElementById('resetBtn');

    const desktopTable       = document.getElementById('desktopTable');
    const mobileTable        = document.getElementById('mobileTable');
    const activeOffersCount  = document.getElementById('activeOffersCount');
    const totalOffersCount   = document.getElementById('totalOffersCount');
    const paginationWrapper  = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading offers...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading offers...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load offer data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load offer data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)        searchInput.value        = '';
        if (customerTypeFilter) customerTypeFilter.value = '';
        if (typeFilter)         typeFilter.value         = '';
        if (statusFilter)       statusFilter.value       = '';
        if (monthFilter)        monthFilter.value        = '';
        if (fromDate)           fromDate.value           = '';
        if (toDate)             toDate.value             = '';
    }

    function fetchFilteredOffers(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search       = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const customerType = encodeURIComponent(customerTypeFilter ? customerTypeFilter.value : '');
            const type         = encodeURIComponent(typeFilter ? typeFilter.value : '');
            const status       = encodeURIComponent(statusFilter ? statusFilter.value : '');
            const month        = encodeURIComponent(monthFilter ? monthFilter.value : '');
            const from         = encodeURIComponent(fromDate ? fromDate.value : '');
            const to           = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.offers.index.data') }}?search=${search}&customer_type=${customerType}&type=${type}&status=${status}&month=${month}&from_date=${from}&to_date=${to}`;
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
            if (activeOffersCount && data.activeOffers !== undefined) {
                activeOffersCount.innerText = data.activeOffers;
            }
            if (totalOffersCount && data.totalOffers !== undefined) {
                totalOffersCount.innerText = data.totalOffers;
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
        fetchFilteredOffers();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredOffers(), 400);
        });
    }

    if (customerTypeFilter) customerTypeFilter.addEventListener('change', () => fetchFilteredOffers());
    if (typeFilter)         typeFilter.addEventListener('change',         () => fetchFilteredOffers());
    if (statusFilter)       statusFilter.addEventListener('change',       () => fetchFilteredOffers());
    if (monthFilter)        monthFilter.addEventListener('change',        () => fetchFilteredOffers());
    if (fromDate)           fromDate.addEventListener('change',           () => fetchFilteredOffers());
    if (toDate)             toDate.addEventListener('change',             () => fetchFilteredOffers());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredOffers();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredOffers(link.href);
            }
        });
    }
});
</script>
@endpush