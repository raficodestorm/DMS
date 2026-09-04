@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      @include('components.alert')
      <h2 class="mb-0">All Customer Accounts</h2>
      <p class="text-muted mb-0">Manage all registered Customer Accounts</p>
    </div>
    <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
      <i class="fas fa-users me-1"></i> Total Customers: <span id="totalCustomerCount">0</span>
    </div>
  </div>

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-2">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Search by Full Name, Username or Customer ID..." value="{{ request('search') }}" style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
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
          <th>Full Name</th>
          <th>Username</th>
          <th>Branch</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="customerTable">
        <tr>
          <td colspan="5" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view customer accounts.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="customerMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view customer accounts.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const resetBtn          = document.getElementById('resetBtn');

    const customerTable     = document.getElementById('customerTable');
    const customerMobile    = document.getElementById('customerMobile');
    const totalCountEl      = document.getElementById('totalCustomerCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (customerTable) {
            customerTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading customer accounts...
                    </td>
                </tr>`;
        }
        if (customerMobile) {
            customerMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading customer accounts...
                </p>`;
        }
    }

    function showErrorState() {
        if (customerTable) {
            customerTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load customer data. Please try again.
                    </td>
                </tr>`;
        }
        if (customerMobile) {
            customerMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load customer data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput) searchInput.value = '';
    }

    function fetchFilteredCustomers(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            url = `{{ route('manager.index.customers.data') }}?search=${search}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (customerTable)  customerTable.innerHTML  = data.table;
            if (customerMobile) customerMobile.innerHTML = data.mobile;
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

    // Initial Load: Only fetch if search/page parameters exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredCustomers();
    }

    // Debounce search input
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredCustomers(), 350);
        });
    }

    // Reset button handler
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredCustomers();
        });
    }

    // AJAX pagination handling
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredCustomers(link.href);
            }
        });
    }
});
</script>
@endpush