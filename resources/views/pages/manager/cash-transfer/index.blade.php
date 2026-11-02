@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  {{-- Card Header --}}
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Cash Transfer History</h2>
      <p class="text-muted mb-0">Manage and track branch cash transfers to central Admin cash</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-money-bill-transfer me-1"></i> Total Transfers: <span id="totalTransferCount">0</span>
      </div>
      <button type="button" class="btn-smart btn-blue" onclick="openCreateTransferModal()">
        <i class="fas fa-plus me-1"></i> Create New Transfer
      </button>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar (stock-in format) --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-5">

      {{-- Search --}}
      <div>
        <label>Search Transfer</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Search ID or note..." value="{{ request('search') }}" style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Statuses --</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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

  {{-- Desktop Table View --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Transfer ID</th>
          <th>Amount</th>
          <th>Cash Before</th>
          <th>Cash After</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="transferTable">
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view cash transfers.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards View --}}
  <div class="manage-mobile-cards" id="transferMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view cash transfers.
    </p>
  </div>

</div>

{{-- Dynamic Pagination --}}
<div class="mt-3" id="paginationWrapper"></div>

{{-- =========================================================================
     MODAL 1: Create New Cash Transfer Request
     ========================================================================= --}}
<div id="createTransferModal" class="ct-modal-overlay" onclick="handleCreateModalBackdrop(event)">
  <div class="ct-modal-box">
    <div class="ct-modal-header">
      <div>
        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-paper-plane text-primary me-2"></i> Request Cash Transfer</h4>
        <small class="text-muted">Transfer funds from Branch Cash to Central Admin Cash</small>
      </div>
      <button type="button" class="ct-modal-close-btn" onclick="closeCreateTransferModal()">&times;</button>
    </div>

    <form action="{{ route('manager.cash-transfers.store') }}" method="POST" id="createTransferForm">
      @csrf
      <div class="ct-modal-body">

        {{-- Branch & Current Balance Info --}}
        <div class="p-3 mb-3 rounded-3" style="background: var(--background); border: 1px solid var(--border-color);">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small">Branch:</span>
            <span class="fw-bold text-main">{{ $branch->name ?? 'No Branch' }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small">Current Cash:</span>
            <span class="fw-bold text-main">৳ {{ number_format($branch->cash ?? 0, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">Available for Transfer:</span>
            <span class="fw-bold text-success" id="modalAvailCashText">৳ {{ number_format($availableCash ?? 0, 2) }}</span>
          </div>
        </div>

        {{-- Transfer Amount Input --}}
        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size: 0.85rem;">
            Transfer Amount (৳) <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text" style="background: var(--background); border-color: var(--border-color); color: var(--text-main); font-weight: 700;">৳</span>
            <input type="number" 
                   name="amount" 
                   id="transferAmountInput" 
                   class="input-form" 
                   step="0.01" 
                   min="1" 
                   max="{{ $availableCash ?? 0 }}"
                   placeholder="Enter transfer amount" 
                   required 
                   style="height: 42px; font-size: 1rem; font-weight: 600;">
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <small class="text-muted" id="remainingCashHint">Remaining cash after transfer: ৳ {{ number_format($branch->cash ?? 0, 2) }}</small>
            @if(($availableCash ?? 0) > 0)
              <button type="button" class="btn btn-link p-0 text-primary small text-decoration-none" onclick="setMaxAmount({{ $availableCash ?? 0 }})">
                Max Available
              </button>
            @endif
          </div>
        </div>

        {{-- Note / Reason --}}
        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size: 0.85rem;">
            Transfer Note / Reason <span class="text-muted">(Optional)</span>
          </label>
          <textarea name="note" 
                    id="transferNoteInput" 
                    class="input-form" 
                    rows="3" 
                    placeholder="e.g., Cash sent via bank deposit slip #1234 or courier/personnel handover..."
                    style="height: auto; resize: vertical;"></textarea>
        </div>

        {{-- Admin Approval Notice --}}
        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.78rem; border-radius: 8px;">
          <i class="fa-solid fa-circle-info me-1"></i>
          This request will remain in <strong>Pending</strong> status until Admin reviews and approves it. Upon approval, cash will transfer directly to Admin Cash.
        </div>

      </div>

      <div class="ct-modal-footer">
        <button type="button" class="btn btn-secondary px-3" onclick="closeCreateTransferModal()" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
          Cancel
        </button>
        <button type="submit" class="btn-smart btn-blue px-4" id="submitTransferBtn">
          <i class="fa-solid fa-check me-1"></i> Submit Request
        </button>
      </div>
    </form>
  </div>
</div>

{{-- =========================================================================
     MODAL 2: View Cash Transfer Details
     ========================================================================= --}}
<div id="viewTransferModal" class="ct-modal-overlay" onclick="handleViewModalBackdrop(event)">
  <div class="ct-modal-box">
    <div class="ct-modal-header">
      <div>
        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Transfer Details</h4>
        <small class="text-muted" id="modalDetailSubtitle">Cash Transfer Information</small>
      </div>
      <button type="button" class="ct-modal-close-btn" onclick="closeViewTransferModal()">&times;</button>
    </div>

    <div class="ct-modal-body">
      <div class="detail-grid">
        <div class="detail-row">
          <span class="detail-label">Transfer Reference</span>
          <span class="detail-value fw-bold text-primary" id="detTransferId">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Branch</span>
          <span class="detail-value" id="detBranch">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="detail-value" id="detStatus">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Transfer Amount</span>
          <span class="detail-value fw-bold fs-5 text-main" id="detAmount">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Cash Before Transfer</span>
          <span class="detail-value text-muted" id="detCashBefore">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Cash After Transfer</span>
          <span class="detail-value text-muted" id="detCashAfter">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Requested By</span>
          <span class="detail-value" id="detRequestedBy">-</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Date & Time</span>
          <span class="detail-value" id="detDate">-</span>
        </div>
        <div class="detail-row flex-column align-items-start border-bottom-0 pt-2">
          <span class="detail-label mb-1">Manager Note / Reference</span>
          <div class="p-2 w-100 rounded-2 text-muted" style="background: var(--background); font-size: 0.85rem; min-height: 45px;" id="detNote">-</div>
        </div>
      </div>
    </div>

    <div class="ct-modal-footer">
      <button type="button" class="btn btn-secondary px-4" onclick="closeViewTransferModal()" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
        Close
      </button>
    </div>
  </div>
</div>

<style>
  /* Modal System */
  .ct-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeInModal 0.2s ease;
  }
  .ct-modal-overlay.show {
    display: flex;
  }
  .ct-modal-box {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    width: 100%;
    max-width: 520px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    animation: slideUpModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .ct-modal-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--section-bg);
  }
  .ct-modal-close-btn {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: var(--text-muted);
    cursor: pointer;
    transition: color 0.2s;
  }
  .ct-modal-close-btn:hover {
    color: #ef4444;
  }
  .ct-modal-body {
    padding: 22px 24px;
    max-height: 75vh;
    overflow-y: auto;
  }
  .ct-modal-footer {
    padding: 14px 24px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: var(--background);
  }

  /* Detail Grid in View Modal */
  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px dashed var(--border-color);
    font-size: 0.88rem;
  }
  .detail-label {
    color: var(--text-muted);
    font-weight: 500;
  }
  .detail-value {
    color: var(--text-main);
    font-weight: 600;
  }

  @keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes slideUpModal {
    from { opacity: 0; transform: translateY(20px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const statusFilter      = document.getElementById('statusFilter');
    const fromDate          = document.getElementById('fromDate');
    const toDate            = document.getElementById('toDate');
    const resetBtn          = document.getElementById('resetBtn');

    const transferTable     = document.getElementById('transferTable');
    const transferMobile    = document.getElementById('transferMobile');
    const totalCountEl      = document.getElementById('totalTransferCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    const branchCurrentCash = {{ $branch->cash ?? 0 }};

    function showLoadingState() {
        if (transferTable) {
            transferTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading cash transfers...
                    </td>
                </tr>`;
        }
        if (transferMobile) {
            transferMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading cash transfers...
                </p>`;
        }
    }

    function showErrorState() {
        if (transferTable) {
            transferTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load cash transfer records. Please try again.
                    </td>
                </tr>`;
        }
        if (transferMobile) {
            transferMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load cash transfer records.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)  searchInput.value  = '';
        if (statusFilter) statusFilter.value = '';
        if (fromDate)     fromDate.value     = '';
        if (toDate)       toDate.value       = '';
    }

    function fetchFilteredTransfers(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const status = encodeURIComponent(statusFilter ? statusFilter.value : '');
            const from   = encodeURIComponent(fromDate ? fromDate.value : '');
            const to     = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('manager.cash-transfers.data') }}?search=${search}&status=${status}&from_date=${from}&to_date=${to}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (transferTable)  transferTable.innerHTML  = data.table;
            if (transferMobile) transferMobile.innerHTML = data.mobile;
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

    // Initial Load: Only fetch if search/status/date parameters exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredTransfers();
    }

    // Debounce search input
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredTransfers(), 350);
        });
    }

    // Filter change listeners
    if (statusFilter) statusFilter.addEventListener('change', () => fetchFilteredTransfers());
    if (fromDate)     fromDate.addEventListener('change',     () => fetchFilteredTransfers());
    if (toDate)       toDate.addEventListener('change',       () => fetchFilteredTransfers());

    // Reset button listener
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredTransfers();
        });
    }

    // AJAX pagination handling
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredTransfers(link.href);
            }
        });
    }

    // Real-time calculation on amount input in Create Modal
    const amountInput = document.getElementById('transferAmountInput');
    const remainingHint = document.getElementById('remainingCashHint');
    if (amountInput && remainingHint) {
        amountInput.addEventListener('input', function () {
            const entered = parseFloat(this.value) || 0;
            const remaining = Math.max(0, branchCurrentCash - entered);
            remainingHint.innerHTML = `Remaining cash after transfer: <strong>৳ ${remaining.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>`;
            if (entered > branchCurrentCash) {
                remainingHint.classList.add('text-danger');
            } else {
                remainingHint.classList.remove('text-danger');
            }
        });
    }
});

// =========================================================================
// Modal Functions
// =========================================================================
function openCreateTransferModal() {
    const modal = document.getElementById('createTransferModal');
    if (modal) modal.classList.add('show');
}

function closeCreateTransferModal() {
    const modal = document.getElementById('createTransferModal');
    if (modal) modal.classList.remove('show');
}

function handleCreateModalBackdrop(e) {
    if (e.target.id === 'createTransferModal') {
        closeCreateTransferModal();
    }
}

function setMaxAmount(max) {
    const amountInput = document.getElementById('transferAmountInput');
    if (amountInput) {
        amountInput.value = max;
        amountInput.dispatchEvent(new Event('input'));
    }
}

function viewTransferDetails(data) {
    document.getElementById('detTransferId').innerText = data.id;
    document.getElementById('detBranch').innerText = data.branch;
    document.getElementById('detAmount').innerText = data.amount;
    document.getElementById('detCashBefore').innerText = data.cash_before;
    document.getElementById('detCashAfter').innerText = data.cash_after;
    document.getElementById('detRequestedBy').innerText = data.requested_by;
    document.getElementById('detDate').innerText = data.date;
    document.getElementById('detNote').innerText = data.note;

    let statusHtml = '';
    if (data.status === 'pending') {
        statusHtml = '<span class="status-pending-badge">Pending...</span>';
    } else if (data.status === 'approved') {
        statusHtml = '<span class="status-approved-badge">Approved</span>';
    } else if (data.status === 'rejected') {
        statusHtml = '<span class="status-rejected-badge">Rejected</span>';
    } else {
        statusHtml = `<span class="status-undefined-badge">${data.status}</span>`;
    }
    document.getElementById('detStatus').innerHTML = statusHtml;

    const modal = document.getElementById('viewTransferModal');
    if (modal) modal.classList.add('show');
}

function closeViewTransferModal() {
    const modal = document.getElementById('viewTransferModal');
    if (modal) modal.classList.remove('show');
}

function handleViewModalBackdrop(e) {
    if (e.target.id === 'viewTransferModal') {
        closeViewTransferModal();
    }
}

function confirmCancelTransfer(e, form) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Cancel Transfer Request?',
            text: 'Are you sure you want to cancel this pending cash transfer request?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Cancel Request',
            cancelButtonText: 'No, Keep It'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else if (confirm('Are you sure you want to cancel this pending cash transfer request?')) {
        form.submit();
    }
    return false;
}
</script>
@endpush
