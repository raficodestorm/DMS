@extends('layouts.adminlayout')

@section('content')
<style>
  /* ===== Inline Order Status Changer ===== */
  .os-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .os-arrow-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 4px;
    color: #888;
    font-size: 0.65rem;
    line-height: 1;
    border-radius: 4px;
    transition: background 0.15s, color 0.15s;
  }
  .os-arrow-btn:hover {
    background: rgba(2,2,226,0.08);
    color: var(--primary);
  }
  .os-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    min-width: 160px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.13);
    z-index: 9999;
    overflow: hidden;
  }
  .os-dropdown.open { display: block; }
  .os-option {
    padding: 8px 14px;
    font-size: 0.82rem;
    cursor: pointer;
    color: #333;
    transition: background 0.15s;
  }
  .os-option:hover { background: rgba(2,2,226,0.06); }
  .os-option.active {
    font-weight: 700;
    color: var(--primary);
    background: rgba(2,2,226,0.04);
  }
  .os-option.loading {
    color: #aaa;
    pointer-events: none;
  }
  /* spinner on arrow while saving */
  .os-arrow-btn.saving i {
    animation: spin 0.6s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">All Orders</h2>
      <p class="text-muted mb-0">Manage all registered orders</p>
    </div>
    <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
      <i class="fas fa-shopping-bag me-1"></i> Total Orders: <span id="totalOrderCount">0</span>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-7">

      {{-- Search Input --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="search" class="input-form" placeholder="Search Order ID / Shop..." value="{{ request('search') }}" style="padding-left: 28px;">
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

      {{-- Order Type Filter --}}
      <div>
        <label>Order Type</label>
        <select id="orderTypeFilter" class="input-form">
          <option value="">-- All Types --</option>
          <option value="field_order" {{ request('order_type') == 'field_order' ? 'selected' : '' }}>Field Order</option>
          <option value="retail" {{ request('order_type') == 'retail' ? 'selected' : '' }}>Retail</option>
          <option value="online" {{ request('order_type') == 'online' ? 'selected' : '' }}>Online</option>
        </select>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Statuses --</option>
          <option value="pending_sr" {{ request('status') == 'pending_sr' ? 'selected' : '' }}>Pending SR</option>
          <option value="pending_manager" {{ request('status') == 'pending_manager' ? 'selected' : '' }}>Pending Manager</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
          <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
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
        <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show All" style="height: 36px; width: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; width: 100%;">
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
          <th>Order ID</th>
          <th>Branch</th>
          <th>Amount</th>
          <th>Order Type</th>
          <th>Order Status</th>
          <th>Payment Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view orders.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view orders.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput    = document.getElementById('search');
    const branchFilter   = document.getElementById('branchFilter');
    const orderTypeFilter= document.getElementById('orderTypeFilter');
    const statusFilter   = document.getElementById('statusFilter');
    const fromDate       = document.getElementById('fromDate');
    const toDate         = document.getElementById('toDate');
    const resetBtn       = document.getElementById('resetBtn');

    const desktopTable   = document.getElementById('desktopTable');
    const mobileTable    = document.getElementById('mobileTable');
    const totalCountEl   = document.getElementById('totalOrderCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading orders...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading orders...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load order data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load order data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)     searchInput.value     = '';
        if (branchFilter)    branchFilter.value    = '';
        if (orderTypeFilter) orderTypeFilter.value = '';
        if (statusFilter)    statusFilter.value    = '';
        if (fromDate)        fromDate.value        = '';
        if (toDate)          toDate.value          = '';
    }

    function fetchFilteredOrders(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const query     = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const branch    = encodeURIComponent(branchFilter ? branchFilter.value : '');
            const orderType = encodeURIComponent(orderTypeFilter ? orderTypeFilter.value : '');
            const status    = encodeURIComponent(statusFilter ? statusFilter.value : '');
            const from      = encodeURIComponent(fromDate ? fromDate.value : '');
            const to        = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('admin.order.data') }}?search=${query}&branch_id=${branch}&order_type=${orderType}&status=${status}&from_date=${from}&to_date=${to}`;
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

    // Initial Load: Only fetch if filters/page parameters exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredOrders();
    }

    // Search Keyup Debounce
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredOrders(), 350);
        });
    }

    // Filter Change Event Listeners
    if (branchFilter)    branchFilter.addEventListener('change', () => fetchFilteredOrders());
    if (orderTypeFilter) orderTypeFilter.addEventListener('change', () => fetchFilteredOrders());
    if (statusFilter)    statusFilter.addEventListener('change', () => fetchFilteredOrders());
    if (fromDate)        fromDate.addEventListener('change', () => fetchFilteredOrders());
    if (toDate)          toDate.addEventListener('change', () => fetchFilteredOrders());

    // Reset Button (Clears filters & fetches all orders)
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredOrders();
        });
    }

    // AJAX Pagination Clicks
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredOrders(link.href);
            }
        });
    }

    // ================================================================
    // ORDER STATUS INLINE CHANGER
    // ================================================================
    const STATUS_URL   = '{{ rtrim(route("admin.order.update_status", ["order" => "__ID__"]), "") }}';
    const CSRF_TOKEN   = '{{ csrf_token() }}';

    const STATUS_BADGES = {
      pending_sr:      { cls: 'status-pending-badge',   lbl: 'Pending..SR..' },
      pending_manager: { cls: 'status-pmanager-badge',  lbl: 'Pending..Manager..' },
      approved:        { cls: 'status-approved-badge',  lbl: 'Approved' },
      rejected:        { cls: 'status-rejected-badge',  lbl: 'Rejected' },
    };

    // Open / close dropdown
    document.addEventListener('click', function (e) {
      // Close all first
      document.querySelectorAll('.os-dropdown.open').forEach(function (d) {
        if (!d.closest('.os-wrap').contains(e.target)) {
          d.classList.remove('open');
        }
      });

      // Toggle clicked arrow
      const arrowBtn = e.target.closest('.os-arrow-btn');
      if (arrowBtn) {
        const wrap     = arrowBtn.closest('.os-wrap');
        const dropdown = wrap.querySelector('.os-dropdown');
        dropdown.classList.toggle('open');
      }
    });

    // Select a status option
    document.addEventListener('click', function (e) {
      const option = e.target.closest('.os-option');
      if (!option) return;

      const wrap      = option.closest('.os-wrap');
      const dropdown  = wrap.querySelector('.os-dropdown');
      const arrowBtn  = wrap.querySelector('.os-arrow-btn');
      const badge     = wrap.querySelector('.os-badge');
      const orderId   = wrap.dataset.orderId;
      const newStatus = option.dataset.value;

      if (option.classList.contains('active')) {
        dropdown.classList.remove('open');
        return;
      }

      // Show loading
      arrowBtn.classList.add('saving');
      arrowBtn.innerHTML = '<i class="fa-solid fa-spinner"></i>';
      dropdown.querySelectorAll('.os-option').forEach(o => o.classList.add('loading'));

      const url = STATUS_URL.replace('__ID__', orderId);

      fetch(url, {
        method: 'PATCH',
        headers: {
          'Content-Type':     'application/json',
          'X-CSRF-TOKEN':     CSRF_TOKEN,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept':           'application/json',
        },
        body: JSON.stringify({ status: newStatus }),
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const info = STATUS_BADGES[newStatus];
          if (info) {
            // Update badge class + text
            badge.className = info.cls + ' os-badge';
            badge.textContent = info.lbl;
          }
          // Update active option
          dropdown.querySelectorAll('.os-option').forEach(o => {
            o.classList.remove('active', 'loading');
          });
          option.classList.add('active');
        } else {
          alert(data.message || 'Status update failed.');
          dropdown.querySelectorAll('.os-option').forEach(o => o.classList.remove('loading'));
        }
      })
      .catch(() => {
        alert('Network error. Please try again.');
        dropdown.querySelectorAll('.os-option').forEach(o => o.classList.remove('loading'));
      })
      .finally(() => {
        arrowBtn.classList.remove('saving');
        arrowBtn.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
        dropdown.classList.remove('open');
      });
    });
});
</script>
@endpush