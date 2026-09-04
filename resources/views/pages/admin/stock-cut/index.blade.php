@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Stock Cut History</h2>
      <p class="text-muted mb-0">Manage all stock cuts recorded in the system</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-cut me-1"></i> Total Stock Cuts: <span id="totalStockCutCount">0</span>
      </div>
      <a href="{{ route('admin.stock.cut.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus-circle me-1"></i> Create New Stock Cut
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
          <input type="text" id="searchInput" class="input-form" placeholder="Supplier or User..." value="{{ request('search') }}" style="padding-left: 30px;">
          <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
        </div>
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
          <th>Date</th>
          <th>Supplier</th>
          <th>Requested By</th>
          <th>Total Amount</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="6" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view stock cuts.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view stock cuts.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput    = document.getElementById('searchInput');
    const supplierFilter = document.getElementById('supplierFilter');
    const fromDate       = document.getElementById('fromDate');
    const toDate         = document.getElementById('toDate');
    const resetBtn       = document.getElementById('resetBtn');

    const desktopTable      = document.getElementById('desktopTable');
    const mobileTable       = document.getElementById('mobileTable');
    const totalCountEl      = document.getElementById('totalStockCutCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading stock cuts...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading stock cuts...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load stock cut data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load stock cut data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)    searchInput.value    = '';
        if (supplierFilter) supplierFilter.value = '';
        if (fromDate)       fromDate.value       = '';
        if (toDate)         toDate.value         = '';
    }

    function fetchFilteredStockCuts(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search   = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const supplier = encodeURIComponent(supplierFilter ? supplierFilter.value : '');
            const from     = encodeURIComponent(fromDate ? fromDate.value : '');
            const to       = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.stock.cut.index.data') }}?search=${search}&supplier_id=${supplier}&from_date=${from}&to_date=${to}`;
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
        fetchFilteredStockCuts();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredStockCuts(), 450);
        });
    }

    if (supplierFilter) supplierFilter.addEventListener('change', () => fetchFilteredStockCuts());
    if (fromDate)       fromDate.addEventListener('change',       () => fetchFilteredStockCuts());
    if (toDate)         toDate.addEventListener('change',         () => fetchFilteredStockCuts());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredStockCuts();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredStockCuts(link.href);
            }
        });
    }
});
</script>
@endpush