@extends('layouts.srlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">All Transactions</h2>
      <p class="text-muted mb-0">View all payment and purchase records of your customers</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-receipt me-1"></i> Total: <span id="totalPaymentCount">0</span>
      </div>
      <a href="{{ route('sr.payments.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> New Payment
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
          <input type="text" id="searchInput" class="input-form" placeholder="BRT ID or Customer name..." style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>

      {{-- Type Filter --}}
      <div>
        <label>Type</label>
        <select id="typeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="pay">Payment</option>
          <option value="buy">Purchase</option>
          <option value="return">Return</option>
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
          <th>Transaction ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="paymentTable">
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <div class="d-flex flex-column align-items-center justify-content-center gap-2">
              <div><i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click below to view transactions.</div>
              <button type="button" class="btn btn-sm btn-primary see-all-btn d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1 mt-1" style="border-radius: 6px; font-weight: 500; width: auto !important; max-width: max-content; margin: 0 auto;">
                <i class="fas fa-eye"></i> See All
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="paymentMobile">
    <div class="text-center text-muted py-5 d-flex flex-column align-items-center justify-content-center gap-2">
      <div><i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click below to view transactions.</div>
      <button type="button" class="btn btn-sm btn-primary see-all-btn d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1 mt-1" style="border-radius: 6px; font-weight: 500; width: auto !important; max-width: max-content; margin: 0 auto;">
        <i class="fas fa-eye"></i> See All
      </button>
    </div>
  </div>

  <div id="paginationWrapper" class="mt-3"></div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput        = document.getElementById('searchInput');
    const typeFilter         = document.getElementById('typeFilter');
    const fromDate           = document.getElementById('fromDate');
    const toDate             = document.getElementById('toDate');
    const resetBtn           = document.getElementById('resetBtn');
    const paymentTable       = document.getElementById('paymentTable');
    const paymentMobile      = document.getElementById('paymentMobile');
    const paginationWrapper  = document.getElementById('paginationWrapper');
    const totalPaymentCount  = document.getElementById('totalPaymentCount');

    let debounceTimer;

    function showLoadingState() {
        if (paymentTable) {
            paymentTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading transactions...
                    </td>
                </tr>`;
        }
        if (paymentMobile) {
            paymentMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading transactions...
                </p>`;
        }
    }

    function showErrorState() {
        if (paymentTable) {
            paymentTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load transaction data. Please try again.
                    </td>
                </tr>`;
        }
        if (paymentMobile) {
            paymentMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load transaction data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput) searchInput.value = '';
        if (typeFilter)  typeFilter.value  = '';
        if (fromDate)    fromDate.value    = '';
        if (toDate)      toDate.value      = '';
    }

    function fetchFilteredPayments(pageUrl = null) {
        showLoadingState();

        const query = searchInput ? searchInput.value.trim() : '';
        const type  = typeFilter ? typeFilter.value : '';
        const from  = fromDate ? fromDate.value : '';
        const to    = toDate ? toDate.value : '';

        let url = pageUrl || "{{ route('sr.payments.index.data') }}";
        const params = new URLSearchParams();

        if (query) params.append('search', query);
        if (type)  params.append('type', type);
        if (from)  params.append('from_date', from);
        if (to)    params.append('to_date', to);

        if (params.toString()) {
            url += (url.includes('?') ? '&' : '?') + params.toString();
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (paymentTable)  paymentTable.innerHTML  = data.table;
            if (paymentMobile) paymentMobile.innerHTML = data.mobile;
            if (paginationWrapper && data.pagination !== undefined) {
                paginationWrapper.innerHTML = data.pagination;
            }
            if (totalPaymentCount && data.total_count !== undefined) {
                totalPaymentCount.innerText = data.total_count;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showErrorState();
        });
    }

    // Initial Load: Only fetch if URL parameters exist
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredPayments();
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredPayments(), 350);
        });
    }

    if (typeFilter) typeFilter.addEventListener('change', () => fetchFilteredPayments());
    if (fromDate)   fromDate.addEventListener('change',   () => fetchFilteredPayments());
    if (toDate)     toDate.addEventListener('change',     () => fetchFilteredPayments());

    // Click handler for See All button
    document.addEventListener('click', function (e) {
        if (e.target.closest('.see-all-btn')) {
            clearAllFilterInputs();
            fetchFilteredPayments();
        }
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredPayments();
        });
    }

    // AJAX pagination handling
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredPayments(link.href);
            }
        });
    }
});
</script>
@endpush