@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  {{-- Card Header --}}
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Closing History</h2>
      <p class="text-muted mb-0">Daily cash closing records for <strong>{{ $branch->name ?? 'your branch' }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-history me-1"></i> Total Records: <span id="totalClosingCount">{{ $totalClosingsCount }}</span>
      </div>
      <a href="{{ route('manager.cash.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem; height: 38px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fas fa-arrow-left"></i> Back to Cash
      </a>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-4">

      {{-- Search --}}
      <div>
        <label>Search Notes</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Search in notes..." style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
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

  {{-- Desktop Table View --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Date</th>
          <th>Opening Cash</th>
          <th>Cash In</th>
          <th>Cash Out</th>
          <th>Expected</th>
          <th>Actual</th>
          <th>Variance</th>
          <th>Status</th>
          <th>Closed By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="closingTable">
        <tr>
          <td colspan="11" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view closing history.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards View --}}
  <div class="manage-mobile-cards" id="closingMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view closing history.
    </p>
  </div>

</div>

{{-- Dynamic Pagination --}}
<div class="mt-3" id="paginationWrapper"></div>

{{-- =========================================================================
     MODAL: View Closing Details
     ========================================================================= --}}
<div id="viewClosingModal" class="ch-modal-overlay" onclick="handleModalBackdrop(event)">
  <div class="ch-modal-box">
    <div class="ch-modal-header">
      <div>
        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-file-chart-column text-primary me-2"></i> Closing Breakdown</h4>
        <small class="text-muted" id="modalDateSubtitle">Daily Cash Closing</small>
      </div>
      <button type="button" class="ch-modal-close-btn" onclick="closeClosingModal()">&times;</button>
    </div>

    <div class="ch-modal-body">
      <div class="ch-detail-grid">
        <div class="ch-detail-row">
          <span class="ch-detail-label">Closing Date</span>
          <span class="ch-detail-value fw-bold text-main" id="detDate">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Status</span>
          <span class="ch-detail-value" id="detStatus">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Opening Cash</span>
          <span class="ch-detail-value text-muted" id="detOpeningCash">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Cash In (Today)</span>
          <span class="ch-detail-value text-success fw-semibold" id="detCashIn">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Cash Out (Today)</span>
          <span class="ch-detail-value text-danger fw-semibold" id="detCashOut">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Expected Cash</span>
          <span class="ch-detail-value fw-bold text-main" id="detExpectedCash">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Actual Cash (Counted)</span>
          <span class="ch-detail-value fw-bold text-primary fs-5" id="detActualCash">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Variance</span>
          <span class="ch-detail-value fw-bold" id="detVariance">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Closed By</span>
          <span class="ch-detail-value" id="detClosedBy">-</span>
        </div>
        <div class="ch-detail-row">
          <span class="ch-detail-label">Sealed At</span>
          <span class="ch-detail-value text-muted" id="detClosedAt">-</span>
        </div>
        <div class="ch-detail-row flex-column align-items-start border-bottom-0 pt-2">
          <span class="ch-detail-label mb-1">Notes / Reason</span>
          <div class="p-2 w-100 rounded-2 text-muted" style="background: var(--background); font-size: 0.85rem; min-height: 45px;" id="detNotes">-</div>
        </div>
      </div>
    </div>

    <div class="ch-modal-footer">
      <button type="button" class="btn btn-secondary px-4" onclick="closeClosingModal()" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
        Close
      </button>
    </div>
  </div>
</div>

<style>
  /* Modal System */
  .ch-modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeInModal 0.2s ease;
  }
  .ch-modal-overlay.show { display: flex; }
  .ch-modal-box {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    width: 100%; max-width: 540px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.25);
    overflow: hidden;
    animation: slideUpModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .ch-modal-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: space-between;
    background: var(--section-bg);
  }
  .ch-modal-close-btn {
    background: transparent; border: none;
    font-size: 1.5rem; line-height: 1;
    color: var(--text-muted); cursor: pointer; transition: color 0.2s;
  }
  .ch-modal-close-btn:hover { color: #ef4444; }
  .ch-modal-body {
    padding: 22px 24px;
    max-height: 70vh; overflow-y: auto;
  }
  .ch-modal-footer {
    padding: 14px 24px;
    border-top: 1px solid var(--border-color);
    display: flex; justify-content: flex-end; gap: 10px;
    background: var(--background);
  }
  .ch-detail-grid { display: flex; flex-direction: column; }
  .ch-detail-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0;
    border-bottom: 1px dashed var(--border-color);
    font-size: 0.88rem;
  }
  .ch-detail-label { color: var(--text-muted); font-weight: 500; }
  .ch-detail-value { color: var(--text-main); font-weight: 600; }
  @keyframes fadeInModal { from { opacity: 0; } to { opacity: 1; } }
  @keyframes slideUpModal { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const fromDate          = document.getElementById('fromDate');
    const toDate            = document.getElementById('toDate');
    const resetBtn          = document.getElementById('resetBtn');

    const closingTable      = document.getElementById('closingTable');
    const closingMobile     = document.getElementById('closingMobile');
    const totalCountEl      = document.getElementById('totalClosingCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (closingTable) {
            closingTable.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading closing records...
                    </td>
                </tr>`;
        }
        if (closingMobile) {
            closingMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading closing records...
                </p>`;
        }
    }

    function showErrorState() {
        if (closingTable) {
            closingTable.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load records. Please try again.
                    </td>
                </tr>`;
        }
        if (closingMobile) {
            closingMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load records.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)  searchInput.value  = '';
        if (fromDate)     fromDate.value     = '';
        if (toDate)       toDate.value       = '';
    }

    function fetchFilteredClosings(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const from   = encodeURIComponent(fromDate ? fromDate.value : '');
            const to     = encodeURIComponent(toDate ? toDate.value : '');

            url = `{{ route('manager.cash.data') }}?search=${search}&from_date=${from}&to_date=${to}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (closingTable)  closingTable.innerHTML  = data.table;
            if (closingMobile) closingMobile.innerHTML = data.mobile;
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

    // Initial Load: Only auto-fetch if URL has params
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredClosings();
    }

    // Debounce search input
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredClosings(), 350);
        });
    }

    if (fromDate)     fromDate.addEventListener('change',     () => fetchFilteredClosings());
    if (toDate)       toDate.addEventListener('change',       () => fetchFilteredClosings());

    // Reset button — fetch all records
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredClosings();
        });
    }

    // AJAX pagination handling
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredClosings(link.href);
            }
        });
    }
});

// =========================================================================
// Closing Detail Modal Functions (called from table/mtable partials)
// =========================================================================
function viewClosingDetails(data) {
    document.getElementById('modalDateSubtitle').innerText = data.date ?? '';
    document.getElementById('detDate').innerText           = data.date ?? '-';
    document.getElementById('detOpeningCash').innerText    = data.opening_cash ?? '-';
    document.getElementById('detCashIn').innerText         = data.cash_in ?? '-';
    document.getElementById('detCashOut').innerText        = data.cash_out ?? '-';
    document.getElementById('detExpectedCash').innerText   = data.expected_cash ?? '-';
    document.getElementById('detActualCash').innerText     = data.actual_cash ?? '-';
    document.getElementById('detClosedBy').innerText       = data.closed_by ?? '-';
    document.getElementById('detClosedAt').innerText       = data.closed_at ?? '-';
    document.getElementById('detNotes').innerText          = data.notes ?? 'No note recorded.';

    // Variance display
    const varianceRaw = data.variance_raw ?? 0;
    const varianceEl  = document.getElementById('detVariance');
    if (Math.abs(varianceRaw) < 0.01) {
        varianceEl.innerHTML = '<span class="text-muted">৳ 0.00 — Balanced</span>';
    } else if (varianceRaw > 0) {
        varianceEl.innerHTML = `<span class="text-success">${data.cash_variance} — Excess</span>`;
    } else {
        varianceEl.innerHTML = `<span class="text-danger">${data.cash_variance} — Shortage</span>`;
    }

    // Status
    const statusEl = document.getElementById('detStatus');
    if (data.status === 'closed') {
        statusEl.innerHTML = '<span class="status-approved-badge">Closed</span>';
    } else if (data.status === 'open') {
        statusEl.innerHTML = '<span class="status-pending-badge">Open</span>';
    } else {
        statusEl.innerHTML = `<span class="status-undefined-badge">${data.status}</span>`;
    }

    const modal = document.getElementById('viewClosingModal');
    if (modal) modal.classList.add('show');
}

function closeClosingModal() {
    const modal = document.getElementById('viewClosingModal');
    if (modal) modal.classList.remove('show');
}

function handleModalBackdrop(e) {
    if (e.target.id === 'viewClosingModal') {
        closeClosingModal();
    }
}
</script>
@endpush
